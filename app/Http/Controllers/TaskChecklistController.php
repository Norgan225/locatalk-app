<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskChecklist;
use Illuminate\Http\Request;

class TaskChecklistController extends Controller
{
    public function store(Request $request, $taskId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $task = Task::findOrFail($taskId);

        // Check permissions (same as task management)
        if (!auth()->user()->canManageUsers() &&
            auth()->id() !== $task->created_by &&
            auth()->id() !== $task->assigned_to) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $checklist = $task->checklists()->create([
            'title' => $request->title,
            'is_completed' => false,
        ]);

        if (method_exists($task, 'activities')) {
            $task->activities()->create([
                'user_id' => auth()->id(),
                'action' => 'checklist_added',
                'description' => "a ajouté à la checklist : " . $checklist->title,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added',
            'item' => $checklist,
            'progress' => $this->calculateProgress($task)
        ]);
    }

    public function update(Request $request, $id)
    {
        $checklist = TaskChecklist::findOrFail($id);
        $task = $checklist->task;

        if (!auth()->user()->canManageUsers() &&
            auth()->id() !== $task->created_by &&
            auth()->id() !== $task->assigned_to) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $checklist->update([
            'is_completed' => $request->boolean('is_completed')
        ]);

        if (method_exists($task, 'activities')) {
            $action = $checklist->is_completed ? 'coché' : 'décoché';
            $task->activities()->create([
                'user_id' => auth()->id(),
                'action' => 'checklist_updated',
                'description' => "a $action : " . $checklist->title,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item updated',
            'item' => $checklist,
            'progress' => $this->calculateProgress($task)
        ]);
    }

    public function destroy($id)
    {
        $checklist = TaskChecklist::findOrFail($id);
        $task = $checklist->task;

        if (!auth()->user()->canManageUsers() &&
            auth()->id() !== $task->created_by &&
            auth()->id() !== $task->assigned_to) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $title = $checklist->title;
        $checklist->delete();

        if (method_exists($task, 'activities')) {
            $task->activities()->create([
                'user_id' => auth()->id(),
                'action' => 'checklist_removed',
                'description' => "a supprimé de la checklist : " . $title,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item deleted',
            'progress' => $this->calculateProgress($task)
        ]);
    }

    private function calculateProgress($task)
    {
        $total = $task->checklists()->count();
        $completed = $task->checklists()->where('is_completed', true)->count();
        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'percentage' => $percentage
        ];
    }
}
