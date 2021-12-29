<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Language;
use App\Flag;

class LanguageController extends Controller
{
    public function index()
    {
        return view('admin.languages', [
            'Languages' => Language::orderBy('id', 'desc')->get(),
            'js'     => [
                asset('js/admin/languages.js')
            ]
        ]);
    }

    public function edit($id)
    {
        $Language = Language::find($id);

        if (is_null($Language))
            return redirect(route('admin-languages'));

        return view('admin.language-edit', [
            'Language' => $Language,
            'Flags' => Flag::GetFlags(),
            'js'    => [
                asset('js/admin/languages.js')
            ]
        ]);
    }

    public function add()
    {
        return view('admin.language-add', [
            'Flags' => Flag::GetFlags(),
            'js'    => [
                asset('js/admin/languages.js')
            ]
        ]);
    }

    public function add_language(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'flag'   => 'required|max:255',
            'name'   => 'required|max:255',
            'status' => 'required|max:255',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $Language         = new Language;
        $Language->flag  = $request->input('flag');
        $Language->name   = $request->input('name');
        $Language->status = $request->input('status');
        $Language->save();

        return response()->json([
            'status'  => 'success',
            'language_id' => $Language->id
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'required',
            'flag'        => 'required|max:255',
            'name'        => 'required|max:255',
            'status'      => 'required|max:255',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $Language = Language::find($request->input('language_id'));

        if (is_null($Language))
            return response()->json([
                'status' => 'error',
                'message' => 'Language not found.'
            ]);

        $Language->flag  = $request->input('flag');
        $Language->name   = $request->input('name');
        $Language->status = $request->input('status');
        $Language->save();

        return response()->json(['status' => 'success']);
    }

    public function delete(Request $request)
    {
        $Language = Language::find($request->input('language_id'));

        if (is_null($Language) or $Language->id == 1)
            return response()->json([
                'status' => 'error',
                'message' => ($Language->id == 1) ? 'Unable to delete primary language.' : 'Language not found.'
            ]);

        $Language->delete();

        return response()->json(['status' => 'success']);
    }
}