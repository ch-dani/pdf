<?php

namespace App\Http\Controllers;

use App\Language;
use App\LanguageConstatns;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TranslatePagesController extends Controller{
    private $languages = array(
        //"en"=>'English',
        "fr"=>"Français",
        "de"=>"Deutsch",
        "da"=>"Dansk",
        "es"=>"Español",
//       "hi"=>"हिंदी",
        "id"=>"Bahasa Indonesia",
        "it"=>"Italiano",
        "ja"=>"日本語",
//        "lt"=>"Lietuvių",
        "no"=>"Norsk",
        "pl"=>"Polski",
        "pt"=>"Português",
        "ru"=>"Русский",
        "sv"=>"Svenska",
        "tr"=>"Türkçe",
        "uk"=>"Українська",
        "vi"=>"Tiếng Việt",
        "zh"=>"中文(简体)",
        "zh-TW"=>"中文(繁體) ",
//        "he"=>"עברית",
//        "ar"=>"العربية"
    );
    
    
    
	public function clean(Request $req, $type=false){
        $langs = \App\Language::get();
        $page_id = "all";

            if((int)$page_id){
                $pages = \App\Guide::where("id", "=", $page_id)->get();
            }else if($page_id=='all'){
                //exit("nea");
                $pages = \App\Guide::get();
                //$pages = \App\Guide::where("id","=", 1)->get();
            }else{
                return response()->json(['success'=>false, "message"=>"no! no! no ! guide"]);
            }

            foreach($pages as $page){
                echo "proccess page {$page->id}<br>\r\n";
                flush();
                ob_flush();

                echo "proccess titles<br>";
                $titles = json_decode($page->title, 1);
                if(array_filter($titles)){
                    $titles = [1=>$titles[1]];
                    $page->title = json_encode($titles);
                }

                echo "proccess subtitles<br>\r\n";
                $subtitle = json_decode($page->subtitle, 1);
                if(array_filter($subtitle)){
                    $subtitle = [1=>$subtitle[1]];
                    $page->subtitle = json_encode($subtitle);
                }

                echo "proccess content<br>\r\n";
                $content = json_decode($page->content, 1);
                if(array_filter($content)){
                    $content = [1=>$content[1]];
                    $page->content = json_encode($content);
                }

                $page->update();
            }




	
	
		exit("time to clean");
	
	}
    public function translatePage(Request $req, $type=false, $page_id=0){
        $lar = [];
        set_time_limit(9999);
        if($type!='const'){
        	//exit("horosh");
        }


        $langs = \App\Language::get();

        $this->languages = [];

        foreach($langs as $l){
            $this->languages[$l->code] = $l->name;
        }

        foreach($this->languages as $code=>$lang){
            $lang_exist = \App\Language::where("code", $code)->get()->first();

            if(!$lang_exist){
                var_dump($lang);
                exit("not exist");

                $t = \App\Language::create([
                    'name'=>$lang,
                    'code'=>$code,
                    'status'=>'active',
                    'flag'=>"/storage/flags/{$lang}.png"
                ]);
                $lar[] = ['id'=>$t->id, 'code'=>$code];
            }else{
                $lar[$lang_exist->id] = ['id'=>$lang_exist->id, 'code'=>$code];
            }
        }

        switch($type){

            case 'faq': //+
                if((int)$page_id){
                    $pages = \App\Faq::where("id", "=", $page_id)->get();
                }else if($page_id=='all'){
                    $pages = \App\Faq::where("id", ">", "0")->get();
                }else{
                    return response()->json(['success'=>false, "message"=>"no! no! no ! blog"]);
                }




                $fields = array("title", "steps");

                foreach($pages as $page){
                    echo "proccess page {$page->id}<br>\r\n";
                    foreach($fields as $f){
                        echo "proccess field $f<br>\r\n";
                        flush();
                        ob_flush();
                        $blocks = json_decode($page->{$f}, 1);

                        if($blocks[1]){
                            $blocks = $this->translateBlocks($blocks, $lar);
                            $page->{$f} = json_encode($blocks);

                            $page->update();
                        }
                    }
                }

//				echo "<pre>";
//				var_dump($blocks);
//				exit("end");
                break;

            case 'const': //+
            
                if($page_id=='all'){
                    $pages = \App\LanguageConstatns::get();
                }else{
                    return response()->json(['success'=>false, "message"=>"no! no! no ! lang const"]);
                }

                $fields = array("translate");

                foreach($pages as $xit=>$page){
                    echo "proccess page {$page->id}<br>\r\n";
                    
                    	
                    foreach($fields as $f){
                        echo "proccess field $f<br>\r\n";
                        flush();
                        ob_flush();
                        $blocks = json_decode($page->{$f}, 1);
                        


                        if($blocks[1]){
                            $blocks = $this->translateBlocks($blocks, $lar);
                            $page->{$f} = json_encode($blocks);
                            
                            $page->update();
                            
                        }
                    }
                    if($xit>1){
                    	//exit("after first");
                    }
                    
                }
                break;

            case 'menu_group': //+
                if((int)$page_id){
                    $pages = \App\MenuCategory::where("id", "=", $page_id)->get();
                }else if($page_id=='all'){
                    $pages = \App\MenuCategory::where("id", ">", "0")->get();
                }else{
                    return response()->json(['success'=>false, "message"=>"no! no! no ! blog"]);
                }

                $fields = array("title");

                foreach($pages as $page){
                    echo "proccess page {$page->id}<br>\r\n";
                    foreach($fields as $f){
                        echo "proccess field $f<br>\r\n";
                        flush();
                        ob_flush();
                        $blocks = json_decode($page->{$f}, 1);

                        if($blocks[1]){
                            $blocks = $this->translateBlocks($blocks, $lar);
                            $page->{$f} = json_encode($blocks);

                            $page->update();
                        }
                    }
                }
                break;

            case 'menu': //+
                if((int)$page_id){
                    $pages = \App\Menu::where("id", "=", $page_id)->get();
                }else if($page_id=='all'){
                    $pages = \App\Menu::where("id", ">", "0")->get();
                }else{
                    return response()->json(['success'=>false, "message"=>"no! no! no ! blog"]);
                }

                $fields = array("title", "tooltip");

                foreach($pages as $page){
                    echo "proccess page {$page->id}<br>\r\n";
                    foreach($fields as $f){
                        echo "proccess field $f<br>\r\n";
                        flush();
                        ob_flush();
                        $blocks = json_decode($page->{$f}, 1);
                        if($blocks[1]){
                        	try{
		                        $blocks = $this->translateBlocks($blocks, $lar, $page, $f);
		                        $page->{$f} = json_encode($blocks);
		                        $page->update();
                        	}catch(\ErrorException $e){
                        		
                        	}
                        }
                    }
                }
                break;

            case 'b1og': //+
                if((int)$page_id){
                    $pages = \App\Blog::where("id", "=", $page_id)->get();
                }else if($page_id=='all'){
                    $pages = \App\Blog::where("id", ">", "0")->get();
                }else{
                    return response()->json(['success'=>false, "message"=>"no! no! no ! blog"]);
                }

                $fields = array("title", "summary", "content", "seo_title", "seo_keywords", "seo_description");

                foreach($pages as $page){
                    echo "proccess page {$page->id}<br>\r\n";
                    foreach($fields as $f){
                        echo "proccess field $f<br>\r\n";
                        flush();
                        ob_flush();
                        $blocks = json_decode($page->{$f}, 1);
                        $blocks = $this->translateBlocks($blocks, $lar);
                        $page->{$f} = json_encode($blocks);
                        $page->update();
                    }
                }
                break;
            case 'page':
                if((int)$page_id){
                    $pages = \App\Page::where("id", "=", $page_id)->get();
                }else if($page_id=='all'){
                    $pages = \App\Page::where("id", ">", "0")->get();
                }else{
                    return response()->json(['success'=>false, "message"=>"no! no! no ! pages"]);
                }


                $fields = array("blocks", "bottom_blocks", "seo_title", "seo_keywords", "seo_description");

                foreach($pages as $page){
                    echo "proccess page {$page->id}<br>\r\n";
                    foreach($fields as $f){
                        echo "proccess field $f<br>\r\n";
                        flush();
                        ob_flush();
                        $blocks = json_decode($page->{$f}, 1);

                        if(isset($blocks[1])){
                            $blocks = $this->translateBlocks($blocks, $lar);
                            
//                            echo "<pre>";
//                            var_dump($blocks);
//                            exit();
                            $page->{$f} = json_encode($blocks);
                            $page->update();
                        }else{
                            echo "block $f not exist<br>\r\n";

                        }
                    }
                }



//				foreach($pages as $page){
//					$blocks = json_decode($page->blocks, 1);
//					echo "proccess page {$page->id}<br>\r\n";
//					flush();
//					ob_flush();
//
//					//TODO uncomment
//					$blocks = $this->translateBlocks($blocks, $lar);
//					$page->blocks = json_encode($blocks);
//					$page->update();
//					//TODO добавить парсинг контента
//				}
                break;
            case 'guides': 
                if((int)$page_id){
                    $pages = \App\Guide::where("id", "=", $page_id)->get();
                }else if($page_id=='all'){
                    //exit("nea");
                    $pages = \App\Guide::get();
                    //$pages = \App\Guide::where("id","=", 1)->get();
                }else{
                    return response()->json(['success'=>false, "message"=>"no! no! no ! guide"]);
                }

                foreach($pages as $page){
                    echo "proccess page {$page->id}<br>\r\n";
                    flush();
                    ob_flush();

                    echo "proccess titles<br>";
                    $titles = json_decode($page->title, 1);
                    if(array_filter($titles)){
                        $titles = $this->translateBlocks($titles, $lar, $page);
                        $page->title = json_encode($titles);
                    }

                    echo "proccess subtitles<br>\r\n";
                    $subtitle = json_decode($page->subtitle, 1);
                    if(array_filter($subtitle)){
                        $subtitle = $this->translateBlocks($subtitle, $lar, $page);
                        $page->subtitle = json_encode($subtitle);
                    }

                    echo "proccess content<br>\r\n";
                    $content = json_decode($page->content, 1);
                    if(array_filter($content)){
                        $content = $this->translateBlocks($content, $lar, $page);
                        $page->content = json_encode($content);
                    }

                    $page->update();
                }

                break;

            default:
                return response()->json(['success'=>false, "message"=>"unk action"]);
                break;
        }


//		echo "<pre>";
//		var_dump($pages);
//		exit();

        exit("the end");

    }




    public function transText($blocks=array(), $langs){
        $multi = curl_multi_init();
        $channels = array();
        $url = route("translate-block");
        $url .= "?tech=1";
//		print_r($url);
        $token = csrf_token();
//		print_r($token);
        $responses = array();

        foreach($blocks as $k=>$block){
            $data = array(
                "text"=>$block,
                "key"=>$k,
                "lang_from"=>$langs['from'],
                "lang_to"=>$langs['to']
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-CSRF-TOKEN: $token"));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_multi_add_handle($multi, $ch);
            $channels[$k] = $ch;
        }
        $active = null;
        do{
            $mrc = curl_multi_exec($multi, $active);
        }while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while($active && $mrc == CURLM_OK){
            if(curl_multi_select($multi) == -1){
                continue;
            }
            do{
                $mrc = curl_multi_exec($multi, $active);
            }while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }


        foreach($channels as $channel){

            $resp = json_decode(curl_multi_getcontent($channel), 1);


            if(isset($resp['key'])){
                $blocks[$resp['key']] = $resp['translated'];
            }
            curl_multi_remove_handle($multi, $channel);
        }
        curl_multi_close($multi);
        return $blocks;
    }

    private function getLangCode($lid=1){
        foreach($this->languages as $k=>$l){

            var_dump($k);
            exit();
            if($lid==$l['id']){
                return $l['code'];
            }
        }
        return "error";
    }


    public function translateBlocks($blocks=array(), $langs=array(), $page_id=0, $field=false){
        $regex = "/>(.*?)</m";
        
        
        
        if(!$blocks){
            return $blocks;
        }

        if(isset($blocks[1]) and !is_array($blocks[1])){
            foreach($blocks as $k=>$block){
                foreach($langs as $lang){
                    if(!isset($blocks[$lang['id']])){
                        $blocks[$lang['id']] = "";
                    }
                }
            }
        }else{
            foreach($blocks as $k=>$block){
                foreach($langs as $lang){
                    if(!isset($blocks[$lang['id']])){
                        if(!$blocks[1]){
                            return;
                        }
                        $blocks[$lang['id']] = array_fill(1, count($blocks[1]), "");
                    }
                }
            }
        }



        if($blocks[1] && !is_array($blocks[1])){
            if(!isset($blocks[$lang['id']])){
                $blocks[$lang['id']] = "";
            }

            foreach($blocks as $lang_id=>$block_text){
            
                //var_dump($lang['id']);
                if($lang_id!= 1){// and !$block_text || $block_text == $blocks[1]){
                    $eng_text = html_entity_decode($blocks[1]);
                    $temp_text = $eng_text;
                    $temp_text = preg_replace("/(<.*?>\s*)+/", "|||||||", $temp_text);
                    $temp_text = array_filter(explode("|||||||", $temp_text));

                    foreach($temp_text as $tk=>$tt){
                        if($tt=='&nbsp;'){
                            unset($temp_text[$tk]); //TODO возможно сбивает порядок, надо проверить
                        }
                    }
                    
                    if(!isset($langs[$lang_id]['code'])){
                    	unset($blocks[$lang_id]);
                    	continue;
                    }
                    
                    try{
	                    $resp = $this->transText($temp_text, ['from'=>'en', 'to'=>$langs[$lang_id]['code']] );
                    }catch(\ErrorException  $e){
                    	echo "exception\r\n<br>";
                    	var_dump($lang_id);
                    	continue;
                    }
//
                    foreach($resp as $key=>$rp){
                        $eng_text = str_replace($temp_text[$key], $resp[$key], $eng_text);
                    }
                    $blocks[$lang_id] = $eng_text; //"no_text";
                }
            }

        }else{

            foreach($blocks as $lang_id=>$block){
                //var_dump($lang['id']);

                if($lang_id==1){

                }else{
                    foreach($block as $bt_id=>$block_text){

                        if(true){//!$block_text || $block_text=='no_text'){

                            $eng_text = $blocks[1][$bt_id];
                            $temp_text = $eng_text;
                            $temp_text = preg_replace("/(<.*?>\s*)+/", "|||||||", $temp_text);
                            $temp_text = array_filter(explode("|||||||", $temp_text));


                            if($temp_text){
                                $resp = $this->transText($temp_text, ['from'=>'en', 'to'=>$langs[$lang_id]['code']]);
                                foreach($resp as $key=>$rp){
                                    $eng_text = str_replace($temp_text[$key],$resp[$key], $eng_text);
                                }

                                $blocks[$lang_id][$bt_id] = $eng_text; //"no_text";
                            }else{
                                echo "empty blocks<br>\r\n";
                                //								var_dump($temp_text);
                                //								exit("error 2");
                                //								$eng_text = $blocks[1][$bt_id];
                                //								$trans = "no_text";
                                //								//$trans = $this->transBlock($eng_text, 'en', $lang['code']);
                                //								$blocks[$lang['id']][$bt_id] = $trans; //"no_text";
                            }
                        }



                    }
                }

            }



        }

        return $blocks;
    }

    public function transBlock($text="",$currentLanguage='en', $targetLanguage = ''){

        $this->client = new \Aws\Translate\TranslateClient([
            'profile' => 'default',
            'region' => 'us-west-2',
            'version' => '2017-07-01'
        ]);
        try{
            $result = $this->client->translateText([
                'SourceLanguageCode' => $currentLanguage,
                'TargetLanguageCode' => $targetLanguage,
                'Text' => $text,
            ]);
            return $translated = ($result->get("TranslatedText"));
        }catch(AwsException $e){
            exit(json_encode([
                "success"=>false,
                "translated"=>$text,
                "original"=>$text,
                "key"=>$req->post("key")
            ]));
        }

    }

    public function translatePhrase(Request $request)
    {
        $lang_id = $request->active_language;

        $text = trim($request->text);

        $new_obj = LanguageConstatns::where("key", $text)->first();;

        if(!$new_obj){
            $new_obj = [];
            foreach(Language::all() as $l){
                if($l->id==1){
                    $new_obj[$l->id] = $text;
                }else{
                    $new_obj[$l->id] = "";
                }
            }

            LanguageConstatns::create([
                "key"=>$text,
                "translate"=>json_encode($new_obj)
            ]);

            $phrase = $text;
        }else{
            $new_obj = json_decode($new_obj['translate'], 1);
            $ret = ($new_obj && isset($new_obj[$lang_id]) )?$new_obj[$lang_id]:$text;
            if(!$ret){
                $phrase = $text;
            } else {
                $phrase = $ret;
            }
        }

        $response = $this->formatResponse('success', null, $phrase);
        return response($response, 200);
    }
}
