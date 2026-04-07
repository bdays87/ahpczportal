<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Certificate - Innocent Tauzeni</title>
    <style>
          @page { size: A4 landscape; margin: 0; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: #444;
            font-family: "Times New Roman", Times, serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 40px;
        }

        /* Main Certificate Card */
        .cert-card {
            background-color: #fff;
            width: 1500px; /* Landscape width */
            height: 950px; /* Fixed height for consistent border */
            padding: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            position: relative;
            box-sizing: border-box;
            border: 1px solid #000;
        }

        /* The Decorative Border - Now covering the full interior */
        .main-frame {
            height: 100%;
            width: 100%;
            border: 25px solid transparent;
            /* Geometric professional pattern */
            border-image: repeating-conic-gradient(#222 100% 100%, #222 100% 100%) 25;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            box-sizing: border-box;
            padding: 30px 50px;
        }

        /* Emblem Box overlapping the top border */
        .emblem-box {
            position: absolute;
            top: -75px;
            left: 50%;
            transform: translateX(-50%);
            width: 110px;
            height: 120px;
            background-color: #fff;
            border: 1.5px solid #000;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
            text-align: center;
            font-weight: bold;
            font-size: 0.7rem;
        }

        .serial-section {
            width: 100%;
            text-align: left;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .header {
            text-align: center;
        }

        .header h1 {
            font-size: 3rem;
            margin: 0;
            letter-spacing: 5px;
        }

        .header h2 {
            font-size: 1.4rem;
            margin: 5px 0;
            font-weight: normal;
        }

        .title-bar {
            font-size: 1.7rem;
            font-weight: bold;
            text-transform: uppercase;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            margin: 20px 0;
            padding: 5px 40px;
        }

        .certify-text {
            font-size: 1.8rem;
            font-weight: bold;
            font-style: italic;
            margin: 10px 0;
        }

        .name-line {
            width: 80%;
            border-bottom: 3px solid #000;
            font-size: 3.5rem;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            color: #1a237e;
            padding-bottom: 5px;
        }

        .register-statement {
            font-size: 1.5rem;
            margin: 5px 0;
        }

        .category-line {
            width: 75%;
            border-bottom: 2px solid #000;
            height: 45px;
            font-size: 1.6rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .legal-clause {
            width: 85%;
            text-align: center;
            font-size: 1.1rem;
            font-weight: bold;
            line-height: 1.4;
            color: #333;
        }

        /* Footer: Date, QR, and Signatures */
        .footer-container {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto; /* Pushes everything to the bottom of the frame */
        }

        .date-qr-group {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .date-text {
            font-weight: bold;
            font-size: 1.1rem;
        }

        .qr-placeholder {
            width: 90px;
            height: 90px;
            border: 2px solid #000;
            background: repeating-conic-gradient(#000 0% 25%, #fff 0% 50%) 0% 0% / 10px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-placeholder::after {
            content: "QR CODE";
            font-size: 0.6rem;
            background: white;
            padding: 2px;
            font-weight: bold;
        }

        .registrar-area {
            text-align: center;
            width: 400px;
        }

        .signature-img {
            font-family: 'Brush Script MT', cursive;
            font-size: 3.5rem;
            color: #1a237e;
            margin-bottom: -20px;
        }

        .sig-line {
            border-top: 2px solid #000;
            padding-top: 5px;
            font-weight: bold;
            font-size: 1rem;
        }

        .sig-subtext {
            display: block;
            font-size: 0.7rem;
            font-weight: bold;
        }

        /* Custom Underline helper */
        .val-underline {
            text-decoration: underline;
            padding: 0 5px;
        }
    </style>
</head>
<body>

    <div class="cert-card">
        <div class="main-frame">

            <div class="emblem-box">
                COUNCIL<br>EMBLEM<br>MLCSCZ
            </div>

            <div class="serial-section">
                NO. <span class="val-underline">{{ $data->customerprofession->customer->regnumber }}</span>
            </div>

            <div class="header">
                <h1>ZIMBABWE</h1>
                <h2>Medical Laboratory and Clinical Scientists Council</h2>
                <div class="title-bar">REGISTRATION CERTIFICATE</div>
                <div class="certify-text">This is to certify that</div>
            </div>

            <div class="name-line">{{ strtoupper($data->customerprofession->customer->name . ' ' . $data->customerprofession->customer->surname) }}</div>

            <div class="register-statement">is registered on the register of</div>
            <div class="category-line">{{ strtoupper($data->customerprofession->profession->name) }}</div>

            <p class="legal-clause">
                kept by the Medical Laboratory & Clinical Scientists Council Zimbabwe in accordance with the provisions of the Health Professions Act, CAP 27:19
            </p>

            <div class="footer-container">

                <div class="date-qr-group">
                    <div class="date-text">
                        DATE <span class="val-underline">{{ \Carbon\Carbon::parse($data->registrationdate)->format('d F Y') }}</span>
                    </div>
                     <img class="qr-placeholder" src="{{ $qrcode }}" alt="QR Code" style="margin-top:3mm;">
                </div>

                <div class="registrar-area">
                    <img class="sig-img" src="./imgs/signature.png" alt="Signature">
                    <div class="sig-line">REGISTRAR</div>
                    <span class="sig-subtext">MEDICAL LABORATORY & CLINICAL SCIENTISTS COUNCIL</span>
                </div>

            </div>

        </div>
    </div>

</body>
</html>
