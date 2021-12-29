<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Language extends Model {

	protected $table = 'languages';
	protected $primaryKey = 'id';

    protected $fillable = ['name', 'status', 'flag'];

}
