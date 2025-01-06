<?php

namespace App\Http\Controllers;

// User og Email er i many-to-many-relasjon

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Random\RandomException;
use Twilio\Rest\Client;

class SocialAuthController extends Controller
{
    function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'contact' => 'required|string|max:255',
            'channel' => 'required|string|max:255',
            'code' => 'required|string|max:255'
        ]);
        $verification = DB::table('verification_codes')
            ->where('channel', $validated['channel'])
            ->where('contact', $validated['contact'])
            ->where('code', $validated['code'])
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($verification) {
            // Kode er gyldig
            return ['success' => true, 'message' => __('auth.verified')];
        } else {
            // Kode er ugyldig eller utløpt
            return ['success' => false, 'message' => __('auth.invalid_code')];
        }
    }

    /**
     * @throws RandomException
     */
    function generateVerificationCode(Request $request)
    {
        $validated = $request->validate([
            'contact' => 'required|string|max:255',
            'channel' => 'required|string|max:255'
        ]);

        $code = random_int(100000, 999999); // Generer en tilfeldig 6-sifret kode

        // Lagre koden i databasen
        DB::table('verification_codes')->updateOrInsert(
            [
                'channel' => $validated['channel'],
                'contact' => $validated['contact']
            ],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(5), // Koden utløper om 5 minutter
                'updated_at' => Carbon::now(),
            ]
        );

        // Hvis kanalen er "phone", bruk Twilio for å sende SMS
        if ($validated['channel'] === 'phone') {
            try {
                $sid = config('services.twilio.sid');
                $token = config('services.twilio.token');
                $twilioNumber = config('services.twilio.phone_number');

                // Instansier Twilio-klienten
                $twilio = new Client($sid, $token);

                // Meldingsinnhold
                $message = "Din verifikasjonskode er: {$code}";

                // Send melding
                $twilio->messages->create(
                    $validated['contact'], // Mottakerens telefonnummer
                    [
                        'from' => $twilioNumber, // Avsendernummer
                        'body' => $message       // Selve meldingen
                    ]
                );

                // Loggfør melding ved suksess
                //Log::info("Verification code {$code} sent successfully to {$validated['contact']} via Twilio.");
            } catch (\Exception $e) {
                // Loggfør feilen dersom noe går galt
                //Log::error("Failed to send verification code to {$validated['contact']}. Error: {$e->getMessage()}");

                // Returner respons med feilmelding
                return response()->json(['error' => __('auth.error_while_sending_code')], 500);
            }
        }elseif ($validated['channel'] === 'email') {
            Log::info("Sending verification code {$code} to {$validated['contact']} via email.");
            try {
                // Send verifikasjonskoden via e-post
                Mail::raw("Din verifikasjonskode er: {$code}", function ($message) use ($validated) {
                    $message->to($validated['contact'])
                        ->subject('Din verifikasjonskode');
                });

                // Loggfør suksess
                Log::info("Verification code {$code} sent successfully to {$validated['contact']} via email.");
            } catch (\Exception $e) {
                // Loggfør feil
                Log::error("Failed to send verification code to {$validated['contact']}. Error: {$e->getMessage()}");

                // Return feilmelding
                return response()->json(['error' => __('auth.error_while_sending_code')], 500);
            }
        }



        // Returner generert kode som suksessrespons (unngå direkte koden i produksjon)
        return response()->json([
            'success' => true,
            'message' => __('auth.code_sent'),
            'code' => $code // Dette bør bare returneres i `APP_DEBUG === true`
        ]);
    }

}
