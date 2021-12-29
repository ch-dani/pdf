<?php

namespace App\Http\Controllers\Admin;

use App\Document;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        return view('admin.documents', [
            'Documents' => Document::leftJoin('users', 'documents.user_id', '=', 'users.id')->select('documents.*', 'users.role', 'users.email')->where('documents.created_at', '>=', \Carbon\Carbon::now()->subHour())->orderBy('id', 'desc')->get(),
            'js' => [
                asset('js/admin/documents.js')
            ]
        ]);
    }
}