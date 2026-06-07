<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function loginForm()
    {
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectUserByRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form.
     */
    public function registerForm()
    {
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }
        return view('auth.register');
    }

    /**
     * Handle registration request (User/Nasabah only).
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ]);

        $userRole = Role::where('name', 'user')->first();

        if (!$userRole) {
            return back()->with('error', 'Role Nasabah (user) tidak ditemukan di database.');
        }

        DB::transaction(function () use ($request, $userRole) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $userRole->id,
            ]);

            // Generate unique QR Code
            do {
                $qrCode = 'TK-' . strtoupper(Str::random(8));
            } while (UserProfile::where('qr_code', $qrCode)->exists());

            UserProfile::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'qr_code' => $qrCode,
                'points_balance' => 0,
            ]);

            Auth::login($user);
        });

        return redirect()->route('user.dashboard')->with('success', 'Registrasi berhasil! Selamat datang di Tuker.in.');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Helper to redirect users based on their role.
     */
    private function redirectUserByRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isEmployee()) {
            return redirect()->route('employee.dashboard');
        } elseif ($user->isUser()) {
            return redirect()->route('user.dashboard');
        }

        Auth::logout();
        return redirect()->route('login')->with('error', 'Role tidak valid.');
    }
}
