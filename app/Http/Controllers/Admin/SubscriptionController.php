<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\SubscriptionPlan;
use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = SubscriptionPlan::all();
        return view('admin.subscriptions', [
            'subscriptions' => $subscriptions,
            'js' => [
                asset('js/admin/subscriptions.js')
            ]
        ]);
    }

    public function edit(SubscriptionPlan $subscription)
    {
        return view('admin.subscription-edit', compact('subscription'));
    }

    public function update(Request $request, SubscriptionPlan $subscription)
    {
        $validatedData = $request->validate([
            'code' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'interval_unit' => 'required',
            'interval_count' => 'nullable|numeric',
        ]);

        $subscription->update($validatedData);

        return redirect()->back()->with('success', 'Subscription ' . $subscription->name . ' edited');
    }

    public function create()
    {
        return view('admin.subscription-add');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|unique:subscription_plans,code',
            'name' => 'required|string|unique:subscription_plans,name',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'interval_unit' => 'required',
            'interval_count' => 'nullable|numeric',
        ]);

        $subscription = SubscriptionPlan::create($validatedData);

        return redirect()->back()->with('success', 'Subscription ' . $subscription->name . ' added');
    }

    public function delete(SubscriptionPlan $subscription)
    {
        $subscription->delete();
        return response()->json(['status' => 'success', 'message' => 'Subscription ' . $subscription->name . ' was deleted']);
    }
}
