<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class DataExportController extends Controller
{
    public function download(Request $request)
    {
        $user = $request->user();

        // How a task is written into the file.
        $taskShape = fn ($task) => [
            'title' => $task->title,
            'description' => $task->description,
            'project' => $task->project?->title,
            'status' => $task->column?->name,
            'priority' => $task->priority,
            'due_date' => $task->due_date?->toDateString(),
            'assigned_to' => $task->assignee?->name,
            'created_by' => $task->creator?->name,
            'created_at' => $task->created_at?->toIso8601String(),
        ];

        $relations = ['project', 'column', 'assignee', 'creator'];

        $export = [
            'exported_at' => now()->toIso8601String(),

            'account' => [
                'name' => $user->name,
                'email' => $user->email,
                'joined_at' => $user->created_at?->toIso8601String(),
            ],

            'projects' => $user->projects()->get()->map(fn ($project) => [
                'title' => $project->title,
                'description' => $project->description,
                'your_role' => $project->pivot->role,
                'created_at' => $project->created_at?->toIso8601String(),
            ])->values(),

            'tasks_assigned_to_you' => $user->assignedTasks()
                ->with($relations)->get()->map($taskShape)->values(),

            'tasks_you_created' => Task::where('created_by', $user->id)
                ->with($relations)->get()->map($taskShape)->values(),

            'your_comments' => $user->comments()->with('task.project')->latest()->get()
                ->map(fn ($comment) => [
                    'task' => $comment->task?->title,
                    'project' => $comment->task?->project?->title,
                    'comment' => $comment->body,
                    'posted_at' => $comment->created_at?->toIso8601String(),
                ])->values(),

            'your_notifications' => $user->notifications()->latest()->get()
                ->map(fn ($notification) => [
                    'message' => $notification->data['message'] ?? null,
                    'read' => $notification->read_at !== null,
                    'received_at' => $notification->created_at?->toIso8601String(),
                ])->values(),
        ];

        $filename = 'crewboard-data-' . now()->format('Y-m-d') . '.json';

        return response()->streamDownload(function () use ($export) {
            echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }, $filename, ['Content-Type' => 'application/json']);
    }
}