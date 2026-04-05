<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            background: #fff;
            font-size: 9pt;
        }

        /* TOP RECEIPT SECTION */
        .top-section {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
        }

        .top-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            text-decoration: underline;
            width: 70%;
        }

        .top-no {
            font-size: 10pt;
            text-align: right;
            vertical-align: top;
            width: 30%;
        }

        .no-num {
            color: #cc0000;
            font-size: 14pt;
            font-weight: bold;
        }

        /* META FIELDS */
        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
        }

        .meta td {
            padding: 1mm 0;
            font-size: 9pt;
            font-weight: bold;
            vertical-align: top;
        }

        .meta-left {
            width: 50%;
        }

        .meta-right {
            width: 50%;
        }

        .meta-label {
            width: 22mm;
            display: inline-block;
        }

        .meta-val {
            border-bottom: 1px solid #333;
            display: inline-block;
            min-width: 55mm;
        }

        .crest-cell {
            text-align: center;
            vertical-align: middle;
            width: 30mm;
        }

        .crest-cell img {
            width: 22mm;
            height: 22mm;
            object-fit: contain;
        }

        /* BORDERED CERTIFICATE
        .cert-border {
            border: 3px solid #333;
            padding: 2px;
            margin-top: 2mm;
            cert-border
        } */

        /* .cert-inner-border {
            border: 2px solid #333;
            padding: 4mm 6mm;
            background-image: url('./logo/hivrib.jpg');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 40%;
        } */

        /* CERT HEADER */
        .cert-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 2mm;
        }

        .cert-contact {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            margin-bottom: 3mm;
        }

        .cert-contact td {
            vertical-align: top;
        }

        .cert-contact-right {
            text-align: right;
        }

        .cert-act {
            text-align: center;
            font-size: 9.5pt;
            font-weight: bold;
            margin-bottom: 1mm;
        }

        .cert-hiv {
            text-align: center;
            font-size: 8.5pt;
            text-decoration: underline;
               margin-top: 10px;
            margin-bottom: 10px;
        }

        .cert-main-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        /* CERT FIELDS */
        .cf {
            font-size: 9pt;
            margin-bottom: 4mm;
        }

        .cf-label {
            font-weight: normal;
        }

        .cf-val {
            border-bottom: 1px solid #333;
            display: inline-block;
            min-width: 80mm;
        }

        .cf-conditions {
            font-size: 9pt;
            margin-bottom: 4mm;
        }

        .cf-conditions-text {
            font-size: 8.5pt;
            margin-left: 22mm;
            line-height: 1.5;
        }

        /* FOOTER */
        .cert-footer {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5mm;
        }

        .cert-footer td {
            vertical-align: bottom;
            padding: 0;
            font-size: 9pt;
            font-weight: bold;
        }

        .footer-date {
            width: 55%;
        }

        .footer-reg {
            width: 45%;
            text-align: right;
        }

        .dots {
            letter-spacing: 1px;
        }

        .sig-img {
            width: 38mm;
            height: 13mm;
            object-fit: contain;
            display: inline-block;
            vertical-align: bottom;
        }
    </style>
</head>

<body style="padding: 10px!important">

    {{-- TOP RECEIPT --}}
    <table class="top-section" style="margin-left: 10px!important">
        <tr>
            <td class="top-title">
                MEDICAL LABORATORY &amp; CLINICAL<br>SCIENTISTS COUNCIL ZIMBABWE
            </td>
            <td class="top-no">
                No. &nbsp;<span class="no-num">{{ $data->certificate_number }}</span>
            </td>
        </tr>
    </table>

    {{-- META FIELDS WITH CREST --}}
    <table class="meta" style="margin-left: 10px!important">
        <tr>
            <td class="meta-left">
                <div><span class="meta-label">NAME</span><span
                        class="meta-val">{{ strtoupper($data->customerprofession->customer->name . ' ' . $data->customerprofession->customer->surname) }}</span>
                </div>
                <div style="margin-top:1mm;"><span class="meta-label">PROFESSION</span><span
                        class="meta-val">{{ strtoupper($data->customerprofession->profession->name) }}</span></div>
                <div style="margin-top:1mm;"><span class="meta-label">AMOUNT</span><span class="meta-val"></span></div>
                <div style="margin-top:1mm;"><span class="meta-label">DATE</span><span
                        class="meta-val">{{ \Carbon\Carbon::parse($data->registration_date)->format('d F Y') }}</span>
                </div>
            </td>
            <td class="crest-cell">
                <img src="./logo/mlcscz.png" alt="Crest">
            </td>
            <td class="meta-right">
                <div><span class="meta-label">REG. No.</span><span
                        class="meta-val">{{ $data->customerprofession->customer->regnumber }}</span></div>
                <div style="margin-top:1mm;"><span class="meta-label">FOR</span><span class="meta-val"></span></div>
            </td>
        </tr>
    </table>

    {{-- BORDERED CERTIFICATE --}}
    <div class="cert-border"  style="width: 180mm;height: 230mm;margin: 0 auto;border: 5px solid black;padding: 8mm;position: relative;background-image:url('./logo/hivrib.png');background-size: 75% !important;background-repeat: no-repeat;background-position: center;background-color: #ffffff;box-sizing: border-box;">
        <div class="cert-inner-border">

            <div class="cert-title">MEDICAL LABORATORY &amp; CLINICAL<br>SCIENTISTS COUNCIL ZIMBABWE</div>

            <table class="cert-contact">
                <tr>
                    <td>71 Suffolk Road<br>Avondale West, Harare<br>Tel: (263) (242) 303348<br>Fax: (263) (242)
                        303348<br>E-mail: mlcscz@zol.co.zw<br>Website: www.mlcscz.org</td>
                    <td class="cert-contact-right">P. O. Box A1620<br>Avondale<br>Harare</td>
                </tr>
            </table>

            <div class="cert-act">HEALTH PROFESSIONS ACT</div>
            <br/>   <br/>
            <div class="cert-act">(CHAPTER 27:19)</div>
              <br/>   <br/>
            <div class="cert-hiv">RAPID HUMAN IMMUNO-DEFICIENCY VIRUS (HIV) TESTING</div>
              <br/>   <br/>
            <div class="cert-main-title">PRACTISING CERTIFICATE</div>
  <br/>   <br/>  <br/>   <br/>
            <div class="cf">This is to certify that &nbsp;<span
                    class="cf-val">{{ strtoupper($data->customerprofession->customer->name . ' ' . $data->customerprofession->customer->surname) }}</span>
            </div>
            <div class="cf">Reg. No. &nbsp;<span
                    class="cf-val">{{ $data->customerprofession->customer->regnumber }}</span></div>
            <div class="cf">Is authorised to practice as a Rapid HIV Testing Health Practitioner</div>
             <br/>   <br/>  <br/>   <br/>
            <div class="cf-conditions">
                <span style="font-weight:bold;">Conditions:</span> &nbsp; To be employed only in a Registered HIV
                Testing &amp; Counselling Site<br>
                <span class="cf-conditions-text">(HTC) and not authorised to operate a laboratory testing service
                    independently</span>
            </div>
 <br/>   <br/>  <br/>   <br/>
            <div class="cf">This certificate expires on &nbsp;<span
                    class="cf-val">{{ $data->certificate_expiry_date }}</span></div>
 <br/>   <br/>  <br/>   <br/> <br/>   <br/>  <br/>   <br/>
            <table class="cert-footer">
                <tr>
                    <td class="footer-date">Date <span
                            class="dots">................................................</span></td>
                    <td class="footer-reg">
                        Registrar &nbsp;<img class="sig-img" src="./imgs/signature.png" alt="Signature">
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>

</html>
