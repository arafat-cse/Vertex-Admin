<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Interfaces\SettingsRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsRepository implements SettingsRepositoryInterface
{
    /**
     * Get all settings keyed by their 'key' column.
     *
     * Returns a Collection so callers can do $settings->get('company_name'), etc.
     *
     * @return Collection  keyed by the 'key' column
     */
    public function getAll(): Collection
    {
        return Setting::all()->keyBy('key');
    }

    /**
     * Get a single setting by its key.
     *
     * @param  string  $key
     * @return Setting|null
     */
    public function getByKey(string $key): ?Setting
    {
        return Setting::where('key', $key)->first();
    }

    /**
     * Update a single setting value by its key.
     * Clears the setting cache entry afterwards.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return Setting
     */
    public function updateByKey(string $key, mixed $value): Setting
    {
        $serialised = $this->serialiseValue($value);

        $setting = Setting::where('key', $key)->firstOrNew(['key' => $key]);
        $setting->value = $serialised;
        $setting->save();

        Cache::forget('setting:' . $key);

        return $setting;
    }

    /**
     * Get all settings belonging to a specific group.
     *
     * @param  string  $group
     * @return Collection
     */
    public function getByGroup(string $group): Collection
    {
        return Setting::where('group', $group)->get();
    }

    /**
     * Update multiple settings at once.
     *
     * Accepts an associative array of [ key => value ] pairs.
     * Each setting is updated individually so that type metadata and cache
     * invalidation are handled correctly.
     *
     * @param  array  $data
     * @return bool
     */
    public function updateMultiple(array $data): bool
    {
        foreach ($data as $key => $value) {
            $serialised = $this->serialiseValue($value);

            Setting::where('key', $key)->update(['value' => $serialised]);

            Cache::forget('setting:' . $key);
        }

        return true;
    }

    /**
     * Store an uploaded logo file and update the 'company_logo' setting.
     *
     * The file is saved to storage/app/public/settings/ and the public URL
     * (relative storage path) is persisted in the settings table.
     *
     * @param  UploadedFile  $file
     * @return string  The stored relative path (suitable for Storage::url())
     */
    public function uploadLogo(UploadedFile $file): string
    {
        $path = $file->store('settings', 'public');

        $this->updateByKey('company_logo', $path);

        return Storage::url($path);
    }

    /**
     * Store an uploaded favicon file and update the 'company_favicon' setting.
     *
     * @param  UploadedFile  $file
     * @return string  The stored relative path (suitable for Storage::url())
     */
    public function uploadFavicon(UploadedFile $file): string
    {
        $path = $file->store('settings', 'public');

        $this->updateByKey('company_favicon', $path);

        return Storage::url($path);
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Serialise a PHP value to a plain string for database storage.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function serialiseValue(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string) $value;
    }
}
