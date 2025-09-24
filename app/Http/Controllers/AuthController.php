<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('login', [
            'title' => 'Login',
            'active' => 'login'
        ]);
    }

    public function authentication(Request $request)
    {
        // Validasi input
        $credential = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credential)) {
            session(['previous_url' => url()->previous()]);
            $request->session()->regenerate();
            $data = ['status' => 'success'];
            return json_encode($data, 400);
        } else {
            // Auth gagal
            $data = ['status' => 'failed'];
            return json_encode($data, 200);
        }
    }

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect('/');
    }

    public function generateToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate a new token
        $token = Str::random(60);

        // Save token to user
        $user->api_token = hash('sha256', $token);
        $user->save();

        return response()->json(['access_key' => $token]);
    }
}
