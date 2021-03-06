<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogCategories extends Model
{

    protected $table = 'blog_categories';
    protected $primaryKey = 'id';

    protected $fillable = ['title', 'status'];

}
