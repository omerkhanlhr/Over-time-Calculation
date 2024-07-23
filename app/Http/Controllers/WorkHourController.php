<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use Illuminate\Http\Request;

class WorkHourController extends Controller
{
    public function create_workhour()
    {
        $employees = Employee::all();

        $clients = Client::all();

        return view('workhours.add_workhours',compact('employees','clients'));
    }
}
