<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceBreakdown;
use App\Models\Labour;
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

    // Group work hours by labor type
    $laborTypeGroups = $workhours->groupBy('labour_id');
    $invoiceBreakdowns = [];

    foreach ($laborTypeGroups as $labor_id => $groupedWorkhours) {
        $hoursWorked = $groupedWorkhours->sum(function ($workhour) {
            return timeToHours($workhour->daily_workhours);
        });

        $overtime = $groupedWorkhours->sum(function ($workhour) {
            return timeToHours($workhour->daily_overtime);
        });

        $rate = $request->input("labor_types.$labor_id");

        if ($hoursWorked > 8) {
            $subtotal = 8 * $rate;
            $overtimeAmount = 1.5 * $overtime * $rate;
            $totalBreakdownAmount = $subtotal + $overtimeAmount;
        } else {
            $subtotal = $hoursWorked * $rate;
            $totalBreakdownAmount = $subtotal;
        }

        $totalEmployees += $groupedWorkhours->groupBy('employee_id')->count();

        // Add the breakdown amount to the total invoice amount
        $totalAmount += $totalBreakdownAmount;

        $invoiceBreakdowns[] = [
            'labor_type_id' => $labor_id,
            'hours_worked' => $hoursWorked,
            'rate' => $rate,
            'overtime_amount' => $overtimeAmount,
            'subtotal' => $subtotal,
            'total_amount' => $totalBreakdownAmount,
            'total_employees' => $totalEmployees,
        ];
    }

    // Calculate the grand total with tax
    $grandTotal = $totalAmount + ($tax / 100);

    // Create the invoice with the total amount being the sum of the breakdowns
    $invoice = Invoice::create([
        'client_id' => $request->client_id,
        'from_date' => $request->from_date,
        'to_date' => $request->to_date,
        'due_date' => $request->due_date,
        'status' => 0,
        'total_amount' => $totalAmount,  // Sum of all breakdowns
        'tax' => $tax,
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

    public function generatePdf($id, $breakdown_id = null)
    {
        $invoice = Invoice::with('invoiceBreakdowns.labour')->findOrFail($id);

        if ($breakdown_id) {
            $breakdowns = $invoice->invoiceBreakdowns()->where('id', $breakdown_id)->get();
        } else {
            $breakdowns = $invoice->invoiceBreakdowns;
        }
        $groupedBreakdowns = $breakdowns->groupBy(function ($breakdown) {
            // Use the first associated workhour date
            return $breakdown->labour->workhours->first()->work_date ?? 'Unknown Date';
        })->map(function ($items, $date) {
            $totalHours = $items->sum('hours_worked');
            $totalOvertime = $items->sum('overtime_hours'); // Assuming this field exists
            $rate = $items->first()->rate;
            $totalAmount = $items->sum('total_amount');
            $totalOvertimeAmount = $items->where('overtime_hours', '>', 0)->sum('overtime_amount'); // Assuming this field exists

            return [
                'items' => $items,
                'rate' => $rate,
                'total_hours' => $totalHours,
                'total_overtime' => $totalOvertime,
                'employee_count' => $items->unique('employee_id')->count(),
                'total_amount' => $totalAmount,
                'total_overtime_amount' => $totalOvertimeAmount,
                'labor_type' => $items->first()->labour->name
            ];
        });


        $path = public_path('images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $pdfView = $breakdown_id ? 'invoices.breakdown_invoices.breakdown_pdf' : 'invoices.invoice_pdf';
        $pdf = PDF::loadView($pdfView, compact('invoice', 'groupedBreakdowns', 'breakdowns', 'base64'));

        return $pdf->stream('invoice_' . $id . ($breakdown_id ? '_breakdown_' . $breakdown_id : '_combined') . '.pdf');
    }

    public function generateBreakdownPdf($invoiceId, $breakdownId)
    {
        // Fetch the invoice with its breakdowns and associated labour data
        $invoice = Invoice::with('invoiceBreakdowns.labour')->findOrFail($invoiceId);

        // Filter for the specific breakdown
        $breakdowns = $invoice->invoiceBreakdowns()->where('id', $breakdownId)->get();

        // Group the breakdowns by date and calculate totals
        $groupedBreakdowns = $breakdowns->groupBy(function ($breakdown) {
            // Group by the work date associated with the labour
            return $breakdown->labour->workhours->first()->work_date ?? 'Unknown Date';
        })->map(function ($items, $date) {
            $totalHours = $items->sum('hours_worked');
            $totalOvertime = $items->sum('overtime_hours'); // Assuming this field exists
            $rate = $items->first()->rate;
            $totalAmount = $items->sum('total_amount');
            $totalOvertimeAmount = $items->where('overtime_hours', '>', 0)->sum('overtime_amount'); // Assuming this field exists

            return [
                'items' => $items,
                'rate' => $rate,
                'total_hours' => $totalHours,
                'total_overtime' => $totalOvertime,
                'employee_count' => $items->unique('employee_id')->count(),
                'total_amount' => $totalAmount,
                'total_overtime_amount' => $totalOvertimeAmount,
                'labor_type' => $items->first()->labour->name // Fetch the labor type name
            ];
        });

        // Convert the company logo to base64
        $path = public_path('images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        // Load the breakdown PDF view and pass the required data
        $pdf = PDF::loadView('invoices.breakdown_invoices.breakdown_pdf', compact('invoice', 'groupedBreakdowns', 'base64', 'breakdowns'));

        // Stream the PDF as a download
        return $pdf->stream('invoice_' . $invoiceId . '_breakdown_' . $breakdownId . '.pdf');
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

        // Fetch work hours within the new date range
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

        // Group work hours by labor type
        $laborTypeGroups = $workhours->groupBy('labour_id');
        $newInvoiceBreakdowns = [];

        foreach ($laborTypeGroups as $labor_id => $groupedWorkhours) {
            $hoursWorked = $groupedWorkhours->sum(function ($workhour) {
                return timeToHours($workhour->daily_workhours);
            });

            $overtime = $groupedWorkhours->sum(function ($workhour) {
                return timeToHours($workhour->daily_overtime);
            });

            $rate = $request->input("labor_types.$labor_id");

            if ($hoursWorked > 8) {
                $subtotal = 8 * $rate;
                $overtimeAmount = 1.5 * $overtime * $rate;
                $totalAmount = $subtotal + $overtimeAmount;
            } else {
                $subtotal = $hoursWorked * $rate;
                $totalAmount += $subtotal;
            }

            $totalEmployees += $groupedWorkhours->groupBy('employee_id')->count();

            $newInvoiceBreakdowns[] = [
                'labor_type_id' => $labor_id,
                'hours_worked' => $hoursWorked,
                'rate' => $rate,
                'overtime_amount' => $overtimeAmount,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'total_employees' => $totalEmployees,
            ];
        }

        // Check each new breakdown against existing records to see if an update is necessary
        foreach ($newInvoiceBreakdowns as $newBreakdown) {
            $existingBreakdown = InvoiceBreakdown::where('invoice_id', $invoice->id)
                ->where('labor_type_id', $newBreakdown['labor_type_id'])
                ->first();

            if ($existingBreakdown) {
                // Only update if there's a difference
                if ($existingBreakdown->hours_worked != $newBreakdown['hours_worked'] ||
                    $existingBreakdown->rate != $newBreakdown['rate'] ||
                    $existingBreakdown->overtime_amount != $newBreakdown['overtime_amount'] ||
                    $existingBreakdown->subtotal != $newBreakdown['subtotal'] ||
                    $existingBreakdown->total_employees != $newBreakdown['total_employees'] ||
                    $existingBreakdown->total_amount != $newBreakdown['total_amount']
                ) {
                    $existingBreakdown->update($newBreakdown);
                }
            } else {
                // Create a new breakdown if it doesn't exist
                $newBreakdown['invoice_id'] = $invoice->id;
                InvoiceBreakdown::create($newBreakdown);
            }
        }

        // Recalculate the grand total
        $grandTotal = $totalAmount + ($totalAmount * ($tax / 100));

        // Update the invoice details
        $invoice->update([
            'client_id' => $request->client_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'due_date' => $request->due_date,
            'total_amount' => $totalAmount,
            'tax' => $tax,
            'total_employees' => $totalEmployees,
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

        return view('invoices.breakdown_invoices.all_pdfs', compact('invoice'));
    }
}
