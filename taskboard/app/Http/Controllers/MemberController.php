<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Notifications\AddedToProject;

class MemberController extends Controller
{
    public function index(Project $project)
    {
        Gate::authorize('view', $project);

        $project->load('members');

        return view('projects.members', compact('project'));
    }

    // Add an existing user to the project by email.
    public function store(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $data = $request->validate(
            ['email' => ['required', 'email', 'exists:users,email']],
            ['email.exists' => 'No registered user has that email. They need to register first.']
        );

        $user = User::where('email', $data['email'])->first();

        if ($project->members()->where('users.id', $user->id)->exists()) {
            return back()->withErrors(['email' => 'That user is already a member.']);
        }

        $project->members()->attach($user->id, ['role' => 'member']);
        $user->notify(new AddedToProject($project, $request->user()));

        return back()->with('status', "{$user->name} was added to the project.");
    }

    public function update(Request $request, Project $project, User $user)
    {
        Gate::authorize('manageRoles', $project);

        $data = $request->validate([
            'role' => ['required', Rule::in(['admin', 'member'])],
        ]);

        if ($user->id === $project->owner_id) {
            return back()->withErrors(['role' => "The owner's role can't be changed."]);
        }

        $project->members()->updateExistingPivot($user->id, ['role' => $data['role']]);

        return back()->with('status', 'Role updated.');
    }

    public function destroy(Project $project, User $user)
    {
        Gate::authorize('update', $project);

        if ($user->id === $project->owner_id) {
            return back()->withErrors(['member' => "The owner can't be removed."]);
        }

        // Their tasks in this project become unassigned.
        $project->tasks()->where('assigned_to', $user->id)->update(['assigned_to' => null]);

        $project->members()->detach($user->id);

        return back()->with('status', 'Member removed.');
    }
}