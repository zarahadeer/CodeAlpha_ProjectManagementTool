<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    // Any member can open the project.
    public function view(User $user, Project $project): bool
    {
        return $project->roleOf($user) !== null;
    }

    // Owner or admin can edit.
    public function update(User $user, Project $project): bool
    {
        return in_array($project->roleOf($user), ['owner', 'admin']);
    }

    // Only the owner can delete.
    public function delete(User $user, Project $project): bool
    {
        return $project->roleOf($user) === 'owner';
    }

        // Only the owner can change member roles.
    public function manageRoles(User $user, Project $project): bool
    {
        return $project->roleOf($user) === 'owner';
    }
}