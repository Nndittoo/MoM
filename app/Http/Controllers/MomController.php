<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreMomRequest;
use App\Models\Mom;
use App\Models\ActionItem;
use App\Models\MomStatus;
use App\Models\MomAgenda;
use App\Models\MomAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MomController extends Controller
{
    public function store(StoreMomRequest $request)
    {
        // Pastikan user terautentikasi, creator_id diambil dari user login
        $creatorId = auth()->id();
        
        if (!$creatorId) {
            return response()->json(['message' => 'Unauthorized. User must be logged in.'], 401);
        }

        DB::beginTransaction();
        
        try {
            // 1. Dapatkan status default 'Draft'
            $defaultStatus = MomStatus::where('status', 'Menunggu')->firstOrFail();
            
            // 2. Buat MoM utama
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

            // 3. Simpan Action Items (Tindak Lanjut)
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
            
            // 4. Simpan Agenda
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

            // 5. Sinkronkan Peserta Rapat (Attendees)
            // Menggunakan sync untuk relasi Many-to-Many
            $mom->attendees()->sync($request->attendees);


            // 6. Handle Lampiran (File Upload)
            if ($request->hasFile('attachments')) {
                $attachmentsData = [];
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    // Simpan file di storage/app/public/attachments
                    $filePath = $file->storeAs('public/attachments', $fileName); 
                    
                    $attachmentsData[] = [
                        'mom_id' => $mom->version_id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => str_replace('public/', '', $filePath), // Path relatif untuk database
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploader_id' => $creatorId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                MomAttachment::insert($attachmentsData);
                // NOTE: Jangan lupa jalankan php artisan storage:link
            }

            // 7. Commit transaksi
            DB::commit();

            return response()->json([
                'message' => 'Minutes of Meeting berhasil dibuat!',
                'mom_id' => $mom->version_id,
                'mom' => $mom->load(['actionItems', 'attendees', 'attachments', 'agendas'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal membuat Minutes of Meeting.',
                'error_detail' => $e->getMessage(),
            // ⭐️ Tambahkan code status juga jika perlu
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);

        
        }
    }
}