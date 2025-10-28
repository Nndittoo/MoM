<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'nullable|string'
        ]);

        $user = Auth::user();

        // prevent duplicate token
        DeviceToken::updateOrCreate(
            ['token' => $request->token],
            ['user_id' => $user->id, 'platform' => $request->platform ?? 'web']
        );

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $user = Auth::user();

        DeviceToken::where('user_id', $user->id)->where('token', $request->token)->delete();

        return response()->json(['ok' => true]);
    }
}
