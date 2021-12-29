<?php
namespace App\Custom;

use Spatie\Async\Pool;

class DocxParser{
	private $file = false;
	private $texts = [];
	private $client = false;
	
	public function __construct($file=false, $dest_file=false){
		if(!$file){
			return false;
		}
		
		//TODO uncomment
		\File::copy($file, $dest_file);
		$this->file = $dest_file;
	}
	
	private function testText($text=""){
		if(!$text){
			return false;
		}
		if(preg_match("/^\d+$|^-\d+$/", $text)){
			return false;
		}
		$bad_chars = array("-", "?", ".", "=", "+", "_", ">", "<", "~", "@", '#', '"', "—", " ");
		if(in_array($text, $bad_chars)){
			return false;
		}
		return true;
	}
	
	public function transBlocks($blocks=array(), $langs){
		$multi = curl_multi_init();
		$channels = array();
		$url = route("translate-block");
//		print_r($url);
		$token = csrf_token();
//		print_r($token);
		$responses = array();
	
		foreach($blocks as $k=>$block){
			$data = array(
				"text"=>$block['text'],
				"key"=>$k,
				"lang_from"=>$langs['from'],
				"lang_to"=>$langs['to'],
			);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-CSRF-TOKEN: $token"));
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_multi_add_handle($multi, $ch);
//			print_r($data);
//			print_r($ch);
			$channels[$k] = $ch;
		}
		$total = count($blocks);
		$total_translated = 0;

		$active = null;
		do{
			$mrc = curl_multi_exec($multi, $active);
		}while ($mrc == CURLM_CALL_MULTI_PERFORM);

		while($active && $mrc == CURLM_OK){
			if(curl_multi_select($multi) == -1){
				continue;
			}

			while(($info = curl_multi_info_read($multi)) !== false){
				if ($info["result"] == CURLE_OK){
					$content = curl_multi_getcontent($info["handle"]);
					$total_translated++;	
					//var_dump($info);
				}
				echo ceil($total_translated*100/$total)."||||";
				ob_flush();
				flush();
				//echo "$total_translated / $total\r\n";
			}

			do{
				$mrc = curl_multi_exec($multi, $active);
			}while ($mrc == CURLM_CALL_MULTI_PERFORM);
		}
		
		
		foreach($channels as $channel){
			$resp = json_decode(curl_multi_getcontent($channel), 1);
//			var_dump($resp);
			if(isset($resp['key'])){
				$blocks[$resp['key']]['translated'] = $resp['translated'];
			}
			curl_multi_remove_handle($multi, $channel);
		}
		curl_multi_close($multi);
		return $blocks;
	}


	function replace_first($find, $replace, $subject) {
		return implode($replace, explode($find, $subject, 2));
	}

	public function translateXml($filename, $fileXml, $langs)
	{
		if(!$filename || !file_exists($filename)) return false;
		$zip = new \ZipArchive();
		$zip->open($filename);
		$content = $zip->getFromName($fileXml);
		$for_replace = array("“", "”");
		$wp_regex = "/<w:p[^>\/]*>(.*?)<\/w:p>/s";
		
		
		
		preg_match_all($wp_regex, $content, $matches);

		$blocks_for_translate = array();

//		print_r($matches[0]);exit;
		foreach($matches[0] as $k=>$m){

			$mo = $m;
			$wt_regex = "/<w:t[^>\/]*>(.*?)<\/w:t>/s";
			$wt_regex = "/(?:<w:t>|<w:t [^>\/]*?>)(.*?)<\/w:t>/s";
			
			preg_match_all($wt_regex, $m, $tms);


			
			if(!empty($tms)){

				$mn = $mo;
				$block_text = "";
				$first_wt = "";
				$first_wt_content = "";
				
				foreach($tms[1] as $ti=>$tm){
				


					$block_text .= $tm;
					


					if($ti!=0){
						$new_cont = str_replace($tms[1][$ti], "", $tms[0][$ti]);
						$new_cont = str_replace("&AMP;", "", $new_cont);
						$mn = str_replace($tms[0][$ti], $new_cont, $mn);
					}else{
					
						$first_wt = $tms[0][$ti];
						$first_wt_content = $tms[1][$ti];
					}
				}

				if($block_text!="" && $this->testText($block_text)){
					$blocks_for_translate[] = array(
						"text"=>$block_text,
						"translated"=>$block_text,
						"first_wt"=>$first_wt,
						"first_wt_content"=>$first_wt_content,
						"mn"=>$mn, //new block
						"mo"=>$mo, // old block
					);
				}
			}
		}
		

		$translated_blocks = $this->transBlocks($blocks_for_translate, $langs);
		
		

		if(empty($translated_blocks)){
			//TODO show error here
		}else{
			
		
			foreach($translated_blocks as $key=>$tb){
				$new_wt = $this->replace_first(">".$tb['first_wt_content']."<", ">".$tb['translated']."<", $tb['first_wt']);
			
				$new_mn = str_replace($tb['first_wt'], $new_wt, $tb['mn']);
//				
				$new_mn = str_replace('xml:space="preserve"', "", $new_mn);
				
				
				

				$content = str_replace($tb['mo'], $new_mn, $content);
			}
		}
		

//		header("Content-type: text/xml");
//		exit($content);

		$zip->addFromString($fileXml, $content);
		$zip->close();
	}
	
	public function getDocumentXML(){
		exit($this->file);
	}
	
	
	public function getTexts($langs){

//		copy("/var/www/html/homes/test/demo_min2.docx", "/var/www/html/homes/public/uploads/pdf/demo_min2_test.docx");
//		$this->file = "/var/www/html/homes/public/uploads/pdf/demo_min2_test.docx";

//		$texts = array();

//		$filename = $this->file;
//		var_dump($filename);

		$this->translateXml($this->file, 'word/document.xml', $langs);
		$this->translateXml($this->file, 'word/footnotes.xml', $langs);
		$this->translateXml($this->file, 'word/endnotes.xml', $langs);

		return $this->file;

		header('Content-Disposition: attachment; filename="trans.docx"');
		header('Content-Type: application/msword');
		echo file_get_contents($this->file);

		exit();
		
		return $content;
	}

	public function saveTexts(){

	}
}
