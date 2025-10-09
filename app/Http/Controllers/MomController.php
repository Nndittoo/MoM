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
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Throwable;

class MomController extends Controller
{
    /**
     * Menampilkan form untuk membuat MoM baru (untuk Role User biasa).
     */
    public function create()
    {
        $users = User::all();
        return view('user/create', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat MoM baru (untuk Role Admin).
     */
    public function createAdmin()
    {
        $users = User::all(); 
        return view('admin/create', compact('users'));
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

            // 2. LOGIKA ADMIN (Jika form Admin mengirim is_admin_submission=1)
            if ($request->has('is_admin_submission') && $request->is_admin_submission == '1') {
                try {
                    $approvedStatus = MomStatus::where('status', 'Disetujui')->firstOrFail();
                    $statusToUse = $approvedStatus;
                    $statusMessage = 'Disetujui';
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
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
            // NotificationController::createNotification(...);

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

    /**
     * Menampilkan form edit MoM dan mengirim data lama (Untuk Role User).
     */
    public function edit(Mom $mom)
    {
        $users = User::all();
        $mom->load(['agendas', 'attachments']); 
        return view('user.edit', compact('mom', 'users'));
    }

    /**
     * Menampilkan form edit MoM dan mengirim data lama (Untuk Role Admin).
     */
    public function editAdmin(Mom $mom)
    {
        $users = User::all();
        $mom->load(['agendas', 'attachments']); 
        return view('admin.edit', compact('mom', 'users')); // Ganti view ke admin.edit
    }


    /**
     * Memproses update MoM (dipanggil melalui AJAX POST/PATCH).
     */
    public function update(Request $request, Mom $mom)
    {
        DB::beginTransaction();

        try {
            // Menentukan Role dan Status Baru
            $isAdmin = Auth::check() && Auth::user()->role === 'admin';
            
            if ($isAdmin) {
                // Jika Admin, gunakan status_id yang dikirim dari form (diharapkan 2 / Disetujui)
                $newStatusId = $request->input('status_id', 2);
                $statusMessage = 'disetujui';
                
                // Hapus komentar penolakan jika MoM diperbarui oleh Admin
                $mom->rejection_comment = null; 

            } else {
                // Jika User biasa, MoM yang di-edit harus kembali ke status 'Menunggu' (1)
                $newStatusId = 1; 
                $statusMessage = 'dikirim ulang untuk persetujuan';
            }

            // PROSES PENGHAPUSAN FILE LAMA
            if ($request->has('files_to_delete')) {
                $idsToDelete = is_array($request->input('files_to_delete')) ? $request->input('files_to_delete') : [$request->input('files_to_delete')];
                
                foreach ($idsToDelete as $attachmentId) {
                    $attachment = MomAttachment::find($attachmentId);
                    
                    if ($attachment && $attachment->mom_id === $mom->version_id) {
                        Storage::disk('public')->delete($attachment->file_path);
                        $attachment->delete();
                    }
                }
            }
            
            // PROSES UPDATE DATA UTAMA MoM
            $mom->update([
                'title' => $request->title,
                'location' => $request->location,
                'pimpinan_rapat' => $request->pimpinan_rapat,
                'notulen' => $request->notulen,
                'meeting_date' => $request->meeting_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'pembahasan' => $request->pembahasan,
                
                // Terapkan status ID yang sudah ditentukan berdasarkan role
                'status_id' => $newStatusId, 

                // Data JSON/Array
                'nama_peserta' => $request->input('attendees_manual'), 
                'nama_mitra' => json_decode($request->input('partner_attendees_json'), true),
            ]);

            // PROSES UPDATE AGENDA (Hapus lama, tambahkan baru)
            MomAgenda::where('mom_id', $mom->version_id)->delete();
            
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
            
            // PROSES PENAMBAHAN FILE BARU
            if ($request->hasFile('attachments')) {
                $attachmentsData = [];
                $disk = 'public';

                $uploaderId = Auth::id(); // Ambil ID pengunggah saat ini

                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('attachments', $fileName, $disk);

                    $attachmentsData[] = [
                        'mom_id' => $mom->version_id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploader_id' => $uploaderId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                MomAttachment::insert($attachmentsData);
            }

            DB::commit();

            return response()->json([
                'message' => 'MoM berhasil diupdate dan ' . $statusMessage . '!', 
                'mom_id' => $mom->version_id
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("MOM Update Failed: " . $e->getMessage() . " on file " . $e->getFile() . " line " . $e->getLine());

            return response()->json([
                'message' => 'Gagal mengupdate Minutes of Meeting.', 
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    public function export(Mom $mom)
    {
        $mom->load(['creator', 'agendas', 'actionItems', 'attachments']);
        return view('user/export', compact('mom'));
    }

    public function repository()
    {
        $adminRole = 'admin'; 
        
        $momsByAdmin = Mom::whereHas('creator', function ($query) use ($adminRole) {
            $query->where('role', $adminRole);
        })
        ->with(['creator', 'status', 'agendas', 'attachments']) 
        ->orderByDesc('meeting_date')
        ->get();

        $allMoms = Mom::whereIn('status_id', [1, 2, 3])
            ->with(['creator', 'status', 'agendas', 'attachments'])
            ->orderByDesc('meeting_date')
            ->get();

        return view('admin.mom', compact('momsByAdmin', 'allMoms'));
    }

    
}