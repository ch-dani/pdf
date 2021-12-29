<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use Spatie\Async\Pool;
use Spatie\Async\Task;

use Aws\Translate\TranslateClient; 
use Aws\Exception\AwsException;
use Aws\Translate\Exception;
use App\Http\Controllers\Controller;

class TranslatePDF extends Controller{
	
		
	public static function getUserIpAddr(){
		if(isset($_COOKIE['mm'])){
			//return "104.35.19.16";
		}
	
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		    //ip from share internet
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		    //ip pass from proxy
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
		    $ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	
	public static function translateAv($last_trans){
		if(!$last_trans){
			return false;
		}
		$before_block = \App\Option::option('before_block');
		$ip_restriction = \App\Option::option('ip_restriction')*3600;
		
		$ts = strtotime($last_trans->created_at)+$ip_restriction;
		
		if($last_trans && $last_trans->ts+$ip_restriction<=time()){
			return false;
		}
		if($last_trans->trans_count>=$before_block ){
			return ["success"=>false, "time"=>$ts, "message"=>"You cannot translate a document now, try again after<br><span id='c_timer'>0m 00s</span>"];
		}
		return false;
	}
	
	
	public function translate(Request $req){
		pcntl_async_signals(true);
	
		$from = $req->input("from");
		$to = $req->input("to");
		$texts = $req->input("texts");
		$total_chars = (int)$req->input("total_chars");
		$payment_number = (int)$req->input("total_chars");
		
		#$cumulative = (int)$req->input("cumulative");
	
		$ip_restriction = \App\Option::option('ip_restriction')?\App\Option::option('ip_restriction'):1*3600;
		$before_block = \App\Option::option('before_block')?\App\Option::option('before_block'):5;
		$maximum_allowed = \App\Option::option('maximum_allowed')?\App\Option::option('maximum_allowed'):10000;
		$ip = TranslatePDF::getUserIpAddr();
	
		$last = new \App\LastTranslate();
		$last_trans = $last->where(["ip"=>$ip])->first();
		
		
		$paid = false;

		if($last_trans && $last_trans->ts+$ip_restriction<=time()){
			$last_trans->delete();
			$last_trans = false;
		}
			
		if($req->input("charge_id")){
			$last_trans = false;
			$paid = true;
		}else{
			if($total_chars>=$maximum_allowed){
				return response()->json(["success"=>false, "message"=>"Maximum allowed chars: $maximum_allowed"]);
			}
		}
		
		if(!$last_trans){
			\App\LastTranslate::create(["ip"=>$ip,"trans_count"=>1, "chars_count"=>$total_chars, "ts"=>time()]);
		}else{
			
			if(!$payment_number  && $last_trans->trans_count>=$before_block){
				return response()->json(["success"=>false, "message"=>"Maximum times: $before_block"]);
			}
			
			if(!$payment_number  && $last_trans->chars_count+$total_chars>=$maximum_allowed){
				$not_enough = $maximum_allowed-$last_trans->chars_count+$total_chars;
				return response()->json(["success"=>false, "message"=>"Quantity of the maximum allowed symbols: $before_block"]);
			}
//			
//			if($cumulative){
//				$last_trans->update(["trans_count"=>$last_trans->trans_count+1, "chars_count"=>$last_trans->chars_count+$total_chars]);
//			}
		}
		
		//$last->where("ip"=>$);

		$multi = curl_multi_init();
		$channels = array();
		$url = route("translate-pdf-string");
		$token = csrf_token();
		$responses = array();
		
		$this->new_texts = []; // $texts;
		$pool = Pool::create()->concurrency(5)->timeout(5)->sleepTime(50000);
		
		
		$debug_it = $req->input("debug_it");
		
		

		foreach ($texts as $k=>$text) {
		
			$pool[] = async(function() use ($text, $from, $to, $k, $debug_it) {
				$resp = $this->translateString2(["text"=>$text['full_text'], "from"=>$from, "to"=>$to, "key"=>$k], $debug_it);
				$remove_elements = [];
				foreach($text as $k=>$t){
					if($k!=0 && isset($t['element'])){
						$remove_elements[] = $t['element'];
					}
				}
				return ["translated"=>$resp['translated'], 'key'=>$resp['key'], "element"=>$text['element'], "remove_elements"=>$remove_elements];
			})->then(function($output) use($texts, $k) {
				$key = $output["key"];
				
				if(!isset($texts[$key])){
					return;
				}
				
				$this->new_texts[] = [
					"original_text"=>$texts[$key]['full_text'],
					"translated"=>$output['translated'],
					"size"=>$texts[$k]['size'],
					"element"=>$output['element'],
					"remove_elements"=>$output['remove_elements']
				];
			})->catch(function(Exception $e){
				exit($e->getMessage());
				exit("catch");
			});

		}
		$x= await($pool);

		exit(json_encode(["success"=>true, "texts"=>$this->new_texts]));
	}

	public function translateString2($params, $debug_it=false){
		$currentLanguage = $params['from'];
		$targetLanguage= $params['to'];
		$text = $params['text'];

//		if(true){ //$debug_it){
//			return (([
//				"success"=>false,
//				"translated"=>$text,
//				"original"=>"",
//				"error"=>"",
//				"key"=>$params['key']
//			]));
//		}
		


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
			$translated = ($result->get("TranslatedText"));
			return (([
				"success"=>true,
				"error"=>false,
				"translated"=>$translated,
				"original"=>$text,
				"key"=>$params['key']
			]));

		}catch(AwsException $e){
			return (([
				"success"=>false,
				"translated"=>$text,
				"original"=>$text,
				"error"=>$e->getMessage(),
				"key"=>$params['key']
			]));
		}
	}

	
	public function translateString(Request $req){

		$currentLanguage = $req->post('lang_from');
		$targetLanguage= $req->post('lang_to');



		$this->client = new \Aws\Translate\TranslateClient([
			'profile' => 'default',
			'region' => 'us-west-2',
			'version' => '2017-07-01'
		]);
		
		$text = $req->post("text");
		try{
			$result = $this->client->translateText([
				'SourceLanguageCode' => $currentLanguage,
				'TargetLanguageCode' => $targetLanguage, 
				'Text' => $text, 
			]);
			$translated = ($result->get("TranslatedText"));
			exit(json_encode([
				"success"=>true,
				"error"=>false,
				"translated"=>$translated,
				"original"=>$text,
				"key"=>$req->post("key")
			]));

		}catch(AwsException $e){
			exit("herase");
			exit(json_encode([
				"success"=>false,
				"translated"=>$text,
				"original"=>$text,
				"error"=>$e->getMessage(),
				"key"=>$req->post("key")
			]));
		}
	
	}







	public function transBlocks($blocks=array(), $langs){
		$multi = curl_multi_init();
		$channels = array();
		$url = route("translate-block");
		$token = csrf_token();
		$responses = array();
	
		foreach($blocks as $k=>$block){
			$data = array(
				"text"=>$block['text'],
				"key"=>$k,
				"lang_from"=>$langs['from'],
				"lang_to"=>$langs['to']
			);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-CSRF-TOKEN: $token"));
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_multi_add_handle($multi, $ch);
//			print_r($data);
//			print_r($ch);
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
//			var_dump($resp);
			if(isset($resp['key'])){
				$blocks[$resp['key']]['translated'] = $resp['translated'];
			}
			curl_multi_remove_handle($multi, $channel);
		}
		curl_multi_close($multi);
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
		}catch(TranslateException $e){
			exit(json_encode([
				"success"=>false,
				"translated"=>$text,
				"original"=>$text,
				"key"=>$req->post("key")
			]));
		}
		
	}

	
	
}



class MyTask extends Task
{
    public function configure()
    {
        // Setup eg. dependency container, load config,...
    }

    public function run()
    {
    	sleep(2);
        // Do the real work here.
    }
}

