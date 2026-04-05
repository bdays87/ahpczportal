<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Practising Certificate - MLCSCZ</title>
<style>
@page { size: A4 portrait; margin: 0; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: "Times New Roman", Times, serif; width: 210mm; height: 297mm; overflow: hidden; background: #fff; }
.page { width: 210mm; height: 297mm; position: relative; }
.wrap { position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 210mm; height: 297mm; border-collapse: collapse; }
.td-top { vertical-align: top; padding: 8mm 10mm 0 10mm; }
.td-bot { vertical-align: bottom; padding: 0 10mm 6mm 10mm; height: 20mm; }

/* Top receipt header */
.council-title { font-size: 13pt; font-weight: bold; text-align: center; padding-right: 25mm; }
.receipt-no { font-size: 11pt; color: #b71c1c; font-weight: bold; text-align: right; }

.meta-table { width: 100%; border-collapse: collapse; margin-top: 5mm; font-size: 9pt; font-weight: bold; }
.meta-table td { padding: 2px 4px; }
.meta-label { width: 22mm; }
.meta-val { border-bottom: 1px solid #555; width: 60mm; }

/* Certificate bordered area */
.cert-outer { border: 3px double #333; padding: 3px; margin-top: 5mm; }
.cert-inner { border: 1px solid #333; padding: 6mm 8mm; position: relative; }

/* Contact info */
.contact-table { width: 100%; border-collapse: collapse; font-size: 7pt; font-weight: bold; line-height: 1.4; margin-bottom: 4mm; }
.contact-table td { vertical-align: top; }
.contact-right { text-align: right; }

/* Inner titles */
.inner-titles { text-align: center; margin-bottom: 5mm; }
.inner-titles .t1 { font-size: 11pt; font-weight: bold; margin-bottom: 2px; }
.inner-titles .t2 { font-size: 10pt; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
.inner-titles .t3 { font-size: 13pt; font-weight: bold; text-decoration: underline; letter-spacing: 2px; margin-top: 3px; }

/* Content fields */
.field { font-size: 10pt; font-weight: bold; margin-bottom: 5mm; line-height: 2; }
.field span { border-bottom: 1px solid #333; display: inline-block; min-width: 80mm; }

/* Footer */
.footer-table { width: 100%; border-collapse: collapse; }
.footer-table td { vertical-align: bottom; padding: 0; }
.date-cell { width: 50%; }
.date-line { border-bottom: 1px dotted #000; font-size: 9pt; font-weight: bold; padding-bottom: 2px; }
.reg-cell { width: 50%; text-align: right; }
.sig-area { font-size: 9pt; font-weight: bold; }
.sig-line { border-top: 1px solid #000; margin-top: 10mm; padding-top: 3px; }
</style>
</head>
<body>
<div class="page">
<table class="wrap">
<tr>
<td class="td-top">

    {{-- Top header --}}
    <table style="width:100%;border-collapse:collapse;">
        <tr>
            <td class="council-title">MEDICAL LABORATORY &amp; CLINICAL SCIENTISTS COUNCIL ZIMBABWE</td>
            <td class="receipt-no">No. {{ $data->certificate_number }}</td>
        </tr>
    </table>

    {{-- Meta fields --}}
    <table class="meta-table">
        <tr>
            <td class="meta-label">NAME</td>
            <td class="meta-val">{{ strtoupper($data->customerprofession->customer->name . ' ' . $data->customerprofession->customer->surname) }}</td>
            <td class="meta-label">REG. No.</td>
            <td class="meta-val">{{ $data->customerprofession->customer->regnumber }}</td>
        </tr>
        <tr>
            <td class="meta-label">PROFESSION</td>
            <td class="meta-val" colspan="3">{{ strtoupper($data->customerprofession->profession->name) }}</td>
        </tr>
        <tr>
            <td class="meta-label">DATE</td>
            <td class="meta-val" colspan="3">{{ \Carbon\Carbon::parse($data->registration_date)->format('d F Y') }}</td>
        </tr>
    </table>

    {{-- Certificate bordered area --}}
    <div class="cert-outer">
        <div class="cert-inner">

            {{-- Contact info --}}
            <table class="contact-table">
                <tr>
                    <td>
                        71 Suffolk Road<br>
                        Avondale West, Harare<br>
                        Tel: (263) (04) 303348<br>
                        Fax: (263) (04) 303348<br>
                        E-mail: mlcscz@zol.co.zw<br>
                        Website: www.mlcscz.org
                    </td>
                    <td class="contact-right">
                        P. O. Box A1620<br>
                        Avondale<br>
                        Harare
                    </td>
                </tr>
            </table>

            {{-- Inner titles --}}
            <div class="inner-titles">
                <div class="t1">MEDICAL LABORATORY &amp; CLINICAL SCIENTISTS COUNCIL ZIMBABWE</div>
                <div class="t2">HEALTH PROFESSIONS ACT</div>
                <div class="t2">(CHAPTER 27:19)</div>
                <div class="t3">PRACTISING CERTIFICATE</div>
            </div>

            {{-- Content fields --}}
            <div class="field">This is to certify that <span>{{ strtoupper($data->customerprofession->customer->name . ' ' . $data->customerprofession->customer->surname) }}</span></div>
            <div class="field">Reg. No. <span>{{ $data->customerprofession->customer->regnumber }}</span></div>
            <div class="field">Is authorised to practice as a <span>{{ strtoupper($data->customerprofession->profession->name) }}</span></div>
            <div class="field">Conditions <span>N/A</span></div>
            <div class="field">This certificate expires on <span>{{ $data->certificate_expiry_date }}</span></div>

            {{-- Footer --}}
            <table class="footer-table">
                <tr>
                    <td class="date-cell">
                        <div class="date-line">Date: {{ \Carbon\Carbon::parse($data->registration_date)->format('d F Y') }}</div>
                    </td>
                    <td class="reg-cell">
                        <div class="sig-area">
                            @if(file_exists(public_path('imgs/signature.png')))
                            <img src="./imgs/signature.png" style="width:40mm;height:14mm;object-fit:contain;display:block;margin-left:auto;">
                            @endif
                            <div class="sig-line">Registrar</div>
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

</td>
</tr>
<tr>
<td class="td-bot"></td>
</tr>
</table>
</div>
</body>
</html>
