<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    // Dashboard: all projects I belong to.
    public function index(Request $request)
    {
        $projects = $request->user()
            ->projects()
            ->withCount(['tasks', 'members'])
            ->latest('projects.created_at')
            ->get();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $project = Project::create(
            $this->validated($request) + ['owner_id' => $request->user()->id]
        );

        return redirect()->route('projects.show', $project)->with('status', 'Project created.');
    }

    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        $project->load(['columns.tasks.assignee', 'members']);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        Gate::authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $project->update($this->validated($request));

        return redirect()->route('projects.show', $project)->with('status', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return redirect()->route('dashboard')->with('status', 'Project deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}