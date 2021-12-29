<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TranslatePricing extends Model{
    protected $table = 'translate_pricing';
    protected $primaryKey = 'id';
    protected $fillable = ['chars', 'price'];	

}
