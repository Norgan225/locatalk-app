<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskTagController extends Controller
{
    public function store(Request $request, $taskId)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'nullable|string|max:7',
        ]);

        $task = Task::findOrFail($taskId);

        if (!auth()->user()->canManageUsers() &&
            auth()->id() !== $task->created_by &&
            auth()->id() !== $task->assigned_to) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $tag = Tag::firstOrCreate(
            [
                'organization_id' => auth()->user()->organization_id,
                'name' => $request->name
            ],
            [
                'color' => $request->color ?? '#3B82F6'
            ]
        );

        if (!$task->tags()->where('tag_id', $tag->id)->exists()) {
            $task->tags()->attach($tag->id);

            if (method_exists($task, 'activities')) {
                $task->activities()->create([
                    'user_id' => auth()->id(),
                    'action' => 'tag_added',
                    'description' => "a ajouté l'étiquette : " . $tag->name,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tag added',
            'tag' => $tag
        ]);
    }

    public function destroy($taskId, $tagId)
    {
        $task = Task::findOrFail($taskId);

        if (!auth()->user()->canManageUsers() &&
            auth()->id() !== $task->created_by &&
            auth()->id() !== $task->assigned_to) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $tag = $task->tags()->find($tagId);
        if ($tag) {
            $tagName = $tag->name;
            $task->tags()->detach($tagId);

            if (method_exists($task, 'activities')) {
                $task->activities()->create([
                    'user_id' => auth()->id(),
                    'action' => 'tag_removed',
                    'description' => "a retiré l'étiquette : " . $tagName,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tag removed'
        ]);
    }
}
