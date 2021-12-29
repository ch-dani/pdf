<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use GeoIP;

class UniqueVisitor extends Model
{

    protected $table = 'unique_visitors';
    protected $primaryKey = 'id';

    protected $fillable = ['ip', 'iso_code'];

    public static function setVisiting($ip)
    {
    	return false;
        if ($ip == '127.0.0.1')
            return false;

        if (!UniqueVisitor::where('ip', $ip)->where('created_at', '>=', date('Y-m-d').' 00:00:00')->count()) {
            $Geo = GeoIP::getLocation($ip);

            UniqueVisitor::create([
                'ip' => $ip,
                'iso_code' => isset($Geo->iso_code) ? $Geo->iso_code : NULL
            ]);
        }
    }

}
