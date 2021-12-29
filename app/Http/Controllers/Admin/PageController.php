<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Page;
use App\Language;
use App\Excel;
use View;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages', [
            'Pages' => Page::orderBy('id', 'desc')->get(),
            'js'    => [
                'https://cdn.ckeditor.com/4.5.11/full/ckeditor.js',
                asset('js/admin/pages.js')
            ]
        ]);
    }

    public function add()
    {
        return view('admin.page-add', [
            'Languages' => Language::orderBy('id', 'asc')->get(),
            'js'        => [
                'https://cdn.ckeditor.com/4.5.11/full/ckeditor.js',
                asset('js/admin/pages.js')
            ]
        ]);
    }

    public function edit($id)
    {
        $Page = Page::find($id);

        if (is_null($Page))
            return redirect(route('admin-pages'));

        return view('admin.page-edit', [
            'Page'      => $Page,
            'Languages' => Language::orderBy('id', 'asc')->get(),
            'Blocks'    => json_decode($Page->blocks, true),
            'BottomBlocks'    => json_decode($Page->bottom_blocks, true),
            'js'        => [
                'https://cdn.ckeditor.com/4.5.11/full/ckeditor.js',
                asset('js/admin/pages.js')
            ]
        ]);
    }

    public function add_page(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status'            => 'required|max:255',
            'title'             => 'required|max:255',
            'link'              => 'required|max:255|unique:pages',
            'seo_title'         => 'max:255',
            'seo_keywords'      => 'max:255',
            'seo_description'   => 'max:255',
            'tool'              => 'max:255'
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $Page = new Page;
        $Page->status = $request->input('status');
        $Page->title = $request->input('title');
        $Page->blocks = json_encode([]);
        $Page->content = $request->input('content');
        $Page->default_link = $request->input('link');
        $Page->link = $request->input('link');
        $Page->seo_title = $request->input('seo_title');
        $Page->seo_keywords = $request->input('seo_keywords');
        $Page->seo_description = $request->input('seo_description');
        $Page->tool = $request->input('tool');
        $Page->added_dashboard = 1;
        $Page->save();

        return response()->json([
            'status' => 'success',
            'page_id' => $Page->id
        ]);
    }

    public function update(Request $request)
    {
    
        $Page = Page::find($request->input('page_id'));

        if (is_null($Page))
            return response()->json([
                'status' => 'error',
                'message' => 'Page not found.'
            ]);

        $validator = Validator::make($request->all(), [
            'page_id'           => 'required',
            'status'            => 'required|max:255',
            'title'             => 'required|max:255',
            'link'              => $Page->link != $request->input('link') ? 'required|max:255|unique:pages' : 'required|max:255',
            'tool'              => 'max:255'
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

		//blocks

        $Page->status = $request->input('status');
        $Page->title = $request->input('title');
        $Page->blocks = !is_null($request->input('blocks')) ? json_encode($request->input('blocks')) : json_encode([]);
        
        $Page->bottom_blocks = !is_null($request->input('bottom_blocks')) ? json_encode($request->input('bottom_blocks')) : json_encode([]);
        
        $Page->content = $request->input('content');
        $Page->link = $request->input('link');
        $Page->seo_title = !is_null($request->input('seo_title')) ? json_encode($request->input('seo_title')) : json_encode([]);
        $Page->seo_keywords = !is_null($request->input('seo_keywords')) ? json_encode($request->input('seo_keywords')) : json_encode([]);
        $Page->seo_description = !is_null($request->input('seo_description')) ? json_encode($request->input('seo_description')) : json_encode([]);
        $Page->tool = $request->input('tool');
        $Page->save();

        return response()->json([
            'status' => 'success',
            'page_id' => $Page->id
        ]);
    }

    public function delete(Request $request)
    {
        $Page = Page::find($request->input('page_id'));

        if (is_null($Page) or $Page->static == 1)
            return response()->json([
                'status' => 'error',
                'message' => 'Page not found.'
            ]);

        $Page->delete();

        return response()->json(['status' => 'success']);
    }

    public function export()
    {
        $Languages = [];
        $headers = [
            'ID', 'Title Page', 'Field'
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
        $max_line = $line;

        foreach (Page::get() as $key => $Page) {
            $data[$line]['A'] = $Page->id;
            $data[$line]['B'] = $Page->title;

            foreach (json_decode($Page->blocks) as $lang_id => $blocks) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $line_tmp = $line;

                foreach ($blocks as $key => $block) {
                    $data[$line_tmp]['C'] = 'Block #'.$key;
                    $data[$line_tmp][$char] = $block;
                    $line_tmp++;

                    if ($line_tmp > $max_line)
                        $max_line = $line_tmp;
                }
            }

            $line = $max_line;

            foreach (json_decode($Page->seo_title) as $lang_id => $seo_title) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $data[$line]['C'] = 'SEO Title';
                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $data[$line][$char] = $seo_title;
            }

            $line++;

            foreach (json_decode($Page->seo_keywords) as $lang_id => $seo_keywords) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $data[$line]['C'] = 'SEO Keywords';
                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $data[$line][$char] = $seo_keywords;
            }

            $line++;

            foreach (json_decode($Page->seo_description) as $lang_id => $seo_description) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $data[$line]['C'] = 'SEO Description';
                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $data[$line][$char] = $seo_description;
            }

            $line++;
        }

        //var_dump($data);

        Excel::generate($data, 'pages');
    }
}
