<?php
namespace App\Services;

use App\Repositories\Booking\BookingRepositoryInterface;
use App\Repositories\BookingStatusLog\BookingStatusLogRepositoryInterface;
use App\Repositories\Payment\PaymentRepositoryInterface;
use App\Repositories\PaymentAttempt\PaymentAttemptRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaymentService
{
    private const FAKE_PROVIDERS = ['vnpay', 'momo', 'zalopay'];

    public function __construct(
        private BookingRepositoryInterface $bookingRepository,
        private BookingStatusLogRepositoryInterface $bookingStatusLogRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private PaymentAttemptRepositoryInterface $paymentAttemptRepository,
        private MembershipService $membershipService
    ) {
    }

    public function paginate(int $limit, ?string $q, ?string $status, ?string $provider, ?string $fromDate, ?string $toDate): LengthAwarePaginator
    {
        return $this->paymentRepository->paginate($limit, $q, $status, $provider, $fromDate, $toDate);
    }

    public function create(array $data, string $userId)
    {
        $booking = $this->bookingRepository->find($data['booking_id'], ['*'], [
            'payment',
            'showtime.movie',
            'showtime.room.cinema',
            'items',
            'combos',
        ]);

        if ($booking->user_id !== $userId) {
            throw new HttpException(403, 'Bạn không có quyền thanh toán booking này.');
        }

        if ($booking->status !== 'pending') {
            throw new HttpException(422, 'Booking này không còn ở trạng thái chờ thanh toán.');
        }

        if ($booking->expired_at && now()->greaterThan($booking->expired_at)) {
            throw new HttpException(422, 'Booking đã hết hạn thanh toán.');
        }

        $payment = $this->paymentRepository->findByBookingId($booking->id);

        if (! $payment || $payment->status !== 'pending') {
            $payment = $this->paymentRepository->create([
                'booking_id' => $booking->id,
                'provider' => $data['provider'],
                'transaction_code' => strtoupper($data['provider']) . '-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6)),
                'amount' => $booking->total_amount,
                'status' => 'pending',
                'idempotency_key' => (string) Str::uuid(),
            ]);
        }

        $this->paymentAttemptRepository->create([
            'booking_id' => $booking->id,
            'provider' => $data['provider'],
            'request_payload' => json_encode($data),
            'response_payload' => json_encode(['payment_id' => $payment->id]),
            'status' => 'pending',
        ]);

        return [
            'payment' => $payment->fresh(['booking.showtime.movie', 'booking.showtime.room.cinema', 'booking.items', 'booking.combos']),
            'payment_url' => "/booking/payment/{$payment->id}",
            'qr_content' => strtoupper($data['provider']) . " {$booking->booking_code} {$booking->total_amount}",
        ];
    }

    public function find(string $paymentId, string $userId)
    {
        $payment = $this->paymentRepository->find($paymentId, ['*'], [
            'booking.showtime.movie',
            'booking.showtime.room.cinema',
            'booking.items',
            'booking.combos',
        ]);

        if (! $payment->booking || $payment->booking->user_id !== $userId) {
            throw new HttpException(403, 'Bạn không có quyền xem thanh toán này.');
        }

        return $payment;
    }

    public function confirm(string $paymentId, string $userId)
    {
        $payment = $this->paymentRepository->find($paymentId, ['*'], [
            'booking.showtime.movie',
            'booking.showtime.room.cinema',
            'booking.items',
            'booking.combos',
        ]);

        $booking = $payment->booking;

        if (! $booking || $booking->user_id !== $userId) {
            throw new HttpException(403, 'Bạn không có quyền xác nhận thanh toán này.');
        }

        if (! in_array($payment->provider, self::FAKE_PROVIDERS, true)) {
            throw new HttpException(422, 'Phương thức thanh toán không hỗ trợ xác nhận giả lập.');
        }

        if ($payment->status === 'paid') {
            return [
                'payment' => $payment,
                'booking' => $booking,
            ];
        }

        if ($payment->status !== 'pending' || $booking->status !== 'pending') {
            throw new HttpException(422, 'Payment này không còn ở trạng thái chờ thanh toán.');
        }

        try {
            DB::beginTransaction();

            $oldStatus = $booking->status;

            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $booking->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            $this->bookingStatusLogRepository->create([
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => 'confirmed',
                'changed_at' => now(),
            ]);

            $this->paymentAttemptRepository->create([
                'booking_id' => $booking->id,
                'provider' => $payment->provider,
                'request_payload' => json_encode(['payment_id' => $payment->id, 'action' => 'fake_confirm']),
                'response_payload' => json_encode(['status' => 'paid']),
                'status' => 'success',
            ]);

            $this->membershipService->addPointsForConfirmedBooking($booking->fresh());

            DB::commit();

            return [
                'payment' => $payment->fresh(),
                'booking' => $booking->fresh(['showtime.movie', 'showtime.room.cinema', 'items', 'combos', 'payment']),
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
