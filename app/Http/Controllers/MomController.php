<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreMomRequest;
use App\Models\Mom;
use App\Models\ActionItem;
use App\Models\MomStatus;
use App\Models\MomAgenda;
use App\Models\MomAttachment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Throwable;

class MomController extends Controller
{
    /**
     * Menampilkan form untuk membuat MoM baru (untuk Role User biasa).
     */
    public function create()
    {
        // Variabel $users tetap dikirimkan (untuk creator_id)
        $users = User::all();
        return view('user/create', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat MoM baru (untuk Role Admin).
     * Memuat view yang memiliki hidden input is_admin_submission.
     */
    public function createAdmin()
    {
        $users = User::all(); 
        return view('admin/create', compact('users')); // <-- Memuat view Admin yang baru
    }
    
    public function store(StoreMomRequest $request)
    {
        $creatorId = auth()->id();

        if (!$creatorId) {
            return response()->json(['message' => 'Unauthorized. User must be logged in.'], 401);
        }

        DB::beginTransaction();

        try {
            // 1. Tentukan status default (Menunggu)
            $defaultStatus = MomStatus::where('status', 'Menunggu')->firstOrFail();
            $statusToUse = $defaultStatus;
            $statusMessage = 'Menunggu';

            // 2. LOGIKA ADMIN: Cek apakah ini submission dari Admin (menggunakan hidden input)
            if ($request->has('is_admin_submission') && $request->is_admin_submission == '1') {
                try {
                    // Cari status 'Disetujui'
                    $approvedStatus = MomStatus::where('status', 'Disetujui')->firstOrFail();
                    $statusToUse = $approvedStatus;
                    $statusMessage = 'Disetujui';
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    // Jika status 'Disetujui' tidak ditemukan, log warning dan gunakan status default
                    Log::warning("Status 'Disetujui' tidak ditemukan di tabel MomStatus. Menggunakan status default 'Menunggu'.");
                }
            }
            
            $partnerAttendees = $request->partner_attendees_json ? json_decode($request->partner_attendees_json, true) : [];
            $manualAttendees = $request->attendees_manual ?? [];

            // Membuat MoM utama
            $mom = Mom::create([
                'title' => $request->title,
                'meeting_date' => $request->meeting_date,
                'location' => $request->location,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,

                'pimpinan_rapat' => $request->pimpinan_rapat,
                'notulen' => $request->notulen,

                'creator_id' => $creatorId,
                'pembahasan' => $request->pembahasan,
                // MENGGUNAKAN STATUS YANG SUDAH DITENTUKAN OLEH LOGIKA ADMIN DI ATAS
                'status_id' => $statusToUse->status_id, 
                
                'nama_peserta' => $manualAttendees,
                'nama_mitra' => $partnerAttendees,
            ]);

            // Menyimpan Action Items (Tindak Lanjut)
            if ($request->filled('action_items')) {
                $actionItemsData = collect($request->action_items)->map(fn ($item) => [
                    'mom_id' => $mom->version_id,
                    'item' => $item['item'],
                    'due' => $item['due'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all();
                ActionItem::insert($actionItemsData);
            }

            // Menyimpan Agenda
            if ($request->filled('agendas')) {
                $agendasData = collect($request->agendas)->map(fn ($item, $index) => [
                    'mom_id' => $mom->version_id,
                    'item' => $item,
                    'order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all();
                MomAgenda::insert($agendasData);
            }

            // Handle Lampiran (File Upload)
            if ($request->hasFile('attachments')) {
                $attachmentsData = [];
                $disk = 'public';

                foreach ($request->file('attachments') as $file) {

                    if (!$file->isValid()) {
                        throw new \Exception("File '{$file->getClientOriginalName()}' tidak valid.", 422);
                    }

                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $filePath = null;

                    try {
                        $filePath = $file->storeAs('attachments', $fileName, $disk);

                    } catch (\Throwable $e) {
                        Log::error("File Upload Failed for MOM {$mom->version_id}: " . $e->getMessage());
                        throw new \Exception("Gagal menyimpan file {$file->getClientOriginalName()}. Kemungkinan masalah Izin Disk.", 500);
                    }

                    if (!$filePath) {
                          throw new \Exception("Penyimpanan file mengembalikan nilai kosong.");
                    }

                    $attachmentsData[] = [
                        'mom_id' => $mom->version_id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploader_id' => $creatorId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                MomAttachment::insert($attachmentsData);
            }


            // === NOTIFICATION: MoM Berhasil Dibuat ===
            NotificationController::createNotification(
                userId: $creatorId,
                momId: $mom->version_id,
                type: 'created',
                title: 'MoM Berhasil Dibuat',
                message: "MoM '{$mom->title}' telah berhasil dibuat dan menunggu approval."
            );

            // Commit transaksi
            DB::commit();

            return response()->json([
                'message' => 'Minutes of Meeting berhasil dibuat dan berstatus ' . $statusMessage . '!',
                'mom_id' => $mom->version_id,
                'mom' => $mom->load(['attachments', 'agendas'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("MOM Creation Failed: " . $e->getMessage() . " on file " . $e->getFile() . " line " . $e->getLine());

            return response()->json([
                'message' => 'Gagal membuat Minutes of Meeting.',
                'error_detail' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function show(Mom $mom)
    {
        $mom->load(['creator', 'agendas', 'attachments']);
        return view('user/show', compact('mom'));
    }

    public function show_admin(Mom $mom)
    {
        $mom->load(['creator', 'agendas', 'attachments']);
        return view('admin/details', compact('mom'));
    }

    public function edit(Mom $mom)
    {
        $users = User::all();
        return view('moms.edit', compact('mom', 'users'));
    }

    public function export(Mom $mom)
    {
        $mom->load(['creator', 'agendas', 'actionItems', 'attachments']);
        return view('user/export', compact('mom'));
    }
}
