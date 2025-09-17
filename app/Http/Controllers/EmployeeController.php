<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function add_employee()
    {

        return view('employees.add_employee');
    }
    public function all_employees()
    {
        $employees = Employee::all();
        return view('employees.all_employees',compact('employees'));
    }
    public function single_employee($id)
    {
        $employee = Employee::with('workHours')->findOrFail($id);
        return view('employees.single_employee', compact('employee'));
    }


    public function store_employee(Request $request)
    {
        $validatedData = $request->validate([
            'employee_name' => 'required|string|max:255',
            // 'email' => 'required|email|unique:employees,email',
            // 'phone' => 'required|numeric|unique:employees,phone'
        ]);

        $employee = new Employee();
        $employee->name = $request->employee_name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->save();

        $notification = array(
            'message' => 'Employee Added Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.employee')->with($notification);
    }

    public function edit_employee($id)
    {
        $employee = Employee::findOrFail($id);

        return view('employees.edit_employee',compact('employee'));
    }

    public function update_employee(Request $request , $id)
    {
        $validatedData = $request->validate([
            'employee_name' => 'required|string|max:255',
            'phone' => 'required|numeric',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->name = $request->employee_name;
        $employee->phone = $request->phone;
        $employee->save();

        $notification = array(
            'message' => 'Employee Updated Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.employee')->with($notification);
    }

    public function delete_employee($id)
    {
        $employee = Employee::findOrFail($id);
        if($employee)
        {
            $employee->delete();
            $notification = array(
                'message' => 'Employee Deleted Successfully',
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
