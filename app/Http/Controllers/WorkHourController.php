<?php

namespace App\Http\Controllers;

use App\Imports\WorkhourImport;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Labour;
use App\Models\Workhour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class WorkHourController extends Controller
{

    public function import_workhour()
    {
        return view('workhours.import_workhour');
    }


public function save_import_workhour(Request $request)
    {
        Excel::import(new WorkhourImport, $request->file('file'));
        $notification = [
            'message' => "Data Inserted Successfully",
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }

    public function store_forecasting_workhour(Request $request)
{
    // dd('hello');
    // $request->validate([
    //     'client_id' => 'required|exists:clients,id',
    //     'date' => 'required|date',
    //     'employees' => 'required|array',
    //     'employees.*.id' => 'required|exists:employees,id',
    //     'employees.*.check_in' => 'required|date_format:H:i',
    //     'employees.*.check_out' => 'required|date_format:H:i',
    //     'employees.*.rate' => 'required|numeric|min:0',
    //     'employees.*.break_time' => 'nullable|integer|min:0',
    // ]);
    $clientId = $request->client_id;
    $workDate = $request->date;
    $updatedEmployees = [];

    foreach ($request->employees as $employeeId => $employeeData) {
        $workhour = Workhour::where('employee_id', $employeeId)
            ->where('client_id', $clientId)
            ->where('work_date', $workDate)
            ->first();

        if ($workhour) {
            // Convert times to DateTime objects
            $checkIn = new \DateTime("$workDate {$employeeData['check_in']}");
            $checkOut = new \DateTime("$workDate {$employeeData['check_out']}");

            // Handle overnight shifts
            if ($checkOut <= $checkIn) {
                $checkOut->modify('+1 day');
            }

            $interval = $checkIn->diff($checkOut);
            $hoursWorked = $interval->h + ($interval->d * 24);
            $minutesWorked = $interval->i;

            // Subtract break time
            $breakTimeMinutes = $employeeData['break_time'] ?? 0;
            $totalMinutesWorked = ($hoursWorked * 60) + $minutesWorked - $breakTimeMinutes;
            $totalMinutesWorked = max(0, $totalMinutesWorked); // Prevent negative values

            // Recalculate hours and minutes
            $hoursWorked = intdiv($totalMinutesWorked, 60);
            $minutesWorked = $totalMinutesWorked % 60;

            // Determine overtime
            $dailyOvertimeHours = 0;
            $dailyOvertimeMinutes = 0;
            if ($hoursWorked > 8 || ($hoursWorked == 8 && $minutesWorked > 0)) {
                $overtimeMinutes = ($hoursWorked * 60 + $minutesWorked) - (8 * 60);
                $dailyOvertimeHours = intdiv($overtimeMinutes, 60);
                $dailyOvertimeMinutes = $overtimeMinutes % 60;
                $overtime = 1;
            } else {
                $overtime = 0;
            }

            // Calculate weekly work hours
            $existingWeeklyWorkhours = Workhour::where('employee_id', $employeeId)
                ->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum(DB::raw("TIME_TO_SEC(daily_workhours)")) / 3600;

            $totalWeeklyWorkhours = $existingWeeklyWorkhours + ($hoursWorked + $minutesWorked / 60);
            $weeklyOvertimeSeconds = max(0, ($totalWeeklyWorkhours > 40) ? ($totalWeeklyWorkhours - 40) * 3600 : 0);

            // Calculate total amount
            $totalAmount = ($hoursWorked > 8)
                ? (8 * $employeeData['rate']) + (1.5 * $employeeData['rate'] * $dailyOvertimeHours)
                : $hoursWorked * $employeeData['rate'];

            // Update Workhour record
            $workhour->update([
                'start_time' => $employeeData['check_in'],
                'end_time' => $employeeData['check_out'],
                'break_time' => $breakTimeMinutes,
                'rate' => $employeeData['rate'],
                'daily_workhours' => sprintf('%02d:%02d', $hoursWorked, $minutesWorked),
                'daily_overtime' => sprintf('%02d:%02d', $dailyOvertimeHours, $dailyOvertimeMinutes),
                'overtime' => $overtime,
                'weekly_workhours' => $totalWeeklyWorkhours,
                'weekly_overtime' => gmdate('H:i:s', $weeklyOvertimeSeconds),
                'total_amount' => $totalAmount,
            ]);

            $updatedEmployees[] = $employeeId;
        }
    }

    if (empty($updatedEmployees)) {
        return redirect()->back()->with([
            'message' => 'No workhour records found for the selected client and date.',
            'alert-type' => 'warning',
        ]);
    }

    return redirect()->route('display.work.hours')->with([
        'message' => 'Work Hours Updated Successfully!',
        'alert-type' => 'success',
    ]);
    dd('Inserted');

    return redirect()->route('display.work.hours')->with([
        'message' => 'Work Hours Updated Successfully!',
        'alert-type' => 'success',
    ]);
}


    public function store_employee_forecast(Request $request)
    {
          $request->validate([
            'client_id' => 'required|exists:clients,id',
            'employee_id.*' => 'required|exists:employees,id',
             'date' => 'required|date',
          ]);
          foreach ($request->employee_id as $index => $employeeId) {
            $workhour = new Workhour();

            $workhour->employee_id = $employeeId;

            $workhour->client_id = $request->client_id;
            $workhour->work_date = $request->date;
            $workhour->save();
        }

        $notification = array(
            'message' => 'Employees Added Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }

    public function fetchAssignedEmployees(Request $request)
{
    $client_id = $request->input('client_id');
    $work_date = $request->input('work_date');

    $employees = WorkHour::where('client_id', $client_id)
                         ->where('work_date', $work_date)
                         ->with('employee') // Assuming WorkHour has a relation with Employee model
                         ->get()
                         ->map(function ($workhour) {
                             return [
                                 'id' => $workhour->employee->id,
                                 'name' => $workhour->employee->name,
                             ];
                         });

    return response()->json($employees);
}


    public function employee_forecast()
    {

        return view('workhours.employees_forecasting');
    }

    public function employee_forecasting_workhour()
    {

        return view('workhours.employee_forecasting_list');
    }

    public function create_workhour()
    {
        $labours = Labour::all();

        return view('workhours.add_workhours',compact('labours'));
    }

    public function display()
    {
        $workhours = Workhour::with(['client', 'employee'])->get();

        return view('workhours.display', compact('workhours'));
    }

    public function store_workhour(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'employee_id' => 'required|exists:employees,id',
            'labour_id' => 'required|exists:labours,id',
            'date.*' => 'required|date',
            'rate.*' => 'required|numeric|min:0',
            // 'check_in_time.*' => 'required|date_format:H:i',
            // 'check_out_time.*' => 'required|date_format:H:i',
            'break_time.*' => 'nullable|integer|min:0', // Validate break time in minutes
        ]);

        $overworkedEntries = [];

        foreach ($request->employee_id as $index => $employeeId) {
            // $employeeId = $request->employee_id;

            $checkIn = new \DateTime($request->date[$index] . ' ' . $request->check_in_time[$index]);
            $checkOut = new \DateTime($request->date[$index] . ' ' . $request->check_out_time[$index]);

            if ($checkOut <= $checkIn) {
                $checkOut->modify('+1 day');
            }

            $interval = $checkIn->diff($checkOut);
            $hoursWorked = $interval->h + ($interval->d * 24); // Include days if check-out is on the next day
            $minutesWorked = $interval->i;

            // Subtract break time
            $breakTimeMinutes = $request->break_time[$index] ?? 0; // Default to 0 if break time is null
            $totalMinutesWorked = ($hoursWorked * 60) + $minutesWorked;
            $totalMinutesWorked -= $breakTimeMinutes;

            // Recalculate hours and minutes after subtracting break time
            $hoursWorked = intdiv($totalMinutesWorked, 60);
            $minutesWorked = $totalMinutesWorked % 60;

            // Initialize variables for standard hours and overtime
            $dailyOvertimeHours = 0;
            $dailyOvertimeMinutes = 0;

            // If the total worked time exceeds 8 hours (including minutes), calculate overtime
            if ($hoursWorked > 8 || ($hoursWorked == 8 && $minutesWorked > 0)) {
                // Set standard hours to 8 hours and calculate overtime
                $standardHours = '08:00:00';

                // Calculate overtime minutes (if worked more than 8 hours)
                $overtimeMinutes = ($hoursWorked * 60 + $minutesWorked) - (8 * 60);
                $dailyOvertimeHours = intdiv($overtimeMinutes, 60);
                $dailyOvertimeMinutes = $overtimeMinutes % 60;

                $overtime = 1;
            } else {
                // If worked 8 hours or less, no overtime
                $standardHours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);
                $overtime = 0;
            }

            // Fetch the existing weekly work hours
            $existingWeeklyWorkhours = Workhour::where('employee_id', $employeeId)
                ->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum(DB::raw("TIME_TO_SEC(daily_workhours)")) / 3600;


            $totalWeeklyWorkhours = $existingWeeklyWorkhours + ($hoursWorked + $minutesWorked / 60);

            // Calculate weekly overtime if weekly work hours exceed 40
            $weeklyOvertimeSeconds = 0;
            if ($totalWeeklyWorkhours > 40) {
                $weeklyOvertimeSeconds = ($totalWeeklyWorkhours - 40) * 3600;
            }


            $totalAmount = $hoursWorked > 8
                ? (8 * $request->rate[$index]) + (1.5 * $request->rate[$index] * $dailyOvertimeHours)
                : $hoursWorked * $request->rate[$index];

                $hoursField = $request->hours[$index] ?? null;
                $minutesField = $request->minutes[$index] ?? null;

                if (!is_null($hoursField) && !is_null($minutesField)) {
                    // Calculate total minutes worked
                    $totalMinutesWorked = ($hoursField * 60) + $minutesField - $breakTimeMinutes;

                    // Ensure that total minutes worked is not negative
                    $totalMinutesWorked = max(0, $totalMinutesWorked);

                    // Convert total minutes back to hours and minutes
                    $hoursWorked = intdiv($totalMinutesWorked, 60);
                    $minutesWorked = $totalMinutesWorked % 60;
                    // Set daily work hours in 'HH:MM:SS' format
                    $dailyOvertimeHours = 0;
                    $dailyOvertimeMinutes = 0;

                    if ($hoursWorked > 8 || ($hoursWorked == 8 && $minutesWorked > 0)) {
                        // Set standard hours to 8 hours and calculate overtime
                        $standardHours = '08:00:00';

                        // Calculate overtime minutes (if worked more than 8 hours)
                        $overtimeMinutes = ($hoursWorked * 60 + $minutesWorked) - (8 * 60);
                        $dailyOvertimeHours = intdiv($overtimeMinutes, 60);
                        $dailyOvertimeMinutes = $overtimeMinutes % 60;

                        $overtime = 1;
                    } else {
                        // If worked 8 hours or less, no overtime
                        $standardHours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);
                        $overtime = 0;
                    }


                }

                $workhour = new Workhour();

            $workhour->employee_id = $employeeId;
            $workhour->hours = $hoursWorked;
            $workhour->minutes = $minutesWorked;
            $workhour->client_id = $request->client_id;
            $workhour->labour_id = $request->labour_id[$index];
            $workhour->work_date = $request->date[$index];
            $workhour->start_time = $request->check_in_time[$index];
            $workhour->end_time = $request->check_out_time[$index];
            $workhour->daily_workhours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);
            $workhour->daily_overtime = sprintf('%02d:%02d', $dailyOvertimeHours, $dailyOvertimeMinutes);
            $workhour->overtime = $overtime;
            $workhour->weekly_workhours = $totalWeeklyWorkhours; // Store as DECIMAL
            $workhour->weekly_overtime = gmdate('H:i:s', $weeklyOvertimeSeconds); // Store weekly overtime as TIME with seconds
            $workhour->break_time = $breakTimeMinutes; // Store break time in minutes
            $workhour->rate = $request->rate[$index];
            $workhour->standard_hours = $standardHours;
            $workhour->total_amount = $totalAmount;
            $workhour->save();
}
            if ($hoursWorked >= 12 || ($hoursWorked == 12 && $minutesWorked > 0)) {
                $overworkedEntries[] = [
                    'employee_id' => $employeeId,
                    'date' => $request->date[$index],
                ];

        }

        $notificationMessage = 'Work Hours Added Successfully';
        if (!empty($overworkedEntries)) {
            $notificationMessage .= '. Note: Some entries exceed 12 hours.';
        }

        $notification = array(
            'message' => $notificationMessage,
            'alert-type' => 'success',
        );

        return redirect()->route('display.work.hours')->with($notification);
    }


    public function searchClients(Request $request)
    {
        $search = $request->get('query');
        $clients = Client::where('name', 'like', '%' . $search . '%')
        ->orWhere('client_id', 'like', '%' . $search . '%')
        ->get();
        return response()->json($clients);
    }

    public function searchEmployees(Request $request)
    {
        $search = $request->get('query');
        $employees = Employee::where('name', 'like', '%' . $search . '%')
        ->orWhere('emp_id', 'like', '%' . $search . '%')
        ->get();
        return response()->json($employees);
    }

    public function single_Workdetails($id)
    {
        $workhour = Workhour::findOrFail($id);
        $clients = Client::all();
        $employees = Employee::all();
        return view('workhours.single_work_details', compact('workhour', 'clients', 'employees'));
    }

    public function edit($id)
    {
        $workhour = Workhour::findOrFail($id);
        $clients = Client::all();
        $employees = Employee::all();
        $labours = Labour::all();
        return view('workhours.edit_workhours', compact('workhour', 'clients', 'employees','labours'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'client_id' => 'required|exists:clients,id',
        'employee_id' => 'required|exists:employees,id',
        'labour_id' => 'required|exists:labours,id',
        'date' => 'required|date',
        'rate' => 'required|numeric|min:0',
        'break_time' => 'required|integer|min:0', // Validate break time in minutes
    ]);

    $workhour = Workhour::findOrFail($id);

    // Calculate working hours and overtime
    $checkIn = new \DateTime($request->date . ' ' . $request->check_in_time);
    $checkOut = new \DateTime($request->date . ' ' . $request->check_out_time);

    if ($checkOut <= $checkIn) {
        $checkOut->modify('+1 day');
    }

    $interval = $checkIn->diff($checkOut);

    // Convert interval to hours and minutes
    $hoursWorked = $interval->h + ($interval->d * 24); // Include days if check-out is on the next day
    $minutesWorked = $interval->i;

    // Subtract break time
    $breakTimeMinutes = $request->break_time;
    $totalMinutesWorked = ($hoursWorked * 60) + $minutesWorked - $breakTimeMinutes;

    // Recalculate hours and minutes after subtracting break time
    $hoursWorked = intdiv($totalMinutesWorked, 60);
    $minutesWorked = $totalMinutesWorked % 60;

    // Determine if overtime is applicable
    $dailyOvertimeHours = 0;
    $dailyOvertimeMinutes = 0;
    if ($hoursWorked > 8 || ($hoursWorked == 8 && $minutesWorked > 0)) {
        // If the employee worked more than 8 hours or 8 hours + extra minutes, calculate overtime
        $overtimeMinutes = ($hoursWorked * 60 + $minutesWorked) - (8 * 60);
        $dailyOvertimeHours = intdiv($overtimeMinutes, 60);
        $dailyOvertimeMinutes = $overtimeMinutes % 60;
        $standardHours = '08:00:00';
        $totalAmount = 8 * $request->rate + 1.5 * $request->rate * ($dailyOvertimeHours + $dailyOvertimeMinutes / 60);
        $overtime = 1;
    } else {
        $standardHours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);
        $totalAmount = $hoursWorked * $request->rate;
        $overtime = 0;
    }

    // Fetch the existing weekly work hours excluding the current record
    $existingWeeklyWorkhours = Workhour::where('employee_id', $workhour->employee_id)
        ->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])
        ->sum(DB::raw("TIME_TO_SEC(daily_workhours)")) / 3600;

    // Add the current day's work hours to the existing weekly work hours
    $totalWeeklyWorkhours = $existingWeeklyWorkhours + ($hoursWorked + $minutesWorked / 60);

    // Calculate weekly overtime if weekly work hours exceed 40
    $weeklyOvertimeSeconds = 0;
    if ($totalWeeklyWorkhours > 40) {
        $weeklyOvertimeSeconds = ($totalWeeklyWorkhours - 40) * 3600;
    }

    if($workhour->stats==1)
    {
        $workhour->employee_id = $request->employee_id;
        $workhour->client_id = $request->client_id;
        $workhour->work_date = $request->date;
        $workhour->start_time = $request->check_in_time;
        $workhour->end_time = $request->check_out_time;
        $workhour->daily_workhours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);
        $workhour->stats_standard_hours = $standardHours;
        $workhour->total_amount = $totalAmount;
        $workhour->stats_overtime = $overtime; // Set overtime flag
        $workhour->stats_overtime_hours = sprintf('%02d:%02d:%02d', $dailyOvertimeHours, $dailyOvertimeMinutes, 0);
        $workhour->save();
        $notification = array(
            'message' => 'Stat Hours Updated Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('display.work.hours')->with($notification);

    }

    else
    {
        $workhour->employee_id = $request->employee_id;
        $workhour->client_id = $request->client_id;
        $workhour->work_date = $request->date;
        $workhour->start_time = $request->check_in_time;
        $workhour->end_time = $request->check_out_time;
        $workhour->daily_workhours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);
        $workhour->weekly_workhours = $totalWeeklyWorkhours;
        $workhour->break_time = $breakTimeMinutes;
        $workhour->daily_overtime = sprintf('%02d:%02d:%02d', $dailyOvertimeHours, $dailyOvertimeMinutes, 0);
        $workhour->overtime = $overtime; // Set overtime flag
        $workhour->rate = $request->rate;
        $workhour->labour_id = $request->labour_id;
        $workhour->standard_hours = $standardHours;
        $workhour->total_amount = $totalAmount;
        $workhour->save();

        $notification = array(
            'message' => 'Work Hours Updated Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('display.work.hours')->with($notification);
    }

    // Update the workhour record in the database

}


    public function delete_workhour($id)
    {
        $workhour = Workhour::findOrFail($id);
        if($workhour)
        {
            $workhour->delete();
            $notification = array(
                'message' => 'Work Hours Deleted Successfully',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        }
        else
        {
            $notification = array(
                'message' => 'Something went wrong',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }


}
