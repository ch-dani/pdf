<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stripe extends Model
{
    protected $table = 'stripe_payments';
    protected $primaryKey = 'id';
    protected $fillable = ['token', 'charge_id', 'status', 'data', 'user_id', 'amount', 'country'];
}
