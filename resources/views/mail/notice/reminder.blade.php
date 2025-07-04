<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reminder</title>
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
            background-color: #e53e3e;
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
            background-color: #fed7d7;
            border-left: 4px solid #e53e3e;
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
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .details-table td:first-child {
            font-weight: bold;
            color: #555;
        }

        .notice-box {
            background-color: #fff5f5;
            border: 1px solid #feb2b2;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .notice-box h3 {
            margin-top: 0;
            color: #e53e3e;
        }

        .payment-button {
            display: inline-block;
            background-color: #4c51bf;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: bold;
        }

        .footer {
            background-color: #f7fafc;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Payment Reminder</h1>
            </div>

            <div class="content">
                <p>Hello {{ $user->first_name }},</p>

                <p>This is a friendly reminder that your <strong>{{ $noticeType }}</strong> has been pending payment for {{ $daysOld }} day{{ $daysOld !== 1 ? 's' : '' }}.</p>

                <div class="highlight-box">
                    <strong>Notice Details:</strong><br>
                    Property: {{ $propertyAddress }}<br>
                    Tenant(s): {{ $tenants->pluck('full_name')->join(', ') }}
                </div>

                <table class="details-table">
                    <tr>
                        <td>Notice Created:</td>
                        <td>{{ $notice->created_at->format('F j, Y') }}</td>
                    </tr>
                    <tr>
                        <td>Amount Due:</td>
                        <td>${{ number_format($amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Notice ID:</td>
                        <td>#{{ $notice->id }}</td>
                    </tr>
                </table>

                <div class="notice-box">
                    <h3>Action Required</h3>
                    <p>To proceed with serving your notice, please complete your payment as soon as possible. Once payment is received, we will begin processing your notice for service.</p>
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