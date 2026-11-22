# Crewboard

Crewboard is a collaborative project management tool in the style of Trello and Asana. Teams create projects, organise work on a board, assign tasks, and discuss them in comments. Changes appear live for everyone on the board.

Built as an internship project with Laravel.

## Features

- **Accounts:** register, log in, log out, edit profile, delete account
- **Download all data:** export your own projects, tasks, comments and notifications as a JSON file (available on the profile page, before deleting an account)
- **Group projects:** create a project and add teammates by email, with roles (owner, admin, member)
- **Project boards:** every project gets columns (To Do, In Progress, In Review, Done)
- **Task cards:** title, description, status, assignee, priority and due date; overdue tasks are highlighted
- **Drag and drop:** move cards between columns or reorder them; the order is saved
- **Comments:** discuss a task right on its card
- **Notifications:** in-app bell with an unread count when you are assigned a task, someone comments on your task, or you are added to a project
- **Real-time updates (WebSockets):** board changes (create, edit, move, delete) and new notifications appear instantly for other users, with no page refresh
- **Permissions:** only project members can see a project; roles control who can edit, remove members or delete

## Tech stack

| Part | Technology |
|---|---|
| Backend | Laravel 12 (PHP 8.2+) |
| Auth | Laravel Breeze (Blade) |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| Drag and drop | SortableJS |
| Database | SQLite (works with MySQL too) |
| Real-time | Laravel Reverb (WebSocket server) + Laravel Echo |

## Requirements

- PHP 8.2 or newer
- Composer
- Node.js 18 or newer, with npm

## Setup

```bash
git clone https://github.com/zarahadeer/CodeAlpha_ProjectManagementTool.git
cd CodeAlpha_ProjectManagementTool/taskboard

composer install
npm install

cp .env.example .env
php artisan key:generate
```

On Windows PowerShell, use `copy .env.example .env` instead of `cp`.

Create the SQLite database file, then build the tables and add demo data:

```bash
# macOS / Linux
touch database/database.sqlite

# Windows PowerShell
New-Item database\database.sqlite -ItemType File

php artisan migrate --seed
```

## Run the app

Open **three terminals** in the `taskboard` folder and keep them running:

```bash
php artisan serve
```
```bash
npm run dev
```
```bash
php artisan reverb:start
```

Then open http://127.0.0.1:8000

The third terminal (Reverb) is only needed for live updates. Without it the app works normally, but changes show up after a refresh.

## Demo accounts

`php artisan migrate --seed` creates four users, two projects, sample tasks and comments. The password for every account is `password`.

| Name | Email | Roles |
|---|---|---|
| Alex Morgan | alex@crewboard.test | Owner of *Website Redesign*, admin in *Mobile App Launch* |
| Sara Khan | sara@crewboard.test | Admin in *Website Redesign*, owner of *Mobile App Launch* |
| Omar Ali | omar@crewboard.test | Member of both projects |
| Mia Chen | mia@crewboard.test | Member of *Website Redesign* only |

### Try the real-time features

1. Log in as Alex in a normal window and as Sara in a private/incognito window.
2. Open **Website Redesign** in both.
3. Drag a card, add a task, edit or delete one in a window and watch the other window update.
4. Assign a task to the other person. Their bell badge updates and a pop-up appears.

## Roles and permissions

| Action | Owner | Admin | Member |
|---|:---:|:---:|:---:|
| View the project and its board | Yes | Yes | Yes |
| Create, edit and move tasks, comment | Yes | Yes | Yes |
| Delete a task | Yes | Yes | Only tasks they created |
| Delete a comment | Yes | Yes | Only their own |
| Edit the project, add or remove members | Yes | Yes | No |
| Change member roles | Yes | No | No |
| Delete the project | Yes | No | No |

Permissions are enforced on the server with Laravel Policies (`ProjectPolicy`, `TaskPolicy`, `CommentPolicy`).

## Database

| Table | Purpose |
|---|---|
| `users` | Accounts |
| `projects` | Projects, with an owner |
| `project_user` | Membership and role of each user in a project |
| `board_columns` | The columns of a project's board |
| `tasks` | Task cards (column, assignee, priority, due date, position) |
| `comments` | Comments on tasks |
| `notifications` | In-app notifications (Laravel's built-in table) |

## Project layout

| Path | What it holds |
|---|---|
| `app/Models` | Eloquent models and relationships |
| `app/Http/Controllers` | Projects, tasks, members, comments, notifications, data export |
| `app/Policies` | Permission rules |
| `app/Events` | Real-time events (`BoardUpdated`, `NotificationReceived`) |
| `app/Notifications` | Notification classes |
| `routes/channels.php` | Rules for who can listen to which WebSocket channel |
| `resources/views` | Blade pages and layouts |
| `resources/js` | Drag and drop (`board.js`) and live notifications (`notifications.js`) |
| `database/seeders/DemoSeeder.php` | Demo data |

## Notes

- Broadcasting uses `ShouldBroadcastNow`, so no queue worker is needed.
- If Reverb is not running, live events are skipped and logged; they never break saving a task.
- Email goes to log file.