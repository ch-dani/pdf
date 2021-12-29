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

class ToolController2 extends Controller
{
    private $mpdf = false;

    public function mpdfGet()
    {
        return $this->mpdf;
    }

    public function generateInvoice(Request $req)
    {
        set_time_limit(120);
        $font_path = base_path() . "/vendor/mpdf/mpdf/ttfonts/custom";
        $tmp_fonts = [];

        $append_page_size = $req->post("add_page_size");

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $fonts = array(
            "fontDir" => array_merge($fontDirs, [$font_path]),
            "fontdata" => array_merge($fontData, $tmp_fonts, array(
                "montserrat" => array(
                    "R" => "Montserrat-Regular.ttf",
                    "B" => "Montserrat-Bold.ttf",
                ),

            ))
        );

        $page_height = 176.662551937;


        $mpdf = new \Mpdf\Mpdf($fonts);


        $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];


        $mpdf->addPage("P", "", "", "", "", "0", "0", "0", "0", "", "", "", "", "", "", "", "", "", "", "", [216 - 12.7, $page_height + $append_page_size], false);

        $texts = $req->post("texts");

        foreach ($texts as $t) {
            $size = $t['size'];
            $styles = [];

            switch ($t['type']) {
                case 'bg':
                case 'image':
                    $styles = [
                        "position: absolute",
                        "top: {$size['top']}mm",
                        "left: {$size['left']}mm",
                        "width: {$size['width']}mm",
                        "height: {$size['height']}mm",
                    ];

                    break;

                case 'border_bottom':
                    $styles = [
                        "border-bottom: 1px solid {$t['css']['border-color']}"
                    ];
                    $size['top'] = $size['top'] + $size['height'];
                    unset($t['css']['border-color']);
                    break;

                case 'border':
                    $styles = [
                        "border-left: 1px solid {$t['css']['border-color']}",
                        "border-bottom: 1px solid {$t['css']['border-color']}",
                        "border-right: 1px solid {$t['css']['border-color']}",


                    ];
                    unset($t['css']['border-color']);
                    break;
                default:
                    break;
            }

            foreach ($t['css'] as $rule => $val) {
                $styles[] = "$rule:$val";
            }

            $styles[] = "font-family: montserrat; ";
            //$styles += $t['css'];

            if ($t['type'] == 'image') {
                if ($t['src'] != '#') {
                    $html = "<div style='white-space: nowrap; " . implode("; ", $styles) . ";'>
						<img src='{$t['src']}'>
					</div>";
                } else {
                    $html = "";
                }
            } else if ($t['type'] == 'border') {
                $html = "<div style='white-space: nowrap; height: {$size['height']}mm; " . implode("; ", $styles) . "'>
				</div>";
            } else {
                $size['width'] = $size['width'] + 0.3;
                $html = "<div style='white-space: nowrap; overflow: hidden; " . implode("; ", $styles) . "'>
					{$t['text']}
				</div>";
            }

            if ($html) {
                $mpdf->WriteFixedPosHTML($html, $size['left'], $size['top'], $size['width'], $size['height'], 'hidden', [], true);
            }

        }


        $operation_type = "invoice-generator";
        $uuid = $req->post("uuid");
        //$original_file = EditPdf::getFilePath($uuid, $operation_type);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pdf"));

        Document::create([
            "edited_document" => $dest_file,
            "delete_after" => time() + 18000,
            "UUID" => $uuid,
            "operation_id" => $req->post("operation_id"),
            "operation_type" => $operation_type,
            "original_name" => "invoice.pdf",
            "original_document" => "nope"
        ]);
        $mpdf->Output($dest_file, \Mpdf\Output\Destination::FILE);

        return response()->json(['success' => true, 'new_file_name' => "invoice.pdf",
            'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);
    }


    public function testpdf()
    {

        $html = file_get_contents("/var/www/html/homes/test.html");
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename="test.docx"');
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('php://output');

        exit();

        exit("end");
    }


    //TODO useless. используется перевод с помощью жс, сохранение в EditPdf.php
    public function translate(Request $req)
    {

        $texts = $req->post("texts");

        $client = new \Aws\Translate\TranslateClient([
            'profile' => 'default',
            'region' => 'us-west-2',
            'version' => '2017-07-01'
        ]);
        $currentLanguage = 'en';
        $targetLanguage = 'ru';

        $otexts = json_decode($req->post("texts"));
        $texts = implode(" [] ", json_decode($req->post("texts"))) . "";
        $texts = implode(" //// ", array("wowdoge", "Deferred Interest) will be added to your account. See the ", " Promotional Interest Charge Calculation", "table for details."));

        try {
            $result = $client->translateText([
                'SourceLanguageCode' => $currentLanguage,
                'TargetLanguageCode' => $targetLanguage,
                'Text' => $texts
            ]);
            $res_texts = $result->get("TranslatedText");
            //preg_match_all("/<span>(.*?)<\/span>/m", $res_texts, $return);

            $return = explode(" //// ", $res_texts);

            return response()->json(['success' => true, 'message' => false, "texts" => $res_texts, "original_texts" => ""]);
        } catch (AwsException $e) {
            // output error message if fails
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }


        exit("test");
    }

    public function pdf2ppt(Request $req){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);


		$single_page_extractor = (int)Request()->input("single_page_extractor");

        $uuid = $req->post("uuid");
        $operation_type = "pdf2ppt";
       	//$original_file = EditPdf::getFilePath($uuid, $operation_type);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pptx"));
        $original_file = $dest_file_merged = (EditPdf::getDestPath($uuid, $operation_type, "_merged.pdf"));        
        $dest_file_pptx = (EditPdf::getDestPath($uuid, $operation_type, ".pptx"));
        

        $operation_id = $req->post("operation_id");

        $shell = "";
        $total_pages = 0;

        $doc = Document::where([
            'UUID'=>$uuid,
            'operation_type'=>$operation_type,
            'operation_id'=>$operation_id,
        ])->orderBy('ID', 'asc')->get();

        if(!$doc){
            return response()->json(['success'=>false, "message"=>"Operation not found"]);
        }

        $doc = $doc->toArray();
        $all_files = [];
        $base = base_path("/public/");
        
        
        if($single_page_extractor){
        
        	
        	$single_doc_path = $base.$doc[$req->document]['original_document'];
			$merge_shell = "pdftk $single_doc_path cat {$req->page} output $dest_file_merged";
			shell_exec($merge_shell);        
        }else{
		    foreach($doc as  $d){
		    	$or = $base.$d['original_document'];
		    	$all_files[] = $or;
		    }
			$merge_shell = "pdftk ".implode(" ", $all_files)." cat output $dest_file_merged";
			shell_exec($merge_shell);
        }

        
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

        //dd("pdfjam --outfile $original_file.resized --landscape  --fitpaper true  $original_file");
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


		foreach($doc as $d){		
        	Document::where(['id'=>$d['id']])->update(['edited_document' => $dest_file, "delete_after"=>(time()+18000)]);
        }

        return response()->json(['status'=>'success', 'success'=>true, 
        	'url'=>$file,
        	'new_file_name'=>EditPdf::getNewFileName($doc[0]['original_name'], "", ".pptx"),
        	'filename'=>EditPdf::getNewFileName($doc[0]['original_name'], "", ".pptx"), 'file'=>$file]);
    }


    
    
    
//    public function pdf2ppt(Request $req){
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);

//        $uuid = $req->post("uuid");
//        $operation_type = "pdf2ppt";
//        $original_file = EditPdf::getFilePath($uuid, $operation_type);
//        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pptx"));
//        $dest_file_pptx = (EditPdf::getDestPath($uuid, $operation_type, ".pptx"));


//        $operation_id = $req->post("operation_id");

//        $shell = "";
//        $total_pages = 0;

//        $docs = Document::where([
//            'UUID'=>$uuid,
//            'operation_type'=>$operation_type,
//            'operation_id'=>$operation_id,
//        ])->orderBy('ID', 'desc')->get();
//        
//        
//        

//        if(!$docs){
//            return response()->json(['success'=>false, "message"=>"Operation not found"]);
//        }

//        $docs = $docs->toArray();
//        foreach ($docs as $doc) {
//            $original_file = '/var/www/freeconvert/public/' . $doc['original_document'];

//            $temp = storage_path("pdfburst/$uuid/");
//            File::deleteDirectory($temp);

//            if(!is_dir($temp)){
//                if(!File::makeDirectory($temp)){
//                    return response()->json(['success'=>false, 'message'=>'Cant create temp folder...']);
//                }
//            }

//            shell_exec("pdfjam --outfile $original_file.resized --landscape  --fitpaper true  $original_file");
//            rename("$original_file.resized", $original_file);
//            $shell = escapeshellcmd("libreoffice --infilter='impress_pdf_import' --headless --convert-to pptx $original_file  --outdir $temp");

//            shell_exec($shell);


//            function file_ext_strip($filename){
//                return preg_replace('/.[^.]*$/', '', $filename);
//            }
//            $or2 = file_ext_strip($original_file).".pptx";

//            $tmp_path = explode("/", $or2);
//            $or2 = $temp."/".end($tmp_path);
//            if(!is_file($or2)){
//                sleep(10);
//            }
//            rename($or2, $dest_file);

//            $file = str_replace('/var/www/freeconvert/public', '', $dest_file);

//            $filename = EditPdf::getNewFileName($doc['original_name'], "", ".pptx");


//            Document::where(['id'=>$doc['id']])->update(['edited_document' => $dest_file, "delete_after"=>(time()+18000)]);
//        }

//        return response()->json(['status'=>'success', 'filename'=>$filename, 'file'=>$file]);
//    }

    /*public function pdf2ppt(Request $request)
    {
        $uuid = $request->UUID;
        $operation_type = "pdftoppt";
        $docBuilder = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type]);
dd($request->input('files'));
        foreach ($request->input('files') as $index => $filePostfix) {
            if ($index === 0) {
                $docBuilder->where('original_document', 'like', 'uploads/pdf-to-ppt/' . $uuid . '/tmp/' . $uuid . '-' . $filePostfix . '%');
            } else {
                $docBuilder->orWhere('original_document', 'like', 'uploads/pdf-to-ppt/' . $uuid . '/tmp/' . $uuid . '-' . $filePostfix . '%');
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
                'operation_type' => 'pdftoppt',
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
            'url' => str_replace('/var/www/freeconvert', '', str_replace('app/', '', $outputPath)),
        ]);
    }*/

    public function ppt2pdf(Request $request)
    {
        $uuid = $request->UUID;
        $operation_type = "ppttopdf";
        $docBuilder = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type]);

        foreach ($request->input('files') as $index => $filePostfix) {
            if ($index === 0) {
                $docBuilder->where('original_document', 'like', 'uploads/ppt-to-pdf/' . $uuid . '/tmp/' . $uuid . '-' . $filePostfix . '%');
            } else {
                $docBuilder->orWhere('original_document', 'like', 'uploads/ppt-to-pdf/' . $uuid . '/tmp/' . $uuid . '-' . $filePostfix . '%');
            }
        }

        $doc = $docBuilder->get()->toArray();

        if (!$doc) return response()->json(['success' => false, "message" => "Operation not found"]);

        $edited_documents_path = [];

        foreach ($doc as $singleDoc) {
            $original_file = $singleDoc['original_document'];
            if (!Storage::exists($original_file)) return response()->json(['success' => false, "message" => "File not found"]);

            $original_file_storage_path = storage_path('app/' . $original_file);
			//dd("HOME=" . getcwd() . " && export HOME && unoconv -f pdf $original_file_storage_path");
            $process = new Process("HOME=" . getcwd() . " && export HOME && unoconv -f pdf $original_file_storage_path");
            $process->run();

            $edited_document_path = preg_replace('/\\.[^.\\s]{3,4}$/', '', $original_file_storage_path) . '.pdf';
            $edited_documents_path[] = $edited_document_path;

            Document::where([
                'id' => $singleDoc['id'],
                'operation_id' => $singleDoc['operation_id'],
                'operation_type' => 'ppttopdf',
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
        //dd($cmd);
        shell_exec($cmd);

		$outputPath = \App\Custom\PDFHelpers::replacePages($outputPath);
		$outputPath = str_replace(base_path("public"), '', $outputPath);


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

    public function removeUserUploadedFileAndRecord(Request $request)
    {
        $document = Document::where(['UUID' => $request->UUID, 'operation_type' => $request->operation_type])
            ->where('original_document', 'LIKE', '%' . $request->UUID . '-' . $request->file_number_postfix . '%')
            ->first();

        if (!$document) return response()->json(['status' => 'error', 'message' => 'Record not found']);

        if (Storage::exists($document->original_document)) {
            Storage::delete($document->original_document);

            if ($document->edited_document && Storage::exists($document->edited_document)) Storage::delete($document->edited_document);
            $document->delete();

            return response()->json(['status' => 'success', 'message' => 'File was successfully deleted']);
        }

        return response()->json(['status' => 'error', 'message' => 'File not found']);
    }

    public function excel2pdf(Request $request)
    {
        $uuid = $request->UUID;
        $operation_type = "exceltopdf";

        $docBuilder = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type]);

        foreach ($request->input('files') as $index => $filePostfix) {
            if ($index === 0) {
                $docBuilder->where('original_document', 'like', 'uploads/excel-to-pdf/' . $uuid . '/tmp/' . $uuid . '-' . $filePostfix . '%');
            } else {
                $docBuilder->orWhere('original_document', 'like', 'uploads/excel-to-pdf/' . $uuid . '/tmp/' . $uuid . '-' . $filePostfix . '%');
            }
        }

        $doc = $docBuilder->get()->toArray();

        if (!$doc) return response()->json(['success' => false, "message" => "Operation not found"]);

        $edited_documents_path = [];

        foreach ($doc as $singleDoc) {
            $original_file = $singleDoc['original_document'];
            if (!Storage::exists($original_file)) return response()->json(['success' => false, "message" => "File not found"]);

            $original_file_storage_path = storage_path('app/' . $original_file);
            $dest_file_storage_path = storage_path('app/' . 'uploads/excel-to-pdf/' . $uuid . '/tmp/');

            $process = new Process("export HOME=/tmp && soffice --headless --convert-to pdf:calc_pdf_Export --outdir $dest_file_storage_path $original_file_storage_path");
            $process->run();

            $original_files_storage_path_chunks = explode('/', storage_path('app/' . $singleDoc['original_document']));
            $fileName = array_pop($original_files_storage_path_chunks);
            $original_files_storage_path = implode('/', $original_files_storage_path_chunks);
            $outputName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName) . '.pdf';
            $outputPath = $original_files_storage_path . '/' . $outputName;
            $edited_documents_path[] = $outputPath;

            Document::where(['id' => $singleDoc['id']])->update([
                'edited_document' => str_replace('app/', '', str_replace('/home/admin/public_html', '', $dest_file_storage_path)),
                "delete_after" => (time() + 18000)
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

		$outputPath = \App\Custom\PDFHelpers::replacePages($outputPath);
		$outputPath = str_replace(base_path("public"), '', $outputPath);

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

    public function compress(Request $req)
    {
        $uuid = $req->post("UUID");
        $path = (public_path()) . "/uploads/pdf/";
        $uuid = str_replace(array("/", "~", "."), "-", $req->post("UUID"));
        $rnd = rand();
        $url = "uploads/pdf/";

        $operation_type = "compresspdf";
        $file = $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $file_dest = $dest_file = (EditPdf::getDestPath($uuid, $operation_type, "_n.pdf"));


        $doc = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type])->orderBy('ID', 'desc')->first();

        if (!$doc) {
            return response()->json(['success' => false, "message" => "Operation not found"]);
        }

        if (!is_file($file)) {
            return response()->json(['success' => false, "message" => "file not found"]);
        }
        //$shell1 = "pdftocairo -pdf $original_file {$original_file}_fixed";
        //shell_exec($shell1);
        //copy("{$original_file}_fixed", $original_file);


        $size = File::size($original_file);
        $imgr = $req->post("image_resolution");
        if ($size < 500000) {
            //$imgr *= 2;
        }

        $shell = "sudo nice -n 19 gs -sOutputFile=$file_dest -dQUIET -dSAFER -dNOPAUSE -dBATCH -dPDFSETTING=/default -sDEVICE=pdfwrite -dCompressFonts=true";// -dAutoFilterColorImages=false -dAutoFilterGrayImages=false -dGrayImageFilter=/FlateEncode -dColorImageFilter=/FlateEncode";


        $imgqc = "";
        $imgq = $req->post("image_quality");
        $img_conv = $req->post("image_conversion");


//		if($img_conv=='none'){
//			switch($imgq){
//				default:
//				case 'low':
//					$imgqc = " -c '.setpdfwrite << /ColorConversionStrategy /LeaveColorUnchanged /ColorACSImageDict << /VSamples [ 2 1 1 2 ] /HSamples [ 2 1 1 2 ] /QFactor 10 /Blend 1 >> /CompatibilityLevel 1.4  >> setdistillerparams ' ";
//				break;
//				case 'medium':
//					$imgqc = " -c '.setpdfwrite << /ColorConversionStrategy /LeaveColorUnchanged /ColorACSImageDict << /VSamples [ 2 1 1 2 ] /HSamples [ 2 1 1 2 ] /QFactor 2 /Blend 1 >> /CompatibilityLevel 1.4  >> setdistillerparams ' ";
//				break;
//				case 'good':
//					$imgqc = " -c '.setpdfwrite << /ColorConversionStrategy /LeaveColorUnchanged /ColorACSImageDict << /VSamples [ 2 1 1 2 ] /HSamples [ 2 1 1 2 ] /QFactor 0.4 /Blend 1 >> /CompatibilityLevel 1.4  >> setdistillerparams ' ";
//				break;
//				case 'best':
//					$imgqc = " -c '.setpdfwrite << /ColorConversionStrategy /LeaveColorUnchanged /ColorACSImageDict << /VSamples [ 2 1 1 2 ] /HSamples [ 2 1 1 2 ] /QFactor 0.15 /Blend 1 >> /CompatibilityLevel 1.4  >> setdistillerparams ' ";
//				break;
//			}
//		}

        $imgqc .= " -dDownsampleColorImages=true -dDownsampleGrayImages=true -dDownsampleMonoImages=true ";

        $shell .= $imgqc;

        //$imgr = $req->post("image_resolution");
        switch ($imgr) {
            case '36':
                $imgrc = "-dColorImageResolution=55 -dGrayImageResolution=55 -dMonoImageResolution=55";
                break;

            default:
            case '72':
                $imgrc = "-dColorImageResolution=72 -dGrayImageResolution=72 -dMonoImageResolution=72";
                break;
            case '144':
                $imgrc = "-dColorImageResolution=144 -dGrayImageResolution=144 -dMonoImageResolution=144";
                break;
            case '288':
                $imgrc = "-dColorImageResolution=288 -dGrayImageResolution=288 -dMonoImageResolution=288";
                break;
            case '720':
                $imgrc = "-dColorImageResolution=720 -dGrayImageResolution=720 -dMonoImageResolution=720";
                break;

        }

        $shell .= $imgrc;
        switch ($img_conv) {
            case 'grayscale':
                $imgg = " -sProcessColorModel=DeviceGray -sColorConversionStrategy=Gray ";
                break;
            default:
                $imgg = "";
                break;
        }

        $cs = $req->post("compression_speed");
        switch ($cs) {
            default:
            case 'normal':
                $shell .= " -dMonoImageDownsampleThreshold=1 -dGrayImageDownsampleThreshold=1 -dColorImageDownsampleThreshold=1 ";
                break;
            case 'fast':
                $shell .= " -dMonoImageDownsampleThreshold=1.5 -dGrayImageDownsampleThreshold=1.5 -dColorImageDownsampleThreshold=1.5 ";
                break;
        }
        if (is_file($dest_file)) {
            unlink($dest_file);
        }


        $pwd = "";
        if ($password = $req->post("pdf_password")) {
            $pwd = escapeshellarg("--sPDFPassword=$password");
        }


        $shell .= $imgg;

        $shell .= " -f $file $pwd";

//		if(isset($_COOKIE['dbg_delete'])){
//			exit($shell);
//		}

        $x = shell_exec($shell);
        $new_file_name = EditPdf::getNewFileName($doc['original_name'], "compressed", ".pdf");


        shell_exec("sudo chmod 777 $file_dest");


        //exit($original_file);
        $size_before = filesize($original_file);
        $size_after = filesize($dest_file);


        Document::where('id', $doc['id'])->update([
                'edited_document' => $dest_file,
                "download_name" => $new_file_name,
                "delete_after" => (time() + 18000)]
        );


        if ($size_after > $size_before) {
            $shell = "sudo mutool clean -ggg $original_file $dest_file.x 2>&1";
            shell_exec($shell);
            $size_after = filesize("$dest_file.x");
            rename("$dest_file.x", $dest_file);
        }

        return response()->json(['success' => true,
            "s1" => $size_before,
            "s2" => $size_after,
            'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))
        ]);
    }

    public function rotate(Request $req)
    {
        $pages = $req->post("pages");
        $uuid = str_replace(array("/", "~", "."), "-", $req->post("UUID"));

        $operation_type = "rotatepdf";
        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $file_dest = $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pdf"));

        $doc = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type])->orderBy('ID', 'desc')->first();

        if (!$doc) return response()->json(['success' => false, "message" => "Operation not found"]);

        $doc = $doc->toArray();

        $file = $original_file;

        $pc = "";
        foreach ($pages as $pn => $rotate) {
            $pn = (int)$pn;
            if ($rotate < 0) {
                $rotate = 360 - ($rotate * -1);
            }
            switch ($rotate) {
                default:
                case '0':
                    $pc .= "{$pn} ";
                    break;
                case '90':
                    $pc .= "{$pn}east ";
                    break;
                case '180':
                    $pc .= "{$pn}south ";
                    break;
                case '270':
                    $pc .= "{$pn}west ";
                    break;
            }
        }

        if (is_file($file_dest)) unlink($file_dest);

        $pwd = "";
        $password = $req->post("pdf_password");
        if ($password and $password != 'false') $pwd = "input_pw $password";

        $shell = "pdftk $file $pwd cat $pc output $file_dest 2>&1";
        shell_exec($shell);

		$dest_file = \App\Custom\PDFHelpers::replacePages($dest_file);

        $new_file_name = EditPdf::getNewFileName($doc['original_name'], "rotated", ".pdf");

        Document::where('id', $doc['id'])->update([
            'edited_document' => $dest_file,
            "download_name" => $new_file_name,
            "delete_after" => (time() + 18000)
        ]);

        return response()->json([
            'success' => true,
            'new_file_name' => $new_file_name,
            'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type)),
            "shell" => $shell
        ]);
    }


    public function burst(Request $req)
    {
        $uuid = $req->post("uuid");
        $operation_type = "splitpdf";
        $pages_list = array();
        $doc = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type])->orderBy('ID', 'desc')->first()->toArray();

        $file_name_db = ($doc['original_name']);


        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".zip"));
        if (!is_file($original_file)) {
            return response()->json(['success' => false, 'message' => 'Original file not found...']);
        }

        $zip = new ZipArchive();
        $temp = storage_path("pdfburst/$uuid/");

        File::deleteDirectory($temp);

        if (!is_dir($temp)) {
            if (!File::makeDirectory($temp)) {
                return response()->json(['success' => false, 'message' => 'Cant create temp folder...']);
            }
        }

        $name_patern = ($req->post("name_patern") ? $req->post("name_patern") : "[CURRENTPAGE]-[BASENAME]");

        $pwd = "";
        if ($password = $req->post("pdf_password")) {
            $pwd = "input_pw $password";
        }

        switch ($req->post("type")) {
            default:
            case 'everyPage':
                $shell_b = "nice -n 19 pdftk $original_file $pwd  burst output $temp/page%2d.pdf";
                shell_exec($shell_b);
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
                    $ranges = explode(",", $ranges);
                    $pn = 1;
                    $shell_cat = "nice -n 19 pdftk $original_file $pwd cat %s output  $temp/page-%d.pdf";
                    foreach ($ranges as $r) {
                        $shell = sprintf($shell_cat, escapeshellarg($r), ($pn));
                        shell_exec($shell);
                        $pn++;
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

        if ($zip->open($dest_file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
            return response()->json(['success' => false, 'message' => 'Failed to create zip archive...']);
        }

        $pi = 1;
        foreach ($pages_list as $page) {
            $file_name = $name_patern;
            $of = pathinfo($file_name_db)['filename'];


            $file_name = str_replace("[BASENAME]", $of, $file_name);
            $file_name = str_replace("[CURRENTPAGE]", $pi, $file_name);
            $zip->addFile($page, $file_name . ".pdf");
            $pi++;
        }
        $zip->close();
        File::deleteDirectory($temp);

        $new_file_name = EditPdf::getNewFileName($doc['original_name'], "split", ".zip");

        Document::where('id', $doc['id'])->update(['edited_document' => $dest_file,
            "download_name" => $new_file_name,
            "delete_after" => (time() + 18000)]);

        return response()->json(['success' => true, 'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);
    }


    public function crop(Request $req)
    {
        $uuid = $req->post("uuid");
        $operation_type = "croppdf";
        $crop_pages = ($req->post("pages"));
        $pages_list = array();
        $doc = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type])->orderBy('ID', 'desc')->first()->toArray();


        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pdf"));
        if (!is_file($original_file)) {
            return response()->json(['success' => false, 'message' => 'Original file not found...']);
        }

        shell_exec("pdftocairo -pdf $original_file {$original_file}_cleaned");
        shell_exec("sudo chmod 777 {$original_file}_cleaned");
        copy("{$original_file}_cleaned", $original_file);
        ///(?:.*)obj([\w\W]*?)\/Page$([\w\W]*?)\/MediaBox([\w\W]*?)endobj


        $fc = file_get_contents($original_file);


        $pattern = "/^(?:.*)obj\R(?:.*)(\/MediaBox[\n\r\s]?+\[[\n\r\s]?+(\d+)[\n\r\s]?+(\d+)[\n\r\s]?+(\d+)[\n\r\s]?+(\d+)\])(?:.*)\Rendobj\R/m";


        $mp = "\/MediaBox[\n\r\s]?+\[[\n\r\s]?+(\d+)[\n\r\s]?+(\d+)[\n\r\s]?+(\d+)[\n\r\s]?+(\d+)[\n\r\s]?+\]";
        $pattern = "/^(?:.*)obj([\w\W]*?)\/Page$([\w\W]*?)$mp([\w\W]*?)endobj/m";

        $pattern = "/\d \d obj\n<< \/Type \/Page([\w\W]*?)(\/MediaBox[ \s]?+\[[ \s]?+(\d+\.?\d*)[ \s]?+(\d+\.?\d*)[ \s]?+(\d+\.?\d*)[ \s]?+(\d+\.?\d*)[ \s]?+\])([\w\W]*?)endobj/m";


        $result = preg_match_all($pattern, $fc, $matches);


//		echo "<pre>";
//
//		var_dump($matches);
//		exit();
//
//
//		exit($fc);


        $lines = preg_split("/(\r\n|\n|\r)/", $fc);

        foreach ($matches[0] as $pn => $m) {
            $page_num = $pn + 1;
            if ($req->post("for_all_pages") == 'true') {
                $crop_pages[$page_num] = $crop_pages[1];
            }

            if (isset($crop_pages[$page_num])) {
                if ((int)$crop_pages[$page_num]['rotation']) {
                    switch ($crop_pages[$page_num]) {
                        //TODO найти страницы с 180/270
                        default:
                        case '90':
                            $new_left = $crop_pages[$page_num]['top'] * 72;  //TODO теперь это топ
                            $new_bot = $crop_pages[$page_num]['left'] * 72;//TODO теперь это лево
                            $new_w = $matches[5][$pn] - ($crop_pages[$page_num]['bottom'] * 72); //TODO теперь это бот
                            $new_h = $matches[6][$pn] - ($crop_pages[$page_num]['right'] * 72); //TODO теперь это ширина
                            break;
                    }
                } else {
                    $new_left = $crop_pages[$page_num]['left'] * 72;
                    $new_bot = $crop_pages[$page_num]['bottom'] * 72;
                    $new_w = $matches[5][$pn] - ($crop_pages[$page_num]['right'] * 72);
                    $new_h = $matches[6][$pn] - ($crop_pages[$page_num]['top'] * 72);
                }
                $new_m = str_replace($matches[2][$pn],
                    "/MediaBox [$new_left $new_bot $new_w $new_h]", $m);

                $fc = str_replace($m, $new_m, $fc);
            }
        }
        $content = $fc;

        file_put_contents($dest_file, $content);

		$dest_file = \App\Custom\PDFHelpers::replacePages($dest_file);

        $new_file_name = EditPdf::getNewFileName($doc['original_name'], "cropped", ".pdf");

        Document::where('id', $doc['id'])->update(['edited_document' => $dest_file,
            "download_name" => $new_file_name,
            "delete_after" => (time() + 18000)]);
        return response()->json(['success' => true, 'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);
    }

    public function resize(Request $req)
    {
        //shell_
        $uuid = $req->post("uuid");
        $operation_type = "resizepdf";
        $crop_pages = ($req->post("pages"));
        $pages_list = array();
        $doc = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type])->orderBy('ID', 'desc')->first()->toArray();

        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pdf"));
        if (!is_file($original_file)) {
            return response()->json(['success' => false, 'message' => 'Original file not found...']);
        }
        if (is_file($dest_file)) {
            unlink($dest_file);
        }

        //sl


        $pwd = "";
        if ($password = $req->post("pdf_password")) {
            $pwd = escapeshellarg("--sPDFPassword=$password");
        }


        if ($req->post("resize_type") == 'paper') {
            $new_paper_size = escapeshellarg("{" . $req->post("new_paper_size") . "}");

            $shell = ("pdfjam --outfile $dest_file --papersize {$new_paper_size} $original_file");

            shell_exec($shell);
        } else {

            $fc = file_get_contents($original_file);

            //file_put_contents("/var/www/html/pdf-magic/public/test.txt", $fc);


            $pattern = "/^(?:.*)obj\R(?:.*)(\/MediaBox \[[\n\r\s]?+(\d+)[\n\r\s]?+(\d+)[\n\r\s]?+(\d+)[\n\r\s]?+(\d+) \])(?:.*)\Rendobj\R/s";
            $pattern = "/^(?:.*)obj\R(?:.*)(\/MediaBox[\n\r\s]?+\[[\n\r\s]?+(\d*\.?\d+)[\n\r\s]?+(\d*\.?\d+)[\n\r\s]?+(\d*\.?\d+)[\n\r\s]?+(\d*\.?\d+)[\n\r\s]?+\])(?:.*)\Rendobj\R/su";

            $pattern = "/(\/MediaBox[\n\r\s]?+\[[\n\r\s]?+(\d*\.?\d+)[\n\r\s]?+(\d*\.?\d+)[\n\r\s]?+(\d*\.?\d+)[\n\r\s]?+(\d*\.?\d+)[\n\r\s]?+\])/";

            #$result = preg_match_all($pattern, $fc, $matches);

            $result = preg_match_all($pattern, $fc, $matches);

            $lines = preg_split("/(\r\n|\n|\r)/", $fc);


            foreach ($matches[0] as $pn => $m) {
                $page_num = $pn + 1;
                if ($req->post("for_all_pages") == 'true') {
                    $crop_pages[$page_num] = $crop_pages[1];
                }

                if (isset($crop_pages[$page_num])) {
                    if ((int)$crop_pages[$page_num]['rotation']) {
                        switch ($crop_pages[$page_num]) {
                            //TODO найти страницы с 180/270
                            default:
                            case '90':
                                $new_left = $crop_pages[$page_num]['new']['top'] * 72;  //TODO теперь это топ
                                $new_bot = $crop_pages[$page_num]['new']['left'] * 72;//TODO теперь это лево
                                $new_w = $matches[4][$pn] - ($crop_pages[$page_num]['new']['bottom'] * 72); //TODO теперь это бот
                                $new_h = $matches[5][$pn] - ($crop_pages[$page_num]['new']['right'] * 72); //TODO теперь это ширина
                                break;
                        }
                    } else {
                        $new_left = -($crop_pages[$page_num]['new']['left']);
                        $new_bot = -($crop_pages[$page_num]['new']['bottom']);
                        $new_w = ($matches[4][$pn] + ($crop_pages[$page_num]['new']['right']));
                        $new_h = ($matches[5][$pn] + ($crop_pages[$page_num]['new']['top']));

                    }


                    $new_m = str_replace($matches[1][$pn],
                        "/MediaBox [$new_left $new_bot $new_w $new_h]", $m);
                    $fc = str_replace($m, $new_m, $fc);
                }
            }


            $fc = preg_replace("/\/CropBox.*(?=])]/U", "", $fc);

            $content = $fc;
            file_put_contents($dest_file, $content);
            //file_put_contents("/var/www/html/pdf-magic/public/test.pdf", $content);

        }
        if (!is_file($dest_file)) {
            return response()->json(["success" => false, "message" => "Error 1"]);
        }
        $new_file_name = EditPdf::getNewFileName($doc['original_name'], "resized", ".pdf");
        Document::where('id', $doc['id'])->update(['edited_document' => $dest_file,
            "download_name" => $new_file_name,
            "delete_after" => (time() + 38000)]);
        return response()->json(['success' => true, 'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);
    }


    public function mix(Request $req)
    {
        $uuid = $req->post("uuid");
        $operation_type = "mixpdf";
        $files = ($req->post("files"));
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pdf"));

        $operation_id = $req->post("operation_id");
        $shell = "";
        $total_pages = 0;

        $docs = Document::where([
            'UUID' => $uuid,
            'operation_type' => $operation_type,
            'operation_id' => $operation_id,
        ])->orderBy('ID', 'desc')->get()->toArray();

        foreach ($files as $pp => $file) {
            $pos = array_search($file['name'], array_column($docs, 'original_name'));
            if ($pos === false) {
                exit("error 1");
            }

            $fp = public_path($docs[$pos]['original_document']);
            if (!is_file($fp)) {
                return response()->json(['success' => false, "message" => "file $file not found"]);
            }

            $total_pages += $file['pages_count'];
            $files[$pp]['pp'] = 0;
            $files[$pp]['name'] = $fp;
        }


        $letters = range("A", "Z");
        $pages_iterator = 0;
        $breaker = true;
        $shell = "";
        $files_letters = [];
        while ($breaker) {
            $flag = false;
            foreach ($files as $kf => $file) {
                $letter = $letters[$kf - 1];
                $files_letters[$letter] = $file['name'];
                $temp = "";
                $files[$kf]['letter'] = $letter;
                if ($file['pp'] <= $file['pages_count']) {
                    if ($file['pages_ordering'] == 'reverse') {
                        for ($p = 1; $p != $file['document_switch'] + 1; $p++) {
                            if ($file['pp'] + $p < $file['pages_count'] + 1) {
                                $temp .= "{$letter}" . (($file['pages_count'] + 1) - $file['pp'] - $p) . " ";
                            }
                            $files[$kf]['pp']++;
                        }
                        $flag = false;
                    } else {
                        for ($p = 1; $p != $file['document_switch'] + 1; $p++) {
                            if ($file['pp'] + $p < $file['pages_count'] + 1) {
                                $temp .= "{$letter}" . ($file['pp'] + $p) . " ";
                            }
                            $files[$kf]['pp']++;
                        }
                        $flag = false;
                    }
                } else {
                    $flag = true;
                }
                $shell .= $temp;
            }

            if ($flag) {
                $breaker = false;
            }
            $pages_iterator++;
            if ($pages_iterator > 400) {
                exit("error");
            }
        }
        foreach ($files as $file) {
            if ($file['pages_count'] >= $file['pp']) {
                if ($file['pages_ordering'] == 'regular') {
                    $f = $file['pp'] + 1;
                    $s = "{$file['letter']}{$f}-{$file['pages_count']}";
                } else {
                    $f = $file['pages_count'] - $file['pp'] + 1;
                    $s = "{$file['letter']}$f-1";
                }
                $shell .= $s;
            }
        }

        $tsh = "";
        foreach ($files_letters as $l => $f) {
            $tsh .= "$l=$f ";
        }

        $pwd = "";
        if ($password = $req->post("pdf_password")) {
            $pwd = "input_pw $password";
        }

        $shell = escapeshellcmd("nice -n 19 pdftk " . $tsh . " $pwd shuffle " . $shell . " output $dest_file ");


        $x = shell_exec($shell . "2>&1");
        if ($x !== null) {
            response()->json(['success' => false]);
        }
        $new_file_name = "mixed_pdf_" . rand(1, 100) . ".pdf";
        foreach ($docs as $doc) {
            Document::where(['id' => $doc['id']])->update(['edited_document' => $dest_file,
                "download_name" => $new_file_name,
                "delete_after" => (time() + 18000)]);
        }

        return response()->json(['success' => true, 'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);
    }

    public function extractPages(Request $req)
    {
        $uuid = $req->post("uuid");
        $operation_type = "extractpdf";
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pdf"));
        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $ranges = $req->post("ranges");
        if ($ranges == 'false' || !$ranges) {
            return response()->json(['success' => false, 'message' => 'Pages not selected']);
        }

        $operation_id = $req->post("operation_id");
        $shell = "";
        $total_pages = 0;


        $doc = Document::where([
            'UUID' => $uuid,
            'operation_type' => $operation_type,
            'operation_id' => $operation_id,
        ])->orderBy('ID', 'desc')->first()->toArray();

        if (!$doc) {
            return response()->json(['success' => false, 'message' => 'Original file not found']);
        }


        $pwd = "";
        if ($password = $req->post("pdf_password")) {
            $pwd = "input_pw $password";
        }


        $pages = str_replace(",", " ", $ranges);

        $shell = escapeshellcmd("nice -n 19 pdftk $original_file $pwd cat $pages output $dest_file ");
        shell_exec($shell);

        if (!(int)$req->post("discard_bookmarks")) {
            $shell = escapeshellcmd("pdftk $original_file $pwd dump_data output $dest_file.info");
            shell_exec($shell);
            $shell = ("nice -n 19 pdftk $dest_file $pwd update_info $dest_file.info output $dest_file.tmp");
            shell_exec($shell);
            rename("$dest_file.tmp", $dest_file);
        }

        $new_file_name = EditPdf::getNewFileName($doc['original_name'], "exploded", ".pdf");
        Document::where('id', $doc['id'])->update(['edited_document' => $dest_file,
            "download_name" => $new_file_name,
            "delete_after" => (time() + 18000)]);
        return response()->json(['success' => true, 'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);

    }

    public function extractByOutline(Request $req)
    {
        $uuid = $req->post("uuid");
        $operation_id = $req->post("operation_id");
        $operation_type = "splitoutlinepdf";
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pdf"));
        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $pages = $req->post("pages");
        if ($pages == 'false' || !$pages) {
            return response()->json(['success' => false, 'message' => 'Bookmarks not selected']);
        }

        $doc = Document::where([
            'UUID' => $uuid,
            'operation_type' => $operation_type,
            'operation_id' => $operation_id,
        ])->orderBy('ID', 'desc')->first()->toArray();

        if (!$doc) {
            return response()->json(['success' => false, 'message' => 'Original file not found']);
        }

        $zip = new ZipArchive();
        $temp = storage_path("splitbypage/$uuid/");

        File::deleteDirectory($temp);

        if (!is_dir($temp)) {
            if (!File::makeDirectory($temp)) {
                return response()->json(['success' => false, 'message' => 'Cant create temp folder...']);
            }
        }


        $pwd = "";
        if ($password = $req->post("pdf_password")) {
            $pwd = "input_pw $password";
        }


        $files = array();
        $fn = 1;
        foreach ($pages as $page) {

            $file_name = ("{$page['title']}");
            $shell_cat = escapeshellcmd("nice -n 19 pdftk $original_file $pwd cat {$page['page_num']} output  \"{$temp}{$file_name}\"");
            shell_exec($shell_cat);
            $files[] = array("path" => "{$temp}{$file_name}", "name" => $file_name);
            $fn++;
        }


        if ($zip->open($dest_file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
            return response()->json(['success' => false, 'message' => 'Failed to create zip archive...']);
        }

        $name_patern = $req->post("name_patern") ? $req->post("name_patern") : "[FILENUMBER]_[BOOKMARK_NAME_STRICT]";
        $name_patern = str_replace(array("/", "\\", ".", "..",), "", $name_patern);

        $pi = 1;
        foreach ($files as $page) {
            $file_name = $name_patern;
            //$of = pathinfo($req->post("file_name"))['filename'];
            $file_name = str_replace("[FILENUMBER]", $pi, $file_name);
            $file_name = str_replace("[BOOKMARK_NAME_STRICT]", $page['name'], $file_name);

            $zip->addFile($page['path'], $file_name . ".pdf");
            $pi++;
        }
        $zip->close();
        File::deleteDirectory($temp);

        $new_file_name = EditPdf::getNewFileName($doc['original_name'], "split_by_bookmark", ".zip");

        Document::where('id', $doc['id'])->update(['edited_document' => $dest_file,
            "download_name" => $new_file_name,
            "delete_after" => (time() + 18000)]);

        return response()->json(['success' => true, 'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);
    }


    public function epub2pdf(Request $request)
    {
        $uuid = $request->UUID;
        $operation_type = "epubtopdf";
        
        //exit

        $docBuilder = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type]);

        foreach ($request->input('files') as $index => $filePostfix) {
            if ($index === 0) {
                $docBuilder->where('original_document', 'like', 'uploads/epub-to-pdf/' . $uuid . '/tmp/' . $uuid . '-' . $filePostfix . '%');
            } else {
                $docBuilder->orWhere('original_document', 'like', 'uploads/epub-to-pdf/' . $uuid . '/tmp/' . $uuid . '-' . $filePostfix . '%');
            }
        }

        $doc = $docBuilder->get()->toArray();

        if (!$doc) return response()->json(['success' => false, "message" => "Operation not found"]);

        $edited_documents_path = [];

        foreach ($doc as $singleDoc) {
            $original_file = $singleDoc['original_document'];
            if (!Storage::exists($original_file)) return response()->json(['success' => false, "message" => "File not found"]);

            $original_file_storage_path = storage_path('app/' . $original_file);
            $outputName = Str::random();
            $dest_file_storage_path = storage_path('app/' . 'uploads/epub-to-pdf/' . $uuid . '/tmp/' . $outputName . '.pdf');
			//dd("ebook-convert $original_file_storage_path $dest_file_storage_path --pdf-hyphenate");
            $shell = ("ebook-convert $original_file_storage_path $dest_file_storage_path");
            shell_exec($shell);

            $edited_documents_path[] = $dest_file_storage_path;

            Document::where(['id' => $singleDoc['id']])->update([
                'edited_document' => str_replace('app/', '', str_replace('/home/admin/public_html', '', $dest_file_storage_path)),
                "delete_after" => (time() + 18000)
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
        //dd($cmd);
        shell_exec($cmd);

		$outputPath = \App\Custom\PDFHelpers::replacePages($outputPath);
		$outputPath = str_replace(base_path("public"), '', $outputPath);

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

    /*
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

        $file = str_replace('/var/www/freeconvert/public', '', $dest_file);
        Document::where(['id'=>$doc['id']])->update(['edited_document' => $dest_file, "delete_after"=>(time()+18000)]);

        return response()->json(['status'=>'success', 'filename'=>EditPdf::getNewFileName($doc['original_name'], "", ".epub"), 'file'=>$file]);
    }
	*/

	public function pdf2epub(Request $req){
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$single_page_extractor = (int)Request()->input("single_page_extractor");

		$uuid = $req->post("uuid");
		$operation_type = "pdf2epub";
		//$original_file = EditPdf::getFilePath($uuid, $operation_type);
		$dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".epub"));
		$original_file = $dest_file_merged = (EditPdf::getDestPath($uuid, $operation_type, "_merged.pdf"));
		$dest_file_epub = (EditPdf::getDestPath($uuid, $operation_type, ".epub"));

		$operation_id = $req->post("operation_id");

		$shell = "";
		$total_pages = 0;

		$doc = Document::where([
			'UUID'=>$uuid,
			'operation_type'=>$operation_type,
			'operation_id'=>$operation_id,
			//])->orderBy('ID', 'desc')->first();
		])->orderBy('ID', 'asc')->get();

		if(!$doc){
			return response()->json(['success'=>false, "message"=>"Operation not found"]);
		}

		$doc = $doc->toArray();
		$all_files = [];
		$base = base_path("/public/");

		if($single_page_extractor){
			$single_doc_path = $base.$doc[$req->document]['original_document'];
			$merge_shell = "pdftk $single_doc_path cat {$req->page} output $dest_file_merged";
			shell_exec($merge_shell);
		}else{
			foreach($doc as  $d){
				$or = $base.$d['original_document'];
				$all_files[] = $or;
			}
			$merge_shell = "pdftk ".implode(" ", $all_files)." cat output $dest_file_merged";
			shell_exec($merge_shell);
		}

		if(!is_file($original_file)){
			return response()->json(['success'=>false, "message"=>"File not found"]);
		}

		$temp = storage_path("pdfburst/$uuid/");
		File::deleteDirectory($temp);

		//$oldmask = umask(0);
		//mkdir($temp, 0777);
		//umask($oldmask);

//  	shell_exec("sudo chmod -R 777 $temp");

		if(!is_dir($temp)){
			if(!File::makeDirectory($temp)){
				return response()->json(['success'=>false, 'message'=>'Cant create temp folder...']);
			}
		}

		$shell = escapeshellcmd("ebook-convert  $original_file $dest_file --enable-heuristics --title $temp");

		//dd($shell);
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
//  	//rename($or2, $dest_file);
//		rename($or2, $dest_file);

		$file = str_replace('/home/admin/public_html/public', '', $dest_file);

		//Document::where(['id'=>$doc['id']])->update(['edited_document' => $dest_file, "delete_after"=>(time()+18000)]);
		foreach($doc as $d){
			Document::where(['id'=>$d['id']])->update(['edited_document' => $dest_file, "delete_after"=>(time()+18000)]);
		}

		//return response()->json(['status'=>'success', 'filename'=>EditPdf::getNewFileName($doc['original_name'], "", ".epub"), 'file'=>$file]);
		return response()->json(['status'=>'success', 'success'=>true,
								 'url'=>$file,
								 'new_file_name'=>EditPdf::getNewFileName($doc[0]['original_name'], "", ".epub"),
								 'filename'=>EditPdf::getNewFileName($doc[0]['original_name'], "", ".epub"), 'file'=>$file]);
	}

    public function pdf2word(Request $request)
    {
        $uuid = $request->UUID;
        $operation_type = "pdftoword";

        $docBuilder = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type]);

        foreach ($request->input('files') as $index => $filePostfix) {
            if ($index === 0) {
                $docBuilder->where('original_document', 'like', 'uploads/pdf-to-word/' . $uuid . '/tmp/' . $uuid . '-' . $filePostfix . '%');
            } else {
                $docBuilder->orWhere('original_document', 'like', 'uploads/pdf-to-word/' . $uuid . '/tmp/' . $uuid . '-' . $filePostfix . '%');
            }
        }

        $doc = $docBuilder->get()->toArray();

        if (!$doc) return response()->json(['success' => false, "message" => "File not found"]);

        $outputFilename = Str::random() . '.pdf';
        $dest_file_storage_path = storage_path('app/' . 'uploads/pdf-to-word/' . $uuid . '/tmp/' . $outputFilename);
        $mergePdfsCommand = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$dest_file_storage_path ";

        foreach ($doc as $singleDoc) {
            $original_file = $singleDoc['original_document'];
            if (!Storage::exists($original_file)) return response()->json(['success' => false, "message" => "File not found"]);

            $original_file_storage_path = storage_path('app/' . $original_file);
            $mergePdfsCommand .= $original_file_storage_path . ' ';

            Document::where([
                'id' => $singleDoc['id'],
                'operation_id' => $singleDoc['operation_id'],
                'operation_type' => $operation_type,
            ])->update([
                'edited_document' => $dest_file_storage_path,
                'delete_after' => (time() + 18000),
            ]);
        }

        $process = new Process($mergePdfsCommand);
        $process->run();

        $outputFilePath = storage_path('app/' . 'uploads/pdf-to-word/' . $uuid . '/tmp/');
		//dump("soffice --infilter='writer_pdf_import' --convert-to doc $dest_file_storage_path --outdir $outputFilePath");
		//dd("soffice --infilter='writer_pdf_import' --convert-to doc:\"MS Word 97\" $dest_file_storage_path --outdir $outputFilePath");
		//shell_exec("chmod 0775 $outputFilePath 2>&1");
        //$process = new Process("soffice --infilter='writer_pdf_import' --convert-to doc $dest_file_storage_path --outdir $outputFilePath");
        //$process->run();
		//dd(shell_exec("soffice --infilter='writer_pdf_import' --convert-to doc $dest_file_storage_path --outdir $outputFilePath 2>&1"));
		shell_exec("soffice --infilter='writer_pdf_import' --convert-to doc $dest_file_storage_path --outdir $outputFilePath 2>&1");

        $downloadFileName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $outputFilename) . '.doc';
        Document::create([
            'UUID' => $uuid,
            'operation_id' => $doc[0]['operation_id'],
            'operation_type' => $operation_type,
            'original_document' => $dest_file_storage_path,
            'original_name' => '',
            'download_name' => $downloadFileName,
            'edited_document' => $outputFilePath . $downloadFileName,
            'delete_after' => (time() + 18000),
        ]);

        return response()->json([
            'status' => 'success',
            'new_file_name' => $downloadFileName,
            'url' => str_replace('/home/admin/public_html', '', str_replace('app/', '', $outputFilePath . $downloadFileName)),
        ]);
    }


    public function encrypt(Request $req)
    {
        $uuid = $req->post("uuid");
        $operation_type = "encryptpdf";
        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pdf"));
        $operation_id = $req->post("operation_id");

        $doc = Document::where([
            'UUID' => $uuid,
            'operation_type' => $operation_type,
            'operation_id' => $operation_id,
        ])->orderBy('ID', 'desc')->first()->toArray();

        if (!$doc) {
            return response()->json(['success' => false, "message" => "Operation not found"]);
        }

        $shell = "";

        $permissions = array("edit", "copy", "print", "highprint", "editnotes", "fillandsign", "assemble");
        $encrypt = array("rc4v2", "aesv2", "aesv3");

        $options = $req->post("options");
        $password_1 = $options["password_open"];
        $password_2 = $options["password_own"];
        $enc = $options["encrypt"];

        $allow_shell = "";
        if (!empty($options["allow"])) {
            foreach ($options["allow"] as $allow) {
                if (!in_array($allow, $permissions)) {
                    return response()->json(['success' => false, "message" => "Unknown permission"]);
                }
                $allow_shell .= "--$allow ";
            }
        }
        if (!in_array($enc, $encrypt)) {
            return response()->json(['success' => false, "message" => "Unknown encryption"]);
        }
        $p1 = "";
        $p2 = "";
        if ($password_1) {
            $p1 = "-o " . escapeshellarg($password_1) . "";
        }
        if (!$password_1) {
            $p1 = "-o " . escapeshellarg($password_2) . "";
        }


        if ($password_2) {
            $p2 .= " -u " . escapeshellarg($password_2) . "";
        } else {
            $p1 .= " -u " . escapeshellarg($password_1) . "";
        }

        if (!$password_1 && !$password_2) {
            $p1 = "";
            $p2 = "";
            $opts = [];

            if ($req->post("pdf_password") && !in_array($req->post("pdf_password"), ["false", ""])) {
                $current_password = $req->post("pdf_password");
                $remove_password = "pdftk $original_file input_pw $current_password output $dest_file";
                shell_exec($remove_password);

                copy($dest_file, $original_file);
            }


            if (!empty($options['allow'])) {
                foreach ($options["allow"] as $allow) {
                    if (!in_array($allow, $permissions)) {
                        return response()->json(['success' => false, "message" => "Unknown permission"]);
                    }

                    switch ($allow) {
                        case 'edit':
                            $opts[] = "--modify=all";
                            break;
                        case 'copy':
                            //$opts[] = "--print=y";
                            break;
                        case 'print':
                            $opts[] = "--print=low";
                            break;
                        case 'highprint':
                            $opts[] = "--print=full";
                            break;
                        case 'editnotes':
                            $opts[] = "--print=full";
                            break;
                        case 'fillandsign':

                            break;
                        case 'assemble':

                            break;
                    }
                }
            }
            $pa = implode(" ", $opts);

            $shell = "qpdf --encrypt \"\" \"\" 128 $pa -- $original_file $dest_file";
            shell_exec($shell);
        } else {
            if ($req->post("pdf_password") && !in_array($req->post("pdf_password"), ["false", ""])) {
                $current_password = $req->post("pdf_password");
                $remove_password = "pdftk $original_file input_pw $current_password output $dest_file";
                shell_exec($remove_password);

                copy($dest_file, $original_file);
            }

			$original_file = \App\Custom\PDFHelpers::replacePages($original_file);

            $shell = "podofoencrypt --$enc $p1 $p2 $allow_shell $original_file $dest_file 2>&1";
            $x = shell_exec($shell);
        }

        $new_file_name = EditPdf::getNewFileName($doc['original_name'], "encrypted", ".pdf");

        Document::where(['id' => $doc['id']])->update([
            'edited_document' => $dest_file,
            "download_name" => $new_file_name,
            "delete_after" => (time() + 18000)
        ]);
        return response()->json(['success' => true, 'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);
    }


    public function splitBySize(Request $req)
    {
        $uuid = $req->post("uuid");
        $operation_type = "splitbysizepdf";
        $pages_list = array();
        $doc = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type])->orderBy('ID', 'desc')->first()->toArray();

        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".zip"));
        if (!is_file($original_file)) {
            return response()->json(['success' => false, 'message' => 'Original file not found...']);
        }

        $zip = new ZipArchive();
        $temp = storage_path("pdfburst/$uuid/");

        File::deleteDirectory($temp);

        if (!is_dir($temp)) {
            if (!File::makeDirectory($temp)) {
                return response()->json(['success' => false, 'message' => 'Cant create temp folder...']);
            }
        }

        $pwd = "";
        if ($password = $req->post("pdf_password")) {
            $pwd = "input_pw $password";
        }

        $shell = "nice -n 19 pdftk $original_file $pwd burst output $temp/%d.pdf";
        shell_exec($shell);
        File::delete("$temp/doc_data.txt");

        $pages = File::files($temp, "name");
        foreach ($pages as $p) {
            $pages_list[] = array("path" => $p->getPathName(), "size" => $p->getSize());
        }
        usort($pages_list, function ($a, $b) {
            return ($a['path'] <=> $b['path']);
        });

        $unit = $req->post("unit");
        $size = (float)$req->post("split_size");

        $combined_arr = array();


        usort($pages_list, function ($a, $b) {
            preg_match("/.*\/(\d+)\.pdf/", $a['path'], $n1);
            preg_match("/.*\/(\d+)\.pdf/", $b['path'], $n2);

            return $n1[1] <=> $n2[1];

            return strcmp($a["path"], $b["path"]);
        });


        $cat_size = 0;
        $new_f = 1;
        foreach ($pages_list as $pn => $page) {
            $psz = $page['size'] / 1024;
            $npsz = 0;
            if (isset($pages_list[$pn + 1])) {
                $npsz = $pages_list[$pn]['size'] / 1024;
                if ($unit == 'MB') {
                    $npsz = $npsz / 1024;
                }
            }
            if ($unit == 'MB') {
                $psz = $psz / 1024;
            }
            if (!isset($combined_arr[$new_f])) {
                $combined_arr[$new_f] = [];
            }
            $cat_size += $psz;

            if ($cat_size >= $size || $psz >= $size || (isset($pages_list[$pn + 1]) and ($cat_size + $npsz) >= $size)) {
                $cat_size = 0;
                $combined_arr[$new_f][] = $page;
                $new_f++;
                //exit("add to array ");
            } else {
                $combined_arr[$new_f][] = $page;
            }
        }


        if ($zip->open($dest_file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
            return response()->json(['success' => false, 'message' => 'Failed to create zip archive...']);
        }

        $tmp_files = array();
        $fn = 1;

        foreach ($combined_arr as $car) {
            $shell = "nice -n 19 pdftk ";
            foreach ($car as $page) {
                $shell .= $page['path'] . " ";
            }
            $tmp_name = "$dest_file.$fn";
            $shell .= " cat output $tmp_name";
            shell_exec($shell);
            $tmp_files[] = $tmp_name;


            $zip->addFile($tmp_name, "$fn-pdf.pdf");
            $fn++;
        }

        $zip->close();
        foreach ($tmp_files as $f) {
            File::delete($f);
        }

        $new_file_name = EditPdf::getNewFileName($doc['original_name'], "split_by_size", ".zip");

        Document::where(['id' => $doc['id']])->update(['edited_document' => $dest_file,
            "download_name" => $new_file_name,
            "delete_after" => (time() + 18000)]);
        return response()->json(['success' => true, 'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);
    }

    public function headerfooterpdf(Request $req)
    {
        $uuid = $req->post("uuid");
        $operation_type = "headerfooterpdf";
        $data = $req->post("data");
        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pdf"));
        $operation_id = $req->post("operation_id");

        $doc = Document::where([
            'UUID' => $uuid,
            'operation_type' => $operation_type,
            'operation_id' => $operation_id,
        ])->orderBy('ID', 'desc')->first()->toArray();

        if (!$doc) {
            return response()->json(['success' => false, "message" => "Operation not found"]);
        }


        function toRoman($number)
        {
            $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
            $returnValue = '';
            while ($number > 0) {
                foreach ($map as $roman => $int) {
                    if ($number >= $int) {
                        $number -= $int;
                        $returnValue .= $roman;
                        break;
                    }
                }
            }
            return $returnValue;
        }

        $this->getMpdf($original_file);

        $mpdf = $this->mpdf;
        $positions = ["hcenter", "hleft", "hright", "fcenter", "fleft", "fright"];
        $filename = $req->post("file_name");
        $dt1 = $data['text'];
        $dt2 = $data['text2'];

        $start_from_page = (int)$data["start_from_page"];
        $toop = $data['only_on_page'];
        $oop = [];
        if ($toop) {
            if ($toop == 'odd' || $toop == 'even') {
                $oop = $toop;
            } else {
                $unset = array();
                $xoo = explode(",", $toop);
                foreach ($xoo as $k => $o) {
                    $x = explode("-", $o);
                    $oop = array_merge($oop, range($x[0], end($x)));
                }
            }
        }
        foreach (range(1, $this->pagecount) as $page_num) {
            $template_data = $mpdf->ImportPage((int)$page_num, null, null, 0, 0, "/CropBox", false);
            $this->mpdf->addPage("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", false);
            $this->mpdf->UseTemplate($template_data['tplId'], 0, 0);
//			$this->mpdf->htext("XYZ", 0, "courier2", 96);
            $text = "unknown header/footer type";
            switch ($data['header_type']) {
                default:
                case 'hf-pages-arabic':
                    $text = "$dt1 $page_num";
                    break;
                case 'hf-pages-roman':
                    $text = "$dt1 " . toRoman($page_num);
                    break;
                case 'hf-page-of-total':
                    $text = "$dt1 $page_num $dt2 {$this->pagecount}";
                    break;
                case 'hf-filename':
                    $text = $filename;
                    break;
                case 'hf-text-only':
                    $text = $dt1;
                    break;
            }

            if ($page_num >= $start_from_page) {

                if (is_array($oop) and !empty($oop)) {
                    if (in_array($page_num, $oop)) {
                        $this->mpdf->htext($data['color'], $data['location'], $text, $angle = 0, $data['font'], $data['font_size'], 1);
                    }
                } else if ($oop) {
                    if ($oop == 'odd' and $page_num % 2 != 0) {
                        $this->mpdf->htext($data['color'], $data['location'], $text, $angle = 0, $data['font'], $data['font_size'], 1);
                    }
                    if ($oop == 'even' and $page_num % 2 == 0) {
                        $this->mpdf->htext($data['color'], $data['location'], $text, $angle = 0, $data['font'], $data['font_size'], 1);
                    }
                } else {
                    $this->mpdf->htext($data['color'], $data['location'], $text, $angle = 0, $data['font'], $data['font_size'], 1);
                }
            }
        }
        $this->saveMpdf($dest_file);


        $new_file_name = EditPdf::getNewFileName($doc['original_name'], "header_footer", ".pdf");
        Document::where(['id' => $doc['id']])->update(['edited_document' => $dest_file,
            "download_name" => $new_file_name,
            "delete_after" => (time() + 18000)]);
        return response()->json(['success' => true, 'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);
    }


    private function changeMargins($file = false, $top = true, $pwd = false)
    {
        shell_exec("nice -n 19 pdftocairo -pdf $file {$file}_cleaned");
        rename("{$file}_cleaned", $file);

        $fc = file_get_contents($file);

        $pattern = "/^(?:.*)obj\R(?:.*)(\/MediaBox \[[\n\r\s]?+(\d+)[\n\r\s]?+(\d+)[\n\r\s]?+(\d+)[\n\r\s]?+(\d+) \])(?:.*)\Rendobj\R/s";
        $result = preg_match_all($pattern, $fc, $matches);


        $lines = preg_split("/(\r\n|\n|\r)/", $fc);

        foreach ($matches[0] as $pn => $m) {
            $page_num = $pn + 1;
//			if($req->post("for_all_pages")=='true'){
//				$crop_pages[$page_num] = $crop_pages[1];
//			}

//			if(isset($crop_pages[$page_num])){
            if ($top) {
                $new_left = 1;//-($margins[0]);
                $new_bot = 1; //-($margins[3]);
                $new_w = ($matches[4][$pn]);
                $new_h = ($matches[5][$pn]);
            } else {
                $new_left = 1;//-($margins[0]);
                $new_bot = 1; //-($margins[3]);
                $new_w = ($matches[4][$pn]);
                $new_h = ($matches[5][$pn]);

            }

            $new_m = str_replace($matches[1][$pn],
                "/MediaBox [$new_left $new_bot $new_w $new_h]", $m);
            $fc = str_replace($m, $new_m, $fc);
        }
//		}
        $content = $fc;
        file_put_contents($file, $content);
    }

    public function batesNumbering(Request $req)
    {
        $uuid = $req->post("uuid");
        $operation_type = "batespdf";
        $data = $req->post("data");
        //$original_file = EditPdf::getFilePath($uuid, $operation_type);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".zip"));
        $operation_id = $req->post("operation_id");

        if ($data['margins'] == 'increase') {

        }

        $docs = Document::where([
            'UUID' => $uuid,
            'operation_type' => $operation_type,
            'operation_id' => $operation_id,
        ])->orderBy('ID', 'desc')->get()->toArray();

        if (!$docs) {
            return response()->json(['success' => false, "message" => "Operation not found"]);
        }
        //location
        $temp_files = array();

        $positions = ["hcenter", "hleft", "hright", "fcenter", "fleft", "fright"];

        $pd = $req->post("data");

        $page_counter = 0;

        foreach ($docs as $fn => $file) {
            $path = (base_path("/public/" . $file['original_document']));
            //hcenter

//			if($data['margins']=='increase'){
//				shell_exec("pdfjam --trim '0cm 0in 0cm 0cm' $path  --outfile $path.margins");
//				rename("$path.margins", $path);
//			}


            $temp_file_dest = (EditPdf::getDestPath($uuid, $operation_type, "_$fn.pdf"));
            $this->getMpdf($path);
            $mpdf = $this->mpdf;
            $filename = $file['original_name'];
            $temp_files[] = ["name" => $filename, "path" => $temp_file_dest];


            $start_from_page = (int)$pd["bates_start_from"];
            $start_from_file = (int)$pd["file_start_from"];

            foreach (range(1, $this->pagecount) as $page_num) {
                $page_counter++;
                $file_num = $fn + 1;
                switch ($pd['exhb']) {
                    case 'just-number':
                    case 'full-bates':
                    case 'bates-with-exhibit':
                    default:
                        $new_page_num = sprintf('%06d', $page_counter);
                        break;
                    case 'bates-with-exhibit-3-digits':
                    case 'full-bates-3-digits':
                    case 'just-number-3-digits':
                        $new_page_num = sprintf('%03d', $page_counter);
                        break;
                }

                $data = array(
                    "only_on_page" => false,
                    "header_type" => "hf-pages-arabic",
                    "start_from_page" => $pd['bates_start_from'],
                    "color" => $pd['color'],
                    "location" => $pd['location'],
                    "font" => $pd['font'],
                    "font_size" => $pd['font_size']
                );

                $text = "";

                if ($pd['exhb'] == 'bates-custom') {
                    $text = $pd['user_inp_3'];
                    $text = str_replace("[FILE_NUMBER]", $file_num, $text);
                    $text = str_replace("[BATES_NUMBER]", $new_page_num, $text);
                } else {
                    if (isset($pd['user_inp_1'])) {
                        $text = "{$pd['user_inp_1']} $file_num ";
                    }
                    if (isset($pd['user_inp_2'])) {
                        $text .= "{$pd['user_inp_2']} $new_page_num ";
                    }
                    if (!isset($pd['user_inp_1']) and !isset($pd['user_inp_2'])) {
                        $text = $new_page_num;
                    }
                }


                $add_bottom = 0;
                $add_top = 0;

                if ($pd['margins'] == 'increase' and in_array($pd['location'], ['hcenter', 'hleft', 'hright'])) {
                    $add_top = 10;
                    $add_bottom = 10;
                }
                if ($pd['margins'] == 'increase' and in_array($pd['location'], ['fcenter', 'fleft', 'fright'])) {
                    $add_bottom = 10;
                }

                $template_data = $mpdf->ImportPage((int)$page_num, null, null, 0, 0, "/CropBox", false);

//				var_dump($template_data['tpl_box']);
//				exit();

                $size = [$template_data['tpl_box']['w'], $template_data['tpl_box']['h'] + $add_bottom];

                $orientation = "P";
                if ($size[0] > $size[1]) {
                    $orientation = "P";
                    if ($template_data['rotated']) {
                        $orientation = "L";
                    }
                }

                $this->mpdf->addPage($orientation, "", "", "", "",
                    0,
                    0,
                    0,
                    0,
                    0, "", "", "", "", "", "", "", "", "", "", $size, false);

                $this->mpdf->UseTemplate($template_data['tplId'], 0, $add_top);

                //			$this->mpdf->htext("XYZ", 0, "courier2", 96);

                if ($page_num >= $start_from_page and $file_num >= $start_from_file) {
                    $this->mpdf->htext($data['color'], $data['location'], $text, $angle = 0, $data['font'], $data['font_size'], 1);
                }
            }
////////////////////////////////////////////////////////////
            $this->saveMpdf($temp_file_dest);
        }


        $filename = str_replace(array(".", "/"), "", $filename);
        if (count($temp_files) === 1) {
            $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pdf"));
            copy($temp_files[0]['path'], $dest_file);
            $new_file_name = EditPdf::getNewFileName("batespdf", str_replace("pdf", "", $filename), ".pdf");
        } else {
            $zip = new ZipArchive();
            if ($zip->open($dest_file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
                return response()->json(['success' => false, 'message' => 'Failed to create zip archive...']);
            }
            $tmp_files = array();
            $fn = 1;
            $pattern = $pd['file_patern'];

            foreach ($temp_files as $temp) {
                $zip->addFile($temp['path'], $pattern . $temp['name']);
            }
            $zip->close();
            $new_file_name = EditPdf::getNewFileName("batespdf", rand(1000, 9999), ".zip");
        }


        foreach ($docs as $doc) {
            Document::where(['id' => $doc['id']])->update(['edited_document' => $dest_file,
                "download_name" => $new_file_name,
                "delete_after" => (time() + 18000)]);
        }

        return response()->json(['success' => true, 'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);


    }


    public function ocr(Request $req)
    {
        setlocale(LC_ALL, "C.UTF-8");
        putenv('LC_ALL=C.UTF-8');

        $external_server = "https://apipro1.ocr.space/parse/image";

        $lang = $req->post("lang");
        //lang
        $uuid = $req->post("uuid");
        $operation_type = "ocrpdf";
        $pages_list = array();
        $doc = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type])->orderBy('ID', 'desc')->first()->toArray();
        $type = $req->post("type");

        $original_file = EditPdf::getFilePath($uuid, $operation_type);

        $base = base_path("public");
        $file_for_proccess = str_replace($base, "", $original_file);

        $api_key = strlen(Option::option('OcrApiKey')) ? Option::option('OcrApiKey') : Option::option('OcrApiKey', 'PDMXB8110888A');

        $resp = json_decode($this->httpPost($external_server, ["url" => "https://deftpdf.com/" . $file_for_proccess, "language" => $lang, "isCreateSearchablePdf" => "true", "isSearchablePdfHideTextLayer" => "true"], ['apikey:' . $api_key]), 1);

        //$resp = ($this->httpPost($external_server, ["file"=>"https://deftpdf.com/".$file_for_proccess, "lang"=>$lang, "operation"=>$operation_type, "type"=>$type]));

        if (!$resp or ($resp['IsErroredOnProcessing'] and !isset($resp["SearchablePDFURL"]))) {

            ob_start();
            var_dump($resp);
            $x = ob_get_clean();

            return response()->json(['success' => false, 'message' => 'Error 2', "temp" => $x]);
        }

        if ($type == 'pdf') {
            $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".pdf"));
            $cont = file_get_contents($resp['SearchablePDFURL']);
            file_put_contents($dest_file, $cont);
            $ext = "pdf";
        } else {

            if (!is_null($req->post('page_item'))) {
                if (isset($resp['ParsedResults'][$req->post('page_item')]['ParsedText']))
                    return response()->json(['success' => true, 'text' => $resp['ParsedResults'][$req->post('page_item')]['ParsedText']]);
                else
                    return response()->json(['success' => false, 'message' => 'Error 3']);
            }

            $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".txt"));

            $ext = "txt";
            $text = '';
            foreach ($resp['ParsedResults'] as $page) {
                $text .= $page['ParsedText'] . PHP_EOL;
            }

            file_put_contents($dest_file, $text);
        }

        //$x = $this->httpPost($external_server, ["operation"=>"delete_file", "fp"=>$resp['fp']]);

        $new_file_name = EditPdf::getNewFileName($req->post("file_name"), "ocr", ".$ext");

        Document::where(['id' => $doc['id']])->update(['edited_document' => $dest_file,
            "download_name" => $new_file_name,
            "delete_after" => (time() + 18000)]);
        return response()->json(['success' => true, 'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);
    }


    public function fillAndSignLink(Request $req)
    {
        $uuid = $req->post("uuid");
        $email = $req->post("email");
        $operation_type = "fill_and_sign";
        $doc = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type])->orderBy('ID', 'desc')->first();

        if ($doc->share_id) {
            $replicate = $doc->replicate();
            $replicate->save();
            $doc = $replicate;
        }
        $doc = $doc->toArray();

        $share_id = "file_" . md5($doc['operation_id'] . "_" . $email);
        $share_url = \URL::to("/pdf-editor-fill-sign/" . $share_id);

        Document::where(['id' => $doc['id']])->update(["share_id" => $share_id]);
        return response()->json(['success' => true, "url" => $share_url]);
    }

    public function fillAndSignEmail(Request $req)
    {


        $uuid = $req->post("uuid");
        $email = $req->post("email");
        $operation_type = "fill_and_sign";
        $doc = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type])->orderBy('ID', 'desc')->first();
        if ($doc->share_id) {
            $replicate = $doc->replicate();
            $replicate->save();
            $doc = $replicate;
        }
        $doc = $doc->toArray();


        $share_id = "file_" . md5($doc['operation_id'] . "_" . $email);
        $share_url = \URL::to("/pdf-editor-fill-sign/" . $share_id);

        Document::where(['id' => $doc['id']])->update(["share_id" => $share_id]);


        $data = array(
            "link" => "test",
            "noty" => $req->post("noty"),
            "sender" => $req->post("sender_email"),
            "document_title" => $req->post("document_title"),
            "document_url" => $share_url,
            "site_url" => \URL::to('/')
        );

        \Mail::send('emails.fill', $data, function ($message) use ($req) {
            $domain = $_SERVER['SERVER_NAME'];

            $title = $req->post("sender_email") . " want you to sign and fill this PDF file";


            $message->from("no-reply@$domain", 'DeftPDF')->subject($title);
            $message->to(Request()->post("recipient_email")); //->cc('1kruler1@gmail.com');

            if ((int)Request()->post("send_me_copy")) {
                $message->cc(Request()->post("user_email"));
            }
            if (is_file(base_path() . "/public/" . Request()->post("file_url"))) {

                $message->attach(base_path() . "/public/" . Request()->post("file_url"), array(
                        'as' => Request()->post("document_title"),
                        'mime' => "application/pdf")
                );

            }

        });

        return response()->json(array(
            "success" => true,
            "message" => "after send email"
        ));


    }

    public function getMpdf($file = false)
    {
        if (!$file) {
            return false;
        }
        $fonts = array();
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $font_path = base_path() . "/vendor/mpdf/mpdf/ttfonts/custom";
        $fonts = array(
            "fontDir" => array_merge($fontDirs, [$font_path]),
            "fontdata" => array_merge($fontData, array(
                "courier2" => array(
                    "R" => "Courier.ttf",
                    "B" => "Courier.ttf",
                ),
                "helvetica2" => array(
//                    "R" => "Helvetica.ttf",
//                    "B" => "Helvetica-Bold-Font.ttf",                    
//                    "R" => "Helvetica-Bold-Font.ttf",
                ),
                "timesnewroman2" => array(
                    "R" => "times-new-roman.ttf",
                    "B" => "times-new-roman.ttf",
                ),


            ))
        );

        $params = ["margin_left" => 0, "margin_top" => 0, "margin_right" => 0, "margin_header" => 0, "default_font_size" => 10, "open_layer_pane" => false, "format" => "", "useActiveForms" => true,];
        $this->mpdf = new \Mpdf\PDFHeaderFooter(array_merge($params, $fonts));
        $this->mpdf->useSubstitutions = true;
        $this->mpdf->text_input_as_HTML = true;
        $this->mpdf->SetImportUse();
        $this->pagecount = $this->mpdf->SetSourceFile($file);
    }

    private function saveMpdf($dest = false)
    {
        $this->mpdf->Output($dest, 'F');
    }

    public function splitByText(Request $req)
    {
        //pdfgrep -nip --cache 'benefits' ./sage.pdf
        $data = $req->post("data")['selected'];

        $uuid = $req->post("uuid");
        $operation_type = "splitbytextpdf";
        $operation_id = $req->post("operation_id");
        $on_page = $req->post("page");
        $doc = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type, "operation_id" => $operation_id])->orderBy('ID', 'desc')->first();
        if (!$doc) {
            return response()->json(['success' => false, 'message' => 'Operation not found...']);
        }
        $doc = $doc->toArray();


        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".zip"));
        if (!is_file($original_file)) {
            return response()->json(['success' => false, 'message' => 'Original file not found...']);
        }


        $zip = new ZipArchive();
        $temp = storage_path("pdfburst/$uuid/");
        File::deleteDirectory($temp);
        if (!is_dir($temp)) {
            if (!File::makeDirectory($temp)) {
                return response()->json(['success' => false, 'message' => 'Cant create temp folder...']);
            }
        }

        $on_page = (int)$on_page;


        $pwd = "";
        if ($password = $req->post("pdf_password")) {
            $pwd = "-opw $password";
        }


        $shell = "nice -n 19 pdftotext $pwd -nopgbrk -f $on_page -l $on_page -x {$data['x']} -y {$data['y']} -W {$data['w']} -H {$data['h']} $original_file - 2>&1";
        $selected_word = $req->post("text_start_from") . trim(shell_exec($shell));

        if (!$selected_word) {
            return response()->json(['success' => false, 'message' => "Please, select text"]);
        }


        if (strpos($selected_word, "Command Line Error") !== false) {
            $error = explode(":", $selected_word);
            unset($error[0]);
            $error = implode(":", $error);
            return response()->json(['success' => false, 'message' => $error]);
        }

        $shell = "nice -n 19 pdfgrep -nip '$selected_word' $original_file 2>&1";
        $pages_tmp = shell_exec($shell);
        $pages = array_filter(explode(PHP_EOL, $pages_tmp));

        if (empty($pages)) {
            return response()->json(['success' => false, 'message' => 'Text not found']);
        }

        $prev_page = 0;
        $page_ranges = array();
        foreach ($pages as $page) {
            list($page_num, $count_on_page) = explode(":", $page);
            if ($page == end($pages)) {
                $page_ranges[] = ($prev_page + 1) . "-end";
            } else {
                $page_ranges[] = ($prev_page + 1) . "-" . $page_num;
                $prev_page = $page_num;
            }
        }
        $pn = 1;

        $original_file_name = $req->post("file_name");
        $original_file_name = pathinfo($original_file_name)['filename'];


        $pwd = "";
        if ($password = $req->post("pdf_password")) {
            $pwd = "input_pw $password";
        }

        $temp_files = array();
        foreach ($page_ranges as $pr) {
            $cat_dest_file = sprintf("$temp/page-%d.pdf", ($pn));
            $shell = sprintf("nice -n 19 pdftk $original_file $pwd cat %s output $cat_dest_file", escapeshellarg($pr));

            shell_exec($shell);
            $temp_files[] = array("path" => $cat_dest_file, "name" => "fn");
            $pn++;
        }


        $zip = new ZipArchive();
        if ($zip->open($dest_file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
            return response()->json(['success' => false, 'message' => 'Failed to create zip archive...']);
        }
        $tmp_files = array();
        $fn = 1;


        $file_name = $req->post("filename_pattern") ?: "[BASENAME]-[CURRENTPAGE]";
        $file_name = str_replace("[BASENAME]", $original_file_name, $file_name);


        $fn = 1;
        foreach ($temp_files as $k => $temp) {
            $nfile_name = str_replace("[CURRENTPAGE]", $fn, $file_name . ".pdf");
            $zip->addFile($temp['path'], $nfile_name);
            $fn++;
        }
        $zip->close();
        Document::where(['id' => $doc['id']])->update(['edited_document' => $dest_file, "delete_after" => (time() + 18000)]);
        return response()->json(['success' => true, 'new_file_name' => EditPdf::getNewFileName($req->post("file_name"), "split_by_text", ".zip"), 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);
    }

    public function transBlock(Request $req)
    {
//		exit('asd');
//		var_dump($req->post('lang_from'));
//		var_dump($req->post('lang_to'));
//		$currentLanguage = 'en';
//		$targetLanguage= 'ru';
        $currentLanguage = $req->post('lang_from');
        $targetLanguage = $req->post('lang_to');

        $this->client = new \Aws\Translate\TranslateClient([
            'profile' => 'default',
            'region' => 'us-west-2',
            'version' => '2017-07-01'
        ]);
        $text = $req->post("text");
        try {
            $result = $this->client->translateText([
                'SourceLanguageCode' => $currentLanguage,
                'TargetLanguageCode' => $targetLanguage,
                'Text' => $text,
            ]);
            $translated = ($result->get("TranslatedText"));


            exit(json_encode([
                "success" => true,
                "translated" => $translated,
                "original" => $text,
                "key" => $req->post("key")
            ]));

        } catch (AwsException $e) {
            exit(json_encode([
                "success" => false,
                "translated" => $text,
                "original" => $text,
                "key" => $req->post("key")
            ]));
        }
    }

    public function transDocxDebug(Request $req)
    {

        $parser = new \App\Custom\DocxParser("3", "2");
        $parser->getTexts();
    }

    public function transDocx(Request $req)
    {

        $uuid = $req->post("UUID");
        $operation_type = "translatedocx";
        $pages_list = array();
        if ($req->post("download")) {
            $doc = Document::where(['UUID' => $uuid,
                'operation_type' => $operation_type,
                'operation_id' => $req->post("operation_id")
            ])->first();
        } else {
            $doc = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type])->orderBy('id', 'desc')->first();
        }
//		var_dump($doc);
        if ($doc) {
            $doc = $doc->toArray();
        } else {
            //TODO uncoment
            return response()->json(['success' => false, 'message' => 'Operation not found...']);
        }

        $type = $req->post("type");

        $original_file = EditPdf::getFilePath($uuid, $operation_type, 'docx', 'docx');
        $dest_file = EditPdf::getDestPath($uuid, $operation_type, '.docx', 'docx');


        if ($req->post("download")) {
//			print_r($doc['original_name']);
            $new_file_name = EditPdf::getNewFileName($doc['original_name'], $operation_type, ".docx");
            return response()->json(['success' => true, 'new_file_name' => $new_file_name, 'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);
        } else {
            if (!is_file($original_file)) {
                return response()->json(['success' => false, 'message' => 'Original file not found...']);
            }

            $parser = new \App\Custom\DocxParser($original_file, $dest_file);
            $langs = ['from' => $req->post("lang_from"), 'to' => $req->post("lang_to")];

            $parser->getDocumentXML();
            return response()->json(['success' => true, "file_path" => $file_path_pdf, "docx_file" => $file_path_docx]);

            $parser->getTexts($langs);


            if (is_file($dest_file)) {
                $dest_file_pdf = EditPdf::getDestPath($uuid, $operation_type, '.pdf', 'docx');
                $shell_exec = ("doc2pdf " . $dest_file . ' ' . $dest_file_pdf);
                $shell_exec = str_replace('//', '/', $shell_exec);
                $sh = shell_exec($shell_exec . ' 2>&1');

                $new_file_name = EditPdf::getNewFileName($doc['original_name'], $operation_type, ".docx");
                Document::where(['id' => $doc['id']])->update(['download_name' => $new_file_name, 'edited_document' => $dest_file, "delete_after" => (time() + 18000)]);


                $file_path_pdf = str_replace(base_path() . '//public/', '', $dest_file_pdf);
                $file_path_docx = str_replace(base_path() . '//public/', '', $dest_file);

                if (is_file($dest_file)) {
                    return response()->json(['success' => true, "file_path" => $file_path_pdf, "docx_file" => $file_path_docx]);
                } else {
                    return response()->json(['success' => false, 'message' => 'failed...']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Translation failed...']);
            }

            exit("test");
        }
    }


    private function httpPost($url, $data, $headers = false)
    {

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        if ($headers)
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);


        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $response;
    }


}


class PDFHeaderFooter extends \Mpdf\Mpdf
{
    public $otl = false;

    public function htext($color = "#ebebeb", $pos = "hcenter", $texte, $angle = 45, $fontfamily = "Courier", $fontsize = 96, $alpha = 0.5, $top = false, $left = false){
        if($this->PDFA || $this->PDFX){
            throw new MpdfMpdfException('PDFA and PDFX do not permit transparency, so mPDF does not allow Watermarks!');
        }
        
        
        $this->SetFont($fontfamily, "B", $fontsize, false); // Don't output
        $texte = $this->purify_utf8_text($texte);
        
        if ($this->text_input_as_HTML) {
            $texte = $this->all_entities_to_utf8($texte);
        }

        if ($this->usingCoreFont) {
            $texte = mb_convert_encoding($texte, $this->mb_enc, 'UTF-8');
        }

        if (preg_match("/([" . $this->pregRTLchars . "])/u", $texte)) {
            $this->biDirectional = true;
        } // *OTL*
        $textvar = 0;
        if (@$this->OTLtags) {
            $save_OTLtags = $this->OTLtags;
        }
        $this->OTLtags = [];
        if ($this->useKerning) {
            if ($this->CurrentFont['haskernGPOS']) {
                $this->OTLtags['Plus'] .= ' kern';
            } else {
                $textvar = ($textvar | TextVars::FC_KERNING);
            }
        }

        if (isset($this->CurrentFont['useOTL']) && $this->CurrentFont['useOTL'] && @$this->otl) {
            $texte = $this->otl->applyOTL($texte, $this->CurrentFont['useOTL']);
            $OTLdata = $this->otl->OTLdata;
        }

        /* -- END OTL -- */
        if (@$this->OTLtags) {
            $this->OTLtags = $save_OTLtags;
        }
        $this->magic_reverse_dir($texte, $this->directionality, $OTLdata);
        $this->SetAlpha($alpha);

        extract(hexToRgb($color));

        $this->SetTextColor($r, $g, $b);
        $szfont = $fontsize;
        $loop = 0;
        $maxlen = (min($this->w, $this->h)); // sets max length of text as 7/8 width/height of page
        while ($loop == 0) {
            $this->SetFont($fontfamily, "B", $szfont, false); // Don't output
            $offset = ((sin(deg2rad($angle))) * ($szfont / \Mpdf\Mpdf::SCALE));
            $strlen = $this->GetStringWidth($texte, true, $OTLdata, $textvar);
            if ($strlen > $maxlen - $offset) {
                //$szfont--;
                break;
            } else {
                $loop++;
            }
        }
        $scale = \Mpdf\Mpdf::SCALE;


        $this->SetFont($fontfamily, "B", $szfont - 0.1, true, true); // Output The -0.1 is because SetFont above is not written to PDF

        $adj = ((cos(deg2rad($angle))) * ($strlen / 2));
        $opp = ((sin(deg2rad($angle))) * ($strlen / 2));


        switch ($pos) {
            case 'hcenter':
                $wx = ($this->w / 2) - $adj + $offset / 3; //left
                $wy = ($fontsize / $scale) + 3; //
                break;
            case 'hleft':
                $wx = 10;
                $wy = ($fontsize / $scale) + 3; //
                break;
            case 'hright':
                $wx = $this->w - $strlen - 10;
                $wy = ($fontsize / $scale) + 3; //
                break;
            case 'fcenter':
                $wx = ($this->w / 2) - $adj + $offset / 3;
                $wy = $this->h - ($fontsize / $scale);
                break;
            case 'fleft':
                $wx = 10;
                $wy = $this->h - ($fontsize / $scale);

                break;
            case 'fright':
                $wx = $this->w - $strlen - 10;
                $wy = $this->h - ($fontsize / $scale);
                break;
            case 'cord':
                $wx = $top;
                $wy = $left;
                break;

            default:
                return;
//				exit($pos);
                break;
        }
//		$wx =  ($this->w / 2) - $adj + $offset / 3; //left
//		//$wy = ($this->h / 2) + $opp;
//		$wy = ($fontsize/$scale); //


        $this->Rotate($angle, $wx, $wy);
        $this->Text($wx, $wy, $texte, $OTLdata, $textvar, "", "shit");
        $this->Rotate(0);
        $this->SetTColor("");
        //$this->SetAlpha(1);
    }


}


function hexToRgb($hex, $alpha = false)
{
    $hex = str_replace('#', '', $hex);
    $length = strlen($hex);
    $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
    $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
    $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
    if ($alpha) {
        $rgb['a'] = $alpha;
    }
    return $rgb;
}






