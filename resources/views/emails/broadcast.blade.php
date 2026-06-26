@php
    $appName = config('app.name', 'MLCSCZ');
    $color = config('app.color', '#1d4ed8');
    if (! $color || $color === '#000000') {
        $color = '#1d4ed8';
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? $appName }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">
                    {{-- Header --}}
                    <tr>
                        <td style="background-color:{{ $color }};padding:22px 24px;text-align:center;">
                            <span style="color:#ffffff;font-size:20px;font-weight:bold;letter-spacing:0.3px;">{{ $appName }}</span>
                        </td>
                    </tr>

                    {{-- Optional title --}}
                    @if(! empty($title))
                        <tr>
                            <td style="padding:24px 24px 0 24px;">
                                <h1 style="margin:0;font-size:18px;font-weight:bold;color:#111827;">{{ $title }}</h1>
                            </td>
                        </tr>
                    @endif

                    {{-- Body --}}
                    <tr>
                        <td style="padding:24px;font-size:15px;line-height:1.7;color:#374151;">
                            {!! $content !!}
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:18px 24px;background-color:#f9fafb;border-top:1px solid #e5e7eb;text-align:center;font-size:12px;color:#9ca3af;line-height:1.6;">
                            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.<br>
                            This is an automated message — please do not reply to this email.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
