<?php


namespace App\Http\Controllers;

use Exception;
use Mpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;


class TestController extends Controller{


	public function test_test_test(){
		$dest = \App\Custom\PDFHelpers::replacePages();


		exit($dest);
	}


}
