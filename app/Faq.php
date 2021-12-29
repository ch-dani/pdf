<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model {

	protected $table = 'faq';
	protected $primaryKey = 'id';

    protected $fillable = ['title', 'icons', 'sort', 'steps', 'link', 'link_title', 'status'];

}