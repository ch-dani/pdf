<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model {

	protected $table = 'menu_category';
	protected $primaryKey = 'id';

    protected $fillable = ['title', 'sort'];

}