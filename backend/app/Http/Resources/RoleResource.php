<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this?->id,
            'name' => $this?->name,
            'description' => $this?->description,
            'user_count' => $this->users_count ?? $this->users?->count() ?? 0,
            'users' => $this->whenLoaded('users', fn() => $this->users->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
            ])->values()),
            'permissions' => PermissionResource::collection($this->permissions->unique('id')),
            'created_at' => $this?->created_at,
            'updated_at' => $this?->updated_at,
        ];
    }
}
