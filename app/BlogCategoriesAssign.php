<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogCategoriesAssign extends Model
{

    protected $table = 'blog_categories_assign';
    protected $primaryKey = 'id';

    protected $fillable = ['blog_id', 'category_id'];


}
