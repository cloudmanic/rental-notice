<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
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

        .details-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .details-table td:first-child {
            font-weight: bold;
            color: #4a5568;
            width: 40%;
        }

        .notice-box {
            background-color: #f0f9ff;
            border: 1px solid #3182ce;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .notice-box h3 {
            color: #2b6cb0;
            margin-top: 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #718096;
            padding: 20px;
            background-color: #f7fafc;
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
            
            .details-table td {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Payment Confirmation</h1>
            </div>

        <div class="content">
            <p>Hello {{ $user->name }},</p>

            <p>Thank you for your payment. This email confirms that we have successfully received your payment for the <strong>{{ $noticeType }}</strong>.</p>

            <div class="highlight-box">
                <strong>Notice Details:</strong><br>
                Property: {{ $propertyAddress }}<br>
                Tenant(s): {{ $tenants->pluck('full_name')->join(', ') }}
            </div>

            <table class="details-table">
                <tr>
                    <td>Payment Date:</td>
                    <td>{{ now()->format('F j, Y') }}</td>
                </tr>
                <tr>
                    <td>Amount Paid:</td>
                    <td>${{ number_format($amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Payment Method:</td>
                    <td>Credit/Debit Card</td>
                </tr>
                <tr>
                    <td>Notice ID:</td>
                    <td>#{{ $notice->id }}</td>
                </tr>
            </table>

            <div class="notice-box">
                <h3>What Happens Next?</h3>
                <p>Your notice is now being processed for service. The service of your notice is currently underway and you will receive an email notification once the service has been completed.</p>
                <p>The notice will be served via first-class mail to the tenant(s) at the property address listed above.</p>
            </div>

            <p>Thank you for using {{ config('constants.oregonpastduerent_com.company_name') }}.</p>

            <p>Best regards,<br>
            The {{ config('constants.oregonpastduerent_com.company_name') }} Team</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('constants.oregonpastduerent_com.company_name') }}. All rights reserved.</p>
            <p>{{ config('constants.oregonpastduerent_com.company_address_1') }}, {{ config('constants.oregonpastduerent_com.company_city') }}, {{ config('constants.oregonpastduerent_com.company_state') }} {{ config('constants.oregonpastduerent_com.company_zip') }}</p>
            <p>If you have any questions, feel free to reply to this email.</p>
        </div>
    </div>
    </div>
</body>

</html>