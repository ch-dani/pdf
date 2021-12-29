<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Storage;
use Image;
use App\Excel;
use App\Page;
use App\Language;
use App\Faq;
use App\Guide;
use App\Menu;
use App\FooterMenu;

class UploadController extends Controller
{
    public function upload_ck( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'upload' => 'required|mimes:jpeg,png,gif,jpg',
        ]);

        $funcNum = $request->input('CKEditorFuncNum');

        if ($validator->fails())
            return response(
                "<script>
                    window.parent.CKEDITOR.tools.callFunction({$funcNum}, '', '{$validator->errors()->first()}');
                </script>"
            );

        $url = asset('/storage/'.Storage::putFile('uploads', $request->file('upload')));

        return response(
            "<script>
                window.parent.CKEDITOR.tools.callFunction({$funcNum}, '{$url}', 'Image uploaded successfully');
            </script>"
        );

    }

    public function upload( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
            'file.*' => 'image|mimes:jpeg,png,jpg,gif,ico,svg',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $extension = $request->file('file')->getClientOriginalExtension();
        $filename = uniqid().'.'.$extension;

        $upload = '/storage/'.Storage::putFileAs('uploads', $request->file('file'), $filename);

        return response()->json([
            'status' => 'success',
            'upload' => $upload
        ]);

    }

    public function import( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'file' => 'required',
            'file.*' => 'mimes:xls',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $extension = $request->file('file')->getClientOriginalExtension();
        $filename = uniqid().'.'.$extension;

        $storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $file = Storage::putFileAs('import', $request->file('file'), $filename);
        $filepath = $storagePath.$file;

        $result = Excel::XlsToArray($filepath, $request->input('type'));

        if (!$result)
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid file structure.'
            ]);

        foreach (Language::get() as $Language)
            $Languages[$Language->name] = $Language->id;

        switch ($request->input('type')) {
            case "pages":
                foreach ($result as $page_id => $data) {
                    $Page = Page::find($page_id);

                    if (!is_null($Page)) {
                        $Page->title = $data['title'];

                        $blocks = [];
                        $seo_title = [];
                        $seo_keywords = [];
                        $seo_description = [];

                        foreach ($data as $field => $val) {
                            if (strpos($field, 'Block #') !== false) {
                                $number_block = intval(str_replace('Block #', '', $field));

                                foreach ($val as $lang => $v)
                                    $blocks[$Languages[$lang]][$number_block] = $v;
                            }

                            if (strpos($field, 'SEO Title') !== false) {
                                foreach ($val as $lang => $v)
                                    $seo_title[$Languages[$lang]] = $v;
                            }

                            if (strpos($field, 'SEO Keywords') !== false) {
                                foreach ($val as $lang => $v)
                                    $seo_keywords[$Languages[$lang]] = $v;
                            }

                            if (strpos($field, 'SEO Description') !== false) {
                                foreach ($val as $lang => $v)
                                    $seo_description[$Languages[$lang]] = $v;
                            }
                        }

                        $Page->blocks = json_encode($blocks);
                        $Page->seo_title = json_encode($seo_title);
                        $Page->seo_keywords = json_encode($seo_keywords);
                        $Page->seo_description = json_encode($seo_description);
                        $Page->save();
                    }
                }
                break;
            case "faq":
                foreach ($result as $faq_id => $data) {
                    $Faq = Faq::find($faq_id);

                    if (!is_null($Faq)) {

                        $title = [];
                        $link_title = [];
                        $link = [];
                        $steps = [];

                        foreach ($data as $field => $val) {
                            if (strpos($field, 'Step #') !== false) {
                                $number_step = intval(str_replace('Step #', '', $field));

                                foreach ($val as $lang => $v)
                                    $steps[$Languages[$lang]][$number_step] = $v;
                            }

                            if (strpos($field, 'Title') !== false) {
                                foreach ($val as $lang => $v)
                                    $title[$Languages[$lang]] = $v;
                            }

                            if (strpos($field, 'Link title') !== false) {
                                foreach ($val as $lang => $v)
                                    $link_title[$Languages[$lang]] = $v;
                            }

                            if (strpos($field, 'Link') !== false) {
                                foreach ($val as $lang => $v)
                                    $link[$Languages[$lang]] = $v;
                            }
                        }

                        $Faq->link = json_encode($link);
                        $Faq->link_title = json_encode($link_title);
                        $Faq->title = json_encode($title);
                        $Faq->steps = json_encode($steps);
                        $Faq->save();
                    }
                }
                break;
            case "guides":
                foreach ($result as $guide_id => $data) {
                    $Guide = Guide::find($guide_id);

                    if (!is_null($Guide)) {

                        $title = [];
                        $subtitle = [];
                        $content = [];

                        foreach ($data as $field => $val) {
                            if (strpos($field, 'Title') !== false) {
                                foreach ($val as $lang => $v)
                                    $title[$Languages[$lang]] = $v;
                            }

                            if (strpos($field, 'Subtitle') !== false) {
                                foreach ($val as $lang => $v)
                                    $subtitle[$Languages[$lang]] = $v;
                            }

                            if (strpos($field, 'Content') !== false) {
                                foreach ($val as $lang => $v)
                                    $content[$Languages[$lang]] = htmlspecialchars($v);
                            }
                        }

                        $Guide->title = json_encode($title);
                        $Guide->subtitle = json_encode($subtitle);
                        $Guide->content = json_encode($content);
                        $Guide->save();
                    }
                }
                break;
            case "menu":
                foreach ($result as $menu_id => $data) {
                    $Menu = Menu::find($menu_id);

                    if (!is_null($Menu)) {

                        $title = [];
                        $tooltip = [];

                        foreach ($data as $field => $val) {
                            if (strpos($field, 'Title') !== false) {
                                foreach ($val as $lang => $v)
                                    $title[$Languages[$lang]] = $v;
                            }

                            if (strpos($field, 'Tooltip') !== false) {
                                foreach ($val as $lang => $v)
                                    $tooltip[$Languages[$lang]] = $v;
                            }
                        }

                        $Menu->title = json_encode($title);
                        $Menu->tooltip = json_encode($tooltip);
                        $Menu->save();
                    }
                }
                break;
            case "footer-menu":
                foreach ($result as $menu_id => $data) {
                    $Menu = FooterMenu::find($menu_id);

                    if (!is_null($Menu)) {

                        $title = [];

                        foreach ($data as $field => $val) {
                            if (strpos($field, 'Title') !== false) {
                                foreach ($val as $lang => $v)
                                    $title[$Languages[$lang]] = $v;
                            }
                        }

                        $Menu->title = json_encode($title);
                        $Menu->save();
                    }
                }
                break;
        }

        return response()->json([
            'status' => 'success'
        ]);

    }

}