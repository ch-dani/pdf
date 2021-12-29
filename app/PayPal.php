<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayPal extends Model
{
    protected $table = 'paypal_payments';

    protected $fillable = ['charge_id', 'status', 'data', 'user_id', 'amount', 'country'];
}
