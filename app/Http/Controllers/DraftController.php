<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Controller;
use App\Models\Mom;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DraftController extends Controller
{
    public function index(Request $request)
    {
        // Memastikan user telah login
        $userId = auth()->id();

        if (!$userId) {
            // Tangani kasus jika user belum login (redirect atau tampilkan error)
            return redirect('/login')->withErrors('Anda harus login untuk melihat draf.');
        }

        // Get filter parameters
        $search = $request->input('search');
        $month = $request->input('month'); // Format: YYYY-MM
        $status = $request->input('status'); // Format: Menunggu, Ditolak, Disetujui

        // Query untuk My MoMs (Menunggu dan Ditolak)
        $myMomsQuery = Mom::where('creator_id', $userId)
            ->whereHas('status', function (Builder $query) {
                $query->whereIn('status', ['Menunggu', 'Ditolak']);
            })
            ->with(['creator', 'status', 'attachments']);

        // Apply search filter
        if ($search) {
            $myMomsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%');
            });
        }

        // Apply month filter untuk My MoMs
        if ($month) {
            $currentYear = Carbon::now()->year;
            $myMomsQuery->whereMonth('created_at', $month)
                        ->whereYear('created_at', $currentYear);
        }

        // Apply status filter untuk My MoMs
        if ($status && in_array($status, ['Menunggu', 'Ditolak', 'Disetujui'])) {
            $myMomsQuery->whereHas('status', function (Builder $query) use ($status) {
                $query->where('status', $status);
            });
        }

        $myMoms = $myMomsQuery->latest('created_at')->paginate(9)->withQueryString();

        // Query untuk All MoMs (Disetujui only)
        $allMomsQuery = Mom::whereHas('status', function (Builder $query) {
                $query->where('status', 'Disetujui');
            })
            ->with(['creator', 'status', 'attachments']);

            // Apply search filter
        if ($search) {
            $allMomsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%');
            });
        }

        // Apply month filter untuk All MoMs
        if ($month) {
            $currentYear = Carbon::now()->year;
            $allMomsQuery->whereMonth('created_at', $month)
                        ->whereYear('created_at', $currentYear);
        }

        $allMoms = $allMomsQuery->latest('created_at')->paginate(9)->withQueryString();

        return view('user.draft', compact('myMoms', 'allMoms', 'month', 'status'));
    }
}
