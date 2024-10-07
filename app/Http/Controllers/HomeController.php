<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $user = $request->user();
        $posts = Post::postsForTimeline($userId)
            ->where(function ($query) use ($userId) {
                $query->whereNull('group_id')
                    ->where(function ($query) use ($userId) {
                        $query->where('user_id', $userId) // Les posts de l'utilisateur courant
                                ->orWhereHas('user.followers', function ($query) use ($userId) {
                                    $query->where('follower_id', $userId); // Les posts des utilisateurs qu'il suit
                                });
                    });
            })
            ->orWhereHas('group.groupUsers', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', GroupUserStatus::APPROVED->value);
            })
            ->paginate(5);

        $posts = PostResource::collection($posts);

        if ($request->wantsJson()) {
            return $posts;
        }

        $groups = Group::query()
            ->with('currentUserGroup')
            ->select(['groups.*', 'gu.status', 'gu.role'])
            ->join('group_users AS gu', 'gu.group_id', 'groups.id')
            ->where('gu.user_id', Auth::id())
            ->orderBy('gu.role')
            ->orderBy('name', 'desc')
            ->get();

        return Inertia::render('Home', [
            'posts' => $posts,
            'groups' => GroupResource::collection($groups),
            'followings' => UserResource::collection($user->followings)
        ]);
    }
}
