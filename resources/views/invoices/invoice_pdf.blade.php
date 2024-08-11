<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
    font-family: Arial, sans-serif;
}

.invoice-box {
    width: 100%;
    max-width: 800px;
    margin: auto;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
    font-size: 16px;
    line-height: 24px;
}

.header {
    display: flex;
    justify-content: space-between;
}

.company-details, .invoice-details {
    width: 48%;
}

.company-details img {
    max-width: 100px;
    margin-bottom: 10px;
}

.company-details h2, .invoice-details h3 {
    margin: 0;
}

.invoice-details p {
    margin: 5px 0;
}

.client-details {
    margin: 20px 0;
}

.invoice-items {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.invoice-items th, .invoice-items td {
    border: 1px solid #ddd;
    padding: 8px;
}

.invoice-items th {
    background-color: black;
    color: white;
    text-align: left;
}

.total {
    text-align: right;
    margin-top: 20px;
}

.total p {
    margin: 5px 0;
}

.terms {
    margin-top: 20px;
    font-size: 14px;
    color: #777;
}

    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="company-details">
                {{-- <img src="{{ asset('logo.png') }}" alt="Company Logo"> --}}
                <h2>Theta Smart Corporate Solutions</h2>
                <p>26920 29th Ave Aldergrove V4W3C1 Canada</p>
            </div>
            <div class="invoice-details">
                <h3 style="text-align: right;">INVOICE</h3>
                <p style="text-align: right;">{{ $invoice->id }}</p>
                <p style="text-align: right;">Date: Jul 5, 2024</p>
                <p style="text-align: right;">Payment Terms: {{ $invoice->from_date }} - {{ $invoice->to_date }}</p>
                <p style="text-align: right;">Due Date: Jul 26, 2024</p>
                <p style="background-color: rgb(233, 230, 230); text-align: right;"><strong>Balance Due: CA$ {{ number_format($invoice->grand_total, 2) }}</strong></p>
            </div>
        </div>
        <div class="client-details">
            <p>Bill To:</p> <p><strong style="vertical-align: top;">Fleet Optics Inc</strong></p>
        </div>
        <table class="invoice-items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th >Quantity</th>
                    <th >Rate</th>
                    <th >Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedWorkhours as $date => $data)
                <tr>
                    <td>{{ $date }} - ({{ $data['employee_count'] }} Personnel Provided) Sorters</td>
                    <td>{{ $data['total_hours'] }}</td>
                    <td>CA${{ number_format($data['rate'], 2) }}</td>
                    <td>${{ number_format($data['total_amount'], 2) }}</td>
                </tr>

                @if ($data['total_overtime'] > '00:00:00')
                    <tr>
                        <td>{{ $date }} - ({{ $data['employee_count'] }} Personnel Provided) Sorters OT</td>
                        <td>{{ $data['total_overtime'] }}</td>
                        <td>CA${{ number_format($data['rate'] * 1.5, 2) }}</td> <!-- Assuming OT rate is 1.5 times -->
                        <td>${{ number_format($data['total_overtime_amount'], 2) }}</td>
                    </tr>
                @endif
            @endforeach
                </tbody>
        </table>
        <div class="total">
            <p>Subtotal: CA$ {{ number_format($invoice->total_amount, 2) }}</p>
            <p>Tax ( {{ number_format($invoice->tax, 2) }} ): CA$269.46</p>
            <p>Total: CA$ {{ number_format($invoice->grand_total, 2) }} </p>
        </div>
        <div class="terms">
            <p>Terms:</p>
            <p>Payments received after the due date will be subject to a late fee of 2% percent charged monthly.</p>
        </div>
    </div>
</body>
</html>
