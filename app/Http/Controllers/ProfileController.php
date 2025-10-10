<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
    $user = $request->user();

    if ($user->role === 'admin') {
        return view('admin.profile.edit', compact('user'));
    }

    return view('user.profile.edit', compact('user'));
}

    public function updatePhoto(Request $request)
    {
    $request->validate([
        'avatar' => ['required','image','mimes:jpg,jpeg,png,webp','max:2048'],
    ]);

    $user = $request->user();

    // Hapus avatar lama jika ada
    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
        Storage::disk('public')->delete($user->avatar);
    }

    // Simpan avatar baru
    $path = $request->file('avatar')->store('avatars', 'public');
    $user->update(['avatar' => $path]);

    // 🔄 Refresh user di session (tanpa perlu logout/login ulang)
    Auth::setUser($user->fresh());

    return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function deletePhoto(Request $request)
    {
        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return back()->with('success', 'Foto profil dihapus.');
    }


}
