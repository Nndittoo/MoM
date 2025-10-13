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
        $month = $request->input('month'); // Format: YYYY-MM
        $status = $request->input('status'); // Format: Menunggu, Ditolak, Disetujui

        // Query untuk My MoMs (Menunggu dan Ditolak)
        $myMomsQuery = Mom::where('creator_id', $userId)
            ->whereHas('status', function (Builder $query) {
                $query->whereIn('status', ['Menunggu', 'Ditolak']);
            })
            ->with(['creator', 'status']);

        // Apply month filter untuk My MoMs
        if ($month) {
            $currentYear = Carbon::now()->year;
            $startDate = Carbon::createFromFormat('Y-m', $currentYear . '-' . $month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $currentYear . '-' . $month)->endOfMonth();
            $myMomsQuery->whereBetween('updated_at', [$startDate, $endDate]);
        }

        // Apply status filter untuk My MoMs (hanya jika ada filter status yang dipilih)
        if ($status && in_array($status, ['Menunggu', 'Ditolak', 'Disetujui'])) {
            $myMomsQuery->whereHas('status', function (Builder $query) use ($status) {
                $query->where('status', $status);
            });
        }

        $myMoms = $myMomsQuery->latest()->paginate(9);

        // Query untuk All MoMs (Disetujui only)
        $allMomsQuery = Mom::whereHas('status', function (Builder $query) {
                $query->where('status', 'Disetujui');
            })
            ->with(['creator', 'status']);

        // Apply month filter untuk All MoMs
        if ($month) {
            $currentYear = Carbon::now()->year;
            $startDate = Carbon::createFromFormat('Y-m', $currentYear . '-' . $month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $currentYear . '-' . $month)->endOfMonth();
            $allMomsQuery->whereBetween('updated_at', [$startDate, $endDate]);
        }

        $allMoms = $allMomsQuery->latest()->paginate(9);

        return view('user.draft', compact('myMoms', 'allMoms', 'month', 'status'));
    }
}
