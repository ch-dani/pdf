<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\UserImages;
use Illuminate\Support\Facades\Auth;
//use Mpdf;
use File;
use URL;
use Mail;
use App\SharedLinks; 
use App\NewTranslate;

class EditDocx extends Controller
{


	private function str_replace_first($from, $to, $content){
		$from = '/'.preg_quote($from, '/').'/';

		return preg_replace($from, $to, $content, 1);
	}


	public function fillDocx(Request $request, $token=""){
		$texts = ($request->input("texts"));
		$exist = NewTranslate::where("token", $token)->get()->first();
		$document = Document::where(["operation_id"=>$token])->get()->first();
		
		if($document && $exist && $exist->texts){
			$exist_texts = json_decode($exist->texts,1);

			copy(base_path()."/public/".$document->original_document, base_path()."/public/".$document->original_document."_translated.docx");
			$document_path = base_path()."/public/".$document->original_document."_translated.docx";


			$zip = new \ZipArchive;
			if ($zip->open($document_path) === TRUE) {
				$document_content = $zip->getFromName('word/document.xml');
				//$zip->close();
			} else {
				return response()->json(["success"=>false, "message"=>"Can't open docx file"]);
			}
			$flag = false;
			
			
			foreach($exist_texts as $pn=>$et){
			
				$original = $et['original'][0];
				
				$new_text = ($texts[$pn][0][0]);

				if($new_text=='ВВЕДЕНИЕ'){
					$flag = true;
				}
				
				$new_text = htmlspecialchars($new_text);
				$new_text = str_replace("&", "&amp;", $new_text);
				
//				preg_match_all("/<w:t[^>]*>(.*?)<\/w:t>/", $original, $matches);
				preg_match_all("/<w:t>(.*?)<\/w:t>|<w:t [^>]*>(.*?)<\/w:t>/", $original, $matches);

				$replace_to = "<w:t>$new_text</w:t>";
				$new_string = $original;
				if($matches && @$matches[0]){
					foreach($matches[0] as $it=>$match){
						if($it==0){
							$new_string = $this->str_replace_first($match, $replace_to, $new_string);
							if($flag){
							}
						}else{
							$new_string = str_replace($match, "<w:t></w:t>", $new_string);
						}
					}
				}
				
				if($flag){
				
					//exit($new_string);
				}
				
				$new_string = str_replace("：", ":", $new_string);
				
				$document_content = (str_replace($original, $new_string, $document_content));
			}

			$zip->deleteName("word/document.xml");
			$zip->addFromString("word/document.xml", $document_content);
			$zip->close();
			$download_url = str_replace(base_path()."/public/", "",$document_path);
			$document->update(["edited_document"=>$download_url]);
			return response()->json(["success"=>true, "download_url"=>$download_url]);
		}
		return response()->json(["success"=>false, "message"=>"Document not found"]);

	}

    public function uploadDOCX(Request $request)
    {
		// exit('d');
    	$is_external = false;
    	if($request->post("contents")){
    		$is_external= true;
    	}
    	
        $ui = new UserImages;
		$ot = $request->post('operation_type')?strtolower($request->post('operation_type')):"edit";
		$is_multiple_upload = $request->post("multiple_upload");
		
		
		if($is_external){
			$file = $request->post("contents");	
		}else{
	        $file = $request->file("file");
		}

        $destinationPath = 'uploads/docx';
        $uuid = $request->post("UUID");
		
		$filename = "{$uuid}_{$ot}.docx";

		if($is_external){

            $url = $request->link;
            $name = base_path() . "/public/{$destinationPath}/$filename";

            set_time_limit(0);
            $fp = fopen ($name, 'w+');
            $ch = curl_init(str_replace(" ","%20",$url));
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer '.$request->access_token,
            ));
            // get curl response
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
		}else{
        	$file->move($destinationPath, $filename);
		    $ui->UUID = $request->post("UUID");
		    $ui->file_name = $filename;
		    $ui->file_type = $request->post("type");
		    $ui->save();
        }

        $temp_file = base_path() . "/public/{$destinationPath}/temp/$filename";
        $dest_file = base_path() . "/public/{$destinationPath}/$filename";

	    $temp_file .= ".notext.pdf";

		$password = $request->post("pdf_password");
		$pwd = "";
		$pwdm = "";
		$pwdcairo = "";

		$font_array = array();
		
		if($is_external){
			$original_name = $request->post("file_name");
		}else{
			$original_name = $file->getClientOriginalName();
		}
		
	    $x = Document::create([
		    'user_id' => Auth::check() ? Auth::user()->id : NULL,
		    'UUID' => $uuid,
		    'operation_id'=>$request->post("operation_id"),
		    'operation_type'=>$ot,
		    'original_document' => $destinationPath.'/'.$filename,
		    'original_name' => $original_name."",
		    "delete_after"=>(time()+18000),
	    ]);

		$zip = new \ZipArchive;
		if ($zip->open($destinationPath.'/'.$filename) === TRUE) {
			$document_content = $zip->getFromName('word/document.xml');
			$zip->close();
		} else {
			return response()->json(["success"=>false, "message"=>"Can't open docx file"]);
		}
	    
	     return response()->json(['success' => true, "file_path" => false, "file_path2"=>$filename, "document_content"=>$document_content, "del"=>time()+18000]);


//	    
//	    exit("time to extract");
//	    

//	    // $output = shell_exec(base_path()."/public/pdf_scripts/extract_fonts.sh '$dest_file'");
//		// shell_exec('doc2pdf ' . base_path() . '/public/' . $destinationPath . "/{$uuid}_{$ot}.docx ./demo.pdf");
//		
//		$shell_exec = ("doc2pdf " . base_path() . "/public/{$destinationPath}/{$uuid}_{$ot}.docx " . base_path() . "/public/{$destinationPath}/{$ot}_{$uuid}.pdf");
//		shell_exec($shell_exec);
//        // return response()->json(['success' => true, "file_path"=>$filename, "fonts"=>$font_array, "del"=>time()+18000]);
//        return response()->json(['success' => true, "file_path" => "{$uuid}_{$ot}.pdf", "file_path2"=>$filename, "fonts"=>$font_array, "del"=>time()+18000]);
    }
}
