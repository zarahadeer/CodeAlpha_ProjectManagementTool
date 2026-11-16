<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    // The author can delete their own comment; project owner/admin can delete any.
    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id
            || in_array($comment->task->project->roleOf($user), ['owner', 'admin']);
    }
}