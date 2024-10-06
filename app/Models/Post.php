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

    public static function postsForTimeline($userId): Builder
    {
        return Post::query()
        ->withCount('reactions')
        ->with([
            'comments' => function ($query) {
                $query->withCount('reactions');
            },
            'reactions' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
            'group' => function ($query) use ($userId) {
                $query->whereHas('groupUsers', function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                          ->where('status', GroupUserStatus::APPROVED->value);
                });
            }
        ])
        // Ajouter la condition whereHas pour les groupes ou les posts personnels
        ->where(function ($query) use ($userId) {
            $query->whereDoesntHave('group')  // Post sans groupe (personnel)
                  ->orWhereHas('group', function ($query) use ($userId) {
                      // Ou bien un post associé à un groupe auquel l'utilisateur est approuvé
                      $query->whereHas('groupUsers', function ($query) use ($userId) {
                          $query->where('user_id', $userId)
                                ->where('status', GroupUserStatus::APPROVED->value);
                      });
                  });
        })
        ->latest();
    }

    public function isOwner($userId)
    {
        return $this->user_id === $userId;
    }
}
