<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mom;
use App\Models\AdminNotification;
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
     * Redirect langsung ke halaman detail dengan status disetujui.
     */
    public function approve(Mom $mom)
    {
        DB::beginTransaction();

        try {
            // Update status ke Disetujui
            $mom->update([
                'status_id' => 2,
                'rejection_comment' => null // Hapus komentar penolakan jika ada
            ]);

            // === NOTIFICATION: MoM Disetujui ===
            NotificationController::createNotification(
                userId: $mom->creator_id,
                momId: $mom->version_id,
                type: 'mom_approved',
                title: 'MoM Disetujui',
                message: "MoM '{$mom->title}' telah disetujui dan siap digunakan."
            );

            DB::commit();

            // Redirect langsung ke halaman detail
            return redirect()
                ->route('admin.details', $mom->version_id)
                ->with('success', "MoM '{$mom->title}' berhasil disetujui!");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Approval Failed: " . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Gagal menyetujui MoM: ' . $e->getMessage());
        }
    }

    /**
     * Menolak MoM: Mengubah status_id menjadi 3 (Ditolak) dan menyimpan komentar.
     * Mengembalikan JSON untuk diproses oleh AJAX frontend.
     */
    public function reject(Request $request, Mom $mom)
    {
        // Validasi input
        $request->validate([
            'comment' => 'required|string|max:500'
        ]);

        $comment = $request->comment;
        $redirectTo = $request->redirect_to; // Tangkap URL tujuan dari frontend

        DB::beginTransaction();

        try {
            // Update status ke Ditolak
            $mom->update([
                'status_id' => 3,
                'rejection_comment' => $comment,
            ]);

            // === NOTIFICATION: MoM Ditolak ===
            NotificationController::createNotification(
                userId: $mom->creator_id,
                momId: $mom->version_id,
                type: 'mom_rejected',
                title: 'MoM Ditolak',
                message: "MoM '{$mom->title}' ditolak. Alasan: {$comment}. Silakan edit dan submit ulang."
            );

            DB::commit();

            // Tentukan URL redirect (prioritaskan dari request, fallback ke route)
            $finalRedirectUrl = $redirectTo ?? route('admin.details', $mom->version_id);

            // MENGEMBALIKAN RESPON JSON SUKSES untuk AJAX
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "MoM '{$mom->title}' berhasil ditolak. Notifikasi revisi sudah dikirim.",
                    'redirect_url' => $finalRedirectUrl
                ], 200);
            }

            // Fallback redirect biasa (jika bukan AJAX)
            return redirect($finalRedirectUrl)
                ->with('success', "MoM '{$mom->title}' berhasil ditolak.");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Rejection Failed: " . $e->getMessage());

            // MENGEMBALIKAN RESPON JSON ERROR
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menolak MoM: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Gagal menolak MoM: ' . $e->getMessage());
        }
    }
}
