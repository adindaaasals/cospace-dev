<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;  // Pastikan Log di-import

class ProviderController extends Controller
{
    public function redirect(string $provider)
    {
        Log::info('Redirecting to ' . $provider . ' authentication.');
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        try {
            // Mendapatkan data user dari provider
            $providerUser = Socialite::driver($provider)->user();

            // Log email dan ID pengguna
            Log::info('Google User Email: ' . $providerUser->getEmail());
            Log::info('Google User ID: ' . $providerUser->getId());

            // Mencari pengguna berdasarkan email
            $user = User::where('email', $providerUser->getEmail())->first();

            if (!$user) {
                // Jika pengguna belum ada, buat pengguna baru
                Log::info('User not found, creating new user.');

                $user = User::create([
                    'name' => $providerUser->getName(),
                    'email' => $providerUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $providerUser->getId(),
                    'provider_token' => $providerUser->token,
                ]);

                Log::info('New user created: ' . $user->id);
            } else {
                // Jika pengguna sudah ada, update data pengguna
                Log::info('User found, updating user data.');

                $user->update([
                    'provider' => $provider,
                    'provider_id' => $providerUser->getId(),
                    'provider_token' => $providerUser->token,
                ]);

                Log::info('User updated: ' . $user->id);
            }

            // Melakukan login otomatis setelah pembuatan atau pembaruan pengguna
            Auth::login($user, true);
            Log::info('User logged in successfully: ' . $user->id);

            return redirect('/')->with('success', 'Successfully authenticated with ' . $provider);
        } catch (\Exception $e) {
            // Menangkap dan mencatat error
            Log::error('Authentication failed with ' . $provider . ': ' . $e->getMessage());

            return redirect()->route('auth.login.view')->with('error', 'Unable to authenticate with ' . $provider);
        }
    }
}
