<?php

namespace App\Http\Controllers;

use App\Document;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Image;
use IMagick;
use PHPExcel;
use PHPExcel_IOFactory;
use Sabre;
use Storage;
use Validator;

class ToolController extends Controller
{
    private $storagePath;


    private function httpPost($url, $data)
    {

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);


        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $response;
    }


    public function __construct()
    {
        $this->storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
    }

    public function upload(Request $request)
    {
        $all_request = $request->all();

        if (isset($all_request['files']))
            $validation = [
                'files' => 'required'
            ];
        else
            $validation = [
                'file' => 'required',
                'file.*' => 'mimes:pdf',
            ];


        $validator = Validator::make($request->all(), $validation);

        $file_num = isset($_POST['file_num']) ? (int)$_POST['file_num'] : null;

        //foreach

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);
        $operationId = time();
        if (isset($all_request['files'])) {
            $path = (!is_null($request->input('path'))) ? $request->input('path') : 'pdf-to-jpg';

            foreach ($all_request['files'] as $key => $file) {
                $extension = $file->getClientOriginalExtension();

                if (count($all_request['files']) == 1 and $file_num !== null) {
                    $key = $file_num;

                }

                if ($path == 'combine-pdf') {
                    $filename = $file->getClientOriginalName();
                } else {
                    if (!is_null($request->input('numFiles')))
                        $filename = $request->input('UUID') . '-' . ($request->input('numFiles') + $key) . '.' . $extension;
                    else
                        $filename = $request->input('UUID') . '-' . $key . '.' . $extension;
                }

                $uploadedFile = Storage::putFileAs('uploads/' . $path . '/' . $request->input('UUID') . '/tmp', $file, $filename);

                Document::create([
                    'user_id' => Auth::check() ? Auth::user()->id : NULL,
                    'UUID' => $request->input('UUID'),
                    'operation_id' => $operationId,
                    'operation_type' => str_replace("-", "", $request->input('path')),
                    'original_document' => $uploadedFile,
                    'original_name' => $file->getClientOriginalName(),
                    "delete_after" => (time() + 86400),
                ]);
            }

            exec("cd " . $this->storagePath . "uploads/" . $path . "/" . $request->input('UUID') . "; mkdir tmp_resize;");
        } else {
            $extension = $request->file('file')->getClientOriginalExtension();

            if (!is_null($request->input('numFiles')))
                $filename = $request->input('UUID') . '-' . $request->input('numFiles') . '.' . $extension;
            else
                $filename = $request->input('UUID') . '.' . $extension;

            $path = (!is_null($request->input('path'))) ? $request->input('path') : 'pdf-to-jpg';
            $path .= '/' . $request->input('UUID');

            if ($request->input('path') == 'merge-pdf' and $extension != 'pdf')
                $path .= '/tmp';

            $uploadedFile = Storage::putFileAs('uploads/' . $path, $request->file('file'), $filename);

            if ($request->input('path') != 'merge-pdf' or $extension == 'pdf')
                exec("cd " . $this->storagePath . "uploads/" . $path . "; mkdir tmp;");

            Document::create([
                'user_id' => Auth::check() ? Auth::user()->id : NULL,
                'UUID' => $request->input('UUID'),
                'operation_id' => $operationId,
                'operation_type' => str_replace("-", "", $request->input('path')),
                'original_document' => $uploadedFile,
                'original_name' => $request->file('file')->getClientOriginalName(),
                "delete_after" => (time() + 86400),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'operation_id' => $operationId
        ]);
    }

    public function combine_pdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required',
            'files' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $pwd_line = '';
        if (!is_null($request->input('pdf_password')))
            $pwd_line = ' input_pw ' . $request->input('pdf_password');

        $path = $this->storagePath . "uploads/combine-pdf/" . $request->input('UUID');

        $files = json_decode($request->input('files'));


        $proccessed_files = [];
        $letter = "A";
        $files_letters = [];
        $ranges = [];

        foreach ($files as $file1 => $pages) {
            if (!isset($pages->file)) {
                $file = "/var/www/html/pdf-magic/public/blank.pdf";
            } else {
                $file = $pages->file;
            }
            $result = [];


            $page = $pages->page;
            $pr = $pages->rotation;
            if (!isset($files_letters[$file])) {
                $letter++;
                $files_letters[$file] = $letter;
            }
            $cur_letter = $files_letters[$file];

            if (intval($pr) != 0) {
                switch (intval($pr)) {
                    case 90:
                        $rotate = 'east';
                        break;
                    case 180:
                        $rotate = 'south';
                        break;
                    case 270:
                        $rotate = 'west';
                        break;
                    default:
                        $rotate = 'east';
                }
            } else {
                $rotate = "";
            }

            $ranges[] = $cur_letter . $page . $rotate;


            if (isset($proccessed_files[$file])) {

            }
        }
        $finp = "";
        foreach ($files_letters as $l => $f) {
            if (strpos($l, "/var/www/html/pdf-magic/public/blank.pdf") !== false) {
                $finp .= "$f=" . escapeshellarg("" . $l) . " ";
            } else {
                $finp .= "$f=" . escapeshellarg("./" . $l) . " ";
            }
        }

        $outfile = $request->input('UUID') . ".pdf";

        $command = "cd " . $path . "/tmp; pdftk $finp cat " . (implode(" ", $ranges)) . " output " . escapeshellarg("../$outfile");

        shell_exec($command);


        $file = '/storage/uploads/combine-pdf/' . $request->input('UUID') . '/' . $request->input('UUID') . '.pdf';
        $pp = "/var/www/html/pdf-magic/public" . $file;


        shell_exec("pdftocairo -pdf $pp $pp.compressed");
        if (is_file("$pp.compressed")) {
        }
        rename("$pp.compressed", $pp);

        $filename = uniqid('deftpdf_') . '.pdf';


        return response()->json([
            'file' => $file,
            'filename' => $filename,
            'status' => 'success'
        ]);
    }

    public function pdf_to_excel(Request $request)
    {
        $req = $request;
        $uuid = $req->post("uuid");
        $operation_type = "excelpdf";
        $doc_file = Document::where(['UUID' => $uuid, 'operation_type' => $operation_type])->orderBy('ID', 'desc')->first()->toArray();

        $original_file = EditPdf::getFilePath($uuid, $operation_type);
        $temp_file = $original_file . ".txt";

        if ($request->input('type') == 'csv') {
            $csv = [];
            $format = ".cvs";
            $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".csv"));
        } else {
            $format = ".xlsx";
            $dest_file = (EditPdf::getDestPath($uuid, $operation_type, ".xlsx"));
        }

        if (!is_file($original_file)) {
            return response()->json(['success' => false, 'message' => 'Original file not found...']);
        }

        $validator = Validator::make($request->all(), [
            'UUID' => 'required',
            'numPages' => 'required',
            'tables' => 'required',
            'basename' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $path = $this->storagePath . "uploads/pdf-to-excel/" . $request->input('UUID');

        $objPHPExcel = new PHPExcel();
        $ActiveSheet = 0;

        $alphas = range('A', 'Z');

        $tables = $request->input('tables');

        $pwd_line = '';
        if (!is_null($request->input('pdf_password')))
            $pwd_line = ' -opw ' . $request->input('pdf_password');

//        $tables = [
//            1 => [
//                [
//                    "top" => 143.49977643848013,
//                    "left" => 89.99985978720007,
//                    "width" => 456.39931337933683,
//                    "height" => 193.49969854248016
//                ]
//            ]
//        ];

        for ($numPage = 1; $numPage <= $request->input('numPages'); $numPage++) {

//            exec("cd " . $path . "; pdftotext -bbox-layout -f " . $numPage . " -l " .
//              $numPage . " " . $request->input('UUID') . ".pdf");
//            $xml = File::get($path . '/' . $request->input('UUID') . '.html');

            $xml = shell_exec("pdftotext" . $pwd_line . " -bbox-layout -f " . $numPage . " -l " . $numPage . " " . $original_file . " - 2>&1 ");

            $xml = strstr($xml, '<page');
            $xml = substr($xml, 0, strrpos($xml, '</page>') + 7);

            $service = new Sabre\Xml\Service();

            $doc = $service->parse($xml);

            $blocks = [];

            foreach ($doc as $flow) {

                foreach ($flow['value'] as $block) {
                    if (!isset($tables[$numPage]))
                        continue;

                    $valueString = '';

                    foreach ($block['value'] as $key_block => $line) {
                        foreach ($line['value'] as $key_line => $val)
                            $valueString .= (!$key_block and !$key_line) ? $val['value'] : ' ' . $val['value'];
                    }

                    $occurrence = false;

                    foreach ($tables[$numPage] as $coordinates) {
                        if ($block['attributes']['yMin'] >= $coordinates['top'] and $block['attributes']['xMin'] >= $coordinates['left'])
                            $occurrence = true;
                    }

                    if (!$occurrence)
                        continue;

                    $blocks[] = [
                        'valueString' => $valueString,
                        'xMin' => $block['attributes']['xMin'],
                        'xMax' => $block['attributes']['xMax'],
                        'yMin' => $block['attributes']['yMin'],
                        'yMax' => $block['attributes']['yMax']
                    ];
                }
            }

            $yMin = array_column($blocks, 'yMin');
            $xMin = array_column($blocks, 'xMin');

            array_multisort($yMin, SORT_ASC, $xMin, SORT_ASC, $blocks);

            $rows_size = [];

            foreach ($blocks as $block) {
                if (empty($rows_size)) {
                    $rows_size[0] = [
                        'yMin' => $block['yMin'],
                        'yMax' => $block['yMax']
                    ];
                } else {
                    $add_row = false;

                    foreach ($rows_size as $key => $row_size) {
                        if (($block['yMin'] >= $row_size['yMin'] and $block['yMin'] <= $row_size['yMax']) or ($block['yMax'] <= $row_size['yMin'] and $block['yMax'] >= $row_size['yMax'])) {
                            $rows_size[$key]['yMin'] = ($rows_size[$key]['yMin'] > $block['yMin']) ? $block['yMin'] : $rows_size[$key]['yMin'];
                            $rows_size[$key]['yMax'] = ($rows_size[$key]['yMax'] < $block['yMax']) ? $block['yMax'] : $rows_size[$key]['yMax'];

                            continue 2;
                        } else
                            $add_row = true;
                    }

                    if ($add_row) {
                        $rows_size[count($rows_size)] = [
                            'yMin' => $block['yMin'],
                            'yMax' => $block['yMax']
                        ];
                    }
                }
            }

            $rows = [];
            foreach ($rows as $key => $row)
                $rows[$key] = [];

            foreach ($blocks as $block) {
                $number_row = 0;

                foreach ($rows_size as $key => $row_size)
                    if ($block['yMin'] >= $row_size['yMin'] and $block['yMin'] <= $row_size['yMax'])
                        $number_row = $key;

                $rows[$number_row][] = [
                    'valueString' => $block['valueString'],
                    'xMin' => $block['xMin'],
                    'xMax' => $block['xMax']
                ];
            }

            foreach ($rows as $key => $row) {
                $xMin = array_column($row, 'xMin');
                array_multisort($xMin, SORT_ASC, $row);

                $rows[$key] = $row;
            }

            foreach ($rows as $key => $row) {
                foreach ($row as $key_col => $col) {
                    if (isset($row[$key_col + 1])) {
                        $next_col = $row[$key_col + 1];

                        if ($col['xMax'] > $next_col['xMin']) {
                            $rows[$key][$key_col]['valueString'] = $col['valueString'] . ' ' . $next_col['valueString'];
                            $rows[$key][$key_col]['xMin'] = ($col['xMin'] < $next_col['xMin']) ? $col['xMin'] : $next_col['xMin'];
                            $rows[$key][$key_col]['xMax'] = ($col['xMax'] > $next_col['xMax']) ? $col['xMax'] : $next_col['xMax'];

                            unset($rows[$key][$key_col + 1]);
                        }
                    }
                }
            }

            $max_columns = 1;
            $min_xMin = false;
            $min_xMax = false;

            foreach ($rows as $key => $row) {
                if (count($row) > $max_columns)
                    $max_columns = count($row);

                foreach ($row as $col)
                    if (!$min_xMin or $col['xMin'] < $min_xMin) {
                        $min_xMin = $col['xMin'];
                        $min_xMax = $col['xMax'];
                    }
            }

            $cols_size = [];
            for ($i = 0; $i < $max_columns; $i++)
                $cols_size[$i] = (!$i) ? [$min_xMin, $min_xMax] : [];

            foreach ($rows as $key => $row) {
                foreach ($row as $number_col => $col) {
                    foreach ($cols_size as $key => $col_size) {
                        if (empty($col_size)) {
                            $cols_size[$key] = [$col['xMin'], $col['xMax']];
                            continue 2;
                        } elseif ($col['xMin'] < $col_size[1] and ((count($row) == $max_columns and $number_col == $key) or count($row) != $max_columns)) {
                            $min = $col['xMin'] < $col_size[0] ? $col['xMin'] : $col_size[0];
                            $max = $col['xMax'] > $col_size[1] ? $col['xMax'] : $col_size[1];

                            $cols_size[$key] = [$min, $max];
                            continue 2;
                        }
                    }
                }
            }

            foreach ($rows as $key => $row) {
                if (count($row) != $max_columns) {
                    $tmp_row = [];
                    foreach ($row as $number_col => $col) {
                        foreach ($cols_size as $number_real_col => $col_size) {
                            if (isset($col_size[0]) and $col_size[0] <= $col['xMin'] and $col_size[1] >= $col['xMax']) {
                                $tmp_row[$number_real_col] = $col;
                                continue;
                            } elseif (!isset($tmp_row[$number_real_col]))
                                $tmp_row[$number_real_col] = [
                                    'valueString' => ''
                                ];
                        }
                    }

                    $rows[$key] = $tmp_row;
                }
            }
//

            if (isset($csv)) {
                foreach ($rows as $line => $row) {
                    foreach ($row as $key => $column) {
                        $csv[$line][] = $column['valueString'];
                    }
                }
            } else {
                if ($ActiveSheet > 0)
                    $objPHPExcel->createSheet($ActiveSheet);

                $objPHPExcel->setActiveSheetIndex($ActiveSheet);
                $ActiveSheet++;
                if ($rows) {
                    $objPHPExcel->getActiveSheet()->setTitle("Page " . $numPage);

                    foreach ($rows as $line => $row) {
                        foreach ($row as $key => $column) {
                            $objPHPExcel->getActiveSheet()->setCellValue($alphas[$key] . ($line + 1), $column['valueString']);
                        }
                    }

                    foreach ($alphas as $columnID) {
                        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                            ->setAutoSize(true);
                    }
                }
            }
        }

        if (isset($csv)) {
            $content = '';

            foreach ($csv as $line_key => $values) {
                if ($line_key > 0)
                    $content .= PHP_EOL;

                foreach ($values as $key => $value) {
                    if ($key > 0)
                        $content .= ',';

                    $content .= $value;
                }
            }

            File::put($dest_file, $content);
        } else {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objPHPExcel->setActiveSheetIndex(0);
            //$objWriter->save($path . '/' . $request->input('UUID') . '.xlsx');
            $objWriter->save($dest_file);
        }

        $file = '/storage/uploads/pdf-to-excel/' . $request->input('UUID') . '/' . $request->input('UUID') . '.xlsx';
        $filename = $request->input('basename') . '.xlsx';


        Document::where('id', $doc_file['id'])->update(['edited_document' => $dest_file, "delete_after" => (time() + 18000)]);

        return response()->json(['success' => true,
            'new_file_name' => EditPdf::getNewFileName($doc_file['original_name'], "", $format),
            'url' => EditPdf::getDownloadLink($uuid, strtolower($operation_type))]);

        return response()->json([
            'file' => $file,
            'filename' => $filename,
            'status' => 'success'
        ]);
    }

    public function get_doc_size(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required'
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $pwd_line = '';
        if (!is_null($request->input('pdf_password')))
            $pwd_line = ' -opw ' . $request->input('pdf_password');

        $path = "cd " . $this->storagePath . "uploads/split-in-half/" . $request->input('UUID');

        exec($path . "; pdfinfo" . $pwd_line . " " . $request->input('UUID') . ".pdf 2>&1", $result);

        $page_size = '594.96x841.92';

        foreach ($result as $value)
            if (strpos($value, 'Page size:') !== false)
                $page_size = preg_replace('/[^0-9x.]/', '', $value);

        $page_size = explode('x', $page_size);

        $width = floatval($page_size[0]) / 72;
        $width = ceil($width * 100) / 100;

        $height = floatval($page_size[1]) / 72;
        $height = ceil($height * 100) / 100;

        return response()->json([
            'width' => $width,
            'height' => $height,
            'status' => 'success'
        ]);
    }

    public function split_in_half(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required',
            'basename' => 'required',
            'numPages' => 'required',
            'split' => 'required',
            'width' => 'required',
            'height' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $path = "cd " . $this->storagePath . "uploads/split-in-half/" . $request->input('UUID');


        $pwd_line = '';
        if (!is_null($request->input('pdf_password')))
            $pwd_line = ' input_pw ' . $request->input('pdf_password');

        /* delete pages end */

        if (!is_null($request->input('pattern'))) {
            $command = 'pdftk ' . $request->input('UUID') . '.pdf' . $pwd_line . ' cat ';

            if (!preg_match('#^[0-9\-\,]+$#', $request->input('pattern')))
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pattern error.'
                ]);

            $pages = [];

            for ($i = 1; $i <= intval($request->input('numPages')); $i++)
                $pages[] = $i;

            $patterns = explode(',', $request->input('pattern'));

            foreach ($patterns as $key => $val)
                $patterns[$key] = trim($val);

            foreach ($patterns as $key => $val) {
                $patterns[$key] = explode('-', $val);
            }

            foreach ($patterns as $pattern) {
                if (count($pattern) == 1) {
                    if (array_search(intval($pattern[0]), $pages) !== false)
                        unset($pages[array_search(intval($pattern[0]), $pages)]);
                } else {
                    if (empty($pattern[1])) {
                        $num = intval($request->input('numPages'));
                        for ($i = intval($pattern[0]) + 1; $i <= $num; $i++) {
                            if (array_search($i, $pages) !== false)
                                unset($pages[array_search($i, $pages)]);
                        }
                    } else {
                        for ($i = intval($pattern[0]); $i <= intval($pattern[1]); $i++) {
                            if (array_search($i, $pages) !== false)
                                unset($pages[array_search($i, $pages)]);
                        }
                    }
                }
            }

            foreach ($pages as $page) {
                if (is_null($page))
                    continue;

                $command .= $page . ' ';
            }

            $command .= 'output ' . $request->input('UUID') . '_deleted_pages.pdf';

            exec($path . "; " . $command);
            $start_file = $request->input('UUID') . '_deleted_pages.pdf';
        } else
            $start_file = $request->input('UUID') . ".pdf";

        /* delete pages end */

        if (!is_null($request->input('pdf_password')))
            $pwd_line = ' -opw ' . $request->input('pdf_password');

        exec($path . "; pdfinfo" . $pwd_line . " " . $start_file . " 2>&1", $result);

        $page_size = '594.96x841.92';

        foreach ($result as $value) {
            if (strpos($value, 'Page size:') !== false) {
                $page_size = preg_replace('/[^0-9x.]/', '', $value);
            }
        }

        if (isset($_COOKIE['testb'])) {
//        	echo "<pre>";
//        	var_dump($page_size);
//        	exit();
            // $page_size = '594.96x1841.92';
        }

        $page_size = explode('x', $page_size);

        $width = floatval($page_size[0]) / 72;
        $width = ceil($width * 100) / 100;

        $height = floatval($page_size[1]) / 72;
        $height = ceil($height * 100) / 100;

        if (!is_null($request->input('pdf_password'))) {
            exec($path . "; pdftk " . $start_file . " input_pw " . $request->input('pdf_password') . " output " . $request->input('UUID') . "_decrypted.pdf", $result);
            $start_file = $request->input('UUID') . "_decrypted.pdf";
        }


        if ($request->input('split') == 'vertically') {
            $left = round($request->input('width') / 96, 3);
            $right = $width - $left;

            try {
                $scale_left = $width / $left;
                $scale_right = $width / $right;
            } catch (Exception $e) {
                $scale_left = 1;
                $scale_right = 1;
            }


            exec($path . "; pdfjam -o " . $request->input('UUID') . "_resize_left.pdf --trim '0in 0in " . $right . "in 0in' --clip true " . $start_file . " 2>&1", $result);
            exec($path . "; pdfjam --outfile " . $request->input('UUID') . "_split_left.pdf --scale " . $scale_left . "  --papersize '{" . $left . "in," . $height . "in}' " . $request->input('UUID') . "_resize_left.pdf 2>&1", $result);

            exec($path . "; pdfjam -o " . $request->input('UUID') . "_resize_right.pdf --trim '" . $left . "in 0in 0in 0in' --clip true " . $start_file . " 2>&1", $result);
            exec($path . "; pdfjam --outfile " . $request->input('UUID') . "_split_right.pdf --scale " . $scale_right . " --papersize '{" . $right . "in," . $height . "in}' " . $request->input('UUID') . "_resize_right.pdf 2>&1", $result);
        } else {


            $top = round($request->input('height') / 96, 3);
            $bottom = $height - $top;

            $scale_top = $height / $top;
            $scale_bottom = $height / $bottom;

            if (true) { //isset($_COOKIE['testb'])){
                $scale_top = 1;
                $scale_bottom = 1;


                //$x = ($path . "; pdfjam --papersize '{{$width}in,{$top}in}' -o " . $request->input('UUID') . "_resize_left.pdf --trim '0in " . $bottom . "in 0in 0in' --clip true " . $start_file . " 2>&1");;


                exec($path . "; pdfjam --papersize '{{$width}in,{$top}in}' -o  " . $request->input('UUID') . "_resize_left.pdf --trim '0in " . $bottom . "in 0in 0in' --clip true " . $start_file . " 2>&1", $result);
                //pdfjam --papersize '{216mm,556mm}' ./p1_resized.pdf -o out.pdf
                //exit("pdfjam -o " . $request->input('UUID') . "_resize_left.pdf --trim '0in " . $bottom . "in 0in 0in' --clip true " . $start_file . " 2>&1");


                exec($path . "; pdfjam --papersize '{{$width}in,{$bottom}in}' -o " . $request->input('UUID') . "_resize_right.pdf --trim '0in 0in 0in " . $top . "in' --clip true " . $start_file . " 2>&1", $result);
                //exit($path);

            } else {

                exec($path . "; pdfjam -o " . $request->input('UUID') . "_resize_left.pdf --trim '0in " . $bottom . "in 0in 0in' --clip true " . $start_file . " 2>&1", $result);
                exec($path . "; pdfjam --outfile " . $request->input('UUID') . "_split_left.pdf --scale " . $scale_top . " --clip true --papersize '{" . $width . "in," . $top . "in}' " . $request->input('UUID') . "_resize_left.pdf 2>&1", $result);

                exec($path . "; pdfjam -o " . $request->input('UUID') . "_resize_right.pdf --trim '0in 0in 0in " . $top . "in' --clip true " . $start_file . " 2>&1", $result);
                exec($path . "; pdfjam --outfile " . $request->input('UUID') . "_split_right.pdf --scale " . $scale_bottom . " --clip true --papersize '{" . $width . "in," . $bottom . "in}' " . $request->input('UUID') . "_resize_right.pdf 2>&1", $result);
            }

        }

        if ($request->input('arabic') == 'true' and $request->input('split') == 'vertically')
            $sort = "shuffle B A";
        elseif ($request->input('booklet') == 'true')
            $sort = "shuffle B Aend-1";
        else
            $sort = "shuffle A B";


        if (true) { //isset($_COOKIE['testb'])){
            $x = ($path . "; pdftk A=" . $request->input('UUID') . "_resize_left.pdf B=" . $request->input('UUID') . "_resize_right.pdf " . $sort . " output " . $request->input('UUID') . "_split.pdf 2>&1");
            shell_exec($x);
        } else {
            exec($path . "; pdftk A=" . $request->input('UUID') . "_split_left.pdf B=" . $request->input('UUID') . "_split_right.pdf " . $sort . " output " . $request->input('UUID') . "_split.pdf 2>&1", $result);
        }

        $file = '/storage/uploads/split-in-half/' . $request->input('UUID') . '/' . $request->input('UUID') . '_split.pdf';
        $filename = $request->input('basename') . '.pdf';

        return response()->json([
            'file' => $file,
            'filename' => $filename,
            'status' => 'success'
        ]);
    }

    public function repair_pdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required',
            'basename' => 'required',
            'numPages' => 'required'
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $pwd_line = '';
        if (!is_null($request->input('pdf_password')))
            $pwd_line = ' -sPDFPassword=' . $request->input('pdf_password');

        exec("cd " . $this->storagePath . "uploads/repair-pdf/" . $request->input('UUID') . "; gs -o " . $request->input('UUID') . "_repaired.pdf -sDEVICE=pdfwrite" . $pwd_line . " -dPDFSETTINGS=/prepress " . $request->input('UUID') . ".pdf 2>&1", $result);

        $tmp = [];
        $success = false;

        for ($i = 1; $i <= $request->input('numPages'); $i++)
            $tmp[] = 'Page ' . $i;

        foreach ($tmp as $value)
            if (in_array($value, $tmp))
                $success = true;
            else
                $success = false;

        if (!$success)
            return response()->json([
                'status' => 'bad repair'
            ]);

        $file = '/storage/uploads/repair-pdf/' . $request->input('UUID') . '/' . $request->input('UUID') . '_repaired.pdf';
        $filename = $request->input('basename') . '.pdf';

        return response()->json([
            'file' => $file,
            'filename' => $filename,
            'status' => 'success'
        ]);
    }

    public function check_lock_pdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required'
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        exec("cd " . $this->storagePath . "uploads/unlock-pdf/" . $request->input('UUID') . "; pdfinfo " . $request->input('UUID') . ".pdf 2>&1", $result);

        if (is_array($result) and array_search('Command Line Error: Incorrect password', $result) !== false)
            $lock = true;
        else
            $lock = false;

        return response()->json([
            'lock' => $lock,
            'status' => 'success'
        ]);
    }

    public function unlock_pdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required',
            'basename' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        //password

        $uuid = $request->input('UUID');
        $dest_file = (EditPdf::getDestPath($uuid, "unlock", ".pdf"));

        //$download_link = EditPdf::getDownloadLink($uuid, strtolower("unlock"));

        //output

        exec("cd " . $this->storagePath . "uploads/unlock-pdf/" . $request->input('UUID') . "; pdfinfo " . $request->input('UUID') . ".pdf 2>&1", $result);

        if (is_array($result) and array_search('Command Line Error: Incorrect password', $result) !== false) {

            if (!Storage::exists("uploads/unlock-pdf/" . $request->input('UUID') . "/" . $request->input('UUID') . "_unlock.pdf")) {
                exec("cd " . $this->storagePath . "uploads/unlock-pdf/" . $request->input('UUID') . "; pdftk " . $request->input('UUID') . ".pdf input_pw " . escapeshellarg($request->input('pdf_password')) . " output " . $dest_file);
            }
            if (!is_file($dest_file))
                return response()->json([
                    'status' => 'error',
                    'message' => 'Incorrect password.'
                ]);

            $file = '/storage/uploads/unlock-pdf/' . $request->input('UUID') . '/' . $request->input('UUID') . '_unlock.pdf';
        } else {
            exec("cd " . $this->storagePath . "uploads/unlock-pdf/" . $request->input('UUID') . "; ghostscript -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=" . $dest_file . " -c .setpdfwrite -f '" . $request->input('UUID') . ".pdf'");
            $file = '/storage/uploads/unlock-pdf/' . $request->input('UUID') . '/' . $request->input('UUID') . '_unlock.pdf';
        }

        $dest_file = str_replace("//", "/", $dest_file);
        $dest_file = str_replace(base_path("public"), "", $dest_file);

        $filename = $request->input('basename') . '.pdf';

        return response()->json([
            'file' => $dest_file,
            'filename' => $filename,
            'status' => 'success'
        ]);
    }

    public function n_up_pdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required',
            'basename' => 'required',
            'pages_per_sheet' => 'required',
            'page_ordering' => 'required',
            'original_size' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        switch ($request->input('pages_per_sheet')) {
            case "2x1":
                $page_size = 'a4';
                $landscape = 'landscape';
                break;
            case "2x2":
                $page_size = 'a4';
                $landscape = 'no-landscape';
                break;
            case "4x2":
                $page_size = 'a3';
                $landscape = 'landscape';
                break;
            case "4x4":
                $page_size = 'a3';
                $landscape = 'no-landscape';
                break;
            case "8x4":
                $page_size = 'a2';
                $landscape = 'landscape';
                break;
            default:
                $page_size = 'a4';
                $landscape = 'landscape';
        }

        $pwd_line = '';
        if (!is_null($request->input('pdf_password')))
            $pwd_line = ' -opw ' . $request->input('pdf_password');

        exec("cd " . $this->storagePath . "uploads/n-up-pdf/" . $request->input('UUID') . "; pdfinfo" . $pwd_line . " '" . $request->input('UUID') . ".pdf' 2>&1", $result);

        if (!is_null($request->input('more_options')) and $request->input('more_options') == "true") {

            if ($request->input('page_ordering') == 'horizontal')
                $landscape = 'landscape';
            else
                $landscape = 'no-landscape';


            if ($request->input('original_size') == '1') {
                $original_size = false;

                foreach ($result as $value)
                    if (strpos($value, 'Page size:') !== false)
                        $original_size = preg_replace('/[^0-9x.]/', '', $value);

                if ($original_size) {
                    $original_size = explode('x', $original_size);
                    $papersize = ' --papersize "{' . $original_size[0] . 'px,' . $original_size[1] . 'px}"';
                }
            }
        }

        $start_file = $request->input('UUID') . ".pdf";

        if (!is_null($request->input('pdf_password'))) {
            exec("cd " . $this->storagePath . "uploads/n-up-pdf/" . $request->input('UUID') . "; pdftk '" . $start_file . "' input_pw " . $request->input('pdf_password') . " output " . $request->input('UUID') . "_decrypted.pdf", $result);
            $start_file = $request->input('UUID') . "_decrypted.pdf";
        }

        if (isset($papersize))
            exec("cd " . $this->storagePath . "uploads/n-up-pdf/" . $request->input('UUID') . "; pdfnup --nup " . $request->input('pages_per_sheet') . " --suffix '" . $request->input('pages_per_sheet') . "' --" . $landscape . " " . $papersize . " " . $start_file);
        else
            exec("cd " . $this->storagePath . "uploads/n-up-pdf/" . $request->input('UUID') . "; pdfnup --nup " . $request->input('pages_per_sheet') . " --suffix '" . $request->input('pages_per_sheet') . "' --" . $landscape . " --paper " . $page_size . "paper " . $start_file);

        if (!is_null($request->input('pdf_password')))
            $file = '/storage/uploads/n-up-pdf/' . $request->input('UUID') . '/' . $request->input('UUID') . '_decrypted-' . $request->input('pages_per_sheet') . '.pdf';
        else
            $file = '/storage/uploads/n-up-pdf/' . $request->input('UUID') . '/' . $request->input('UUID') . '-' . $request->input('pages_per_sheet') . '.pdf';
        $filename = $request->input('basename') . '.pdf';

        return response()->json([
            'file' => $file,
            'filename' => $filename,
            'status' => 'success'
        ]);
    }

    public function doc_to_pdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required',
            'basename' => 'required',
            'file_ext' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        exec("cd " . $this->storagePath . "uploads/doc-to-pdf/" . $request->input('UUID') . "; unoconv -f pdf " . $request->input('UUID') . "." . $request->input('file_ext') . " 2>&1", $result);

        $file = '/storage/uploads/doc-to-pdf/' . $request->input('UUID') . '/' . $request->input('UUID') . '.pdf';
        $filename = $request->input('basename') . '.pdf';

        return response()->json([
            'file' => $file,
            'filename' => $filename,
            'status' => 'success'
        ]);
    }


    private function GeneratePageOptions($request)
    {
        switch ($request->input('pageSize')) {
            case "a2":
                $page_size = "23.39in 33.11in";
                break;
            case "a3":
                $page_size = "11.69in 16.54in";
                break;
            case "a4":
                $page_size = "8.27in 11.69in";
                break;
            case "letter":
                $page_size = "8.5in 11in";
                break;
            case "legal":
                $page_size = "8.5in 14in";
                break;
            //case "long":
            //$page_size = false;
            //break;
            default:
                $page_size = "8.27in 11.69in";
        }

        if (!File::exists($this->storagePath . "uploads/html-to-pdf/" . $request->input('UUID'))) {
            Storage::makeDirectory("uploads/html-to-pdf/" . $request->input('UUID'));
            Storage::makeDirectory("uploads/html-to-pdf/" . $request->input('UUID') . "/tmp");
        }

        if (!$page_size and !is_null($request->input('urls'))) {
            $page_size = [];

            $urls = explode(PHP_EOL, $request->input('urls'));
            $urls_processed = [];

            foreach ($urls as $url) {
                $url = trim($url);

                if (strpos($url, 'http://') !== 0 and strpos($url, 'https://') !== 0)
                    $url = 'http://' . $url;

                $urls_processed[] = $url;
            }

            foreach ($urls_processed as $key => $url) {
                exec('cd /home/ubuntu; nodejs screen.node.js ' . $url . ' 797 ' . $this->storagePath . 'uploads/html-to-pdf/' . $request->input('UUID') . ' screen-' . $key);
                $img = Image::make($this->storagePath . 'uploads/html-to-pdf/' . $request->input('UUID') . '/screen-' . $key . '.jpg');

                $page_size[$key] = $img->width() . 'px ' . $img->height() . 'px';
            }

        }
        $pmu = "px";
        $pm2 = floatval($request->input('pageMargin'));

        if (!is_null($request->input('pageMargin'))) {
            switch ($request->input('pageMarginUnits')) {
                case "px":
                    $page_margin = floatval($request->input('pageMargin')) . "px";
                    break;
                case "in":
                    $page_margin = floatval($request->input('pageMargin')) . "in";
                    $pmu = "in";
                    break;
                case "cm":
                    $pmu = "cm";
                    $page_margin = floatval($request->input('pageMargin')) . "cm";
                    break;
                case "mm":
                    $pmu = "mm";
                    $page_margin = floatval($request->input('pageMargin')) / 10 . "cm";
                    break;
                default:
                    $page_margin = "0px";
            }
        } else {
            $pm2 = "0";
            $page_margin = "0px";
        }

        if ($request->input('pageOrientation') == "landscape" and $page_size) {
            if (is_array($page_size)) {
                foreach ($page_size as $key => $tmp) {
                    $tmp = explode(' ', $tmp);
                    $page_size[$key] = $tmp[1] . ' ' . $tmp[0];
                }
            } else {
                $page_size = explode(' ', $page_size);
                $page_size = $page_size[1] . ' ' . $page_size[0];
            }
        }

        if (!is_null($request->input('viewportWidth')) and intval($request->input('viewportWidth')) > 100 and $page_size) {
            if (is_array($page_size)) {
                foreach ($page_size as $key => $tmp) {
                    $tmp = explode(' ', $tmp);
                    $page_size[$key] = intval($request->input('viewportWidth')) . 'px ' . ' ' . $tmp[1];
                }
            } else {
                $page_size = explode(' ', $page_size);
                $page_size = intval($request->input('viewportWidth')) . 'px ' . $page_size[1];
            }
        }

        return [
            'page_size' => $page_size,
            'page_margin' => $page_margin,
            "pm" => $pm2,
            "pmu" => $pmu
        ];
    }

    public function html_to_pdf(Request $request)
    {


        //pageMargin
        $validator = Validator::make($request->all(), [
            'UUID' => 'required'
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);


        $pageOptions = $this->GeneratePageOptions($request);

        //page_margin

        //$pageOptions['page_margin'] = 0;


        if (!is_null($request->input('urls'))) {
            $urls = explode(PHP_EOL, $request->input('urls'));
            $urls_processed = [];

            foreach ($urls as $url) {
                $url = trim($url);

                if (strpos($url, 'http://') !== 0 and strpos($url, 'https://') !== 0)
                    $url = 'http://' . $url;

                $urls_processed[] = $url;
            }

            $command = '';

            foreach ($urls_processed as $url)
                $command .= 'wget -k -l 0 -p -E -nc ' . $url . '; ';

            exec("cd " . $this->storagePath . "uploads/html-to-pdf/" . $request->input('UUID') . "/tmp;" . $command);

            $tmp_filenames = [];

            foreach ($urls_processed as $key => $url) {
                $domain = parse_url($url)['host'];
                $url = str_replace(['http://', 'https://'], '', $url);

                if (strpos($url, '/') !== false)
                    $url = substr($url, strpos($url, '/', 9));
                else
                    $url = 'index';

                if (!strlen($url) or $url == '/')
                    $url = 'index';

                $tmp_filename = str_replace('.', '_', $domain) . '.pdf';
                $tmp_filenames[] = $tmp_filename;

                $chars = ['?', '#'];

                foreach ($chars as $char)
                    if (strpos($url, $char) !== false)
                        $url = substr($url, 0, strpos($url, $char));

                $path = $this->storagePath . "uploads/html-to-pdf/" . $request->input('UUID') . '/tmp/' . $domain . '/' . $url . '.html';


                $command = 'google-chrome-stable --headless --disable-gpu --print-to-pdf="' . $this->storagePath . 'uploads/html-to-pdf/' . $request->input('UUID') . '/' . $tmp_filename . '" ' . $path;

                $tmp_page_size = is_array($pageOptions['page_size']) ? $pageOptions['page_size'][$key] : $pageOptions['page_size'];

                File::append($path, '<style> @media print { ::-webkit-scrollbar { display: none; } @page { margin: 0px; size:' . $tmp_page_size . '; }
                body {
                position: absolute; marg1in: ' . $pageOptions['page_margin'] . ';} } </style>');

                exec("cd " . $this->storagePath . "uploads/html-to-pdf/" . $request->input('UUID') . "/tmp;" . $command);
            }

            $filename = uniqid('deftpdf_') . '_html_to_pdf.zip';
            $file = '/storage/uploads/html-to-pdf/' . $request->input('UUID') . '/' . $filename;

            $command = 'zip ' . $filename;

            foreach ($tmp_filenames as $tmp_filename)
                $command .= ' ' . $tmp_filename;

            exec("cd " . $this->storagePath . "uploads/html-to-pdf/" . $request->input('UUID') . "; " . $command);
        }

        if (!is_null($request->input('code'))) {
            $code = $request->input('code');

            $tmp_page_size = is_array($pageOptions['page_size']) ? $pageOptions['page_size'][$key] : $pageOptions['page_size'];
            $code = '<style> @media print { ::-webkit-scrollbar { display: none; } @page { margin: 0px; size:' . $tmp_page_size . '; margin: ' . $pageOptions['page_margin'] . '; }
            	body { position: absolute; margi1n: ' . $pageOptions['page_margin'] . ';} } </style>' . $code;

            $path = $this->storagePath . "uploads/html-to-pdf/" . $request->input('UUID') . '/tmp/index.html';
            $tmp_name = uniqid() . '.pdf';

            File::put($path, $code);

            $command = 'google-chrome-stable --headless --disable-gpu --print-to-pdf="' . $this->storagePath . 'uploads/html-to-pdf/' . $request->input('UUID') . '/' . $tmp_name . '" ' . $path;

            exec("cd " . $this->storagePath . "uploads/html-to-pdf/" . $request->input('UUID') . "/tmp;" . $command);

            $file = '/storage/uploads/html-to-pdf/' . $request->input('UUID') . '/' . $tmp_name;
            $filename = $tmp_name;
        }

        if (is_null($request->input('code')) and is_null($request->input('urls'))) {

            $path = $this->storagePath . "uploads/html-to-pdf/" . $request->input('UUID') . '/' . $request->input('UUID') . '.html';
            $tmp_name = uniqid() . '.pdf';

            $tmp_page_size = is_array($pageOptions['page_size']) ? $pageOptions['page_size'][$key] : $pageOptions['page_size'];
            File::append($path, '<style> @media print { ::-webkit-scrollbar { display: none; }
            	@page {  background: red; margin: 1000px; padding: 0; size:' . $tmp_page_size . '; margin: ' . $pageOptions['page_margin'] . ' }
            body { position: absolute; margin: ' . $pageOptions['page_margin'] . ';} } </style>');

            $command = 'google-chrome-stable --headless --disable-gpu --print-to-pdf="' . $this->storagePath . 'uploads/html-to-pdf/' . $request->input('UUID') . '/' . $tmp_name . '" ' . $path;

            exec("cd " . $this->storagePath . "uploads/html-to-pdf/" . $request->input('UUID') . ";" . $command);

            $file = '/storage/uploads/html-to-pdf/' . $request->input('UUID') . '/' . $tmp_name;
            $filename = $tmp_name;

        }

        $dp = storage_path('app/uploads/html-to-pdf/' . $request->input('UUID') . '/' . $tmp_name);


//        $margins = "";
//        switch($pageOptions['pmu']){
//        	case 'in':
//        		$x = 72*$pageOptions['pm'];
//        	break;
//        	case 'cm':
//        		$x = 28.346456693*$pageOptions['pm'];
//        	break;
//        	case 'mm':
//        		$x = 2.834645669*$pageOptions['pm'];
//        	break;
//        	case 'px':
//         		$x = 0.75*$pageOptions['pm'];
//        	break;
//
//        }
//   		$margins = "$x $x $x $x";
//        $com = "pdfcrop --margin '$margins' $dp $dp.m";
//        shell_exec($com);
//        rename("$dp.m", $dp);


//        shell_exec();
//		exit($dp);

        if (!isset($file) or !isset($filename)) {
            return response()->json([
                'message' => '',
                'status' => 'error'
            ]);
        }


        return response()->json([
            'file' => $file,
            'filename' => $filename,
            'status' => 'success'
        ]);
    }

    public function merge_pdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required',
            'basename' => 'required',
        ]);

        $ranges = $request->post("ranges");


        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

		$files = Storage::files("uploads/merge-pdf/" . $request->input('UUID') . "/tmp");

		$keys = range("A", "Z");

        if($request->has('page')){
//			if(is_array($request->input('page')[0])){
				foreach ($request->input('page') as $doc_key => $document) {
					$command2_temp_arr = [];
					if(is_array($document)) {
						foreach ( $document as $page ) {
							if ( ! is_null( $page ) ) {
								$command2_temp_arr[] = $page;
							}
						}
					}
//					dump($command2_temp_arr);

					if($command2_temp_arr){
						$name = explode('/', $files[$doc_key]);
						$command2_arr[$keys[$doc_key]] = ['file' => end($name), 'pages' => $command2_temp_arr];
					}
				}

				if($command2_arr){
					$command2 = 'pdftk ';
					$command2_1 = '';
					$command2_2_arr = [];

					foreach($command2_arr as $cletter => $citem){
						$command2_1 .= $cletter . '="' . $citem['file'] . '" ';
						foreach($citem['pages'] as $p){
							$command2_2_arr[] = $cletter.$p;
						}
					}

					$command2_2 = implode(' ', $command2_2_arr);
				}

				$tmp_name = $request->input('basename') . '_merged.pdf';

				$command2 .= $command2_1 . 'cat ' . $command2_2 . ' output "../' . $tmp_name . '"';
//				dd($command2);
//				dd($command2_arr);

				exec("cd " . $this->storagePath . "uploads/merge-pdf/" . $request->input('UUID') . "/tmp; " . $command2);

				$file = '/storage/uploads/merge-pdf/' . $request->input('UUID') . '/' . $tmp_name;

//				dump(\App\Custom\PDFHelpers::replacePages(base_path("public").$file));
//				dd(base_path("public"));
				$file = str_replace(base_path("public"), '', \App\Custom\PDFHelpers::replacePages(base_path("public").$file));

				return response()->json([
					'file' => $file,
					'filename' => $tmp_name,
					'status' => 'success'
				]);
//			}
		}

        if (count($files)) {
            foreach ($files as $file) {
                $command = 'convert ';

                $name = explode('/', $file);
                $name = end($name);
                $ext = substr($name, strripos($name, '.') + 1);
                $name = substr($name, 0, strripos($name, '.'));

                if ($ext != 'pdf') {
                    $image = new \IMagick($this->storagePath . $file);
                    $flattened = new \IMagick();

                    $img_width = $image->getImageWidth();
                    $img_height = $image->getImageHeight();

                    $image->resizeImage($img_width, $img_height, \Imagick::FILTER_LANCZOS, 1);
                    $flattened->newImage($img_width, $img_height, new \ImagickPixel("white"));

                    $flattened->compositeImage($image, imagick::COMPOSITE_OVER, 0, 0);
                    $flattened->setImageFormat("jpg");

                    $flattened->writeImage($this->storagePath . 'uploads/merge-pdf/' . $request->input('UUID') . '/tmp/' . $name . '.jpg');

                    $image->clear();
                    $image->destroy();
                    $flattened->clear();
                    $flattened->destroy();

                    $file = $this->storagePath . 'uploads/merge-pdf/' . $request->input('UUID') . '/tmp/' . $name . '.jpg';

                    $command .= $file . " ../tmp/" . $name . '.pdf;';
                    exec("cd " . $this->storagePath . "uploads/merge-pdf/" . $request->input('UUID') . "/tmp; " . $command, $x);
                } /*else {
                    $command .= 'cp ' . $name . '.pdf ../' . $name . '.pdf;';
                    exec("cd " . $this->storagePath . "uploads/merge-pdf/" . $request->input('UUID') . "/tmp; " . $command);
                }*/
            }
        }

        $tmp_name = uniqid() . '.pdf';

        //$command = "gs -dAutoRotatePages=/None -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -dPDFSETTINGS=/prepress -sOutputFile=" . $tmp_name;

        $command = "pdftk";

        $blankPageIfOdd = (int)$request->post("blankPageIfOdd");
        $normalizePageSizes = (int)$request->post("normalizePageSizes");
        $tableOfContents = $request->post("tableOfContents");
        $outline = $request->post("outline");


        $filenameFooter = (int)$request->post("filenameFooter");
        $fn_names = $request->post("file_names");

        $first_page_size = false;

        $tofcont = array();
        $new_document_total_pages = 0;
        $command_files = [];
        $files_pc = [];
        $files_bookmarks = [];

        $document_ranges = [];

		$doc_key = "A";

		if(!$request->has('docs')){
			return response()->json([
				'status' => 'false'
			]);
		}

        foreach ($request->input('docs') as $num) {
            $doc_key = $keys[$num];
            $result = [];
            exec("cd " . $this->storagePath . "uploads/merge-pdf/" . $request->input('UUID') . "; pdfinfo tmp/" . $request->input('UUID') . "-" . $num . ".pdf 2>&1", $result);
            $st_path = $this->storagePath . "uploads/merge-pdf/" . $request->input('UUID') . "/";


            $inpf = "tmp/" . $request->input('UUID') . "-" . $num . ".pdf";
            $outf = "tmp/" . $request->input('UUID') . "-" . $num . "_out.pdf";
            $doc_pages = (int)shell_exec("cd " . $this->storagePath . "uploads/merge-pdf/" . $request->input('UUID') . "; " . "pdfinfo $inpf | grep Pages | awk '{print $2}' 2>&1");


            $files_pc[$doc_key] = $doc_pages;
            $files_bookmarks[$doc_key] = [
                "file_name" => explode(".", $fn_names[$num])[0],
                "start_on_page" => $new_document_total_pages + 2,
                "bookmarks" => shell_exec("pdftk {$st_path}{$inpf} dump_data  | grep Bookmark 2>&1"),
                "offset" => $new_document_total_pages];


            $tofcont[] = [
                "file_name" => explode(".", $fn_names[$num])[0],
                "start_on_page" => $new_document_total_pages + 2,
            ];
            $new_document_total_pages += $doc_pages;

            if ($filenameFooter) {
                $tl2 = new \App\Http\Controllers\ToolController2();
                $tl2->getMpdf($st_path . "/" . $inpf);
                $mpdf = $tl2->mpdfGet();
                //pd
                $page_counter = 1;
                foreach (range(1, $tl2->pagecount) as $page_num) {
                    $page_counter++;
                    $new_page_num = sprintf('%03d', $page_counter);

                    $data = array(
                        "only_on_page" => false,
                        "header_type" => "hf-pages-arabic",
                        "start_from_page" => 1,
                        "color" => "black",
                        "location" => "fleft",
                        "font" => "timesnewroman2",
                        "font_size" => 12
                    );
                    $text = $fn_names[$num];

                    $template_data = $mpdf->ImportPage((int)$page_num, null, null, 0, 0, "/CropBox", false);

                    $size = [$template_data['tpl_box']['w'], $template_data['tpl_box']['h']];

                    $orientation = "P";
                    if ($size[0] > $size[1]) {
                        $orientation = "P";
                        if ($template_data['rotated']) {
                            $orientation = "L";
                        }
                    }

                    $mpdf->addPage($orientation, "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", $size, false);
                    $mpdf->UseTemplate($template_data['tplId'], 0, 0);

                    $mpdf->htext($data['color'], $data['location'], $text, $angle = 0, $data['font'], $data['font_size'], 1);

//					$data = array(
//						"only_on_page"=>false,
//						"header_type"=>"hf-pages-arabic",
//						"start_from_page"=>1,
//						"color"=>"black",
//						"location"=>"fright",
//						"font"=>"timesnewroman2",
//						"font_size"=>14
//					);
//					$text = $new_page_num;
//					$mpdf->htext($data['color'], $data['location'], $text, $angle=0, $data['font'], $data['font_size'], 1);
                }
                $mpdf->Output($st_path . "/" . $inpf, 'F');
            }

            if ($ranges[$num] == 'all') {
                $document_ranges[$doc_key] = range(1, $doc_pages);
            } else {
                $pages_list = array();
                $rr = explode(",", $ranges[$num]);

                foreach ($rr as $r) {
                    $rr2 = explode("-", $r);
                    if (count($rr2) > 1) {
                        if ((int)$rr2[0] and (int)$rr2[1] and $rr2[0] < $rr2[1]) {
                            $tmp_range = range($rr2[0], $rr2[1]);
                            $pages_list = array_merge($pages_list, $tmp_range);
                        } else {

                        }
                    } else {
                        $pages_list[] = (int)$r;
                    }
                }
                foreach ($pages_list as $k => $pl) {
                    if ($pl > $doc_pages) {
                        unset($pages_list[$k]);
                    }
                }
                $document_ranges[$doc_key] = $pages_list;
            }
            //TODO
            if (is_array($result) and array_search('Command Line Error: Incorrect password', $result) !== false) {
                exec("cd " . $this->storagePath . "uploads/merge-pdf/" . $request->input('UUID') . "; pdftk tmp/" . $request->input('UUID') . "-" . $num . ".pdf input_pw " . $request->input('pdf_password') . " output tmp/" . $request->input('UUID') . "-" . $num . "_decrypted.pdf", $result);
                $command_files[] = " $doc_key=tmp/" . $request->input('UUID') . "-" . $num . "_decrypted.pdf";
            } else {
                $command_files[] = " $doc_key=tmp/" . $request->input('UUID') . "-" . $num . ".pdf";
            }
            //$doc_key++;
        }


        $document_ranges = array_filter($document_ranges);
        foreach ($document_ranges as $k => $dr) {

            $document_ranges[$k] = array_filter($dr);
        }


        $firstInputCoverTitle = $request->post("firstInputCoverTitle");

        $formFields = $request->post("formFields");

        $new_bookmarks = "";
        $remove_bookmarks = "";
        $update_bookmarks = false;
        $bookmarkds_path = $this->storagePath . "uploads/merge-pdf/" . $request->input('UUID') . "/bookmarks";
        $document_ranges_command = "";
        if ($document_ranges) {
            foreach ($document_ranges as $fn => $dr) {
                foreach ($dr as $r) {
                    $document_ranges_command .= " {$fn}{$r} ";
                }
                if ($blankPageIfOdd && count($dr) % 2 != 0) {
                    $document_ranges_command .= " Z1 ";
                }

            }
        }


        $toc_file = false;
        if ($tableOfContents && $tableOfContents != '0') {
            $toc_file = $this->storagePath . "uploads/merge-pdf/" . $request->input('UUID') . "/toc.pdf";
            $toc = new TableOfContWrapper();
            $toc->baseInit($toc->getEmptyFile());

            if ($firstInputCoverTitle) {
                //array_splice($command_files, 1, 0, array($toc_file));
                $document_ranges_command = array_filter(explode(" ", $document_ranges_command));

                array_splice($document_ranges_command, 1, 0, array("Y1"));
                $document_ranges_command = " " . implode(" ", $document_ranges_command) . " ";
                unset($tofcont[0]);
            } else {
                $document_ranges_command = " Y1 " . $document_ranges_command;
                //array_unshift($command_files, $toc_file);
            }

            $toc->tableOfContents($tofcont);
            $toc->saveFile($toc_file);
//	    	echo "<pre>";
//	    	var_dump($tofcont);
//	    	exit();
        }


        if ($outline) {
            switch ($outline) {
                default:
                case 'keepall':
                    $new_bookmarks = array();
                    $update_bookmarks = true;
                    $rlevel = "/^BookmarkLevel: (\d+)$/m";
                    $rpage = "/^BookmarkPageNumber: (\d+)$/m";
                    $itt = 0;
                    foreach ($files_bookmarks as $file_num => $fb) {
                        $new_bookmarks[$file_num] = $files_bookmarks[$file_num]['bookmarks'];
                    }
                    $new_bookmarks = implode("\r\n", $new_bookmarks);
                    file_put_contents($bookmarkds_path, $new_bookmarks);

                    break;
                case 'discardall':
//        			foreach($files_pc as $fn=>$fpc){
//        				$remove_bookmarks .= " {$fn}1-end ";
//        			}
                    break;
                case 'each_doc':

                    $update_bookmarks = true;
                    $new_bookmarks = "";
                    foreach ($tofcont as $t) {
                        if ($tableOfContents) {
                            $page = $t['start_on_page'];
                        } else {
                            $page = $t['start_on_page'] - 1;
                        }
                        $new_bookmarks .= "BookmarkBegin\r\nBookmarkTitle: {$t['file_name']}\r\nBookmarkLevel: 1\r\nBookmarkPageNumber: $page\r\n";
                    }
                    file_put_contents($bookmarkds_path, $new_bookmarks);
                    break;
                case 'keepall2':
                    $new_bookmarks = array();
                    $update_bookmarks = true;
                    $rlevel = "/^BookmarkLevel: (\d+)$/m";
                    $rpage = "/^BookmarkPageNumber: (\d+)$/m";
                    $itt = 0;
                    if ($firstInputCoverTitle) {
                        unset($files_bookmarks['A']);
                    }

                    foreach ($files_bookmarks as $file_num => $fb) {
                        if ($tableOfContents) {
                            $page = $fb['start_on_page'];
                        } else {
                            $page = $fb['start_on_page'] - 1;
                        }

                        $new_level = "BookmarkBegin\r\nBookmarkTitle: {$fb['file_name']}\r\nBookmarkLevel: 1\r\nBookmarkPageNumber: $page\r\n";
                        $new_bookmarks[$file_num] = preg_replace_callback("/^BookmarkLevel: (\d+)$/m", function ($matches) {
                            return "BookmarkLevel: " . ($matches[1] + 1);
                        }, $files_bookmarks[$file_num]['bookmarks']);

                        $start_on = $fb['start_on_page'];
                        $new_bookmarks[$file_num] = preg_replace_callback("/^BookmarkPageNumber: (\d+)$/m", function ($matches) use ($start_on, $tableOfContents) {
                            if ($tableOfContents) {
                                $page = $start_on - 1;
                            } else {
                                $page = $start_on - 2;
                            }
                            return "BookmarkPageNumber: " . ($matches[1] + $page);
                        }, $new_bookmarks[$file_num]);


                        $new_bookmarks[$file_num] = $new_level . "" . $new_bookmarks[$file_num];
                    }
                    $new_bookmarks = implode("\r\n", $new_bookmarks);

                    file_put_contents($bookmarkds_path, $new_bookmarks);
                    break;
            }
        }


        $blank_file = public_path() . "/blank.pdf";
        if ($toc_file) {
            $command .= " " . implode(" ", $command_files) . " Y=$toc_file Z=$blank_file cat $document_ranges_command output " . $tmp_name;
        } else {
            $command .= " " . implode(" ", $command_files) . " Z=$blank_file cat $document_ranges_command output " . $tmp_name;
        }


        exec("cd " . $this->storagePath . "uploads/merge-pdf/" . $request->input('UUID') . ";" . $command . " 2>&1", $x);

        $outpath = $this->storagePath . "uploads/merge-pdf/" . $request->input('UUID') . "/$tmp_name";
        if ($normalizePageSizes) {
            shell_exec("pdftocairo -pdf $outpath {$outpath}_cleaned");
            rename("{$outpath}_cleaned", "$outpath");

            $fc = file_get_contents($outpath);
            $pattern = "/\d \d obj\n<< \/Type \/Page([\w\W]*?)(\/MediaBox[ \s]?+\[[ \s]?+(\d+\.?\d*)[ \s]?+(\d+\.?\d*)[ \s]?+(\d+\.?\d*)[ \s]?+(\d+\.?\d*)[ \s]?+\])([\w\W]*?)endobj/m";
            preg_match_all($pattern, $fc, $matches);

            if ($num == 1) {

            }
            if ($matches) {
                if (!$first_page_size) {
                    $first_page_size = $matches[2][0];
                }

                foreach ($matches[0] as $pn => $m) {
                    $page_num = $pn + 1;
                    $new_m = str_replace($matches[2][$pn],
                        $first_page_size, $m);
                    $fc = str_replace($m, $new_m, $fc);
                }
                $content = $fc;

                file_put_contents("$outpath", $content);
            }
        }
        if ($update_bookmarks) {
            $shell_remove_bookmarks = "pdftk A=$outpath cat A1-end output {$outpath}_bookmarked";
            shell_exec($shell_remove_bookmarks);
            rename("{$outpath}_bookmarked", "{$outpath}");


            $shell_bookmarks = "pdftk $outpath update_info $bookmarkds_path output {$outpath}_bookmarked";
            shell_exec($shell_bookmarks);
            rename("{$outpath}_bookmarked", "{$outpath}");
            if (is_file($bookmarkds_path)) {
                unlink($bookmarkds_path);
            }
        }

        if ($formFields) {
            switch ($formFields) {
                default:
                case 'discard':

                    $fdf = "pdftk $outpath generate_fdf output $outpath.fdf";
                    shell_exec($fdf);
                    $fdf = file_get_contents("$outpath.fdf");
                    $fdf = preg_replace("/\/V \((.+?)\)\n/", "/V ()\n", $fdf);

                    file_put_contents("$outpath.fdf", $fdf);
                    $sh = "pdftk $outpath fill_form $outpath.fdf output $outpath.filled";
                    shell_exec($sh);
                    rename("$outpath.filled", "$outpath");
                    if (is_file("$outpath.fdf")) {
                        unlink("$outpath.fdf");
                    }

                    $flatten_shell = "pdftk $outpath output {$outpath}_flatten flatten 2>&1";
                    $x = shell_exec($flatten_shell);
                    rename("{$outpath}_flatten", $outpath);
//
                    break;
                case 'flatten':
                    $flatten_shell = "pdftk $outpath output {$outpath}_flatten flatten 2>&1";
                    $x = shell_exec($flatten_shell);
                    rename("{$outpath}_flatten", $outpath);
                    break;

                case 'merge':

                    break;
            }
        }

        $file = '/storage/uploads/merge-pdf/' . $request->input('UUID') . '/' . $tmp_name;
        $filename = $request->input('basename') . '_merged.pdf';

        return response()->json([
            'file' => $file,
            'filename' => $filename,
            'status' => 'success'
        ]);
    }

    public function grayscale_pdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required',
            'numPages' => 'required',
            'basename' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);
        }

        if (true) {// && isset($_COOKIE['test1'])){
            $tmp_name = uniqid() . '.pdf';
            $server_ip = "http://18.191.39.200";
            $file = '/storage/uploads/grayscale-pdf/' . $request->input('UUID') . '/' . $request->input('UUID') . ".pdf";
            $file_dest = '/storage/uploads/grayscale-pdf/' . $request->input('UUID') . '/' . $request->input('UUID') . "_grayscale.pdf";

            $original_file = "https://deftpdf.com/" . $file;


            $pwd_line = '';
            if (!is_null($request->input('pdf_password'))) {
                $pwd_line = ' -sPDFPassword=' . $request->input('pdf_password');
            }


            $data = $_POST;
            $data['file'] = $original_file;
            $data['pwd_line'] = $pwd_line;
            $data['operation'] = 'grayscale';
            //$resp = ($this->httpPost($server_ip, $data));
            $resp = json_decode($this->httpPost($server_ip, $data), 1);


            if (!$resp) {
                return response()->json(["status" => "error", "message" => "We could not process your request..."]);
            }


            $public_path = base_path("public");

            $temp_file_content = file_get_contents($resp['file']);

            file_put_contents($public_path . $file_dest, $temp_file_content);


            $rr = $this->httpPost($server_ip, ["operation" => "delete_old", "delete_file" => $resp['original_file'], "delete_dir" => $resp['dest_dir']]);


            $filename = $request->input('basename') . '.pdf';
            return response()->json([
                'file' => $file_dest,
                'filename' => $filename,
                'status' => 'success'
            ]);

        }


        $pwd_line = '';
        if (!is_null($request->input('pdf_password'))) {
            $pwd_line = ' -sPDFPassword=' . $request->input('pdf_password');
        }

        $tmp_name = uniqid() . '.pdf';
        exec("cd " . $this->storagePath . "uploads/grayscale-pdf/" . $request->input('UUID') . "; gs \ -sOutputFile=" . $tmp_name . " -sDEVICE=pdfwrite" . $pwd_line . " -sColorConversionStrategy=Gray -dProcessColorModel=/DeviceGray -dPDFUseOldCMS=false -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH " . $request->input('UUID') . ".pdf");
        //exec("cd " . $this->storagePath . "uploads/grayscale-pdf/" . $request->input('UUID') . "; gs -dNOPAUSE -dBATCH -q -sOutputFile=" . $tmp_name . ".ps -sDEVICE=psmono " . $request->input('UUID') . ".pdf; ps2pdf " . $tmp_name . ".ps". " ".$tmp_name);


        $file = '/storage/uploads/grayscale-pdf/' . $request->input('UUID') . '/' . $tmp_name;
        $filename = $request->input('basename') . '.pdf';

        return response()->json([
            'file' => $file,
            'filename' => $filename,
            'status' => 'success'
        ]);
    }

    public function delete_pages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required',
            'page' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

		$files = Storage::files("uploads/delete-pages/" . $request->input('UUID') . "/tmp");
//		dump($files);

		$keys = range("A", "Z");
		$command_files = [];

        $pwd_line = '';
        if (!is_null($request->input('pdf_password')) && $request->input('pdf_password') != 'false') {
            $pwd_line = ' input_pw ' . $request->input('pdf_password');
        }

        $tmp_name = $request->input('basename') . '.pdf';

        $command = 'pdftk "' . $request->input('UUID') . '.pdf"' . $pwd_line . ' cat ';

        $command2 = '';
		$command2_arr = [];

        if (!is_null($request->input('pattern'))) {
            if (!preg_match('#^[0-9\-\,]+$#', $request->input('pattern')))
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pattern error.'
                ]);

            $pages = [];

            for ($i = 1; $i <= intval($request->input('numPages')); $i++)
                $pages[] = $i;

            $patterns = explode(',', $request->input('pattern'));

            foreach ($patterns as $key => $val)
                $patterns[$key] = trim($val);

            foreach ($patterns as $key => $val) {
                $patterns[$key] = explode('-', $val);
            }

            foreach ($patterns as $pattern) {
                if (count($pattern) == 1) {
                    if (array_search(intval($pattern[0]), $pages) !== false)
                        unset($pages[array_search(intval($pattern[0]), $pages)]);
                } else {
                    if (empty($pattern[1])) {
                        $num = intval($request->input('numPages'));
                        for ($i = intval($pattern[0]) + 1; $i <= $num; $i++) {
                            if (array_search($i, $pages) !== false)
                                unset($pages[array_search($i, $pages)]);
                        }
                    } else {
                        for ($i = intval($pattern[0]); $i <= intval($pattern[1]); $i++) {
                            if (array_search($i, $pages) !== false)
                                unset($pages[array_search($i, $pages)]);
                        }
                    }
                }
            }

            foreach ($pages as $page) {
                if (is_null($page))
                    continue;

                $command .= $page . ' ';
            }
        } else {
        	if(is_array($request->input('page')[0])){
				foreach ($request->input('page') as $doc_key => $document) {
					$command2_temp_arr = [];
					foreach($document as $page){
						if (!is_null($page)){
							$command2_temp_arr[] = $page;
						}
					}
//					dump($command2_temp_arr);

					if($command2_temp_arr){
						$name = explode('/', $files[$doc_key]);
						$command2_arr[$keys[$doc_key]] = ['file' => end($name), 'pages' => $command2_temp_arr];
					}
				}

				if($command2_arr){
					$command2 = 'pdftk ';
					$command2_1 = '';
					$command2_2_arr = [];

					foreach($command2_arr as $cletter => $citem){
						$command2_1 .= $cletter . '="' . $citem['file'] . '" ';
						foreach($citem['pages'] as $p){
							$command2_2_arr[] = $cletter.$p;
						}
					}

					$command2_2 = implode(' ', $command2_2_arr);
				}

				$command2 .= $command2_1 . 'cat ' . $command2_2 . ' output "../' . $tmp_name . '"';
//				dd($command2);
//				dd($command2_arr);
			}else{
				foreach ($request->input('page') as $page) {
					if (is_null($page))
						continue;

					$command .= $page . ' ';
				}
			}
        }

        $command .= 'output "' . $tmp_name . '"';
//		if(isset($_COOKIE['dbg_delete'])){
//			$command = "cd " . $this->storagePath . "uploads/delete-pages/" . $request->input('UUID') . "; " . $command." 2>&1";
//
//			var_dump($request->input('pdf_password'));
//			exit();
//			$x = shell_exec("cd " . $this->storagePath . "uploads/delete-pages/" . $request->input('UUID') . "; " . $command." 2>&1");
//			//input_pw
//			exit($command);
//			var_dump($x);
//			exit();
//		}

		if($command2){
//			dd("cd " . $this->storagePath . "uploads/delete-pages/" . $request->input('UUID') . "/tmp; " . $command2);
			exec("cd " . $this->storagePath . "uploads/delete-pages/" . $request->input('UUID') . "/tmp; " . $command2);
//			$asd = shell_exec("cd " . $this->storagePath . "uploads/delete-pages/" . $request->input('UUID') . "/tmp; " . $command2 . ' 2>&1');
//			print_r($asd);
//			exit;
		}else{
			exec("cd " . $this->storagePath . "uploads/delete-pages/" . $request->input('UUID') . "; " . $command);
		}

		$file = '/storage/uploads/delete-pages/' . $request->input('UUID') . '/' . $tmp_name;

		$file = str_replace(base_path("public"), '', \App\Custom\PDFHelpers::replacePages(base_path("public").$file));

        return response()->json([
            'file' => $file,
            'filename' => $tmp_name,
            'status' => 'success'
        ]);
    }

    public function pdf_to_jpg(Request $request)
    {
        $validation = [
            'UUID' => 'required'
        ];

        //shell

        if (is_null($request->input('numPages'))) {
            $validation['page'] = 'required';
        }

        $validator = Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);
        }

        $pwd_line = '';
        if (!is_null($request->input('pdf_password'))) {
            $pwd_line = ' -sPDFPassword=' . $request->input('pdf_password');
        }

        if (false) {

            $num_files = $request->input('numPages');

            $server_ip = "http://18.191.39.200";
            $pf = "/storage/uploads/pdf-to-jpg/" . $request->input('UUID') . "/";
            $file = $pf . $request->input('UUID') . ".pdf";

            $original_file = "https://deftpdf.com/" . $file;


            $data = $_POST;
            $data['operation'] = "pdf_to_jpg";

            $data['file'] = $original_file;
            $data['pwd_line'] = $pwd_line;

            $resp = json_decode($this->httpPost($server_ip, $data), 1);
            //$resp = ($this->httpPost($server_ip, $data));


            if (!$resp) {
                return response()->json(["status" => "error", "message" => "We could not process your request..."]);
            }


            if ($num_files == 1) {
                $file = '/storage/uploads/pdf-to-jpg/' . $request->input('UUID') . '/' . $resp['filename'];
            } else {
                $file = '/storage/uploads/pdf-to-jpg/' . $request->input('UUID') . '/' . $resp['filename'];
            }

            $public_path = base_path("public");

            $temp_file_content = file_get_contents($resp['file']);
            file_put_contents($public_path . $file, $temp_file_content);


            $rr = $this->httpPost($server_ip, ["operation" => "delete_old", "delete_file" => $resp['original_file'], "delete_dir" => $resp['dest_dir']]);


            return response()->json([
                'file' => $file,
                'filename' => $resp['filename'],
                'status' => 'success'
            ]);


        }


        switch ($request->input('format')) {
            case 'jpeg':
                $format = 'jpeg';
                $expansion = 'jpg';
                break;
            case 'png16m':
                $format = 'png16m';
                $expansion = 'png';
                break;
            case 'tiff':
                $format = 'tiff48nc';
                $expansion = 'tiff';
                break;
            default:
                $format = 'jpeg';
                $expansion = 'jpg';
        }

        if (!is_array($request->input('page')) and !is_null($request->input('page'))) {
        	if($request->has('document')){
				exec("cd " . $this->storagePath . "uploads/pdf-to-jpg/" . $request->input('UUID') . "; gs -dNOPAUSE -dBATCH -sDEVICE=" . $format . $pwd_line . " -dFirstPage=" . $request->input('page') . " -dLastPage=" . $request->input('page') . " -r220 -sOutputFile='" . $request->input('basename') . "-" . $request->input('page') . "." . $expansion . "' tmp/" . $request->input('UUID') . ($request->has('document') ? '-' . $request->input('document') : '') . ".pdf");
				$file = '/storage/uploads/pdf-to-jpg/' . $request->input('UUID') . '/' . $request->input('basename') . '-' . $request->input('page') . '.' . $expansion;
				$filename = $request->input('basename') . '-' . $request->input('page') . '.' . $expansion;
			}else{
				exec("cd " . $this->storagePath . "uploads/pdf-to-jpg/" . $request->input('UUID') . "; gs -dNOPAUSE -dBATCH -sDEVICE=" . $format . $pwd_line . " -dFirstPage=" . $request->input('page') . " -dLastPage=" . $request->input('page') . " -r220 -sOutputFile='" . $request->input('basename') . "-" . $request->input('page') . "." . $expansion . "' " . $request->input('UUID') . ".pdf");
				$file = '/storage/uploads/pdf-to-jpg/' . $request->input('UUID') . '/' . $request->input('basename') . '-' . $request->input('page') . '.' . $expansion;
				$filename = $request->input('basename') . '-' . $request->input('page') . '.' . $expansion;
			}
        } else {
            $dpi = [72, 150, 220];

            if (in_array(intval($request->input('dpi')), $dpi))
                $dpi = intval($request->input('dpi'));
            else
                $dpi = 150;

            $pattern = $request->input('outputFilenamePattern');
            $num_files = 0;

            if (!is_null($request->input('page'))) {
				if(is_array($request->input('page')[0])){
					foreach ($request->input('page') as $doc_key => $document) {
						foreach($document as $page){
							if (!is_null($page)){
								$tmp_name = self::replacePattern($page, $pattern, $request->input('basename'), $request->input('UUID'));

								if (!$tmp_name)
									continue;

//								dd("cd " . $this->storagePath . "uploads/pdf-to-jpg/" . $request->input('UUID') . "; gs -dNOPAUSE -dBATCH -dDOINTERPOLATE -sDEVICE=" . $format . $pwd_line . " -dFirstPage=" . $page . " -dLastPage=" . $page . " -r" . $dpi . " -sOutputFile='tmp/" . $tmp_name . "." . $expansion . "' " . 'tmp/' . $request->input('UUID') . '-' . $doc_key . ".pdf");
								exec("cd " . $this->storagePath . "uploads/pdf-to-jpg/" . $request->input('UUID') . "; gs -dNOPAUSE -dBATCH -dDOINTERPOLATE -sDEVICE=" . $format . $pwd_line . " -dFirstPage=" . $page . " -dLastPage=" . $page . " -r" . $dpi . " -sOutputFile='tmp/" . $doc_key . '-' .$tmp_name . "." . $expansion . "' " . 'tmp/' . $request->input('UUID') . '-' . $doc_key . ".pdf");
								$num_files++;
							}
						}
					}
				}else{
					foreach ($request->input('page') as $page) {
						$tmp_name = self::replacePattern($page, $pattern, $request->input('basename'), $request->input('UUID'));

						if (!$tmp_name)
							continue;

						exec("cd " . $this->storagePath . "uploads/pdf-to-jpg/" . $request->input('UUID') . "; gs -dNOPAUSE -dBATCH -dDOINTERPOLATE -sDEVICE=" . $format . $pwd_line . " -dFirstPage=" . $page . " -dLastPage=" . $page . " -r" . $dpi . " -sOutputFile='tmp/" . $tmp_name . "." . $expansion . "' " . $request->input('UUID') . ".pdf");
						$num_files++;
					}
				}
            } else {


                $tmp_name = self::replacePattern("%0d", $pattern, $request->input('basename'), $request->input('UUID'));


                $nsh = ("cd " . $this->storagePath . "uploads/pdf-to-jpg/" . $request->input('UUID') . "; gs -dNOPAUSE -dBATCH -dDOINTERPOLATE -sDEVICE=" . $format . $pwd_line . " -r" . $dpi . " -sOutputFile='tmp/" . $tmp_name . "." . $expansion . "' " . $request->input('UUID') . ".pdf");

                shell_exec($nsh);

                $num_files = $request->input('numPages');

//                for ($page = 1; $page <= $request->input('numPages'); $page++) {
//
//              		$tmp_name = self::replacePattern($page, $pattern, $request->input('basename'), $request->input('UUID'));

//

//					exit($tmp_name);


//                    if (!$tmp_name){
//                        continue;
//                    }

//                    exec("cd " . $this->storagePath . "uploads/pdf-to-jpg/" . $request->input('UUID') . "; gs -dNOPAUSE -dBATCH -dDOINTERPOLATE -sDEVICE=" . $format . $pwd_line . " -dFirstPage=" . $page . " -dLastPage=" . $page . " -r" . $dpi . " -sOutputFile='tmp/" . $tmp_name . "." . $expansion . "' " . $request->input('UUID') . ".pdf");
//                    $num_files++;
//                }
            }

            if (false && $num_files == 1) {
                $file = '/storage/uploads/pdf-to-jpg/' . $request->input('UUID') . '/tmp/' . $request->input('basename') . '-1.' . $expansion;
                $filename = $request->input('basename') . '-1.' . $expansion;
            } else {
                $filename = uniqid('freeconvert_') . '.zip';
                exec("cd " . $this->storagePath . "uploads/pdf-to-jpg/" . $request->input('UUID') . "/tmp; zip ../" . $filename . " *;");
                exec("rm " . $this->storagePath . "uploads/pdf-to-jpg/" . $request->input('UUID') . "/tmp/*;");

                $file = '/storage/uploads/pdf-to-jpg/' . $request->input('UUID') . '/' . $filename;
            }
        }

        return response()->json([
            'file' => $file,
            'filename' => $filename,
            'status' => 'success'
        ]);
    }

    private function replacePattern($page, $pattern, $basename, $UUID)
    {
        $patterns = [
            '[TIMESTAMP]' => date('Ymd_His'),
            '[BASENAME]' => $basename,
        ];

        foreach ($patterns as $p => $v)
            $pattern = str_replace($p, $v, $pattern);

        if (strpos($pattern, '[CURRENTPAGE]') !== false or strpos($pattern, '[CURRENTPAGE##]') !== false or strpos($pattern, '[CURRENTPAGE###]') !== false) {
            $tmp_name = str_replace('[CURRENTPAGE]', $page, $pattern);

            $page_tmp = ($page < 10) ? '0' . $page : $page;
            $tmp_name = str_replace('[CURRENTPAGE##]', $page_tmp, $tmp_name);

            $page_tmp = ($page < 10) ? '00' . $page :
                (($page < 100) ? '0' . $page : $page);

            $tmp_name = str_replace('[CURRENTPAGE###]', $page_tmp, $tmp_name);
        } elseif (strpos($pattern, '[FILENUMBER') !== false)
            $tmp_name = $pattern;
        else
            $tmp_name = $page . '_' . $pattern;

        if (strpos($pattern, '[FILENUMBER###]') !== false) {
            $page_tmp = ($page < 10) ? '00' . $page :
                (($page < 100) ? '0' . $page : $page);

            $tmp_name = str_replace('[FILENUMBER###]', $page_tmp, $tmp_name);
        }

        if (strpos($pattern, '[FILENUMBER') !== false) {
            $first = strpos($pattern, '[FILENUMBER') + 11;
            $last = strpos($pattern, ']', $first);
            $page_tmp = intval(substr($pattern, $first, $last - $first));

            if ($page_tmp > $page)
                return false;

            $tmp_name = str_replace('[FILENUMBER###]', $page_tmp, $tmp_name);
        }

        if (strpos($pattern, '[TEXT]') !== false) {
            $txt_name = uniqid();
            exec("cd " . $this->storagePath . "uploads/pdf-to-jpg/" . $UUID . "; gs -dNOPAUSE -dBATCH -sDEVICE=txtwrite -dFirstPage=" . $page . " -dLastPage=" . $page . " -sOutputFile='tmp/" . $txt_name . ".txt' " . $UUID . ".pdf");
            $txt = File::get($this->storagePath . "uploads/pdf-to-jpg/" . $UUID . "/tmp/" . $txt_name . ".txt");
            Storage::delete("uploads/pdf-to-jpg/" . $UUID . "/tmp/" . $txt_name . ".txt");
            $txt = trim(preg_replace('/[^\S\r\n]+/', ' ', $txt));

            $txt = trim(substr($txt, 0, strrpos(substr($txt, 0, 25), ' ')));

            $tmp_name = str_replace('[TEXT]', $txt, $tmp_name);
        }


        return $tmp_name;
    }

    public function pdf_to_txt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required',
            'numPages' => 'required',
            'basename' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);
        }


        $pwd_line = '';
        if (!is_null($request->input('pdf_password'))) {
            //$pwd_line = ' -sPDFPassword=' . $request->input('pdf_password');
            $pwd_ling = "-opw " . escapeshellarg($request->input('pdf_password'));
        }

        $tmp_name = uniqid() . '.txt';
        //exec("cd " . $this->storagePath . "uploads/pdf-to-txt/" . $request->input('UUID') . "; gs -dNOPAUSE -dBATCH -sDEVICE=txtwrite" . $pwd_line . " -dFirstPage=1 -dLastPage=" . $request->input('numPages') . " -sOutputFile='tmp/" . $tmp_name . "' " . $request->input('UUID') . ".pdf");

        $out = "tmp/$tmp_name";
        $input = $request->input('UUID') . ".pdf";

        $shell = ("cd " . $this->storagePath . "uploads/pdf-to-txt/" . $request->input('UUID') . "; pdftotext " . $pwd_line . " -layout $input $out");
        shell_exec($shell);


        $txt = File::get($this->storagePath . "uploads/pdf-to-txt/" . $request->input('UUID') . "/tmp/" . $tmp_name . "");
        $txt = trim(preg_replace('/[^\S\r\n]+/', ' ', $txt));
        $txt = str_replace("\n ", "\n", $txt);

        Storage::put("uploads/pdf-to-txt/" . $request->input('UUID') . "/" . $tmp_name, $txt);

        $file = '/storage/uploads/pdf-to-txt/' . $request->input('UUID') . '/' . $tmp_name;
        $filename = $request->input('basename') . '.txt';

        return response()->json([
            'file' => $file,
            'filename' => $filename,
            'status' => 'success'
        ]);
    }

    public function jpg_to_pdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UUID' => 'required'
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $tmp_name = uniqid('freeconvert_') . '.pdf';
        $format_page = $request->input('pageFormat');
        $dpi = 300;

        if ($format_page == 'auto') {
            $command = 'convert ';
        } else {
            $command = 'gs -sDEVICE=pdfwrite ';
        }


        switch ($format_page) {
            case "a5":
                $format_size = [5.83 * $dpi, 8.27 * $dpi];
                break;
            case "a4":
                $format_size = [8.27 * $dpi, 11.69 * $dpi];
                break;
            case "a3":
                $format_size = [11.69 * $dpi, 16.54 * $dpi];
                break;
            case "a2":
                $format_size = [23.39 * $dpi, 33.11 * $dpi];
                break;
            case "a1":
                $format_size = [33.11 * $dpi, 46.81 * $dpi];
                break;
            case "letter":
                $format_size = [8.5 * $dpi, 11 * $dpi];
                break;
            case "legal":
                $format_size = [8.5 * $dpi, 14 * $dpi];
                break;
            case "legder":
                $format_size = [11 * $dpi, 17 * $dpi];
                break;
            case "11x17":
                $format_size = [17 * $dpi, 11 * $dpi];
                break;
            case "executive":
                $format_size = [7.25 * $dpi, 10.55 * $dpi];
                break;
        }

        if ($format_page != 'auto' and $format_page != 'executive')
            $command .= ' -sPAPERSIZE=' . $format_page;
        elseif ($format_page == 'executive') {
            $command .= ' -g' . $format_size[0] . 'x' . $format_size[1];
        }

        if ($format_page != 'auto')
            $command .= ' -dPDFFitPage -r' . $dpi . ' -o ' . $tmp_name . ' viewjpeg.ps -c "';


        foreach ($request->input('files') as $num => $file) {
            $file = "uploads/jpg-to-pdf/" . $request->input('UUID') . "/tmp/" . $file;
            $fp = $this->storagePath . $file;
            $fs = getimagesize($fp);
            //pageMargin

            list($w, $h) = array($fs[0], $fs[1]);
            $new_image = false;
            $flag = false;
            if ($h > 3507) {
                $perc = (3507 * 100 / $h / 100);
                $img = Image::make($fp);
                $w = $w * $perc;
                $h = $h * $perc;
                $img->resize($w, $h);
                $img->save($fp);
                $flag = true;
            }

            if ($w > 2481) {
                $perc = (2481 * 100 / $w / 100);
                $img = Image::make($fp);
                $img->resize($w * $perc, $h * $perc);
                $img->save($fp);
                $flag = true;
            }


            $image = new \IMagick($fp);
            $flattened = new \IMagick();

            if ($format_page == 'auto') {
                $img_width = $image->getImageWidth();
                $img_height = $image->getImageHeight();

                if (floatval($request->input('pageMargin')) != "0") {
                    if ($img_width > 96) {
                        $img_width_new = $img_width - floatval($request->input('pageMargin')) * 96;
                    } else {
                        $img_width_new = $img_width / 3;
                    }
                    if ($img_height > 96) {
                        $img_height_new = $img_height - floatval($request->input('pageMargin')) * 96;
                    } else {
                        $img_height_new = $img_height / 3;
                    }
                } else {
                    $img_width_new = $img_width;
                    $img_height_new = $img_height;
                }

                $image->resizeImage($img_width_new, $img_height_new, \Imagick::FILTER_LANCZOS, 1);

                if ($request->input('pageOrientation') == 'landscape')
                    $image->rotateimage(new \ImagickPixel(), 270);

                $flattened->newImage($img_width, $img_height, new \ImagickPixel("white"));

                $x = round(($img_width - $img_width_new) / 2);
                $y = round(($img_height - $img_height_new) / 2);
            } else {
                $img_width = $image->getImageWidth();
                $img_height = $image->getImageHeight();

                $coefficient = ($request->input('pageOrientation') == 'landscape') ? $img_width / $format_size[1] : $img_width / $format_size[0];

//              var_dump($img_width);
//              var_dump($img_height);
//              var_dump($coefficient);
//              exit();


                $img_width = $img_width / $coefficient;
                $img_height = $img_height / $coefficient;


                if (floatval($request->input('pageMargin')) != "0") {
                    $page_margin = $request->input('pageMargin');
                    if ($page_margin == 1) {
                        $page_margin = 6.3;
                    } else if ($page_margin == 0.5) {
                        $page_margin = 3.15;
                    }

                    if ($img_width > 96) {
                        $img_width = $img_width - floatval($page_margin) * 96;
                    } else {
                        $img_width = $img_width / 3;
                    }
                    if ($img_height > 96) {
                        $img_height -= $img_height / 100 * (floatval($request->input('pageMargin')) * 15);
                    } else {
                        $img_height = $img_height / 3;
                    }
                }

                if (!$flag) {
                    $image->resizeImage($img_width, $img_height, \Imagick::FILTER_LANCZOS, 1);
                }

                if ($request->input('pageOrientation') == 'landscape') {
                    $image->rotateimage(new \ImagickPixel(), 270);
                }

                $flattened->newImage($format_size[0], ($format_size[1]), new \ImagickPixel("white"));

                $x = round(($format_size[0] - $image->getImageWidth()) / 2);
                $y = round(($format_size[1] - $image->getImageHeight()) / 2);

            }

            $flattened->compositeImage($image, imagick::COMPOSITE_OVER, $x, $y);

            $flattened->setImageFormat("jpg");
            $flattened->writeImage($this->storagePath . 'uploads/jpg-to-pdf/' . $request->input('UUID') . '/tmp_resize/' . $num . '.jpg');

            //exit($this->storagePath . 'uploads/jpg-to-pdf/' . $request->input('UUID') . '/tmp_resize/' . $num . '.jpg');

            $image->clear();
            $image->destroy();
            $flattened->clear();
            $flattened->destroy();

            $file = $this->storagePath . 'uploads/jpg-to-pdf/' . $request->input('UUID') . '/tmp_resize/' . $num . '.jpg';

            if ($format_page == 'auto')
                $command .= $file . " ";
            else
                $command .= "(" . $file . ") viewJPEG showpage ";
        }

        if ($format_page == 'auto')
            $command .= $tmp_name . ';';
        else
            $command .= '";';

        if ($request->input('pageOrientation') == 'landscape') {
            $command .= "pdftk " . $tmp_name . " cat 1-endeast output rotate_" . $tmp_name;
            $file = '/storage/uploads/jpg-to-pdf/' . $request->input('UUID') . '/rotate_' . $tmp_name;
        } else {
            $file = '/storage/uploads/jpg-to-pdf/' . $request->input('UUID') . '/' . $tmp_name;
        }

        exec("cd " . $this->storagePath . "uploads/jpg-to-pdf/" . $request->input('UUID') . "; " . $command);
        exec("rm " . $this->storagePath . "uploads/jpg-to-pdf/" . $request->input('UUID') . "/tmp_resize/*;");

//		$file = str_replace(base_path("/public/"), '', \App\Custom\PDFHelpers::replacePages(base_path("/public/").$file));
//		$file = str_replace(base_path("public"), '', \App\Custom\PDFHelpers::replacePages(base_path("/public/").$file));
		$file = str_replace(base_path("public"), '', \App\Custom\PDFHelpers::replacePages(base_path("public").$file));

		$doc = Document::where(['UUID' => $request->input('UUID'), 'operation_type' => 'jpgtopdf'])->orderBy('ID', 'desc')->first()->toArray();
		Document::where('id', $doc['id'])->update(['edited_document' => $file,
												   "download_name" => $tmp_name,
												   "delete_after" => (time() + 18000)]);

        return response()->json([
          	'file' => $file,
            'filename' => $tmp_name,
            'status' => 'success'
        ]);
    }
}


class TableOfContWrapper
{
    public function baseInit($file = false)
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
                    "R" => "Helvetica.ttf",
                    "B" => "Helvetica.ttf",
                ),
                "timesnewroman2" => array(
                    "R" => "times-new-roman.ttf",
                    "B" => "times-new-roman.ttf",
                ),


            ))
        );

        $params = ["margin_left" => 0, "margin_top" => 0, "margin_right" => 0, "margin_header" => 0, "default_font_size" => 10, "open_layer_pane" => false, "format" => "", "useActiveForms" => true,];
        $this->mpdf = new MpdfTableOfCont(array_merge($params, $fonts));
        $this->mpdf->useSubstitutions = true;
        $this->mpdf->text_input_as_HTML = true;
        $this->mpdf->SetImportUse();
        $this->pagecount = $this->mpdf->SetSourceFile($file);
    }

    public function getEmptyFile()
    {
        $blank_file = public_path() . "/blank.pdf";
        return $blank_file;
    }

    public function tableOfContents($contents = false)
    {

        $template_data = $this->mpdf->ImportPage(1, null, null, 0, 0, "/CropBox", false);

        $size = [$template_data['tpl_box']['w'], $template_data['tpl_box']['h']];

        $orientation = "P";
        if ($size[0] > $size[1]) {
            $orientation = "P";
            if ($template_data['rotated']) {
                $orientation = "L";
            }
        }
        $this->mpdf->addPage($orientation, "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", $size, false);
        $this->mpdf->UseTemplate($template_data['tplId'], 0, 0);


        $div = "<div style='position: absolute; top: 20mm; left: 15mm;'>";
        foreach ($contents as $cont) {
            $div .= "<div class='toc_item' style='width: 186mm;'>
						<table  style='width: 186mm; display: block;'>";
            $div .= "<tr>
				<td style='text-align: left; white-space: nowrap; font-size: 16px;'>
					<span style='color: black; text-decoration: none; ' href='#page={$cont['start_on_page']}'>{$cont['file_name']}</span></td>
				<td><span style='color: black; text-decoration: none; ' href='#page={$cont['start_on_page']}'>..........................................................................................................................................................................................................</a></td>
				<td style='text-align: right; font-size: 16px;'>
					<span style='color: black; text-decoration: none; ' href='#page={$cont['start_on_page']}'>{$cont['start_on_page']}</span></td>
			</tr>";
            $div .= "</table>
						</div>";
        }

        $div .= "</div>";

        $this->mpdf->WriteHTML($div, \Mpdf\HTMLParserMode::HTML_BODY);
    }


    public function saveFile($dest = false)
    {
        if (!$dest) {
            return false;
        }
        $this->mpdf->Output($dest, 'F');
    }


}

class MpdfTableOfCont extends \Mpdf\Mpdf
{
    private $mpdf = false;
    private $pagecount = 0;


}
