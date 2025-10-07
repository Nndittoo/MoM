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
     * Menampilkan form untuk membuat MoM baru.
     */
    public function create()
    {
        // Mengambil semua user dari database
        $users = User::all(); 

        // Mengirimkan variabel $users ke view.
        return view('user/create', compact('users')); 
    }
    
    public function store(StoreMomRequest $request)
    {
        $creatorId = auth()->id();
        
        if (!$creatorId) {
            return response()->json(['message' => 'Unauthorized. User must be logged in.'], 401);
        }

        DB::beginTransaction();
        
        try {
            // Status default MoM adalah 'Menunggu'
            $defaultStatus = MomStatus::where('status', 'Menunggu')->firstOrFail();
            
            // Membuat MoM utama
            $mom = Mom::create([
                'title' => $request->title,
                'meeting_date' => $request->meeting_date, 
                'location' => $request->location,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'leader_id' => $request->leader_id, 
                'notulen_id' => $request->notulen_id,
                'creator_id' => $creatorId, 
                'pembahasan' => $request->pembahasan,
                'status_id' => $defaultStatus->status_id,
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

            // Sinkron Peserta Rapat (Attendees)
            $mom->attendees()->sync($request->attendees);


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
                        // Menggunakan disk 'public' secara eksplisit
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

            // Commit transaksi
            DB::commit();

            return response()->json([
                'message' => 'Minutes of Meeting berhasil dibuat!',
                'mom_id' => $mom->version_id,
                'mom' => $mom->load(['actionItems', 'attendees', 'attachments', 'agendas'])
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
    // Memuat semua relasi yang diperlukan untuk halaman detail
    $mom->load(['leader', 'notulen', 'attendees', 'agendas', 'attachments']);
    
    // Memanggil view: resources/views/user/show.blade.php
    return view('user/show', compact('mom')); 
    }

    public function edit(Mom $mom)
    {
        $users = User::all();
        return view('moms.edit', compact('mom', 'users')); 
    }

    public function export(Mom $mom)
    {
        // Memuat semua relasi yang diperlukan untuk dokumen export
        $mom->load(['leader', 'notulen', 'attendees', 'agendas', 'actionItems', 'attachments']);

        // Mengirim data ke view export.blade.php
        return view('user/export', compact('mom'));
    }
}