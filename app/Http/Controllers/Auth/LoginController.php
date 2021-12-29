<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Socialite;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/account';

    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        return abort(404);
    }

    protected function redirectPath()
    {
        return (Auth::user()->role == 'admin' or Auth::user()->role == 'superadmin')
            ? route('admin-dashboard')
            : $this->redirectTo;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function google_auth_redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function facebook_auth_redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function google_callback()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/');
        }

        $existingUser = User::where('email', $user->email)
            ->where('google_id', $user->id)
            ->orWhere('email', $user->email)
            ->whereNull('google_id')
            ->first();

        if ($existingUser) {
            if (is_null($existingUser->google_id)) {
                $existingUser->google_id = $user->id;
                $existingUser->save();
            }

            auth()->login($existingUser, true);
        } else {
            $newUser = new User;
            $newUser->email = $user->email;
            $newUser->google_id = $user->id;
            $newUser->name = $user->name;
            $newUser->last_confirmation = date("Y-m-d H:i:s", time());
            $newUser->register_token = uniqid();

            $newUser->save();
            auth()->login($newUser, true);
        }

        return redirect('/');
    }

    public function facebook_callback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            return redirect('/');
        }

        $existingUser = User::where('email', $user->email)
            ->where('facebook_id', $user->id)
            ->orWhere('email', $user->email)
            ->whereNull('facebook_id')
            ->first();

        if ($existingUser) {
            if (is_null($existingUser->facebook_id)) {
                $existingUser->facebook_id = $user->id;
                $existingUser->save();
            }

            auth()->login($existingUser, true);
        } else {
            $newUser = new User;
            $newUser->email = $user->email;
            $newUser->facebook_id = $user->id;
            $newUser->name = $user->name;
            $newUser->last_confirmation = date("Y-m-d H:i:s", time());
            $newUser->register_token = uniqid();

            $newUser->save();
            auth()->login($newUser, true);
        }

        return redirect('/');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

	public function validateLogin(Request $req){
		

		$this->validate($req, [
			'email' => 'required', 
			'password' => 'required',
			'g-recaptcha-response' => new \App\Rules\Recaptcha
		]);
	}

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Successfully logged in']);
        }
    }
}
