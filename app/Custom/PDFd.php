<?php
namespace PDFD;

use App\Document;
use Illuminate\Http\Request;
use \Mpdf\Writer as wt;

use Illuminate\Support\Facades\Auth;

ini_set("pcre.backtrack_limit", "55000000");
class PDFd{
	public $shiterator = 0;
	private $types = array("text", "whiteout", "images", "rectangle", "elipse", "forms","link", "annotate");
	private $changes = array();
	private $file_path = "/var/www/html/pdfeditor/public/pdf.pdf";
	private $output = "/var/www/html/pdfeditor/public/test.pdf";
	private $mpdf = false;
	private $page_scale = 1;
	private $fonts = [];
	
	private $page_template = '<!doctype html>
								<html lang="en">
									<body style="font-size: 10px; padding: 0; margin: 0; font-family: serif;">
										%page_template%
									</body>
								</html>';
	
	public function __construct($changes = array(), $pages_sizes = array(), $file_path=false){
		global $test_time;

		include __DIR__."/../../mpdf_vendor/vendor/autoload.php";
		$font_path  = base_path()."/vendor/mpdf/mpdf/ttfonts/custom";
	
		$this->changes = $changes;
		if($file_path){
			$this->file_path = $file_path;
		}
		$is_blank = Request()->post("start_blank");
		
		$uuid = isset($_POST['UUID'])?$_POST['UUID']:$_POST['uuid'];
		
		$uuid = str_replace(array("/", "~", "."), "-", $uuid);
		$operation_type = "edit";
		if($is_blank){
			$this->file_path = base_path()."/public/blank.pdf";
			$x = Document::create([
				'user_id' => Auth::check() ? Auth::user()->id : NULL,
				'UUID' => $uuid,
				'operation_id'=>$_POST["operation_id"],
				'operation_type'=>"edit",
				'original_document' => $this->file_path,
				'original_name' => "blank.pdf",
				"delete_after"=>(time()+18000),
			]);

			

		}else{
			$operation_id = \Request::post("operation_id");
			$doc = Document::where([
				'UUID'=>$uuid, 
				//'operation_type'=>$operation_type, 
				'operation_id'=>$operation_id,
			])->orderBy('ID', 'desc')->first();
			
			
			if(!$doc){
				exit(json_encode(['success'=>false, "message"=>"Document not found"]));							
			}
			$doc = $doc->toArray();	
			$operation_type = $doc['operation_type'];
			if(!$doc){
				return response()->json(['success'=>false, "message"=>"Operation not found"]);			
			}
			$this->file_path = public_path($doc['original_document']);
//			$this->file_path = base_path()."/public/uploads/pdf/{$uuid}.pdf";
		}
		
		$this->output = base_path()."/public/uploads/pdf/edited_{$uuid}.pdf";
		$this->output = (\App\Http\Controllers\EditPdf::getDestPath($uuid, $operation_type, ".pdf"));;
		

		$tmp_fonts = [];
		$all_files = "";
		if(isset($_POST['fonts'])){
			foreach ($_POST['fonts'] as $f_name => $base64){
				if($base64=='false'){
					continue;
				}
				$file_name = $f_name; //uniqid($f_name);
				$all_files .= "$file_name;;;";
				$path = $font_path.DIRECTORY_SEPARATOR.$file_name;

				
				$file = fopen($path,"w+");
				fwrite($file, file_get_contents($base64));
				fclose($file);

				$conver_font_shell = base_path()."/public/pdf_scripts/convert_fonts.sh $path 2>&1";
				exec($conver_font_shell);


				
				$tmp_fonts[$f_name] = [
					"R"=>ltrim($file_name.".ttf", DIRECTORY_SEPARATOR),
					"B"=>ltrim($file_name.".ttf", DIRECTORY_SEPARATOR)
				];
				
				
				register_shutdown_function(function() use($path, $tmp_fonts) {
					if(is_file($path)){
						//unlink($path);
					}
				});
			}
		}
		


		$post = $_POST;
		

		$defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
		$fontDirs = $defaultConfig['fontDir'];
		$defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
		$fontData = $defaultFontConfig['fontdata'];

		$fonts = array(
			"fontDir"=>array_merge($fontDirs, [$font_path]),
			"fontdata"=>array_merge($fontData,$tmp_fonts, array(
				"ebafont"=>array(
					"R"=>"AmaticSC-Regular.ttf",
				),
				"sans-serif"=>array(
					"R"=>"serif.ttf"
				),
				"arimo"=>array(
					"R"=>"Arimo-Regular.ttf"
				),
				"carlito"=>array(
					"R"=>"Carlito-Regular.ttf",
				),
				"courier"=>array(
					"R"=>"Courier.ttf",
				),

				"courier2"=>array(
					"R"=>"Courier.ttf",
					"B"=>"Courier.ttf",
				),

				"helvetica2"=>array(
					"R"=>"Helvetica.ttf",
					"B"=>"Helvetica-Bold-Font.ttf",
				),

				"dejaVuSans"=>array(
					"R"=>"DejaVuSans.ttf",
				),
				"droidSerif"=>array(
					"R"=>"DroidSerif-Regular.ttf",
				),
				"helvetica"=>array(
					"R"=>"Helvetica.ttf",
				),
				"lato"=>array(
					"R"=>"Lato.ttf",
				),
				"liberationsans"=>array(
					"R"=>"LiberationSans-Regular.ttf",
				),
				"notosans"=>array(
					"R"=>"NotoSans-Regular.ttf",
				),
				"opensans"=>array(
					"R"=>"OpenSans-Regular.ttf",
				),
				"ptserif"=>array(
					"R"=>"ptserif.ttf",
				),
				"roboto"=>array(
					"R"=>"Roboto-Regular.ttf",
				),								
				"timesnewroman"=>array(
					"R"=>"times-new-roman.ttf",
				),								

				"ebgaramond08"=>array(
					"R"=>"EBGaramond-Regular.ttf",
				),	
				
//				"opensanscondlight"=>array(
//					"R"=>"OpenSans-CondLight.otf",
//				),					

//				"oranienbaum"=>array(
//					"R"=>"Oranienbaum.otf",
//				),									
											
				
				
				//EBGaramond08-Regular.otf
				//OpenSans-CondLight.ttf
				//Oranienbaum.ttf


				"Kaiti"=>array(
					"R"=>"Kaiti.ttf",
				),
				"heiti"=>array(
					"R"=>"heiti.ttf",
				),
				"FangSong"=>array(
					"R"=>"FangSong.ttf",
				),
				"SongTi"=>array(
					"R"=>"SongTi.ttf"
				),
				

				"montserrat"=>array(
					"R"=>"Montserrat-Regular.ttf",
				),
				"pacifico"=>array(
					"R"=>"Pacifico.ttf",
				),
				"gamjaflower"=>array(
					"R"=>"GamjaFlower-Regular.ttf",
				),
				"indieflower"=>array(
					"R"=>"IndieFlower.ttf"
				),
				"charmonman"=>array(
					"R"=>"Charmonman-Regular.ttf"
				),
				"gloriahallelujah"=>array(
					"R"=>"GloriaHallelujah.ttf",
				),
				"amaticsc"=>array(
					"R"=>"AmaticSC-Regular.ttf",
				),
				"shadowsintolight"=>array(
					"R"=>"ShadowsIntoLight.ttf",
				),
				"dancingscript"=>array(
					"R"=>"Dancing Script.ttf"
				),
				"dokdo"=>array(
					"R"=>"Dokdo-Regular.ttf",
				),
				"permanentmarker"=>array(
					"R"=>"PermanentMarker.ttf",
				),
				"patrickhand"=>array(
					"R"=>"PatrickHand-Regular.ttf",
				),				
				"courgette"=>array(
					"R"=>"Courgette.ttf"
				)
			))
		);
		
		
		$params = ["margin_left"=>0, "margin_top"=>0, "margin_right"=>0, "margin_header"=>0, "default_font_size"=>10, "open_layer_pane"=>false, 
				"format"=>isset($_POST['format'])?$_POST['format']:"", 
				"useActiveForms"=>true,
//				'mode' => 'c'
				];



				
		switch($operation_type){
		
			case 'translatepdf':
				if(!class_exists("\Mpdf\Mpdf_x")){
					//require_once ("/var/www/html/pdf-magic/vendor/mpdf/mpdf/src/Mpdf_x.php");
				}

				$this->mpdf = new \Mpdf\Mpdf_x(array_merge($params, $fonts));
				$this->mpdf->useSubstitutions = true;
				$this->mpdf->text_input_as_HTML = true;
				$this->mpdf->SetImportUse();
				$this->pagecount = $this->mpdf->SetSourceFile($this->file_path);


				$this->writeAllBlocks();


			
			break;
		
			default: 
			

			
				$this->mpdf = new \Mpdf\Mpdf(array_merge($params, $fonts));
				$this->mpdf->useSubstitutions = true;
				$this->mpdf->text_input_as_HTML = true;
				$this->mpdf->SetImportUse();
				$this->pagecount = $this->mpdf->SetSourceFile($this->file_path);


				$this->writeAllBlocks();






			break;
			case 'watermarkpdf':
				$this->mpdf = new \Mpdf\PDFHeaderFooter(array_merge($params, $fonts));
				$this->mpdf->useSubstitutions = true;
				$this->mpdf->text_input_as_HTML = true;
				//$this->mpdf->SetImportUse();
				$this->pagecount = $this->mpdf->SetSourceFile($this->file_path);

				$this->wattermark($uuid, $operation_id, $doc);
			break;
		}
		
	}


	public function rotatedText($x,$y,$txt,$angle){
		//Text rotated around its origin
		$this->mpdf->Rotate($angle,$x,$y);
		//$this->mpdf->Text($x,$y,$txt);
		$this->mpdf->Write(3.77, $txt);
		$this->mpdf->Rotate(0);
	}
	
	public function rotatedImage($file,$x,$y,$w,$h,$angle){
		//Image rotated around its upper-left corner
		$this->Rotate($angle,$x,$y);
		$this->Image($file,$x,$y,$w,$h);
		$this->Rotate(0);		
	}

	public function wattermark($uuid, $operation_id, $doc=false){
		
		$fill_sign = ((int)Request()->input("fill_sign"));
		
		if($fill_sign){
			$pages = range(1, \Request::post("total_pages"));
			$mpdf =  $this->mpdf;
			$scale = \Mpdf\Mpdf::SCALE;		
			
			$data = \Request::post("data");
			
			

			foreach($pages as $page_num){
				$template_data = $mpdf->importPage((int)$page_num); //, null, null, 0, 0);
				$mpdf->addPage("P", "","","","","","","","","","","","","","","","","","","",[216, 279], false);
				$mpdf->UseTemplate($template_data);			
				$mpdf->useFixedNormalLineHeight = false;
				$mpdf->useFixedTextBaseline = false;
				$mpdf->adjustFontDescLineheight = 2;
				
				
				if(!isset($data[$page_num-1])){
					continue;
				}
				
				foreach($data[$page_num-1] as $item){
					if(isset($item['image_id'])){

						$image_id = $item['image_id'];
						$image = \App\UserImages::where(['id'=>$image_id])->orderBy('ID', 'desc')->first();
						if(!$image){
							exit(json_encode(array("success"=>false, "message"=>"Image not found")));
						}
						$image_path = public_path("uploads/{$image->file_name}");

						$left = $item['left'];
						$top = $item['top'];
						$wy = $left+$item['width']/2;
						$wx = $top+$item['height']/2;
						$mpdf->Rotate($item['rotate']*-1, $wy, $wx);
						$mpdf->SetAlpha($item['opacity']);	
						$this->mpdf->Image($image_path, $left, $top, $item['width'], $item['height']);
					}else{
				

						$fs = $item['font-size-scaled']; ///$watermark['bm'];
						switch($item['font']){
							case 'Helvetica2':
								$font_base_line = 0.75;
								$font_name = "helvetica2";
							break;
							case 'Courier':
								$font_base_line = 0.75;
								$font_name = "courier2";
							break;
							case 'Montserrat':
								$font_base_line = 0.85555;
								$font_name = "Montserrat";
							break;
							default:
								exit("error not standart font ".$item['font']);
							break;
						}
						
						
						$styles = [
							"bold"=>(int)$item['bold'] ?? 0,
							"italic"=>(int)$item['italic'] ?? 0,
							"underline"=>(int)$item['underline'] ?? 0,
						];
						
						$top = ($item['top']+$item['height']*$font_base_line);
						$left = $item['left'];
						$left = $left;
						$wx = $left+($item['width']/2);
						$wy = $top-($item['height']/$scale);
						$this->mpdf->htext2($styles, $item['color'],"cord", $item['text'], $angle=$item['rotate']*-1, 
								strtolower($font_name), $fs, $item['opacity'], $left,  $top, $wx, $wy);
					}
					
				}
				continue;
			}
		
		}else{
		
	
			$watermark = \Request::post("data")['watermark'];
			$pages = range(1, \Request::post("total_pages"));
			$mpdf=  $this->mpdf;
			$scale = \Mpdf\Mpdf::SCALE;

			
			$image_id = isset($_POST['data']['watermark']['image_id'])?$_POST['data']['watermark']['image_id']:false;
			if($image_id){
				$image = \App\UserImages::where(['id'=>$image_id])->orderBy('ID', 'desc')->first();
				if(!$image){
					exit(json_encode(array("success"=>false, "message"=>"Image not found")));
				}
				$image_path = public_path("uploads/{$image->file_name}");
			}

			foreach($pages as $page_num){
				$template_data = $mpdf->importPage((int)$page_num); //, null, null, 0, 0);
				//TODO fix
				$mpdf->addPage("P", "","","","","","","","","","","","","","","","","","","",[216, 279], false);
				
	//			$mpdf->UseTemplate($template_data['tplId'],0 ,0);
				$mpdf->UseTemplate($template_data);			

				$mpdf->useFixedNormalLineHeight = false;
				$mpdf->useFixedTextBaseline = false;
				$mpdf->adjustFontDescLineheight = 2;
				$data = $watermark;

				if($image_id){

					$left = $data['left'];
					$top = $data['top'];
					
					$wy = $left+$data['width']/2;
					$wx = $top+$data['height']/2;
					
					$mpdf->Rotate($data['rotate']*-1, $wy, $wx);
					
					$mpdf->SetAlpha($data['opacity']);	
					//$this->mpdf->Image($image_path, $data['left'], $data['top'], $data['width'], $data['height']);
					$this->mpdf->Image($image_path, $left, $top, $data['width'], $data['height']);
					
					
				}else{
				
					$fs = $data['font-size-scaled']; ///$watermark['bm'];
					switch($data['font']){
					
						case 'Helvetica2':
							$font_base_line = 0.75;
							$font_name = "helvetica2";
						break;
						case 'Courier':
							$font_base_line = 0.75;
							$font_name = "courier2";
						break;
						case 'Montserrat':
							$font_base_line = 0.85555;
							$font_name = "Montserrat";
						break;
						default:
							exit("error not standart font ".$data['font']);
						break;
					}
					
					$top = ($data['top']+$data['height']*$font_base_line);
					
					$left = $data['left'];
					$left = $left;
					
					//wattermark
					
	//				var_dump($data);
	//				exit();
					
					$wx = $left+($data['width']/2);
					$wy = $top-($data['height']/$scale);
					
					
					$x = $this->mpdf->htext($data['color'],"cord", $data['text'], $angle=$data['rotate']*-1, strtolower($font_name), $fs, $data['opacity'], $left,  $top, $wx, $wy);
				}

			}
			

		}


		$operation_type = "watermarkpdf";
		$this->output = $dest_file = (\App\Http\Controllers\EditPdf::getDestPath($uuid, $operation_type, ".pdf"));
		$this->save();
		
		
		$dest_file = \App\Custom\PDFHelpers::replacePages($dest_file);
		
		
		
		
		$new_file_name = \App\Http\Controllers\EditPdf::getNewFileName($doc['original_name'], "watermark", ".pdf");
		Document::where(['id'=>$doc['id']])->update(['edited_document' => $dest_file, 
			"download_name"=>$new_file_name,
			"delete_after"=>(time()+18000)]);
		
		

		
		if(false && isset($_POST['need_edit']) and (int)$_POST['need_edit']){

		}else{
			exit(json_encode(['success'=>true, 'new_file_name'=>$new_file_name, 'url'=>\App\Http\Controllers\EditPdf::getDownloadLink($uuid, strtolower($operation_type))]));		
		}


	}
	
	public function writeAllBlocks(){
		global $test_time;
		set_time_limit(99999);
		$mpdf = $this->mpdf();
		

		$pages = array_filter($_POST['pages'], function($var){
			return ($var !== NULL && $var !== FALSE && $var !== '');	
		});
		

		
		if(empty($pages)){
		
			exit(json_encode(["success"=>false, "message"=>"pages not found"]));
		
			return ;
		}
		
		
		
		$fonts = array();
		if(Request()->post("fonts")){
			$fonts = implode("\n", Request()->post("fonts"));
		}
		
		$deleted_elements = (Request()->post("deleted"));
		$pages_sizes = Request()->post("pages_sizes");
		
		
		
		foreach($pages as $iiit=>$page_num){
		
			$page_template = $this->page_template;
			
			$on_page = "";
			$this->not_replaced_elements = array();

			
			
			if((int)$page_num){
				$this->page_num = $page_num;
				$current_page_size = isset($pages_sizes["page_".($page_num-1)])?$pages_sizes["page_".($page_num-1)]:false;			
			
				$template_data = $mpdf->ImportPage((int)$page_num, null, null, 0, 0, "/CropBox", isset($deleted_elements[$page_num])?$deleted_elements[$page_num]:false);
				
				if($current_page_size and $current_page_size['w']>$current_page_size['h']){
					if(!$template_data['rotated']){
						$mpdf->addPage("P", "","","","","","","","","","","","","","","","","","","",[$current_page_size['w'], $current_page_size['h']], false);					
					}else{
						$mpdf->addPage("L", "","","","","","","","","","","","","","","","","","","",[$current_page_size['h'], $current_page_size['w']], false);
					}
				}else{
					$mpdf->addPage("P", "","","","","","","","","","","","","","","","","","","",[$current_page_size['w'], $current_page_size['h']], false);
				}
				
				$this->not_replaced_elements = $template_data['not_replaced_elements'];

				$mpdf->UseTemplate($template_data['tplId'],0 ,0);
			}else{
				$page_num_n = (int)str_replace("new_page", "", $page_num);
				
				$current_page_size = isset($pages_sizes["page_".($page_num_n-1)])?$pages_sizes["page_".($page_num_n-1)]:false;			
			
				$mpdf->AddPage("P", "","","","","","","","","","","","","","","","","","","",[$current_page_size['w'], $current_page_size['h']], false);
			}
			
			
			if(isset($this->page_num) && isset($deleted_elements[$this->page_num])){
				foreach($deleted_elements[$this->page_num] as $id=>$de){
					
					
					//TODO remove this;
					$this->not_replaced_elements = (array_keys($this->not_replaced_elements));
					
				
					if(in_array($id, $this->not_replaced_elements)){
						$template = "<div style='color: red; position: absolute; height: {$de['height']}mm; width: {$de['width']}mm; left: {$de['left']}mm; top: {$de['top']}mm; background: {$de['background_color']};'>
							<div class='test'></div>
						</div>";
						

						$this->mpdf->WriteFixedPosHTML($template,
						$de['left'], $de['top']-0.25, $de['width'], $de['height']);
					}
				}
			}
			

			foreach($this->types as $et){
				if(isset($this->changes[$et])){
					foreach($this->changes[$et] as $chel){	
						//TODO fix не всегда передается пейдже_нам
						
						
						if(!isset($chel['page_num'])){
							continue;
						}
						
						
						if($chel['page_num']==$page_num){
							$new_size = array();
							
							switch($et){
								case 'whiteout':
									$on_page .= $this->whiteout($chel, array());
								break;
								case 'images':
									$on_page .= $this->image($chel, array());
								break;

								case 'elipse':
									$mpdf->SetAlpha(1);		
									$color = explode(", ", str_replace(["rgba", "rgb","(", ")"], "", $chel['css']['border-color']));
									$bg_color = explode(", ", str_replace(["rgba", "rgb","(", ")"], "", $chel['css']['background-color']));
									$mpdf->SetLineWidth($chel['css']['border-w']);

									$mpdf->SetDrawColor($color[0],$color[1],$color[2]);
									
									
									$mpdf->Ellipse($chel['size']['left']+($chel['size']['width']/2), $chel['size']['top']+($chel['size']['height']/2),
										$chel['size']['width']/2,
										$chel['size']['height']/2,
										"D");
										
									if(isset($bg_color[3])){
										if((int)$bg_color[3]===0){
											$mpdf->SetAlpha(0);								
										}else{
											$mpdf->SetAlpha($bg_color[3]/255);								
										}
									}
										
									//задник элипса
									$mpdf->SetFillColor($bg_color[0],$bg_color[1],$bg_color[2]);
									$mpdf->Ellipse($chel['size']['left']+($chel['size']['width']/2), $chel['size']['top']+($chel['size']['height']/2),
										$chel['size']['width']/2,
										$chel['size']['height']/2,
										"DF");

								break;

								case 'rectangle':
									$mpdf->SetAlpha(1);		
									$color = explode(", ", str_replace(["rgba", "rgb","(", ")"], "", $chel['css']['border-color']));
									$bg_color = explode(", ", str_replace(["rgba", "rgb","(", ")"], "", $chel['css']['background-color']));
									$mpdf->SetLineWidth($chel['css']['border-w']);
									
									$no_border = false;
									if(isset($color[3])){
										if((int)$color[3]===0){
											$no_border = true;
											$mpdf->SetAlpha(0);		
										}else{
											$mpdf->SetAlpha($color[3]/255);		
										}
									}						
									if(!$no_border){
										$mpdf->SetDrawColor($color[0],$color[1],$color[2]);

										$mpdf->Rect($chel['size']['left'], $chel['size']['top'],
											$chel['size']['width'],
											$chel['size']['height'],
											"D");
											
											
									}
									$mpdf->SetAlpha(1);		

									if(isset($bg_color[3])){
										if((int)$bg_color[3]===0){
											$mpdf->SetAlpha(0);								
										}else{
											$mpdf->SetAlpha($bg_color[3]/255);								
										}
									}
									
									$mpdf->SetFillColor($bg_color[0],$bg_color[1],$bg_color[2]);
									$mpdf->Rect($chel['size']['left'], 
										$chel['size']['top'],
										$chel['size']['width'],
										$chel['size']['height'],
										"F"
										);
								break;
								
								
								case 'text':
									$on_page .=  $this->text($chel);
								break;
								case 'forms':
									$on_page .= $this->forms($chel);
								break;
								case 'link':
									if(isset($chel['link']['link']) and !preg_match("/^(http|https):\/\//", $chel['link']['link'], $matches)){
										$chel['link']['link'] = "http://".$chel['link']['link'];
									}else{
									}
									$mpdf->Link($chel['size']['left'],$chel['size']['top'],$chel['size']['width'], $chel['size']['height'], (isset($chel['link']['link'])?$chel['link']['link']:"")); 
								break;
								case 'annotate':
									if(!isset($chel['annotate']['color'])){
										$color = "255 0 0";
									}else{
										$color = explode(",", str_replace(array("rgba(", ")", " "), "", $chel['annotate']['color']));
										$color = ($color[0]." ".$color[1]." ".$color[2]);
										if($color == '0 0 0'){
											$color = "255 0 0";
										}
										
									}
									
									
									foreach($chel['annotate']['blocks'] as $blo){
										
										$mpdf->CustomAnnotation($blo['left'],$blo['top'],$blo['width'], $blo['height'], false, false,
											@$chel['annotate']['content']?($chel['annotate']['content']):" ", 
											@$chel['annotate']['title']?($chel['annotate']['title']):" ", 
											$color,
											$chel['annotate']['type']
										);
									}
								break;
								default:
									echo "unk {$et}";
								break;
							}
						}
					}
				}
			}
			
			$on_page = str_replace("%page_template%", $on_page, $page_template);
		}
		$this->save();

		$uuid = str_replace(array("/", "~", "."), "-", $_POST['UUID']);
		
		\DB::enableQueryLog();
		
		if(isset($_POST['is_blank']) and (int)$_POST['is_blank']==1){
			
		}
		
		$doc =  Document::where("operation_id", $_POST['operation_id'])->orderBy('id', 'desc')->limit(1)->first();;
		if(!$doc){
			exit(json_encode(array("success"=>false,   "message"=>"Session not found") ) );
		}
		
		
		
		if(isset($_COOKIE['test1234'])){
		}
		
		$x=  $doc->update([
			"delete_after"=>(time()+18000),			
			"edited_document" => $this->output, //"/pdf/uploads/edited_{$uuid}.pdf",
		]);
		
		if(isset($_POST['new_download']) and (int)$_POST['new_download'] and isset($_POST['operation_type'])){
			$operation_type = $_POST['operation_type'];
			$download_url = \App\Http\Controllers\EditPdf::getDownloadLink($uuid, strtolower($operation_type));
		}else{
			$download_url = "/pdf/download_edited/{$uuid}";
		}
		
		if(isset($_POST['need_edit']) and (int)$_POST['need_edit']){
			$doc = $doc->toArray();
			$operation_type = $_POST['operation_type'];
			$new_path = str_replace(".pdf", ".pdf", $doc['original_document']);
			$op = $this->output;
			copy($op, public_path($new_path));
			$new_operation_id = guid();
			
			$share_id = guid();
			
			Document::create([
				"UUID"=>$uuid,
				"operation_id"=>$new_operation_id, //time(),
				"share_id"=>$share_id,
				"original_name"=>"translated_".$doc['original_name'],
				"original_document"=>$new_path,
				"operation_type"=>"edit_after_translate",
			]);
			
			exit(json_encode([
				'success'=>true,
				'edit_link'=>"/pdf-editor/$share_id",
				'redirect'=>true,
				'new_operation_id'=>$new_operation_id,
				'url'=>\App\Http\Controllers\EditPdf::getDownloadLink($uuid, strtolower($operation_type))
			]));
		}
		
		$file_name = "edited_".basename($this->file_path);
		if($doc && $doc->original_name && $doc->operation_type=="translatepdf"){
			$file_name = "translated_{$doc->original_name}";
			$x = $doc->update(["download_name"=>$file_name]);
			
		}


		exit(json_encode(array("success"=>true, "url"=>$download_url, "times"=>$test_time, "file_name"=>$file_name, "new_file_name"=>$file_name)));
	}
	
	

	
	public function forms($el){
		$template = "";
		//$el['size']['top'] = $el['size']['top']-0.5; 
		$x = $this->getElSize($el, array());
		
		$x .= "; width: {$el['size']['width']}mm; height: {$el['size']['height']}mm;";
		if(!isset($el['field_params']['field_type'])){
			$el['field_params']['field_type'] = "checkbox";
		}
		$this->shiterator++;
		switch($el['field_params']['field_type']){
			case 'select':
			case 'dropdown':
				$toptions = preg_split('/\n|\r\n?/', $el['field_params']['field_options']);
				$options = "<option value='0'>&#160;</option>";
				foreach($toptions as $kopt=>$opt){
					$selected = "";
					if(isset($el['field_params']['selected'])){
						$selected = $el['field_params']['selected'];
					}
					$options .= "<option ".($opt==$selected?"selected='selected'":"")." multiple='false' value='{$opt}'>{$opt}</option>";
				}
				$ew = $el['size']['width']*1;
				global $select_ew;
				$select_ew = $ew;
				
				
				
				$template = "<select style='width: {$ew}mm;' selected='1' name='test_select{$this->shiterator}'>{$options}</select>";
			break;
			case 'textarea':
				$ew = $el['size']['width']*1;
				if($el['is_new']=='NaN'){
					$ew = (int)$el['size']['width']*1.6;
				}else{
				}
				$ew = (int)$el['size']['width']*1; //.05;
				$height = (int)$el['size']['height']*1.05;
				
				global $text_ew;
				$text_ew = $ew;
				
				
				$template = "<textarea style='width: {$ew}mm; height: {$height}mm;' id='textarea_{$this->shiterator}'  name='textarea_{$this->shiterator}'>&#160;</textarea>";
				
				
				
//				$template = '<textarea name="test_area" rows="4" cols="50">
//  Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
//</textarea>';
				
			break;
			case 'input':
				$value = "";
				
				
				
				if(isset($el['field_params']['value'])){
					$value = $el['field_params']['value'];
				}
				if($el['is_new']=='NaN'){
					$ew = (int)$el['size']['width']*1;
				}else{
					$ew = (int)$el['size']['width']*1;
				}
				global $text_ew;
				$text_ew = $ew;
				
				
				
				$template = "<input style='width: {$ew}mm; height: {$el['size']['height']}mm;'  name='input_{$this->shiterator}' type='text' ".($value).">";
			break;
			case 'checkbox':
				$value = "";
				if(isset($el['field_params']['value'])){
					$value = $el['field_params']['value'];
				}
				$template = "<input style='{$x}'  type='checkbox' name='checkbox_{$this->shiterator}' value='1' $value>";
			break;
			case 'radio':
				$template = "";
				$template = '<input name="test_rad" type="radio"  value="1">';
			break;
			
			default:
			
			break;
		}
		if($el['field_params']['field_type']!='input'){
			//exit($template);
		}
		
		
		
		
		
		$template = "<div style='position: absolute;  $x'>$template</div>";
		
		
		$this->mpdf->WriteFixedPosHTML($template,
				 $el['size']['left'], $el['size']['top'], $el['size']['width']*2, $el['size']['height']);



		return "";
	}
	
	public function text($el){
		

		$x = $this->getElSize($el, array("height", "original-height", "original-width"));
		
		$css = "";
		$template = "";
		
		
		if((int)$el['is_new']==0){
			if( 
				(isset($el['css']['original-height']) and (int)$el['css']['original-height']===0) 
				|| 
				(isset($el['css']['original-width'] ) and (int)$el['css']['original-width'] ===0)
				){
				
			}else{
				$h = @$el['css']['original-height']; ///$this->page_scale;
				$w = @$el['css']['original-width']; ///$this->page_scale;
				$t = @$el['css']['original-top']; ///$this->page_scale;
				$l = @$el['css']['original-left']; ///$this->page_scale;
				if(in_array($el['element_id'], $this->not_replaced_elements)){


					$template .= "<div style='color: red; position: absolute; height: {$h}mm; width: {$w}mm; left: {$l}mm; top: {$t}mm; background: white;'>
						<div class='test'></div>
					</div>";

					$this->mpdf->WriteFixedPosHTML($template,
					$l, $t, $w, $h);


					if(!isset($_COOKIE['wowow'])){
						$template .= "<div style='".$x."; height: {$h}mm; width: {$w}mm; left: {$l}mm; top: {$t}mm; background: white;'></div>";
					}
				}
			}
		}
		
		
		


		$transform = (isset($el['css']['transform']) and $el['css']['transform']) ? "transform:{$el['css']['transform']};" : "";
		$text_rotate = [];
		
		preg_match("/rotate\((.*?)\)/",$transform,$text_rotate);
		$text_rotate = count($text_rotate) > 1 ? "text-rotate:".substr($text_rotate[1],0,-3).";" : "";
		$letter_space = (isset($el['css']['letter-spacing']) and $el['css']['letter-spacing']) ? "letter-spacing:{$el['css']['letter-spacing']};" : "";
		

		
		if(!isset($el['css']['font-family'])){
			$el['css']['font-family']  = "serif";
		}else{		
			$el['css']['font-family'] = str_replace("-fixed", "", $el['css']['font-family']);
		}
		//$el['css']['color'] = "#2d335f";
		if(!isset($el['css']['color'])){
			$color = "#2d335f";
		}
		
		if($text_rotate){
			$template = "
			<div style='position: absolute; top: {$el['size']['top']}mm; left: {$el['size']['left']}mm;'>
			<table style='width: {$el['size']['height']}mm; height: {$el['size']['width']}'>
				<tr>
					<td style='
					word-break: normal; 							
					white-space: nowrap;
					overflow: visible;
					font-size:{$el['css']['font-size']};				
					font-style:{$el['css']['font-style']};
					font-family: {$el['css']['font-family']}, serif;
					color:{$el['css']['color']};
					{$letter_space}
					$text_rotate'>
						{$el['element_content']}
					</td>
				</tr>
			</table>
			</div>";
		}else{
			$template .= "<div 
						{$text_rotate}
						style='
						position: absolute; 
						word-break: normal; 
						white-space: nowrap; 
						".$x.";
						width: 1000em; 
						font-size:{$el['css']['font-size']};
						font-style:{$el['css']['font-style']};
						font-family: {$el['css']['font-family']}, serif;
						{$letter_space}
						color:{$el['css']['color']};
						{$text_rotate}
						'>
						{$el['element_content']}</div>";		
		}
		
		
		
		$el['css']['font-family'] = str_replace('"', "", $el['css']['font-family']);

		if($el['css']['font-weight']==700){
			$el['css']['font-weight'] = "bold";
		}else{
			$el['css']['font-weight'] = "normal";
		}
		
		
		
		//$el['css']['font-size'] = "2mm";
		
		$lh=  "";
		if(isset($el['css']['line-height'])){
			$lh = "line-height: {$el['css']['line-height']}mm;";
		}
		
//		$block_w = $el['size']['width']*1.1;
//		//overflow



//		if(isset($_COOKIE['maintance'])){
//		}
//		$block_w *=1.05;

		if(isset($el['dont_resize'])){
			$block_w = $el['size']['width'];
		}else{
			$block_w = $el['size']['width']*1.1;
			$block_w *=1.05;
		}


		
		$scale = "";
		if(isset($_COOKIE['maintance'])){
//			$el['css']['font-size'] = ((float)$el['css']['font-size']-2)."mm";
			
			if($el['css']['transform']!=NULL){
				$scale = "transform: ".$el['css']['transform'];
			}
			//$scale = "transform: scaleX(0.1); ";
		}
		
		$template2 = "<div style='{$letter_space};
				$scale
				font-family: {$el['css']['font-family']};
				color:{$el['css']['color']}; 
				white-space: nowrap; overflow: hidden;
				overflow: hidden;
				width: {$block_w}mm;
				$lh
				font-size: {$el['css']['font-size']}; 
				font-style: {$el['css']['font-style']}; 
				font-weight: {$el['css']['font-weight']};
				'>{$el['element_content']}</div>";
		
		//0.25 - ебучий бордер
		


		if(isset($el['dont_resize'])){

			$this->mpdf->useFixedNormalLineHeight = true;
			$this->mpdf->useFixedTextBaseline = true;
			$this->mpdf->adjustFontDescLineheight = 5.14;
			$this->mpdf->normalLineheight = 1;
			
			$block_h = $el['size']['height']*1;

			$el['css']['font-size'] = ((float)$el['css']['font-size'])."mm;";

			$font_weight = "";
			if($el['css']['fontw']){
				$font_weight = "font-weight: {$el['css']['fontw']};";
			}
			
			
			$float_fs = ((float)($el['css']['font-size']));
			if($float_fs>5){
				$float_fs -=1;
			}
			$font_size = "font-size: {$float_fs}mm";

			//$el['css']['font-size'] = "5mm";
			$style = "$scale
				$font_weight
				font-family: Arial;
				color:{$el['css']['color']};
				position: absolute; 
				overflow: auto;
				display: block;
				width: {$block_w}mm;
				height: {$block_h}mm;
				$lh
				$font_size";
			
			$template2 = "<div style='$style' >{$el['element_content']}</div>";
			
			$visible_1 = "visible";
		
			$this->mpdf->WriteFixedPosHTML($template2,
				$el['size']['left'], $el['size']['top']-0.25, $el['size']['width'], $el['size']['height'], $visible_1, [], true
				//,[$el['size']['left'], $el['size']['top']-0.25, $el['size']['width']*1.05, $el['size']['height']]
			);
		}else{

			$visible_1 = "hidden";



			$this->mpdf->WriteFixedPosHTML($template2,
				$el['size']['left'], $el['size']['top']-0.25, $el['size']['width']*1.05, $el['size']['height'], $visible_1, [], true
				//,[$el['size']['left'], $el['size']['top']-0.25, $el['size']['width']*1.05, $el['size']['height']]
			);
		}

		


//		$this->mpdf->WriteFixedPosHTML($template2,
//		$el['size']['left'], $el['size']['top']-0.25, $el['size']['width']*1.2, $el['size']['height'], 'visible');
//		
		
		
		return ""; //$template;
	}
	
	public function image($el, $new_size){
		$w = $el['size']['width']; ///$this->page_scale;
		$h = $el['size']['height']; ///$this->page_scale;
		
		$images_path = (public_path())."/uploads/";
		$uuid = str_replace(array("/", "~", "."), "-", $_POST['UUID']);
		$file_path = $images_path.$uuid."-".$el['element_id'].".image";
		
		$element_content = @$el['element_content']!='false'?@$el['element_content']:$file_path;
		if((int)$el['element_content']){
			$image = \App\UserImages::where(array("id"=>$el['element_content'], "UUID"=>$uuid))->first()->toArray();
			if(!$image){
				return "";
			}
			$template = "<div style='
			width: {$w}mm; height: {$el['size']['height']}mm; position: absolute; left: {$el['size']['left']}mm; top: {$el['size']['top']}mm;
			".$this->getElSize($el)."'><img src='{$images_path}{$image['file_name']}'></div>"; //"<img style='width: 100px; height: 100px; ".$this->csspos($new_size)."' src='{$el['element_content']}'>";
			
		}else{
			if(!$element_content){
				return "";
			}
			$template = "<div style='
			width: {$w}mm; height: {$el['size']['height']}mm; position: absolute; left: {$el['size']['left']}mm; top: {$el['size']['top']}mm;
			".$this->getElSize($el)."'><img src='$element_content'></div>"; //"<img style='width: 100px; height: 100px; ".$this->csspos($new_size)."' src='{$el['element_content']}'>";
			
		}


		$this->mpdf->WriteFixedPosHTML($template,
		 $el['size']['left'], $el['size']['top'], $el['size']['width']*2, $el['size']['height']);
		
		return "";
	}


	public function links($el){
		//$css = (urldecode(str_replace('=', ':', http_build_query($el['css'], null, ';'))));
		$elsize = "left: {$el['size']['left']}mm; top: {$el['size']['top']}mm;width: {$el['size']['width']}mm; height: {$el['size']['height']}mm; position: absolute;"; //$this->getElSize($el, array(""));
		$template = "
			<div style='$elsize; background: green;'>
				<a href='google.com'>
					<div style='display: block; background: yelow; width: 100%; height: 100%; color: red;'>
						test span
					</div>
				</a>
			</div>
		";
		return $template;
	}
	

	public function whiteout($el, $new_size, $replace_transparent_bg=true){
		//$css = (urldecode(str_replace('=', ':', http_build_query($el['css'], null, ';'))));



//		$elsize = "left: {$el['size']['left']}mm; top: {$el['size']['top']}mm;width: {$el['size']['width']}mm; height: {$el['size']['height']}mm; position: absolute;"; //$this->getElSize($el, array(""));
//		if($el['css']['background-color']=='rgba(0, 0, 0, 0)' and $replace_transparent_bg){
//			$el['css']['background-color'] = "rgba(255, 255, 255, 1)";
//		}
//		$template = "
//			<div style='$elsize; user-select: none;'>
//				<div class='border' style='".(isset($el['is_elipse'])?"border-radius: 100%;":"")."height: 100%; width: 100%; background-color: {$el['css']['background-color']}; 
//				border: {$el['css']['border']}mm {$el['css']['border-style']} {$el['css']['border-color']}; '></div>
//			</div>
//		";
//		
//		echo "<pre>";
//		var_dump($el);
//		exit();
		


		$this->mpdf->SetAlpha(1);		
		$color = explode(", ", str_replace(["rgba", "rgb","(", ")"], "", $el['css']['border-color']));
		$bg_color = explode(", ", str_replace(["rgba", "rgb","(", ")"], "", $el['css']['background-color']));
		$this->mpdf->SetLineWidth($el['css']['border-w']);
		$this->mpdf->SetDrawColor($color[0],$color[1],$color[2]);

		$this->mpdf->Rect($el['size']['left'], $el['size']['top'],
			$el['size']['width'],
			$el['size']['height'],
			"D");
			
			
			

		if(isset($bg_color[3])){
			if((int)$bg_color[3]===0){
				$this->mpdf->SetAlpha(0);								
			}else{
				$this->mpdf->SetAlpha($bg_color[3]/255);								
			}
		}
		
		$this->mpdf->SetFillColor($bg_color[0],$bg_color[1],$bg_color[2]);
		$this->mpdf->Rect($el['size']['left'], 
			$el['size']['top'],
			$el['size']['width'],
			$el['size']['height'],
			"DF"
			);




		
		
//		$this->mpdf->WriteFixedPosHTML($template,
//		 $el['size']['left'], $el['size']['top'], $el['size']['width']*2, $el['size']['height']);
		return "";
	}
	
	
	public function getBgBorder(){
	
	
	}
	
	public function getSize($size){
		$return = array();
		foreach($size as $k=>$v){
			$return[] = "$k:{$v}mm";
		}
		return implode(";", $return)."; position: absolute;";
	}
	
	//TODO переделать эти костыли
	public function getElSize($el, $ignore_field=array()){
		foreach($el['size'] as $k=>&$v){
			if(in_array($k, $ignore_field) and isset($el['size'][$k])){
				unset($el['size'][$k]);
			}else{
				//$v = ($v/$this->page_scale);
			}
			if(in_array($k, array("border"))){
				$v = $v+"mm";
			}

		}
		$el['size']['left'] += 0;
		
		if(!isset($el['css'])){ //костыль для картинки
			$el['css'] = array();
		}
		
		return "position: absolute; left: {$el['size']['left']}mm; top: {$el['size']['top']}mm; ".$this->array2style($el['css'], $ignore_field);
	
		//return "position: absolute; left: {$el['size']['left']}em; top: {$el['size']['top']}em; width: {$el['size']['width']}em; height: {$el['size']['height']}em;";
	}
	
	public function array2style($styles=array()){
		$return = array(); 
		foreach($styles as $k=>$ec){
			if((int)$ec){
				if($k==='font-weight'){
					$ec = ($ec==700)?"bold":"normal";
				}else if($k=='border'){
					$ec = $ec."mm";
					//$ec = $ec/$this->page_scale;
				}
			}
			$return[] = "$k:".str_replace("\"","", $ec);
		}
		return implode("; ", $return)."; ";
	}

	public function save(){
		$x = $this->mpdf()->Output($this->output, 'F');	
		//shell_exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dDownsampleColorImages=true -dColorImageResolution=150 -dNOPAUSE  -dBATCH -sOutputFile={$this->output}.compressed {$this->output}");
		//rename("{$this->output}.compressed", $this->output);
	}
	
	public function createBlock($p){
		return $block;
	}
	
	private function mpdf(){
		return $this->mpdf;
	}
	

//	private function px2inch($cord=array()){
//		$w = $this->user_page_size['w']/($this->document_page_size['w']);
//		$h = $this->user_page_size['h']/($this->document_page_size['h']);
//		$temp =  array("top"=>floor($cord['top']/$h), "left"=>floor($cord['left']/$h), "width"=>$cord['width']/$w, "height"=>$cord['height']/$h);
////		var_dump($temp);
////		exit();
//		
//		return $temp;
//	}
	
	
	
}

function guid(){
	if (function_exists('com_create_guid') === true){
		return trim(com_create_guid(), '{}');
	}

	return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}
