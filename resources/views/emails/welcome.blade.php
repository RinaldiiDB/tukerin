<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Tuker.in</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;padding:20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#059669;padding:30px 20px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:700;">Tuker.in</h1>
                            <p style="color:#d1fae5;margin:8px 0 0 0;font-size:14px;">Daur Ulang Botol Plastik</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 20px;">
                            <h2 style="color:#111827;margin:0 0 16px 0;font-size:20px;">Halo, {{ $user->name }}!</h2>
                            <p style="color:#4b5563;line-height:1.6;margin:0 0 12px 0;">Terima kasih telah mendaftar di <strong>Tuker.in</strong>. Akun Anda sekarang aktif dan siap digunakan.</p>
                            <p style="color:#4b5563;line-height:1.6;margin:0 0 12px 0;">Berikut informasi akun Anda:</p>
                            <table width="100%" cellpadding="8" cellspacing="0" style="background-color:#f9fafb;border-radius:8px;margin-bottom:16px;">
                                <tr>
                                    <td style="color:#6b7280;font-size:14px;width:120px;">Nama</td>
                                    <td style="color:#111827;font-size:14px;font-weight:600;">{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;font-size:14px;">Email</td>
                                    <td style="color:#111827;font-size:14px;font-weight:600;">{{ $user->email }}</td>
                                </tr>
                                @if ($user->profile)
                                <tr>
                                    <td style="color:#6b7280;font-size:14px;">Saldo Poin</td>
                                    <td style="color:#059669;font-size:14px;font-weight:600;">{{ number_format($user->profile->points_balance) }} poin</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;font-size:14px;">QR Code</td>
                                    <td style="color:#111827;font-size:14px;font-weight:600;">{{ $user->profile->qr_code }}</td>
                                </tr>
                                @endif
                            </table>
                            <p style="color:#4b5563;line-height:1.6;margin:0 0 20px 0;">Gunakan QR Code di atas saat akan menukarkan botol Anda di pegawai Tuker.in. Setiap botol yang Anda tukarkan akan menambah saldo poin yang bisa dicairkan kapan saja.</p>
                            <table cellpadding="0" cellspacing="0" style="margin:0;">
                                <tr>
                                    <td style="background-color:#059669;border-radius:8px;padding:12px 24px;">
                                        <a href="{{ route('user.dashboard') }}" style="color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;display:block;">Mulai Sekarang</a>
                                    </td>
                                </tr>
                            </table>
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
