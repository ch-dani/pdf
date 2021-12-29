<?php

namespace App\Http\Controllers;

use Exception;
use Google_Client;
use Google_Service_Books;
use Google_Service_Drive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FileController extends Controller
{
    public function saveFromLink(Request $request)
    {
        if ($request->service == 'dropbox') {
            $url = str_replace('dl=0', 'dl=1', $request->link);
            $name = "dropbox-file".time().".pdf";
            $contents = file_get_contents($url);
            File::put(public_path('uploads/pdf')."/".$name, $contents);
        }

        if ($request->service == 'google-drive') {
            $url = $request->link;
            $name = "google-drive-file".time().".pdf";

            set_time_limit(0);
            //This is the file where we save the    information
            $fp = fopen (public_path('uploads/pdf')."/".$name, 'w+');
            //Here is the file we are downloading, replace spaces with %20
            $ch = curl_init(str_replace(" ","%20",$url));
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            // write curl response to file
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer '.$request->access_token,
            ));
            // get curl response
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        }

        $link = '/uploads/pdf/'.$name;

        $response = $this->formatResponse('success', null, [
            'link' => $link
        ]);

        return response($response, 200);
    }
}
