<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration Certificate</title>
</head>
<body style="font-family: Times New Roman; position: relative; min-height: 100vh; margin: 0; padding: 0; border: 10px solid #000; box-sizing: border-box;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('./imgs/certificatefinal.jpg'); background-size: 70%; background-position: center; background-repeat: no-repeat; opacity: 0.50; z-index: -1;"></div>

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
    Certificate No: <span style="font-weight: bold;">{{ $data->certificatenumber }}</span>
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
    <span style="font-weight: bold;font-size: 40px;">STUDENT REGISTRATION CONFIRMATION</span><br/><br/>

</div>
<div style="padding-right:20px;padding-left:20px;margin-top:50px;">
    <table style="width: 100%">
        <tbody>
            <tr>
                <th style="font-weight: bold; text-align: left;">Name</th>
                <td>{{ $data->customerprofession->customer->name }} {{ $data->customerprofession->customer->surname }}</td>
            </tr>
            <tr>
                <th style="font-weight: bold; text-align: left;">Registration Number</th>
                <td>{{ $data->customerprofession->profession->prefix }}{{ $data->customerprofession->customer->regnumber }}</td>
            </tr>
            <tr>
                <th style="font-weight: bold; text-align: left;">Profession</th>
                <td>{{ $data->customerprofession->profession->name }}</td>
            </tr>
            <tr>
                <th style="font-weight: bold; text-align: left;">Qualification being studied</th>
                <td>{{ $data->customerprofession->studentqualifications->qualification }}</td>
            </tr>
            <tr>
                <th style="font-weight: bold; text-align: left;">Name of Institution</th>
                <td>{{ $data->customerprofession->studentqualifications->institution }}</td>
            </tr>
            <tr>
                <th style="font-weight: bold; text-align: left;">Conditions</th>
                <td>{{ $data->customerprofession->profession->conditions->where('customertype_id',$data->customerprofession->customertype_id)->first()->condition??"N/A" }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div style="padding-right:20px;padding-left:20px;margin-top:50px;">
    <table style="width: 100%;">
        <tbody>
          <tr>
            <td style="text-align: left; font-weight: bold;">DATE: {{ $data->registrationdate}}<br>REG NO: {{ $data->customerprofession->customer->regnumber }}</td>
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