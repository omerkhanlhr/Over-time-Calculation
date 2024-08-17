<?php

namespace App\Http\Controllers;

use App\Exports\ClientsExport;
use App\Exports\EmployeeExport;
use App\Exports\InvoicesExport;
use App\Exports\PaymentsExport;
use App\Exports\ProductsExport;
use App\Exports\UsersExport;
use App\Imports\EmployeeImport;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function save_import_employee(Request $request)
{
    $file = $request->file('file');

    // Load the data from the Excel file without importing it
    $data = Excel::toArray([], $file)[0]; // Assuming the first sheet

    // Collect existing emails and phone numbers from the database
    $existingEmails = Employee::pluck('email')->toArray();
    $existingPhones = Employee::pluck('phone')->toArray();

    $duplicateEmails = [];
    $duplicatePhones = [];

    // Track duplicates within the file itself
    $fileEmails = [];
    $filePhones = [];

    foreach ($data as $row) {
        $email = $row[1] ?? null;
        $phone = $row[2] ?? null;

        // Check for duplicate emails in the file
        if ($email && in_array($email, $fileEmails)) {
            $duplicateEmails[] = $email;
        } else {
            $fileEmails[] = $email;
        }

        // Check for duplicate phones in the file
        if ($phone && in_array($phone, $filePhones)) {
            $duplicatePhones[] = $phone;
        } else {
            $filePhones[] = $phone;
        }

        // Check for existing emails in the database
        if ($email && in_array($email, $existingEmails)) {
            $duplicateEmails[] = $email;
        }

        // Check for existing phones in the database
        if ($phone && in_array($phone, $existingPhones)) {
            $duplicatePhones[] = $phone;
        }
    }

    // If there are duplicates, return an error message
    if (!empty($duplicateEmails) || !empty($duplicatePhones)) {
        $errorMessage = 'The file contains duplicate or existing data.';

        if (!empty($duplicateEmails)) {
            $errorMessage .= ' Duplicate emails: ' . implode(', ', array_unique($duplicateEmails)) . '.';
        }

        if (!empty($duplicatePhones)) {
            $errorMessage .= ' Duplicate phones: ' . implode(', ', array_unique($duplicatePhones)) . '.';
        }

        return redirect()->back()->withErrors(['file' => $errorMessage]);
    }

    // If no duplicates are found, proceed with the import
    Excel::import(new EmployeeImport, $file);

    $notification = [
        'message' => "Data Inserted Successfully",
        'alert-type' => 'success'
    ];

    return redirect()->back()->with($notification);
}


    public function import_employee()
    {
        return view('employees.import_employees');
    }

    public function export_employee()
    {
        return Excel::download(new EmployeeExport, 'employees.xlsx');
    }

    public function index()
    {

        return view('admin.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function login()
    {
        return view('admin.admin_login');
    }
    public function profile()
    {
        $id= Auth::user()->id;
        $profile=User::find($id);
        return view('admin.admin_profile',compact('profile'));
    }

    public function update_profile(Request $req)
    {
        $id= Auth::user()->id;
        $data=User::find($id);
        $data->name=$req->name;
        $data->email=$req->email;
        if($req->file('photo'))
        {
            $file=$req->file('photo');
            @unlink(public_path('images/admin_images/'.$data->photo));
            $filename=date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('images/admin_images'),$filename);
            $data['photo']=$filename;
        }
        $data->save();
        $notification=array(
            'message'=>'Admin Profile Updated Successfully',
            'alert-type'=>'success'
        );
        return redirect()->back()->with($notification);
    }
    public function change_password()
    {
        $id= Auth::user()->id;
        $profile=User::find($id);
        return view('admin.admin_change_password',compact('profile'));
    }

    public function update_password(Request $req)
    {
        $req->validate([
            'old_password'=>'required',
            'new_password'=>'required|confirmed'
        ]);
        if(!Hash::check($req->old_password,auth::user()->password))
        {
            $notification=array(
                'message'=>'Old Password does not match',
                'alert-type'=>'error'
            );
            return back()->with($notification);
        }
        User::whereId(auth()->user()->id)->update([
            'password'=>Hash::make($req->new_password)
        ]);
        $notification=array(
            'message'=>'Password Change Successfully',
            'alert-type'=>'success'
        );
        return back()->with($notification);
    }

    public function addUser()
    {
        return view('Users.add_user');
    }

public function saveUser(Request $req)
{
    $req->validate([
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required',
        'role' => 'required',
    ]);

    $existingUser = User::where('email', $req['email'])->first();

    if ($existingUser) {
        $notification = array(
            'message' => 'Email already exists. Please choose a different email.',
            'alert-type' => 'error',
        );

        return redirect()->back()->with($notification);
    }

    // If the email doesn't exist, proceed to save the user
    $user = new User();
    $user->name = $req['name'];
    $user->email = $req['email'];
    $user->role = $req['role'];
    $user->password = $req['password'];
    $user->save();

    $notification = array(
        'message' => 'User Added Successfully',
        'alert-type' => 'success',
    );

    return redirect()->route('all.users')->with($notification);
}

    public function allUsers()
    {
        $users=User::all();
        return view('Users.all_users',compact('users'));
    }

    public function editUser($id)
    {
        $user=User::findOrFail($id);
        return view('Users.edit_user',compact('user'));
    }

    public function updateUser(Request $req)
    {
        $id=$req->id;
        $user=User::findOrFail($id);
        $user->name=$req['name'];
        $user->email=$req['email'];
        $user->role=$req['role'];
        $user->save();
        $notification=array(
            'message'=>'User Updated Successfully',
            'alert-type'=>'success'
        );
        return redirect()->route('all.users')->with($notification);
    }

    public function singleUser($id)
    {
        $user=User::findOrFail($id);
        return view('Users.single_user',compact('user'));
    }

    public function deleteUser($id)
    {
        $user=User::findOrFail($id);
        $user->delete();

        $notification=array(
            'message'=>'User Deleted Successfully',
            'alert-type'=>'success'
        );
        return redirect()->back()->with($notification);
    }


}
