<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class FooterMenu extends Model {

    protected $table = 'footer_menu';
    protected $primaryKey = 'id';

    protected $fillable = ['title', 'url', 'target', 'sort', 'type'];

}