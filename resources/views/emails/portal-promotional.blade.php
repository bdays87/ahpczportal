@php
    $primaryColor = config('app.color', '#000000');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }} Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid {!! $primaryColor !!};
        }
        .header h1 {
            color: {!! $primaryColor !!};
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin-bottom: 30px;
        }
        .content h2 {
            color: {!! $primaryColor !!};
            font-size: 20px;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        .content p {
            margin-bottom: 15px;
            text-align: justify;
        }
        .features {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .features li {
            padding: 10px 0;
            padding-left: 30px;
            position: relative;
        }
        .features li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: {!! $primaryColor !!};
            font-weight: bold;
            font-size: 18px;
        }
        .cta-button {
            display: inline-block;
            background-color: {!! $primaryColor !!};
            color: #ffffff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button:hover {
            opacity: 0.9;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer p {
            margin: 5px 0;
        }
        .contact-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .contact-info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to our new {{ config('app.name') }} Portal</h1>
            <p style="margin-top: 10px; color: #666;">{{ config('app.vision') }}</p>
        </div>

        <div class="content">
            <h2></h2>
            @if(!empty($name) || !empty($surname))
                <p>Dear {{ trim($name) }},</p>
            @else
                <p>Dear Health Practitioner,</p>
            @endif
            <p>We are excited to introduce you to the {{ config('app.name') }} Portal - your one-stop digital platform for managing your professional registration, applications, and compliance requirements.</p>

            <h2>Key Features & Functions</h2>
            <ul class="features">
                <li><strong>Online Registration:</strong> Complete your registration process entirely online, saving you time and paperwork.</li>
                <li><strong>Application Management:</strong> Submit and track your registration, renewal, and other applications in real-time.</li>
                <li><strong>online payments:</strong> Make secure online payments for registration fees, renewals, and other services.</li>
                <li><strong>Online CDP activities:</strong> Enroll and complete online CDP activities to earn points.</li>
                <li><strong>News letters and publications:</strong>  Download newsletters and other publications.</li>
                <li><strong>Useful resources:</strong> Download useful resources for your profession.</li>
            </ul>

            <h2>How to Register</h2>
            <p>Getting started is easy! Follow these simple steps:</p>
            <ol style="padding-left: 20px;">
                <li> visit our portal <a href="{{ route('register') }}">Click here to get started</a></li>
                <li>Click the "Get Started" button to create your account.</li>
                <li>Fill in your personal  on account type if you are a practitioner  or intern  select practitioner else select  student.</li>
                <li>Once you have created your account, the system  will redirect you to the login page.</li>
                <li>Login to your account and complete your profile.</li>
                <li>The system will ask you if you have  registered before  select yes  and enter your national id number and click submit.</li>
               <li>The system shall ask you to  capture  your historical data and attached previous certificates after </li>
               <li>Once you have completed  capturing your historical data you will need to wait for approval from the council.</li>
            </ol> 

        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        
        </div>
    </div>
</body>
</html>
