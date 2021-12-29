<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Faq;
use App\Language;
use App\Excel;

class FaqController extends Controller
{
    public function index()
    {
        return view('admin.faq', [
            'Faq' => Faq::orderBy('id', 'desc')->get(),
            'js'  => [
                asset('js/admin/faq.js')
            ]
        ]);
    }

    public function edit($id)
    {
        $Faq = Faq::find($id);

        if (is_null($Faq))
            return redirect(route('admin-faq'));

        return view('admin.faq-edit', [
            'Faq'       => $Faq,
            'Languages' => Language::orderBy('id', 'asc')->get(),
            'js'        => [
                asset('js/admin/faq.js')
            ]
        ]);
    }

    public function add()
    {
        return view('admin.faq-add', [
            'Languages' => Language::orderBy('id', 'asc')->get(),
            'js'        => [
                asset('js/admin/faq.js')
            ]
        ]);
    }

    public function add_faq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'  => 'required',
            'step'   => 'required',
            'sort'   => 'required|integer',
            'status' => 'required|max:255',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $Faq             = new Faq;
        $Faq->title      = json_encode($request->input('title'));
        $Faq->icons      = json_encode($request->input('icons'));
        $Faq->steps      = json_encode($request->input('step'));
        $Faq->sort       = $request->input('sort');
        $Faq->link       = json_encode($request->input('link'));
        $Faq->link_title = json_encode($request->input('link_title'));
        $Faq->status     = $request->input('status');
        $Faq->save();

        return response()->json([
            'status'  => 'success',
            'faq_id' => $Faq->id
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faq_id' => 'required',
            'title'  => 'required',
            'step'   => 'required',
            'sort'   => 'required|integer',
            'status' => 'required|max:255',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $Faq = Faq::find($request->input('faq_id'));

        if (is_null($Faq))
            return response()->json([
                'status' => 'error',
                'message' => 'Faq not found.'
            ]);

        $Faq->title      = json_encode($request->input('title'));
        $Faq->icons      = json_encode($request->input('icons'));
        $Faq->steps      = json_encode($request->input('step'));
        $Faq->sort       = $request->input('sort');
        $Faq->link       = json_encode($request->input('link'));
        $Faq->link_title = json_encode($request->input('link_title'));
        $Faq->status     = $request->input('status');
        $Faq->save();

        return response()->json(['status' => 'success']);
    }

    public function delete(Request $request)
    {
        $Faq = Faq::find($request->input('faq_id'));

        if (is_null($Faq))
            return response()->json([
                'status'  => 'error',
                'message' => 'Faq not found.'
            ]);

        $Faq->delete();

        return response()->json(['status' => 'success']);
    }

    public function export()
    {
        $Languages = [];
        $headers = [
            'ID', 'Field'
        ];

        foreach (Language::orderBy('id', 'asc')->get() as $Language) {
            $Languages[$Language->id] = $Language->name;
            $headers[] = $Language->name;
        }

        $line = 1;
        $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $data = [];

        foreach ($headers as $key => $header) {
            $data[$line][$alphabet[$key]] = $header;
        }

        $line++;

        foreach (Faq::get() as $key => $Faq) {
            $data[$line]['A'] = $Faq->id;
            $data[$line]['B'] = 'Title';

            foreach (json_decode($Faq->title) as $lang_id => $title) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $data[$line][$char] = $title;
            }

            $line++;
            $data[$line]['B'] = 'Link title';

            foreach (json_decode($Faq->link_title) as $lang_id => $link_title) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $data[$line][$char] = $link_title;
            }

            $line++;
            $data[$line]['B'] = 'Link';

            foreach (json_decode($Faq->link) as $lang_id => $link) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $data[$line][$char] = $link;
            }

            $line++;

            foreach (json_decode($Faq->steps) as $lang_id => $steps) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $line_tmp = $line;
                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                foreach ($steps as $number_step => $step) {
                    $data[$line_tmp]['B'] = 'Step #'.$number_step;
                    $data[$line_tmp][$char] = $step;
                    $line_tmp++;
                }
            }

            $line = $line_tmp;
        }

        Excel::generate($data, 'faq');
    }
}