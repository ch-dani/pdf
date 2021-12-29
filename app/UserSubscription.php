<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    protected $fillable = ['subscription_plan_id', 'user_id', 'status', 'next_payment_at', 'details', 'subscribtion_id'];

    protected $dates = ['next_payment_at'];

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function calculateNextPaymentDate(SubscriptionPlan $subscriptionPlan)
    {
        $now = now();
        if ($subscriptionPlan->interval_unit === SubscriptionPlan::INTERVAL_UNITS['monthly']) {
            return $now->addMonths($subscriptionPlan->interval_count);
        } elseif ($subscriptionPlan->interval_unit === SubscriptionPlan::INTERVAL_UNITS['annually']) {
            return $now->addYears($subscriptionPlan->interval_count);
        }

        return null;
    }
}
