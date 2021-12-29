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

class ToolControllerTest extends Controller
{
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
        $process = new Process("soffice --infilter='writer_pdf_import' --convert-to doc $dest_file_storage_path --outdir $outputFilePath");
        $process->run();

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
            'url' => str_replace('/var/www/freeconvert', '', str_replace('app/', '', $outputFilePath . $downloadFileName)),
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
                'edited_document' => str_replace('app/', '', str_replace('/var/www/freeconvert', '', $dest_file_storage_path)),
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
    }

    public function epub2pdf(Request $request)
    {
        $uuid = $request->UUID;
        $operation_type = "epubtopdf";

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
            $process = new Process("ebook-convert $original_file_storage_path $dest_file_storage_path --pdf-hyphenate");
            $process->run();

            $edited_documents_path[] = $dest_file_storage_path;

            Document::where(['id' => $singleDoc['id']])->update([
                'edited_document' => str_replace('app/', '', str_replace('/var/www/freeconvert', '', $dest_file_storage_path)),
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
    }

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
    }
}