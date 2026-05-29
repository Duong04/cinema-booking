<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'is_active' => $this->is_active,
            'membership' => $this->whenLoaded('membership'),
            'tickets_purchased_count' => $this->whenCounted('confirmedBookingItems'),
            'role' => new RoleResource($this->role)
        ];
    }
}
