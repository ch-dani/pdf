<?php

namespace App\Custom;


class PDFHelpers{
	public static function guid(){
		if (function_exists('com_create_guid') === true){
			return trim(com_create_guid(), '{}');
		}

		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}

		
	public static function replacePages($input_file=false, $pages=2){
	
		$pages_count = (int)shell_exec("pdftk ".$input_file." dump_data|grep NumberOfPages| awk '{print $2}'");
		
		$base = base_path("/public/");
		
		if($pages_count<$pages){
			return $input_file;
		}
		
	
		$user = \Auth::user();
		
		
		if($user && $user->hasActivePlan){
			return $input_file;	
		}else{
			$last_page = self::generateLast();	
			$dest = $base."/uploads/pdf/".self::guid().".pdf";
			$shell = 'pdftk A="'.$input_file.'" B="'.$last_page.'" cat A1-'.$pages.' B1 output "'.$dest.'" 2>&1';
			$x = shell_exec($shell);
			return str_replace("//", "/", $dest);
		}
	}

	public static function generateLast(){
		$server = (Request()->server("HTTP_HOST"));

		include __DIR__."/../../mpdf_vendor/vendor/autoload.php";;
		$site_url = "https://$server/";
		$mpdf = new \Mpdf\Mpdf([
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
            'useAdobeCJK' => true,
            'useSubstitutions' => true,
		]);
        $mpdf->AddPageByArray([
                "sheet-size"=>[
                216,
                279], 
                "orientation"=>'P', //$actualsize['orientation']
        ]);
        $mpdf->SetFont('Verdana');
		$mpdf->WriteHTML('
			<div style="font-size: 24px; width: 100%; text-align:center;">
				Thank you for using '.$server.' service!
			</div>
			<div style="width: 100%; text-align:center; margin-top: 10px; color: orange">
				Only two pages are converted. Please Sign Up to convert all pages.
			</div>
			<div style="text-align: center; margin-top: 10px;">
				<a href="'.$site_url.'">'.$site_url.'</a>
			</div>

		');
		
		
		$path = \App\Http\Controllers\EditPdf::getDestPath(0, "last_page", ".pdf");
		$mpdf->Output($path, "F");
		return $path;
	}


}
