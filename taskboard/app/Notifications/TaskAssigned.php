<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification
{
    public function __construct(public Task $task, public User $assigner)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->assigner->name} assigned you to \"{$this->task->title}\"",
            'url' => route('tasks.show', $this->task, false),
        ];
    }
}