<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; padding: 40px; }
        h1 { color: #4f46e5; margin-bottom: 0; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .total { font-size: 18px; font-weight: bold; text-align: right; margin-top: 20px; }
    </style>
</head>
<body>

    <h1>Invoice</h1>
    <p class="muted">Laravel Mollie Payment</p>

    <table>
        <tr>
            <th>Invoice Number</th>
            <td>#{{ $payment->id }}</td>
        </tr>
        <tr>
            <th>Payment ID</th>
            <td>{{ $payment->payment_id }}</td>
        </tr>
        <tr>
            <th>Billed To</th>
            <td>{{ $payment->customer_email }}</td>
        </tr>
        <tr>
            <th>Date</th>
            <td>{{ $payment->updated_at->format('d M Y, H:i') }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($payment->status) }}</td>
        </tr>
    </table>

    <p class="total">Total Paid: €{{ number_format($payment->amount, 2) }}</p>

</body>
</html>