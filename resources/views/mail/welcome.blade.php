<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Oregon Past Due Rent Notice Service</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        color: #333;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .wrapper {
        width: 100%;
        background-color: #f4f4f4;
        padding: 40px 20px;
    }

    .container {
        width: 600px;
        max-width: 600px;
        margin: 0 auto;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .header {
        background-color: #4c51bf;
        color: white;
        text-align: center;
        padding: 30px 20px;
    }

    .header h1 {
        margin: 0;
        font-size: 24px;
    }

    .content {
        padding: 30px;
    }

    .highlight-box {
        background-color: #edf2f7;
        border-left: 4px solid #4c51bf;
        padding: 15px;
        margin: 20px 0;
        border-radius: 4px;
    }

    .benefit-box {
        background-color: #f0f9ff;
        border: 1px solid #3182ce;
        border-radius: 6px;
        padding: 20px;
        margin: 20px 0;
    }

    .benefit-box h3 {
        color: #2b6cb0;
        margin-top: 0;
    }

    .benefit-box ul {
        margin: 10px 0;
        padding-left: 20px;
    }

    .benefit-box li {
        margin: 8px 0;
    }

    .contact-info {
        background-color: #f7fafc;
        border-radius: 6px;
        padding: 20px;
        margin: 20px 0;
        text-align: center;
    }

    .contact-info p {
        margin: 5px 0;
    }

    .footer {
        text-align: center;
        font-size: 12px;
        color: #718096;
        padding: 20px;
        background-color: #f7fafc;
    }

    .signature {
        margin-top: 30px;
    }

    @media only screen and (max-width: 640px) {
        .wrapper {
            padding: 20px 10px;
        }

        .container {
            width: 100%;
            max-width: 100%;
        }

        .content {
            padding: 20px;
        }
    }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Welcome to Oregon Past Due Rent Notice Service!</h1>
            </div>

            <div class="content">
                <p>Dear {{ $firstName }},</p>

                <p>Thank you for signing up with <strong>Oregon Past Due Rent Notice</strong> service! I'm Spicer, and I
                    wanted to personally welcome you to our service.</p>

                <p>I understand that dealing with past-due rent can be stressful and time-consuming. That's why we've
                    made our process incredibly <strong>simple and easy</strong>. Here's how it works:</p>

                <div class="highlight-box">
                    <strong>Our Simple Process:</strong><br>
                    You simply tell us the details of your situation, and we take care of the rest. It's that easy!
                </div>

                <div class="benefit-box">
                    <h3>Why Choose Our Service?</h3>
                    <ul>
                        <li><strong>Court-Tested Process:</strong> Our process has been tested in court and proven
                            effective</li>
                        <li><strong>90% Success Rate:</strong> 9 times out of 10, our notice motivates tenants to
                            quickly resolve their past due rent before going to court</li>
                        <li><strong>Time is Critical:</strong> It's very important to issue this notice the very second
                            your tenant is late, as the eviction process doesn't even start until this notice is served
                        </li>
                        <li><strong>Third-Party Certification:</strong> Because we're a third party and provide you with
                            a certification of service, there'll be no question in court whether your notice was served
                            properly</li>
                        <li><strong>No Technicalities:</strong> Your case will never be thrown out on a technicality,
                            which is very common in eviction court</li>
                    </ul>
                </div>

                <p>I want you to know that we're here to help you every step of the way. If you have any questions at
                    all, please don't hesitate to reach out.</p>

                <div class="contact-info">
                    <strong>Contact Us Anytime:</strong>
                    <p>📱 Phone: {{ config('constants.oregonpastduerent_com.company_phone') }}</p>
                    <p>📧 Email: {{ config('constants.oregonpastduerent_com.support_email') }}</p>
                </div>

                <p>We're committed to making this process as smooth as possible for you. Remember, the sooner you act
                    when rent is late, the better your chances of a quick resolution.</p>

                <div class="signature">
                    <p>Best regards,</p>
                    <p><strong>Spicer Matthews</strong><br>
                        Founder, {{ config('constants.oregonpastduerent_com.company_name') }}</p>
                </div>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('constants.oregonpastduerent_com.company_name') }}. All rights
                    reserved.</p>
                <p>{{ config('constants.oregonpastduerent_com.company_address_1') }},
                    {{ config('constants.oregonpastduerent_com.company_city') }},
                    {{ config('constants.oregonpastduerent_com.company_state') }}
                    {{ config('constants.oregonpastduerent_com.company_zip') }}</p>
            </div>
        </div>
    </div>
</body>

</html>