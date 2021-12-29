<?php

namespace App\Services\PaymentGateways;

use Cartalyst\Stripe\Stripe;

class StripePayment
{
    public $stripe;
    public $card_number;
    public $card_month;
    public $card_year;
    public $card_cvc;
    public $customer_email;

    /**
     * StripePayment constructor.
     * @param null $customer_email
     * @param null $card_number
     * @param null $card_month
     * @param null $card_year
     * @param null $card_cvc
     * @param null $amount
     * @param null $currency
     */
    function __construct(
    	$subscriptionPlan=false,
        $customer_email = null,
        $card_number = null,
        $card_month = null,
        $card_year = null,
        $card_cvc = null,
        $amount = null,
        $currency = null
    )
    {
    	
    	$priv_key = config('services.stripe.secret');
    	
        $this->stripe = new Stripe($priv_key);
        
        
        $this->subscriptionPlan =  $subscriptionPlan;
        $this->customer_email = $customer_email;
        $this->card_number = $card_number;
        $this->card_month = $card_month;
        $this->card_year = $card_year;
        $this->card_cvc = $card_cvc;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function card_payment()
    {
        $this->stripe->idempotent(uniqid());

        // Creating temporary user
        $customer = $this->stripe->customers()->create([
            'email' => $this->customer_email,
        ]);

        $this->stripe->idempotent(uniqid());

        // Creating temporary card
        $token = $this->stripe->tokens()->create([
            'card' => [
                'number' => $this->card_number,
                'exp_month' => $this->card_month,
                'exp_year' => $this->card_year,
                'cvc' => $this->card_cvc,
            ],
        ]);

        $this->stripe->idempotent(uniqid());

        // Need add temporary card to customer
        $this->stripe->cards()->create($customer['id'], $token['id']);

        $this->stripe->idempotent(uniqid());


		
		if($this->subscriptionPlan->interval_unit=='forever'){
		    $charge = $this->stripe->charges()->create([
		        'customer' => $customer['id'],
		        'currency' => $this->currency,
		        'amount' => $this->amount,
		    ]);
		
		    if (isset($charge['status']) and $charge['status'] == 'succeeded') {
		        return array_merge($charge, ['token' => $token['id']]);
		    } else {
		        return false;
		    }		
		}else{
			$this->stripe->idempotent(uniqid());
			$plan_id = $this->subscriptionPlan->interval_unit."_".uniqid();
			
			//customer

			$this->stripe->idempotent(uniqid());
			$plan = $this->stripe->plans()->create([
				'id'                    => $plan_id,
				'name'                  => "Free Convert PDF ".$this->subscriptionPlan->interval_unit." subscription",
				'amount'                => $this->subscriptionPlan->price,
				'currency'              => 'USD',
				'interval'              => $this->subscriptionPlan->interval_unit,
				//'statement_description' => 'Monthly Subscription to Free Convert PDF',
			]);
			
			$this->stripe->idempotent(uniqid());
			$subscription = $this->stripe->subscriptions()->create($customer['id'], [
				'plan' => $plan_id,
			]);
			
			if($subscription['status']=='active'){
				 return array_merge($subscription, ['token' => $token['id']]);
			}else{
				return false;
			}		
		
		}

    }

    /**
     * Pay by previously created and stored token.
     *
     * @param string $token
     * @return false
     */
    public function card_payment_by_saved_token(string $token)
    {
        $this->stripe->idempotent(uniqid());

        $charge = $this->stripe->charges()->create([
            'customer' => $token,
            'currency' => $this->currency,
            'amount' => $this->amount,
        ]);

        if (isset($charge['status']) && $charge['status'] == 'succeeded') {
            return $charge;
        } else {
            return false;
        }
    }

    /**
     * @param $charge_id
     * @return mixed
     */
    public function refund($charge_id)
    {
        return $this->stripe->refunds()->create($charge_id);
    }

    /**
     * Get array of cards information.
     *
     * @param string $customerToken
     * @return array
     */
    public function get_customer_card_info(string $customerToken): array
    {
        $customerCards = $this->stripe->cards()->all($customerToken);

        return end($customerCards['data']);
    }
}
