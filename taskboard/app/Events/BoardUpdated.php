<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Every public property is sent to the browsers.
    public function __construct(
        public int $projectId,
        public string $type,           // created | updated | moved | deleted
        public int $taskId,
        public ?int $columnId = null,
        public ?string $html = null,   // the rendered task card
        public array $orderedIds = [], // task order of that column
    ) {
    }

    // Build the event for a task that still exists.
    public static function forTask(Task $task, string $type): self
    {
        $task->load('assignee');

        return new self(
            projectId: $task->project_id,
            type: $type,
            taskId: $task->id,
            columnId: $task->board_column_id,
            html: view('projects._task-card', ['task' => $task])->render(),
            orderedIds: Task::where('board_column_id', $task->board_column_id)
                ->orderBy('position')
                ->pluck('id')
                ->all(),
        );
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel("project.{$this->projectId}")];
    }

    public function broadcastAs(): string
    {
        return 'board.updated';
    }
}