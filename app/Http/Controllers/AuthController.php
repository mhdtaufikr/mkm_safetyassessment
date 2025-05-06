<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccessRequestMail;
use Illuminate\Support\Facades\Hash; // Add this line



class AuthController extends Controller
{
         // Handle Change Password Logic
         public function changePassword(Request $request)
         {
             // Validate the request
             $request->validate([
                 'old_password' => 'required',
                 'new_password' => 'required|min:8|confirmed',
             ]);

             // Check if the old password is correct
             if (!Hash::check($request->old_password, Auth::user()->password)) {
                 return back()->withErrors(['old_password' => 'Old password is incorrect']);
             }

             // Update the user's password
             Auth::user()->update([
                 'password' => Hash::make($request->new_password),
             ]);

             // Return a success message
             return back()->with('password', 'Password changed successfully');
         }
    public function login(){
        return view('auth.login');
    }

    public function postLogin(Request $request)
{
    $emailOrName = $request->input('email');
    $password = $request->input('password');

    // Determine if input is an email address or a username
    $isEmail = filter_var($emailOrName, FILTER_VALIDATE_EMAIL);

    // Define credentials based on input type
    $credentials = $isEmail ? ['email' => $emailOrName] : ['username' => $emailOrName];
    $credentials['password'] = $password;

    // Attempt authentication
    if (Auth::attempt($credentials)) {
        // Authentication successful
        $user = Auth::user();

        // Check if the user is active
        if ($user->is_active == '1') {
            // Update last login and login counter
            User::where('id', $user->id)->update([
                'last_login' => now(),
                'login_counter' => $user->login_counter + 1,
            ]);

            // Check user role and redirect accordingly
            if ($user->role === 'User') {
                return redirect('/form'); // Redirect to /form for 'user' role
            } else {
                return redirect('/home'); // Redirect to /home for other roles
            }
        } else {
            // User is not active, redirect with a status message
            return redirect('/')->with('statusLogin', 'Give Access First to User');
        }
    } else {
        // Authentication failed, redirect with a status message
        return redirect('/')->with('statusLogin', 'Wrong Email/Name or Password');
    }
}




    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('statusLogout','Success Logout');
    }

    public function requestAccess(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'department' => 'required|string|max:255',
            'plant' => 'required|string|max:255',
            'purpose' => 'required|string|max:255',
        ]);

        // Send the email
        Mail::to(['aditia@ptmkm.co.id','muhammad.taufik@ptmkm.co.id','bayu@ptmkm.co.id'])
            ->send(new AccessRequestMail($request->all()));

        // Optionally, you can flash a success message or redirect to a specific page
        return back()->with('statusLogin', 'Your request has been submitted.');
    }

}
