<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Follower;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function index(Request $request, User $user)
    {
        $isCurrentUserFollower = false;
        if (!Auth::guest()) {
            $isCurrentUserFollower = Follower::where('user_id', $user->id)->where('follower_id', Auth::id())->exists();
        }
        $followerCount = Follower::where('user_id', $user->id)->count();

        $posts = Post::postsForTimeline(Auth::id())
            ->where('user_id', $user->id)
            ->whereNull('group_id')
            ->paginate(5);

        $posts = PostResource::collection($posts);
        if ($request->wantsJson()) {
            return $posts;
        }

        $followers = $user->followers;

        $followings = $user->followings;

        return Inertia::render('Profile/View', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'notification' => session('notification'),
            'isCurrentUserFollower' => $isCurrentUserFollower,
            'followerCount' => $followerCount,
            'user' => new UserResource($user),
            'posts' => $posts,
            'followers' => UserResource::collection($followers),
            'followings' => UserResource::collection($followings),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile', $request->user())->with('notification', 'Your profile information was successfully updated.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateImage(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $success = '';
        /** @var UploadedFile $avatar */
        $avatar = $data['avatar'] ?? null;
        /** @var UploadedFile $cover */
        $cover = $data['cover'] ?? null;

        if ($avatar) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $path =  $avatar->store('user-'.$user->id, 'public');
            $user->update(['avatar_path' => $path]);
            $success = 'Your avatar image was updated';
        }

        if ($cover) {
            if ($user->cover_path) {
                Storage::disk('public')->delete($user->cover_path);
            }
            $path =  $cover->store('user-'.$user->id, 'public');
            $user->update(['cover_path' => $path]);
            $success = 'Your cover image was updated';
        }

        return back()->with('notification', $success);
    }
}
