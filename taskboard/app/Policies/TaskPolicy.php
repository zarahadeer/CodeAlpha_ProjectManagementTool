<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    // Any member of the project can open and edit its tasks.
    public function view(User $user, Task $task): bool
    {
        return $task->project->roleOf($user) !== null;
    }

    public function update(User $user, Task $task): bool
    {
        return $task->project->roleOf($user) !== null;
    }

    // Only the task's creator, or a project owner/admin, can delete it.
    public function delete(User $user, Task $task): bool
    {
        return $task->created_by === $user->id
            || in_array($task->project->roleOf($user), ['owner', 'admin']);
    }
}