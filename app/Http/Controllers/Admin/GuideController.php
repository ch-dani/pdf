<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Guide;
use App\Page;
use App\GuideTool;
use App\Language;
use App\Excel;

class GuideController extends Controller
{
    public function index()
    {
        return view('admin.guides', [
            'Guides' => Guide::orderBy('id', 'desc')->get(),
            'js'     => [
                asset('assets/ckeditor/ckeditor.js'),
                asset('js/admin/guides.js')
            ]
        ]);
    }

    public function edit($id)
    {
        $Guide = Guide::find($id);
        
        if(isset($_COOKIE['ads'])){
        
        	exit("x1");
        }
        

        if (is_null($Guide))
            return redirect(route('admin-guides'));

        $GuideTools = [];
        foreach (GuideTool::select('tool')->where('guide_id', $Guide->id)->get() as $tool)
            $GuideTools[] = $tool->tool;

        return view('admin.guide-edit', [
            'Guide'      => $Guide,
            'GuideTools' => $GuideTools,
            'Languages'  => Language::orderBy('id', 'asc')->get(),
            'Tools'      => Page::select('tool')->whereNotNull('tool')->distinct()->get(),
            'js'         => [
                asset('assets/ckeditor/ckeditor.js'),
                asset('js/admin/guides.js')
            ]
        ]);
    }

    public function add()
    {
        return view('admin.guide-add', [
            'Tools'     => Page::select('tool')->whereNotNull('tool')->distinct()->get(),
            'Languages' => Language::orderBy('id', 'asc')->get(),
            'js'        => [
                asset('assets/ckeditor/ckeditor.js'),
                asset('js/admin/guides.js')
            ]
        ]);
    }

    public function add_guide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'max:255',
            'subtitle' => 'max:500',
            'sort'     => 'required|integer',
            'status'   => 'required|max:255',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $contents = [];
        foreach ($request->input('content') as $key => $content)
            $contents[$key] = htmlspecialchars(str_replace('#38;', '&', $content));

        $Guide           = new Guide;
        $Guide->title    = json_encode($request->input('title'));
        $Guide->sort     = $request->input('sort');
        $Guide->status   = $request->input('status');
        $Guide->content  = json_encode($contents);
        $Guide->subtitle = json_encode($request->input('subtitle'));
        $Guide->save();

        if (!is_null($request->input('tools')) and is_array($request->input('tools'))) {
            foreach ($request->input('tools') as $tool) {
                $GuideTool           = new GuideTool;
                $GuideTool->guide_id = $Guide->id;
                $GuideTool->tool     = $tool;
                $GuideTool->save();
            }
        }

        return response()->json([
            'status'  => 'success',
            'guide_id' => $Guide->id
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guide_id' => 'required',
            'title'    => 'max:255',
            'subtitle' => 'max:500',
            'sort'     => 'required|integer',
            'status'   => 'required|max:255',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $Guide = Guide::find($request->input('guide_id'));

        if (is_null($Guide))
            return response()->json([
                'status' => 'error',
                'message' => 'Guide not found.'
            ]);

        $contents = [];
        foreach ($request->input('content') as $key => $content)
            $contents[$key] = htmlspecialchars(str_replace('#38;', '&', $content));

        $Guide->title    = json_encode($request->input('title'));
        $Guide->sort     = $request->input('sort');
        $Guide->status   = $request->input('status');
        $Guide->content  = json_encode($contents);
        $Guide->subtitle = json_encode($request->input('subtitle'));
        $Guide->save();

        GuideTool::where('guide_id', $Guide->id)->delete();

        if (!is_null($request->input('tools')) and is_array($request->input('tools'))) {
            foreach ($request->input('tools') as $tool) {
                $GuideTool           = new GuideTool;
                $GuideTool->guide_id = $Guide->id;
                $GuideTool->tool     = $tool;
                $GuideTool->save();
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function delete(Request $request)
    {
        $Guide = Guide::find($request->input('guide_id'));

        if (is_null($Guide))
            return response()->json([
                'status'  => 'error',
                'message' => 'Guide not found.'
            ]);

        GuideTool::where('guide_id', $Guide->id)->delete();
        $Guide->delete();

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

        foreach (Guide::get() as $key => $Guide) {
            $data[$line]['A'] = $Guide->id;
            $data[$line]['B'] = 'Title';

            foreach (json_decode($Guide->title) as $lang_id => $title) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $data[$line][$char] = $title;
            }

            $line++;
            $data[$line]['B'] = 'Subtitle';

            foreach (json_decode($Guide->subtitle) as $lang_id => $subtitle) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $data[$line][$char] = $subtitle;
            }

            $line++;
            $data[$line]['B'] = 'Content';

            foreach (json_decode($Guide->content) as $lang_id => $content) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $data[$line][$char] = htmlspecialchars_decode($content);
            }

            $line++;
        }

        Excel::generate($data, 'guides');
    }
}
