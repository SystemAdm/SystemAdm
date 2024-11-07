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
            'success' => ['required', 'boolean'],
        ]);
        $user = auth()->user();
        $membership = new Membership([
            'online' => true,
            'valid_to' => now()->addYear()
        ]);
        $user->memberships()->save($membership);
        return response()->json($membership);
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

    public function pay(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = PaymentIntent::create([
            'amount' => 15000,
            'currency' => 'nok',
            'payment_method_types' => ['card'],
        ]);

        return response()->json(['clientSecret' => $paymentIntent->client_secret]);
    }
}
