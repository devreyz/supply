<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;

class SocialController extends Controller
{
    /** Redirect to Google */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /** Handle callback from Google */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            Log::info('Google callback', ['email' => $googleUser->getEmail()]);
            Log::info('Google user data', ['user' => $googleUser]);
            // Find or create user
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->name,
                    'email_verified_at' => now(),
                    'password' => Hash::make(bin2hex(random_bytes(16))),
                    'avatar' => $googleUser->avatar,
                    'googleid' => $googleUser->id,
                ]
            );

            Auth::login($user, true);

            return redirect()->intended(route('app'));

        } catch (\Exception $e) {
            Log::error('Google login failed', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Falha ao autenticar com Google.');
        }
    }
}
