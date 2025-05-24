<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Certificate of Mailing</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 30px;
        max-width: 800px;
        font-size: 14px;
    }

    h1 {
        text-align: left;
        font-size: 20px;
        margin-bottom: 15px;
    }

    h2 {
        text-align: center;
        font-size: 18px;
        margin: 0;
    }

    .header {
        text-align: center;
        margin-bottom: 15px;
    }

    .header p {
        margin: 5px 0;
    }

    .section {
        margin-bottom: 15px;
    }

    .section h3 {
        font-size: 16px;
        margin-bottom: 10px;
    }

    .bold {
        font-weight: bold;
    }

    .line {
        border-bottom: 1px solid #000;
        display: inline-block;
        width: 100%;
        height: 18px;
        margin-bottom: 8px;
    }

    .time-line {
        border-bottom: 1px solid #000;
        display: inline-block;
        width: 150px;
        margin-left: 5px;
    }

    .signature {
        margin-top: 30px;
    }

    .signature-line {
        border-bottom: 1px solid #000;
        display: inline-block;
        width: 250px;
        margin: 20px 0 10px 0;
    }
    </style>
</head>

<body>

    <div class="header">
        <h2>{{ $companyName }}</h2>
        <p>{{ $companyAddress }}</p>
        <p>Phone: {{ $companyPhone }} | Email: {{ $companyEmail }}</p>
    </div>

    <h1>Certificate of Mailing</h1>

    <div class="section">
        <p>
            I, Spicer Matthews, certify that on <span class="bold">{{ $mailingDate }}</span>, I personally mailed the
            following notices via first class mail
            from the {{ $postOfficeName }} located at <span class="bold">{{ $postOfficeAddress }}</span>.
        </p>

        <p>
            The notices sent were <span class="bold">{{ $noticeType }}</span>. Along with each notice,
            the state-required <em>"Notice re: Eviction for Nonpayment of Rent"</em> was also included.
        </p>
    </div>

    <div class="section">
        <h3>Tenants and Mailing Addresses:</h3>
        @foreach($tenantAddresses as $address)
        <div class="line">{{ $address }}</div>
        @endforeach
        @for($i = count($tenantAddresses); $i < 6; $i++) <div class="line">
    </div>
    @endfor
    </div>

    <div class="section">
        <p>These notices were physically dropped off at the post office at: <span class="time-line"></span></p>
    </div>

    <div class="section">
        <p>I affirm that the above statements are true and accurate to the best of my knowledge.</p>
    </div>

    <div class="signature">
        <p>Sincerely,</p>
        <div class="signature-line"></div>
        <p style="margin: 5px 0;">Spicer Matthews, Owner</p>
        <p style="margin: 5px 0;">{{ $companyName }}</p>
    </div>

</body>

</html>