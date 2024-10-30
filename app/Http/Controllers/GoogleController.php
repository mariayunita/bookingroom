<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->scopes(['https://www.googleapis.com/auth/calendar'])->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            // Ambil data user dari Google
            $googleUser = Socialite::driver('google')->stateless()->user();
            // Simpan token akses ke sesi atau ke database
            session(['google_access_token' => $googleUser->token]);
            // Cari user berdasarkan email dari Google
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Jika user sudah ada di database, login otomatis
                Auth::login($user);
            } else {
                // Jika user belum ada, buat user baru
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt('password'), // Set password default (tidak akan digunakan)
                    'role' => 'user', // Atur default role sebagai 'user'
                ]);

                // Login otomatis user baru
                Auth::login($newUser);
            }

            // Redirect ke halaman home setelah login berhasil
            return redirect()->route('home');

        } catch (\Exception $e) {
            // Handle jika terjadi error
            return redirect('/login')->with('error', 'Something went wrong while logging in with Google.');
        }
    }
}
