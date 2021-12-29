<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use GuzzleHttp\Client;

class Recaptcha implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

	private function httpPost($url, $data){
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response);
	}

    public function passes($attribute, $value){
    
    	$resp = $this->httpPost("https://www.google.com/recaptcha/api/siteverify", [
    		"secret"=>env('RECAPTCHA_SECRET'),
    		'response'=>$value
    	]);
    	
    	
    	if(!$resp->success){
    		return false;
    	}
    	return true;
    }


    public function message()
    {
        return 'Recaptcha error.';
    }
}
