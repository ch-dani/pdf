<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());
        $passwordReset = DB::table('password_resets')->where('email', $request->email)->first();

        if (Hash::check($request->token, $passwordReset->token)) {
            $user = User::where('email', $request->email)->first();
            $user->update(['password' => bcrypt($request->password)]);
        }

        return redirect('/');
    }

    public function resetPassword(Request $request, PasswordBroker $passwordBroker)
    {
        if ($request->ajax()) {
            $request->validate(['email' => 'required|email']);

            $response = $passwordBroker->sendResetLink(['email' => $request->email]);
            switch ($response) {
                case PasswordBroker::RESET_LINK_SENT:
                    return [
                        'error' => 'false',
                        'msg' => 'A password link has been sent to your email address'
                    ];

                case PasswordBroker::INVALID_USER:
                    return [
                        'error' => 'true',
                        'msg' => "We can't find a user with that email address"
                    ];
            }
        }

        return false;
    }
}
