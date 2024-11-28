<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class KeyController extends Controller
{
    public function getEncryptionKey()
    {
        // Generer en unik nøkkel for denne sesjonen
        $tempKey = Str::random(32);
        
        // Krypter den med Laravel's innebygde kryptering (bruker APP_KEY)
        $encryptedKey = Crypt::encryptString($tempKey);
        
        // Lagre den krypterte nøkkelen i session
        session(['temp_key' => $encryptedKey]);
        
        // Send den ukrypterte midlertidige nøkkelen til frontend
        return response()->json(['key' => $tempKey]);
    }
}
