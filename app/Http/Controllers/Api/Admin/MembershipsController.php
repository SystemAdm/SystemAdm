<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\User;
use ErrorException;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class MembershipsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$user = auth()->user();
        $user = User::find(8);
        return Membership::with('user')->where('user_id', $user->id)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
}
