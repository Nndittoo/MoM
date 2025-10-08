<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Routing\Controller; 
use App\Models\Mom;
use App\Models\User; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DraftController extends Controller
{
    public function index()
    {
        // Memastikan user telah login
        $userId = auth()->id();

        if (!$userId) {
            // Tangani kasus jika user belum login (redirect atau tampilkan error)
            return redirect('/login')->withErrors('Anda harus login untuk melihat draf.');
        }
        
        // Ambil MoM yang dibuat oleh user ini dengan status "Menunggu" atau "Ditolak"
        $myMoms = Mom::where('creator_id', $userId)
            ->whereHas('status', function (Builder $query) { 
                // Status 'Menunggu' dan 'Ditolak' adalah status draft
                $query->whereIn('status', ['Menunggu', 'Ditolak']); 
            })
        
            ->with(['creator', 'status']) 
            ->latest() // Mengurutkan dari yang terbaru
            ->paginate(9); // Menggunakan pagination

        // Memisahkan antara draft (Menunggu) dan rejected
        $pendingMoms = $myMoms->filter(fn($mom) => $mom->status->status === 'Menunggu');
        $rejectedMoms = $myMoms->filter(fn($mom) => $mom->status->status === 'Ditolak');


        // Menyiapkan data untuk tab "All MoM"
        $allMoms = Mom::whereHas('status', function (Builder $query) { 
                $query->where('status', 'Disetujui');
            })
            
            ->with(['creator', 'status'])
            ->latest()
            ->get();


        return view('user.draft', compact('pendingMoms', 'rejectedMoms', 'allMoms', 'myMoms'));
    }
}