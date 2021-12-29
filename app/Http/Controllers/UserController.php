<?php

namespace App\Http\Controllers;

use App\UserImages;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserImages(Request $request)
    {
        $userImages = UserImages::where(["UUID" => $_COOKIE['spe_uuid'], "file_type" => $request->type])->get();

        $response = $this->formatResponse('success', null, $userImages);
        return response($response, 200);
    }

    public function checkUserShouldWait(Request $request)
    {
        if ($request->ajax()) {
            if (!auth()->check()) {
                return response()->json(['status' => 'error', 'message' => 'User is not authenticated']);
            } else {
                $user = auth()->user();
                if (!$user->subscription) return response()->json(['status' => 'error', 'message' => 'User does not have a subscription']);
                if ($user->subscription->status !== 'active') return response()->json(['status' => 'error', 'message' => 'User subscription expired']);

                return response()->json(['status' => 'success']);
            }
        }
    }
}
