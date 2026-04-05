<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Competency - HIV Testing</title>
    <style>
        @page { size: A4 landscape; margin: 0; }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Times New Roman", Times, serif;
            width: 297mm;
            height: 210mm;
            background: #fff;
            -webkit-print-color-adjust: exact;
        }

        .page {
            width: 297mm;
            height: 210mm;
            position: relative;
        }

        /* Double border */
        .b-outer {
            position: absolute;
            top: 6mm; left: 6mm; right: 6mm; bottom: 6mm;
            border: 3px solid #111;
        }
        .b-inner {
            position: absolute;
            top: 9mm; left: 9mm; right: 9mm; bottom: 9mm;
            border: 1px solid #111;
        }

        /* Layout table */
        .wrap {
            position: absolute;
            top: 11mm; left: 11mm; right: 11mm; bottom: 11mm;
            width: calc(297mm - 22mm);
            height: calc(210mm - 22mm);
            border-collapse: collapse;
        }

        .td-top {
            vertical-align: top;
            text-align: center;
            padding: 8mm 10mm 0 10mm;
        }

        .td-bot {
            vertical-align: bottom;
            padding: 0 10mm 6mm 10mm;
            height: 38mm;
        }

        /* QR + serial top-right */
        .top-right {
            text-align: right;
            margin-bottom: 4mm;
        }
        .top-right img { width: 18mm; height: 18mm; display: inline-block; }
        .serial { font-size: 8pt; font-weight: bold; margin-top: 2px; }

        /* Act title */
        .act-title {
            font-size: 11pt;
            text-decoration: underline;
            font-weight: bold;
            margin-bottom: 5mm;
        }

        /* Logo */
        .logo { width: 22mm; height: 22mm; object-fit: contain; display: block; margin: 0 auto 3mm; }

        /* Council name */
        .council { font-size: 11pt; font-weight: bold; line-height: 1.4; margin-bottom: 5mm; }

        /* Thick lines around cert type */
        .thick-line { border: none; border-top: 3px solid #111; width: 70%; margin: 0 auto; }

        .cert-type {
            font-size: 13pt;
            color: #1a237e;
            font-style: italic;
            font-weight: bold;
            margin: 3mm 0;
        }

        /* Body */
        .intro { font-size: 11pt; font-weight: bold; margin: 6mm 0 3mm; }

        .name-block {
            font-size: 20pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #111;
            display: inline-block;
            min-width: 80%;
            padding-bottom: 2px;
            margin-bottom: 5mm;
            letter-spacing: 1px;
        }

        .desc { font-size: 10pt; line-height: 1.6; margin: 3mm auto; width: 85%; }
        .auth { font-size: 10pt; font-weight: bold; line-height: 1.6; margin: 3mm auto; width: 85%; }

        /* Footer table */
        .ft { width: 100%; border-collapse: collapse; }
        .ft td { vertical-align: bottom; padding: 0; }
        .ftl { width: 45%; text-align: left; }
        .ftr { width: 55%; text-align: center; }

        .date-row { font-size: 9pt; font-weight: bold; margin-bottom: 2mm; }
        .no-row   { font-size: 9pt; font-weight: bold; }
        .dots     { letter-spacing: 2px; font-weight: normal; }

        .sig-block { margin-bottom: 8mm; }
        .sig-line  { border-top: 1.5px solid #111; width: 70%; margin: 0 auto; }
        .sig-title { font-size: 9pt; font-weight: bold; margin-top: 3px; }
    </style>
</head>
<body>
<div class="page">

    <div class="b-outer"></div>
    <div class="b-inner"></div>

    <table class="wrap">
        <tr>
            <td class="td-top">

                {{-- QR + serial top right --}}
                <div class="top-right">
                    <img src="{{ $qrcode }}" alt="QR Code">
                    <div class="serial">Serial No. {{ $data->customerprofession->customer->regnumber }}</div>
                </div>

                <div class="act-title">HEALTH PROFESSIONS ACT (CHAPTER 27:19)</div>

                <img class="logo" src="./logo/mlcscz.png" alt="Logo">

                <div class="council">
                    MEDICAL LABORATORY &amp; CLINICAL SCIENTISTS COUNCIL<br>OF ZIMBABWE
                </div>

                <hr class="thick-line">
                <div class="cert-type">Certificate of Competency in Rapid HIV Testing</div>
                <hr class="thick-line">

                <div class="intro">This is to certify that</div>

                <div class="name-block">
                    {{ strtoupper($data->customerprofession->customer->name . ' ' . $data->customerprofession->customer->surname) }}
                </div>

                <div class="desc">
                    successfully completed an approved course in Rapid Human Immuno-deficiency Virus Testing conducted by the
                </div>
                <div class="auth">
                    Medical Laboratory &amp; Clinical Scientists Council of Zimbabwe in conjunction with<br>
                    the Ministry of Health &amp; Child Care Zimbabwe
                </div>

            </td>
        </tr>
        <tr>
            <td class="td-bot">
                <table class="ft">
                    <tr>
                        <td class="ftl">
                            <div class="date-row">DATE &nbsp; {{ \Carbon\Carbon::parse($data->registrationdate)->format('d F Y') }}</div>
                            <div class="no-row">No. <span class="dots">................................................</span></div>
                        </td>
                        <td class="ftr">
                            <div class="sig-block">
                                <div class="sig-line"></div>
                                <div class="sig-title">Chairman</div>
                            </div>
                            <div class="sig-block">
                                <div class="sig-line"></div>
                                <div class="sig-title">Chief Executive Officer</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>
</body>
</html>
