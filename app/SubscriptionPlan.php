<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    public const INTERVAL_UNITS = [
        'annually' => 'year',
        'monthly' => 'month',
        'one_time' => 'forever',
    ];

    protected $fillable = [
        'code', 'name', 'description', 'price', 'interval_unit', 'interval_count',
    ];
}
