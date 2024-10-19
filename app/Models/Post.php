<?php

namespace App\Models;

use App\Http\Enums\GroupUserStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'body',
        'user_id',
        'group_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PostAttachment::class);
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'object');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    // tous les posts avec le nombre de réactions,
    // les commentaires avec leur nombre de reactions,
    // savoir si l'utilisateur actuel a de reaction
    // groupes où je suis approuvés,
    // les posts qui ont un groupe_id null
    public static function postsForTimeline($userId, $getLatest = true): Builder
    {
        $query = Post::query()
        ->withCount('reactions')
        ->with([
            'comments.reactions',
            'reactions' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
            'group.groupUsers' => function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                          ->where('status', GroupUserStatus::APPROVED->value);
            }
        ])
        ->where(function ($query) use ($userId) {
            $query->whereNull('group_id')
                  ->orWhereHas('group.groupUsers', function ($query) use ($userId) {
                        $query->where('user_id', $userId)
                            ->where('status', GroupUserStatus::APPROVED->value);
                    });
        });
        if ($getLatest) {
            $query->latest();
        }

        return $query;
    }

    public function isOwner($userId)
    {
        return $this->user_id === $userId;
    }
}
