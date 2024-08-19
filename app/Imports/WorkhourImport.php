<?php

namespace App\Imports;

use App\Models\Workhour;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Maatwebsite\Excel\Concerns\ToModel;

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
            ->sum(DB::raw("TIME_TO_SEC(daily_workhours)")) / 3600;

        // Add the current day's work hours to the existing weekly work hours
        $totalWeeklyWorkhours = $existingWeeklyWorkhours + ($hoursWorked + $minutesWorked / 60);

        // Calculate weekly overtime if weekly work hours exceed 40
        $weeklyOvertimeSeconds = 0;
        if ($totalWeeklyWorkhours > 40) {
            $weeklyOvertimeSeconds = ($totalWeeklyWorkhours - 40) * 3600;
        }

        // Calculate total amount
        if ($hoursWorked > 8) {
            $totalAmount = 8 * $rate + 1.5 * $rate * $dailyOvertimeHours;
        } else {
            $totalAmount = $hoursWorked * $rate;
        }

        // Store the work hours in the database
        return new Workhour([
            'employee_id' => $employeeId,
            'client_id' => $clientId,
            'labour_id' => $labourId,
            'work_date' => $workDate,
            'start_time' => $checkInTime,
            'end_time' => $checkOutTime,
            'daily_workhours' => sprintf('%02d:%02d', $hoursWorked, $minutesWorked),
            'daily_overtime' => sprintf('%02d:%02d', $dailyOvertimeHours, $dailyOvertimeMinutes),
            'overtime' => ($dailyOvertimeHours > 0 || $dailyOvertimeMinutes > 0) ? 1 : 0,
            'weekly_workhours' => $totalWeeklyWorkhours, // Store as DECIMAL
            'weekly_overtime' => gmdate('H:i:s', $weeklyOvertimeSeconds), // Store weekly overtime as TIME with seconds
            'break_time' => $breakTimeMinutes, // Store break time in minutes
            'rate' => $rate,
            'total_amount' => $totalAmount,
        ]);
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
