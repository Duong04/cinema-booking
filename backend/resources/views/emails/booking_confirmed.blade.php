<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé xem phim của bạn</title>
</head>
<body style="margin:0;background:#f3f4f6;color:#111827;font-family:Arial,Helvetica,sans-serif;line-height:1.6">
    <div style="max-width:720px;margin:0 auto;padding:28px 14px">
        <div style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb">
            <div style="background:#111827;color:#ffffff;padding:26px 30px">
                <p style="margin:0 0 6px;color:#93c5fd;font-size:13px;font-weight:700;text-transform:uppercase">Đặt vé thành công</p>
                <h1 style="margin:0;font-size:26px;line-height:1.25">Vé xem phim của bạn đã sẵn sàng</h1>
                <p style="margin:10px 0 0;color:#d1d5db">Mã booking: <strong style="color:#ffffff">{{ $ticket['booking_code'] }}</strong></p>
            </div>

            <div style="padding:28px 30px">
                <p style="margin:0 0 18px">Xin chào <strong>{{ $ticket['customer_name'] }}</strong>, cảm ơn bạn đã đặt vé tại Cinema Booking. Vui lòng xuất trình mã QR hoặc mã booking tại quầy soát vé.</p>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                    <tr>
                        <td style="vertical-align:top;padding-right:20px">
                            <h2 style="margin:0 0 10px;font-size:22px;color:#111827">{{ $ticket['movie_title'] }}</h2>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px">
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;width:130px">Suất chiếu</td>
                                    <td style="padding:8px 0;font-weight:700">{{ $ticket['showtime'] ? \Carbon\Carbon::parse($ticket['showtime'])->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280">Rạp</td>
                                    <td style="padding:8px 0;font-weight:700">{{ $ticket['cinema_name'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280">Địa chỉ</td>
                                    <td style="padding:8px 0">{{ $ticket['cinema_address'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280">Phòng</td>
                                    <td style="padding:8px 0;font-weight:700">{{ $ticket['room_name'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280">Ghế</td>
                                    <td style="padding:8px 0;font-weight:700">{{ implode(', ', $ticket['seats']) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280">Thanh toán</td>
                                    <td style="padding:8px 0;font-weight:700">{{ number_format($ticket['total_amount'], 0, ',', '.') }} đ</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:220px;vertical-align:top;text-align:center">
                            <div style="border:1px solid #e5e7eb;border-radius:10px;padding:12px;background:#f9fafb">
                                <img src="{{ $qrUrl }}" width="200" height="200" alt="QR {{ $ticket['booking_code'] }}" style="display:block;width:200px;height:200px;margin:0 auto">
                                <p style="margin:10px 0 0;font-size:12px;color:#6b7280">Quét QR để kiểm tra vé</p>
                            </div>
                        </td>
                    </tr>
                </table>

                @if(count($ticket['combos']))
                    <div style="margin-top:24px;border-top:1px solid #e5e7eb;padding-top:18px">
                        <h3 style="margin:0 0 10px;font-size:16px">Combo đã đặt</h3>
                        @foreach($ticket['combos'] as $combo)
                            <div style="display:flex;justify-content:space-between;gap:16px;margin:6px 0;color:#374151">
                                <span>{{ $combo['name'] }} x{{ $combo['quantity'] }}</span>
                                <strong>{{ number_format($combo['total_price'], 0, ',', '.') }} đ</strong>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div style="margin-top:24px;border-radius:10px;background:#eff6ff;color:#1e40af;padding:14px 16px;font-size:13px">
                    Vui lòng đến rạp trước giờ chiếu khoảng 15 phút. Email này có thể được dùng thay cho vé điện tử.
                </div>
            </div>
        </div>

        <p style="text-align:center;margin:18px 0 0;color:#6b7280;font-size:13px">Hotline hỗ trợ: 0373 405 375</p>
    </div>
</body>
</html>
