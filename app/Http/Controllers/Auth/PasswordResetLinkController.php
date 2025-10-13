<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    // Tampilkan form minta email
    public function create()
    {
        return view('auth.forgot'); // kita buat view di langkah 4
    }

    // Kirim email reset link
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
        ]);

        // kirim link reset; akan buat token di tabel password_reset_tokens
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
