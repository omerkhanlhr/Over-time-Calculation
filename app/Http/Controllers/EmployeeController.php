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
        $designations  = Designation::all();
        return view('employees.add_employee',compact('designations'));
    }
    public function all_employees()
    {
        $employees = Employee::all();
        return view('employees.all_employees',compact('employees'));
    }

    public function single_employee($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.single_employee',compact('employee'));
    }

    public function store_employee(Request $request)
    {
        $validatedData = $request->validate([
            'employee_name' => 'required|string|max:255',
            'employee_cnic' => 'required|string|size:13|unique:employees,cnic|regex:/^\d{13}$/',
            'email' => 'required|email|unique:employees,email',
            'designation_id' => 'required|exists:designations,id',
            'joining_date' => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
            'rent' => 'required|numeric|min:0',
            'allowance' => 'required|numeric|min:0',
            'deduction' => 'required|numeric|min:0',
            'salary_month' => 'required|date_format:Y-m'
        ]);

        $sub_salary = $request->base_salary + $request->commission + $request->rent + $request->allowance;
        $total_salary = $sub_salary - $request->deduction;

        $employee = new Employee();
        $employee->emp_name = $request->employee_name;
        $employee->cnic = $request->employee_cnic;
        $employee->email = $request->email;
        $employee->designation_id = $request->designation_id;
        $employee->joining_date = $request->joining_date;
        $employee->save();

        Salary::create([
            'employee_id' => $employee->id,
            'base_salary' => $request->base_salary,
            'commission' => $request->commission,
            'rent' => $request->rent,
            'allowance' => $request->allowance,
            'deduction' => $request->deduction,
            'sub_salary' => $sub_salary,
            'salary_month' => $request->salary_month . '-01',
            'total_salary' => $total_salary
        ]);

        $notification = array(
            'message' => 'Employee Added Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.employee')->with($notification);
    }

    public function edit_employee($id)
    {
        $employee = Employee::findOrFail($id);
        $designations = Designation::all();
        return view('employees.edit_employee',compact('employee' , 'designations'));
    }

    public function update_employee(Request $request , $id)
    {
        $validatedData = $request->validate([
            'employee_name' => 'required|string|max:255',
            'designation_id' => 'required|exists:designations,id',
            'joining_date' => 'required|date',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->emp_name = $request->employee_name;
        $employee->designation_id = $request->designation_id;
        $employee->joining_date = $request->joining_date;
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

    public function showPage()
    {
        $employees = Employee::all();
        return view('employees_salaries.employee_salary', compact('employees'));
    }

    public function getEmployeeData(Request $request)
    {
        $employeeId = $request->employee_id;
        $latestSalary = Salary::where('employee_id', $employeeId)->latest()->first();

        return response()->json([
            'base_salary' => $latestSalary->base_salary ?? '',
            'rent' => $latestSalary->rent ?? '',
            'allowance' => $latestSalary->allowance ?? '',
            'commission' => $latestSalary->commission ?? '',
            'deduction' => $latestSalary->deduction ?? '',
        ]);
    }
}
