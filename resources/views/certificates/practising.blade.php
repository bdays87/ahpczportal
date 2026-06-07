<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Practising Certificate</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #ffffff;
            color: #111;
            width: 210mm;
        }

        /* ── Fixed borders (always on page regardless of content) ── */
        .border-outer {
            position: fixed;
            top: 5mm; left: 5mm; right: 5mm; bottom: 5mm;
            border: 4px solid #000000;
            z-index: 0;
        }

        .border-inner {
            position: fixed;
            top: 9mm; left: 9mm; right: 9mm; bottom: 9mm;
            border: 2px dashed #000000;
            z-index: 0;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            width: 110mm;
            height: 110mm;
            margin-top: -55mm;
            margin-left: -55mm;
            opacity: 0.08;
            z-index: 0;
        }

        /* ── Fixed footer ── */
        .footer-section {
            position: fixed;
            bottom: 12mm;
            left: 14mm;
            right: 14mm;
            z-index: 5;
        }

        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { vertical-align: bottom; padding: 0; }
        .f-left  { text-align: left;  width: 50%; }
        .f-right { text-align: right; width: 50%; }

        .qr-img  { width: 20mm; height: 20mm; display: block; margin-bottom: 1mm; }
        .sig-img { width: 22mm; height: 22mm; display: block; margin-left: auto; margin-bottom: 1mm; }
        .sig-line { width: 38mm; border-top: 1px solid #111; margin-left: auto; margin-bottom: 1mm; }
        .date-label { font-size: 7.5pt; font-weight: bold;margin-left: -86px;margin-top: 15px }
        .sig-label  { font-size: 7.5pt; font-weight: bold; }

        /* ── Content (normal flow) ── */
        .content {
            position: relative;
            z-index: 2;
            padding: 13mm 15mm 42mm 15mm;
        }

        .cert-number {
            text-align: right;
            font-size: 7.5pt;
            margin-bottom: 2mm;
        }
        .cert-number .prefix { color: #c0392b; font-weight: bold; }

        .logo-wrap { text-align: center; margin-bottom: 2mm; }
        .logo-wrap img { height: 18mm; width: auto; }

        .council-name {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 0.2mm;
            line-height: 1.3;
            margin-bottom: 2mm;
        }

        .contact-info {
            text-align: center;
            font-size: 7.5pt;
            color: #444;
            line-height: 1.6;
        }
        .contact-info a { color: #1473d2; text-decoration: none; }

        .divider {
            border: none;
            border-top: 1.5px solid #c89e15;
            margin: 3mm 8mm;
        }

        .act-ref {
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            color: #219fff;
            line-height: 1.5;
            margin: 2mm 0 1mm 0;
        }

        .cert-title {
            text-align: center;
            font-size: 24pt;
            font-weight: bold;
            color: #434ee5;
            letter-spacing: 1mm;
            text-transform: uppercase;
            margin: 2mm 0 4mm 0;
        }

        .details-table {
            width: 78%;
            margin: 0 auto 3mm auto;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 2mm 3mm;
            font-size: 10pt;
            vertical-align: top;
            line-height: 1.4;
        }
        .details-table .lbl { color: #555; width: 44%; font-size: 12pt; }
        .details-table .sep { width: 4%; color: #555; }
        .details-table .val { font-weight: bold; color: #111; }

        .conditions-block {
            text-align: center;
            margin: 1mm 10mm 2mm 10mm;
            font-size: 8.5pt;
            color: #444;
            line-height: 1.5;
        }
        .conditions-title { font-weight: bold; color: #1737b4; font-size: 12pt; }

        .expiry-block {
            text-align: center;
            margin: 4mm 0 2mm 0;
            font-size: 12pt;
            color: #444;
        }
        .expiry-date {
            font-size: 12pt;
            font-weight: bold;
            color: #0a4ab0;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    {{-- Fixed decorative borders --}}
    <div class="border-outer"></div>
    <div class="border-inner"></div>

    {{-- Watermark logo --}}
    <img src="{{ public_path('logo/mlcscz.png') }}" class="watermark" alt="">

    {{-- Fixed footer: always pinned to bottom of page --}}
    <div class="footer-section">
        <table class="footer-table">
            <tr>
                <td class="f-left">
                    <img src="{{ $qrcode }}" class="qr-img" alt="QR Code">
                    <span class="date-label"><br>
                        Date: {{ $data->registration_date ? \Carbon\Carbon::parse($data->registration_date)->format('d F Y') : '' }}
                    </span>
                </td>
                <td class="f-right">
                    <img src="{{ public_path('imgs/signature.png') }}" class="sig-img" alt="Signature">
                    <div class="sig-line"></div>
                    <span class="sig-label">REGISTRAR</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Normal flow content --}}
    <div class="content">

        <div class="cert-number">
            <strong>{{ $data->certificate_number }}</strong>
        </div>

        <div class="logo-wrap">
            <img src="{{ public_path('logo/mlcscz.png') }}" alt="MLCSCZ">
        </div>

        <div class="council-name">
            MEDICAL LABORATORY CLINICAL SCIENTISTS COUNCIL ZIMBABWE
        </div>

        <div class="contact-info">
            71 Suffolk Road Avondale West, Harare &nbsp;|&nbsp; Tel: (263) (04) 303348 / Fax: (263) (04) 303348<br>
            Email: <a href="mailto:mlcscz@zol.co.zw">mlcscz@zol.co.zw</a>
            &nbsp;|&nbsp; Website: <a href="http://www.mlcscz.org">-www.mlcscz.org</a>
        </div>
<br/><br/>
        <hr class="divider">
<br/><br/>
        <div class="act-ref">
           <h2> HEALTH PROFESSIONS ACT<br>(CHAPTER 27:19)</h2>
        </div>
<br/><br/>
        <div class="cert-title">PRACTISING CERTIFICATE</div>
<br/><br/>
        <table class="details-table">
            <tr>
                <td class="lbl">This is to certify that</td>
                <td class="sep">:</td>
                <td class="val">{{ strtoupper($data->customerprofession->customer->name . ' ' . $data->customerprofession->customer->surname) }}</td>
            </tr>
            <tr>
                <td class="lbl">Registration Number</td>
                <td class="sep">:</td>
                <td class="val">
                     {{ $data->customerprofession->registrationnumber ?? '' }}
                    {{-- {{ ($data->customerprofession->customer->prefix ?? '') . '-' . $data->customerprofession->customer->regnumber }} --}}
                </td>
            </tr>
            <tr>
                <td class="lbl">Is Authorised to practise as a/an</td>
                <td class="sep">:</td>
                <td class="val">{{ $data->customerprofession->profession->name }}</td>
            </tr>

        </table>
<br/><br/>
        <hr class="divider">

        <div class="conditions-block">
            <span class="conditions-title">Condition/s</span><br><br>
            @php
                $condition = $data->customerprofession->profession?->conditions
                    ->where('customertype_id', $data->customerprofession->customertype_id)
                    ->first();
            @endphp
           <p style="font-size: 12pt"><b>{{ $condition?->condition ?? '' }}</b></p>
        </div><br>
        <div class="expiry-block">
            This certificate expires on<br><br/>



            <span class="expiry-date">
                {{ $data->certificate_expiry_date ? \Carbon\Carbon::parse($data->certificate_expiry_date)->format('d F Y') : '' }}
            </span>
        </div>

    </div>

</body>
</html>
