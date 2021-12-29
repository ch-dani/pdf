<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use File;
use App\Http\Controllers\Controller;

class Docx2 extends Controller{


	public function fallbackFont($font="sans-serif", $text= ""){
		//TODO remove
		preg_match("/\p{Han}+/u", $text, $matches);
		if($matches){
			return "Microsoft YaHei";
		}
	
		switch($font){
			case 'Helvetica':
			case 'sans-serif':
				return "Helvetica";
			break;
			
			case 'serif':
				return "Times New Roman" ;
			break;
		}
		return $font;
	}
	
	public function proccessFile(Request $req){
		$uuid = $req->post("uuid");
		$operation_type = "pdf2word";
		$original_file = EditPdf::getFilePath($uuid, $operation_type);
		$dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".docx"));
		$operation_id = $req->post("operation_id");
		
		$shell = "";
		$total_pages = 0;
				
		$api = new \CloudConvert\Api("EPPsMEUV6xiAn5kVu4aSGm2SMLnIpBAlL3tHHESCVnlFDDDWfSLSV8166JTsHy7c");


		
		$doc = \App\Document::where([
			'UUID'=>$uuid, 
			'operation_type'=>$operation_type, 
			'operation_id'=>$operation_id,
		])->orderBy('ID', 'desc')->first(); //->toArray();	
		
		if(!$doc){
			return response()->json(['success'=>false, 'message'=>"Operation not found"]);				
		}
		
		$doc = $doc->toArray();
		//$docx_file = $this->test(false, $original_file, $dest_file, $uuid);
		

		$api->convert([
			'inputformat' => 'pdf',
			'outputformat' => 'docx',
			'input' => 'upload',
			'file' => fopen($original_file, 'r'),
		])
		->wait()
		->download($dest_file);
		
		
		\App\Document::where(['id'=>$doc['id']])->update(['edited_document' => $dest_file, "delete_after"=>(time()+18000)]);
		return response()->json(['success'=>true, 'new_file_name'=>EditPdf::getNewFileName($doc['original_name'], "", ".docx"), 'url'=>EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);		
	}
	
	public function proccessFileTest(){
		$original_file = "/var/www/html/npdf/ch.pdf";
		$dest_file = "/var/www/html/npdf/temp/remove_it.docx";
		$uuid = "wow_doge";
		$this->test(true, $original_file, $dest_file, $uuid);
		exit();
	}

	private function fixFont($font=false, $dbg=false){

		if($font =='Times'){
			return "Times New Roman";
		}
		return $font;
	}
	

	public function parText($par=""){
		$r = "";
		foreach($par['texts'] as $t){
			foreach($t['texts'] as $t1){
				$r.=$t1['text']." _______ ";
			}
		}
		return $r;
	}
	
	
	private function fixTops($par=[]){
		$tops = [];
		foreach($par as &$p){
			$tops = array_merge($tops, $p['tops']);
		}
		
		
		return min($tops);
	}

	
	
	private function combinePars($pages=[]){


		foreach($pages as &$pg){
			
			array_multisort(array_column($pg['paragraphs'], 'top'), SORT_ASC, $pg['paragraphs']);
			array_multisort(array_column($pg['paragraphs'], 'left'), SORT_ASC, $pg['paragraphs']);


		
			foreach($pg['paragraphs'] as $k1=>&$par){
				$npg[$k1] = $par;
				
				//left
				
				foreach($pg['paragraphs'] as $k2=>&$par2){
					if(!isset($par2['top'])){
						exit("error 106");
					}
					if($par2 != $par){
					
						if(
							$par != $par2 &&
							(
								($par['top'] >= $par2['top']
								&& $par2['bottom'] <= $par['bottom']
								&& $par['top'] <= $par2['bottom']
								)
								||
								($par2['top'] >= $par['top']
								&& $par2['bottom'] <= $par['bottom']
								&& $par2['top'] <= $par['bottom']
								)
							)
						)
						{
							$merg = array_merge($par['texts'], $par2['texts']);
							$par['top'] = $this->fixTops($merg);
							
							$par['texts'] = $merg;
							
							unset($pg['paragraphs'][$k2]);
						}else{
							//$par_it++;
						}
					}
					
					
				}
			}
			
			
			
//			foreach($pg['paragraphs'] as $p){
//				
//				exit();
//			}
//			
			
		}

		
		$pages = $this->orderPars($pages);
		return $pages;
	}
	
	
	public function getMaxBottom($section = []){
		$bots =[];
		foreach($section['texts'] as $t){
			$bots[] = $t['bottom'];
		}
		
		return max($bots);
	}

	public function getMinTop($section = []){
		$bots =[];
		foreach($section['texts'] as $t){
			$bots[] = $t['top'];
		}
		
		return min($bots)-2;
	}
	
	
	private function orderPars($pages=[]){

		foreach($pages as &$pg){
			//$pg['paragraphs'] = [$pg['paragraphs'][9]];
			foreach($pg['paragraphs'] as $k1=>&$par){
				usort($par['texts'], function ($item1, $item2) {
					return $item1['left'] <=> $item2['left'];
				});
			}
		}
		return $pages;
	}
	
	public function test($std_out=false, $original_file=false, $dest_file=false, $uuid=false){
		$table_border = 2;
		$break_flag = false;
	
		if(!$original_file || !$uuid){
			return false;
		}
		
		$file_path = $original_file;
		
		$bg_paths = base_path()."/public/docx_bg/$uuid";
		$font_paths = base_path()."/public/user_fonts/$uuid";

		if(!is_dir($font_paths)){
			File::makeDirectory($font_paths);
		}
		
		if(!is_dir($bg_paths)){
			File::makeDirectory($bg_paths);
		}
		$bg_files = $bg_paths."/page-%03d.png";
		$dump_file = $bg_paths;
		
		shell_exec("mutool clean -sgd $file_path $file_path.cleaned");
		rename("$file_path.cleaned", $file_path);
//		
//		
//		shell_exec("pdftocairo -pdf $file_path $file_path.cleaned");
//		rename("$file_path.cleaned", $file_path);
		
		$x = shell_exec("node /var/www/html/npdf/index.js $file_path");
		//TODO uncomment
		shell_exec("gs -sDEVICE=pngalpha -dFILTERTEXT -dFILTERIMAGE -o $bg_files -r144 $file_path");
		$xml_file = "$bg_paths/temp.xml";
		shell_exec("pdftohtml -hidden -c -noframes -nomerge -zoom 1 -xml $file_path $xml_file");;
//		exit("pdftohtml -jp2 -xml $file_path $bg_paths/temp.xml");
		shell_exec("gs -sDEVICE=pngalpha -dFILTERTEXT -dFILTERIMAGE -o $bg_files -r144 $file_path");

		$images = [];
		$xml_data = json_decode(json_encode((array)simplexml_load_file($xml_file)),true);

		if(isset($xml_data['page']['@attributes'])){
			$page = $xml_data['page'];
			$pn = 0;
			$images[$pn+1] = []; 
			if(isset($page['image'])){

				if(isset($page['image'][0])){
					foreach($page['image'] as $image){
						$images[$pn][] = @$image['@attributes'];
					}
				}else{
					$image = $page['image'];

					if(isset($image['@attributes'])){
						$images[$pn][] = @$image['@attributes']; //['@attributes'];	
					}else{
						$images[$pn][] = @$image;//['@attributes'];	
					}
				}
			}
		}else{
			foreach($xml_data['page'] as $pn=>$page){
				$images[$pn+1] = []; 
				if(isset($page['image'])){
					

					if(isset($page['image'][0])){
						foreach($page['image'] as $image){
							$images[$pn][] = @$image['@attributes'];
						}
					}else{
						$image = $page['image'];

						if(isset($image['@attributes'])){
							$images[$pn][] = @$image['@attributes']; //['@attributes'];	
						}else{
							$images[$pn][] = @$image;//['@attributes'];	
						}
					}

				}
			}
		}



		function base64_to_file($base64_string, $output_file) {
			$ifp = fopen( $output_file, 'wb' ); 
			$data = explode( ',', $base64_string );
			fwrite( $ifp, base64_decode( $data[ 1 ] ) );
			fclose( $ifp ); 
			return $output_file; 
		}

		$document = json_decode($x, 1);
		extract($document);// = $document;

		$embedFonts = [];
		$allowedFonts = [];

//		echo "<pre>";
//		var_dump($fonts);
//		exit();
		

		if($fonts){
			$font_id = 1;
			foreach($fonts as $k=>$font){
				$fp = $font_paths."/$k";
				$ttf = base64_to_file($font['base64'], "$fp.ttf");
				//TODO fonts $font['nice_name']
				
				$emf = convertODTTF($ttf, $font_paths, $font['nice_name'], $font_id);
				if($emf){
					$allowedFonts= $k;
					$embedFonts[$font_id] = $emf; 
					$font_id++;
				}
			}
		}
		

		

		
		$relTable = $this->generateFontRels($embedFonts);
		$fontTable = $this->generateFontTable($embedFonts);
		
		

		$this->fonts = $fonts;

		\PhpOffice\PhpWord\Settings::setCompatibility(true);
		\PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
		$this->phpWord  = $phpWord = new \PhpOffice\PhpWord\PhpWord();
		$phpWord->setDefaultFontSize(7);
		$phpWord->getCompatibility()->setOoxmlVersion(14);
		
		
		
		//$page_texts = (json_decode($this->json, 1));
		//TODO добавить сортировку по всем страницам
		
		//$document['pages'] = [$document['pages'][0]];
		
		$pg = $document['pages'][0];

		$npg = [];
		$x = 0;
		
		$par_it = 0;


		

//		echo "<pre>";
//		var_dump($pages);
//		exit();		
		$pages = $this->combinePars($pages);
		
		
		
		

		$prev_par = false;
		$prev_key = false;
		$tables = [];

		$phpWord->setDefaultParagraphStyle(
			array(
				//'align'      => 'both',
				'spaceBefore' => 0,
				'size'=>1,
				'keepLines'=>true,
				'spacing'=>1,
				'indentation' => array('left' => 0, 'right' => 0, 'hanging' => 0, 'top'=>0),
				'lineHeight'=>0.0001,
				'spaceAfter'=>0,
			)
		);
		


				
		foreach($pages as $pn=>$page){
			if($pn>=1){
				continue;
			}
			
		
			$page_height = $page['height']; //in pt
			$page_width = $page['width'];

			$prev_bottom = 0;
			$prev_height = 0;
			$prev_right = 0;
			$prev_left = false;
			$spaceBefore = 0;
			$current_x = 0;

			//$current_top = $page_height+20;

			$first_text_on_page = true;
			
			$section = $this->createNewPage($page_width, $page_height+100);
			$page_bg = sprintf($bg_files, $pn+1);


			$header_height = 0;
			
			if(is_file($page_bg)){ //TODO remove false
			
				$header_height = 0;
				$header = $section->createHeader();
				
				//$par = $this->beforeTableSpace(0, "Padding_header_".rand(0,19999999), $header, "header test");
				
				//$page_bg = "/var/www/page-001.png";
				
				$header->addWatermark($page_bg, 
					array(
						'marginTop'=>0, 'marginLeft'=>0,
						'width'=>$page_width,
						'posHorizontal' => 'absolute',
						'posVertical' => 'absolute',	
						'size'=>1			
					)
				);
				
			}
			
			usort($page['paragraphs'], function ($item1, $item2) {
				return $item1['top'] <=> $item2['top'];
			});
			
			$page['paragraphs'] = array_values($page['paragraphs']);
			
//			$page['paragraphs'] = [$page['paragraphs'][3]];
			

			$it = 0;



			foreach($page['paragraphs'] as $ttop=>$pdf_section){
				$it++;
				if($it>4){
				}
				//table
				$parn = "page{$pn}_paragraph".$ttop;
				$left_margin =  $this->getSectionLeft($pdf_section); 
				
				$pdf_section['bottom'] = $pdf_section['bottom']+5;
				
				$section_height = $pdf_section['bottom']-$pdf_section['top'];
				
				
				if($pdf_section['top']>=$page_height-10 || $pdf_section['top']<0){
					continue;
				}
				//table

				$right_offset = ($page_width-$pdf_section['left']-$pdf_section['width'])-10;
				
				$right = \PhpOffice\PhpWord\Shared\Converter::pointToTwip($right_offset);
				$left = \PhpOffice\PhpWord\Shared\Converter::pointToTwip($pdf_section['left']);

				$line_heights = [];
				
				if(true) { //count($pdf_section['texts'])>1){ //колонки
					//continue;
					$rand = rand(100000, 900000);
					//========================
					$fancyTableStyleName = "Table_$ttop";
					$fancyTableStyle = array(
						'spaceBefore' => 0,
						'spaceAfter'=>0,
						//'borderSize' => 0, 
						//'borderColor' => 'ff00ff', 
						'cellMargin' => 0, 
						'unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_PERCENT,
						//'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 
						'layout'      => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
						'cellSpacing' => 0
					);
					
					//========================
					//TODO debug
					if(count($pdf_section['texts'])==2 && $pdf_section['texts'][0]['texts'][0]['text'] == 'similar(Automatic/5Seats,IVAR)'){
						$section_height = $pdf_section['texts'][0]['texts'][0]['height'];
					}
					
					
					$mm = $this->getMinTop($pdf_section, $pdf_section['top']);
					
					
					if($first_text_on_page){
						$spaceBefore = ($pdf_section['top']-$header_height);
					}else{
						$ch = ($pdf_section['top']-$prev_bottom);
						
						$ch = ($mm-$prev_bottom);
						
						$spaceBefore = ($ch);
					}
					
					$current_x += ($section_height+$spaceBefore);

					if($current_x>=$page_height){
						break;
					}
					if($pn>0 && $first_text_on_page){
						//$spaceBefore += 12; //$pdf_section['texts'][0]['height']/2;
					}
					
					$spaceBefore = pt2tw($spaceBefore);

					if($spaceBefore<0){
						$spaceBefore = 0;
						$spaceBefore = 0;
					}
					
					//$this->beforeTableSpace($spaceBefore, "Padding_$parn".rand(0,19999999), $section);


					//$spaceBefore = 0;

					$fontStyle = array('size' => 24);
					$paragraphStyle = array('spacing' => 0, 'size' => 0);
					$phpWord->addFontStyle('fontStyle', array('size' => 9));
					//
					


					$phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle);
					$table = $section->addTable($fancyTableStyleName);


					$table->addRow(0, ['exactHeight'=>true, 'spaceBefore'=>0, 'spaceAfter'=>0]);
					$cell = $table->addCell(pt2tw($left_margin), ['spaceAfter'=>0, 'spaceBefore'=>0]);

					$phpWord->addParagraphStyle(
						"temp_par",
						[
							'spaceBefore' =>0,
							'spaceAfter'=>0,
							'indentation' => array('left' => 0, 'right' => 0, 'hanging' => 0),
							'lineHeight'=>0.0001
						]
					);
					
					$textrun = $cell->createTextRun("temp_par");
					$left = pt2tw($pdf_section['left']);
					
					//addRow
					$create_paragraph = true;
					
					
					$word_right = 0;
					
					$prev_text_top = 0;
					$prev_text_left = 0;
					
					
					
					foreach($pdf_section['texts'] as $text_key=>$ttt){
						//continue; //
						$line_height = 1; $this->getLineHeight($ttt['height'], $ttt['texts'], $pdf_section, true);


						$line_heights[] = $line_height;
						
						if($create_paragraph){
							$phpWord->addParagraphStyle(
								"tablePar_$parn",
								[
									'spaceBefore' => $spaceBefore,
									'spaceAfter'=>0,
//									'shading' => array('fill' => 'dddddd'),
									'indentation' => array('left' => 0, 'right' => 0, 'hanging' => 0),
									//'space' => array('line' => pt2tw($line_height), 'rule' => 'exact'),
									'lineHeight'=>$line_height
								]
							);
						}
						//addCell
						$create_paragraph = false; 
						$prev_left = $left;
						$cell_text = "";
						$ncl = 0; //next left cell

						if(isset($pdf_section['texts'][$text_key+1])){
							$ncl = $pdf_section['texts'][$text_key+1]['left']; 
							//$cell_width = $ncl; //-$ttt['left'];
							$cell_width = $ttt['width']+$ncl-($ttt['left']+$ttt['width']); 
						}else{
							$cell_width = $ttt['width']+25;  //TODO fix
						}
						
						if($cell_width==0 && isset($pdf_section['texts'][$text_key+1]) && $ttt['left'] == $pdf_section['texts'][$text_key+1]['left'] ){
							$cell_width = max($pdf_section['texts'][$text_key+1]['width'], $ttt['width'])+25; 
						}
						//row
						
						$next_line_flag = false;
						//TODO если текущий лефт = предыдущему - добавляем в текущую ячейку с новой строки
						if($prev_text_left and $prev_text_left==$ttt['left']){
							$next_line_flag = true;
						}else{
							$cell = $table->addCell(pt2tw($cell_width), ["bgCol1or"=>"9966CC"]);
							$textrun = $cell->createTextRun("tablePar_$parn");
						}
						
						

						$prev_top = false;
						$prev_left = false;
						
						$line_texts = [];
						$word_right = 0;
						$word_top = 0;
						
						
						foreach($ttt['texts'] as $xx=>$text){

						
							$line_texts[] = $text;
							
							$text['font-size'] = ($text['font-size']);
							
							$cell_text = fixChar(trim($text['text']), $text['font-family'], $fonts);
							$font_name = "Helvetica";
							if(in_array($text['font-family'], ['Helvetica', 'Arial', 'Timew New Roman'])){
								$font_name = $text['font-family'];
							}else{
								$font_name = $fonts[$text['font-family']]['nice_name'];
							}
							//paragraph

							if(isset($fonts[$text['font-family']])){
								$style = ['left'=>0, 'size'=>($text['font-size']), 'color'=>rgb2hex($text['color']), 'name'=>$font_name ]; //TODO font	
								$space_style = ['left'=>0, 'size'=>($text['font-size']), 'color'=>rgb2hex($text['color'])];								
								
							}else{
								$style = [ 'left'=>0, 'size'=>($text['font-size']), 'color'=>rgb2hex($text['color']), 'name'=>$this->fallbackFont($text['fallbackFont'], $text['text']) ];
								$space_style = [ 'left'=>0, 'size'=>($text['font-size']), 'color'=>rgb2hex($text['color']), 'name'=>""];
							}
							
							
							
							
							
							$cell_text = str_replace(chr(194).chr(160), " ", $cell_text);
							
							if($prev_top && $prev_top==$text['top'] && $prev_left!= $text['left']){
								$textrun->addText(" ", $space_style);
							}
							

							$exploded = explode(" ", $cell_text);
//line

//							"779 50TH ST FL 3, Brooklyn NY 11220-2222";
//							echo "<pre>";
//							var_dump($page);
//							exit();


							if($next_line_flag){
//								echo "<pre>";
//								var_dump($prev_text_left);
//								var_dump($ttt['left']);

//								var_dump($ttt['texts']);
//								exit("ep");
								$textrun->addTextBreak(1);
							}
							if($this->isNewLine($line_texts)){ //$prev_top !==false && (int)$text['top'] != (int)$prev_top ){ //TODO перенос строк в таблице
								$textrun->addTextBreak(1);
								//$textrun->addText("<w:br/>");
								foreach($exploded as $ex){
									$ex = trim($ex);
									$textrun->addText(("$ex"), $style);
									if(end($exploded)!= $ex){
										$textrun->addText(" ", $space_style);
									}
								}
							}else{ //добавление в текущую строку
								foreach($exploded as $ex){
									$ex = trim($ex);
									$textrun->addText(("$ex"), $style);
									if(end($exploded)!= $ex){
										$textrun->addText(" ", $space_style);
									}
								}
							}
							
							$prev_top = $text['top'];
							$prev_left = $text['left'];
							
						}
						$create_paragraph = true;
						
						$prev_right = $ttt['left']+$ttt['width'];
						$prev_text_top = $ttt['top'];
						$prev_text_left = $ttt['left'];
						
					}
					
					$prev_bottom = $pdf_section['bottom']; //-max($line_heights);
					
					$prev_bottom = $this->getMaxBottom($pdf_section);
					
					$first_text_on_page = false;


					//$table->addCell(pt2tw($table_margins/2), $fancyTableCellStyle);
				}else{
					$section_height = $pdf_section['bottom']-$pdf_section['top'];


					foreach($pdf_section['texts'] as $ttt){
						$rand = rand(100000, 900000);
						$parn = md5(uniqid($rand, true));

						$line_height = $this->getLineHeight($section_height, $ttt['texts']);

						
						$line_heights[] = $line_height;
						
						$prec = ($ttt['texts'][0]['height']);
						
						$prec = 0;
						if($first_text_on_page){
							$offset = $pdf_section['top']-$prec-$header_height; //(int)($page_height-$ttt['tops'][0]);
						}else{
							$offset = $pdf_section['top'];
						}
						
						$bottom = $pdf_section['bottom']; //(int)($page_height-$ttt['tops'][0]+$ttt['heights'][0]);
						$left = pt2tw($pdf_section['left']);

						if($prev_bottom){
							$spaceBefore = pt2tw($offset-$prev_bottom) ;//-8.982595813919);
						}else{
							$spaceBefore = pt2tw($offset);
						}

						if($first_text_on_page){
							$spaceBefore = ($pdf_section['top']); //-2-$header_height);
						}else{
							
							$ch = ($pdf_section['top']-$prev_bottom);
							$spaceBefore = ($ch);
						}
						$spaceBefore = pt2tw($spaceBefore);


												
						if($spaceBefore<0){
							$spaceBefore = 1;
							exit("<0");
						}
						
						
						$phpWord->addParagraphStyle(
							'pStyleSB'.$parn,
							[
								'spaceBefore' => $spaceBefore,
								'spaceAfter'=>0,
								
								'space' => array('line' => pt2tw($line_height), 'rule' => 'exact'),
								
								//'indentation' => array('left' => $left, 'right' => $right, 'hanging' => 0),
								
								'indentation' => array('left' => $left, 'right' => $right, 'hanging' => 0),
//								'lineHeight'=>$line_height
							]
						);

						$textrun = $section->createTextRun('pStyleSB'.$parn);
						foreach($ttt['texts'] as $text){
						
							$text['font-size'] = ($text['font-size']);
						
							if(isset($fonts[$text['font-family']])){
								$style = [ 
								'bold'=>$fonts[$text['font-family']]['bold'],
								'italic'=>$fonts[$text['font-family']]['italic'],
								'left'=>0, 'size'=>($text['font-size']), 'color'=>rgb2hex($text['color']), 'name'=>$this->fixFont(explode("-",$fonts[$text['font-family']]['nice_name'])[0]) ];									
								
							}else{
								$style = [ 'left'=>0, 'size'=>($text['font-size']), 'color'=>rgb2hex($text['color']), 'name'=>$this->fallbackFont($text['fallbackFont'], $text['text']) ];
							}
						
							$text['text'] = trim($text['text']);
							
							$textrun->addText("".htmlspecialchars($text['text'])."", $style);
						}
						$prev_bottom = $bottom;
					}
					
					$first_text_on_page = false;
					$prev_bottom = $pdf_section['bottom']; //+max($line_heights);
					$prev_top = $pdf_section['top'];
					//break;
					
				}

				$first_text_on_page = false;
			}

			//exit("end");


			if(isset($images[$pn])){
				foreach($images[$pn] as $img){
//					echo "
//					<br>============<br>
//					<pre>";
//					var_dump($page_height);
//					var_dump($img['left']);
//					var_dump($img['top']);

					$textrun = $section->createTextRun("temp_par");

					$textrun->addImage(
						$img['src'],
						array(
							'width'            =>  round($img['width']), //round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(10)),
							'height'           => round($img['height']),//round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(10)),
							'positioning'      => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
							'wrappingStyle'=>"inline",
							'posHorizontal' => 'absolute',
							'posVertical' => 'absolute',

							'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
							'posVerticalRel'   => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
							'marginLeft'       => round($img['left']),//round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(15.5)),
							'marginTop'        => round($img['top'])//round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(1.55)),
						)
					);			

				}
			}
			//exit("the end");

		}


		$dest_file = $dest_file."";
		

		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$objWriter->save($dest_file);
		
//		echo "<pre>";
//		var_dump($embedFonts);
//		exit();
		
		$zip = new \ZipArchive;
		if ($zip->open($dest_file) === TRUE) {
			$zip->addEmptyDir('word/fonts');
//			
			foreach($embedFonts as $ef){
				$zip->addFile($ef['file_path'], "word/fonts/{$ef['file_name']}");
			}
			
			$content_types = $zip->getFromName("[Content_Types].xml");

			$sxe = new \SimpleXMLElement($content_types);
			
			//<Default Extension="odttf" ContentType="application/vnd.openxmlformats-officedocument.obfuscatedFont" />
			
			$ff = $sxe->addChild('Default'); 
			$ff->addAttribute("Extension", "odttf");
			$ff->addAttribute("ContentType", "application/vnd.openxmlformats-officedocument.obfuscatedFont");
			$content_types = $sxe->asXML();
			
			
			$zip->addFromString("[Content_Types].xml", $content_types); //getContentTypes());
			$zip->addFromString("word/_rels/fontTable.xml.rels", $relTable);
			
			$zip->addFromString('word/fontTable.xml', $fontTable);
			$zip->close();
		}else{
			exit("error");
		}
		
		
		
		//File::deleteDirectory($bg_paths);
		//exit("here");
		
		if($std_out){
			header("Content-type: application/vnd.ms-word");
			header("Content-Disposition: attachment;Filename=test_file.docx");
			echo file_get_contents($dest_file);
			exit();
		}else{
			return $dest_file;
		}
	}
	
	public $flag = false;
	
	private function getLineHeight($section_height=0, $texts=[], $section=false, $debug=false){
		$line_heights = 0;
		$prev_top = false;
		$prev_left = false;
		
		$total_lines = 0;
		
		$first = true;
		$last_height = 1;
		$tops = [];
		
		
		foreach($texts as $t){
			$tops[] = $t['otop'];

			if($prev_top===false || (int)$prev_top!=(int)$t['top']){
				$total_lines++;
				if($debug){
					$line_heights += ($t['font-size']);
				}else{
					$line_heights += ($t['font-size']); //+$t['textAdvanceScale']);
				}
			}
			
			$last_height = $t['font-size'];
			$prev_top = (int)$t['top'];
			$prev_left = (int)$t['left'];
			$first = false;
		}
		
		
		$h = $section_height-$last_height;
		
		
		if($debug){
			
		}
		
		if(!$section_height || !$total_lines || !$t['font-size']){
			return 1;
//			var_dump($section_height, $total_lines, $t['font-size']);
//			exit();
		}
		
		$tmp = ($section_height/($total_lines)/($t['font-size']));;

		return $tmp;
		
		$new_temp = ((max($tops)-min($tops))/$total_lines)/$t['textAdvanceScale'];
		return $new_temp;
		
	}
	
	private function getSectionLeft($pdf_section){
		$lefts = [];
		foreach($pdf_section['texts'] as $text_key=>$ttt){
			$lefts[] = $ttt['left'];
		}
		return min($lefts);
	}
	
	
	private function isNewLine($texts=[]){
		if(count($texts)==1){ return false; }
		$current = array_pop($texts);
		$ct = (int)$current['top']; //current top
		$cb = (int)($current['top']+$current['height']); //current bottom
		
		
		$flag = false;
		foreach($texts as $t){
			$tt = (int)$t['top']; //text top
			$tb = (int)($t['top']+$t['height']); //text bottom

			
			
			if($tt==$ct || $tb==$cb ){
				$flag = false;
			}else{
				$flag = true;
			}
		}
		return $flag;
	}
	
	
	private function beforeTableSpace($spaceBefore=0, $par_name="Paragraph_1", $section=false, $x=" "){
		$spaceBefore = $spaceBefore>0?$spaceBefore:1;
		$this->phpWord->addParagraphStyle(
			$par_name,
			[
				///'shading' => array('fill' => 'ff0000'),
				'spaceBefore' =>$spaceBefore,
				'spaceAfter'=>0,
				'lineHeight'=>0.00001
			]
		);


		
		$textrun = $section->createTextRun($par_name);

		$style = [ 'left'=>0, 'size'=>1];
		$textrun->addText(($x), $style);
		
		return $textrun;


		//return $section;
	}
	
	private function createNewPage($page_width=100, $page_height=100){
		$section = $this->phpWord->addSection(
			[
				'pageSizeW' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip($page_width),
				'pageSizeH' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip($page_height),
				'marginLeft'   => 0,
				'marginRight'  => 0, //\PhpOffice\PhpWord\Shared\Converter::pointToTwip(27.016216216),
				'marginTop'    => 0,
				'marginBottom' => 0,
				'headerHeight' => 0, //\PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.0001),
				'footerHeight' => 0,
				'shading' => array('fill' => 'ddffdd'),
			]
		);
		return $section;
	}
	
	
	private function generateFontTable($fonts=[]){

		$body = "";

		foreach($fonts as $f){
			$body .= $f['xml'];
		}
		
		$body = '<?xml version="1.0" encoding="UTF-8"?>
<w:fonts xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">	<w:font w:name="Times New Roman">
		<w:charset w:val="00" />
		<w:family w:val="roman" />
		<w:pitch w:val="variable" />
	</w:font>
	<w:font w:name="Symbol">
		<w:charset w:val="02" />
		<w:family w:val="roman" />
		<w:pitch w:val="variable" />
	</w:font>
	<w:font w:name="Arial">
		<w:charset w:val="00" />
		<w:family w:val="swiss" />
		<w:pitch w:val="variable" />
	</w:font>
	<w:font w:name="Liberation Serif">
		<w:altName w:val="Times New Roman" />
		<w:charset w:val="01" />
		<w:family w:val="roman" />
		<w:pitch w:val="variable" />
	</w:font>
	<w:font w:name="Liberation Sans">
		<w:altName w:val="Arial" />
		<w:charset w:val="01" />
		<w:family w:val="swiss" />
		<w:pitch w:val="variable" />
	</w:font>
	<w:font w:name="Arial">
		<w:charset w:val="01" />
		<w:family w:val="swiss" />
		<w:pitch w:val="variable" />
	</w:font>'.$body.'</w:fonts>';

		return $body;

	}	
	
	private function generateFontRels($fonts=[]){
		
		$body = "";
		foreach($fonts as $f){
			$body .= "<Relationship Id=\"rId{$f['font_id']}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/font\" Target=\"fonts/{$f['file_name']}\" />";
		}
		return '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.$body.'</Relationships>';
	}
	
	
	
}

function fixChar($text="", $ff="", $fonts=[]){
	if($text==''){
		return "02-2014";
	}
	return $text;
}


function tw2pt($tw=0){
	return $tw*0.05;
}

function pt2tw($pt=0){
	return \PhpOffice\PhpWord\Shared\Converter::pointToTwip($pt);
}


function rgb2hex($rgb, $prefix = ''){
	if(!is_array($rgb)){
		$rgb = str_replace(['rgb(', ')'], '', $rgb);
		$rgb = explode(',', $rgb);
	}
	if(!isset($rgb[1])){
		return "rgb(0,0,0)";
	}

	return $prefix
		   . sprintf('%02x', $rgb[0])
		   . sprintf('%02x', $rgb[1])
		   . sprintf('%02x', $rgb[2]);
}

function guidv4(){
	if (function_exists('com_create_guid') === true){ return trim(com_create_guid(), '{}'); }
	$data = openssl_random_pseudo_bytes(16);
	$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
	$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
	return strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
}

function fixFontKey($fontKey = ""){
	$hexStrings = str_replace("-", "", $fontKey);
	preg_match_all('/(..)/', $hexStrings, $hexStrings);
	$hexStrings = $hexStrings[0];
	$hexNumbers = array_map(function($v){ return hexdec($v); }, $hexStrings);
	return array_reverse($hexNumbers);
}

function generateODTTF($file_path="", $fontKey = "", $fonts_path, $font_id){

	$hexNumbers = fixFontKey($fontKey);
	$content = file_get_contents($file_path); //, null, null, 0, 32);
	if(!$content){
		return false;
	}
	
	for($i=0; $i!=32;$i++){
		$byte = ord($content[$i]); 
		$new_byte = $byte ^ $hexNumbers[$i % count($hexNumbers)];
		$content[$i] = chr($new_byte);
	}
	$font_file_name = "font$font_id.odttf";
	
//	echo $file_path."  :: " .md5($content).  " <br>";
	
	file_put_contents("$fonts_path/$font_file_name", $content);
	return $font_file_name;
}

function getContentTypes(){
return '<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
	<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml" />
	<Default Extension="xml" ContentType="application/xml" />
	<Default Extension="png" ContentType="image/png" />
	<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml" />
	<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml" />
	<Override PartName="/docProps/custom.xml" ContentType="application/vnd.openxmlformats-officedocument.custom-properties+xml" />
	<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml" />
	<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml" />
	<Override PartName="/word/numbering.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml" />
	<Override PartName="/word/settings.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml" />
	<Override PartName="/word/theme/theme1.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml" />
	<Override PartName="/word/webSettings.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.webSettings+xml" />
	<Override PartName="/word/fontTable.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.fontTable+xml" />
	<Override PartName="/word/comments.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.comments+xml" />
	<Override PartName="/word/header1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.header+xml" />
	<Default Extension="odttf" ContentType="application/vnd.openxmlformats-officedocument.obfuscatedFont" />
</Types>


';
}


function convertODTTF($file_path="", $fonts_path="", $font_name="", $font_id=1){

	$fontKey = guidv4();
	
	
	$file_name = generateODTTF($file_path, $fontKey, $fonts_path, $font_id);
	if(!$file_name){
		return;
	}
	return ["fontKey"=>$fontKey, 
	"file_name"=>$file_name,
	"font_id"=>$font_id,
	"file_path"=>$fonts_path."/".$file_name,
	"xml"=>"<w:font w:name=\"$font_name\"><w:altName w:val=\"Arial\" /><w:charset w:val=\"01\" /><w:family w:val=\"swiss\" /><w:sig w:usb0=\"00000000\" w:usb1=\"00000000\" w:usb2=\"00000000\" w:usb3=\"00000000\" w:csb0=\"00000000\" w:csb1=\"00000000\" /><w:pitch w:val=\"variable\" /><w:embedRegular r:id=\"rId$font_id\" w:fontKey=\"{{$fontKey}}\"/></w:font>"];
	return true;
}



