<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginVerification;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'numeric', 'min:11']
        ]);

        $user = User::where('phone', $request->phone)
            ->where('login_code_expires_at', '>', now()->subMinute())
            ->first();
        
        if($user)
        {
            return response()->json(['message' => 'Please wait before requesting another code'], 429);
        }
        
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        $user->notify(new LoginVerification());

        return response()->json(['message' => 'Text message notification sent']);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'numeric', 'min:10'],
            'login_code' => ['required', 'numeric', 'between:111111,999999']
        ]);

        $user = User::where('phone', $request->phone)
            ->where('login_code', $request->login_code)
            ->where('login_code_expires_at', '>', now())
            ->first();

        if ($user) {
            $user->update([
                'login_code' => null,
                'login_code_expires_at' => null,
            ]);

            return response()->json([
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user' => $user
            ]);
        }

        return response()->json(['message' => 'Invalid or expired verification code'], 401);
    }
}
