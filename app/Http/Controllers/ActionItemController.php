<?php

namespace App\Http\Controllers;

use App\Models\ActionItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;

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
        $actionItem->delete();
        return response()->json(['success' => true]);
    }
}