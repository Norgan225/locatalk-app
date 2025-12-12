<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    public function store(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);

        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('task-attachments/' . $task->id, 'public');

        $attachment = $task->attachments()->create([
            'user_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        // Log activity (if activity logging is enabled/implemented)
        if (method_exists($task, 'activities')) {
            $task->activities()->create([
                'user_id' => auth()->id(),
                'action' => 'attachment_added',
                'description' => "a ajouté le fichier : " . $attachment->file_name,
            ]);
        }

        return response()->json([
            'success' => true,
            'attachment' => $attachment,
            'url' => Storage::url($path),
            'formatted_size' => $this->formatSize($file->getSize())
        ]);
    }

    public function destroy($taskId, $attachmentId)
    {
        $task = Task::findOrFail($taskId);
        $attachment = TaskAttachment::findOrFail($attachmentId);

        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $fileName = $attachment->file_name;
        $attachment->delete();

        if (method_exists($task, 'activities')) {
            $task->activities()->create([
                'user_id' => auth()->id(),
                'action' => 'attachment_removed',
                'description' => "a supprimé le fichier : " . $fileName,
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }
}
