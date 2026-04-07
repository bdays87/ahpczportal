<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Certificate</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            width:  210mm;
            height: 297mm;
            overflow: hidden;
            background: #fdfdf0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ─── PAGE SHELL ─────────────────────────────────────────── */
        .page {
            width:  210mm;
            height: 297mm;
            position: relative;
            background: #fdfdf0;
        }

        /* ─── DECORATIVE BORDER ──────────────────────────────────── */
        /* Outer solid line */
        .b-line-outer {
            position: absolute;
            inset: 5mm;
            border: 2px solid #111;
        }
        /* Dense checkerboard pattern strip (mimics Celtic/Greek-key look) */
        .b-pattern-top, .b-pattern-bottom {
            position: absolute;
            left: 5mm; right: 5mm;
            height: 9mm;
            background:
                repeating-conic-gradient(#1a1500 0% 25%, #fdfdf0 0% 50%)
                0 0 / 4.5px 4.5px;
        }
        .b-pattern-top    { top:    5mm; }
        .b-pattern-bottom { bottom: 5mm; }

        .b-pattern-left, .b-pattern-right {
            position: absolute;
            top: 5mm; bottom: 5mm;
            width: 9mm;
            background:
                repeating-conic-gradient(#1a1500 0% 25%, #fdfdf0 0% 50%)
                0 0 / 4.5px 4.5px;
        }
        .b-pattern-left  { left:  5mm; }
        .b-pattern-right { right: 5mm; }

        /* Inner solid line (inside the pattern strip) */
        .b-line-inner {
            position: absolute;
            inset: 14mm;
            border: 1.5px solid #111;
        }

        /* ─── CONTENT AREA ───────────────────────────────────────── */
        .content {
            position: absolute;
            top:    16mm;
            left:   16mm;
            right:  16mm;
            bottom: 16mm;
            display: flex;
            flex-direction: column;
        }

        /* Watermark behind content */
        .watermark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width:  160px;
            height: 160px;
            background: url('./imgs/back.jpg') center/contain no-repeat;
            opacity: 0.07;
            pointer-events: none;
            z-index: 0;
        }

        /* ─── BODY ROW: left col + divider + right col ───────────── */
        .body-row {
            flex: 1;
            display: flex;
            flex-direction: row;
            min-height: 0;
            position: relative;
            z-index: 1;
        }

        /* ── LEFT COLUMN ── */
        .left-col {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-evenly;
            padding: 6px 14px;
            text-align: center;
        }

        .h-country {
            font-size: 28pt;
            font-weight: bold;
            letter-spacing: 6px;
            text-transform: uppercase;
            line-height: 1;
        }
        .h-council {
            font-size: 10.5pt;
            margin-top: 5px;
            line-height: 1.3;
        }
        .h-reg-cert {
            font-size: 15pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 10px;
        }
        .h-certify {
            font-size: 11pt;
            margin-top: 9px;
        }
        .name-underline {
            display: inline-block;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1.5px solid #111;
            padding-bottom: 2px;
            min-width: 210px;
            letter-spacing: 1px;
        }
        .reg-of {
            font-size: 11pt;
        }
        .profession-underline {
            display: inline-block;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1.5px solid #111;
            padding-bottom: 2px;
            min-width: 210px;
        }

        /* ── COLUMN DIVIDER ── */
        .col-divider {
            width: 1px;
            background: #666;
            flex-shrink: 0;
            margin: 8px 0;
        }

        /* ── RIGHT COLUMN ── */
        .right-col {
            width: 44px;
            flex-shrink: 0;
            display: flex;
            flex-direction: row;
            align-items: stretch;
            overflow: hidden;
        }

        /* rotated text + signature stacked vertically */
        .right-inner {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            overflow: hidden;
        }

        .legal-text {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 8pt;
            line-height: 1.65;
            text-align: center;
            flex: 1;
            max-height: 100%;
            overflow: hidden;
        }

        .sig-block {
            text-align: center;
            padding-top: 6px;
        }
        .sig-block img {
            width: 44px;
            height: 34px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .sig-label {
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 2px;
            letter-spacing: 0.5px;
        }

        /* Far-right narrow vertical label */
        .vert-label {
            width: 11px;
            border-left: 1px solid #444;
            padding-left: 2px;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* ─── FOOTER: cert info + QR ────────────────────────────── */
        .cert-footer {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 4px 14px 2px;
            border-top: 1px dashed #aaa;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .cert-info {
            font-size: 7pt;
            line-height: 1.55;
            text-align: left;
        }
        .cert-info strong { font-size: 7.5pt; }
        .qr-img {
            width: 55px;
            height: 55px;
        }

        /* ─── BOTTOM BAR: NO. and DATE ──────────────────────────── */
        .bottom-bar {
            position: relative;
            z-index: 1;
            height: 26px;
            border-top: 1.5px solid #333;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 14px;
            font-size: 10pt;
            font-weight: bold;
            flex-shrink: 0;
            background: #fdfdf0;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Decorative border --}}
    <div class="b-line-outer"></div>
    <div class="b-pattern-top"></div>
    <div class="b-pattern-bottom"></div>
    <div class="b-pattern-left"></div>
    <div class="b-pattern-right"></div>
    <div class="b-line-inner"></div>

    <div class="content">
        <div class="watermark"></div>

        {{-- ── BODY ROW ── --}}
        <div class="body-row">

            {{-- LEFT: main certificate text --}}
            <div class="left-col">

                <div>
                    <div class="h-country">Zimbabwe</div>
                    <div class="h-council">{{ config('app.title') }}</div>
                </div>

                <div class="h-reg-cert">Registration Certificate</div>

                <div class="h-certify">THIS IS TO CERTIFY THAT</div>

                <div>
                    <span class="name-underline">
                        {{ strtoupper($data->customerprofession->customer->name . ' ' . $data->customerprofession->customer->surname) }}
                    </span>
                </div>

                <div class="reg-of">is registered on the register of</div>

                <div>
                    <span class="profession-underline">
                        {{ $data->customerprofession->profession->name }}
                    </span>
                </div>

            </div>

            {{-- VERTICAL DIVIDER --}}
            <div class="col-divider"></div>

            {{-- RIGHT: rotated legal text + signature + label --}}
            <div class="right-col">
                <div class="right-inner">
                    <div class="legal-text">
                        kept by the {{ config('app.title') }} in accordance with
                        the provisions of the Health Professions Act, CAP&nbsp;27:19
                    </div>
                    <div class="sig-block">
                        <img src="./imgs/signature.png" alt="Signature">
                        <div class="sig-label">Registrar</div>
                    </div>
                </div>
                <div class="vert-label">{{ config('app.title') }}</div>
            </div>

        </div>{{-- /body-row --}}

        {{-- CERT INFO + QR --}}
        <div class="cert-footer">
            <div class="cert-info">
                <strong>Certificate No:</strong> {{ $data->certificatenumber }}<br>
                {{ config('app.address') }}<br>
                Tel: {{ config('app.phone') }} &nbsp;|&nbsp; {{ config('app.email') }}
            </div>
            <img class="qr-img" src="{{ $qrcode }}" alt="QR Code">
        </div>

        {{-- NO. / DATE BOTTOM BAR --}}
        <div class="bottom-bar">
            <span>NO.&nbsp;&nbsp;&nbsp; {{ $data->customerprofession->customer->regnumber }}</span>
            <span>DATE&nbsp;&nbsp;&nbsp; {{ \Carbon\Carbon::parse($data->registrationdate)->format('d F Y') }}</span>
        </div>

    </div>{{-- /content --}}
</div>{{-- /page --}}
</body>
</html>
