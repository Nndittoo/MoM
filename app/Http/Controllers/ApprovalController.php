<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\NotificationController; 
use Illuminate\Support\Facades\Log;

class ApprovalController extends Controller
{
    // Menampilkan daftar MoM yang menunggu persetujuan (status_id = 1).
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

    /**
     * Menyetujui MoM: Mengubah status_id menjadi 2 (Disetujui).
     * Catatan: Approve tetap menggunakan redirect()->back() karena frontend submit form biasa.
     */
    public function approve(Mom $mom)
    {
        DB::beginTransaction();

        try {
            $mom->update(['status_id' => 2]);

            // === NOTIFICATION: MoM Disetujui ===
            NotificationController::createNotification(
                userId: $mom->creator_id,
                momId: $mom->version_id,
                type: 'approved',
                title: 'MoM Disetujui',
                message: "MoM '{$mom->title}' telah disetujui dan siap digunakan."
            );

            DB::commit();

            Session::flash('success', "MoM '{$mom->title}' berhasil **disetujui**!");
            return redirect()->back(); 

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Approval Failed: " . $e->getMessage());

            Session::flash('error', 'Gagal menyetujui MoM: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Menolak MoM: Mengubah status_id menjadi 3 (Ditolak) dan menyimpan komentar.
     * Mengembalikan JSON untuk diproses oleh AJAX frontend.
     */
    public function reject(Request $request, Mom $mom)
    {
        $comment = $request->comment;
        $redirectTo = $request->redirect_to; // Tangkap URL tujuan

        if (empty($comment)) {
            // Jika validasi gagal, kembalikan JSON error
            return response()->json(['error' => 'Komentar penolakan wajib diisi.'], 422);
        }

        DB::beginTransaction();

        try {
            $mom->update([
                'status_id' => 3,
                'rejection_comment' => $comment,
            ]);

            // === NOTIFICATION: MoM Ditolak ===
            NotificationController::createNotification(
                userId: $mom->creator_id,
                momId: $mom->version_id,
                type: 'rejected',
                title: 'MoM Ditolak',
                message: "MoM '{$mom->title}' ditolak. Alasan: {$comment}. Silakan edit dan submit ulang."
            );

            DB::commit();

            // MENGEMBALIKAN RESPON JSON SUKSES
            return response()->json([
                'success' => true,
                'message' => "MoM '{$mom->title}' berhasil ditolak. Notifikasi revisi sudah dikirim.",
                'redirect_url' => $redirectTo // Kirim URL tujuan redirect ke JS
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Rejection Failed: " . $e->getMessage());

            // MENGEMBALIKAN RESPON JSON ERROR
            return response()->json([
                'error' => 'Gagal menolak MoM: ' . $e->getMessage()
            ], 500);
        }
    }
}