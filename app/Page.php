<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Page extends Model {

	protected $table = 'pages';
	protected $primaryKey = 'id';

    protected $fillable = ['title', 'status', 'link', 'default_link','bottom_blocks', 'blocks', 'seo_title', 'seo_keywords', 'seo_description', 'tool'];

    public function ads(){
        return $this->hasMany('App\Ads');
    }
}
