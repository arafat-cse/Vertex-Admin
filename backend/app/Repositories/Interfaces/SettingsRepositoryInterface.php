<?php

namespace App\Repositories\Interfaces;

interface SettingsRepositoryInterface
{
    /**
     * Get all settings.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * Get a single setting by its key.
     *
     * @param string $key
     * @return \App\Models\Setting|null
     */
    public function getByKey(string $key);

    /**
     * Update a setting value by its key.
     *
     * @param string $key
     * @param mixed $value
     * @return \App\Models\Setting
     */
    public function updateByKey(string $key, mixed $value);

    /**
     * Get all settings belonging to a specific group.
     *
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByGroup(string $group);

    /**
     * Update multiple settings at once.
     *
     * @param array $data  Associative array of key => value pairs
     * @return bool
     */
    public function updateMultiple(array $data);

    /**
     * Upload and store the company logo, returning the stored path.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public function uploadLogo(\Illuminate\Http\UploadedFile $file);

    /**
     * Upload and store the company favicon, returning the stored path.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public function uploadFavicon(\Illuminate\Http\UploadedFile $file);
}
