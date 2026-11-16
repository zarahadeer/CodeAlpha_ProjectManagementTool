<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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