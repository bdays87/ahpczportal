<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate</title>
</head>
<body style="font-family: Times New Roman; position: relative; min-height: 100vh; margin: 0; padding: 0; border: 10px solid #000; box-sizing: border-box;">

    {{-- Watermark --}}
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('./imgs/certificatefinal.jpg'); background-size: 70%; background-position: center; background-repeat: no-repeat; opacity: 0.50; z-index: -1;"></div>

    {{-- Header: title + QR --}}
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="text-align: center; font-size: 20px; padding-top: 20px; padding-right: 80px; padding-left: 80px;">
                    {{ config('app.title') }}
                </td>
                <td style="padding: 20px; width: 110px;">
                    <img src="{{ $qrcode }}" alt="QR Code" style="width: 90px; height: 90px;">
                </td>
            </tr>
        </tbody>
    </table>

    <div style="padding: 10px 20px;">
        Certificate No: <span style="font-weight: bold;">{{ $data->certificate_number }}</span>
    </div>

    {{-- Address block --}}
    <div style="padding: 0 20px;">
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="font-weight: bold;">
                        {{ config('app.address') }}<br>
                        Tel: {{ config('app.phone') }}<br>
                        Email: {{ config('app.email') }}
                    </td>
                    <td style="font-weight: bold; text-align: right;">
                        P.O. Box {{ config('app.po_box') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Title block --}}
    <div style="text-align: center; padding-top: 20px; margin-top: 30px;">
        <span style="font-weight: bold; font-size: 36px;">CERTIFICATE OF REGISTRATION</span><br><br>
        THIS IS TO CERTIFY THAT<br><br>
    </div>

    {{-- Details --}}
    <div style="padding: 0 20px; margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tbody>
                <tr>
                    <td style="padding: 6px 0; width: 40%;">This is to certify that</td>
                    <td style="padding: 6px 0; font-weight: bold;">
                        {{ $data->customer->name }} {{ $data->customer->surname }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0;">Reg No:</td>
                    <td style="padding: 6px 0; font-weight: bold;">{{ $data->customer->regnumber }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0;">Institution / Trade Name:</td>
                    <td style="padding: 6px 0; font-weight: bold;">{{ $data->tradename }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0;">Service:</td>
                    <td style="padding: 6px 0; font-weight: bold;">{{ $data->otherservice->name }}</td>
                </tr>
                @if($data->customerprofession)
                <tr>
                    <td style="padding: 6px 0;">Profession:</td>
                    <td style="padding: 6px 0; font-weight: bold;">{{ $data->customerprofession->profession->name }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 6px 0;">Period:</td>
                    <td style="padding: 6px 0; font-weight: bold;">{{ $data->period }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0;">Conditions:</td>
                    <td style="padding: 6px 0;">N/A</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0;">This certificate expires on:</td>
                    <td style="padding: 6px 0; font-weight: bold;">{{ $data->certificate_expiry_date }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Footer: date + signature --}}
    <div style="padding: 20px; margin-top: 60px;">
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="font-weight: bold;">DATE: {{ $data->registration_date }}</td>
                    <td style="text-align: right;">
                        <img src="./imgs/signature.png" alt="Signature" style="width: 70px; height: 70px;"><br>
                        <span style="font-weight: bold;">REGISTRAR</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>
