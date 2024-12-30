<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class GroupUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $avatarUrl = $this->avatar_path ? Storage::url($this->avatar_path) : '/img/default_avatar.webp';
        return [
            "id" => $this->id,
            "name" => $this->name,
            'role' => $this->role,
            'status' => $this->status,
            'group_id' => $this->group_id,
            "username" => $this->username,
            "avatar_url" => env('APP_URL') . $avatarUrl,
        ];
    }
}
