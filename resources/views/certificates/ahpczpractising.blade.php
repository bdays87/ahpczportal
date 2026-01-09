<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Practising Certificate</title>
</head>
<body style="font-family: Times New Roman; position: relative; min-height: 100vh; margin: 0; padding: 0; border: 5px solid green; box-sizing: border-box;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 3px dashed green; background-image: url('./imgs/back.jpg'); background-size: 70%; background-position: center; background-repeat: no-repeat; opacity: 0.50; z-index: -1;"></div>

   <div>
    <table>
        <tbody>
            <tr>
                <td style="text-align: center; font-size: 20px; padding-top: 20px; padding-right: 80px; padding-left: 80px;">
                    {{ config('app.title') }}
                </td>
                <td style="padding: 20px;">
                    <img src="{{ $qrcode }}" alt="QR Code" style="width: 90px; height: 90px;">
                </td>
            </tr>
        </tbody>
    </table>
   </div>
   <div style="padding: 20px;">
    Certificate No: <span style="font-weight: bold;">{{ $data->certificate_number }}</span>
   </div>
   <div style="padding-right: 20px;padding-left: 20px;padding-top: 5px;">
    
   
   <table style="width: 100%;">
    <tbody>
      <tr>
        <td style="font-weight: bold;">
            {{ config('app.address') }}<br>
            Tel: {{ config('app.phone') }}<br/>
            Email: {{ config('app.email') }}
        </td>
        <td style="font-weight: bold; text-align: right;">
            P.O. Box {{ config('app.po_box') }}

        </td>
      </tr>
    </tbody>
</table>
<div style="text-align: center;padding-top: 20px; margin-top: 40px;">
    HEALTH PROFESSIONS ACT <br/>
    (Chapter 27:19) <br/>
    <br/>
    <br/>
    <span style="font-weight: bold;font-size: 40px;">PRACTISING CERTIFICATE</span><br/><br/>
    THIS IS TO CERTIFY THAT<br/><br/><br/>    
       
</div>
<div style="margin-top:50px;padding-right:20px;padding-left:20px;padding-top:5px;">
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td>This is to certify that</td>
                <td>{{ $data->customerprofession->customer->name }} {{ $data->customerprofession->customer->surname }}</td>
            </tr>
            <tr>
                <td>Reg No:</td>
                <td>{{ $data->customerprofession->customer->regnumber }}</td>
            </tr>
            <tr>
                <td>Is authorized to practice as a</td>
                <td>{{ $data->customerprofession->profession->name }}</td>
            </tr>
        </tbody>
    </table>

</div>
<div style="margin-top:50px;padding-right:20px;padding-left:20px;padding-top:5px;">
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td>Conditions</td>
                <td>N/A</td>
            </tr>
            <tr>
                <td>This certificate  expires on:</td>
                <td>{{ $data->certificate_expiry_date }}</td>
            </tr>
        </tbody>
    </table>
</div>
    <table style="width: 100%;">
        <tbody>
          <tr>
            <td style="text-align: left; font-weight: bold;">DATE: {{ $data->registration_date}}</td>
            <td style="text-align: right;">
                <div style="text-align: right;">
                    <img src="./imgs/signature.png" alt="Signature" style="width: 70px; height: 70px;"><br/>
                    <span style="font-weight: bold;">REGISTRAR</span><br/>
                       </div>

            </td>
          </tr>
        </tbody>
    </table>
    
</div>
</body>
</html>