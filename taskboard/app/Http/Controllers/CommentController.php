<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Notifications\CommentAdded;
use Illuminate\Support\Facades\Notification;

class CommentController extends Controller
{
        public function store(Request $request, Task $task)
    {
        Gate::authorize('view', $task);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $task->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        // Notify the task's creator and assignee (never the person who commented).
        $recipientIds = collect([$task->created_by, $task->assigned_to])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->reject(fn ($id) => $id === $request->user()->id);

        Notification::send(
            User::whereIn('id', $recipientIds)->get(),
            new CommentAdded($task, $request->user())
        );

        return redirect(route('tasks.show', $task) . '#comments');
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $task = $comment->task;
        $comment->delete();

        return redirect(route('tasks.show', $task) . '#comments');
    }
}