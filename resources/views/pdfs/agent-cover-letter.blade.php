<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Agent Cover Letter</title>
    <style>
    @page {
        size: 8.5in 11in;
        margin: 0.6in;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 11pt;
        line-height: 1.5;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .letterhead {
        text-align: left;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #eee;
    }

    .letterhead h1 {
        font-size: 18pt;
        color: #4a5568;
        margin: 0 0 10px 0;
    }

    .letterhead p {
        margin: 3px 0;
        color: #666;
        font-size: 11pt;
    }

    .date {
        margin-bottom: 15px;
    }

    .recipient {
        margin-bottom: 15px;
    }

    .recipient p {
        margin: 3px 0;
    }

    .salutation {
        margin-bottom: 15px;
    }

    .body {
        margin-bottom: 30px;
        text-align: justify;
    }

    .body p {
        margin-bottom: 15px;
    }

    .closing {
        margin-top: 40px;
    }

    .closing p {
        margin: 3px 0;
    }

    .signature-space {
        margin: 60px 0 5px 0;
    }

    .enclosed-list {
        margin: 15px 0 15px 30px;
    }

    .enclosed-list li {
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <!-- Company Letterhead -->
    <div class="letterhead">
        <h1>{{ $companyName }}</h1>
        <p>{{ $companyAddress1 }}@if($companyAddress2), {{ $companyAddress2 }}@endif</p>
        <p>{{ $companyCity }}, {{ $companyState }} {{ $companyZip }}</p>
        <p>Phone: {{ $companyPhone }}</p>
        <p>Email: {{ $companyEmail }}</p>
    </div>

    <!-- Date -->
    <div class="date">
        <p>{{ $currentDate }}</p>
    </div>

    <!-- Salutation -->
    <div class="salutation">
        <p>Dear {{ $agentName }},</p>
    </div>

    <!-- Body -->
    <div class="body">
        <p>Thank you for trusting us to serve your property management needs.</p>

        <p>Enclosed you will find the following documents for your records:</p>

        <ul class="enclosed-list">
            <li>A copy of the {{ $noticeType }} issued to your tenant{{ $tenantCount > 1 ? 's' : '' }}.</li>
            <li>A signed Certificate of Mailing as confirmation that these notices were sent via First Class Mail.</li>
        </ul>

        <p>You can also find a copy of the attached documents in your portal at {{ $portalUrl }}.</p>

        <p>We understand the importance of timely and compliant communication with tenants, and we appreciate the
            opportunity to help you stay on track with your legal obligations.</p>

        <p>If you have any questions or need additional services, please don't hesitate to reach out.</p>
    </div>

    <!-- Closing -->
    <div class="closing">
        <p>Sincerely,</p>
        <div class="signature-space"></div>
        <p>Spicer Matthews, Owner</p>
        <p>{{ $companyName }}</p>
    </div>
</body>

</html>