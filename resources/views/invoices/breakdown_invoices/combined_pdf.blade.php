<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }} - Combined PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .invoice-header p {
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .total {
            text-align: right;
            margin-right: 20px;
        }
        .total-amount {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>Invoice #{{ $invoice->id }}</h1>
        <p>Date Range: {{ $invoice->from_date }} - {{ $invoice->to_date }}</p>
        <p>Client: {{ $invoice->client->name }}</p>
    </div>

    @foreach ($invoice->invoiceBreakdowns as $breakdown)
        <h3>Labour Type: {{ $breakdown->labour->name }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Hours Worked</th>
                    <th>Rate</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $breakdown->hours_worked }}</td>
                    <td>${{ number_format($breakdown->rate, 2) }}</td>
                    <td>${{ number_format($breakdown->subtotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <div class="total">
        <p>Total Amount: ${{ number_format($invoice->total_amount, 2) }}</p>
        <p>Tax: ${{ number_format($invoice->tax, 2) }}</p>
        <p class="total-amount">Grand Total: ${{ number_format($invoice->grand_total, 2) }}</p>
    </div>
</body>
</html>
