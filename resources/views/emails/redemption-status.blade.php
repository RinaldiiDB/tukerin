<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pencairan Poin Tuker.in</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;padding:20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background-color:{{ $status === 'approved' ? '#059669' : '#dc2626' }};padding:30px 20px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:700;">Tuker.in</h1>
                            <p style="color:{{ $status === 'approved' ? '#d1fae5' : '#fecaca' }};margin:8px 0 0 0;font-size:14px;">
                                {{ $status === 'approved' ? 'Pencairan Disetujui' : 'Pencairan Ditolak' }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 20px;">
                            <h2 style="color:#111827;margin:0 0 16px 0;font-size:20px;">Halo, {{ $redemption->user->name }}!</h2>

                            @if ($status === 'approved')
                            <p style="color:#4b5563;line-height:1.6;margin:0 0 16px 0;">Permintaan pencairan poin Anda telah <strong style="color:#059669;">disetujui</strong>.</p>
                            @else
                            <p style="color:#4b5563;line-height:1.6;margin:0 0 16px 0;">Mohon maaf, permintaan pencairan poin Anda <strong style="color:#dc2626;">ditolak</strong>.</p>
                            @endif

                            <h3 style="color:#111827;font-size:16px;margin:0 0 12px 0;">Detail Pengajuan</h3>
                            <table width="100%" cellpadding="8" cellspacing="0" style="background-color:#f9fafb;border-radius:8px;margin-bottom:16px;">
                                <tr>
                                    <td style="color:#6b7280;font-size:14px;width:140px;">Metode</td>
                                    <td style="color:#111827;font-size:14px;font-weight:600;">
                                        {{ $redemption->method === 'cash' ? 'Tunai' : 'E-Wallet' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;font-size:14px;">Poin Digunakan</td>
                                    <td style="color:#111827;font-size:14px;font-weight:600;">{{ number_format($redemption->points_used) }} poin</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;font-size:14px;">Nilai</td>
                                    <td style="color:#111827;font-size:14px;font-weight:600;">Rp{{ number_format($redemption->amount, 0, ',', '.') }}</td>
                                </tr>
                                @if ($redemption->method === 'ewallet')
                                <tr>
                                    <td style="color:#6b7280;font-size:14px;">Nama Bank</td>
                                    <td style="color:#111827;font-size:14px;font-weight:600;">{{ $redemption->bank_name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="color:#6b7280;font-size:14px;">Rekening Tujuan</td>
                                    <td style="color:#111827;font-size:14px;font-weight:600;">{{ $redemption->recipient_account }}</td>
                                </tr>
                                @if ($redemption->processed_at)
                                <tr>
                                    <td style="color:#6b7280;font-size:14px;">Diproses Pada</td>
                                    <td style="color:#111827;font-size:14px;font-weight:600;">{{ $redemption->processed_at->isoFormat('dddd, D MMMM YYYY HH:mm') }}</td>
                                </tr>
                                @endif
                                @if ($status === 'rejected' && $redemption->rejection_note)
                                <tr>
                                    <td style="color:#6b7280;font-size:14px;vertical-align:top;">Alasan</td>
                                    <td style="color:#dc2626;font-size:14px;">{{ $redemption->rejection_note }}</td>
                                </tr>
                                @endif
                            </table>

                            @if ($status === 'rejected' && $redemption->user->profile)
                            <p style="color:#4b5563;line-height:1.6;margin:16px 0 0 0;">Saldo poin Anda tetap utuh sebesar <strong style="color:#059669;">{{ number_format($redemption->user->profile->points_balance) }} poin</strong>. Silakan ajukan kembali dengan data yang benar.</p>
                            @endif

                            <p style="color:#4b5563;line-height:1.6;margin:16px 0 0 0;">Jika ada pertanyaan, silakan hubungi admin Tuker.in.</p>
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
