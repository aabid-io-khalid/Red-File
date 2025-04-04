<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Subscription as StripeSubscription;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth'); // Ensure user is authenticated for all methods
    // }

    // Show subscription page
    public function showSubscriptionPage()
    {
        $user = auth()->user();

        if ($user->hasActiveSubscription()) {
            return redirect()->route('subscription.manage')
                ->with('info', 'You already have an active subscription.');
        }

        return view('subscription.index');
    }

    // Process subscription (Stripe Checkout flow)
    public function processSubscription(Request $request)
    {
        $user = auth()->user();

        if ($user->hasActiveSubscription()) {
            return redirect()->route('subscription.manage')
                ->with('info', 'You already have an active subscription.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $customer = null;
        $existingSubscription = $user->subscription;

        if ($existingSubscription && $existingSubscription->stripe_customer_id) {
            $customer = Customer::retrieve($existingSubscription->stripe_customer_id);
        } else {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => ['user_id' => $user->id],
            ]);
        }

        $session = \Stripe\Checkout\Session::create([
            'customer' => $customer->id,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => env('STRIPE_PRICE_ID'),
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscription.cancel'),
        ]);

        return redirect($session->url);
    }

    // API endpoint for direct subscription creation from the form
    public function createSubscription(Request $request)
{
    $user = auth()->user();

    if ($user->hasActiveSubscription()) {
        return response()->json([
            'success' => false,
            'message' => 'You already have an active subscription.'
        ], 400);
    }

    $validated = $request->validate([
        'payment_method_id' => 'required|string',
        'plan' => 'required|string',
    ]);

    Stripe::setApiKey(env('STRIPE_SECRET'));

    try {
        // Create or get customer
        $customer = null;
        $existingSubscription = $user->subscription;

        if ($existingSubscription && $existingSubscription->stripe_customer_id) {
            $customer = Customer::retrieve($existingSubscription->stripe_customer_id);
        } else {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
                'payment_method' => $validated['payment_method_id'],
                'invoice_settings' => [
                    'default_payment_method' => $validated['payment_method_id'],
                ],
                'metadata' => ['user_id' => $user->id],
            ]);
        }

        // Attach payment method to customer
        PaymentMethod::retrieve($validated['payment_method_id'])->attach([
            'customer' => $customer->id,
        ]);

        // Create subscription
        $subscription = StripeSubscription::create([
            'customer' => $customer->id,
            'items' => [['price' => env('STRIPE_PRICE_ID')]],
            'payment_behavior' => 'allow_incomplete',
            'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
            'metadata' => ['user_id' => $user->id, 'plan' => $validated['plan']],
        ]);

        // Retrieve the latest invoice to check payment intent
        $invoice = \Stripe\Invoice::retrieve($subscription->latest_invoice, ['expand' => ['payment_intent']]);
        $paymentIntent = $invoice->payment_intent;

        // Save subscription data
        $userSubscription = $user->subscription ?? new Subscription(['user_id' => $user->id]);
        $userSubscription->fill([
            'stripe_customer_id' => $customer->id,
            'stripe_subscription_id' => $subscription->id,
            'status' => $subscription->status,
            'plan_name' => 'PELIXS Premium',
            'amount' => 2.00,
            'trial_ends_at' => $subscription->trial_end ? Carbon::createFromTimestamp($subscription->trial_end) : null,
            'current_period_starts_at' => $subscription->current_period_start ? Carbon::createFromTimestamp($subscription->current_period_start) : null,
            'current_period_ends_at' => $subscription->current_period_end ? Carbon::createFromTimestamp($subscription->current_period_end) : null,
            'canceled_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        $userSubscription->save();

        // Check if payment needs additional action (e.g., 3D Secure)
        if ($paymentIntent && $paymentIntent->status === 'requires_action') {
            return response()->json([
                'requires_action' => true,
                'client_secret' => $paymentIntent->client_secret,
            ]);
        }

        // If no action required, subscription is active
        return response()->json(['success' => true]);

    } catch (\Stripe\Exception\ApiErrorException $e) {
        \Log::error('Stripe API Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Stripe Error: ' . $e->getMessage()
        ], 400);
    } catch (\Exception $e) {
        \Log::error('Subscription Creation Error: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
        return response()->json([
            'success' => false,
            'message' => 'Server Error: ' . $e->getMessage()
        ], 500);
    }
}



    // Handle subscription success
    public function success(Request $request)
    {
        $user = auth()->user();

        if (!$request->has('session_id')) {
            return redirect()->route('subscription');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = \Stripe\Checkout\Session::retrieve($request->session_id);
            $subscription = StripeSubscription::retrieve($session->subscription);

            $userSubscription = $user->subscription ?? new Subscription(['user_id' => $user->id]);
            $userSubscription->fill([
                'stripe_customer_id' => $session->customer,
                'stripe_subscription_id' => $session->subscription,
                'status' => $subscription->status,
                'plan_name' => 'PELIXS Premium',
                'amount' => 2.00,
                'trial_ends_at' => $subscription->trial_end ? Carbon::createFromTimestamp($subscription->trial_end) : null,
                'current_period_starts_at' => Carbon::createFromTimestamp($subscription->current_period_start),
                'current_period_ends_at' => Carbon::createFromTimestamp($subscription->current_period_end),
                'canceled_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);          
            
            $userSubscription->save();

            return view('subscription.success');
        } catch (\Exception $e) {
            return redirect()->route('subscription')
                ->with('error', 'Error processing subscription: ' . $e->getMessage());
        }
    }

    // Handle subscription cancellation (show cancel page)
    public function cancel()
    {
        return view('subscription.cancel');
    }

    // Manage subscription
    public function manage()
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        if (!$subscription) {
            return redirect()->route('subscription')
                ->with('info', 'You don\'t have any active subscriptions.');
        }

        return view('subscription.manage', compact('subscription'));
    }

    // Cancel subscription
    public function cancelSubscription()
    {
        $user = auth()->user();

        if (!$user->subscription || !$user->subscription->stripe_subscription_id) {
            return redirect()->back()->with('error', 'No active subscription found.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $stripeSubscription = StripeSubscription::update(
                $user->subscription->stripe_subscription_id,
                ['cancel_at_period_end' => true]
            );

            $user->subscription->update([
                'canceled_at' => Carbon::now(),
            ]);

            return redirect()->back()->with('success', 'Your subscription has been canceled. You will have access until the end of your billing period.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error canceling subscription: ' . $e->getMessage());
        }
    }
}