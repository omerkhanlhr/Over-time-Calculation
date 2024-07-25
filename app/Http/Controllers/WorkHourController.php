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
        // Validate the request
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'rate' => 'required|numeric|min:0',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i|after:check_in_time',
        ]);

        // Calculate working hours and overtime
        $checkIn = new \DateTime($request->date . ' ' . $request->check_in_time);
        $checkOut = new \DateTime($request->date . ' ' . $request->check_out_time);
        $interval = $checkIn->diff($checkOut);

        // Convert interval to hours and minutes
        $hoursWorked = $interval->h;
        $minutesWorked = $interval->i;

        // Determine if overtime is applicable
        $dailyOvertimeHours = 0;
        $dailyOvertimeMinutes = 0;
        if ($hoursWorked >= 8) {
            $dailyOvertimeHours = $hoursWorked - 8;
            $dailyOvertimeMinutes = $minutesWorked;
        }

        // Fetch the existing weekly work hours
        $existingWeeklyWorkhours = Workhour::where('employee_id', $request->employee_id)
            ->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum(DB::raw("TIME_TO_SEC(daily_workhours)"));

        // Add the current day's work hours to the existing weekly work hours
        $totalWeeklyWorkhoursInSeconds = $existingWeeklyWorkhours + ($hoursWorked * 3600 + $minutesWorked * 60);
        $weeklyWorkhours = gmdate('H:i', $totalWeeklyWorkhoursInSeconds);

        // Calculate the total amount
        $totalAmount = ($hoursWorked + $minutesWorked / 60) * $request->rate;

        // Store the work hours in the database
        $workhour = new Workhour();
        $workhour->employee_id = $request->employee_id;
        $workhour->client_id = $request->client_id;
        $workhour->work_date = $request->date;
        $workhour->start_time = $request->check_in_time;
        $workhour->end_time = $request->check_out_time;
        $workhour->daily_workhours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);
        $workhour->weekly_workhours = $weeklyWorkhours;
        $workhour->daily_overtime = sprintf('%02d:%02d', $dailyOvertimeHours, $dailyOvertimeMinutes);
        $workhour->overtime = ($dailyOvertimeHours > 0 || $dailyOvertimeMinutes > 0) ? 1 : 0;
        $workhour->rate = $request->rate;
        $workhour->total_amount = $totalAmount;
        $workhour->save();

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
    ]);

    $workhour = Workhour::findOrFail($id);

    // Calculate working hours and overtime
    $checkIn = new \DateTime($request->date . ' ' . $request->check_in_time);
    $checkOut = new \DateTime($request->date . ' ' . $request->check_out_time);
    $interval = $checkIn->diff($checkOut);

    // Convert interval to hours and minutes
    $hoursWorked = $interval->h;
    $minutesWorked = $interval->i;

    // Determine if overtime is applicable
    $dailyOvertimeHours = 0;
    $dailyOvertimeMinutes = 0;
    if ($hoursWorked >= 8) {
        $dailyOvertimeHours = $hoursWorked - 8;
        $dailyOvertimeMinutes = $minutesWorked;
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



    }
