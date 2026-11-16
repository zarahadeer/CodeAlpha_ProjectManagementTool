<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    // Quick-add from the board: just a title and which column.
    public function store(Request $request, Project $project)
    {
        Gate::authorize('view', $project);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'board_column_id' => [
                'required',
                Rule::exists('board_columns', 'id')->where('project_id', $project->id),
            ],
        ]);

        $data['position'] = $this->nextPosition($data['board_column_id']);

        $project->tasks()->create($data + ['created_by' => $request->user()->id]);

        return back()->with('status', 'Task added.');
    }

    public function show(Task $task)
    {
        Gate::authorize('view', $task);

        $task->load(['project.columns', 'project.members', 'creator', 'assignee', 'comments.user']);

        return view('tasks.show', compact('task'));
    }

        public function update(Request $request, Task $task)
    {
        Gate::authorize('update', $task);

        $project = $task->project;

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'board_column_id' => [
                'required',
                Rule::exists('board_columns', 'id')->where('project_id', $project->id),
            ],
            'assigned_to' => [
                'nullable',
                Rule::exists('project_user', 'user_id')->where('project_id', $project->id),
            ],
            'priority' => ['required', Rule::in(Task::PRIORITIES)],
            'due_date' => ['nullable', 'date'],
        ]);

        if ((int) $data['board_column_id'] !== $task->board_column_id) {
            $data['position'] = $this->nextPosition($data['board_column_id']);
        }

        $previousAssignee = (int) $task->assigned_to;

        $task->update($data);

        // Tell the new assignee (not if you assigned the task to yourself).
        $newAssignee = (int) $task->assigned_to;

        if ($newAssignee && $newAssignee !== $previousAssignee && $newAssignee !== $request->user()->id) {
            User::find($newAssignee)?->notify(new TaskAssigned($task, $request->user()));
        }

        return redirect()->route('tasks.show', $task)->with('status', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);

        $project = $task->project;
        $task->delete();

        return redirect()->route('projects.show', $project)->with('status', 'Task deleted.');
    }

     // Called by drag-and-drop: save the card's new column and the new order of that column.
    public function move(Request $request, Task $task)
    {
        Gate::authorize('update', $task);

        $project = $task->project;

        $data = $request->validate([
            'board_column_id' => [
                'required',
                Rule::exists('board_columns', 'id')->where('project_id', $project->id),
            ],
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer'],
        ]);

        $ids = $data['ordered_ids'];

        // Every id must be a task in this project, with no duplicates, and include this task.
        $allInProject = Task::where('project_id', $project->id)->whereIn('id', $ids)->count() === count($ids);

        abort_unless(
            $allInProject && count(array_unique($ids)) === count($ids) && in_array($task->id, $ids),
            422
        );

        DB::transaction(function () use ($ids, $data, $project) {
            foreach ($ids as $position => $id) {
                Task::where('project_id', $project->id)
                    ->where('id', $id)
                    ->update([
                        'board_column_id' => $data['board_column_id'],
                        'position' => $position,
                    ]);
            }
        });

        return response()->json(['ok' => true]);
    }

    private function nextPosition(int|string $columnId): int
    {
        return (Task::where('board_column_id', $columnId)->max('position') ?? -1) + 1;
    }
}