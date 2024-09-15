<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Labour;
use App\Models\Stats;
use App\Models\Workhour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function create_statshour()
    {
        $labours = Labour::all();

        return view('stats.add_statshours', compact('labours'));
    }

    public function store_statshour(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'employee_id' => 'required|exists:employees,id',
            'labour_id' => 'required|exists:labours,id',
            'date.*' => 'required|date',
            'rate.*' => 'required|numeric|min:0',
            'check_in_time.*' => 'required|date_format:H:i',
            'check_out_time.*' => 'required|date_format:H:i',
            'break_time.*' => 'nullable|integer|min:0', // Validate break time in minutes
        ]);

        $overworkedEntries = [];

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
            $totalMinutesWorked -= $breakTimeMinutes;

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
            $existingWeeklyWorkhours = Stats::where('employee_id', $employeeId)
                ->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum(DB::raw("TIME_TO_SEC(daily_workhours)")) / 3600;



            if ($hoursWorked > 8)
            {
                $standardHours = '08:00:00';

                $totalAmount = 8 * $request->rate[$index] + 1.5 * $request->rate[$index] * $dailyOvertimeHours;
            }
            else
            {
                $standardHours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);

                $totalAmount = $hoursWorked * $request->rate[$index];
            }


            // Store the work hours in the database
            $stats = new Stats();
            $stats->employee_id = $employeeId;
            $stats->client_id = $request->client_id;
            $stats->labour_id = $request->labour_id;
            $stats->work_date = $request->date[$index];
            $stats->start_time = $request->check_in_time[$index];
            $stats->end_time = $request->check_out_time[$index];
            $stats->daily_workhours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);
            $stats->daily_overtime = sprintf('%02d:%02d', $dailyOvertimeHours, $dailyOvertimeMinutes);
            $stats->overtime = ($dailyOvertimeHours > 0 || $dailyOvertimeMinutes > 0) ? 1 : 0;
            $stats->break_time = $breakTimeMinutes; // Store break time in minutes
            $stats->rate = $request->rate[$index];
            $stats->standard_hours = $standardHours;
            $stats->total_amount = $totalAmount;
            $stats->save();

            if ($hoursWorked >= 12 || ($hoursWorked == 12 && $minutesWorked > 0)) {
                $overworkedEntries[] = [
                    'employee_id' => $employeeId,
                    'date' => $request->date[$index],
                ];
            }
        }

        $notificationMessage = 'Stats Hours Added Successfully';
        if (!empty($overworkedEntries)) {
            $notificationMessage .= '. Note: Some entries exceed 12 hours.';
        }

        $notification = array(
            'message' => $notificationMessage,
            'alert-type' => 'success',
        );

        return redirect()->route('display.stats.hours')->with($notification);
    }

    public function display()
    {
        $stats = Stats::with(['client', 'employee'])->get();

        return view('stats.display', compact('stats'));
    }

    public function single_statsdetails($id)
    {
        $stat = Stats::findOrFail($id);
        $clients = Client::all();
        $employees = Employee::all();
        return view('stats.single_stats_work_details', compact('stat', 'clients', 'employees'));
    }

    public function edit($id)
    {
        $stat = Stats::findOrFail($id);
        $clients = Client::all();
        $employees = Employee::all();
        $labours = Labour::all();
        return view('stats.edit_statshour', compact('stat', 'clients', 'employees', 'labours'));
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
        if ($hoursWorked > 8)
        {
            $dailyOvertimeMinutes = ($hoursWorked - 8) * 60 + $minutesWorked;
            $dailyOvertimeHours = intdiv($dailyOvertimeMinutes, 60);
            $dailyOvertimeMinutes = $dailyOvertimeMinutes % 60;
            $standardHours = '08:00:00';
            $totalAmount = 8 * $request->rate + 1.5 * $request->rate * $dailyOvertimeHours;
        }
        else
        {
            $standardHours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);

            $totalAmount = $hoursWorked * $request->rate;
        }

        $employeeId=$workhour->employee_id;

        // Fetch the existing weekly work hours excluding the current record
        $existingWeeklyWorkhours = Workhour::where('employee_id', $employeeId)
            ->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum(DB::raw("TIME_TO_SEC(daily_workhours)")) / 3600;

        // Add the current day's work hours to the existing weekly work hours
        $totalWeeklyWorkhours = $existingWeeklyWorkhours + ($hoursWorked + $minutesWorked / 60);

        // Calculate weekly overtime if weekly work hours exceed 40
        $weeklyOvertimeSeconds = 0;
        if ($totalWeeklyWorkhours > 40) {
            $weeklyOvertimeSeconds = ($totalWeeklyWorkhours - 40) * 3600;
        }



        $workhour->employee_id = $request->employee_id;
        $workhour->client_id = $request->client_id;
        $workhour->work_date = $request->date;
        $workhour->start_time = $request->check_in_time;
        $workhour->end_time = $request->check_out_time;
        $workhour->daily_workhours = sprintf('%02d:%02d', $hoursWorked, $minutesWorked);
        $workhour->weekly_workhours = $totalWeeklyWorkhours;
        $workhour->break_time = $breakTimeMinutes;
        $workhour->daily_overtime = sprintf('%02d:%02d', $dailyOvertimeHours, $dailyOvertimeMinutes);
        $workhour->overtime = ($dailyOvertimeHours > 0 || $dailyOvertimeMinutes > 0) ? 1 : 0;
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

    public function moveToWorkHours($id)
    {
        $stat = Stats::findOrFail($id);

        $workhour = new Workhour();

        $workhour->employee_id = $stat->employee_id;
        $workhour->client_id = $stat->client_id;
        $workhour->work_date = $stat->work_date;
        $workhour->start_time = $stat->start_time;
        $workhour->end_time = $stat->end_time;
        $workhour->daily_workhours = $stat->daily_workhours;
        $workhour->daily_overtime = $stat->daily_overtime;
        $workhour->overtime = $stat->overtime;
        $workhour->total_amount = $stat->total_amount;
        $workhour->standard_hours = $stat->standard_hours;
        $workhour->rate = $stat->rate;
        $workhour->break_time = $stat->break_time;
        $workhour->labour_id = $stat->labour_id;

        // Extract hours from daily_workhours (TIME) and add to weekly_workhours (INTEGER)
        $dailyHours = (int) date('H', strtotime($stat->daily_workhours));

        // Add daily hours to weekly_workhours
        $workhour->weekly_workhours += $dailyHours;

        // Check if weekly_workhours exceeds 40
        if ($workhour->weekly_workhours > 40) {
            $overtimeHours = $workhour->weekly_workhours - 40;

            // Update weekly_overtime (TIME) column
            $existingOvertime = strtotime($workhour->weekly_overtime);
            $additionalOvertime = strtotime("$overtimeHours:00:00");

            // Add the overtime to existing weekly_overtime
            $newOvertime = date("H:i:s", $existingOvertime + $additionalOvertime);
            $workhour->weekly_overtime = $newOvertime;

            // Set weekly_workhours to 40 (as the rest is overtime)
            $workhour->weekly_workhours = 40;
        }

        $workhour->save();

        $stat->delete();

        $notification = array(
            'message' => 'Data Moved Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }


    public function delete_statshour($id)
    {
        $stat = Stats::findOrFail($id);
        if ($stat) {
            $stat->delete();
            $notification = array(
                'message' => 'Stats Hours Deleted Successfully',
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
}
