<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Practitioner Guide - {{ config('app.name') }}</title>
    <style>
        @page { margin: 60px 45px 55px 45px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #0f172a; }
        .cover { text-align: center; padding: 40px 0 20px; }
        .cover img { height: 80px; }
        .cover h2 { font-size: 15px; color: #475569; margin: 12px 0 0; font-weight: normal; }
        .footer { position: fixed; bottom: -35px; left: 0; right: 0; text-align: center; font-size: 9px; color: #64748b; }
    </style>
</head>
<body>
    <div class="cover">
        @php $logo = public_path(ltrim((string) config('app.logo'), '/')); @endphp
        @if (is_file($logo))
            <img src="{{ $logo }}" alt="">
        @endif
        <h2>{{ config('app.title') }}</h2>
    </div>
    <div class="footer">{{ config('app.name') }} Practitioner Guide | {{ config('generalutils.base_url') }}/guide | {{ now()->format('d M Y') }}</div>

    @include('guide.practitioner-content', ['forPdf' => true])
</body>
</html>
