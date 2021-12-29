<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Storage;

class Flag extends Model {

    public static function GetFlags()
    {
        $files = Storage::allFiles('flags');
        $flags = [];

        foreach ($files as $file) {
            $name = substr(explode('/', $file)[1], 0, -4);

            $flags[] = [
                'name' => $name,
                'flag' => asset('storage/'.$file)
            ];
        }

        return $flags;
    }

}