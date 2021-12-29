<?php



namespace App\Http\Controllers;

use App\UserImages;
use Illuminate\Http\Request;
use App\NewTranslate;
use App\Document;

function px2mm($px, $scale=1){
    return ($px/$scale)*25.4/72;
}

class TranslateController extends Controller
{


	public function createTranslatePdf(Request $req){

		
		ini_set('memory_limit', '512M');

		include __DIR__."/../../../mpdf_vendor/vendor/autoload.php";
		$pages = $req->input("pages");
		//$pixel_ratio = 0.264583333;
		$pixel_ratio = 0.35277777777778;
		$operation_id = $req->input("operation_id");
		$uuid = $req->input("uuid");
		$pub_path = public_path()."/uploads/pdf/".$req->input("file_name");
		$scale = $req->input("scale");


		$x = Document::create([
			"UUID"=>$uuid,
			"operation_id"=>$operation_id,
			"original_document"=>"f",
			"operation_type"=>"translate_docx",
			"original_name"=>$req->input("file_name"),
			"download_name"=>"translate_".$req->input("file_name"),
			"edited_document"=>$pub_path
		]);

		$download_url = \App\Http\Controllers\EditPdf::getDownloadLink($uuid, "translate_docx", ".pdf");

		$mpdf = new \Mpdf\Mpdf([
			'autoLangToFont' => true,
			'autoScriptToLang' => true,
			'useAdobeCJK' => true,
			'useSubstitutions' => true,
			//'dpi'=>96
			//'backupSubsFont' => ['dejavusanscondensed','arialunicodems','sun-exta'],
			]
		);
		$mpdf->simpleTables = true;
		$mpdf->SetCompression(false);

		$original_file = public_path()."/".$req->input("server_path");
		$pagecount = $mpdf->setSourceFile($original_file);


		foreach($pages as $pn=>$page){
			if($pn>1){
				//continue;
			}

			$mpdf->AddPageByArray([
				"sheet-size"=>[
				px2mm($page['width']),
				px2mm($page['height'])], 
				"orientation"=>'P', //$actualsize['orientation']
			]);			

			$tplId = $mpdf->importPage($pn);
			$actualsize = $mpdf->useTemplate($tplId); //, 0, 0, $page['width'], $page['height']);
			//$mpdf->addPage($actualsize);






			// echo "<pre>";
			// var_dump($actualsize);
			// exit();



			// $orientation = "P"; // ($page['width']>$page['height'])?"L":"P";
			// $mpdf->AddPageByArray([
			// 	"sheet-size"=>[$page['width']/$scale*$pixel_ratio,
			// 	$page['height']/$scale*$pixel_ratio], 
			// 	"orientation"=>$orientation
			// ]);



			// var_dump($actualsize);
			// exit();

			//$mpdf->AddPageByArray(["sheet-size"=>$actualsize]);


			// if(@$page['backgroundImage']){
			// 	$mpdf->Image($page['backgroundImage']['src'], 0, 0, px2mm($page['width']), px2mm($page['height']), 'jpg', '', true, false);
			// }
			$mpdf->SetFont('Verdana');


			foreach($page['objects'] as $obj){
				if($obj['left']>=$page['width']){
					continue;
				}
				if(!isset($obj['type'])){
					$obj['type'] = 'text';
				}
				switch($obj['type']){
					case 'text':
					case 'i-text':
						$color = "black";
						if(@$obj['fill']){
							$color = $obj['fill'];
						}
						$font_weight = "";
						if(@$obj['fontWeight']=='bold'){
							$font_weight = "font-weight: bold;";
						}
						$obj['text'] = htmlspecialchars($obj['text']);





						// $mpdf->SetTextColor(255, 0, 0);
						// $mm = 2.834645669;
						// $mpdf->SetFontSize(($obj['fontSize']));

						// $mpdf->SetXY(px2mm($obj['left'], $scale), px2mm($obj['top'], $scale));

						//$mpdf->Cell(40,200,'Descritpion');

						//$mpdf->Write(8, $obj['text']);


						$fontsize = (px2mm(($obj['fontSize'])+0.5, $scale))."mm";
						
						$mpdf->WriteFixedPosHTML("<div style='
						$font_weight white-space:nowrap; 
						background: white,
						letter-spacing: 0.1;
						line-height: 0,
						width: {$obj['width']}px;
						font-family: Verdana; color: {$color}; font-size: {$fontsize};'>
							{$obj['text']}</div>", 
						px2mm($obj['left'], $scale),
						px2mm($obj['top'], $scale),
						px2mm($obj['width']*5, $scale), 
						px2mm($obj['height'], $scale), 'auto');

						
					break;
				}
			}
			// if($pn>){
			// 	break;
			// 	return false;
			// }
		}
		$mpdf->Output($pub_path,'F');
		return response()->json(["success"=>true, "url"=>$download_url, "file_name"=>"translate_".$req->input("file_name"),]);
	}
	

	public function createTranslatePage(Request $request, $token=false){
		NewTranslate::create([
			"token"=>$token,
			"texts"=>json_encode($request->input("texts")),
			"type"=>$request->input("type")
		]);
		return response()->json(["success"=>true, "token"=>$token]);
	}

	//#googtrans(en|ja)

    public function translate(Request $request, $token=false){
		if($request->input("test_string")!=null){
			return view('new_translate', ["type"=>"lang_detect", "texts"=>explode("|||", $request->input("test_string"))]);
		}else{
			$exist = NewTranslate::where("token", $token)->get()->first();
			if(!$exist){
				abort(404);
			}
		}

		return view('new_translate', ["type"=>$exist->type, "texts"=>($exist && $exist->texts)?json_decode($exist->texts, 1):[]]);
		
    }
}
