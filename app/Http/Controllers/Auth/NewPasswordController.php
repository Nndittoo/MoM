<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    // token & email dari URL akan diteruskan ke view
    public function create(string $token)
    {
        return view('auth.reset', [
            'token' => $token,
            'email' => request('email')
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'token'    => ['required'],
        'email'    => ['required', 'email'],
        'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)],
    ]);

    $status = \Illuminate\Support\Facades\Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user) use ($request) {
            $user->forceFill([
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ])->save();
        }
    );

    if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
        // Setelah reset sukses, arahkan ke login dengan notifikasi
        return redirect()->route('login')->with('success', 'Password Anda telah berhasil diperbarui! Silakan login dengan password baru.');
    }

    return back()->withErrors(['email' => __($status)]);
}
}
