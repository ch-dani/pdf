<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\SubscriptionPlan;
use App\Stripe;
use App\PayPal;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\UserSubscription;
use App\Services\PaymentGateways\StripePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PurchaseSubscriptionController extends Controller
{
    public function getSubscriptionPlanById(SubscriptionPlan $subscriptionPlan)
    {
        return response()->json(['status' => 'success', 'data' => $subscriptionPlan]);
    }

    public function storeStepAccountCredentials(Request $request)
    {
        $validatedData = $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'email' => 'required|email|exists:users',
            'password' => 'sometimes',
        ]);

        if ($request->has('password')) {
            if (auth()->attempt([
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
            ], true)) {
                session()->put('purchase-subscription.steps.credentials', $validatedData);
                return response()->json(['status' => 'success', 'message' => 'Data saved']);
            }
        } else {
            session()->put('purchase-subscription.steps.credentials', $validatedData);
            return response()->json(['status' => 'success', 'message' => 'Data saved']);
        }

        return response()->json(['status' => 'error', 'message' => 'These credentials do not match our records.'], 403);
    }

    public function storeStepLocation(Request $request)
    {
        $validatedData = $request->validate(['location' => 'required|string']);

        session()->put('purchase-subscription.steps.location', $validatedData);

        return response()->json(['status' => 'success', 'message' => 'Data saved']);
    }

    public function storeStepPayByCard(Request $request)
    {
        $validatedData = $request->validate([
            'card_number' => 'required',
            'cardholder_name' => 'required|string',
            'card_mm' => 'required',
            'card_yyyy' => 'required',
            'card_cvv' => 'required',
        ]);

        $sessionData = array_merge(
            session()->get('purchase-subscription.steps.credentials'),
            session()->get('purchase-subscription.steps.location')
        );
//        print_r($sessionData);exit;
        $subscriptionPlan = SubscriptionPlan::findOrFail($sessionData['subscription_plan_id']);

        try {
            DB::beginTransaction();
            

            $stripePayment = new StripePayment(
            	$subscriptionPlan,
                $sessionData['email'],
                $validatedData['card_number'],
                $validatedData['card_mm'],
                $validatedData['card_yyyy'],
                $validatedData['card_cvv'],
                $subscriptionPlan->price,
                'USD'
            );

            $payment = $stripePayment->card_payment();
            
            
            
            
            if ($payment) {
            
            	echo "<pre>";
            	var_dump($payment);
            	exit();
            
                Stripe::create([
                    'token' => $payment['token'],
                    'charge_id' => $payment['id'],
                    'status' => 'success',
                    'amount' => $subscriptionPlan->price,
                    'user_id' => auth()->user()->id,
                    'country' => $sessionData['location'],
                ]);

                UserSubscription::create([
                    'subscription_plan_id' => $subscriptionPlan->id,
                    'user_id' => auth()->user()->id,
                    'next_payment_at' => UserSubscription::calculateNextPaymentDate($subscriptionPlan),
                ]);

                DB::commit();
                
                echo "<pre>";
                exit("the end");
                
                $this->sendOrderMail($request,  $payment['id'], $payment['amount_captured']/100);
                
                

                return response()->json(['status' => 'success', 'message' => 'Subscription successfully purchased']);
            } else {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => 'Payment failed'], 402);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $exception->getMessage()], 402);
        }
    }
    
    
    public function sendOrderMail($req, $payment_id=false, $amount=0){


        $user = auth()->user();
        $userSubscription = $user->subscription;
        if(!$userSubscription){
        	return;
        }
        $userSubscriptionPlan = $userSubscription->subscriptionPlan;
        
        $data= [
        	'amount'=>$amount,
        	'trans_id'=>$payment_id,
        	'next_payment_at'=>"", //$userSubscription->next_payment_at->format('m/d/Y'),
        	'plan'=>$userSubscriptionPlan->name,
        	'payment_date'=>$userSubscriptionPlan->created_at->format('m/d/Y'),
			'site_url'=>env('APP_URL')        	
        ];
        
        Mail::send('emails.payment', array_merge(['user' => $user], $data), function ($m) use ($user) {
            $m->to($user->email, "Usere name")->subject('Free Convert PDF payment');
        });
    }

    public function storeStepPayByPayPal(Request $request)
    {
        $sessionData = array_merge(
            session()->get('purchase-subscription.steps.credentials'),
            session()->get('purchase-subscription.steps.location')
        );
        $subscriptionPlan = SubscriptionPlan::findOrFail($sessionData['subscription_plan_id']);

        $paymentDetails = $request->payment_details;
        if ($paymentDetails['status'] === 'COMPLETED') {
            PayPal::create([
                'charge_id' => $paymentDetails['id'],
                'status' => 'success',
                'amount' => $subscriptionPlan->price,
                'user_id' => auth()->user()->id,
				'country' => $sessionData['location'],
            ]);

            UserSubscription::create([
                'subscription_plan_id' => $subscriptionPlan->id,
                'user_id' => auth()->user()->id,
                'next_payment_at' => UserSubscription::calculateNextPaymentDate($subscriptionPlan),
            ]);
            
            $this->sendOrderMail($request, $paymentDetails['id']);

            return response()->json(['status' => 'success', 'message' => 'Subscription successfully purchased']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Payment failed'], 402);
        }
    }

    public function payForSubscription(Request $request)
    {
        $validatedData = $request->validate([
            'card_number' => 'required',
            'cardholder_name' => 'required|string',
            'card_mm' => 'required',
            'card_yyyy' => 'required',
            'card_cvv' => 'required',
        ]);

        $user = auth()->user();
        $userSubscription = $user->subscription;
        $userSubscriptionPlan = $userSubscription->subscriptionPlan;

        try {
            DB::beginTransaction();

            $stripePayment = new StripePayment(
                $user->email,
                $validatedData['card_number'],
                $validatedData['card_mm'],
                $validatedData['card_yyyy'],
                $validatedData['card_cvv'],
                $userSubscriptionPlan->price,
                'USD'
            );

            $payment = $stripePayment->card_payment();
            if ($payment) {
                Stripe::create([
                    'token' => $payment['token'],
                    'charge_id' => $payment['id'],
                    'status' => 'success',
                    'amount' => $userSubscriptionPlan->price,
                    'user_id' => auth()->user()->id,
                ]);

                $userSubscription->update([
                    'subscription_plan_id' => $userSubscriptionPlan->id,
                    'status' => 'active',
                    'next_payment_at' => UserSubscription::calculateNextPaymentDate($userSubscriptionPlan),
                ]);

                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Subscription successfully purchased']);
            } else {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => 'Payment failed'], 402);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $exception->getMessage()], 402);
        }
    }
}
