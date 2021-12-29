<?php

namespace App\Http\Controllers;

use App\Document;
use App\Option;
use File;
use Illuminate\Support\Str;
use Storage;
use Symfony\Component\Process\Process;
use ZipArchive;
use Mpdf;
use Mpdf\Css\TextVars;
use Mnvx\Lowrapper\Converter;
use Mnvx\Lowrapper\LowrapperParameters;
use Mnvx\Lowrapper\Format;
use Illuminate\Http\Request;
use App\Http\Controllers\EditPdf as EditPdf;
use Aws\Translate\TranslateClient;
use Aws\Exception\AwsException;
use Mail;

class ToolControllerT2 extends Controller
{
    public function pdf2ppt(Request $req){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);


        $uuid = $req->post("uuid");
        $operation_type = "pdf2ppt";
        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pptx"));
        $dest_file_pptx = (EditPdf::getDestPath($uuid, $operation_type, ".pptx"));


        $operation_id = $req->post("operation_id");

        $shell = "";
        $total_pages = 0;

        $doc = Document::where([
            'UUID'=>$uuid,
            'operation_type'=>$operation_type,
            'operation_id'=>$operation_id,
        ])->orderBy('ID', 'desc')->first();

        if(!$doc){
            return response()->json(['success'=>false, "message"=>"Operation not found"]);
        }

        $doc = $doc->toArray();
        
        if(!is_file($original_file)){
            return response()->json(['success'=>false, "message"=>"File not found"]);
        }

        $temp = storage_path("pdfburst/$uuid/");
        File::deleteDirectory($temp);

        if(!is_dir($temp)){
            if(!File::makeDirectory($temp)){
                return response()->json(['success'=>false, 'message'=>'Cant create temp folder...']);
            }
        }

        shell_exec("pdfjam --outfile $original_file.resized --landscape  --fitpaper true  $original_file");
        rename("$original_file.resized", $original_file);

        $shell = escapeshellcmd("libreoffice --infilter='impress_pdf_import' --headless --convert-to pptx $original_file  --outdir $temp");

        shell_exec($shell);


        function file_ext_strip($filename){
            return preg_replace('/.[^.]*$/', '', $filename);
        }
        $or2 = file_ext_strip($original_file).".pptx";

        $tmp_path = explode("/", $or2);
        $or2 = $temp."/".end($tmp_path);
        if(!is_file($or2)){
            sleep(10);
        }
        rename($or2, $dest_file);

        $file = str_replace('/home/admin/public_html', '', $dest_file);

        Document::where(['id'=>$doc['id']])->update(['edited_document' => $dest_file, "delete_after"=>(time()+18000)]);

        return response()->json(['status'=>'success', 'filename'=>EditPdf::getNewFileName($doc['original_name'], "", ".pptx"), 'file'=>$file]);
    }


    public function pdf2epub(Request $req){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);


        $uuid = $req->post("uuid");
        $operation_type = "pdf2epub";
        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".epub"));
        $dest_file_epub = (EditPdf::getDestPath($uuid, $operation_type, ".epub"));

        $operation_id = $req->post("operation_id");

        $shell = "";
        $total_pages = 0;

        $doc = Document::where([
            'UUID'=>$uuid,
            'operation_type'=>$operation_type,
            'operation_id'=>$operation_id,
        ])->orderBy('ID', 'desc')->first();
        if(!$doc){
            return response()->json(['success'=>false, "message"=>"Operation not found"]);
        }
        $doc = $doc->toArray();


        if(!is_file($original_file)){
            return response()->json(['success'=>false, "message"=>"File not found"]);
        }

        $temp = storage_path("pdfburst/$uuid/");
        File::deleteDirectory($temp);


        $oldmask = umask(0);
        mkdir($temp, 0777);
        umask($oldmask);

//        shell_exec("sudo chmod -R 777 $temp");

        $shell = escapeshellcmd("ebook-convert  $original_file $dest_file --enable-heuristics --title $temp");

        shell_exec($shell);

        function file_ext_strip($filename){
            return preg_replace('/.[^.]*$/', '', $filename);
        }
        $or2 = file_ext_strip($original_file).".epub";

        $tmp_path = explode("/", $or2);
        $or2 = $temp."/".end($tmp_path);
        if(!is_file($or2)){
            sleep(10);
        }
//        rename($or2, $dest_file);

        $file = str_replace('/home/admin/public_html', '', $dest_file);
        Document::where(['id'=>$doc['id']])->update(['edited_document' => $dest_file, "delete_after"=>(time()+18000)]);

        return response()->json(['status'=>'success', 'filename'=>EditPdf::getNewFileName($doc['original_name'], "", ".epub"), 'file'=>$file]);
    }


    public function burst(Request $req)
    {
        $uuid = $req->post("uuid");
        $operation_type = "splitpdf";
        $pages_list = array();
        $doc = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type])->orderBy('ID', 'desc')->first()->toArray();

        $file_name_db = ($doc['original_name']);


        $original_file = EditPdf::getFilePath($uuid, $operation_type);
//        dd($original_file);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".zip"));

        $file_not_found = false;

		if(empty($req->input('letters'))){
			if (!is_file($original_file)) {
				$file_not_found = true;
			}
		}else{
			foreach($req->input('letters') as $letter){
				$original_file_letter = EditPdf::getFilePath($uuid, $letter . '_' . $operation_type);
				if (!is_file($original_file_letter)) {
					$file_not_found = true;
				}
			}
		}

        if ($file_not_found) {
            return response()->json(['success' => false, 'message' => 'Original file not found...']);
        }

        $temp = storage_path("pdfburst/$uuid/");

        File::deleteDirectory($temp);

        try {
            $oldmask = umask(0);
            mkdir($temp, 0777);
            umask($oldmask);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cant create temp folder...']);
        }
//        dump($temp);

        $name_patern = ($req->post("name_patern") ? $req->post("name_patern") : "[CURRENTPAGE]-[BASENAME]");

        $pwd = "";
        if ($password = $req->post("pdf_password")) {
        	if($password != 'false'){
				$pwd = "input_pw $password";
			}
        }

        switch ($req->post("type")) {
            default:
            case 'everyPage':
//                $shell_b = "nice -n 19 pdftk $original_file $pwd  burst output $temp/page%2d.pdf";
				if(empty($req->input('letters'))){
					$shell_b = "pdftk $original_file $pwd  burst output $temp/page%2d.pdf";
					shell_exec($shell_b);
				}else{
					foreach($req->input('letters') as $letter){
						$original_file_letter = EditPdf::getFilePath($uuid, $letter . '_' . $operation_type);
						$shell_b = "pdftk $original_file_letter $pwd  burst output $temp/page".$letter."%2d.pdf";
						shell_exec($shell_b);
					}
				}

                File::delete("$temp/doc_data.txt");
                $pages = File::files($temp, "name");
                foreach ($pages as $p) {
                    $pages_list[] = $p->getPathName();
                }
                break;

            case 'byPage':
                $ranges = $req->post("ranges");
                if ($ranges == 'false') {
                    $pages_list[] = $original_file;
                } else {
					$single_page_extractor = $req->input("single_page_extractor");

                	if($single_page_extractor){
						$original_file_letter = EditPdf::getFilePath($uuid, $req->input('letter') . '_' . $operation_type);
						$shell_pe = "pdftk $original_file_letter $pwd cat %s output  $temp/page%s-%d.pdf";
						$shell_pe = sprintf($shell_pe, escapeshellarg($req->input('page')), ($req->input('letter')), ($req->input('page')));
						shell_exec($shell_pe);
					}else{
						$ranges = explode(",", $ranges);
						$pn = 1;
//                    	$shell_cat = "nice -n 19 pdftk $original_file $pwd cat %s output  $temp/page-%d.pdf";
						$shell_cat = "pdftk $original_file $pwd cat %s output  $temp/page-%d.pdf";
						foreach ($ranges as $r) {
							$shell = sprintf($shell_cat, escapeshellarg($r), ($pn));
							shell_exec($shell);
							$pn++;
						}
					}

                    $pages = File::files($temp, "name");
                    foreach ($pages as $p) {
                        $pages_list[] = $p->getPathName();
                    }
                }

                break;
        }
        sort($pages_list);
        if (empty($pages_list)) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve pages...']);
        }

        $path = base_path("public") . '/uploads/split-pdf/';
        $folder = $path . $uuid;
        File::deleteDirectory($folder);
        if(!File::exists($folder)) {
            try {
                $oldmask = umask(0);
                mkdir($folder, 0777);
                umask($oldmask);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Cant create temp folder...']);
            }
        }

        rename($temp, $folder);
        exec("cd " . $path . "; zip -r $uuid.zip $uuid");

        $url = str_replace('/home/admin/public_html', '', $folder) . '.zip';
        $file_name = str_replace('pdf', 'zip', $doc['original_name']);

//        File::deleteDirectory($temp);

        $new_file_name = EditPdf::getNewFileName($doc['original_name'], "split", ".zip");
        Document::where('id', $doc['id'])->update(['edited_document' => $dest_file,
            "download_name" => $new_file_name,
            "delete_after" => (time() + 18000)]);

        return response()->json(['status' => 'success', 'filename' => "$uuid.zip", 'file' => $url]);
    }


    public function word2pdf(Request $request)
    {
        $uuid = $request->UUID;
        $operation_type = "doctopdf";
        $docBuilder = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type]);

        foreach ($request->input('files') as $index => $filePostfix) {
            if ($index === 0) {
                $docBuilder->where('original_document', 'like', 'uploads/doc-to-pdf/' . $uuid . '/tmp/' . $uuid . '-' . $filePostfix . '%');
            } else {
                $docBuilder->orWhere('original_document', 'like', 'uploads/doc-to-pdf/' . $uuid . '/tmp/' . $uuid . '-' . $filePostfix . '%');
            }
        }
        $doc = $docBuilder->get()->toArray();

        if (!$doc) return response()->json(['success' => false, "message" => "Operation not found"]);

        $edited_documents_path = [];

        foreach ($doc as $singleDoc) {
            $original_file = $singleDoc['original_document'];
            if (!Storage::exists($original_file)) return response()->json(['success' => false, "message" => "File not found"]);

            $original_file_storage_path = storage_path('app/' . $original_file);
            $process = new Process("HOME=" . getcwd() . " && export HOME && unoconv -f pdf $original_file_storage_path");
            $process->run();

            $edited_document_path = preg_replace('/\\.[^.\\s]{3,4}$/', '', $original_file_storage_path) . '.pdf';
            $edited_documents_path[] = $edited_document_path;

            Document::where([
                'id' => $singleDoc['id'],
                'operation_id' => $singleDoc['operation_id'],
                'operation_type' => 'doctopdf',
            ])->update([
                'edited_document' => $edited_document_path,
                'delete_after' => (time() + 18000)
            ]);
        }

        $original_files_storage_path_chunks = explode('/', storage_path('app/' . $doc[0]['original_document']));
        array_pop($original_files_storage_path_chunks);
        $original_files_storage_path = implode('/', $original_files_storage_path_chunks);
        $outputName = Str::random() . '.pdf';
        $outputPath = $original_files_storage_path . $outputName;

        $cmd = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$outputPath ";

        foreach ($edited_documents_path as $edited_document) {
            $cmd .= $edited_document . ' ';
        }
        shell_exec($cmd);

        if (count($doc) > 1) {
            Document::create([
                'UUID' => $uuid,
                'operation_id' => $doc[0]['operation_id'],
                'operation_type' => $operation_type,
                'original_document' => '',
                'original_name' => '',
                'download_name' => $outputName,
                'edited_document' => $outputPath,
                'delete_after' => (time() + 18000),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'new_file_name' => $outputName,
            'url' => str_replace('/home/admin/public_html', '', str_replace('app/', '', $outputPath)),
        ]);
    }
}
