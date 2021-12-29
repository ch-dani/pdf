<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Menu;
use App\FooterMenu;
use App\MenuCategory;
use App\Language;
use App\Excel;
use View;

class MenuController extends Controller{

    public function index()
    {
    
    	$menuConv = Menu::orderBy('sort', 'desc')->where('category_id', 99999)->get();
        return view('admin.menu', [
        	'menuConv'=>$menuConv,
            'Languages' => Language::orderBy('id', 'asc')->get(),
            'css'       => [
                asset('admin-ui/bower_components/bootstrap/dist/css/bootstrap-iconpicker.css')
            ],
            'js'        => [
                asset('js/admin/menu-editor.js')
            ]
        ]);
    }

    public function footer_index()
    {
        return view('admin.footer-menu', [
            'Languages' => Language::orderBy('id', 'asc')->get(),
            'css'       => [
                asset('admin-ui/bower_components/bootstrap/dist/css/bootstrap-iconpicker.css')
            ],
            'js'        => [
                asset('js/admin/footer-menu-editor.js')
            ]
        ]);
    }

    public function add(Request $request) {
    
    	
        if ($request->input('type') == 'category')
            $validation = [
                'type' => 'required',
                'title' => 'required',
            ];
        else
            $validation = [
                'type' => 'required',
                'title' => 'required',
                'target' => 'required',
                'tooltip' => 'max:500',
                'url' => 'required|max:255',
                'category' => 'required'
            ];



        $validator = Validator::make($request->all(), $validation);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);


        if ($request->input('type') == 'category') {
            $MenuCategorySort = MenuCategory::select('sort')->orderBy('sort', 'desc')->first();

            if (is_null($MenuCategorySort))
                $sort = 1;
            else
                $sort = $MenuCategorySort->sort + 1;

            $MenuCategory = new MenuCategory;
            $MenuCategory->title = json_encode($request->input('title'));
            $MenuCategory->sort = $sort;
            $MenuCategory->save();

            $data = [
                'category_id' => $MenuCategory->id,
                'sort' => $sort
            ];
        } else {
            $Menu = new Menu;
            
            
	        if($request->input("category")=='footer'){
                $MenuSort = Menu::select('sort')->orderBy('sort', 'desc')->where('category_id', 7)->first();

                if (is_null($MenuSort))
                    $sort = 1;
                else
                    $sort = $MenuSort->sort + 1;
                $category_id = 7;	        
	        }else           
            if($request->input("category")=='conv'){
                $MenuSort = Menu::select('sort')->orderBy('sort', 'desc')->where('category_id', 99999)->first();

                if (is_null($MenuSort))
                    $sort = 1;
                else
                    $sort = $MenuSort->sort + 1;
                $category_id = 99999;

            }else if ($request->input('category') == 'main') {
                $MenuSort = Menu::select('sort')->orderBy('sort', 'desc')->whereNull('category_id')->first();

                if (is_null($MenuSort))
                    $sort = 1;
                else
                    $sort = $MenuSort->sort + 1;

                $category_id = NULL;
            } else {
                $MenuCategory = MenuCategory::select('id')->orderBy('sort', 'desc')->first();

                if (!is_null($MenuCategory))
                    $category_id = $MenuCategory->id;
                else
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You must create a category.'
                    ]);

                $MenuSort = Menu::select('sort')->orderBy('sort', 'desc')->where('category_id', $category_id)->first();

                if (is_null($MenuSort))
                    $sort = 1;
                else
                    $sort = $MenuSort->sort + 1;
            }

            $Menu->title = json_encode($request->input('title'));
            $Menu->url = $request->input('url');
            $Menu->target = $request->input('target');
            $Menu->tooltip = json_encode($request->input('tooltip'));
            $Menu->new = $request->input('new');
            $Menu->category_id = $category_id;
            $Menu->sort = $sort;
            $Menu->save();

            $data = [
                'menu_id' => $Menu->id,
                'sort' => $sort
            ];
        }

        $MenuCategories = [];
        $Menu = [];

        foreach (MenuCategory::orderBy('sort')->get() as $category)
            $MenuCategories[$category->id] = (object) [
                'title' => $category->title,
                'sort'  => $category->sort
            ];

        foreach (Menu::orderBy('sort')->get() as $menu) {
            $category_id = is_null($menu->category_id) ? 0 : $menu->category_id;

            $Menu[$category_id][] = (object) [
                'title' => $menu->title,
                'url'  => $menu->url,
                'target'  => $menu->target,
                'tooltip'  => $menu->tooltip,
                'sort'  => $menu->sort,
                'new'  => $menu->new,
                'id'  => $menu->id,
            ];
        }

        $html = View::make('admin.includes.menu-all', [
            'MenuCategories' => $MenuCategories,
            'Languages' => Language::orderBy('id', 'asc')->get(),
            'Menu' => $Menu,
        ])->render();

        return response()->json([
            'status' => 'success',
            'html' => $html,
            'category_id' => isset($category_id) ? $category_id : NULL,
            'data' => $data
        ]);
    }

    public function footer_add(Request $request) {
        $validation = [
            'type_menu' => 'required',
            'title' => 'required',
            'target' => 'required',
            'url' => 'required|max:255',
        ];

        $validator = Validator::make($request->all(), $validation);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $Menu = new FooterMenu;

        $MenuSort = FooterMenu::select('sort')->orderBy('sort', 'desc')->where('type', $request->input('type_menu'))->first();

        if (is_null($MenuSort))
            $sort = 1;
        else
            $sort = $MenuSort->sort + 1;

        $Menu->title = json_encode($request->input('title'));
        $Menu->url = $request->input('url');
        $Menu->target = $request->input('target');
        $Menu->type = $request->input('type_menu');
        $Menu->sort = $sort;
        $Menu->save();

        $data = [
            'menu_id' => $Menu->id,
            'sort' => $sort
        ];

        $MenuFooter = [];
        $MenuBottom = [];

        foreach (FooterMenu::where('type', 'footer')->orderBy('sort')->get() as $menu) {
            $MenuFooter[] = (object) [
                'title' => $menu->title,
                'url'  => $menu->url,
                'target'  => $menu->target,
                'sort'  => $menu->sort,
                'id'  => $menu->id,
            ];
        }

        foreach (FooterMenu::where('type', 'bottom')->orderBy('sort')->get() as $menu) {
            $MenuBottom[] = (object) [
                'title' => $menu->title,
                'url'  => $menu->url,
                'target'  => $menu->target,
                'sort'  => $menu->sort,
                'id'  => $menu->id,
            ];
        }

        $html = View::make('admin.includes.footer-menu-all', [
            'MenuFooter' => $MenuFooter,
            'MenuBottom' => $MenuBottom,
            'Languages' => Language::orderBy('id', 'asc')->get(),
        ])->render();

        return response()->json([
            'status' => 'success',
            'html' => $html,
            'data' => $data
        ]);
    }

    public function cancel(Request $request) {
        $MenuCategories = [];
        $Menu = [];

        foreach (MenuCategory::orderBy('sort')->get() as $category)
            $MenuCategories[$category->id] = (object) [
                'title' => $category->title,
                'sort'  => $category->sort
            ];

        foreach (Menu::orderBy('sort')->get() as $menu) {
            $category_id = is_null($menu->category_id) ? 0 : $menu->category_id;

            $Menu[$category_id][] = (object) [
                'title' => $menu->title,
                'url'  => $menu->url,
                'target'  => $menu->target,
                'tooltip'  => $menu->tooltip,
                'sort'  => $menu->sort,
                'new'  => $menu->new,
                'id'  => $menu->id,
            ];
        }

        $html = View::make('admin.includes.menu-all', [
            'MenuCategories' => $MenuCategories,
            'Menu' => $Menu,
            'Languages' => Language::orderBy('id', 'asc')->get(),
        ])->render();

        return response()->json([
            'status' => 'success',
            'html' => $html
        ]);
    }

    public function footer_cancel(Request $request) {
        $MenuFooter = [];
        $MenuBottom = [];

        foreach (FooterMenu::where('type', 'footer')->orderBy('sort')->get() as $menu) {
            $MenuFooter[] = (object) [
                'title' => $menu->title,
                'url'  => $menu->url,
                'target'  => $menu->target,
                'sort'  => $menu->sort,
                'id'  => $menu->id,
            ];
        }

        foreach (FooterMenu::where('type', 'bottom')->orderBy('sort')->get() as $menu) {
            $MenuBottom[] = (object) [
                'title' => $menu->title,
                'url'  => $menu->url,
                'target'  => $menu->target,
                'sort'  => $menu->sort,
                'id'  => $menu->id,
            ];
        }

        $html = View::make('admin.includes.footer-menu-all', [
            'MenuFooter' => $MenuFooter,
            'MenuBottom' => $MenuBottom,
            'Languages' => Language::orderBy('id', 'asc')->get(),
        ])->render();

        return response()->json([
            'status' => 'success',
            'html' => $html
        ]);
    }

    public function update(Request $request) {
        if ($request->input('type') == 'category')
            $validation = [
                'id' => 'required',
                'type' => 'required',
                'title' => 'max:255',
            ];
        else
            $validation = [
                'id' => 'required',
                'type' => 'required',
                'title' => 'required|max:255',
                'target' => 'required',
                'url' => 'required|max:255'
            ];

        $validator = Validator::make($request->all(), $validation);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);


        if ($request->input('type') == 'category') {
            $MenuCategory = MenuCategory::find($request->input('id'));

            if (is_null($MenuCategory))
                return response()->json([
                    'status' => 'error',
                    'message' => 'Menu category not found.'
                ]);

            $MenuCategory->title = json_encode($request->input('title'));
            $MenuCategory->save();
        } else {
            $Menu = Menu::find($request->input('id'));

            if (is_null($Menu))
                return response()->json([
                    'status' => 'error',
                    'message' => 'Menu item not found.'
                ]);

            $Menu->title = json_encode($request->input('title'));
            $Menu->url = $request->input('url');
            $Menu->target = $request->input('target');
            $Menu->tooltip = json_encode($request->input('tooltip'));
            $Menu->new = $request->input('new');
            $Menu->save();
        }

        return response()->json(['status' => 'success']);
    }

    public function footer_update(Request $request) {
        $validation = [
            'id' => 'required',
            'title' => 'required|max:255',
            'target' => 'required',
            'url' => 'required|max:255'
        ];

        $validator = Validator::make($request->all(), $validation);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);


        $Menu = FooterMenu::find($request->input('id'));

        if (is_null($Menu))
            return response()->json([
                'status' => 'error',
                'message' => 'Menu item not found.'
            ]);

        $Menu->title = json_encode($request->input('title'));
        $Menu->url = $request->input('url');
        $Menu->target = $request->input('target');
        $Menu->save();

        return response()->json(['status' => 'success']);
    }

    public function save(Request $request) {
        foreach ($request->input('MainMenuSort') as $menu_id => $sort) {
            $Menu = Menu::find($menu_id);
            if (!is_null($Menu)) {
                $Menu->sort = $sort;
                $Menu->save();
            }
        }

        foreach ($request->input('ToolsMenuSort') as $category_id => $arr) {
            $MenuCategory = MenuCategory::find($category_id);
            if (!is_null($MenuCategory)) {
                $MenuCategory->sort = $arr['sort'];
                $MenuCategory->save();

                foreach ($arr['items'] as $menu_id => $sort) {
                    $Menu = Menu::find($menu_id);
                    if (!is_null($Menu)) {
                        $Menu->sort = $sort;
                        $Menu->save();
                    }
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function footer_save(Request $request) {
        foreach ($request->input('FooterMenuSort') as $menu_id => $sort) {
            $Menu = FooterMenu::find($menu_id);
            if (!is_null($Menu)) {
                $Menu->sort = $sort;
                $Menu->save();
            }
        }

        foreach ($request->input('BottomMenuSort') as $menu_id => $sort) {
            $Menu = FooterMenu::find($menu_id);
            if (!is_null($Menu)) {
                $Menu->sort = $sort;
                $Menu->save();
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function remove(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);


        if ($request->input('type') == 'category') {
            $MenuCategory = MenuCategory::find($request->input('id'));

            if (is_null($MenuCategory))
                return response()->json([
                    'status' => 'error',
                    'message' => 'Menu category not found.'
                ]);

            Menu::where('category_id', $MenuCategory->id)->delete();
            $MenuCategory->delete();
        } else {
            $Menu = Menu::find($request->input('id'));

            if (is_null($Menu))
                return response()->json([
                    'status' => 'error',
                    'message' => 'Menu item not found.'
                ]);

            $Menu->delete();
        }

        return response()->json(['status' => 'success']);
    }

    public function footer_remove(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $Menu = FooterMenu::find($request->input('id'));

        if (is_null($Menu))
            return response()->json([
                'status' => 'error',
                'message' => 'Menu item not found.'
            ]);

        $Menu->delete();

        return response()->json(['status' => 'success']);
    }

    public function default_menu(Request $request) {
        Menu::whereNotNull('id')->delete();
        MenuCategory::whereNotNull('id')->delete();

        foreach (DB::table('menu_default')->get() as $menu) {
            $Menu = new Menu;
            $Menu->id = $menu->id;
            $Menu->title = $menu->title;
            $Menu->url = $menu->url;
            $Menu->target = $menu->target;
            $Menu->tooltip = $menu->tooltip;
            $Menu->new = $menu->new;
            $Menu->category_id = $menu->category_id;
            $Menu->sort = $menu->sort;
            $Menu->save();
        }

        foreach (DB::table('menu_category_default')->get() as $category) {
            $MenuCategory = new MenuCategory;
            $MenuCategory->id = $category->id;
            $MenuCategory->title = $category->title;
            $MenuCategory->sort = $category->sort;
            $MenuCategory->save();
        }

        return response()->json(['status' => 'success']);
    }

    public function footer_default_menu(Request $request) {
        FooterMenu::whereNotNull('id')->delete();

        foreach (DB::table('footer_menu_default')->get() as $menu) {
            $Menu = new FooterMenu;
            $Menu->id = $menu->id;
            $Menu->title = $menu->title;
            $Menu->url = $menu->url;
            $Menu->target = $menu->target;
            $Menu->sort = $menu->sort;
            $Menu->type = $menu->type;
            $Menu->save();
        }

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

        foreach (Menu::get() as $key => $Menu) {
            $data[$line]['A'] = $Menu->id;
            $data[$line]['B'] = 'Title';

            foreach (json_decode($Menu->title) as $lang_id => $title) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $data[$line][$char] = $title;
            }

            $line++;
            $data[$line]['B'] = 'Tooltip';

            foreach (json_decode($Menu->tooltip) as $lang_id => $tooltip) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $data[$line][$char] = $tooltip;
            }

            $line++;
        }

        Excel::generate($data, 'menu');
    }

    public function footer_export()
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

        foreach (FooterMenu::get() as $key => $Menu) {
            $data[$line]['A'] = $Menu->id;
            $data[$line]['B'] = 'Title';

            foreach (json_decode($Menu->title) as $lang_id => $title) {
                if (!isset($Languages[intval($lang_id)]))
                    continue;

                $char = $alphabet[array_search($Languages[intval($lang_id)], $headers)];

                $data[$line][$char] = $title;
            }

            $line++;
        }

        Excel::generate($data, 'footer-menu');
    }

}
