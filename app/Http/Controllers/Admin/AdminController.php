<?php

namespace App\Http\Controllers\Admin;

use App\Document;
use App\Http\Controllers\Controller;
use App\UniqueVisitor;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class AdminController extends Controller
{
    public function login()
    {
        return view('admin.login');
    }

    public function redirect()
    {
        if (Auth::check() and (Auth::user()->role == 'admin' or Auth::user()->role == 'superadmin'))
            return redirect(route('admin-dashboard'));
        else
            return redirect(route('admin-login'));
    }

    public function dashboard()
    {
        return view('admin.dashboard', [
            'ActiveUsers' => User::where('last_activity', '>=', date('Y-m-d') . ' 00:00:00')->count(),
            'PDFToday' => Document::where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->count(),
            'UserToday' => User::where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->count(),
            'UniqueVisitors' => UniqueVisitor::where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->count(),
            'Documents' => Document::leftJoin('users', 'documents.user_id', '=', 'users.id')->select('documents.*', 'users.role', 'users.email')->orderBy('id', 'desc')->limit(20)->get(),
            'MapVisitors' => UniqueVisitor::select('iso_code', DB::raw('count(*) as total'))->groupBy('iso_code')->get(),
            'js' => [
                asset('admin-ui/dist/js/pages/dashboard.js'),
                asset('js/admin/dashboard.js')
            ]
        ]);
    }

    public function profile()
    {
        return view('admin.profile', [
            'js' => [
                asset('js/admin/profile.js')
            ]
        ]);
    }

    public function save_profile(Request $request)
    {
        $User = Auth::user();

        $validator = Validator::make($request->all(), [
            'email' => $User->email == $request->input('email') ? 'required|string|email|max:255' : 'required|string|email|max:255|unique:users',
            'password' => is_null($request->input('password')) ? '' : 'required|string|min:6|confirmed',
            'name' => 'required|max:255',
            'avatar' => 'required|max:255',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $User->email = $request->input('email');
        $User->avatar = $request->input('avatar');
        $User->name = $request->input('name');

        if (!is_null($request->input('password')))
            $User->password = bcrypt($request->input('password'));

        $User->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    /* administrators */

    public function administrators()
    {
        if (Auth::user()->role != 'superadmin')
            return redirect(route('admin-dashboard'));

        return view('admin.administrators', [
            'Users' => User::where('role', 'admin')->get(),
            'js' => [
                asset('js/admin/administrators.js')
            ]
        ]);
    }

    public function edit($id)
    {
        if (Auth::user()->role != 'superadmin')
            return redirect(route('admin-dashboard'));

        $User = User::find($id);

        if (is_null($User) or $User->role != 'admin')
            return redirect(route('admin-administrators'));

        return view('admin.administrator-edit', [
            'User' => $User,
            'js' => [
                asset('js/admin/administrators.js')
            ]
        ]);
    }

    public function update(Request $request)
    {
        if (Auth::user()->role != 'superadmin')
            return response()->json([
                'status' => 'error',
                'message' => 'No access.'
            ]);

        $User = User::find($request->input('user_id'));

        if (is_null($User))
            return response()->json([
                'status' => 'error',
                'message' => 'Administrator not found.'
            ]);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'email' => $User->email != $request->input('email') ? 'required|string|email|max:255|unique:users' : 'required|string|email|max:255',
            'password' => !is_null($request->input('password')) ? 'required|string|min:6|confirmed' : ''
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $User->email = $request->input('email');

        if (!is_null($request->input('password')))
            $User->password = bcrypt($request->input('password'));

        $User->save();

        return response()->json(['status' => 'success']);
    }

    public function delete(Request $request)
    {
        if (Auth::user()->role != 'superadmin')
            return response()->json([
                'status' => 'error',
                'message' => 'No access.'
            ]);

        $User = User::find($request->input('user_id'));

        if (is_null($User) or $User->role != 'admin')
            return response()->json([
                'status' => 'error',
                'message' => 'Administrator not found.'
            ]);

        $User->delete();

        return response()->json(['status' => 'success']);
    }

    public function add()
    {
        if (Auth::user()->role != 'superadmin')
            return redirect(route('admin-dashboard'));

        return view('admin.administrator-add', [
            'js' => [
                asset('js/admin/administrators.js')
            ]
        ]);
    }

    public function add_administrator(Request $request)
    {
        if (Auth::user()->role != 'superadmin')
            return response()->json([
                'status' => 'error',
                'message' => 'No access.'
            ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $User = new User;
        $User->email = $request->input('email');
        $User->password = bcrypt($request->input('password'));
        $User->status = 'active';
        $User->role = 'admin';
        $User->save();

        return response()->json([
            'status' => 'success',
            'user_id' => $User->id
        ]);
    }
}
