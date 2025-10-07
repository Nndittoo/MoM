<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mom;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        
        $pendingMoms = Mom::where('status_id', 1) 
                             ->with('creator') 
                             ->latest() 
                             ->get();

        return view('admin.approvals', [
            'pendingMoms' => $pendingMoms,
        ]);
    }

    // Aksi Approve
    public function approve(Mom $mom)
    {
        
        $mom->update(['status_id' => 2]); 
        
        // Logika untuk masuk ke All MoM akan otomatis berjalan 
        // karena DraftController mengkueri status 'Disetujui'
        return response()->json(['message' => 'MoM berhasil disetujui.']);
    }

    // Aksi Reject
    public function reject(Request $request, Mom $mom)
    {
        // Mengubah Status ID menjadi 3 (Ditolak)
        $mom->update([
            'status_id' => 3, 
            'rejection_comment' => $request->comment,
        ]);
        
        // MoM ini akan otomatis masuk ke 'Ditolak' di DraftController
        return response()->json(['message' => 'MoM berhasil ditolak dan dikembalikan ke creator untuk direvisi.']);
    }
}
