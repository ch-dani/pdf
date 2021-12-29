<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model{
	protected $table = 'rating';
	protected $primaryKey = 'id';

    protected $fillable = ['ip', 'url', 'rate'];


}
