<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Transaksi Tuker.in</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;padding:20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#059669;padding:30px 20px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:700;">Tuker.in</h1>
                            <p style="color:#d1fae5;margin:8px 0 0 0;font-size:14px;">Transaksi Berhasil</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 20px;">
                            <h2 style="color:#111827;margin:0 0 16px 0;font-size:20px;">Halo, {{ $user->name }}!</h2>
                            <p style="color:#4b5563;line-height:1.6;margin:0 0 16px 0;">Transaksi penukaran botol Anda telah berhasil diproses oleh <strong>{{ $transaction->employee->name }}</strong> pada <strong>{{ $transaction->transacted_at->isoFormat('dddd, D MMMM YYYY HH:mm') }}</strong>.</p>

                            <h3 style="color:#111827;font-size:16px;margin:0 0 12px 0;">Rincian Transaksi</h3>
                            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;margin-bottom:16px;">
                                <thead>
                                    <tr style="background-color:#f9fafb;">
                                        <th style="text-align:left;font-size:13px;color:#6b7280;border-bottom:2px solid #e5e7eb;padding:10px;">Jenis Botol</th>
                                        <th style="text-align:center;font-size:13px;color:#6b7280;border-bottom:2px solid #e5e7eb;padding:10px;">Jumlah</th>
                                        <th style="text-align:right;font-size:13px;color:#6b7280;border-bottom:2px solid #e5e7eb;padding:10px;">Poin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaction->details as $detail)
                                    <tr>
                                        <td style="font-size:14px;color:#111827;border-bottom:1px solid #e5e7eb;padding:10px;">{{ $detail->bottleType->name }}</td>
                                        <td style="text-align:center;font-size:14px;color:#111827;border-bottom:1px solid #e5e7eb;padding:10px;">{{ $detail->quantity }}</td>
                                        <td style="text-align:right;font-size:14px;color:#111827;border-bottom:1px solid #e5e7eb;padding:10px;">{{ number_format($detail->points_earned) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background-color:#f0fdf4;">
                                        <td style="font-size:14px;font-weight:700;color:#059669;padding:10px;border-top:2px solid #059669;">Total</td>
                                        <td style="text-align:center;font-size:14px;font-weight:700;color:#059669;padding:10px;border-top:2px solid #059669;"></td>
                                        <td style="text-align:right;font-size:14px;font-weight:700;color:#059669;padding:10px;border-top:2px solid #059669;">{{ number_format($transaction->total_points) }} poin</td>
                                    </tr>
                                </tfoot>
                            </table>

                            @if ($user->profile)
                            <p style="color:#4b5563;line-height:1.6;margin:0 0 4px 0;">Saldo poin Anda saat ini: <strong style="color:#059669;">{{ number_format($user->profile->points_balance) }} poin</strong></p>
                            @endif

                            <p style="color:#4b5563;line-height:1.6;margin:16px 0 0 0;">Terima kasih telah berkontribusi dalam daur ulang botol plastik!</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f9fafb;padding:20px;text-align:center;border-top:1px solid #e5e7eb;">
                            <p style="color:#9ca3af;font-size:12px;margin:0;">&copy; {{ date('Y') }} Tuker.in. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
