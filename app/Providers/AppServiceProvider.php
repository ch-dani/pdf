<?php



namespace App\Providers;
require_once(base_path()."/geoip2.phar");
use GeoIp2\Database\Reader;
use App\Guide;
use App\GuideTool;
use App\Language;
use App\Menu;
use App\FooterMenu;
use App\MenuCategory;
use App\Option;
use App\Page;
use App\Blog;
use App\UniqueVisitor;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
    

	//link
		global $lang_code, $aw_languages, $lang_id;
		



		
		
		$aw_languages = [];
		$aw_languages_temp = \App\Language::select("code")->get();
		if($aw_languages_temp){
			foreach($aw_languages_temp as $alt){
				$aw_languages[] = $alt->code;
			}
		}else{
			$aw_languages = ['en'];
		}
		
	
    	$lang = \App\Language::where("code", $request->segment(1))->first();
    	
    	if($lang){
    		$ActiveLanguage = Language::find($lang->id);
    	}else{
    		$ActiveLanguage = Language::find(1);
    	}
    	$lang_code = $ActiveLanguage->code?$ActiveLanguage->code:"en";
		$lang_id = $ActiveLanguage->id;

    	
    	$current_url = url()->current();
   		if(strpos($current_url, "//www.")!==false || strpos($current_url, "/index.php")!==false){
   			$current_url = str_replace("www.", "", $current_url);
   			$current_url = str_replace("/index.php", "", $current_url);
	    	
			header("Location: $current_url",TRUE,301);
	    	
   			exit();
   		}



   
    
        /* Unique Visitors */

        UniqueVisitor::setVisiting($request->ip());
        
	    $current_url = $request->path();
	    if($current_url and $current_url != "/"){
	    	$current_menu = Menu::where("url", $current_url)->get()->first();
	    	if($current_menu){
	    		$current_menu->popularity++;
	    		$current_menu->save();
	    	}
	    }

		if(!isset($_SERVER['REMOTE_ADDR'])){
			$country = "unk";
		}else{
            if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == 'localhost') {
                $country = "unk";
            } else {
                $reader = new Reader(base_path().'/GeoLite2-Country_20190319/GeoLite2-Country.mmdb');
                $record = $reader->country($_SERVER['REMOTE_ADDR']);
                $country = false;
                if($record and $record->country){
                    $country = $record->country->name;
                }
            }
		}


        $MenuCategories = [];
        $Menu = [];
        $MenuFooter = [];
        $MenuBottom = [];

        foreach (MenuCategory::orderBy('sort')->get() as $category){
            $MenuCategories[$category->id] = (object)[
                'title' => $category->title,
                'sort' => $category->sort
            ];
        }
		if($lang_code=='en'){
			$menu_add = "";
		}else{
			$menu_add = "$lang_code/";
		}

        foreach (Menu::where("id", "!=", 0)->orderBy('sort')->get() as $menu) {
            $category_id = is_null($menu->category_id) ? 0 : $menu->category_id;

            $Menu[$category_id][] = (object)[
                'title' => $menu->title,
                'url' => $menu_add.$menu->url,
                'target' => $menu->target,
                'tooltip' => $menu->tooltip,
                'sort' => $menu->sort,
                'new' => $menu->new,
                'id' => $menu->id,
                "pop"=>$menu->popularity,
            ];
        }
        

		$menuConv = [];
		

        foreach (Menu::where('category_id', 99999)->orderBy('sort')->get() as $menu) {
            $menuConv[] = (object)[
                'title' => $menu->title,
                'url' => $menu_add.$menu->url,
                'target' => $menu->target,
                'sort' => $menu->sort,
                'id' => $menu->id,
            ];
        }
		

        foreach (FooterMenu::where('type', 'footer')->orderBy('sort')->get() as $menu) {
            $MenuFooter[] = (object)[
                'title' => $menu->title,
                'url' => $menu_add.$menu->url,
                'target' => $menu->target,
                'sort' => $menu->sort,
                'id' => $menu->id,
            ];
        }

        foreach (FooterMenu::where('type', 'bottom')->orderBy('sort')->get() as $menu) {
            $MenuBottom[] = (object)[
                'title' => $menu->title,
                'url' => $menu_add.$menu->url,
                'target' => $menu->target,
                'sort' => $menu->sort,
                'id' => $menu->id,
            ];
        }


		$popularity = Menu::where("id", "!=", 2)->orderBy('popularity', 'desc')->limit(6)->get();
		$MenuPopular = [];
        foreach ($popularity as $menu) {
            $MenuPopular[] = (object)[
                'title' => $menu->title,
                'url' => $menu_add.$menu->url,
                'target' => $menu->target,
                'sort' => $menu->sort,
                'id' => $menu->id,
                'tooltip' => $menu->tooltip,
            ];
        }


		$detect = new \Mobile_Detect();
		$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
		view()->share('device_is', $deviceType);


		if(isset($_COOKIE['ads'])){
			view()->share('ads', true);
		}else{
			view()->share('ads', false);
		}
		
		
        view()->share('country', $country);
        view()->share('Menu', $Menu);
        view()->share('MenuFooter', $MenuFooter);
        view()->share('MenuBottom', $MenuBottom);
        view()->share('MenuCategories', $MenuCategories);
		view()->share('popular_menu', $MenuPopular);
		view()->share('menuConv', $menuConv);
        /* Loading languages */

        //$ActiveLanguage = (isset($_COOKIE['lang_id'])) ? Language::find($_COOKIE['lang_id']) : Language::find(1);
        if (is_null($ActiveLanguage)){
            $ActiveLanguage = Language::find(1);
		}
		
		

		

        view()->share('ActiveLanguage', $ActiveLanguage);
        view()->share('SiteLanguages', Language::where('status', 'active')->where('id', '!=', $ActiveLanguage->id)->orderBy('id', 'asc')->get());

        $PageHome = Page::where('link', '/')->first();
        $PageHomeBlocks = json_decode($PageHome->blocks, true);
        
       	if(!isset($PageHomeBlocks[$ActiveLanguage->id])){
	        view()->share('PageHomeBlocks', $PageHomeBlocks[1]);
       	}else{
	        view()->share('PageHomeBlocks', $PageHomeBlocks[$ActiveLanguage->id]);
       	}


        
		$link = "/".ltrim($request->path(), "/");
		//exit($link);
        $link = str_replace(array("/$lang_code/"), "", $link);
        $link = !$link?"/":ltrim($link, "/");
        if($link==$lang_code){
        	$link = "/";
        }
        if(!$link){ $link = "/"; }
        if($request->segment(2) and  $request->segment(2)=='blog'){
        	$link = "blog";
        }
        
        

        $Page = Page::where('link', $link)->first();



		if(isset($_GET['testx'])){
			var_dump($Page->seo_keywords);
			exit("testx");
		}        
        
		$current_link  = $link;
        view()->share('current_link', $current_link);
		
        /* global seo */

        $seo_title = json_decode(Option::option('seo_title'), true);
        $seo_keywords = json_decode(Option::option('seo_keywords'), true);
        $seo_description = json_decode(Option::option('seo_description'), true);

        $seo_global = [
            'title' => isset($seo_title[$ActiveLanguage->id]) ? $seo_title[$ActiveLanguage->id] : $seo_title[1],
            'keywords' => isset($seo_keywords[$ActiveLanguage->id]) ? $seo_keywords[$ActiveLanguage->id] : $seo_keywords[1],
            'description' => isset($seo_description[$ActiveLanguage->id]) ? $seo_description[$ActiveLanguage->id] : $seo_description[1]
        ];
        
        

		$blog_items = Blog::where('status', 'active')->take(6)->get();
		view()->share("blog_items", $blog_items);




        
        
        if (!is_null($Page)) {
            view()->share('PageInfo', $Page);

            if ($Page->status != 'publish')
                abort('404');

            /* page seo */

            $seo_title = json_decode($Page->seo_title, true);
            $seo_keywords = json_decode($Page->seo_keywords, true);
            $seo_description = json_decode($Page->seo_description, true);


            $seo_global = [
                'title' => isset($seo_title[$ActiveLanguage->id]) ? $seo_title[$ActiveLanguage->id]
                    : (isset($seo_title[1]) ? $seo_title[1] : $seo_global['title']),
                'keywords' => isset($seo_keywords[$ActiveLanguage->id]) ? $seo_keywords[$ActiveLanguage->id]
                    : (isset($seo_keywords[1]) ? $seo_keywords[1] : $seo_global['keywords']),
                'description' => isset($seo_description[$ActiveLanguage->id]) ? $seo_description[$ActiveLanguage->id]
                    : (isset($seo_description[1]) ? $seo_description[1] : $seo_global['description']),
            ];


            /* Loading blocks */
            




            $PageBlocks = json_decode($Page->blocks, true);


//			echo "<pre>";
//			var_dump($PageBlocks);
//			exit();

            if (count($PageBlocks) > 0) {
                $lang = false;
                if (isset($PageBlocks[$ActiveLanguage->id]) and is_array($PageBlocks[$ActiveLanguage->id]) and count($PageBlocks[$ActiveLanguage->id])) {
                    foreach ($PageBlocks[$ActiveLanguage->id] as $tmp)
                        if ($tmp != NULL)
                            $lang = true;
                }

                $PageBlocks = $lang ? $PageBlocks[$ActiveLanguage->id] : $PageBlocks[1];


                view()->share('PageBlocks', $PageBlocks);
            }

            /* Loading guides */


//title
            if (!is_null($Page->tool)) {
                $PageGuides = GuideTool::where('tool', $Page->tool)->where('guides.status', 'show')->join('guides', 'guides.id', '=', 'guides_tools.guide_id')
                	->select('guides.*')->distinct()->orderBy('guides.sort', 'asc')->orderBy('guides.id', 'asc')->get();
                $PageGuidesSite = [];



                foreach ($PageGuides as $key => $PageGuide) {
                    $PageGuidesSite[$key] = (object)[
                        'title' => $PageGuide->title,
                        'subtitle' => $PageGuide->subtitle,
                        'content' => $PageGuide->content,
                    ];

                    $titles = json_decode($PageGuide->title, true);
                    $subtitles = json_decode($PageGuide->subtitle, true);
                    $contents = json_decode($PageGuide->content, true);


					if(isset($_GET['eee'])){
						echo "<pre>";
						var_dump($titles);
						var_dump($ActiveLanguage);
						exit("eee");
					}



                    $PageGuidesSite[$key]->title = (isset($titles[$ActiveLanguage->id]) and !empty($titles[$ActiveLanguage->id])) ? $titles[$ActiveLanguage->id] : (isset($titles[1]) ? $titles[1] : '');
                    $PageGuidesSite[$key]->subtitle = (isset($subtitles[$ActiveLanguage->id]) and !empty($subtitles[$ActiveLanguage->id])) ? $subtitles[$ActiveLanguage->id] : (isset($subtitles[1]) ? $subtitles[1] : '');
                    $PageGuidesSite[$key]->content = (isset($contents[$ActiveLanguage->id]) and !empty($contents[$ActiveLanguage->id])) ? $contents[$ActiveLanguage->id] : (isset($contents[1]) ? $contents[1] : '');
                }



            	if(isset($_COOKIE['ads'])){
            		echo "<pre>";
            		$splited = preg_split("/<\s*p[^>]*>(.*?)<\s*\/\s*p>|<\s*ul[^>]*>(.*?)<\s*\/\s*ul>/ms", $PageGuidesSite[$key]->content);
            		var_dump($splited);
            		exit("x1");
            	}




                view()->share('PageGuides', $PageGuidesSite);

                view()->share('SeoGlobal', $seo_global);
            } else
                view()->share('PageGuides', []);
        } else {



            view()->share('PageBlocks', []);
            view()->share('PageGuides', []);
        }




        if ($request->is('*blog/*')) {
        
            $id = explode('/', $request->path());
			$id = end($id);

            $Article = Blog::where("url", $id)->first();
//            $Article = Blog::find($id);
            if(!$Article){
				abort(404);
            }
            
            $Page = Page::where('link', 'blog')->first();

            $seo_title = json_decode($Article->seo_title, true);
            $seo_keywords = json_decode($Article->seo_keywords, true);
            $seo_description = json_decode($Article->seo_description, true);

            $page_seo_title = json_decode($Page->seo_title, true);
            $page_seo_keywords = json_decode($Page->seo_keywords, true);
            $page_seo_description = json_decode($Page->seo_description, true);

            $title = $seo_global['title'];

            if (isset($seo_title[$ActiveLanguage->id]) and !empty($seo_title[$ActiveLanguage->id]))
                $title = $seo_title[$ActiveLanguage->id];
            elseif (isset($seo_title[1]) and !empty($seo_title[1]))
                $title = $seo_title[1];
            elseif (isset($page_seo_title[$ActiveLanguage->id]) and !empty($page_seo_title[$ActiveLanguage->id]))
                $title = $page_seo_title[$ActiveLanguage->id];
            elseif (isset($page_seo_title[1]) and !empty($page_seo_title[1]))
                $title = $page_seo_title[1];

            $keywords = $seo_global['keywords'];

            if (isset($seo_keywords[$ActiveLanguage->id]) and !empty($seo_keywords[$ActiveLanguage->id]))
                $keywords = $seo_keywords[$ActiveLanguage->id];
            elseif (isset($seo_keywords[1]) and !empty($seo_keywords[1]))
                $keywords = $seo_keywords[1];
            elseif (isset($page_seo_keywords[$ActiveLanguage->id]) and !empty($page_seo_keywords[$ActiveLanguage->id]))
                $keywords = $page_seo_keywords[$ActiveLanguage->id];
            elseif (isset($page_seo_keywords[1]) and !empty($page_seo_keywords[1]))
                $keywords = $page_seo_keywords[1];

            $description = $seo_global['description'];

            if (isset($seo_description[$ActiveLanguage->id]) and !empty($seo_description[$ActiveLanguage->id]))
                $description = $seo_description[$ActiveLanguage->id];
            elseif (isset($seo_description[1]) and !empty($seo_description[1]))
                $description = $seo_description[1];
            elseif (isset($page_seo_description[$ActiveLanguage->id]) and !empty($page_seo_description[$ActiveLanguage->id]))
                $description = $page_seo_description[$ActiveLanguage->id];
            elseif (isset($page_seo_description[1]) and !empty($page_seo_description[1]))
                $description = $page_seo_description[1];

            $seo_global = [
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
            ];
        }


        view()->share('SeoGlobal', $seo_global);

		$countries = json_decode(file_get_contents(public_path('country-by-abbreviation.json')), true);
		view()->share('Countries', $countries);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
