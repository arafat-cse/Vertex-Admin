<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'method'      => $this->method,
            'url'         => $this->url,
            'ip_address'  => $this->ip_address,
            'status_code' => $this->status_code,
            'payload'     => $this->payload,
            'user'        => $this->when(
                $this->relationLoaded('user'),
                fn () => $this->user === null ? null : [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ]
            ),
            'created_at'  => $this->created_at?->toISOString(),
        ];
    }
}
