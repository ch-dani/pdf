<?php
/**
 * Created by PhpStorm.
 * User: westham
 * Date: 03.05.18
 * Time: 16:58
 */

namespace App\Traits;

trait FormatResponse
{
    protected function formatResponse($status, $message = null, $data = null){
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
    }
}