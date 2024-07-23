<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Mail\OTPMail;


class UserController extends Controller
{
    public function sendOtp(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $existingUser = User::where('email', $request->input('email'))->first();
    if ($existingUser)
    {
        return back()->withErrors(['email' => 'Email already exists'])->withInput();
    }
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Generate and store OTP
        $otp = mt_rand(100000, 999999);
        $request->session()->put('otp', $otp);
        $request->session()->put('name', $request->input('name'));
        $request->session()->put('email', $request->input('email'));
        $request->session()->put('password', bcrypt($request->input('password')));

        Mail::send(['text' => 'otp'], ['otp' => $otp], function ($message) use ($request) {
            $message->to($request->input('email'))->subject('Your OTP');
        });

         return redirect('/verify-otp');
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp.*' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        $enteredOtpArray = $request->input('otp');
        $enteredOtp = implode('', $enteredOtpArray);
        $storedOtp = $request->session()->get('otp');


        if ($enteredOtp == $storedOtp)
        {

             $name = $request->session()->get('name');
            $email = $request->session()->get('email');
            $password = $request->session()->get('password');
            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
                'role' => 'admin'
            ]);
            $request->session()->forget('otp','name','email','password');

            return redirect()->route('admin.login');
        } else {
            return back()->withErrors(['otp' => 'Invalid OTP'])->withInput();
        }
    }


}
