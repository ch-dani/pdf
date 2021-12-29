<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model {

    protected $table = 'contacts';
    protected $primaryKey = 'id';

    protected $fillable = ['message', 'name', 'email', 'problem'];

}