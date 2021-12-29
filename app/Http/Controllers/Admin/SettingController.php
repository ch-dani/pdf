<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Language;
use App\Option;

class SettingController extends Controller
{
    public function seo()
    {
        return view('admin.seo-global', [
            'Languages' => Language::orderBy('id', 'asc')->get(),
            'js'        => [
                asset('js/admin/seo-global.js')
            ]
        ]);
    }

    public function seo_save(Request $request)
    {
        $title = json_encode($request->input('title'));
        $keywords = json_encode($request->input('keywords'));
        $description = json_encode($request->input('description'));
        $ads = json_encode($request->input('ads'));

        Option::option('seo_title', $title);
        Option::option('seo_keywords', $keywords);
        Option::option('seo_description', $description);
        Option::option('seo_ads', $ads);

        return response()->json(['status' => 'success']);
    }

    public function payment()
    {
        return view('admin.payment', [
            'js'        => [
                asset('js/admin/payment.js')
            ]
        ]);
    }

    public function payment_save(Request $request)
    {
        Option::option('stripe_pub', $request->stripe_pub);
        Option::option('stripe_priv', $request->stripe_priv);
        Option::option('paypal_client_id', $request->paypal_client_id);

        return response()->json(['status' => 'success']);
    }

    public function contacts()
    {
        return view('admin.contacts', [
            'js' => [
                asset('js/admin/contacts.js')
            ]
        ]);
    }

    public function contacts_save(Request $request)
    {
        Option::option('contact_location', $request->input('location'));
        Option::option('contact_phone', $request->input('phone'));
        Option::option('contact_email', $request->input('email'));

        return response()->json(['status' => 'success']);
    }
    
    
    public function translatePricing(){
        
        return view('admin.translate', [
        	'ranges'=> \App\TranslatePricing::orderBy('id', 'asc')->get(),
            'js' => [
                asset('js/admin/translate.js')
            ]
        ]);
    }
    
    
    public function translatePricingSave(Request $req){
    	
    	Option::option('aws_pub', $req->post("aws_pub"));
    	Option::option('aws_priv', $req->post("aws_priv"));
    	Option::option('stripe_pub', $req->post("stripe_pub"));
    	Option::option('stripe_priv', $req->post("stripe_priv"));
    	
    	Option::option('translate_price', $req->post("translate_price"));
    	Option::option('translate_count', $req->post("translate_count"));
    	Option::option('free_translate_count', $req->post("free_translate_count"));
    	

    	Option::option('maximum_allowed', $req->post("maximum_allowed"));
    	Option::option('ip_restriction', $req->post("ip_restriction"));    	
    	Option::option('before_block', $req->post("before_block"));     	

    	

		return response()->json(["success"=>true]);
	
    	
    	

    	$prc = new \App\TranslatePricing();
    	$prc = $prc->where("id", ">", 0);
    	$prc->delete();
    	
    	$ranges = $req->post("trans_prices");
    	unset($ranges['%num%']);
    	if(!$ranges){
    		return response()->json(["success"=>true]);
    	}
    	
    	
    	foreach($ranges as $range){
    		\App\TranslatePricing::create([
    			"chars"=>$range['range'],
    			"price"=>$range['price']
    		]);
    	}
    	return response()->json(["success"=>true]);
    }
    

    public function socials()
    {
        return view('admin.socials', [
            'js' => [
                asset('js/admin/socials.js')
            ]
        ]);
    }

    public function socials_save(Request $request)
    {
        Option::option('social_twitter', $request->input('twitter'));
        Option::option('social_facebook', $request->input('facebook'));
        Option::option('social_google', $request->input('google'));

        return response()->json(['status' => 'success']);
    }

    public function sendgrid()
    {
        return view('admin.sendgrid', [
            'js' => [
                asset('js/admin/sendgrid.js')
            ]
        ]);
    }

    public function sendgrid_save(Request $request)
    {
        $path = base_path('.env');

        file_put_contents($path, str_replace(
            'MAIL_USERNAME='.env('MAIL_USERNAME'), 'MAIL_USERNAME='.$request->input('username'), file_get_contents($path)
        ));

        file_put_contents($path, str_replace(
            'MAIL_PASSWORD='.env('MAIL_PASSWORD'), 'MAIL_PASSWORD='.$request->input('password'), file_get_contents($path)
        ));

        file_put_contents($path, str_replace(
            'MAIL_FROM_ADDRESS='.env('MAIL_FROM_ADDRESS'), 'MAIL_FROM_ADDRESS='.$request->input('address'), file_get_contents($path)
        ));

        return response()->json(['status' => 'success']);
    }
}
