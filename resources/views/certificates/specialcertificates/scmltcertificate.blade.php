<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>SCMLT Certificate</title>
<style>
@page { size: A4 portrait; margin: 0; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: "Times New Roman", Times, serif; width: 210mm; height: 297mm; overflow: hidden; background: #fff; }
.b-pattern {
    position: absolute; top: 4mm; left: 4mm; right: 4mm; bottom: 4mm;
    border: 4px solid #111;
    background-image: repeating-conic-gradient(#111 0% 25%, #fff 0% 50%);
    background-size: 5px 5px;
}
.b-fill {
    position: absolute; top: 12mm; left: 12mm; right: 12mm; bottom: 12mm;
    background: #fff; border: 1.5px solid #111;
}
.wrap {
    position: absolute; top: 14mm; left: 14mm; right: 14mm; bottom: 14mm;
    width: calc(210mm - 28mm); height: calc(297mm - 28mm);
    border-collapse: collapse;
}
.td-top { vertical-align: top; text-align: center; padding: 5mm 8mm 0 8mm; }
.td-bot { vertical-align: bottom; padding: 0 8mm 4mm 8mm; height: 55mm; }

.title { font-size: 14pt; font-weight: bold; text-transform: uppercase; text-align: center; margin-bottom: 1mm; }
.serial { text-align: right; font-size: 9pt; font-weight: bold; margin-bottom: 2mm; }
.serial span { color: #cc0000; font-size: 13pt; font-weight: bold; }
.crest { width: 30mm; height: 30mm; object-fit: contain; display: block; margin: 3mm auto; }
.cert-type { font-size: 10pt; font-weight: bold; text-transform: uppercase; text-align: center; margin: 3mm 0; letter-spacing: 0.5px; }
.certify { font-style: italic; font-size: 11pt; text-align: center; margin: 4mm 0 2mm; }
.name-dots { text-align: center; font-size: 9pt; letter-spacing: 2px; margin: 2mm 0 3mm; border-bottom: 1px dotted #333; width: 80%; margin-left: auto; margin-right: auto; padding-bottom: 1mm; }
.name-val { font-size: 11pt; font-weight: bold; text-transform: uppercase; text-align: center; margin-bottom: 3mm; }
.body-text { font-size: 9.5pt; font-weight: bold; text-align: center; line-height: 1.7; width: 85%; margin: 0 auto; }

.ft { width: 100%; border-collapse: collapse; }
.ft td { vertical-align: bottom; padding: 0; }
.seal-cell { width: 35%; vertical-align: bottom; }
.seal { width: 28mm; height: 28mm; border-radius: 50%; background-color: #c94c4c; display: block; }
.sig-cell { width: 65%; vertical-align: bottom; text-align: center; }
.sig-block { margin-bottom: 5mm; }
.sig-img { width: 38mm; height: 13mm; object-fit: contain; display: block; margin: 0 auto; }
.sig-line { border-top: 1.5px solid #000; width: 70%; margin: 0 auto; padding-top: 2px; }
.sig-label { font-size: 9pt; font-weight: bold; text-align: center; }
.sig-sub { font-size: 6pt; font-weight: bold; text-align: center; }

.bot-row { width: 100%; border-collapse: collapse; margin-top: 3mm; }
.bot-row td { vertical-align: bottom; padding: 0; }
.date-cell { width: 50%; }
.date-line { border-bottom: 1px solid #000; display: inline-block; min-width: 40mm; font-size: 8.5pt; }
.date-label { font-size: 8.5pt; font-weight: bold; margin-top: 1mm; }
.scmlt-cell { width: 50%; text-align: right; font-size: 8.5pt; font-weight: bold; }
</style>
</head>
<body>
<div class="b-pattern"></div>
<div class="b-fill"></div>
<table class="wrap">
<tr>
<td class="td-top">
    <div class="serial">Serial No. <span>{{ $data->certificate_number }}</span></div>
    <div class="title">MEDICAL LABORATORY &amp; CLINICAL<br>SCIENTISTS COUNCIL ZIMBABWE</div>
    <img class="crest" src="./logo/mlcscz.png" alt="Crest">
    <div class="cert-type">STATE CERTIFIED MEDICAL LABORATORY TECHNICIAN CERTIFICATE</div>
    <br> <br>
    <div class="certify">This is to certify that</div>
    <br>
    <div class="name-val">{{ strtoupper($data->customerprofession->customer->name . ' ' . $data->customerprofession->customer->surname) }}</div>
<br> <br> <br>  <br>
    <div class="body-text">
        has passed the final examination for State Certified Medical Laboratory
        Technicians conducted by the Medical Laboratory and Clinical Scientists
        Council of Zimbabwe and has successfully completed a two year prescribed
        course of training
    </div>
</td>
</tr>
<tr>
<td class="td-bot">
    <table class="ft">
    <tr>
    <td class="seal-cell">
          <br> <br>
        <div class="seal"></div>
    </td>
    <td class="sig-cell">
          <br> <br> <br>
            <br> <br> <br>
        <div class="sig-block">
            <br> <br> <br>
            <img class="sig-img" src="./imgs/signature.png" alt="Chairman Signature">
            <div class="sig-line"></div>
            <div class="sig-label">Chairman</div>
            <div class="sig-sub">Medical Laboratory and Clinical Scientists Council of Zimbabwe</div>
 <br> <br> <br>
        </div>
        <div class="sig-block">
            <img class="sig-img" src="./imgs/signature.png" alt="Registrar Signature">
            <div class="sig-line"></div>
            <div class="sig-label">Registrar</div>
            <div class="sig-sub">Medical Laboratory and Clinical Scientists Council of Zimbabwe</div>
        </div>
    </td>
    </tr>
    </table>
    <table class="bot-row">
    <tr>
    <td class="date-cell">
        <div class="date-line">Date:  {{ \Carbon\Carbon::parse($data->registration_date)->format('d F Y') }}</div>

        <br> <br>
         <div class="scmlt-cell" style="text-align: left">SCMLT:</div>
    </td>

    </tr>
    </table>
</td>
</tr>
</table>
</body>
</html>
