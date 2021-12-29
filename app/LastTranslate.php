<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LastTranslate extends Model{

    protected $table = 'last_translate';
    protected $primaryKey = 'id';

    protected $fillable = ['ip', 'trans_count', 'chars_count', 'ts'];


}
