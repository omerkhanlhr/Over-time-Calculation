<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Workhour;
use Illuminate\Http\Request;

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
        $hoursWorked = $interval->h + ($interval->i / 60); // Convert minutes to fraction of hour

        // Determine if overtime is applicable
        $dailyOvertime = $hoursWorked > 8 ? $hoursWorked - 8 : 0;
        $overtimeFlag = $dailyOvertime > 0 ? 1 : 0;

        // Calculate the total amount
        $totalAmount = $hoursWorked * $request->rate;

        // Store the work hours in the database
        $workhour = new Workhour();
        $workhour->employee_id = $request->employee_id;
        $workhour->client_id = $request->client_id;
        $workhour->work_date = $request->date;
        $workhour->start_time = $request->check_in_time;
        $workhour->end_time = $request->check_out_time;
        $workhour->daily_workhours = $hoursWorked;
        $workhour->weekly_workhours = $hoursWorked;
        $workhour->overtime = $overtimeFlag;
        $workhour->total_amount = $totalAmount;
        $workhour->save();

        $notification = array(
            'message' => 'Work Hours Added Succesfully',
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
}
