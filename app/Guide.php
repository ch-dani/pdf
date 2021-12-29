<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Guide extends Model {

	protected $table = 'guides';
	protected $primaryKey = 'id';

    protected $fillable = ['title', 'content', 'sort', 'status', 'subtitle'];

}