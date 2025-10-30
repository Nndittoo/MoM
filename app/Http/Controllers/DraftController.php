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
            return redirect('/login')->withErrors('Anda harus login untuk melihat draf.');
        }

        // Get filter parameters
        $search = $request->input('search');
        $month = $request->input('month');
        $status = $request->input('status');
        $tab = $request->input('tab', 'my-mom'); // Default tab

        // ============================================
        // QUERY MY MOM: SEMUA STATUS
        // ============================================
        $myMomsQuery = Mom::where('creator_id', $userId)
            ->with(['creator', 'status', 'attachments']);

        // Apply search filter
        if ($search) {
            $myMomsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('pembahasan', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        // Apply month filter
        if ($month) {
            $currentYear = Carbon::now()->year;
            $myMomsQuery->whereMonth('meeting_date', $month)
                        ->whereYear('meeting_date', $currentYear);
        }

        // Apply status filter
        if ($status && in_array($status, ['Menunggu', 'Ditolak', 'Disetujui'])) {
            $myMomsQuery->whereHas('status', function (Builder $query) use ($status) {
                $query->where('status', $status);
            });
        }

        $myMoms = $myMomsQuery->latest('created_at')
            ->paginate(9, ['*'], 'my_mom_page')
            ->appends([
                'tab' => 'my-mom',
                'search' => $search,
                'month' => $month,
                'status' => $status
            ]);

        // ============================================
        // QUERY ALL MOM: HANYA DISETUJUI
        // ============================================
        $allMomsQuery = Mom::whereHas('status', function (Builder $query) {
                $query->where('status', 'Disetujui');
            })
            ->with(['creator', 'status', 'attachments']);

        // Apply search filter
        if ($search) {
            $allMomsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('pembahasan', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        // Apply month filter
        if ($month) {
            $currentYear = Carbon::now()->year;
            $allMomsQuery->whereMonth('meeting_date', $month)
                         ->whereYear('meeting_date', $currentYear);
        }

        $allMoms = $allMomsQuery->latest('created_at')
            ->paginate(9, ['*'], 'all_mom_page')
            ->appends([
                'tab' => 'all-mom',
                'search' => $search,
                'month' => $month
            ]);

        return view('user.draft', compact('myMoms', 'allMoms', 'month', 'status', 'tab'));
    }
}
