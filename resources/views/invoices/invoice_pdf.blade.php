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
        }


        .company-details {
            width: 50%;
            float: left;
        }

        .invoice-details {
            width: 50%;
            float: right;
        }

        .company-details img {
            width: 200px;
            margin-top: -30px;
            margin-bottom: 10px;
        }

        .company-details h2,
        .invoice-details h3 {
            margin: 0;
        }

        .invoice-details p {
            margin: 5px 0;
        }

        .client-details {
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
        }

        .invoice-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-items th,
        .invoice-items td {
            border: none;
            padding: 5px;
        }

        .invoice-items th {
            background-color: rgba(0, 0, 0, 0.728);
            color: white;
            text-align: left;
            border: none;
        }

        .total {
            text-align: right;
            margin-top: 20px;
        }

        .total p {
            margin: 5px 0;
        }

        .terms {
            margin-top: 70px;
            font-size: 14px;
            color: #777;
        }

    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <div class="company-details">
                <img src="{{ $base64 }}" alt="Logo">
                <h6 style="margin-top:-5px">Theta Smart Corporate Solutions</h6>
                <p style="margin-top:-25px; font-size:13px">26920 29th Ave Aldergrove V4W3C1 Canada</p>
            </div>
            @php
                $lateFee = 0;

                if ($invoice->status == 0 && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($invoice->due_date))) {
                    $lateFee = $invoice->grand_total * 0.02; // 2% late fee
                }

                $finalTotal = $invoice->grand_total + $lateFee;
            @endphp
            <div class="invoice-details">
                <h3 style="text-align: right;">INVOICE</h3>
                <p style="font-size: 18px; text-align: right;">{{ $invoice->customer_prefix }} # {{ $invoice->id }}</p>
                <p style="text-align: right;">Date: {{ \Carbon\Carbon::now()->format('M d, Y') }}</p>
                <p style="text-align: right;">Payment Terms:
                    {{ \Carbon\Carbon::parse($invoice->from_date)->format('M d Y') }} -
                    {{ \Carbon\Carbon::parse($invoice->to_date)->format('M d Y') }}</p>
                <p style="text-align: right;">Due Date:
                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }} </p>
                <p style="background-color: rgb(233, 230, 230); text-align: right;"><strong>Balance Due: CA$
                        {{ number_format($finalTotal, 2) }}</strong></p>
            </div>
        </div>
        <div class="client-details">
            <p>Bill To: <strong>{{ $invoice->client->company }}</strong></p>
            <p>Ship To:<strong>Fleet Optics Inc</strong></p>
        </div>
        <table class="invoice-items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sortedBreakdowns as $data)
                    <tr>
                            <td><strong>{{ \Carbon\Carbon::parse($data['work_date'])->format('M d') }} - ({{ $data['employee_count'] }} Personnel Provided) {{ $data['labor_type'] }}</strong> </td>
                            <td>{{ $data['total_hours'] }}</td>
                            <td> CA${{ number_format($data['rate'], 2) }}</td>
                            <td> CA${{ number_format($data['total_amount'], 2) }} </td>
                    </tr>
                @if ($data['total_overtime'] > 0)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($data['work_date'])->format('M d') }}- ({{ $data['overtime_employees'] }} Personnel Provided) {{ $data['labor_type'] }} - OT</strong></td>
                        <td> {{ $data['total_overtime'] }} </td>
                        <td> CA${{ number_format($data['overtime_rate'], 2) }}</td>
                        <td> CA${{ number_format($data['total_overtime_amount'], 2) }}</td>
                    </tr>
                @endif
                @if ($data['statsHours'] > 0)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($data['work_date'])->format('M d') }}- ({{ $data['stats_employees'] }} Personnel Provided) {{ $data['labor_type'] }} - Stat</strong></td>
                        <td> {{ $data['statsHours'] }} </td>
                        <td> CA${{ number_format($data['stats_rate'], 2) }}</td>
                        <td> CA${{ number_format($data['total_stat_amount'], 2) }}</td>
                    </tr>
                @endif
                @if ($data['statsOvertime'] > 0)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($data['work_date'])->format('M d') }}- ({{ $data['stats_overtime_employees'] }} Personnel Provided) {{ $data['labor_type'] }} - Stat -OT</strong></td>
                        <td> {{ $data['statsOvertime'] }} </td>
                        <td> CA${{ number_format($data['stats_overtime_rate'], 2) }}</td>
                        <td> CA${{ number_format($data['total_stat_overtime_amount'], 2) }}</td>
                    </tr>
                @endif
                @endforeach

            </tbody>
        </table>

        <div class="total">


            <p>Subtotal: CA$ {{ number_format($invoice->total_amount, 2) }}</p>
            <p>Tax ( {{ number_format($invoice->tax, 2) }}% )</p>
            {{-- @if ($lateFee > 0)
                Late Fee (2%): CA$ {{ number_format($lateFee, 2) }}</p>
            @endif --}}
            <p>Total: CA$ {{ number_format($finalTotal, 2) }} </p>
            </div>
            <div class="remarks" style="margin-top: -90px">
                <span>Remarks</span>
                @if($invoice->remarks!="")
                    <p>{{ $invoice->remarks }}</p>
                @else
                <p>{{ " " }}</p>
                @endif
            </div>
            <div class="terms mt-20">
                <p>Terms:</p>
                <p>Payments received after the due date will be subject to a late fee of 2% percent charged monthly.</p>
            </div>
        </div>
</body>

</html>
