<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Wraps the grouped settings array returned by SettingsService::getAll().
 *
 * Expected $this->resource shape:
 *   [
 *     'general' => ['company_name' => '...', 'company_email' => '...', ...],
 *     'mail'    => ['mail_driver' => '...', ...],
 *     ...
 *   ]
 *
 * Usage:
 *   return new SettingsResource($settingsService->getAll());
 */
class SettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Each top-level key is a settings group; its value is an associative
     * array of key => typed-value pairs belonging to that group.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $this->resource is already the grouped array produced by
        // SettingsService::getAll() or Setting::getGroup().
        // Cast to array to be safe whether an array or a Collection is passed.
        $grouped = is_array($this->resource)
            ? $this->resource
            : (array) $this->resource;

        // Ensure every value inside each group is a plain scalar / array so
        // it serialises cleanly to JSON without Eloquent model wrappers.
        $normalised = [];

        foreach ($grouped as $group => $settings) {
            $normalised[(string) $group] = is_array($settings)
                ? $settings
                : (array) $settings;
        }

        return $normalised;
    }
}
