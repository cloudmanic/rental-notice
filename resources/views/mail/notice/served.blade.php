<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Served Confirmation</title>
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
            background-color: #10b981;
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
            background-color: #d1fae5;
            border-left: 4px solid #10b981;
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

        .success-box {
            background-color: #f0fdf4;
            border: 1px solid #22c55e;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .success-box h3 {
            color: #16a34a;
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
                <h1>Notice Successfully Served</h1>
            </div>

        <div class="content">
            <p>Hello {{ $user->name }},</p>

            <p>Great news! Your <strong>{{ $noticeType }}</strong> has been successfully served via first-class mail. The certificate of mailing has been uploaded to our system for your records.</p>

            <div class="highlight-box">
                <strong>Notice Details:</strong><br>
                Property: {{ $propertyAddress }}<br>
                Tenant(s): {{ $tenants->pluck('full_name')->join(', ') }}
            </div>

            <table class="details-table">
                <tr>
                    <td>Notice Type:</td>
                    <td>{{ $noticeType }}</td>
                </tr>
                <tr>
                    <td>Date Served:</td>
                    <td>{{ $servedDate }}</td>
                </tr>
                <tr>
                    <td>Service Method:</td>
                    <td>First-Class Mail</td>
                </tr>
                <tr>
                    <td>Notice ID:</td>
                    <td>#{{ $notice->id }}</td>
                </tr>
            </table>

            <div class="success-box">
                <h3>Important Information</h3>
                <p>Your notice has been served and the certificate of mailing is now available in your account. This certificate serves as proof that the notice was properly mailed to the tenant(s).</p>
                <p>The time period specified in your notice ({{ str_replace(' Notice', '', $noticeType) }}) begins from the date of mailing.</p>
                <p>You can view and download the certificate of mailing by logging into your account and navigating to the notice details page.</p>
            </div>

            <p>If you have any questions about the service of your notice or need assistance with next steps, please don't hesitate to contact us.</p>

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