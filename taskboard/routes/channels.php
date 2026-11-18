<?php

use App\Models\Project;
use Illuminate\Support\Facades\Broadcast;

// Each user's private channel (used for notifications).
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Only members of a project may listen to its board.
Broadcast::channel('project.{projectId}', function ($user, $projectId) {
    $project = Project::find($projectId);

    return $project !== null && $project->roleOf($user) !== null;
});