<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Option extends Model {

	protected $table = 'options';
	protected $primaryKey = 'id';
    protected $fillable = ['value', 'name'];

    public static function option($name, $value = false, $url = false) {
        $Option = Option::where('name', $name)->first();

        if ($value !== false) {
            if (is_null($Option)) {
                $Option = new Option;
                $Option->name = $name;
            }

            $Option->value = $value;
            $Option->save();
        }

        if ($url) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $Option->value)) {
                $Option->value = "http://" . $Option->value;
            }
        }

        return (!is_null($Option)) ? $Option->value : '';
    }
}