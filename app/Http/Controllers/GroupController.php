<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Requests\InviteUsersRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupUserResource;
use App\Http\Resources\PostAttachmentResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\User;
use App\Notifications\InvitationApproved;
use App\Notifications\InvitationInGroup;
use App\Notifications\RequestApproved;
use App\Notifications\RequestToJoinGroup;
use App\Notifications\RoleChanged;
use App\Notifications\UserRemovedFromGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function profile(Request $request, Group $group)
    {
        $userId = Auth::id();

        if ($group->hasApprovedUser($userId)) {
            $posts = Post::postsForTimeline($userId)
                ->where('group_id', $group->id)
                ->paginate(5);
            $posts = PostResource::collection($posts);
        } else {
            return Inertia::render('Group/View', [
                'notification' => session('notification'),
                'group' => new GroupResource($group),
                'posts' => null,
                'users' => [],
                'requests' => []
            ]);
        }

        if ($request->wantsJson()) {
            return $posts;
        }

        $users = User::query()
            ->select(['users.*', 'gu.role', 'gu.status', 'gu.group_id'])
            ->join('group_users AS gu', 'gu.user_id', 'users.id')
            ->orderBy('gu.role')
            ->orderBy('users.name')
            ->where('group_id', $group->id)
            ->where('gu.status', GroupUserStatus::APPROVED->value)
            ->get();

        $requests = $group->pendingUsers()->orderBy('name')->get();

        $photos = PostAttachment::query()
            ->select('post_attachments.*')
            ->join('posts AS p', 'p.id', 'post_attachments.post_id')
            ->where('p.group_id', $group->id)
            ->where('mime', 'like', 'image/%')
            ->latest()
            ->get();

        return Inertia::render('Group/View', [
            'notification' => session('notification'),
            'group' => new GroupResource($group),
            'posts' => $posts,
            'users' => GroupUserResource::collection($users),
            'requests' => UserResource::collection($requests),
            'photos' => PostAttachmentResource::collection($photos)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $group = Group::create($data);

        $groupUserData = [
            'status' => GroupUserStatus::APPROVED->value,
            'role' => GroupUserRole::ADMIN->value,
            'user_id' => Auth::id(),
            'group_id' => $group->id,
            'created_by' => Auth::id()
        ];

        GroupUser::create($groupUserData);

        return response(new GroupResource($group), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $data = $request->validated();
        $removeRejections = $data["auto_approval"] != $group->auto_approval;
        $group->update($data);
        // TODO : notification to group admins

        if ($removeRejections) {
            $groupUsersRejected = GroupUser::query()
                ->where('group_id', $group->id)
                ->where('status', GroupUserStatus::REJECTED->value)
                ->get();

            foreach ($groupUsersRejected as $groupUser) {
                $groupUser->delete();
            }
        }

        return back()->with('notification', "Group was updated");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        //
    }

    public function updateImage(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("You don't have permission to perform this action", 403);
        }
        $data = $request->validate([
            'cover' => ['nullable', 'image'],
            'thumbnail' => ['nullable', 'image']
        ]);

        /** @var \Illuminate\Http\UploadedFile $thumbnail */
        $thumbnail = $data['thumbnail'] ?? null;
        /** @var \Illuminate\Http\UploadedFile $cover */
        $cover = $data['cover'] ?? null;

        $success = '';
        if ($cover) {
            if ($group->cover_path) {
                Storage::disk('public')->delete($group->cover_path);
            }
            $path = $cover->store('group-' . $group->id, 'public');
            $group->update(['cover_path' => $path]);
            $success = 'Your cover image was updated';
        }

        if ($thumbnail) {
            if ($group->thumbnail_path) {
                Storage::disk('public')->delete($group->thumbnail_path);
            }
            $path = $thumbnail->store('group-' . $group->id, 'public');
            $group->update(['thumbnail_path' => $path]);
            $success = 'Your thumbnail image was updated';
        }

        return back()->with('notification', $success);
    }

    public function inviteUsers(InviteUsersRequest $request, Group $group)
    {
        // user we want to invite and previous invitation pending
        $user = $request->user;
        $groupUser = $request->groupUser;

        if ($groupUser) {
            $groupUser->delete();
        }

        $hours = 24;
        $token = Str::random(256);

        GroupUser::create([
            'status' => GroupUserStatus::INVITED->value,
            'role' => GroupUserRole::USER->value,
            'token' => $token,
            'token_expire_date' => Carbon::now()->addHours($hours),
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => Auth::id(),
        ]);

        $user->notify(new InvitationInGroup($group, $hours, $token));


        return back()->with('notification', $user->name . ' was invited to join to group');
    }

    public function approveInvitation(string $token)
    {
        $groupUser = GroupUser::query()
            ->where('token', $token)
            ->first();

        $errorTitle = '';
        if (!$groupUser) {
            $errorTitle = 'The link is not valid';
        } else if ($groupUser->token_used || $groupUser->status === GroupUserStatus::APPROVED->value) {
            $errorTitle = 'The link is already used';
        } else if ($groupUser->token_expire_date < Carbon::now()) {
            $errorTitle = 'The link is expired';
        }

        if ($errorTitle) {
            // TODO : Proper rendering
            return \inertia('Error', ['title' => $errorTitle]);
        }

        $groupUser->status = GroupUserStatus::APPROVED->value;
        $groupUser->token_used = Carbon::now();
        $groupUser->save();

        $adminUser = $groupUser->adminUser;

        $adminUser->notify(new InvitationApproved($groupUser->group, $groupUser->user));

        return redirect(route('group.profile', $groupUser->group))->with('notification', 'You accepted to join to group "' . $groupUser->group->name . '"');
    }

    public function acceptInvitation(Group $group)
    {
        $groupUser = GroupUser::query()
            ->where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->where('status', GroupUserStatus::INVITED->value)
            ->first();

        $errorTitle = '';
        if (!$groupUser) {
            $errorTitle = 'The link is not valid';
        } else if ($groupUser->token_used || $groupUser->status === GroupUserStatus::APPROVED->value) {
            $errorTitle = 'The link is already used';
        } else if ($groupUser->token_expire_date < Carbon::now()) {
            $errorTitle = 'The link is expired';
        }

        if ($errorTitle) {
            // TODO : Proper rendering
            return \inertia('Error', ['title' => $errorTitle]);
        }

        $groupUser->status = GroupUserStatus::APPROVED->value;
        $groupUser->token_used = Carbon::now();
        $groupUser->save();

        $adminUser = $groupUser->adminUser;

        $adminUser->notify(new InvitationApproved($groupUser->group, $groupUser->user));

        return redirect(route('group.profile', $groupUser->group))->with('notification', 'You accepted to join to group "' . $groupUser->group->name . '"');
    }

    public function join(Request $request, Group $group)
    {
        $user = $request->user();

        if ($group->isOwner($user->id)) {
            return back()->with('notification', 'The owner of a group cannot ask to join it');
        }

        $ancientDatas = GroupUser::query()
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->get();

        if ($ancientDatas->last()?->status === GroupUserStatus::APPROVED->value) {
            return back();
        }

        foreach ($ancientDatas as $ancientData) {
            $ancientData->delete();
        }

        $status = GroupUserStatus::APPROVED->value;
        $successMessage = 'You have joined to group "' . $group->name . '"';

        if (!$group->auto_approval) {
            $status = GroupUserStatus::PENDING->value;
            $successMessage = 'Your request has been accepted. You will be notified once you will be approved';


            if (!count($ancientDatas)) {
                Notification::send($group->adminUsers, new RequestToJoinGroup($group, $user));
            }
        }

        GroupUser::create([
            'status' => $status,
            'role' => GroupUserRole::USER->value,
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => $user->id,
        ]);

        return back()->with('notification', $successMessage);
    }

    public function approveRequest(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("You don't have permission to perform this action", 403);
        }

        $data = $request->validate([
            'user_id' => ['required'],
            'action' => ['required']
        ]);

        $groupUser = GroupUser::where('user_id', $data['user_id'])
            ->where('group_id', $group->id)
            ->where('status', GroupUserStatus::PENDING->value)
            ->first();

        if ($groupUser) {
            $approved = false;
            if ($data['action'] === 'approve' || $group->isOwner($data['user_id'])) {
                $approved = true;
                $groupUser->status = GroupUserStatus::APPROVED->value;
            } else {
                $groupUser->status = GroupUserStatus::REJECTED->value;
            }
            $groupUser->save();

            $user = $groupUser->user;
            $user->notify(new RequestApproved($groupUser->group, $user, $approved));

            return back()->with('notification', 'User "' . $user->name . '" was ' . ($approved ? 'approved' : 'rejected'));
        }

        return back();
    }

    public function removeUser(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("You don't have permission to perform this action", 403);
        }

        $data = $request->validate([
            'user_id' => ['required'],
        ]);

        $user_id = $data['user_id'];
        if ($group->isOwner($user_id)) {
            return response("The owner of the group cannot be removed", 403);
        }

        $groupUser = GroupUser::where('user_id', $user_id)
            ->where('group_id', $group->id)
            ->where('status', GroupUserStatus::APPROVED->value)
            ->first();

        if ($groupUser) {
            $user = $groupUser->user;
            if ($groupUser->group->auto_approval) {
                $groupUser->status = GroupUserStatus::REJECTED->value;
                $groupUser->save();
            } else {
                $groupUser->delete();
            }

            $user->notify(new UserRemovedFromGroup($group));
        }

        return back();
    }

    public function changeRole(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("You don't have permission to perform this action", 403);
        }

        $data = $request->validate([
            'user_id' => ['required'],
            'role' => ['required', Rule::enum(GroupUserRole::class)]
        ]);

        $user_id = $data['user_id'];
        if ($group->isOwner($user_id)) {
            return response("You can't change role of the owner of the group", 403);
        }

        $groupUser = GroupUser::where('user_id', $user_id)
            ->where('group_id', $group->id)
            ->where('status', GroupUserStatus::APPROVED->value)
            ->first();

        if ($groupUser) {
            $groupUser->role = $data['role'];
            $groupUser->save();

            $groupUser->user->notify(new RoleChanged($group, $data['role']));
        }

        return back();
    }
}
