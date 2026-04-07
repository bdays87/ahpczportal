<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
@page { size: A4 landscape; margin: 0; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: serif; width: 297mm; height: 210mm; overflow: hidden; }
.b-pattern {
    position: absolute; top: 4mm; left: 4mm; right: 4mm; bottom: 4mm;
    border: 4px solid #111;
    background-image: repeating-conic-gradient(#111 0% 25%, #fff 0% 50%);
    background-size: 5px 5px;
}
.b-fill {
    position: absolute; top: 12mm; left: 12mm; right: 12mm; bottom: 12mm;
    background: #fff; border: 2px solid #111;
}
.crest {
    position: absolute; top: 5mm; left: 50%; margin-left: -16mm;
    width: 32mm; z-index: 10; background: #fff;
    border: 2px solid #111; padding: 2px; text-align: center;
}
.crest img { width: 28mm; height: 28mm; object-fit: contain; display: block; }
.wrap {
    position: absolute; top: 14mm; left: 14mm; right: 14mm; bottom: 14mm;
    width: calc(297mm - 28mm); height: calc(210mm - 28mm);
    border-collapse: collapse;
}
.td-top { vertical-align: middle; text-align: center; padding: 8mm 14mm 0 14mm; }
.td-bot { vertical-align: bottom; padding: 0 14mm 5mm 14mm; height: 30mm; }
.no { text-align: left; font-size: 8pt; font-weight: bold; margin-bottom: 5mm; }
.no span { display: inline-block; border-bottom: 1px solid #111; min-width: 28mm; margin-left: 2mm; }
.c1 { font-size: 17pt; font-weight: bold; letter-spacing: 4px; text-transform: uppercase; }
.c2 { font-size: 9pt; margin-top: 3px; }
.c3 { font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-top: 4px; }
.c4 { font-size: 10pt; font-weight: bold; margin-top: 4px; }
.hr { border: none; border-top: 1px solid #111; margin: 4mm 0; }
.nm { font-size: 15pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin: 3mm 0; }
.ro { font-size: 9pt; margin: 3mm 0; }
.pr { font-size: 12pt; font-weight: bold; text-transform: uppercase; margin: 3mm 0; }
.lp { font-size: 9.5pt; line-height: 1.6; margin: 3mm 0; }
.ft { width: 100%; border-collapse: collapse; }
.ft td { vertical-align: bottom; padding: 0; }
.ftl { width: 40%; }
.ftr { width: 60%; text-align: right; }
.db { font-size: 8pt; font-weight: bold; }
.db span { display: inline-block; border-bottom: 1px solid #111; min-width: 32mm; margin-left: 2mm; }
.qi { width: 22mm; height: 22mm; display: block; margin-top: 3mm; }
.si { width: 42mm; height: 16mm; object-fit: contain; display: block; margin-left: auto; }
.sl { border-top: 1px solid #111; padding-top: 3px; text-align: right; font-size: 7.5pt; font-weight: bold; text-transform: uppercase; }
.sc { font-size: 6pt; text-transform: uppercase; text-align: right; margin-top: 2px; }
</style>
</head>
<body>
<div class="b-pattern" style="background-image: url('./logo/reg.png')!important;background-size: 100%!important;background-repeat: no-repeat!important;"></div>
<div class="b-fill"></div>
<div class="crest"><img src="./logo/mlcscz.png" alt="Crest"></div>
<table class="wrap">
<tr>
<td class="td-top">
    <br/><br/><br/>
<div class="no">NO.<span>{{ $data->customerprofession->customer->regnumber }}</span></div>
<div class="c1">Zimbabwe</div>
<div class="c2">{{ config('app.title') }}</div>
<div class="c3">Registration Certificate</div>
<div class="c4">This is to Certify That</div>
<hr class="hr">
<div class="nm">{{ strtoupper($data->customerprofession->customer->name . ' ' . $data->customerprofession->customer->surname) }}</div>
<div class="ro">is registered on the register of</div>
<hr class="hr">
<div class="pr">{{ strtoupper($data->customerprofession->profession->name) }}</div>
<hr class="hr">
<br/><br/><br/>
<div class="lp">kept by the {{ config('app.title') }}<br>in accordance with the provisions of the Health Professions Act, CAP 27:19</div>
</td>
</tr>
<tr>
<td class="td-bot">
<table class="ft">
<tr>
<td class="ftl">
    <br/><br/><br/>
<div class="db">DATE<span>{{ \Carbon\Carbon::parse($data->registrationdate)->format('d F Y') }}</span></div>
<img class="qi" src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(70)->generate($data->customerprofession->customer->regnumber)) }}" alt="QR">
</td>
<td class="ftr">
<img class="si" src="./imgs/signature.png" alt="Signature">
<div class="sl">Registrar</div>
<div class="sc">{{ config('app.title') }}</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
