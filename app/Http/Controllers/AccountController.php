<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use Illuminate\Http\Request;
use Mail;
use App\Mail\ActivateAccount;

class AccountController extends Controller
{
    public function index()
    {
        $user = \auth()->user();

        return view('account', compact('user'));
    }

    public function changePassword(Request $request)
    {
        $request->validate(['password' => 'required|confirmed|string|min:6']);

        \auth()->user()->update(['password' => bcrypt($request->password)]);

        return response()->json(['status' => 'success', 'message' => 'Password was changed']);
    }
    
    
    public function resendConfirmationEmail(){
    	$User = Auth::user();
    	if(!$User->last_confirmation || strtotime($User->last_confirmation)+300 >= time()){
    		exit("error. 5 minutes have not passed yet");
    	}
    	
    	$User->register_token = uniqid(); 
    	$User->last_confirmation = date("Y-m-d H:i:s");
    	$User->save();
    	
        Mail::to($User)->send(new ActivateAccount($User));
        return redirect('/account');
    }

    public function change_password_save(Request $request)
    {
        $User = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|max:255',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        if (!Hash::check($request->input('current_password'), Auth::user()->password))
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid current password'
            ]);

        $User->password = bcrypt($request->input('new_password'));
        $User->save();

        return response()->json([
            'status' => 'success'
        ]);
    }
}
