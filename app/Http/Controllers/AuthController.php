<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\RefreshToken;



class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return response()->json(['message' => 'User created'], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        $user = JWTAuth::user();
        $refreshToken = bin2hex(random_bytes(64));
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => $refreshToken,
            'expires_at' => Carbon::now()->addMinutes(config('jwt.refresh_ttl')),
        ]);

        return response()->json([
            'user'=>$user,
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ]);
    }

//    public function refresh(Request $request)
//    {
//        $refreshToken = $request->input('refresh_token');
//
//        $record = RefreshToken::where('token', $refreshToken)->first();
//
//        if (!$record) {
//            return response()->json(['error' => 'Invalid refresh token'], 401);
//        }
//
//        $user = User::find($record->user_id);
//
//        $newAccessToken = JWTAuth::fromUser($user);
//
//        return response()->json([
//            'access_token' => $newAccessToken,
//            'token_type' => 'bearer',
//            'expires_in' => JWTAuth::factory()->getTTL() * 60
//        ]);
//    }

    public function refresh(Request $request)
    {
        $refreshToken = $request->input('refresh_token');
        $record = RefreshToken::where('token', $refreshToken)->first();

        if (!$record) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }

        $user = User::find($record->user_id);

        // নতুন access token
        $newAccessToken = JWTAuth::fromUser($user);

        // নতুন refresh token ও জেনারেট করো (rotate করার জন্য)
        $newRefreshToken = bin2hex(random_bytes(64));
        $record->update([
            'token' => $newRefreshToken,
            'expires_at' => Carbon::now()->addMinutes(config('jwt.refresh_ttl')),
        ]);

        return response()->json([
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ]);
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        $user = JWTAuth::user();
        RefreshToken::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function profile()
    {
        return response()->json(JWTAuth::user());
    }
}
