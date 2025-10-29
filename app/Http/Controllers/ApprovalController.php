<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\NotificationController; 

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
     */
    public function approve(Mom $mom)
    {
    // Gunakan DB Transaction untuk keamanan
    DB::beginTransaction();

        try {
            // Logika utama: Update status MoM
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

            // Mengatur Flash Session
            Session::flash('success', "MoM '{$mom->title}' berhasil **disetujui**!");

            // MENGARAHKAN PENGGUNA KEMBALI KE HALAMAN SEBELUMNYA
            return redirect()->back(); 

        } catch (\Throwable $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Approval Failed: " . $e->getMessage());

            Session::flash('error', 'Gagal menyetujui MoM: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Menolak MoM: Mengubah status_id menjadi 3 (Ditolak) dan menyimpan komentar.
     */
    public function reject(Request $request, Mom $mom)
    {
        $comment = $request->comment;

        // Basic validation: Pastikan komentar ada
        if (empty($comment)) {
            Session::flash('error', 'Komentar penolakan wajib diisi.');
            return redirect()->back();
    }

        DB::beginTransaction();

        try {
            // Update status dan simpan komentar
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

            // Mengatur Flash Session
            Session::flash('warning', "MoM '{$mom->title}' berhasil **ditolak**. Notifikasi revisi sudah dikirim.");

            // MENGARAHKAN PENGGUNA KEMBALI KE HALAMAN SEBELUMNYA
            return redirect()->back(); 

        } catch (\Throwable $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Rejection Failed: " . $e->getMessage());

            Session::flash('error', 'Gagal menolak MoM: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}