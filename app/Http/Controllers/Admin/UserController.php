<?php

namespace App\Http\Controllers\Admin;

use App\Document;
use App\Documents;
use App\Http\Controllers\Controller;
use App\User;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class UserController extends Controller
{
    public function index(Request $req)
    {
	
		if($req->input('only_subscribed')){
			$users = User::whereHas("subscription")->where('role', '!=', 'admin')
				        ->leftjoin('documents', 'users.id', '=', 'documents.user_id')
				        ->select('users.*', DB::raw('COUNT(documents.id) as documents_count'))
				        ->orderBy('users.id', 'desc')
				        ->groupBy('users.id')
				        ->get();
		}else{
			$users = User::where('role', '!=', 'admin')
				        ->leftjoin('documents', 'users.id', '=', 'documents.user_id')
				        ->select('users.*', DB::raw('COUNT(documents.id) as documents_count'))
				        ->orderBy('users.id', 'desc')
				        ->groupBy('users.id')
				        ->get();		
		
		}
		 
        return view('admin.users', [
            'Users' => $users,
            'only_subscribed'=>(int)$req->input('only_subscribed') ?? false,
            'js' => [
                asset('js/admin/users.js')
            ]
        ]);
    }

    public function edit($id)
    {
        $User = User::find($id);

        if (is_null($User) or $User->role == 'admin')
            return redirect(route('admin-users'));

        return view('admin.user-edit', [
            'User' => $User,
            'Documents' => Document::where('user_id', $User->id)->orderBy('id', 'desc')->get(),
            'js' => [
                asset('js/admin/users.js')
            ]
        ]);
    }

    public function show($id)
    {
        $User = User::find($id);

        if (is_null($User) or $User->role == 'admin')
            return redirect(route('admin-users'));

        return view('admin.user-show', [
            'User' => $User,
            'Documents' => Document::where('user_id', $User->id)->orderBy('id', 'desc')->get(),
            'js' => [
                asset('js/admin/users.js')
            ]
        ]);
    }

    public function add()
    {
        return view('admin.user-add', [
            'js' => [
                asset('js/admin/users.js')
            ]
        ]);
    }

    public function add_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'required|max:255',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $User = new User;
        $User->email = $request->input('email');
        $User->password = bcrypt($request->input('password'));
        $User->status = $request->input('status');
        $User->save();

        return response()->json([
            'status' => 'success',
            'user_id' => $User->id
        ]);
    }

    public function update(Request $request)
    {
        $User = User::find($request->input('user_id'));

        if (is_null($User))
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ]);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'email' => $User->email != $request->input('email') ? 'required|string|email|max:255|unique:users' : 'required|string|email|max:255',
            'password' => !is_null($request->input('password')) ? 'required|string|min:6|confirmed' : '',
            'status' => 'required|max:255',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $User->email = $request->input('email');

        if (!is_null($request->input('password')))
            $User->password = bcrypt($request->input('password'));

        $User->status = $request->input('status');
        $User->save();

        return response()->json(['status' => 'success']);
    }

    public function delete(Request $request)
    {
        $User = User::find($request->input('user_id'));

        if (is_null($User) or $User->role == 'admin')
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ]);

        $User->delete();

        return response()->json(['status' => 'success']);
    }

    public function delete_document(Request $request)
    {
        if (is_array($request->input('document_id'))) {
            $count = 0;

            foreach ($request->input('document_id') as $document_id) {
                $Document = Document::find($document_id);

                if (!is_null($Document)) {
                    if (!is_null($Document->original_document))
                        File::delete(public_path($Document->original_document));

                    if (!is_null($Document->edited_document))
                        File::delete(public_path($Document->edited_document));

                    $Document->delete();
                    $count++;
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Removed '.$count.' documents']);
        } else {
            $Document = Document::find($request->input('document_id'));

            if (is_null($Document))
                return response()->json([
                    'status' => 'error',
                    'message' => 'Document not found.'
                ]);

            if (!is_null($Document->original_document))
                File::delete(public_path($Document->original_document));

            if (!is_null($Document->edited_document))
                File::delete(public_path($Document->edited_document));

            $Document->delete();

            return response()->json(['status' => 'success']);
        }
    }

    public function login($id)
    {
        $User = User::find($id);

        if (is_null($User))
            return back();

        Auth::login($User);

        return redirect(route('account'));
    }
}
