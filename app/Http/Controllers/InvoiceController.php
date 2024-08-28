<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceBreakdown;
use App\Models\Labour;
use App\Models\Payment;
use App\Models\Workhour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDF;

class InvoiceController extends Controller
{
    public function create_invoice()
    {
        $clients = Client::all();
        return view('invoices.add_invoice', compact('clients'));
    }

    public function createInvoice(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'due_date' => 'required|date',
            'tax' => 'nullable|numeric'
        ]);

        $workhours = Workhour::where('client_id', $request->client_id)
            ->whereBetween('work_date', [$request->from_date, $request->to_date])
            ->with('labour')
            ->get();

        if ($workhours->isEmpty()) {
            return redirect()->back()->with('error', 'No work hours found for the selected dates.');
        }

        // Initialize totals
        $totalAmount = 0;
        $overtimeAmount = 0;
        $totalEmployees = 0;
        $tax = $request->tax;

        // Helper function to convert time to decimal hours
        function timeToHours($time)
        {
            list($hours, $minutes, $seconds) = explode(':', $time);
            return $hours + ($minutes / 60) + ($seconds / 3600);
        }

        // Group work hours by labor type and date
        $laborTypeDateGroups = $workhours->groupBy(function ($workhour) {
            return $workhour->labour_id . '_' . $workhour->work_date;
        });

        $invoiceBreakdowns = [];

        foreach ($laborTypeDateGroups as $key => $groupedWorkhours) {
            list($labor_id, $work_date) = explode('_', $key);

            $hoursWorked = $groupedWorkhours->sum(function ($workhour) {
                return timeToHours($workhour->daily_workhours);
            });

            $overtime = $groupedWorkhours->sum(function ($workhour) {
                return timeToHours($workhour->daily_overtime);
            });

            $rate = $request->input("labor_types.$labor_id");

            if ($hoursWorked > 8) {
                $hoursWorked = 8; // Store 8 hours in the hours_worked column
                $subtotal = 8 * $rate;
                $overtimeAmount = 1.5 * $overtime * $rate;
                $totalBreakdownAmount = $subtotal + $overtimeAmount;
            } else {
                $subtotal = $hoursWorked * $rate;
                $totalBreakdownAmount = $subtotal;
            }

            $totalEmployees = $groupedWorkhours->groupBy('employee_id')->count();

            // Add the breakdown amount to the total invoice amount
            $totalAmount += $totalBreakdownAmount;

            $invoiceBreakdowns[] = [
                'labor_type_id' => $labor_id,
                'work_date' => $work_date,
                'hours_worked' => $hoursWorked, // Storing the adjusted hours worked
                'overtime_work' => $overtime,
                'rate' => $rate,
                'overtime_amount' => $overtimeAmount,
                'subtotal' => $subtotal,
                'total_amount' => $totalBreakdownAmount,
                'total_employees' => $totalEmployees,
            ];
        }

        // Calculate the grand total with tax
        $grandTotal = $totalAmount + ($totalAmount * ($tax / 100));

        // Create the invoice with the total amount being the sum of the breakdowns
        $invoice = Invoice::create([
            'client_id' => $request->client_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'due_date' => $request->due_date,
            'status' => 0,
            'total_amount' => $totalAmount,
            'customer_prefix' => $request->prefix,
            'tax' => $tax,
            'remarks' => $request->remarks,
            'total_employees' => $totalEmployees,
            'grand_total' => $grandTotal
        ]);

        // Save the breakdowns
        foreach ($invoiceBreakdowns as &$breakdown) {
            $breakdown['invoice_id'] = $invoice->id;
        }

        InvoiceBreakdown::insert($invoiceBreakdowns);

        $notification = array(
            'message' => 'Invoice Added Successfully',
            'alert-type' => 'success',
        );
        return redirect()->route('invoices.show')->with($notification);
    }



    public function all_Invoices(Request $request)
    {
        $invoices = Invoice::with(['workhours.client'])->get();

        return view('invoices.display', compact('invoices'));
    }


    public function getWorkhoursDetails(Request $request)
    {
        $client_id = $request->client_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        Log::info('Received request:', [
            'client_id' => $client_id,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);

        $workhours = Workhour::where('client_id', $client_id)
            ->whereBetween('work_date', [$from_date, $to_date])
            ->get();

        Log::info('Workhours Query Result:', ['workhours' => $workhours]);

        $total_employees = $workhours->groupBy('employee_id')->count();
        $total_amount = $workhours->sum('total_amount');

        Log::info('Total Employees:', ['total_employees' => $total_employees]);
        Log::info('Total Amount:', ['total_amount' => $total_amount]);

        return response()->json([
            'total_employees' => $total_employees,
            'total_amount' => $total_amount,
        ]);
    }

    public function getLaborTypes(Request $request)
    {
        $client_id = $request->client_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        Log::info('Received request:', [
            'client_id' => $client_id,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);

        // Fetch work hours for the selected client and date range
        $workhours = Workhour::where('client_id', $client_id)
            ->whereBetween('work_date', [$from_date, $to_date])
            ->with('labour') // Using the correct relationship
            ->get();

        Log::info('Fetched workhours:', $workhours->toArray());

        // Get unique labor types
        $labor_types = $workhours->groupBy('labour_id')->map(function ($workhourGroup) {
            $labour = $workhourGroup->first()->labour;
            return [
                'id' => $labour ? $labour->id : null,
                'name' => $labour ? $labour->name : 'Unknown Labor Type',
            ];
        })->values();

        Log::info('Labor Types:', $labor_types->toArray());

        // Return labor types as JSON
        return response()->json([
            'labor_types' => $labor_types,
        ]);
    }
    public function previewPdf($id)
    {
        $invoice = Invoice::with('client' ,'invoiceBreakdowns.labour')->findOrFail($id);
        $breakdowns = $invoice->invoiceBreakdowns;

        // Group the breakdowns by date and labor type
        $groupedBreakdowns = $breakdowns->groupBy(function ($breakdown) {
            return $breakdown->work_date . '_' . $breakdown->labour->name;
        })->map(function ($items, $key) {
            $dateAndType = explode('_', $key);
            $workDate = $dateAndType[0];
            $laborType = $dateAndType[1];

            // Generate the initials from the labor type name
            $laborTypeInitials = $this->generateInitials($laborType);

            $totalHours = $items->sum('hours_worked');
            $totalOvertime = $items->sum('overtime_work'); // Assuming this field exists
            $rate = $items->first()->rate;
            $totalAmount = $items->sum('subtotal');
            $totalOvertimeAmount = $items->where('overtime_work', '>', 0)->sum('overtime_amount'); // Assuming this field exists

            return [
                'items' => $items,
                'rate' => $rate,
                'total_hours' => $totalHours,
                'total_overtime' => $totalOvertime,
                'employee_count' => $items->unique('employee_id')->count(),
                'total_amount' => $totalAmount,
                'total_overtime_amount' => $totalOvertimeAmount,
                'labor_type' => $laborTypeInitials, // Display initials instead of full labor type name
                'work_date' => $workDate,
            ];
        });

        // Handle logo
        $path = public_path('images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $pdfView = 'invoices.invoice_pdf';
        $pdf = PDF::loadView($pdfView, compact('invoice', 'groupedBreakdowns', 'breakdowns', 'base64'));

        return $pdf->stream('invoice_' . $id);
    }




    public function downloadPdf($id)
    {
        $invoice = Invoice::with('client' , 'invoiceBreakdowns.labour')->findOrFail($id);

        $breakdowns = $invoice->invoiceBreakdowns;

        // Group the breakdowns by date and labor type
        $groupedBreakdowns = $breakdowns->groupBy(function ($breakdown) {
            return $breakdown->work_date . '_' . $breakdown->labour->name;
        })->map(function ($items, $key) {
            $dateAndType = explode('_', $key);
            $workDate = $dateAndType[0];
            $laborType = $dateAndType[1];

            $laborTypeInitials = $this->generateInitials($laborType);

            $totalHours = $items->sum('hours_worked');
            $totalOvertime = $items->sum('overtime_work'); // Assuming this field exists
            $rate = $items->first()->rate;
            $totalAmount = $items->sum('subtotal');
            $totalOvertimeAmount = $items->where('overtime_work', '>', 0)->sum('overtime_amount'); // Assuming this field exists

            return [
                'items' => $items,
                'rate' => $rate,
                'total_hours' => $totalHours,
                'total_overtime' => $totalOvertime,
                'employee_count' => $items->unique('employee_id')->count(),
                'total_amount' => $totalAmount,
                'total_overtime_amount' => $totalOvertimeAmount,
                'labor_type' => $laborTypeInitials,
                'work_date' => $workDate,
            ];
        });

        // Handle logo
        $path = public_path('images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $pdfView = 'invoices.invoice_pdf';
        $pdf = PDF::loadView($pdfView, compact('invoice', 'groupedBreakdowns', 'breakdowns', 'base64'));

        return $pdf->download('invoice_' . $id . '.pdf');
    }

    public function delete_invoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        if ($invoice) {
            $invoice->delete();
            $notification = array(
                'message' => 'Invoice Deleted Successfully',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Something went wrong',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    public function previewBreakdownPdf($invoiceId, $laborType)
    {
        // Fetch the invoice with its breakdowns and associated labour data
        $invoice = Invoice::with('client', 'invoiceBreakdowns.labour')->findOrFail($invoiceId);

        // Filter for the specific labor type
        $breakdowns = $invoice->invoiceBreakdowns()->whereHas('labour', function ($query) use ($laborType) {
            $query->where('name', $laborType);
        })->get();


        // Group the breakdowns by date and calculate totals
        $groupedBreakdowns = $breakdowns->groupBy('work_date')->map(function ($items, $date) {
            $totalHours = $items->sum('hours_worked');
            $totalOvertime = $items->sum('overtime_work'); // Assuming this field exists
            $rate = $items->first()->rate;
            $subtotal = $items->sum('subtotal');
            $totalAmount = $items->sum('total_amount');
            $totalOvertimeAmount = $items->where('overtime_work', '>', 0)->sum('overtime_amount'); // Assuming this field exists

            $laborTypeInitials = $this->generateInitials($items->first()->labour->name);
            return [
                'items' => $items,
                'rate' => $rate,
                'total_hours' => $totalHours,
                'total_overtime' => $totalOvertime,
                'employee_count' => $items->unique('employee_id')->count(),
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'total_overtime_amount' => $totalOvertimeAmount,
                'labor_type' => $laborTypeInitials // Fetch the labor type name
            ];
        });

        // Convert the company logo to base64
        $path = public_path('images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        // Load the breakdown PDF view and pass the required data
        $pdf = PDF::loadView('invoices.breakdown_invoices.breakdown_pdf', compact('invoice', 'groupedBreakdowns', 'base64', 'breakdowns'));

        return $pdf->stream('invoice_' . $invoiceId . '_breakdown_' . $laborType . '.pdf');
    }

    public function downloadBreakdownPdf($invoiceId, $laborType)
    {
        // Fetch the invoice with its breakdowns and associated labour data
        $invoice = Invoice::with('client' , 'invoiceBreakdowns.labour')->findOrFail($invoiceId);

        // Filter for the specific labor type
        $breakdowns = $invoice->invoiceBreakdowns()->whereHas('labour', function ($query) use ($laborType) {
            $query->where('name', $laborType);
        })->get();

        // Group the breakdowns by date and calculate totals
        $groupedBreakdowns = $breakdowns->groupBy('work_date')->map(function ($items, $date) {
            $totalHours = $items->sum('hours_worked');
            $totalOvertime = $items->sum('overtime_work'); // Assuming this field exists
            $rate = $items->first()->rate;
            $subtotal = $items->sum('subtotal');
            $totalAmount = $items->sum('total_amount');
            $totalOvertimeAmount = $items->where('overtime_work', '>', 0)->sum('overtime_amount'); // Assuming this field exists

            $laborTypeInitials = $this->generateInitials($items->first()->labour->name);
            return [
                'items' => $items,
                'rate' => $rate,
                'total_hours' => $totalHours,
                'total_overtime' => $totalOvertime,
                'employee_count' => $items->unique('employee_id')->count(),
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'total_overtime_amount' => $totalOvertimeAmount,
                'labor_type' => $laborTypeInitials // Fetch the labor type name
            ];
        });

        // Convert the company logo to base64
        $path = public_path('images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $pdf = PDF::loadView('invoices.breakdown_invoices.breakdown_pdf', compact('invoice', 'groupedBreakdowns', 'base64', 'breakdowns'));

        return $pdf->download('invoice ' . $invoiceId . ' ' . $laborType . '.pdf');
    }

    // Helper function to generate initials from labor type name
    private function generateInitials($laborTypeName)
    {
        $words = explode(' ', $laborTypeName);
        $initials = '';

        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper($word[0]);
            }
        }

        return $initials;
    }

    public function edit($id)
    {
        $invoice = Invoice::with('invoiceBreakdowns.labour')->findOrFail($id);
        $clients = Client::all();

        return view('invoices.edit_invoice', compact('invoice', 'clients'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'client_id' => 'required',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'due_date' => 'required|date',
            'tax' => 'nullable|numeric'
        ]);

        // Retrieve the existing invoice
        $invoice = Invoice::findOrFail($id);


        $tax = $request->tax;



        $grandTotal = $invoice->total_amount + ($invoice->total_amount * ($tax / 100));


        // Update the invoice details
        $invoice->update([
            'client_id' => $request->client_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'due_date' => $request->due_date,
            'total_amount' => $invoice->total_amount,
            'tax' => $tax,
            'remarks' => $request->remarks,
            'status' => $request->status,
            'grand_total' => $grandTotal,
        ]);

        $notification = [
            'message' => 'Invoice Updated Successfully',
            'alert-type' => 'success',
        ];
        return redirect()->route('invoices.show')->with($notification);
    }


    public function showPdfs($id)
    {
        $invoice = Invoice::with('invoiceBreakdowns.labour')->findOrFail($id);

        // Group breakdowns by labor type
        $groupedBreakdowns = $invoice->invoiceBreakdowns->groupBy(function ($breakdown) {
            return $breakdown->labour->name;
        });

        return view('invoices.breakdown_invoices.all_pdfs', compact('invoice', 'groupedBreakdowns'));
    }

    public function Allpayments()
    {
        $payments = Payment::with(['invoice.client'])->get();
        return view('payments.all_payments', compact('payments'));
    }

    public function getpayment($id)
    {
        $invoice = Invoice::findOrFail($id);

        return view('payments.getpayment', compact('invoice'));
    }

    public function payment_invoice(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $payment = new Payment();

        $payment->invoice_id = $invoice->id;

        $payment->amount = $request->amount;

        $payment->payment_date = date('Y-m-d');

        $check = $payment->save();

        if ($check) {
            $invoice->status = 1;

            $invoice->save();

            $notification = array(
                'message' => 'Payment Added Successfully',
                'alert-type' => 'success',
            );
            return redirect()->route('admin.dashboard')->with($notification);
        } else {

            $notification = array(
                'message' => 'Something Went Wrong',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    public function show_invoice($id)
    {
        $invoice = Invoice::with(['client'])->findOrFail($id);

        $payments = Payment::where('invoice_id', $id)->get();

        return view('invoices.single_invoice', ['invoice' => $invoice, 'payments' => $payments]);
    }
}
