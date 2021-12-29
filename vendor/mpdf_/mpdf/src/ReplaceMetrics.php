<?php

namespace Mpdf;

class ReplaceMetrics extends Mpdf{
	public function __construct(){
	}



	function mmSize2pdfSize($arr=array("left"=>0, "top"=>0, "width"=>0, "height"=>0)){
		$baseline_shift = $arr['base_line'];

		$tb = ($arr['height']-$arr['font_size'])*2;
		$baseline_shift = $tb;

		$glyphYorigin = 0;
		
		$scale = Mpdf::SCALE;
		
		if($this->w > $this->h){
			$l = ($arr['left']) * Mpdf::SCALE;
			$t = ($this->h-($arr['top'] + $glyphYorigin - $baseline_shift)) * Mpdf::SCALE;
			$l2 = ($arr['left']+$arr['width']) * Mpdf::SCALE;
//			$tt3 = ($arr['top']*$scale)+(($arr['height'])*$scale)-($arr['font_size']/$scale);
//			$tt3 = (($arr['top']) * Mpdf::SCALE)+(($arr['height']-$arr['font_size'])*Mpdf::SCALE); //($this->h - (($arr['top']+$arr['width']) +0 )) * Mpdf::SCALE; //float(272.28705117938
			$tt3 = ($arr['top']*$scale)+(($arr['height'])*$scale)-$arr['font_size'];;
			
		}else{
			$l = ($arr['left']) * Mpdf::SCALE;
			$t = ($this->h-($arr['top'] + $glyphYorigin - $baseline_shift)) * Mpdf::SCALE;
			$l2 = ($arr['left']+$arr['width']) * Mpdf::SCALE;
			$tt3 = ($this->h - (($arr['top']+$arr['height']) + $glyphYorigin - $baseline_shift)) * Mpdf::SCALE;
		}

		if(isset($_COOKIE['wowow'])){
			//var_dump($this->h*Mpdf::SCALE);
			
			//exit();
		}


		return [
			"l"=>$l, 
			"t"=>$t, 
			"l2"=>$l2, 
			"tt3"=>$tt3, 
			];
	}


	public function mergeMetrics($arr_1=[0,0,0,0,0,0], $arr_2=[0,0,0,0,0,0]){
//		return [$arr_2[0],$arr_2[1],$arr_2[2],$arr_2[3],$arr_2[0],$arr_2[0],]
	} 


	public function replace($deleted_elements=array(), $buffer="", $h=0, $w=0, $scale=0){
		$deviations = "3";
		$deviations2 = "3";


		if(isset($_COOKIE['wowow'])){
//			$buffer= "
//1 0 0 1 -54 -693.72 cm
//54 79.26 127.22 .54 re
//BT
//0 g
///F1 10.02 Tf
//54 622.42 Td
//0 g
///F1 10.02 Tf
//[(A )-66(re)-2(pr)-2(e)5.0000007(sent)4(ativ)5.0000007(e )-69(sa)-3(mple )-69(of)8( )-68(the )-68(pr)-2(odu)3(ct )-69(co)-3(ver)6(ed )-70(by )-66(this )-69(report )-67(ha)4(s )-71(been)-3( )-68(eval)8(u)5.0000007(ated )-68(and )-71(fou)7(nd )-70(to )-67(co)-3(mply)9.000001( )-68(with )-69(the)-4( )] TJ
//ET
//";
		}


//		echo "<pre>";
//		exit($buffer);

		if($w>$h){
			$this->w = $h;
			$this->h = $w;
		}else{
		
		}
		
		$this->h = $h;
		$this->w = $w;
		
		$flag = false;
		$hyerga = array();
		if(!empty($deleted_elements)){
			preg_match_all("#^([^\n]*)BT\s(.*)ET$#ismU", $buffer, $textContainers);
			$page_temp_elements = array();

//			новый регексп
//			0 - все что есть
//			1 - главная метрика
//			2 - саб метрика
//			3 - ебучий тл
//			4 - контент
//			5 - что-то вроде трансформа

			$at = array();
			$page_elements = array();
			$replaced_elements = array();

			foreach($deleted_elements as $id=>$de){
				$deleted_elements[$id]['document_pos'] = $this->mmSize2pdfSize($de);
			}
			
			
			$replaced_strings =  array();
			
			
			$element_cont = "";
			$rotated = false;
			$debug_flag = false;			
			
			$global_cm = array(0,0,0,0,0,0);
			$x = 0;
			$y = 0;
			
			
			foreach($textContainers[0] as $container_id=>$tc){
				$metrica = array(1,0,0,1,0,0,0);
				
				preg_match("/(.*) cm$/ismU", $tc, $block_has_cm);
				preg_match("/(.*) Tm$/ismU", $tc, $block_has_tm);				
				
				
				if(!empty($block_has_cm)){ $block_has_cm = true; }else{ $block_has_cm = false; }
				if(!empty($block_has_tm)){ $block_has_tm = true; }else{ $block_has_tm = false; }
				if(!$block_has_cm and !$block_has_tm){
					$global_cm = array(0,0,0,0,0,0);
					$x = 0;
					$y = 0;
				}
				
				
				$original_tc = $tc; 
				$replaced_strings = array();
				$td_count = 0;
				$tl_count = 0;
				$new_line_count = 0;
				$tl = 0;
				
				$tl_sum = 0;
				$td_sum = 0;
				$td_sum_y = 0;
				$new_line_summ = 0;
				
				$local_cm = array(0,0,0,0,0,0);
				$current_td = array(0,0);
				
				$regexp = "/(.*)Tm(?:\R)|(.*)Td(?:\R)|(.*)Tl\R|(.*)(?:tj|\')(?:\R)|(.*)(?:cm)\R|(.*)\R/i";
				//TODO Tm - сбрасывает координаты, cm - нет. tm плюсуется к cm, cm всегда должен складываться, вроде.
				
				preg_match_all($regexp, @$tc, $texts);
				$single_m = false;
				if($texts){
					foreach($texts[0] as $k=>$t){
						$finded = array();
						$full_val = $t; 
						if($texts[1][$k]){ //разбор TM 
							$rotated = false;
							$td_count = 0; 	$tl_count = 0; $new_line_count = 0; $td_sum = 0; $tl_sum = 0; $td_sum_y = 0;
							$new_line_summ = 0;

							$previous_metrica = $metrica;

							$metrica = explode(" ", trim($texts[1][$k]));
							//если у блока есть CM  - используем его, если CM нет - используем предыдущий, если он есть
							if($block_has_cm){
								$x = $metrica[5];
								$y = $metrica[4];
							}else{
								$x = ($metrica[5] * ($global_cm[3]!=0?$global_cm[3]:1))+$global_cm[5];
								$y = ($metrica[4] * ($global_cm[0]!=0?$global_cm[0]:1))+$global_cm[4];
							}
//							if($metrica[1]!=$previous_metrica[1] or $metrica[2]!=$previous_metrica[2]){
							if($metrica[1]!=0 or $metrica[2]!=0){
								$rotated = true;
								$tx = $x;
								$x = $y;
								$y = $tx;								
							}
						}else if($texts[5][$k]){ //разбор cm
							$rotated = false;
							$local_cm = explode(" ", trim($texts[5][$k]));
							
//							echo "<pre>";
//							var_dump($local_cm);
//							var_dump($global_cm);
//							exit();
							

							//TODO добавить проверку на поворот
							if($local_cm[1]!=$global_cm[1] and $local_cm[2]!=$global_cm[2]){
								$rotated = true;
							}

							if($block_has_tm){
								//TODO глобальный тм считается выше
								$prev_cm = $global_cm;
								$global_cm = $local_cm;
								//TODO херня наверное. 
								if($metrica and $block_has_tm and ($metrica[3]==$global_cm[3] || $metrica[3]!=$global_cm[3])){
									$x = ($x * ($global_cm[3]!=0?$global_cm[3]:1))+$global_cm[5];
									$y = ($y * ($global_cm[0]!=0?$global_cm[0]:1))+$global_cm[4];
								}else{
									$x += $global_cm[5];
									$y += $global_cm[4];
								}
								
							}else{	//если у блока нет Tm - сумируем cm
								if($rotated and $global_cm[5]){ //если это не первый cm
									$global_cm[4] += $local_cm[4];
									$global_cm[5] -= $local_cm[5];
									$global_cm[0] = $local_cm[0];
									$global_cm[1] = $local_cm[1];
									$global_cm[2] = $local_cm[2];
									$global_cm[3] = $local_cm[3];
									$x = $global_cm[5];
									$y = $global_cm[4];
								}else{ //если первый цм - просто присваиваем значения
									$global_cm[0] = $local_cm[0];
									$global_cm[1] = $local_cm[1];
									$global_cm[2] = $local_cm[2];
									$global_cm[3] = $local_cm[3];
									$global_cm[4] += ($rotated?$local_cm[5]:$local_cm[4]);
									$global_cm[5] += ($rotated?$local_cm[4]:$local_cm[5]);
									$x = $global_cm[5];
									$y = $global_cm[4];
									//echo "<pre>x is $x and local_cm {$local_cm[5]}\r\n";
								}
							}
							if($rotated){
//								$tx = $x;
//								$x = $y;
//								$y = $tx;
//								unset($tx);								
							}

							
						}else if($texts[2][$k]){ // разбор TD
							$td = $current_td = explode(" ", trim($texts[2][$k]));
							
							
							if((float)$td[1]!=0){ 
								$td_count++;
								$td_sum += $td[1];
							}
							$td_sum_y += $td[0];
						}else if($texts[3][$k]){ //add line ебаный тл
							$tl = (float)trim($texts[3][$k]);
							$tl_sum += $tl;
							if((float)$tl!=0){ 
								$tl_count++; 
							}
						}else if($texts[6][$k]){ //new line ебаный тл №2
							$t = $texts[6][$k];
							if($t=='T*'){
								$new_line_count++;
								$new_line_summ += $tl;
							}
						}else if($texts[4][$k]!=false){ //контент строка
							$finded['nl'] = false;
							if(strpos($t, "T*")===0){
								$finded['nl'] = true;
								//exit("here T*");
							}
							if($rotated and $block_has_tm){
								$element_offset_x = (($td_sum*-1)*$metrica[2])+($new_line_summ*$metrica[2]);
							}else{
								$element_offset_x = (($td_sum*-1)*$metrica[3])+($new_line_summ*$metrica[3]);
								if($rotated){
									$element_offset_x *=-1;
								}
							}
							
							if($rotated){
								//$element_offset *=-1;
							}
								
							$element_x = $x-$element_offset_x;
							$element_y = $y+$td_sum_y; 

							
							$finded = array_merge($finded, array(
								"l"=>$element_y,
								"t"=>$element_x,
								"content"=>$t,
							));

							$page_elements[] = $finded;
							
							foreach($deleted_elements as $de_k=>$de){
								$shp = $de['document_pos'];
								if(
									($finded['l']>=$shp['l']-$deviations && $finded['l']<=$shp['l2']-1) 								
									and
									($finded['t']>=$shp['tt3']-$deviations && $finded['t']<=$shp['tt3']+$deviations)
								){
									$is_array = strpos($t, "]TJ")?true:false;
									$empty = "()Tj\r\n";
									if($finded['nl']){ $empty = "T*[()]TJ\r\n"; }
									if($is_array and $finded['nl']){ $empty = "T*[()]TJ\r\n"; }
									if($is_array and !$finded['nl']){ $empty = "[()]TJ\r\n"; }
									if(!$is_array and $finded['nl']){ $empty = "T*[()]Tj\r\n"; }
									if(!$is_array and !$finded['nl']){ $empty = "()Tj\r\n"; }
									$empty = "()Tj";
									
									$replaced_strings[] = array("line"=>$k, "string"=>$t, "replace_to"=>$empty);
									$replaced_elements[] = $finded;
									//TODO uncomment
									$texts[0][$k] = $empty;
									$deleted_elements[$de_k]['is_replaced'] = true;
								}
							}
						}else{
							//exit("что-то мы делаем не так");
						}
						$texts[0][$k] = trim($texts[0][$k]);
					}
					
					if(!empty($replaced_strings)){					
						foreach($texts[0] as $eee=>$tte){
							$texts[0][$eee] = trim($texts[0][$eee]);
						}
						
						$new_tc = implode("\n", array_filter($texts[0]));
						$new_tc .= "\r\nET\r\n";
						$buffer = str_replace($original_tc, ($new_tc), $buffer);
	//					$buffer = preg_replace("/".preg_quote($original_tc, "/()")."/", preg_quote($new_tc), $buffer);
					}
				}
			}
			
			

			
			$not_replaced_elements = array();
			foreach($deleted_elements as $nk=>$de){
				if(!isset($de['is_replaced'])){
					$not_replaced_elements[] = $nk;
				}
			}


//			if(isset($_COOKIE['wowow'])){
//				echo "<pre>";
////				var_dump($deleted_elements);
//				var_dump($not_replaced_elements);
//				exit();
////				exit($buffer);
////				echo "<pre>";
////				var_dump($deleted_elements);
////				exit();
////				exit($buffer);
////				var_dump($page_elements);
////				exit();
////				var_dump($page_elements);
////				exit();
//				
//			}

			
			return array("buffer"=>$buffer, "not_replaced_elements"=>$not_replaced_elements);
			
		}
		return array("buffer"=>$buffer, "not_replaced_elements"=>array());		
		
	}




}
