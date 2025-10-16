<?php

namespace App\Http\Controllers;

use App\Models\ActionItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActionItemController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'mom_id' => 'required|exists:moms,version_id',
                'item' => 'required|string|max:500',
                'due' => 'required|date_format:Y-m-d',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors(), 'message' => 'Validasi Gagal'], 422);
        }

        $actionItem = ActionItem::create([
            'mom_id' => $validated['mom_id'],
            'item' => $validated['item'],
            'due' => $validated['due'],
            'status' => 'mendatang',
        ]);

        return response()->json(['message' => 'Tugas berhasil ditambahkan.', 'action_item' => $actionItem], 201);
    }


    public function destroy(ActionItem $actionItem)
    {
        try {
            // Simpan info untuk response
            $itemTitle = $actionItem->item;

            // Delete akan trigger observer yang akan menghapus Google Calendar event
            $actionItem->delete();

            Log::info("Action item deleted successfully", [
                'action_id' => $actionItem->action_id,
                'item' => $itemTitle
            ]);

            return response()->json([
                'message' => 'Tindak lanjut berhasil dihapus!',
                'deleted_item' => $itemTitle
            ], 200);

        } catch (\Exception $e) {
            Log::error("Failed to delete action item: " . $e->getMessage(), [
                'action_id' => $actionItem->action_id ?? 'unknown'
            ]);

            return response()->json([
                'message' => 'Gagal menghapus tindak lanjut.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
