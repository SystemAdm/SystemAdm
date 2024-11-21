<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class MembershipsController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $memberships = Membership::where('user_id', 8)->orderBy('valid_to', 'DESC')->get();
        return response()->json(['memberships' => $memberships, 'user' => $user]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_intent_id' => ['required', 'string'],
            'success' => ['required', 'boolean'],
            'gifted' => ['boolean'],
        ]);

        $user = auth()->user();
        $transaction = new Membership([
            'user_id' => $user->id,
            'gifted' => $validated['gifted'] ?? false,
            'online' => true,    // Alltid true for Stripe
            'cash' => false,     // Alltid false for Stripe
            'vipps' => false,    // Alltid false for Stripe
            'card' => true,      // Alltid true for Stripe
            'valid_to' => now()->addYear(),
        ]);
        
        $user->memberships()->save($transaction);
        return response()->json($transaction);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Generer Stripe client secret
     */
    public function pay(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount' => config('memberships.price', 15000),
            'currency' => config('memberships.currency', 'nok'),
            'payment_method_types' => ['card'],
            'metadata' => [
                'user_id' => auth()->id()
            ]
        ]);

        return response()->json(['clientSecret' => $paymentIntent->client_secret]);
    }
}
