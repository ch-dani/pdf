<?php

namespace App;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'last_activity', 'avatar', 'name', 'status', 'register_token', 'last_confirmation', 'country', 'google_id', 'facebook_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $with = ['subscription'];

    public function subscription()
    {
        return $this->hasOne(UserSubscription::class)->latest();
    }

	/**
	 * @return bool
	 */
	public function getHasActivePlanAttribute()
	{
		return (isset($this->subscription) && $this->subscription->status == 'active') ? true : false;
	}

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

	public function getPaymentCountryAttribute()
	{
		$data = ['code' => '', 'name' => ''];

		$paypal_payment = \App\PayPal::where('user_id', $this->id)->latest()->first();
		$stripe_payment = \App\Stripe::where('user_id', $this->id)->latest()->first();

		$payment_date = '';
		$payment_country = '';

		if($paypal_payment){
			$payment_date = $paypal_payment->created_at;
			$payment_country = $paypal_payment->country;
		}
		if($stripe_payment && $stripe_payment->created_at > $payment_date){
			$payment_country = $stripe_payment->country;
		}

		if($payment_country){
			$countries = json_decode(file_get_contents(public_path('countries.json')), true);

			$data['code'] = $payment_country;
			$data['name'] = $countries[$payment_country];
		}

		return $data;
	}
}
