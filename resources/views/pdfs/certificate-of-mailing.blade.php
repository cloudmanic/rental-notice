<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Certificate of Mailing</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      max-width: 800px;
    }
    h1, h2 {
      text-align: left;
    }
    .header, .footer {
      text-align: center;
      margin-bottom: 20px;
    }
    .section {
      margin-bottom: 20px;
    }
    .bold {
      font-weight: bold;
    }
    .line {
      border-bottom: 1px solid #000;
      display: inline-block;
      width: 100%;
      height: 20px;
      margin-bottom: 10px;
    }
    .signature {
      margin-top: 40px;
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
      I, Spicer Matthews, certify that on <span class="line">{{ $mailingDate }}</span>, I personally mailed the following notices via first-class mail
      from the {{ $postOfficeName }} located at <span class="bold">{{ $postOfficeAddress }}</span>.
    </p>

    <p>
      The notices sent were <span class="line">{{ $noticeType }}</span> termination for non-payment of rent notices. Along with each notice,
      the state-required <em>"Notice re: Eviction for Nonpayment of Rent"</em> was also included.
    </p>
  </div>

  <div class="section">
    <h2>Tenants and Mailing Addresses:</h2>
    @foreach($tenantAddresses as $address)
      <div class="line">{{ $address }}</div>
    @endforeach
    @for($i = count($tenantAddresses); $i < 6; $i++)
      <div class="line"></div>
    @endfor
  </div>

  <div class="section">
    <p>These notices were physically dropped off at the post office at:</p>
    <p>Time: <span class="line"></span></p>
  </div>

  <div class="section">
    <p>I affirm that the above statements are true and accurate to the best of my knowledge.</p>
  </div>

  <div class="signature">
    <p>Sincerely,</p>
    <p class="line" style="width: 300px;"></p>
    <p>Spicer Matthews</p>
    <p>Owner, {{ $companyName }}</p>
  </div>

</body>
</html>