<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StripeController extends Controller{

	public function createCharge(Request $req){
		$input = ($req->input());
		$email = $req->post("email");

		$o_count = \App\Option::option('translate_count');
		$o_price = \App\Option::option('translate_price');
		$free_count = \App\Option::option('free_translate_count');
		
		$user_price = $req->input("price");
		
		$check_price = (($user_price['count'] - $free_count) / $o_count) * $o_price;
		
//		var_dump($user_price);
//		exit($check_price);

//		if((int)$check_price!=(int)$user_price['price']){
//			if(!isset($_COOKIE['maintance'])){
//				return response()->json(["success"=>false, "message"=>"Incorrect price..."]);
//			}
//		}

		if(!isset($input['id'])){
			return response()->json(["success"=>false, "message"=>"token not exist"]);
		}

//		$price = \App\TranslatePricing::find($input['range_id']);
//		
//		var_dump($price);
//		exit();
//		
//		
//		if(!$price){
//			return response()->json(["success"=>false, "message"=>"Wrong price..."]);
//		}
		
		
		$chars = $user_price['count'];
		
		
		list($first, $second) = explode('.', (string)$user_price['price']);
		
		if(!$second){
			$second = "00";
		}
		$price = $first."".$second;
		
		try{
			
			\Stripe\Stripe::setApiKey(\App\Option::option('stripe_priv'));
			$charge = \Stripe\Charge::create(['amount' => $price, 'currency' => 'usd', 'source' => $input['id']]);
			
			\App\Stripe::create([
				'token'=>$input['id'],
				'charge_id'=>$charge->id,
				"amount"=>$charge->amount,
				"data"=>serialize($charge),
				'status'=>'success',
			]);
		
			
			$data= array(
				"id"=>$charge->id,
				"amount"=>($charge->amount/100),
				"time"=>date("Y-m-d H:i", $charge->created),
				"currency"=>$charge->currency,
				"card"=>$charge->payment_method_details->card->last4,
				"site_url"=>\URL::to('/'),
				"payfor"=>"Translate PDF document, $chars chars",
			);


			\Mail::send('emails.payment', $data, function ($message) use($req) {
				$domain = $_SERVER['SERVER_NAME'];
				$title = "Payment complete";
				
				$message->from("no-reply@$domain", 'DeftPDF')->subject($title);
				$message->to(Request()->post("email")); //->cc('1kruler1@gmail.com');
			});
			return response()->json(["success"=>true, "message"=>"payment complete"]);
		}catch(\Exception $e){
				die( json_encode( [
				'success'  => false,
				'message' => $e->getMessage()
				] ) );
		}
		
	
	}	

}
