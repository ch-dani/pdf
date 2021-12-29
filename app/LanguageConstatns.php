<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LanguageConstatns extends Model{
	protected $table = 'language_constants';
	protected $primaryKey = 'id';

    protected $fillable = ['key', 'translate'];




}
