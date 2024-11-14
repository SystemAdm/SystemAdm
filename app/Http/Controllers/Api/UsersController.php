<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\Phone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use libphonenumber\PhoneNumberUtil;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UsersController extends Controller
{
    private PhoneNumberUtil $phoneUtil;

    public function __construct()
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::query()
            ->where(function ($query) {
                $query->where('active', true)
                    ->whereHas('profile', function ($query) {
                        $query->where('visible', true);
                    });
            })
            ->orWhereHas('roles', function ($query) {
                $query->where('name', 'Crew');
            })
            ->with(['roles', 'profile.postal']) // Ensure roles and profile are loaded
            ->orderByRaw('CASE WHEN EXISTS (
            SELECT 1
            FROM model_has_roles
            WHERE model_has_roles.role_id = users.id
            AND model_has_roles.model_id = (SELECT id FROM roles WHERE name = "Crew" LIMIT 1)
        ) THEN 0 ELSE 1 END')
            ->orderBy('first_name') // Ordering users by first name
            ->get();
        return response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /*$validated = $request->validate([
            'firstname' => ['required', 'min:2', 'max:32'],
            'middlename' => ['nullable', 'min:2', 'max:32'],
            'lastname' => ['required', 'min:2', 'max:32'],
            'phone' => 'required',
        ]);
        $phoneUtil = $this->phoneUtil->parse($validated['phone'], 'NO');
        $phone = Phone::firstOrCreate(['country' => $phoneUtil->getCountryCode(), 'number' => $phoneUtil->getNationalNumber(), 'primary' => true]);
        $user = new User(['first_name' => $validated['firstname'], 'middle_name' => $validated['middlename'], 'last_name' => $validated['lastname']]);
        $user->save();
        $user->assignRole(6);
        $user->phones()->attach($phone);*/
        $user = User::find(8);
        return response()->json($user);

        //return User::with('phones')->find(8);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return User::with('phones', 'roles', 'profile')->find($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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

    public function findByPhone(Request $request)
    {
        $validated = $request->validate(['phone' => 'required']);
        $parsed = $this->phoneUtil->parse($validated['phone'], 'NO');
        if ($this->phoneUtil->isValidNumber($parsed)) {
            $phone = Phone::with('users','users.profile')->where('number', $parsed->getNationalNumber())->where('country', $parsed->getCountryCode())->first();
            if ($phone->users()->count() > 0) {
                return response()->json($phone->users()->get());
            }

        }
        return response()->json(null, 404);
    }

    public function validatePhone(Request $request)
    {
        $validated = $request->validate(['phone' => 'required']);
        $parsed = $this->phoneUtil->parse($validated['phone'], 'NO');
        if ($this->phoneUtil->isValidNumber($parsed)) {
            $phone = Phone::with('users','users.profile')->where('number', $parsed->getNationalNumber())->where('country', $parsed->getCountryCode())->first();
            if ($phone && $phone->users->count() > 0) {
                return response()->json($phone->users->keyBy('id'));
            }
            return response()->json(1);
        }
        return response()->json(0);
    }

    public function validateEmail(Request $request)
    {
        $validated = $request->validate(['email' => 'required']);
        $splitted = explode('@', $validated['email']);

        $name = $splitted[0]; // Part before @ (username)
        $domainPart = $splitted[1]; // Part after @

        // Find the position of the last dot in the domain part
        $lastDotPos = strrpos($domainPart, '.');

        // Extract the domain and TLD
        $domain = substr($domainPart, 0, $lastDotPos); // Domain part before the last dot
        $tld = substr($domainPart, $lastDotPos + 1); // TLD (part after the last dot)

        $email = Email::with('users')->where('name', $name)->where('domain', $domain)->where('tld', $tld)->first();
        if ($email && $email->users->count() > 0) {
            return response()->json($email->users);
        }

        if ($name != null && $domain != null && $tld == null) {
            return response()->json(1);
        }
        return response()->json(0);
    }

    public function me(Request $request)
    {
        return User::with('phones')->find($request->user()->id);
    }

    public function qr(Request $request)
    {
        $validated = $request->validate([
            'selected'=>['nullable','integer']
        ]);
        //$u = Auth::user()->id;
        $u = $validated['selected'];
        $i = $u * 35;
        $w = 'SpL';

        $riddle = $w.$i;

        $qrCode = base64_encode(QrCode::format('png')->size(200)->generate($riddle));

        return response($qrCode)->header('Content-Type','image/png');
    }
    public function check(Request $request)
    {
        $validated = $request->validate(['u'=>['required','exists:users,id'],'p'=>['required','string']]);
        $user = User::find($validated['u']);
        $pwd = $validated['p'];

        $auth = Hash::check($pwd, $user->password);
        return response()->json($auth);
    }
}
