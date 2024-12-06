<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use App\Models\User;
use Illuminate\Http\Request;

class PhonesController extends Controller
{
    public function setPrimary(Request $request,Phone $phone) {
        $validated = $request->validate([
            'user' => ['required','exists:users,id'],
        ]);
        $user = User::find($validated['user']);

        $user->phones()->updateExistingPivot($user->phones->pluck('id')->toArray(), ['primary' => false]);
        $user->phones()->updateExistingPivot($phone,['primary' => true]);

        response()->json(['message'=>'Phone set as primary'],200);
    }
}
