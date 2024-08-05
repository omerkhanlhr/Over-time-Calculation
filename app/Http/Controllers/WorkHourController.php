<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Workhour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkHourController extends Controller
{
    public function create_workhour()
    {

        return view('workhours.add_workhours');
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
        'date.*' => 'required|date',
        'rate.*' => 'required|numeric|min:0',
        'check_in_time.*' => 'required|date_format:H:i',
        'check_out_time.*' => 'required|date_format:H:i',
        'break_time.*' => 'nullable|integer|min:0', // Validate break time in minutes
    ]);

    foreach ($request->date as $index => $date) {
        $employeeId = $request->employee_id;

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

        // Recalculate hours and minutes after subtracting break time
        $hoursWorked = intdiv($totalMinutesWorked, 60);
        $minutesWorked = $totalMinutesWorked % 60;

        // Determine if overtime is applicable
        $dailyOvertimeHours = 0;
        $dailyOvertimeMinutes = 0;
        if ($hoursWorked > 8) {
            $dailyOvertimeMinutes = ($hoursWorked - 8) * 60 + $minutesWorked;
            $dailyOvertimeHours = intdiv($dailyOvertimeMinutes, 60);
            $dailyOvertimeMinutes = $dailyOvertimeMinutes % 60;
        }

        // Fetch the existing weekly work hours
        $existingWeeklyWorkhours = Workhour::where('employee_id', $employeeId)
            ->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum(DB::raw("TIME_TO_SEC(daily_workhours)"));

        // Add the current day's work hours to the existing weekly work hours
        $totalWeeklyWorkhoursInSeconds = $existingWeeklyWorkhours + ($hoursWorked * 3600 + $minutesWorked * 60);
        $weeklyWorkhours = gmdate('H:i', $totalWeeklyWorkhoursInSeconds);

        // Calculate the total amount
        $totalAmount = ($hoursWorked + $minutesWorked / 60) * $request->rate[$index];

        // Store the work hours in the database
        $workhour = new Workhour();
        $workhour->employee_id = $employeeId;
        $workhour->client_id = $request->client_id;
        $workhour->work_date = $request->date[$index];
        $workhour->start_time = $request->check_in_time[$index];
        $workhour->end_time = $request->check_out_time[$index];
        $workhour->daily_workhours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);
        $workhour->overtime = ($dailyOvertimeHours > 0 || $dailyOvertimeMinutes > 0) ? 1 : 0;
        $workhour->is_overtime = ($dailyOvertimeHours > 0 || $dailyOvertimeMinutes > 0) ? 1 : 0;
        $workhour->weekly_workhours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked); // Store as VARCHAR
        $workhour->break_time = $breakTimeMinutes;
        $workhour->rate = $request->rate[$index];
        $workhour->total_amount = $totalAmount;
        $workhour->save();
    }

    $notification = array(
        'message' => 'Work Hours Added Successfully',
        'alert-type' => 'success',
    );

    return redirect()->route('display.work.hours')->with($notification);
}



    public function searchClients(Request $request)
    {
        $search = $request->get('query');
        $clients = Client::where('name', 'like', '%' . $search . '%')->get();
        return response()->json($clients);
    }

    public function searchEmployees(Request $request)
    {
        $search = $request->get('query');
        $employees = Employee::where('name', 'like', '%' . $search . '%')->get();
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
        return view('workhours.edit_workhours', compact('workhour', 'clients', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'employee_id' => 'required|exists:employees,id',
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
        if ($hoursWorked >= 8) {
            $dailyOvertimeMinutes = ($hoursWorked - 8) * 60 + $minutesWorked;
            $dailyOvertimeHours = intdiv($dailyOvertimeMinutes, 60);
            $dailyOvertimeMinutes = $dailyOvertimeMinutes % 60;
        }

        // Fetch the existing weekly work hours excluding the current record
        $existingWeeklyWorkhours = Workhour::where('employee_id', $request->employee_id)
            ->where('id', '!=', $id)
            ->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum(DB::raw("TIME_TO_SEC(daily_workhours)"));

        // Add the current day's work hours to the existing weekly work hours
        $totalWeeklyWorkhoursInSeconds = $existingWeeklyWorkhours + ($hoursWorked * 3600 + $minutesWorked * 60);
        $weeklyWorkhours = gmdate('H:i', $totalWeeklyWorkhoursInSeconds);

        // Calculate the total amount
        $totalAmount = ($hoursWorked + $minutesWorked / 60) * $request->rate;

        $workhour->employee_id = $request->employee_id;
        $workhour->client_id = $request->client_id;
        $workhour->work_date = $request->date;
        $workhour->start_time = $request->check_in_time;
        $workhour->end_time = $request->check_out_time;
        $workhour->daily_workhours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);
        $workhour->weekly_workhours = $weeklyWorkhours;
        $workhour->break_time = $breakTimeMinutes; // Store break time in minutes
        $workhour->daily_overtime = sprintf('%02d:%02d', $dailyOvertimeHours, $dailyOvertimeMinutes);
        $workhour->overtime = ($dailyOvertimeHours > 0 || $dailyOvertimeMinutes > 0) ? 1 : 0;
        $workhour->rate = $request->rate;
        $workhour->total_amount = $totalAmount;
        $workhour->save();

        $notification = array(
            'message' => 'Work Hours Updated Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('display.work.hours')->with($notification);
    }

    public function calculate_overtime($id)
    {
        $workhour = Workhour::findOrFail($id);
        $employeeId = $workhour->employee_id;

        // Calculate daily work time and overtime
        $start_time = new \DateTime($workhour->start_time);
        $end_time = new \DateTime($workhour->end_time);
        $break_time_minutes = $workhour->break_time;

        // If the end time is before the start time, it means the work period spans midnight
        if ($end_time <= $start_time) {
            $end_time->modify('+1 day');
        }

        // Calculate total worked interval
        $interval = $start_time->diff($end_time);
        $hoursWorked = $interval->h + ($interval->d * 24); // Convert days to hours if any
        $minutesWorked = $interval->i;

        // Convert total work time to minutes
        $totalWorkedMinutes = ($hoursWorked * 60) + $minutesWorked;

        // Subtract break time from total worked minutes
        $effectiveWorkedMinutes = $totalWorkedMinutes - $break_time_minutes;

        // Convert back to hours and minutes
        $effectiveWorkedHours = intdiv($effectiveWorkedMinutes, 60);
        $effectiveWorkedMinutes = $effectiveWorkedMinutes % 60;

        // Determine daily overtime
        $dailyOvertimeMinutes = 0;
        if ($effectiveWorkedHours > 8) {
            $dailyOvertimeMinutes = ($effectiveWorkedHours - 8) * 60 + $effectiveWorkedMinutes;
        }

        $dailyOvertimeHours = intdiv($dailyOvertimeMinutes, 60);
        $dailyOvertimeMinutes = $dailyOvertimeMinutes % 60;

        // Update daily work hour record
        $workhour->daily_workhours = sprintf('%02d:%02d', $effectiveWorkedHours, $effectiveWorkedMinutes);
        $workhour->daily_overtime = sprintf('%02d:%02d', $dailyOvertimeHours, $dailyOvertimeMinutes);
        $workhour->overtime = 0;
        $workhour->save();

        // Calculate weekly work hours and overtime
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $weeklyWorkhours = Workhour::where('employee_id', $employeeId)
            ->whereBetween('work_date', [$startOfWeek, $endOfWeek])
            ->get();

        $totalWeeklyWorkedMinutes = 0;
        $totalWeeklyBreakMinutes = 0;

        foreach ($weeklyWorkhours as $entry) {
            $start_time = new \DateTime($entry->start_time);
            $end_time = new \DateTime($entry->end_time);

            if ($end_time <= $start_time) {
                $end_time->modify('+1 day');
            }

            $interval = $start_time->diff($end_time);
            $hoursWorked = $interval->h + ($interval->d * 24);
            $minutesWorked = $interval->i;

            $totalMinutesWorked = ($hoursWorked * 60) + $minutesWorked;

            $totalWeeklyWorkedMinutes += $totalMinutesWorked;
            $totalWeeklyBreakMinutes += $entry->break_time;
        }

        // Subtract total break time for the week
        $effectiveWeeklyWorkedMinutes = $totalWeeklyWorkedMinutes - $totalWeeklyBreakMinutes;

        // Convert back to hours and minutes
        $effectiveWeeklyWorkedHours = intdiv($effectiveWeeklyWorkedMinutes, 60);
        $effectiveWeeklyWorkedMinutes = $effectiveWeeklyWorkedMinutes % 60;

        // Determine weekly overtime
        $weeklyOvertimeMinutes = 0;
        if ($effectiveWeeklyWorkedHours > 40) {
            $weeklyOvertimeMinutes = ($effectiveWeeklyWorkedHours - 40) * 60 + $effectiveWeeklyWorkedMinutes;
        }

        $weeklyOvertimeHours = intdiv($weeklyOvertimeMinutes, 60);
        $weeklyOvertimeMinutes = $weeklyOvertimeMinutes % 60;

        // Update the workhour record with weekly work hours and overtime details
        $workhour->weekly_workhours = sprintf('%02d:%02d', $effectiveWeeklyWorkedHours, $effectiveWeeklyWorkedMinutes);
        $workhour->weekly_overtime = sprintf('%02d:%02d', $weeklyOvertimeHours, $weeklyOvertimeMinutes);
        $workhour->save();

        $notification = array(
            'message' => 'Overtime Calculated Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }
}
