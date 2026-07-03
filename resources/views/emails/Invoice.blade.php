<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; background:#f4f4f7; padding:30px;">

    <div style="max-width:500px; margin:0 auto; background:#ffffff; border-radius:12px; padding:30px;">

        <h2 style="color:#4f46e5;">Payment Confirmed</h2>

        <p style="color:#374151;">
            Hi, thank you for your payment. Please find your invoice attached to this email.
        </p>

        <table style="width:100%; margin-top:20px; border-collapse:collapse;">
            <tr>
                <td style="padding:8px 0; color:#6b7280;">Invoice ID</td>
                <td style="padding:8px 0; text-align:right; font-weight:bold;">#{{ $payment->id }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:#6b7280;">Payment ID</td>
                <td style="padding:8px 0; text-align:right;">{{ $payment->payment_id }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:#6b7280;">Amount Paid</td>
                <td style="padding:8px 0; text-align:right; font-weight:bold;">€{{ $payment->amount }}</td>
            </tr>
        </table>

        <p style="color:#9ca3af; font-size:12px; margin-top:30px;">
            This is an automated email, please do not reply.
        </p>

    </div>

</body>
</html>