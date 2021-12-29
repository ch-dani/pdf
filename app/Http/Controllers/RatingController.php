<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Rating as Rating;


class RatingController extends Controller{

	public function rating(Request $request){
		
//		$x = $this->getRating("pdf-editor");
	
		
		
//		echo $this->updateRating($request);
		
//		var_dump($x);
		exit();
		
		return response()->json(["success"=>true, "message"=>"saved"]);
	}
	
	public function updateRating(Request $req){
		$allow_rate = range(1, 5);
		$url = ltrim($req->post("url"), "/");
		$rate = (int)$req->post("rate");


		if(!in_array($rate, $allow_rate)){
			return response()->json(["success"=>false, "message"=>"Incorrect rate"]);
		}

		if(RatingController::userCanVote($url)){
			Rating::create([
				"ip"=>request()->ip(),
				"url"=>$url,
				"rate"=>$rate
			]);
			return response()->json(["success"=>true, "message"=>false, "rate"=>RatingController::getRating($url)]);
		}else{
			Rating::where(["url"=>$url, "ip"=>request()->ip()])->update(["rate"=>$rate]);
			return response()->json(["success"=>true, "message"=>false, "rate"=>RatingController::getRating($url)]);
		}
	}
	
	public static function getRating($url=false){
		if(!$url){
			$url = request()->path();
		}
		$ratings = Rating::where(["url"=>$url])->get();
		$rating_sum = 0;
		$cnt = 0;
		foreach($ratings as $r){
			$cnt++;
			$rating_sum += $r->rate;
		}
		if($cnt==0 || $rating_sum==0){
			return ["rate"=>0, "count"=>0];
		}
		$rate = number_format($rating_sum/$cnt, 1, '.', "");
		$rate = rtrim(rtrim($rate,'0'),'.');
		
		return ["rate"=>$rate, "count"=>$cnt];
	}
	
	public static function getUserVote(){
		$url = request()->path();
		$rate = Rating::where(["url"=>$url, "ip"=>request()->ip()])->get()->first();
		if(!$rate){
			return false;
		}
		
		return $rate->rate;
	}
	
	
	
	
	public static function userCanVote($url = false){
		if(!$url){
			$url = request()->path();
		}
		$x = Rating::where(['ip'=>request()->ip(), "url"=>$url])->get()->first();
		if(!$x){
			return true;
		}
		return false;
	}
	
}
