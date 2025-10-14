<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GoogleCalendarController extends Controller
{
    protected $googleCalendar;

    public function __construct(GoogleCalendarService $googleCalendar)
    {
        $this->googleCalendar = $googleCalendar;
    }

    /**
     * Redirect ke Google OAuth
     */
    public function connect()
    {
        $state = base64_encode(json_encode([
            'type' => 'user',
            'user_id' => Auth::id(),
        ]));

        $authUrl = $this->googleCalendar->getAuthUrl($state);
        return redirect($authUrl);
    }

    /**
     * Callback dari Google OAuth
     */
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('user.calendar')
                ->with('error', 'Koneksi ke Google Calendar dibatalkan.');
        }

        // Validasi state
        $state = $request->get('state');
        if ($state) {
            $stateData = json_decode(base64_decode($state), true);

            // Jika ini request dari admin, redirect ke admin callback
            if (isset($stateData['type']) && $stateData['type'] === 'admin') {
                return redirect()->route('admin.google.calendar.callback', [
                    'code' => $request->get('code'),
                    'state' => $state
                ]);
            }
        }

        $code = $request->get('code');

        try {
            $token = $this->googleCalendar->authenticate($code);

            $user = Auth::user();
            $user->google_access_token = json_encode($token);

            if (isset($token['refresh_token'])) {
                $user->google_refresh_token = $token['refresh_token'];
            }

            $user->google_token_expires_at = Carbon::now()->addSeconds($token['expires_in']);
            $user->save();

            return redirect()->route('user.calendar')
                ->with('success', 'Berhasil terhubung dengan Google Calendar!');
        } catch (\Exception $e) {
            return redirect()->route('user.calendar')
                ->with('error', 'Gagal menghubungkan dengan Google Calendar: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect dari Google Calendar
     */
    public function disconnect()
    {
        $user = Auth::user();
        $user->google_access_token = null;
        $user->google_refresh_token = null;
        $user->google_token_expires_at = null;
        $user->save();

        return redirect()->route('user.calendar')
            ->with('success', 'Berhasil memutus koneksi dengan Google Calendar.');
    }

    /**
     * Sync semua action items ke Google Calendar
     */
    public function sync()
    {
        $user = Auth::user();

        if (!$user->google_access_token) {
            return redirect()->route('user.calendar')
                ->with('error', 'Silakan hubungkan akun Google Calendar terlebih dahulu.');
        }

        try {
            $synced = $this->googleCalendar->syncAllEvents($user);

            return redirect()->route('user.calendar')
                ->with('success', "Berhasil sinkronisasi {$synced} tugas ke Google Calendar!");
        } catch (\Exception $e) {
            return redirect()->route('user.calendar')
                ->with('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    /**
     * Check status koneksi Google Calendar
     */
    public function status()
    {
        $user = Auth::user();
        $connected = !empty($user->google_access_token);

        $data = [
            'connected' => $connected,
            'expires_at' => $user->google_token_expires_at ?
                $user->google_token_expires_at->diffForHumans() : null,
        ];

        return response()->json($data);
    }
}
