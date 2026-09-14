<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uthibitisho wa Barua Pepe - FUNDI</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f1f5f9; padding:40px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width:540px; background-color:#ffffff; border-radius:24px; overflow:hidden; border:1px solid #e2e8f0; box-shadow:0 10px 25px rgba(0,0,0,0.05);" cellspacing="0" cellpadding="0" border="0">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color:#042f2e; padding:32px 30px; text-align:center;">
                            <div style="display:inline-block; background-color:#0d9488; color:#ffffff; font-weight:900; font-size:18px; padding:8px 20px; border-radius:12px; letter-spacing:2px;">
                                FUNDI
                            </div>
                            <h1 style="color:#ffffff; font-size:20px; font-weight:800; margin:16px 0 4px 0; letter-spacing:-0.5px;">
                                Uthibitisho wa Barua Pepe
                            </h1>
                            <p style="color:#99f6e4; font-size:12px; margin:0;">
                                Karibu kwenye Mfumo wa Mafundi na Huduma Tanzania
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding:36px 32px 28px 32px;">
                            <p style="font-size:14px; line-height:1.6; color:#334155; margin:0 0 16px 0;">
                                Habari <strong>{{ $user->full_name }}</strong>,
                            </p>
                            <p style="font-size:13px; line-height:1.6; color:#64748b; margin:0 0 24px 0;">
                                Asante kwa kujiunga na FUNDI. Tafadhali tumia nambari ya siri ya tarakimu sita (OTP) hapa chini kukamilisha usajili na kuthibitisha kuwa anwani hii ya barua pepe ni yako:
                            </p>

                            <!-- OTP Box -->
                            <div style="background-color:#f8fafc; border:2px dashed #0d9488; border-radius:16px; padding:24px; text-align:center; margin:24px 0;">
                                <span style="display:block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:#0f766e; margin-bottom:8px;">
                                    Nambari Yako ya Siri (OTP)
                                </span>
                                <div style="font-size:36px; font-weight:900; letter-spacing:8px; color:#0f172a; font-family:monospace;">
                                    {{ $otpCode }}
                                </div>
                                <span style="display:block; font-size:11px; color:#94a3b8; margin-top:8px;">
                                    Muda wake unamalizika baada ya dakika 15.
                                </span>
                            </div>

                            <p style="font-size:12px; line-height:1.6; color:#64748b; margin:0 0 16px 0;">
                                Ikiwa hukuomba kufungua akaunti kwenye mfumo wa FUNDI, unaweza kupuuza ujumbe huu kwa usalama. Usisambaze nambari hii kwa mtu yeyote.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8fafc; border-top:1px solid #f1f5f9; padding:20px 32px; text-align:center;">
                            <p style="font-size:11px; color:#94a3b8; margin:0;">
                                &copy; {{ date('Y') }} FUNDI Platform. Haki zote zimehifadhiwa.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
