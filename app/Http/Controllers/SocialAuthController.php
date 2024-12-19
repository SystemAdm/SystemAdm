<?php

namespace App\Http\Controllers;

// User og Email er i many-to-many-relasjon

use App\Models\SocialAccounts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function callback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $parsedName = $this->parseName($socialUser->getName() ?? "Unknown User");

        $parsedEmail = $this->parseEmail($socialUser->getEmail() ?? "unknown@email.com");

        $socialAccount = SocialAccounts::where('provider', $provider)->where('provider_id', $socialUser->getId())->first();

        if ($socialAccount) {
            Auth::login($socialAccount->user);
            return redirect('/');
        }
    }

    /**
     * Parse og separer fullt navn i given, additional og family.
     */
    function parseName(?string $name): array
    {
        if (!$name) {
            return [
                'given_name' => 'Ukjent',
                'additional_name' => null,
                'family_name' => 'Bruker',
            ];
        }

        $parts = explode(' ', $name);

        return [
            'given_name' => ucfirst(trim($parts[0] ?? '')), // Første ord
            'additional_name' => count($parts) > 2 ? ucfirst(implode(' ', array_slice($parts, 1, -1))) : null, // Midtdeler (om noen)
            'family_name' => ucfirst(trim($parts[count($parts) - 1] ?? '')), // Siste ord
        ];
    }

    /**
     * Parse e-post til separate deler.
     */
    function parseEmail(string $email): array
    {
        [$localPart, $domain] = explode('@', $email);
        $domainName = Str::beforeLast('.', $domain);
        $domainTld = Str::afterLast('.', $domain);


        return [
            'name' => $localPart,
            'domain' => $domainName ?? '',
            'tld' => $domainTld ?? '',
        ];
    }
}
