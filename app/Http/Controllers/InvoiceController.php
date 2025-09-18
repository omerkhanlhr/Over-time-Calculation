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
        $totalEmployees = 0;
        $totalOvertimeEmployees = 0;  // Total count of overtime employees across all days
        $totalStatsEmployees = 0;  // Total count of overtime employees across all days
<<<<<<< HEAD
    
=======
        $totalStatsOvertimeEmployees = 0;  // Total count of overtime employees across all days
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
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

            // Sum all standard hours and overtime for this labor type on the given date
            $totalStandardHours = 0;
            $totalOvertimeHours = 0;
            $totalstatsHours = 0;
<<<<<<< HEAD
            
=======
            $totalstatsOvertimeHours = 0;
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

            // Track unique employees and overtime employees
            $employeeTracker = [];

            $overtimeEmployeeTracker = [];

            $statEmployeeTracker = [];

<<<<<<< HEAD

            foreach ($groupedWorkhours as $workhour) {
                $standardHours = timeToHours($workhour->standard_hours);
                
                $stat_standardHours = timeToHours($workhour->stats_standard_hours);
                
=======
            $statOvertimeEmployeeTracker = [];

            foreach ($groupedWorkhours as $workhour) {
                $standardHours = timeToHours($workhour->standard_hours);
                $stat_standardHours = timeToHours($workhour->stats_standard_hours);
                $stat_overtimeHours = timeToHours($workhour->stats_overtime_hours);  // Get stats overtime hours
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
                $employeeId = $workhour->employee_id;

                // Track unique employees
                if (!in_array($employeeId, $employeeTracker)) {
                    $employeeTracker[] = $employeeId;
                }

                // Track employees who have worked stats overtime
                if ($stat_standardHours > 0 && !in_array($employeeId, $statEmployeeTracker)) {
                    $statEmployeeTracker[] = $employeeId;
                }

                // Track employees who worked overtime (from daily overtime column)
                if (timeToHours($workhour->daily_overtime) > 0 && !in_array($employeeId, $overtimeEmployeeTracker)) {
                    $overtimeEmployeeTracker[] = $employeeId;
                }

<<<<<<< HEAD
                
=======
                // Track employees who have worked stats overtime
                if ($stat_overtimeHours > 0 && !in_array($employeeId, $statOvertimeEmployeeTracker)) {
                    $statOvertimeEmployeeTracker[] = $employeeId;
                }
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

                // Calculate standard hours and cap at 8 hours per day
                if ($standardHours > 8) {
                    $totalStandardHours += 8; // Cap regular hours at 8
                    $totalOvertimeHours += $standardHours - 8; // Overtime is any hours above 8
                } else {
                    $totalStandardHours += $standardHours; // No overtime, just sum up standard hours
                }

                // Calculate stats standard hours and cap at 8 hours per day
<<<<<<< HEAD
                if ($stat_standardHours > 0) 
                {
                    $totalstatsHours += $stat_standardHours; 
                } 
            }
            
            
            
=======
                if ($stat_standardHours > 8) {
                    $totalstatsHours += 8; // Cap regular hours at 8
                    $totalstatsOvertimeHours += $stat_standardHours - 8; // Overtime is any hours above 8
                } else {
                    $totalstatsHours += $stat_standardHours; // No overtime, just sum up standard hours
                }
            }
            // Overtime hours from the workhour data
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
            $additionalOvertime = $groupedWorkhours->sum(function ($workhour) {
                return timeToHours($workhour->daily_overtime);
            });

<<<<<<< HEAD

            $totalOvertimeHours += $additionalOvertime;

=======
            $additionalstatsOvertime = $groupedWorkhours->sum(function ($workhour) {
                return timeToHours($workhour->stats_overtime_hours);
            });

            $totalOvertimeHours += $additionalOvertime;

            $totalstatsOvertimeHours += $additionalstatsOvertime;
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

            // Get the rate for the labor type
            $rate = $request->input("labor_types.$labor_id");

            $overtimeRate = $request->input("overtime_rates.$labor_id");

<<<<<<< HEAD
=======
            $statsovertimeRate = $request->input("stats_overtime_rates.$labor_id");
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

            $statsRate = $request->input("stats_rates.$labor_id");


<<<<<<< HEAD
            // Calculate the subtotal (standard hours * rate)
            $subtotal = $totalStandardHours * $rate;
                if($rate == $overtimeRate)
                {
                 $overtimeAmount = $totalOvertimeHours * 1.5 * $overtimeRate;   
                }
            // Calculate overtime pay (overtime hours * 1.5 * rate)
            else
            {
                $overtimeAmount = $totalOvertimeHours * $overtimeRate;    
            }
            

            $statsAmount = $totalstatsHours * $statsRate;


            // Total amount for this breakdown (standard + overtime)
            $totalBreakdownAmount = $subtotal + $overtimeAmount + $statsAmount;
=======

            // Calculate the subtotal (standard hours * rate)
            $subtotal = $totalStandardHours * $rate;

            // Calculate overtime pay (overtime hours * 1.5 * rate)
            $overtimeAmount = $totalOvertimeHours * 1.5 * $overtimeRate;

            $statsAmount = $totalstatsHours * $statsRate;

            $statsOvertimeAmount = $totalstatsOvertimeHours * 1.5 * $statsovertimeRate;

            // Total amount for this breakdown (standard + overtime)
            $totalBreakdownAmount = $subtotal + $overtimeAmount + $statsAmount + $statsOvertimeAmount;
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

            // Count total employees and overtime employees for this date
            $totalEmployees = count($employeeTracker);  // Total unique employees for the date
            $overtimeEmployees = count($overtimeEmployeeTracker);  // Employees who worked overtime
            $statsEmployees = count($statEmployeeTracker);  // Employees who worked overtime
<<<<<<< HEAD
            
=======
            $statsovertimeEmployees = count($statOvertimeEmployeeTracker);  // Employees who worked overtime
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

            // Add the number of overtime employees to the total overtime employee count across all days
            $totalOvertimeEmployees += $overtimeEmployees;

            $totalStatsEmployees += $statsEmployees;
<<<<<<< HEAD
=======

            $totalStatsOvertimeEmployees += $statsovertimeEmployees;
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

            // Add the breakdown amount to the total invoice amount
            $totalAmount += $totalBreakdownAmount;

            // Store the breakdown information
            $invoiceBreakdowns[] = [
                'labor_type_id' => $labor_id,
                'work_date' => $work_date,
                'hours_worked' => $totalStandardHours, // Total regular hours worked
                'stats_hours'=> $totalstatsHours,
                'overtime_work' => $totalOvertimeHours, // Total overtime hours
<<<<<<< HEAD
=======
                'stats_overtime'=>$totalstatsOvertimeHours,
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
                'rate' => $rate,
                'overtime_amount' => $overtimeAmount,
                'subtotal' => $subtotal,
                'overtime_rate'=>$overtimeRate,
                'total_amount' => $totalBreakdownAmount,
                'total_employees' => $totalEmployees,
                'overtime_employees' => $overtimeEmployees,
                'stats_employees'=>$statsEmployees,
<<<<<<< HEAD
                'stats_rate'=>$statsRate,
                'stats_amount'=>$statsAmount,
=======
                'stats_overtime_employees'=>$statsovertimeEmployees,
                'stats_rate'=>$statsRate,
                'stats_overtime_rate'=>$statsovertimeRate,
                'stats_amount'=>$statsAmount,
                'stats_overtime_amount'=>$statsOvertimeAmount // Count of employees who worked overtime for this date
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
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
            'total_employees' => count($workhours->pluck('employee_id')->unique()),  // Total unique employees
            'grand_total' => $grandTotal
        ]);

        // Save the breakdowns with the invoice ID
        foreach ($invoiceBreakdowns as &$breakdown) {
            $breakdown['invoice_id'] = $invoice->id;
        }

        InvoiceBreakdown::insert($invoiceBreakdowns);

        // Return success notification
        $notification = [
            'message' => 'Invoice Added Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('invoices.show')->with($notification);
    }


<<<<<<< HEAD
=======


>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
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

<<<<<<< HEAD
        public function getLaborTypes(Request $request)
=======
    public function getLaborTypes(Request $request)
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
{
    function timeToHours($time)
    {
        list($hours, $minutes, $seconds) = explode(':', $time);
        return $hours + ($minutes / 60) + ($seconds / 3600); // Ensure it returns decimal
    }

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
        ->with('labour') // Ensure correct relationship is used
        ->get();

    Log::info('Fetched workhours:', $workhours->toArray());

    // Get unique labor types along with overtime information
    $labor_types = $workhours->groupBy('labour_id')->map(function ($workhourGroup) {
        $labour = $workhourGroup->first()->labour;

<<<<<<< HEAD
        
=======
        // Check if any work hours in the group have overtime (e.g., > 8 hours in a day)
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
        $hasOvertime = $workhourGroup->some(function ($workhour) {
            $standardHours = timeToHours($workhour->standard_hours);
            return $standardHours > 8 || timeToHours($workhour->daily_overtime) > 0;
        });
        $stats_hours = $workhourGroup->some(function ($workhour) {
<<<<<<< HEAD
    $standardHours = timeToHours($workhour->stats_standard_hours);
    return $standardHours > 0; // Only check if there are any stat hours
});
=======
            $standardHours = timeToHours($workhour->stats_standard_hours);
            return $standardHours > 8 || timeToHours($workhour->stats_overtime_hours) > 0;
        });
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609


        return [
            'id' => $labour ? $labour->id : null,
            'name' => $labour ? $labour->name : 'Unknown Labor Type',
            'overtime' => $hasOvertime,
            'stats_hours'=>$stats_hours
        ];
    })->values();

    Log::info('Labor Types with Overtime:', $labor_types->toArray());

    // Return labor types as JSON
    return response()->json([
        'labor_types' => $labor_types,
    ]);
}

<<<<<<< HEAD
=======

>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
    public function previewPdf($id)
    {
        $invoice = Invoice::with('client', 'invoiceBreakdowns.labour')->findOrFail($id);
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

            $statsHours = $items->sum('stats_hours'); // Assuming this field exists

<<<<<<< HEAD
        
=======
            $statsOvertime = $items->sum('stats_overtime'); // Assuming this field exists
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

            $rate = $items->first()->rate;

            $overtime_rate = $items->first()->overtime_rate;

            $stats_rate = $items->first()->stats_rate;

<<<<<<< HEAD
=======
            $stats_overtime_rate = $items->first()->stats_overtime_rate;
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

            $totalAmount = $items->sum('subtotal');

            $totalOvertimeAmount = $items->where('overtime_work', '>', 0)->sum('overtime_amount'); // Assuming this field exists

            $totalStatAmount = $items->where('stats_hours', '>', 0)->sum('stats_amount'); // Assuming this field exists

<<<<<<< HEAD
=======
            $totalStatOvertimeAmount = $items->where('stats_overtime', '>', 0)->sum('stats_overtime_amount'); // Assuming this field exists

>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
            // Use the sum of the total_employees column instead of counting unique employee IDs

            $employeeCount = $items->sum('total_employees');

            $overtime_employees = $items->sum('overtime_employees');

            $stats_employees = $items->sum('stats_employees');

<<<<<<< HEAD
=======
            $stats_overtime_employees = $items->sum('stats_overtime_employees');
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

            return [
                'items' => $items,
                'stats_rate'=>$stats_rate,
<<<<<<< HEAD
                'total_stat_amount'=>$totalStatAmount,
=======
                'stats_overtime_rate'=>$stats_overtime_rate,
                'total_stat_amount'=>$totalStatAmount,
                'total_stat_overtime_amount'=>$totalStatOvertimeAmount,
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
                'rate' => $rate,
                'total_hours' => $totalHours,
                'total_overtime' => $totalOvertime,
                'statsHours'=>$statsHours,
<<<<<<< HEAD
                'employee_count' => $employeeCount, // Use the total_employees column
                'total_amount' => $totalAmount,
                'stats_employees'=>$stats_employees,
=======
                'statsOvertime'=>$statsOvertime,
                'employee_count' => $employeeCount, // Use the total_employees column
                'total_amount' => $totalAmount,
                'stats_employees'=>$stats_employees,
                'stats_overtime_employees'=>$stats_overtime_employees,
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
                'total_overtime_amount' => $totalOvertimeAmount,
                'labor_type' => $laborTypeInitials, // Display initials instead of full labor type name
                'work_date' => $workDate,
                'overtime_rate'=>$overtime_rate,
                'overtime_employees'=>$overtime_employees,
            ];
        });

        $sortedBreakdowns = $groupedBreakdowns->sortBy(function ($group) {
            return $group['work_date'];
        });
        // Handle logo
        $path = public_path('images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $pdfView = 'invoices.invoice_pdf';
        $pdf = PDF::loadView($pdfView, compact('invoice', 'sortedBreakdowns' , 'groupedBreakdowns', 'breakdowns', 'base64'));

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
            $overtime_rate = $items->first()->overtime_rate;
            $stats_rate = $items->first()->stats_rate;
<<<<<<< HEAD
=======
            $stats_overtime_rate = $items->first()->stats_overtime_rate;
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
            $totalAmount = $items->sum('subtotal');
            $totalOvertimeAmount = $items->where('overtime_work', '>', 0)->sum('overtime_amount'); // Assuming this field exists
            $employeeCount = $items->sum('total_employees');
            $overtime_employees = $items->sum('overtime_employees');
            $statsHours = $items->sum('stats_hours'); // Assuming this field exists
<<<<<<< HEAD
            $stats_employees = $items->sum('stats_employees');
            $totalStatAmount = $items->where('stats_hours', '>', 0)->sum('stats_amount');  // Assuming this field exists
=======
            $statsOvertime = $items->sum('stats_overtime');
            $stats_employees = $items->sum('stats_employees');
            $stats_overtime_employees = $items->sum('stats_overtime_employees');
            $totalStatAmount = $items->where('stats_hours', '>', 0)->sum('stats_amount'); // Assuming this field exists
            $totalStatOvertimeAmount = $items->where('stats_overtime', '>', 0)->sum('stats_overtime_amount'); // Assuming this field exists
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

            return [
                'items' => $items,
                'stats_rate'=>$stats_rate,
<<<<<<< HEAD
                'total_stat_amount'=>$totalStatAmount,
                'rate' => $rate,
                'total_hours' => $totalHours,
                'statsHours'=>$statsHours,
=======
                'stats_overtime_rate'=>$stats_overtime_rate,
                'total_stat_amount'=>$totalStatAmount,
                'total_stat_overtime_amount'=>$totalStatOvertimeAmount,
                'rate' => $rate,
                'total_hours' => $totalHours,
                'statsHours'=>$statsHours,
                'statsOvertime'=>$statsOvertime,
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
                'total_overtime' => $totalOvertime,
                'employee_count' => $employeeCount,
                'total_amount' => $totalAmount,
                'stats_employees'=>$stats_employees,
<<<<<<< HEAD
=======
                'stats_overtime_employees'=>$stats_overtime_employees,
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
                'total_overtime_amount' => $totalOvertimeAmount,
                'labor_type' => $laborTypeInitials,
                'work_date' => $workDate,
                'overtime_rate'=>$overtime_rate,
                'overtime_employees'=>$overtime_employees,
            ];
        });

        $sortedBreakdowns = $groupedBreakdowns->sortBy(function ($group) {
            return $group['work_date'];
        });

        // Handle logo
        $path = public_path('images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $pdfView = 'invoices.invoice_pdf';
        $pdf = PDF::loadView($pdfView, compact('invoice', 'sortedBreakdowns' ,'groupedBreakdowns', 'breakdowns', 'base64'));

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
            $overtime_rate = $items->first()->overtime_rate;
<<<<<<< HEAD
            $stats_rate = $items->first()->stats_rate;
=======
            $overtime_rate = $items->first()->overtime_rate;
            $stats_rate = $items->first()->stats_rate;
            $stats_overtime_rate = $items->first()->stats_overtime_rate;
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
            $subtotal = $items->sum('subtotal');
            $totalAmount = $items->sum('total_amount');
            $totalOvertimeAmount = $items->where('overtime_work', '>', 0)->sum('overtime_amount'); // Assuming this field exists
            $statsHours = $items->sum('stats_hours'); // Assuming this field exists
<<<<<<< HEAD
            $stats_employees = $items->sum('stats_employees');
=======
            $statsOvertime = $items->sum('stats_overtime');
            $stats_employees = $items->sum('stats_employees');
            $stats_overtime_employees = $items->sum('stats_overtime_employees');
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
            $laborTypeInitials = $this->generateInitials($items->first()->labour->name);
            $employeeCount = $items->sum('total_employees');
            $overtime_employees = $items->sum('overtime_employees');
            $totalStatAmount = $items->where('stats_hours', '>', 0)->sum('stats_amount'); // Assuming this field exists
<<<<<<< HEAD
=======
            $totalStatOvertimeAmount = $items->where('stats_overtime', '>', 0)->sum('stats_overtime_amount'); // Assuming this field exists
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

            return [
                'items' => $items,
                'stats_rate'=>$stats_rate,
<<<<<<< HEAD
                'total_stat_amount'=>$totalStatAmount,
                'rate' => $rate,
                'statsHours'=>$statsHours,
                'stats_employees'=>$stats_employees,
=======
                'stats_overtime_rate'=>$stats_overtime_rate,
                'total_stat_amount'=>$totalStatAmount,
                'total_stat_overtime_amount'=>$totalStatOvertimeAmount,
                'rate' => $rate,
                'statsHours'=>$statsHours,
                'statsOvertime'=>$statsOvertime,
                'stats_employees'=>$stats_employees,
                'stats_overtime_employees'=>$stats_overtime_employees,
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
                'total_hours' => $totalHours,
                'total_overtime' => $totalOvertime,
                'employee_count' => $employeeCount,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'total_overtime_amount' => $totalOvertimeAmount,
                'overtime_rate'=>$overtime_rate,
                'labor_type' => $laborTypeInitials,
                'overtime_employees'=>$overtime_employees, // Fetch the labor type name
            ];
        });

        $sortedBreakdowns = $groupedBreakdowns->sortBy(function ($items, $date) {
            return \Carbon\Carbon::parse($date); // Sort by work_date as Carbon date
        });

        // Convert the company logo to base64
        $path = public_path('images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);


        // Load the breakdown PDF view and pass the required data
        $pdf = PDF::loadView('invoices.breakdown_invoices.breakdown_pdf', compact('invoice', 'sortedBreakdowns' , 'groupedBreakdowns', 'base64', 'breakdowns'));

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
            $overtime_rate = $items->first()->overtime_rate;
            $subtotal = $items->sum('subtotal');
            $totalAmount = $items->sum('total_amount');
            $totalOvertimeAmount = $items->where('overtime_work', '>', 0)->sum('overtime_amount'); // Assuming this field exists
            $statsHours = $items->sum('stats_hours'); // Assuming this field exists
<<<<<<< HEAD
    
            $stats_employees = $items->sum('stats_employees');
        
            $employeeCount = $items->sum('total_employees');
            
            $overtime_employees = $items->sum('overtime_employees');
          
            $stats_rate = $items->first()->stats_rate;
          
            $laborTypeInitials = $this->generateInitials($items->first()->labour->name);
            
            $totalStatAmount = $items->where('stats_hours', '>', 0)->sum('stats_amount'); // Assuming this field exists
            // Assuming this field exists
=======
            $statsOvertime = $items->sum('stats_overtime');
            $stats_employees = $items->sum('stats_employees');
            $stats_overtime_employees = $items->sum('stats_overtime_employees');
            $employeeCount = $items->sum('total_employees');
            $overtime_employees = $items->sum('overtime_employees');
            $stats_rate = $items->first()->stats_rate;
            $stats_overtime_rate = $items->first()->stats_overtime_rate;
            $laborTypeInitials = $this->generateInitials($items->first()->labour->name);
            $totalStatAmount = $items->where('stats_hours', '>', 0)->sum('stats_amount'); // Assuming this field exists
            $totalStatOvertimeAmount = $items->where('stats_overtime', '>', 0)->sum('stats_overtime_amount'); // Assuming this field exists
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609

            return [
                'items' => $items,
                'stats_rate'=>$stats_rate,
<<<<<<< HEAD
                'total_stat_amount'=>$totalStatAmount,
                'statsHours'=>$statsHours,
                'stats_employees'=>$stats_employees,
=======
                'stats_overtime_rate'=>$stats_overtime_rate,
                'total_stat_amount'=>$totalStatAmount,
                'total_stat_overtime_amount'=>$totalStatOvertimeAmount,
                'statsHours'=>$statsHours,
                'statsOvertime'=>$statsOvertime,
                'stats_employees'=>$stats_employees,
                'stats_overtime_employees'=>$stats_overtime_employees,
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
                'rate' => $rate,
                'total_hours' => $totalHours,
                'total_overtime' => $totalOvertime,
                'employee_count' => $employeeCount,
                'subtotal' => $subtotal,
                'overtime_rate'=>$overtime_rate,
                'total_amount' => $totalAmount,
                'total_overtime_amount' => $totalOvertimeAmount,
                'labor_type' => $laborTypeInitials,
                'overtime_employees'=>$overtime_employees, // Fetch the labor type name
            ];
        });

        $sortedBreakdowns = $groupedBreakdowns->sortBy(function ($items, $date) {
            return \Carbon\Carbon::parse($date); // Sort by work_date as Carbon date
        });

        // Convert the company logo to base64
        $path = public_path('images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $pdf = PDF::loadView('invoices.breakdown_invoices.breakdown_pdf', compact('invoice', 'sortedBreakdowns' ,'groupedBreakdowns', 'base64', 'breakdowns'));

        return $pdf->download('invoice ' . $invoiceId . ' ' . $laborType . '.pdf');
    }

    // Helper function to generate initials from labor type name
    private function generateInitials($laborTypeName)
{
    // Split the labor type name into words
    $words = explode(' ', $laborTypeName);

    // If there's only one word, return it as is (capitalized)
    if (count($words) === 1) {
        return ucfirst($laborTypeName); // Capitalize the first letter of the word
    }

    // If there are multiple words, generate initials
    $initials = '';
    foreach ($words as $word) {
        if (strlen($word) > 0) {
            $initials .= strtoupper($word[0]); // Get the first letter of each word
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
