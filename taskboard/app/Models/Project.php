<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id', 'title', 'description'];

    public const DEFAULT_COLUMNS = ['To Do', 'In Progress', 'In Review', 'Done'];

    protected static function booted(): void
    {
        // When a project is created: add the owner as a member
        // and give the board its default columns.
        static::created(function (Project $project) {
            $project->members()->attach($project->owner_id, ['role' => 'owner']);

            foreach (self::DEFAULT_COLUMNS as $position => $name) {
                $project->columns()->create(['name' => $name, 'position' => $position]);
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function columns(): HasMany
    {
        return $this->hasMany(BoardColumn::class)->orderBy('position');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function roleOf(User $user): ?string
    {
        return $this->members()->where('users.id', $user->id)->first()?->pivot->role;
    }
}