<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ config('app.name') }}</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        a { color: inherit; text-decoration: none; }
        @media only screen and (max-width: 600px) {
            .responsive-container { width: 100% !important; max-width: 100% !important; }
            .pad-main { padding: 24px 20px !important; }
            .pad-sm { padding: 16px 20px !important; }
            .detail-row td { padding-bottom: 14px !important; }
        }
    </style>
</head>
<body>
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f1f5f9;min-width:100%;">
<tr>
<td align="center" style="padding:28px 16px;">

    <!-- Outer wrapper card -->
    <table role="presentation" cellpadding="0" cellspacing="0" width="600" class="responsive-container" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(15,23,42,0.08);">

        <!-- Brand header -->
        <tr>
            <td style="background-color:#15803d;background:linear-gradient(135deg,#15803d 0%,#166534 100%);padding:28px 32px;text-align:center;" class="pad-main">
                <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:-0.5px;">{{ config('app.name') }}</h1>
                <p style="margin:6px 0 0;color:#86efac;font-size:13px;font-weight:600;letter-spacing:0.4px;text-transform:uppercase;">@yield('subtitle', 'Notification')</p>
            </td>
        </tr>

        <!-- Status badge row (rendered when $statusLabel is provided) -->
        @if(isset($statusLabel))
        <tr>
            <td style="background-color:{{ $statusBg ?? '#f8fafc' }};padding:16px 32px;text-align:center;" class="pad-sm">
                <span style="display:inline-block;padding:5px 18px;border-radius:999px;background-color:{{ $statusBadgeBg ?? '#e0f2fe' }};color:{{ $statusBadgeText ?? '#0369a1' }};font-size:13px;font-weight:600;border:1px solid {{ $statusBadgeBorder ?? '#bae6fd' }};">{{ $statusLabel }}</span>
            </td>
        </tr>
        @endif

        <!-- Main body -->
        <tr>
            <td style="padding:32px;" class="pad-main">
                @yield('body')
            </td>
        </tr>

        <!-- Divider -->
        <tr>
            <td style="padding:0 32px;">
                <hr style="border:none;border-top:1px solid #e2e8f0;margin:0;" />
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="padding:24px 32px;text-align:center;" class="pad-sm">
                <p style="margin:0;color:#94a3b8;font-size:12px;line-height:1.7;">
                    You are receiving this email because of activity on your<br>
                    <strong style="color:#64748b;">{{ config('app.name') }}</strong> account.
                </p>
                <p style="margin:10px 0 0;color:#cbd5e1;font-size:11px;">
                    If you did not expect this email, you can safely ignore it.
                </p>
            </td>
        </tr>

    </table>

    <!-- Copyright line below card -->
    <table role="presentation" cellpadding="0" cellspacing="0" width="600" class="responsive-container" style="max-width:600px;width:100%;">
        <tr>
            <td style="padding:18px 0;text-align:center;">
                <p style="margin:0;color:#94a3b8;font-size:11px;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </td>
        </tr>
    </table>

</td>
</tr>
</table>
</body>
</html>
