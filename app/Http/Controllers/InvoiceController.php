<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
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
            'tax' => 'nullable|numeric'
        ]);

        $workhours = Workhour::where('client_id', $request->client_id)
            ->whereBetween('work_date', [$request->from_date, $request->to_date])
            ->get();

        if ($workhours->isEmpty()) {
            return redirect()->back()->with('error', 'No work hours found for the selected dates.');
        }

        $totalAmount = $workhours->sum('total_amount');
        $totalEmployees = $workhours->groupBy('employee_id')->count();
        $tax = $request->tax;
        $grandTotal = $totalAmount + ($totalAmount * ($tax / 100));

        $invoice = Invoice::create([
            'client_id' => $request->client_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'total_employees' => $totalEmployees,
            'total_amount' => $totalAmount,
            'tax' => $tax,
            'grand_total' => $grandTotal
        ]);
        $notification = array(
            'message' => 'Invoice Added Succesfully',
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
            ->with('laborType') // Assuming you have a relationship named laborType in your Workhour model
            ->get();


        // Get unique labor types
        $labor_types = $workhours->groupBy('labor_type_id')->map(function ($workhourGroup) {
            return [
                'id' => $workhourGroup->first()->laborType->id,
                'name' => $workhourGroup->first()->laborType->name,
            ];
        })->values();


        // Return labor types as JSON
        return response()->json([
            'labor_types' => $labor_types,
        ]);
    }



    public function generatePdf($id)
    {
        $invoice = Invoice::with(['workhours' => function ($query) {
            $query->orderBy('work_date');
        }])->findOrFail($id);

        $workhours = $invoice->workhours;

        // Helper functions
        function timeToSeconds($time)
        {
            list($hours, $minutes, $seconds) = explode(':', $time);
            return ($hours * 3600) + ($minutes * 60) + $seconds;
        }

        function secondsToHours($seconds)
        {
            $hours = floor($seconds / 3600);
            return $hours;
        }

        function secondsToTime($seconds)
        {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $seconds = $seconds % 60;
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        // Group workhours by date and calculate totals
        $groupedWorkhours = $workhours->groupBy('work_date')->map(function ($items) {
            $totalHoursInSeconds = $items->where('daily_workhours', '>', 0)->sum(function ($item) {
                return timeToSeconds($item->daily_workhours);
            });


            $totalOvertimeInSeconds = $items->where('daily_overtime', '>', 0)->sum(function ($item) {
                return timeToSeconds($item->daily_overtime);
            });


            $rate = $items->first()->rate; // Assuming all items have the same rate

            $totalAmount = $items->sum('total_amount');

            $totalOvertimeAmount = $items->where('daily_overtime', '>', 0)->sum('total_amount');

            $totalHours = secondsToHours($totalHoursInSeconds);

            $totalOvertime = secondsToHours($totalOvertimeInSeconds);

            return [
                'items' => $items,
                'rate' => $rate,
                'total_hours' => $totalHours,
                'total_overtime' => $totalOvertime,
                'employee_count' => $items->unique('employee_id')->count(),
                'total_amount' => $totalAmount,
                'total_overtime_amount' => $totalOvertimeAmount,
            ];
        });

        $pdf = PDF::loadView('invoices.invoice_pdf', compact('invoice', 'groupedWorkhours'));

        // return $pdf->download('invoice_' . $id . '.pdf');
        return $pdf->stream('invoice.pdf');
    }
}
