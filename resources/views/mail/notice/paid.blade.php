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
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .content {
            padding: 20px 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Payment Confirmation</h1>
    </div>

    <div class="content">
        <p>Hello {{ $name ?? 'Tenant' }},</p>

        <p>Thank you for your recent payment. This email confirms that we have received your payment for
            [Property/Unit].</p>

        <p><strong>Payment Details:</strong></p>
        <ul>
            <li>Date: {{ $date ?? date('F j, Y') }}</li>
            <li>Amount: ${{ $amount ?? '0.00' }}</li>
            <li>Payment Method: {{ $method ?? 'Online Payment' }}</li>
        </ul>

        <p>If you have any questions about your payment or account, please don't hesitate to contact us.</p>

        <p>Best regards,<br>
            The Property Management Team</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Property Management. All rights reserved.</p>
        <p>This is an automated email, please do not reply directly to this message.</p>
    </div>
</body>

</html>