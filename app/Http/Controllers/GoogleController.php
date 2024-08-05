<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('error')) {
            // Handle the error appropriately
            return redirect()->route('login')->with('error', 'Login via Google was cancelled.');
        }
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to login via Google.');
        }

        // Find the user by google_id or email
        $findUser = User::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

        // Use updateOrCreate to update existing user or create a new one
        $user = User::updateOrCreate(
            ['email' => $googleUser->email], // Search criteria
            [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                // Only set userType if creating a new user
                'userType' => $findUser ? $findUser->userType : 'student',
                // Only set password if creating a new user
                'password' => $findUser ? $findUser->password : encrypt('12345678'),
            ]
        );

        // Log the user in
        Auth::login($user);
        return redirect()->intended('dashboard');
    }
}
