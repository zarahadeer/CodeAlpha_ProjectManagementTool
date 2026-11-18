<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Demo users. The password for all of them is "password".
        $people = [
            'alex' => ['Alex Morgan', 'alex@crewboard.test'],
            'sara' => ['Sara Khan', 'sara@crewboard.test'],
            'omar' => ['Omar Ali', 'omar@crewboard.test'],
            'mia' => ['Mia Chen', 'mia@crewboard.test'],
        ];

        $users = [];
        foreach ($people as $key => [$name, $email]) {
            $users[$key] = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => 'password']
            );
        }

        // Task format: [column, title, priority, assignee, due in N days (negative = overdue)]
        $this->seedProject(
            users: $users,
            owner: $users['alex'],
            title: 'Website Redesign',
            description: 'Refresh the company website: a new look, faster pages and a simple blog.',
            members: ['sara' => 'admin', 'omar' => 'member', 'mia' => 'member'],
            tasks: [
                ['To Do', 'Write blog launch post', 'low', 'mia', 14],
                ['To Do', 'Set up analytics dashboard', 'medium', null, 10],

                ['In Progress', 'Design new homepage', 'high', 'sara', 3],
                ['In Progress', 'Build responsive navbar', 'medium', 'omar', 5],
                ['In Progress', 'Collect customer testimonials', 'low', 'mia', -2],

                ['In Review', 'Create logo variations', 'medium', 'sara', 1],

                ['Done', 'Audit current website', 'medium', 'alex', -10],
                ['Done', 'Choose colour palette', 'low', 'sara', -7],
                ['Done', 'Set up staging server', 'high', 'omar', -5],
            ],
            comments: [
                'Design new homepage' => [
                    ['sara', 'First draft is ready. I went with a big hero section and three feature cards.'],
                    ['alex', 'Looks great! Can we make the call-to-action button more prominent?'],
                    ['sara', 'Sure, I will bump up the size and contrast today.'],
                ],
                'Collect customer testimonials' => [
                    ['alex', 'This one is overdue. Mia, do you need any help reaching customers?'],
                    ['mia', 'Two replies so far, waiting on the rest. I will chase them tomorrow.'],
                ],
                'Create logo variations' => [
                    ['omar', 'The dark version works well on the footer. Ready for review.'],
                ],
            ],
        );

        $this->seedProject(
            users: $users,
            owner: $users['sara'],
            title: 'Mobile App Launch',
            description: 'Everything needed to ship version 1.0 of our mobile app.',
            members: ['alex' => 'admin', 'omar' => 'member'],
            tasks: [
                ['To Do', 'Plan push notifications', 'medium', 'omar', 12],
                ['To Do', 'Prepare App Store screenshots', 'low', null, 9],

                ['In Progress', 'Implement login screen', 'high', 'alex', 2],
                ['In Progress', 'Fix crash on startup', 'high', 'omar', -1],

                ['In Review', 'Write onboarding copy', 'medium', 'sara', 4],

                ['Done', 'Set up CI pipeline', 'medium', 'alex', -6],
                ['Done', 'Create app icon', 'low', 'sara', -9],
            ],
            comments: [
                'Fix crash on startup' => [
                    ['sara', 'Users on older Android versions are hitting this. High priority.'],
                    ['omar', 'Found the cause, a missing permission check. Fix coming today.'],
                ],
                'Implement login screen' => [
                    ['alex', 'Using email and password for now, social login can come later.'],
                ],
            ],
        );
    }

    private function seedProject(
        array $users,
        User $owner,
        string $title,
        string $description,
        array $members,
        array $tasks,
        array $comments,
    ): void {
        // Creating a project also adds the owner and the four default columns.
        $project = Project::firstOrCreate(
            ['owner_id' => $owner->id, 'title' => $title],
            ['description' => $description]
        );

        // Already seeded before: do nothing, so running this twice is safe.
        if (! $project->wasRecentlyCreated) {
            return;
        }

        foreach ($members as $key => $role) {
            $project->members()->attach($users[$key]->id, ['role' => $role]);
        }

        $columns = $project->columns()->get()->keyBy('name');
        $positions = [];
        $created = [];

        foreach ($tasks as [$columnName, $taskTitle, $priority, $assigneeKey, $dueInDays]) {
            $positions[$columnName] = ($positions[$columnName] ?? -1) + 1;

            $created[$taskTitle] = Task::create([
                'project_id' => $project->id,
                'board_column_id' => $columns[$columnName]->id,
                'title' => $taskTitle,
                'created_by' => $owner->id,
                'assigned_to' => $assigneeKey ? $users[$assigneeKey]->id : null,
                'priority' => $priority,
                'due_date' => $dueInDays === null ? null : now()->addDays($dueInDays)->toDateString(),
                'position' => $positions[$columnName],
            ]);
        }

        foreach ($comments as $taskTitle => $thread) {
            foreach ($thread as [$userKey, $body]) {
                Comment::create([
                    'task_id' => $created[$taskTitle]->id,
                    'user_id' => $users[$userKey]->id,
                    'body' => $body,
                ]);
            }
        }
    }
}