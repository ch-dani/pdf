<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\UserImages;
use Illuminate\Support\Facades\Auth;
use Mpdf;
use File;
use URL;
use Mail;
use App\SharedLinks;


include base_path() . "/app/Custom/PDFd.php";

class EditPdf extends Controller
{

    private $user_page_size = ["w" => 0, "h" => 0];
    private $document_page_size = ["w" => 0, "h" => 0];

    public static function getFilePath($uuid = false, $type = "edit", $ext = "pdf", $folder = "pdf")
    {
        if (!$uuid) return false;

        $path = (public_path()) . "/uploads/{$folder}/";
        $uuid = str_replace(array("/", "~", "."), "-", $uuid);
        $file = "{$path}{$uuid}_{$type}.{$ext}";

        return $file;
    }

    public static function getDestPath($uuid = false, $type = 'edit', $ext = ".pdf", $folder = "pdf")
    {
        $folder = base_path("public/uploads/{$folder}/");
        return "{$folder}{$type}_{$uuid}{$ext}";
    }

    public static function getDownloadLink($uuid = false, $type = 'edit', $ext = ".pdf")
    {
        $folder = base_path("/public/uploads/pdf/");
        return "downloadfile/$type/{$uuid}";
    }

    public static function getNewFileName($file_name = "file.pdf", $type = "edit", $ext = ".pdf")
    {
        $pi = pathinfo($file_name);

        setlocale(LC_ALL, 'en_US.UTF-8');

        if (!isset($pi['filename']) || !$pi['filename']) {
            $pi_temp = explode(".", $file_name);
            $pi['filename'] = $pi_temp[0];
        }

        if ($type) {
            return "{$pi['filename']}_{$type}{$ext}";
        } else {
            return "{$pi['filename']}{$ext}";
        }
    }

    public function getExternalFile(Request $req)
    {
        $path = $req->get("url");
        header("Content-type:application/pdf");

        $file_content = file_get_contents(urldecode($path));
        exit($file_content);

        response()->json(["data" => $file_content, "message" => "", "success" => true]);
    }

    public function editpdf(string $file_id = null, Request $request, $temp = false)
    {
        global $lang_code;

        $path = ($request->path());
        if ($lang_code == $file_id) {
            $file_id = false;
            if ($temp) {
                $file_id = $temp;
            }
            $view = explode("/", $path)[1];
        } else {
            $view = explode("/", $path)[0];
        }

        $uuid = $operation_id = false;
        $req = new Request;

        $open_exist_file = false;
        $file_name = false;

        if ($file_id) {

            if (!isset($_COOKIE['spe_uuid'])) {
                $uuid = $new_guid = $this->guid();
            } else {
                $uuid = $_COOKIE['spe_uuid'];
            }

            $operation_type = "fill_and_sign";
            $pages_list = array();
            $doc = Document::where(['share_id' => $file_id])->orderBy('ID', 'desc')->first();

            if (!$doc) {
                abort(404);
            }

            if ($doc->UUID == $uuid) {
                $operation_id = $doc->operation_id;
            } else {
                $operation_id = $this->guid();
            }

            $alredy_replicated = Document::where(['share_id' => $file_id, 'UUID' => $uuid, "is_copy" => 1])->orderBy('ID', 'desc')->first(); //->toArray();
            if ($alredy_replicated) {
                $open_exist_file = $doc->original_document;
                //exit("alredy_replicated");
            } else {
                $replicate = $doc->replicate();
                $replicate->is_copy = 1;
                $replicate->operation_id = $operation_id;
                $replicate->UUID = $uuid;
                $replicate->save();
                $open_exist_file = $replicate->original_document;
            }

            $file_name = $doc->original_name;

        }

        return view($view, [
            "file_name" => $file_name ?: "blank.pdf",
            "new_uuid" => $uuid,
            "operation_id" => $operation_id,
            "open_exist_file" => $open_exist_file,
            'html_source' => "test",
            "user_images" => UserImages::where(["UUID" => @$_COOKIE['spe_uuid'], "file_type" => "Image"])->get()->toArray(),
            "user_signs" => UserImages::where(["UUID" => @$_COOKIE['spe_uuid'], "file_type" => "Sign"])->get()->toArray(),
            "default_colors" => $this->defaultColors(),
            "default_borders" => $this->gefaultBorders(),
            "default_fonts" => $this->getDefaultFonts(),
            "is_debug" => isset($_GET['debug']) ? true : false,
            "is_fill_and_sign" => explode("/", $path)[0] == 'pdf-editor-fill-sign' ? true : false,
            "is_pdf_to_excel" => explode("/", $path)[0] == 'pdf-to-excel' ? true : false,
            "home_url" => URL::to('/'),
            "exist_file_id" => $file_id
        ]);

    }

    public function createShareLink(Request $req)
    {
        session_start();
        $ui = new SharedLinks;
        $file_id = md5(time());

        $uuid = ($_COOKIE['spe_uuid']);
        $ui->file_path = base_path() . "/public/uploads/pdf/edited_$uuid.pdf";

        copy($ui->file_path, $ui->file_path . ".shared");
        $ui->file_path .= ".shared";

        $ui->file_name = $req->post("document_title");
        $ui->uniq_file_id = $file_id;
        $ui->exp_time = time() + (86400 * 30);
        $ui->save();

        return response()->json(array(
            "success" => true,
            "share_url" => URL::to("/pdf/share?id={$file_id}")
        ));

        exit("horosh");
    }

    public function uploadShare(Request $req)
    {
        if (!$req->get("id")) {
            exit("Link expired");
        }

        $link = SharedLinks::where(array("uniq_file_id" => $req->get("id")))->first()->toArray();

        if (!$link) {
            exit("Link expired");
        }

        header("Content-type:application/pdf");
        header("Content-Disposition:attachment;filename={$link['file_name']}");
        readfile($link['file_path']);
        exit();
    }

    public function sendByEmail(Request $req)
    {
        $data = array(
            "link" => "test",
            "noty" => $req->post("noty"),
            "sender" => $req->post("user_email"),
            "document_title" => $req->post("document_title"),
            "site_url" => URL::to('/')
        );

        Mail::send('emails.share_pdf', $data, function ($message) {
            $domain = $_SERVER['SERVER_NAME'];
            $message->from("no-reply@$domain", 'DeftPDF')->subject(Request()->post("user_email") . " send you file");
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

    public function deleteOldFiles()
    {
        $documents = Document::where('delete_after', "<=", time())->get();

        foreach ($documents as $doc) {
            $fp = base_path() . "/public/{$doc->original_document}";
            $fp2 = base_path() . "/public/{$doc->edited_document}";

            if (is_file($fp)) {
                unlink($fp);
            }

            if (is_file($fp2)) {
                unlink($fp2);
            }

            $doc->delete();
        }

        //->delete();
        exit("time to delete");
    }


    public function downloadFile($type = 'edit', $uuid = false)
    {
        if (!$type or !$uuid) {
            abort(404);
        }
        //\DB::enableQueryLog();

        $document = Document::where(['UUID' => $uuid, 'operation_type' => $type])->orderBy('ID', 'desc')->first();

        //pathinfo


        if ($document) {

            $file_path = $document->edited_document;
//			exit($file_path);

            $document->update([
                "delete_after" => (time() + (5 * 60)),
            ]);

            if (!is_file($file_path)) {
//				exit('asd');
                abort(404);
            }

            $pi = (pathinfo($document->original_name));
            $pi2 = pathinfo($file_path);
            if (!$pi['filename']) {
                $pi['filename'] = explode(".", $document->original_name)[0];
            }

            if ($document['operation_type'] == 'croppdf') {
                $pi['filename'] .= "_cropped";
            }

            switch ($document['operation_type']) {
                default:

                    break;

                case 'translatepdf':
                    $pi['filename'] = "translated_" . $pi['filename'];
                    $pi2['extension'] = "pdf";
                    break;

                case 'splitbysizepdf':
                    $pi['filename'] .= "_splited";
                    $pi2['extension'] = "zip";
                    break;

                case 'splitoutlinepdf':
                    $pi['filename'] .= "_exploded";
                    $pi2['extension'] = "zip";
                    break;
            }

            $path_info = pathinfo($file_path);

            //TODO fix
            if (false && $pi2['extension'] == 'pdf' and !in_array($document['operation_type'], ['encryptpdf', 'rotatepdf', 'epub2pdf', 'resizepdf'])) {

                shell_exec("gs -sDEVICE=pdfwrite -dPrinted=false -dCompatibilityLevel=1.4 -dDownsampleColorImages=true -dColorImageResolution=150 -dNOPAUSE  -dBATCH -sOutputFile={$document->edited_document}.compressed {$document->edited_document}");
                if (is_file("{$document->edited_document}.compressed")) {
                    rename("{$document->edited_document}.compressed", $document->edited_document);
                }

            }

            if ($path_info['extension'] == 'doc') {
                $mime_type = "application/msword";
            } else if ($path_info['extension'] == 'docx') {
                $mime_type = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
            } else if ($path_info['extension'] == 'pptx') {
                $mime_type = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
            } else {
                $mime_type = mime_content_type($file_path);
            }

            if ($document['download_name']) {
                $file_name = $document['download_name'];
            } else {
                $file_name = "{$pi['filename']}.{$pi2['extension']}";
            }

            if (isset($_GET['rename']) and $_GET['rename']) {
                $file_name = $_GET['rename'];
            }

            header("Content-Disposition: attachment; filename=\"$file_name\"");
            header("Content-type:" . $mime_type . "");
            readfile($file_path);
            exit();
        }
        abort(404);
    }


    //TODO передеалать
    public function downloadEdited($file_id = false, $type = 'edit')
    {
        Document::where('UUID', $file_id)->orderBy('ID', 'desc')->limit(1)->update([
            //'edited_document' => "/uploads/pdf/edited_{$file_id}.pdf",
            "delete_after" => (time() + (5 * 60)),
        ]);

        $x = Document::where('UUID', $file_id)->orderBy('ID', 'desc')->first()->toArray();

        if (!$file_id || !$x) {
            abort(404);
        }
        if (is_file($x['edited_document'])) {
//			$bp = (base_path("public"));
            $file_path = str_replace("//", "/", $x['edited_document']);
//			$file_path = str_replace($bp, "",$file_path);
        } else {
            $file_path = base_path() . "/public/" . $x['edited_document'];//"/public/uploads/pdf/edited_{$file_id}.pdf";
        }


        if (!is_file($file_path)) {
            abort(404);
        }
        header("Content-type:application/pdf");
        readfile($file_path);

        exit();
    }


    public function createPdf(Request $req)
    {
        $mpdf = new \Mpdf\Mpdf();

        if(method_exists($mpdf, "SetImportUse")){
	        $mpdf->SetImportUse();
        }

        $elemetns = $req->post("changes");
        $elemetns = is_array($elemetns) ? $elemetns : [];
        $elemetns['text'] = isset($elemetns['text']) ? $elemetns['text'] : [];
        $default_elements = $req->post("default_text_elements") ? $req->post("default_text_elements") : array();

        $default_elements = array();

        $elemetns['text'] = array_merge($elemetns['text'], $default_elements);

        new \PDFd\PDFd(
            $elemetns,
            isset($_POST['pages_sizes']) ? $_POST['pages_sizes'] : array(),
            false
        );
        //RETURN IN PDFd
    }

    public function deleteImage(Request $req)
    {
        $image_id = $req->post("image_id");
        $uuid = $req->post("UUID");
        if ($image_id && $uuid) {
            $images = UserImages::where(array("id" => $image_id))->get();
            foreach ($images as $image) {
                if ($image->UUID == $uuid) {
                    $image->delete();
                }
            }
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, "message" => "empty uuid or image id"]);
    }

    private function px2inch($cord = array())
    {
        $w = $this->user_page_size['w'] / ($this->document_page_size['w']);
        $h = $this->user_page_size['h'] / ($this->document_page_size['h']);
        $temp = array("top" => $cord['top'] / $h - 1, "left" => $cord['left'] / $h, "width" => $cord['width'] / $w, "height" => $cord['height'] / $h - 1);
        return $temp;
    }

    public function uploadEPUB(Request $request)
    {
        $is_external = false;

        $ui = new UserImages;
        $ot = $request->post('operation_type') ? strtolower($request->post('operation_type')) : "edit";

        $file = $request->file("file");

        $destinationPath = 'uploads/pdf';
        $uuid = $request->post("UUID");

        $filename = "{$uuid}_{$ot}.epub";
        $temp_file = base_path() . "/public/{$destinationPath}/temp/$filename";
        $dest_file = base_path() . "/public/{$destinationPath}/$filename";
        $font_array = array();

        $file->move($destinationPath, $filename);

        $original_name = $file->getClientOriginalName();

        $x = Document::create([
            'user_id' => Auth::check() ? Auth::user()->id : NULL,
            'UUID' => $uuid,
            'operation_id' => $request->post("operation_id"),
            'operation_type' => $ot,
            'original_document' => $destinationPath . '/' . $filename,
            'original_name' => $original_name . "",
            "delete_after" => (time() + 18000),
        ]);
        return response()->json(['success' => true, "file_path" => $filename, "fonts" => $font_array, "del" => time() + 18000]);
    }

    public function uploadPDF(Request $request)
    {

        $is_external = false;
        if ($request->post("contents")) {
            $is_external = true;
        }

        $ui = new UserImages;
        $ot = $request->post('operation_type') ? strtolower($request->post('operation_type')) : "edit";
        $is_multiple_upload = $request->post("multiple_upload");


        if ($is_external) {
            $file = $request->post("contents");
        } else {
            $file = $request->file("file");
        }

        $destinationPath = 'uploads/pdf';
        $uuid = $request->post("UUID");

        if ($is_multiple_upload != '0') {
            $range = range("A", "Z");
            if (!in_array($is_multiple_upload, $range)) {
                return response()->json(['success' => false, "message" => "document name not in range..."]);
            }
//            $filename = time() ."_{$uuid}_{$is_multiple_upload}_{$ot}.pdf";
            $filename = "{$uuid}_{$is_multiple_upload}_{$ot}.pdf";
        } else {
//            $filename = time() ."_{$uuid}_{$ot}.pdf";
            $filename = "{$uuid}_{$ot}.pdf";
        }
        if ($is_external) {
            $url = $request->link;
            $name = base_path() . "/public/{$destinationPath}/$filename";
            set_time_limit(0);
            $fp = fopen($name, 'w+');
            $ch = curl_init(str_replace(" ", "%20", $url));
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $request->access_token,
            ));
            // get curl response
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        } else {
            $file->move($destinationPath, $filename);
            $ui->UUID = $request->post("UUID");
            $ui->file_name = $filename;
            $ui->file_type = $request->post("type");
            $ui->save();
        }

        $temp_file = base_path() . "/public/{$destinationPath}/temp/$filename";
        $dest_file = base_path() . "/public/{$destinationPath}/$filename";

        /**
         * Commented because used command with -dFILTERTEXT
         */


        /* exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$temp_file $dest_file", $x);
        copy($temp_file, $dest_file);*/

        $temp_file .= ".notext.pdf";

        $password = $request->post("pdf_password");
        $pwd = "";
        $pwdm = "";
        $pwdcairo = "";
        if ($password) {
            $password = escapeshellarg($password);
            $pwd = ("--sPDFPassword=$password");
            $pwdm = ("-p $password");
            $pwdcairo = escapeshellarg("-opw $password");
        }


        $skip_fixes = array("ppt2pdf", "pdf2word", "compresspdf", "rotatepdf", "encryptpdf", "translatepdf", "mixpdf", "ocrpdf");


        if (isset($_COOKIE['test2'])) {

        }


        if (!in_array($ot, $skip_fixes)) {
            if ($ot == 'resizepdf') {
                $x = shell_exec("mutool clean $pwdm -s $dest_file $dest_file.fixed 2>&1");
                //shell_exec("sudo chmod 777 $dest_file.fixed");
                rename("$dest_file.fixed", $dest_file);
            } else {
                shell_exec("pdftocairo -pdf $dest_file $dest_file.fixed");
                shell_exec("mutool clean $pwdm -sg $dest_file.fixed $dest_file 2>&1");
                shell_exec("chmod 777 $dest_file");
            }

            if (is_file("$dest_file.fixed")) {
                unlink("$dest_file.fixed");
            }

            //rename($temp_file, $dest_file);
            //rename("$dest_file.fixed", $dest_file);
        }

        $font_array = array();

        if (!(int)$request->post("skip_extract") && $ot != 'pdf2word') {


            $font_extractor_path = base_path() . "/public/pdf_scripts/toolbin_extractFonts.ps";
            $extract_shell = "gs -q -dNODISPLAY $font_extractor_path -c '($dest_file) extractFonts quit' 2>&1";


            $trash_path = (base_path() . "/public/pdf_scripts/trash/$uuid");


            File::makeDirectory($trash_path, $mode = 0777, true, true);
            chdir($trash_path);

            exec("cd $trash_path &&" . $extract_shell, $shell_output);

            $shell_output = $font_list = array_diff(scandir($trash_path . "//"), array('..', '.'));


            foreach ($shell_output as $font_file) {
                $pathinfo = pathinfo($font_file);
                if ($pathinfo && @$pathinfo['extension'] == 'cff') {
                    $conver_font_shell = base_path() . "/public/pdf_scripts/convert_fonts.sh $trash_path//$font_file 2>&1";

                    exec($conver_font_shell);
                    $new_path = "$trash_path//{$pathinfo['filename']}.ttf";
                    if (!is_file($new_path)) {
                        continue;
                    }
                    $src = 'data:font/opentype;base64,' . base64_encode(file_get_contents($new_path));
                    $font_array[str_replace("font_", "", $pathinfo['filename'])] = $src;
                }
            }

//			$eee = shell_exec("mutool clean $pwdm -sgd $dest_file $temp_file 2>&1");
//			rename($temp_file, $dest_file);
        }


        if ($request->post("remove_all_texts") != null and (int)$request->post("remove_all_texts")) {


            exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dFILTERTEXT -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$temp_file $dest_file", $x);
            copy($temp_file, $dest_file);
        }


        if ($is_external) {
            $original_name = $request->post("file_name");
        } else {
            $original_name = $file->getClientOriginalName();
        }

        $x = Document::create([
            'user_id' => Auth::check() ? Auth::user()->id : NULL,
            'UUID' => $uuid,
            'operation_id' => $request->post("operation_id"),
            'operation_type' => $ot,
            'original_document' => $destinationPath . '/' . $filename,
            'original_name' => $original_name . "",
            "delete_after" => (time() + 18000),
        ]);

        $output = shell_exec(base_path() . "/public/pdf_scripts/extract_fonts.sh '$dest_file'");
        return response()->json([
        	'success' => true, 
        	"original_document_url"=>$destinationPath . '/' . $filename,
        	"original_document_name"=>$original_name,
        	"file_path" => $filename,
        	"fonts" => $font_array, 
        	"del" => time() + 18000]
        );
    }


    public function uploadPDFTranslate(Request $request)
    {


        $is_external = false;
        $ui = new UserImages;
        $ot = $request->post('operation_type') ? strtolower($request->post('operation_type')) : "edit";

        $file = $request->file("file");

        $destinationPath = 'uploads/pdf';
        $uuid = $request->post("UUID");

        $filename = "{$uuid}_{$ot}.pdf";


        $file->move($destinationPath, $filename);
        $ui->UUID = $request->post("UUID");
        $ui->file_name = $filename;
        $ui->file_type = $request->post("type");
        $ui->save();

        $temp_file = base_path() . "/public/{$destinationPath}/temp/$filename";
        $dest_file = base_path() . "/public/{$destinationPath}/$filename";

        $temp_file .= ".notext.pdf";

        $password = $request->post("pdf_password");
        $pwd = "";
        $pwdm = "";
        $pwdcairo = "";
        if ($password) {
            $password = escapeshellarg($password);
            $pwd = ("--sPDFPassword=$password");
            $pwdm = ("-p $password");
            $pwdcairo = escapeshellarg("-opw $password");
        }


        $font_array = array();


        exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dFILTERTEXT -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$temp_file $dest_file", $x);
        copy($temp_file, $dest_file);


        $original_name = $file->getClientOriginalName();

        $x = Document::create([
            'user_id' => Auth::check() ? Auth::user()->id : NULL,
            'UUID' => $uuid,
            'operation_id' => $request->post("operation_id"),
            'operation_type' => $ot,
            'original_document' => $destinationPath . '/' . $filename,
            'original_name' => $original_name . "",
            "delete_after" => (time() + 18000),
        ]);

        return response()->json(['success' => true, "file_path" => $destinationPath . "/" . $filename, "fonts" => $font_array, "del" => time() + 18000]);
    }


    public function uploadImage(Request $request)
    {
        $ui = new UserImages;
        $file_id = ($request->post("file_id"));
        $filename = $request->post("UUID") . "-" . $file_id . ".image";
        switch ($request->post("type")) {
            case 'Sign':
                $data = $request->post("file");
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                File::put(public_path('uploads') . "/$filename", $data);

                $ui->UUID = $request->post("UUID");
                $ui->file_type = $request->post("type");

                $ui->file_name = $filename;
                $ui->save();

                break;
            default:

                $file = $request->file("file");
                $path_info = (pathinfo($file->getClientOriginalName()));
                $destinationPath = 'uploads';
                $file->move($destinationPath, $filename);
                $ui->UUID = $request->post("UUID");
                $ui->file_type = $request->post("type");

                $ui->file_name = $filename;
                $ui->save();

                break;


        }


        return response()->json(['success' => true, "image_id" => $ui->id, "message" => false]);
    }

    public function getDefaultFonts()
    {

        //EBGaramond08-Regular.otf
        //OpenSans-CondLight.ttf
        //Oranienbaum.ttf

        return [
            "Arimo",
            "Carlito",
            "Courier",
            "DejaVuSans",
            "DroidSerif",
            "Helvetica",
            "Lato",
            "LiberationSans",
            "NotoSans",
            "OpenSans",
            "PTSerif",
            "PTSans",
            "PTSansCaption",
            "PTSansNarrow",
            "Roboto",
            "TimesNewRoman",
            "EBGaramond",
            "OpenSansCondLight",
            "Oranienbaum",
            "Kaiti" => array("title" => "楷体", "file" => "Kaiti"),
            "heiti" => array("title" => "黑体", "file" => "heiti"),
            "FangSong" => array("title" => "仿宋", "file" => "FangSong"),
            "SongTi" => array("title" => "宋体", "file" => "SongTi"),
            /////////////
            "Gamja Flower",
            "Indie Flower",
            "Charmonman",
            "Pacifico",
            "Gloria Hallelujah",
            "Amatic SC",
            "Shadows Into Light",
            "Dancing Script",
            "Dokdo",
            "Permanent Marker",
            "Patrick Hand",
            "Courgette"
        ];

    }

    public function defaultColors()
    {
        return ["#01579B", "#0277BD", "#0288D1", "#039BE5", "#03A9F4", "#29B6F6", "#4FC3F7", "#81D4FA", "#B3E5FC", "#E1F5FE", "#1B5E20",
            "#2E7D32", "#388E3C", "#43A047", "#4CAF50", "#66BB6A", "#81C784", "#A5D6A7", "#C8E6C9", "#E8F5E9", "#F57F17", "#F9A825",
            "#FBC02D", "#FDD835", "#FFEB3B", "#FFEE58", "#FFF176", "#FFF59D", "#FFF9C4", "#FFFDE7", "#B71C1C", "#C62828", "#D32F2F",
            "#E53935", "#F44336", "#EF5350", "#E57373", "#EF9A9A", "#FFCDD2", "#FFEBEE", "#4A148C", "#6A1B9A", "#7B1FA2", "#8E24AA",
            "#9C27B0", "#AB47BC", "#BA68C8", "#CE93D8", "#E1BEE7", "#F3E5F5", "#3E2723", "#4E342E", "#5D4037", "#6D4C41", "#795548",
            "#8D6E63", "#A1887F", "#BCAAA4", "#D7CCC8", "#EFEBE9", "#212121", "#424242", "#616161", "#757575", "#9E9E9E", "#BDBDBD", "#E0E0E0", "#EEEEEE"];
    }

    public function gefaultBorders()
    {
        return [1, 2, 3, 4, 6, 8, 12, 18];
    }


    private function guid()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }


    public function getFonts()
    {
        $default_fonts = $this->getDefaultFonts();

        $response = $this->formatResponse('success', null, $default_fonts);
        return response($response, 200);
    }

    public function getColors()
    {
        $default_colors = $this->defaultColors();

        $response = $this->formatResponse('success', null, $default_colors);
        return response($response, 200);
    }

    public function getBorders()
    {
        $default_borders = $this->gefaultBorders();

        $response = $this->formatResponse('success', null, $default_borders);
        return response($response, 200);
    }
}
