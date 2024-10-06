<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $coverUrl = $this->cover_path ? Storage::url($this->cover_path) : '/img/default_cover.jpg';
        $avatarUrl = $this->avatar_path ? Storage::url($this->avatar_path) : '/img/default_avatar.webp';
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "email_verified_at" => $this->email_verified_at,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "username" => $this->username,
            "cover_url" => $coverUrl,
            "avatar_url" => $avatarUrl,
        ];
    }
}
