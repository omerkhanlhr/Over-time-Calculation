<?php

namespace App\Imports;

use App\Models\Workhour;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\DB;

class WorkhourImport implements ToModel
{
    public function model(array $row)
    {
        $employeeId = $row[0];
        $clientId = $row[1];
        $labourId = $row[7];

        // Convert Excel date serial number to PHP date format if necessary
        $workDate = $this->transformDate($row[2]);

        // Retrieve and parse time values
        $checkInTime = $this->transformTime($row[3]);
        $checkOutTime = $this->transformTime($row[4]);
        $breakTimeMinutes = $row[5] ?? 0; // Default to 0 if break time is null
        $rate = $row[6];

        // Calculate total worked time
        $checkIn = new \DateTime($workDate . ' ' . $checkInTime);
        $checkOut = new \DateTime($workDate . ' ' . $checkOutTime);

        if ($checkOut <= $checkIn) {
            $checkOut->modify('+1 day');
        }

        $interval = $checkIn->diff($checkOut);
        $hoursWorked = $interval->h + ($interval->d * 24); // Include days if check-out is on the next day
        $minutesWorked = $interval->i;

        // Subtract break time
        $totalMinutesWorked = ($hoursWorked * 60) + $minutesWorked;
        $totalMinutesWorked -= $breakTimeMinutes;

        // Recalculate hours and minutes after subtracting break time
        $hoursWorked = intdiv($totalMinutesWorked, 60);
        $minutesWorked = $totalMinutesWorked % 60;

        // Fetch existing work hours for the same employee and date
        $existingWorkhour = Workhour::where('employee_id', $employeeId)
            ->whereDate('work_date', $workDate)
            ->first();

        if ($existingWorkhour) {
            // Add the new hours to the existing ones
            $existingWorkhour->daily_workhours = $this->addTime($existingWorkhour->daily_workhours, $hoursWorked, $minutesWorked);

            // Recalculate overtime and total amount based on updated hours
            $this->updateWorkhour($existingWorkhour, $rate);

            // Save the updated work hour entry
            $existingWorkhour->save();

            return $existingWorkhour;
        } else {
            // Store the new work hours in the database
            return new Workhour([
                'employee_id' => $employeeId,
                'client_id' => $clientId,
                'labour_id' => $labourId,
                'work_date' => $workDate,
                'start_time' => $checkInTime,
                'end_time' => $checkOutTime,
                'daily_workhours' => sprintf('%02d:%02d', $hoursWorked, $minutesWorked),
                'daily_overtime' => $this->calculateOvertime($hoursWorked, $minutesWorked),
                'overtime' => ($hoursWorked > 8) ? 1 : 0,
                'weekly_workhours' => $this->calculateWeeklyWorkhours($employeeId, $hoursWorked, $minutesWorked),
                'weekly_overtime' => $this->calculateWeeklyOvertime($employeeId),
                'break_time' => $breakTimeMinutes, // Store break time in minutes
                'rate' => $rate,
                'total_amount' => $this->calculateTotalAmount($hoursWorked, $rate),
            ]);
        }
    }

    private function addTime($existingTime, $hoursToAdd, $minutesToAdd)
    {
        list($existingHours, $existingMinutes) = explode(':', $existingTime);

        $totalMinutes = ($existingHours * 60 + $existingMinutes) + ($hoursToAdd * 60 + $minutesToAdd);

        return sprintf('%02d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60);
    }

    private function updateWorkhour($workhour, $rate)
    {
        // Recalculate overtime and total amount
        $hoursWorked = $this->getHoursFromTime($workhour->daily_workhours);
        $minutesWorked = $this->getMinutesFromTime($workhour->daily_workhours);

        // Recalculate daily overtime
        $dailyOvertimeHours = 0;
        $dailyOvertimeMinutes = 0;
        if ($hoursWorked > 8) {
            $dailyOvertimeMinutes = ($hoursWorked - 8) * 60 + $minutesWorked;
            $dailyOvertimeHours = intdiv($dailyOvertimeMinutes, 60);
            $dailyOvertimeMinutes = $dailyOvertimeMinutes % 60;
        }

        // Update the workhour record
        $workhour->daily_overtime = sprintf('%02d:%02d', $dailyOvertimeHours, $dailyOvertimeMinutes);
        $workhour->overtime = ($dailyOvertimeHours > 0 || $dailyOvertimeMinutes > 0) ? 1 : 0;

        // Recalculate weekly work hours
        $workhour->weekly_workhours = $this->calculateWeeklyWorkhours($workhour->employee_id, $hoursWorked, $minutesWorked);

        // Recalculate weekly overtime
        $workhour->weekly_overtime = $this->calculateWeeklyOvertime($workhour->employee_id);

        // Recalculate total amount
        $workhour->total_amount = $this->calculateTotalAmount($hoursWorked, $rate);
    }

    private function calculateWeeklyWorkhours($employeeId, $hoursWorked, $minutesWorked)
    {
        $existingWeeklyWorkhours = Workhour::where('employee_id', $employeeId)
            ->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum(DB::raw("TIME_TO_SEC(daily_workhours)")) / 3600;

        return $existingWeeklyWorkhours + ($hoursWorked + $minutesWorked / 60);
    }

    private function calculateWeeklyOvertime($employeeId)
    {
        $totalWeeklyWorkhours = $this->calculateWeeklyWorkhours($employeeId, 0, 0); // Get total weekly work hours without adding current
        $weeklyOvertimeSeconds = 0;

        if ($totalWeeklyWorkhours > 40) {
            $weeklyOvertimeSeconds = ($totalWeeklyWorkhours - 40) * 3600;
        }

        return gmdate('H:i:s', $weeklyOvertimeSeconds);
    }

    private function calculateOvertime($hoursWorked, $minutesWorked)
    {
        if ($hoursWorked > 8) {
            $overtimeMinutes = ($hoursWorked - 8) * 60 + $minutesWorked;
            $overtimeHours = intdiv($overtimeMinutes, 60);
            $overtimeMinutes = $overtimeMinutes % 60;
            return sprintf('%02d:%02d', $overtimeHours, $overtimeMinutes);
        }

        return '00:00';
    }

    private function calculateTotalAmount($hoursWorked, $rate)
    {
        if ($hoursWorked > 8) {
            $dailyOvertimeHours = $hoursWorked - 8;
            return 8 * $rate + 1.5 * $rate * $dailyOvertimeHours;
        } else {
            return $hoursWorked * $rate;
        }
    }

    private function getHoursFromTime($time)
    {
        list($hours,) = explode(':', $time);
        return (int)$hours;
    }

    private function getMinutesFromTime($time)
    {
        list(, $minutes) = explode(':', $time);
        return (int)$minutes;
    }


    private function transformDate($value)
    {
        if (is_numeric($value)) {
            $date = ExcelDate::excelToDateTimeObject($value);
            return $date->format('Y-m-d');
        } else {
            return date('Y-m-d', strtotime($value));
        }
    }

    private function transformTime($value)
    {
        if (is_numeric($value)) {
            $time = ExcelDate::excelToDateTimeObject($value);
            return $time->format('H:i');
        } elseif (strpos($value, ':') !== false) {
            return date('H:i', strtotime($value));
        } else {
            throw new \Exception("Invalid time format: $value");
        }
    }
}
