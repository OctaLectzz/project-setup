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
            'avatar' => $this->avatar,
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone_number' => $this->phone_number,
            'domicile' => $this->domicile,
            'gender' => $this->gender,
            'pronouns' => $this->pronouns,
            'bio' => $this->bio,
            'status' => $this->status
        ];
    }
}
