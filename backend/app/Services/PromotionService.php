<?php
namespace App\Services;

use App\Repositories\Promotion\PromotionRepositoryInterface;
use App\Repositories\PromotionUsage\PromotionUsageRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PromotionService
{
    private $promotionRepository;
    private $promotionUsageRepository;

    public function __construct(PromotionRepositoryInterface $promotionRepository, PromotionUsageRepositoryInterface $promotionUsageRepository)
    {
        $this->promotionRepository = $promotionRepository;
        $this->promotionUsageRepository = $promotionUsageRepository;
    }

    public function paginate(int $limit, ?string $q, ?string $status, ?string $applicableTo)
    {
        return $this->promotionRepository->paginate($limit, $q, $status, $applicableTo);
    }

    public function create(array $data)
    {
        $data['code'] = strtoupper($data['code']);

        return $this->promotionRepository->create($data);
    }

    public function find(string $id)
    {
        return $this->promotionRepository->find($id);
    }

    public function update(string $id, array $data)
    {
        $data['code'] = strtoupper($data['code']);

        return $this->promotionRepository->update($id, $data);
    }

    public function delete(string $id)
    {
        return $this->promotionRepository->delete($id);
    }

    public function check(array $data, string $userId): array
    {
        $promotion = $this->promotionRepository->findActiveByCodeForUpdate(strtoupper($data['code']));

        if (! $promotion) {
            throw new HttpException(422, 'Mã khuyến mãi không hợp lệ hoặc đã hết hạn.');
        }

        if ($promotion->applicable_to && ! in_array($promotion->applicable_to, ['booking', 'ticket', 'combo'])) {
            throw new HttpException(422, 'Mã khuyến mãi không áp dụng cho đặt vé.');
        }

        if ($promotion->usage_limit !== null && $this->promotionUsageRepository->countByPromotion($promotion->id) >= $promotion->usage_limit) {
            throw new HttpException(422, 'Mã khuyến mãi đã hết lượt sử dụng.');
        }

        if ($promotion->per_user_limit !== null && $this->promotionUsageRepository->countByPromotionAndUser($promotion->id, $userId) >= $promotion->per_user_limit) {
            throw new HttpException(422, 'Bạn đã dùng mã khuyến mãi này quá số lần cho phép.');
        }

        $ticketAmount = (float) ($data['ticket_amount'] ?? 0);
        $comboAmount = (float) ($data['combo_amount'] ?? 0);
        $subtotal = $ticketAmount + $comboAmount;

        $discountBase = match ($promotion->applicable_to) {
            'ticket' => $ticketAmount,
            'combo' => $comboAmount,
            default => $subtotal,
        };

        if ($discountBase <= 0) {
            throw new HttpException(422, 'Giá trị áp dụng mã khuyến mãi phải lớn hơn 0.');
        }

        $discountAmount = $promotion->discount_type === 'percentage'
            ? $discountBase * ((float) $promotion->discount_value / 100)
            : (float) $promotion->discount_value;

        $discountAmount = min($discountAmount, $discountBase);

        return [
            'promotion' => [
                'id' => $promotion->id,
                'code' => $promotion->code,
                'description' => $promotion->description,
                'discount_type' => $promotion->discount_type,
                'discount_value' => (float) $promotion->discount_value,
                'applicable_to' => $promotion->applicable_to,
                'end_date' => $promotion->end_date,
            ],
            'ticket_amount' => $ticketAmount,
            'combo_amount' => $comboAmount,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'total_amount' => max($subtotal - $discountAmount, 0),
        ];
    }
}
