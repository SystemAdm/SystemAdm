<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\Phone;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

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
            ->with(['roles', 'profile.postal', 'guardians', 'children']) // Ensure roles and profile are loaded
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
     * Store a newly created user with either phone or email
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'given_name' => ['required', 'string', 'min:2', 'max:32'],
            'additional_name' => ['nullable', 'string', 'min:2', 'max:32'],
            'family_name' => ['required', 'string', 'min:2', 'max:32'],
            'phone' => ['required_without:email', 'nullable', 'string'],
            'email' => ['required_without:phone', 'nullable', 'email'],
            'birthday' => ['nullable','date']
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'given_name' => $validated['given_name'],
                'additional_name' => $validated['additional_name'],
                'family_name' => $validated['family_name'],
            ]);

            if (isset($validated['phone'])) {
                $this->attachPhone($user, $validated['phone']);
            }

            if (isset($validated['email'])) {
                $this->attachEmail($user, $validated['email']);
            }

            $user->profile()->create(['birthday' => $validated['birthday'] ?? null]);
            $user->assignRole('Member');

            DB::commit();

            return response()->json(
                $user->load(['phones', 'emails', 'profile', 'roles']),
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Attach phone number to user
     */
    private function attachPhone(User $user, string $phoneNumber): void
    {
        $phoneUtil = $this->phoneUtil->parse($phoneNumber, 'NO');

        if (!$this->phoneUtil->isValidNumber($phoneUtil)) {
            throw new \InvalidArgumentException('Invalid phone number');
        }

        $phone = Phone::firstOrCreate([
            'country' => $phoneUtil->getCountryCode(),
            'number' => $phoneUtil->getNationalNumber(),
        ]);

        $user->phones()->attach($phone, ['primary' => true]);
    }

    /**
     * Attach email to user
     */
    private function attachEmail(User $user, string $emailAddress): void
    {
        $emailParts = explode('@', $emailAddress);
        $domainParts = explode('.', $emailParts[1]);

        $email = Email::firstOrCreate([
            'name' => $emailParts[0],
            'domain' => implode('.', array_slice($domainParts, 0, -1)),
            'tld' => end($domainParts),
        ]);

        $user->emails()->attach($email, ['primary' => true]);
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
            $phone = Phone::with('users', 'users.profile')->where('number', $parsed->getNationalNumber())->where('country', $parsed->getCountryCode())->first();
            if ($phone->users()->count() > 0) {
                return response()->json($phone->users()->get());
            }

        }
        return response()->json(null, 404);
    }

    /**
     * @throws NumberParseException
     */
    public function validatePhone(Request $request)
    {
        $validated = $request->validate(['phone' => 'required']);
        $parsed = $this->phoneUtil->parse($validated['phone'], 'NO');
        if ($this->phoneUtil->isValidNumber($parsed)) {
            $phone = Phone::with('users', 'users.profile.postal', 'users.guardians', 'users.children')->where('number', $parsed->getNationalNumber())->where('country', $parsed->getCountryCode())->first();
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
        $split = explode('@', $validated['email']);

        $name = $split[0]; // Part before @ (username)
        $domainPart = $split[1]; // Part after @

        // Find the position of the last dot in the domain part
        $lastDotPos = strrpos($domainPart, '.');

        // Extract the domain and TLD
        $domain = substr($domainPart, 0, $lastDotPos); // Domain part before the last dot
        $tld = substr($domainPart, $lastDotPos + 1); // TLD (part after the last dot)

        $email = Email::with('users', 'users.profile.postal', 'users.guardians', 'users.children')->where('name', $name)->where('domain', $domain)->where('tld', $tld)->first();
        if ($email && $email->users->count() > 0) {
            return response()->json($email->users->keyBy('id'));
        }

        if ($name != null && $domain != null && $tld != null) {
            return response()->json(1);
        }
        return response()->json(0);
    }

    public function me(Request $request)
    {
        return User::with('phones','emails')->find($request->user()->id);
    }

    public function qr(User $user)
    {
        $qrCode = $user->generateQrCode();

        // Sjekk om klienten forventer JSON
        if (request()->expectsJson()) {
            return response()->json([
                'svg' => $qrCode
            ]);
        }

        // Ellers returner SVG direkte med riktige headers
        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    public function check(Request $request)
    {
        $validated = $request->validate(['u' => ['required', 'exists:users,id'], 'p' => ['required', 'string']]);
        $user = User::find($validated['u']);
        $pwd = $validated['p'];

        $auth = Hash::check($pwd, $user->password);
        return response()->json($auth);
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);

        Auth::login($user);

        // Generer Sanctum token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token
        ]);
    }

    public function activate(Request $request)
    {
        // Hent og dekrypter den midlertidige nøkkelen
        $encryptedKey = session('temp_key');
        $tempKey = Crypt::decryptString($encryptedKey);

        // Dekrypter passordet med den midlertidige nøkkelen
        $decrypted = decrypt($request->password, $tempKey);

        // Hash passordet for lagring
        $hashedPassword = Hash::make($decrypted);
    }

    /**
     * Get the authenticated user with permissions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(null);
        }

        // Load permissions
        $permissions = $user->getAllPermissions()->pluck('name');

        // Add permissions to user object
        $userData = $user->toArray();
        $userData['permissions'] = $permissions;

        return response()->json($userData);
    }

    /**
     * Logout the user and invalidate their token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            Log::info('has token and user');
            // Slett brukerens nåværende token
            if ($request->user() && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }
            Log::info('has user');
            // Logg ut brukeren
            Auth::guard('web')->logout();

            Log::info('has none');

            if ($request->hasSession()) {
                // Invalider sesjonen
                if (!$request->session()->isStarted()) {
                    $request->session()->start();
                }
                Log::info('Session initiated:', ['isStarted' => $request->session()->isStarted()]);
                $request->session()->invalidate();
                Log::info('invalidated');
                $request->session()->regenerateToken();
                Log::info('regenerated');
            }
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage(), [
                'user_id' => optional($request->user())->id,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to logout'
            ], 500);
        }
    }
}
