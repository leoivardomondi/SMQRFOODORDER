<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMTP Test Connection Successful</title>
    <style>
        @media only screen and (max-width: 620px) {
            .email-shell { width: 100% !important; }
            .email-padding { padding-left: 20px !important; padding-right: 20px !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background:#f4f4f5; color:#18181b; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f4f4f5;">
    <tr>
        <td align="center" style="padding:32px 12px;">
            <table role="presentation" class="email-shell" width="600" cellspacing="0" cellpadding="0" border="0" style="width:600px; max-width:600px; background:#ffffff; border:1px solid #e4e4e7; border-radius:12px; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                <tr>
                    <td align="center" style="background:#09090b; border-top:6px solid #10b981; padding:26px 24px 20px;">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" width="96" alt="{{ config('app.name') }}" style="display:block; width:96px; max-width:96px; height:auto; margin:0 auto 12px; border:0;">
                        @endif
                        <div style="color:#ffffff; font-size:22px; font-weight:700; letter-spacing:0.3px;">{{ config('app.name', 'Bwibo Restaurant') }}</div>
                        <div style="color:#10b981; font-size:12px; text-transform:uppercase; letter-spacing:1.5px; margin-top:4px;">SMTP Configuration Test</div>
                    </td>
                </tr>
                <tr>
                    <td class="email-padding" style="padding:28px 32px 12px;">
                        <span style="display:inline-block; padding:6px 14px; border-radius:999px; background:#ecfdf5; color:#065f46; font-size:12px; font-weight:700; letter-spacing:0.5px; border:1px solid #a7f3d0;">
                            CONNECTION SUCCESSFUL
                        </span>
                        <h2 style="font-size:24px; line-height:30px; margin:16px 0 8px; color:#09090b; font-weight:700;">Your SMTP Setup is Working!</h2>
                        <p style="font-size:15px; line-height:22px; color:#52525b; margin:0 0 16px;">
                            This automated message confirms that your mail server credentials and transport configuration were authenticated and dispatched successfully.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="email-padding" style="padding:0 32px 24px;">
                        <div style="font-size:14px; font-weight:700; color:#27272a; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.5px;">Connection Diagnostics</div>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e4e4e7; border-radius:8px; border-collapse:separate; overflow:hidden; font-size:13px;">
                            <tr style="background:#fafafa;">
                                <td style="padding:10px 14px; color:#71717a; border-bottom:1px solid #e4e4e7; width:40%;">SMTP Host</td>
                                <td style="padding:10px 14px; color:#09090b; font-weight:600; border-bottom:1px solid #e4e4e7;">{{ $configDetails['host'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px; color:#71717a; border-bottom:1px solid #e4e4e7;">Port / Encryption</td>
                                <td style="padding:10px 14px; color:#09090b; font-weight:600; border-bottom:1px solid #e4e4e7;">{{ $configDetails['port'] ?? 'N/A' }} / {{ strtoupper($configDetails['encryption'] ?? 'None') }}</td>
                            </tr>
                            <tr style="background:#fafafa;">
                                <td style="padding:10px 14px; color:#71717a; border-bottom:1px solid #e4e4e7;">Sender (From)</td>
                                <td style="padding:10px 14px; color:#09090b; font-weight:600; border-bottom:1px solid #e4e4e7;">{{ $configDetails['from_name'] ?? '' }} &lt;{{ $configDetails['from_email'] ?? '' }}&gt;</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px; color:#71717a; border-bottom:1px solid #e4e4e7;">Delivered To</td>
                                <td style="padding:10px 14px; color:#09090b; font-weight:600; border-bottom:1px solid #e4e4e7;">{{ $recipientEmail }}</td>
                            </tr>
                            <tr style="background:#fafafa;">
                                <td style="padding:10px 14px; color:#71717a;">Test Timestamp</td>
                                <td style="padding:10px 14px; color:#09090b; font-weight:600;">{{ $timestamp }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="background:#fafafa; border-top:1px solid #e4e4e7; padding:16px 24px; color:#71717a; font-size:12px; line-height:18px;">
                        This is an automated test from {{ config('app.name') }} Admin Panel.<br>No further action is required.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
