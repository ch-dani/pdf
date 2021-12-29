<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model {

	protected $table = 'menu';
	protected $primaryKey = 'id';

    protected $fillable = ['title', 'url', 'target', 'tooltip', 'category_id', 'sort', 'new', 'popularity'];

}
