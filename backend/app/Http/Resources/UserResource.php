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
            'id'                 => $this->id,
            'name'               => $this->name,
            'email'              => $this->email,
            'status'             => $this->status,
            'avatar'             => $this->avatar_url,
            'last_login_at'      => $this->last_login_at?->format('d M Y H:i:s'),
            'email_verified_at'  => $this->email_verified_at?->toISOString(),
            'roles'              => $this->when(
                $this->relationLoaded('roles'),
                fn () => $this->getRoleNames()->values()->all()
            ),
            'permissions'        => $this->when(
                $this->relationLoaded('permissions'),
                fn () => $this->permissions->pluck('name')->values()->all()
            ),
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }
}
