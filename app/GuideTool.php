<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class GuideTool extends Model {

	protected $table = 'guides_tools';
	protected $primaryKey = 'id';

    protected $fillable = ['guide_id', 'tool'];

}