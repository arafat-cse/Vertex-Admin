<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'event'          => $this->event,
            'auditable_type' => $this->auditable_type,
            'auditable_id'   => $this->auditable_id,
            'old_values'     => $this->old_values,
            'new_values'     => $this->new_values,
            'ip_address'     => $this->ip_address,
            'user_agent'     => $this->user_agent,
            'user'           => $this->when(
                $this->relationLoaded('user'),
                fn () => $this->user === null ? null : [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ]
            ),
            'created_at'     => $this->created_at?->toISOString(),
        ];
    }
}
