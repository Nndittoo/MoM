<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionItem;
use App\Models\Mom;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = ActionItem::with(['mom' => function($q) {
                        $q->where('status_id', 2); // Only approved MOMs
                    }])
                    ->whereHas('mom', function($q) {
                        $q->where('status_id', 2);
                    });

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item', 'like', '%' . $search . '%')
                  ->orWhereHas('mom', function($q) use ($search) {
                      $q->where('title', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Get tasks
        $tasks = $query->orderBy('due', 'asc')->paginate(12);

        // Statistics
        $stats = [
            'total' => ActionItem::whereHas('mom', function($q) {
                        $q->where('status_id', 2);
                    })->count(),
            'mendatang' => ActionItem::whereHas('mom', function($q) {
                            $q->where('status_id', 2);
                        })
                        ->where('status', 'mendatang')
                        ->count(),
            'selesai' => ActionItem::whereHas('mom', function($q) {
                            $q->where('status_id', 2);
                        })
                        ->where('status', 'selesai')
                        ->count(),
            'overdue' => ActionItem::whereHas('mom', function($q) {
                            $q->where('status_id', 2);
                        })
                        ->where('status', 'mendatang')
                        ->where('due', '<', Carbon::now())
                        ->count(),
        ];

        return view('admin.task', compact('tasks', 'stats'));
    }

    /**
     * Update task status
     * @param int $action_id - Primary key dari action_items table
     */
    public function updateStatus(Request $request, $action_id)
    {
        try {
            // Log request untuk debugging
            Log::info('Update status request', [
                'action_id' => $action_id,
                'status' => $request->status,
                'all_data' => $request->all()
            ]);

            // Validate
            $validated = $request->validate([
                'status' => 'required|in:mendatang,selesai'
            ]);

            // Find task by action_id (primary key)
            $task = ActionItem::where('action_id', $action_id)->firstOrFail();

            Log::info('Task found', [
                'action_id' => $task->action_id,
                'current_status' => $task->status,
                'new_status' => $validated['status']
            ]);

            // Update status
            $task->status = $validated['status'];
            $saved = $task->save();

            Log::info('Task save result', ['saved' => $saved]);

            if (!$saved) {
                throw new \Exception('Failed to save task');
            }

            // Refresh task
            $task->refresh();

            Log::info('Task updated successfully', [
                'action_id' => $task->action_id,
                'new_status' => $task->status
            ]);

            // Return JSON response
            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diupdate!',
                'task' => [
                    'action_id' => $task->action_id,
                    'status' => $task->status,
                    'item' => $task->item
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Task not found', ['action_id' => $action_id]);

            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error updating task status', [
                'action_id' => $action_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search tasks (AJAX)
     */
    public function search(Request $request)
    {
        $query = ActionItem::with(['mom' => function($q) {
                        $q->where('status_id', 2);
                    }])
                    ->whereHas('mom', function($q) {
                        $q->where('status_id', 2);
                    });

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item', 'like', '%' . $search . '%')
                  ->orWhereHas('mom', function($q) use ($search) {
                      $q->where('title', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderBy('due', 'asc')->get();

        return response()->json($tasks);
    }
}
