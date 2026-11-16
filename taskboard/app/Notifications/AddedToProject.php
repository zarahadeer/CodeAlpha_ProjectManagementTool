<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\User;
use Illuminate\Notifications\Notification;

class AddedToProject extends Notification
{
    public function __construct(public Project $project, public User $adder)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->adder->name} added you to \"{$this->project->title}\"",
            'url' => route('projects.show', $this->project, false),
        ];
    }
}