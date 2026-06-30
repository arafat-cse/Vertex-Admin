<?php

namespace App\Services;

use App\Http\Resources\SettingResource;
use App\Repositories\Interfaces\SettingsRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class SettingsService
{
    public function __construct(
        private readonly SettingsRepositoryInterface $settingsRepository,
        private readonly FileUploadService $fileUploadService,
    ) {}

    /**
     * Return all settings grouped by their group column.
     *
     * Returns an associative array where each key is a group name and the
     * value is an associative array of [ setting_key => setting_value ].
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAll(): array
    {
        $settings = $this->settingsRepository->getAll();

        $grouped = [];

        foreach ($settings as $setting) {
            $grouped[$setting->group][$setting->key] = $setting->value;
        }

        return $grouped;
    }

    /**
     * Update general / company settings.
     *
     * Accepted keys: company_name, company_email, timezone, date_format.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, array<string, mixed>>
     */
    public function updateGeneral(array $data): array
    {
        $allowed = ['company_name', 'company_email', 'timezone', 'date_format'];

        $filtered = array_filter(
            $data,
            fn (string $key) => in_array($key, $allowed, true),
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($filtered)) {
            $this->settingsRepository->updateMultiple($filtered);
        }

        return $this->getAll();
    }

    /**
     * Update mail / SMTP settings.
     *
     * Accepted keys: mail_driver, mail_host, mail_port, mail_username,
     *                mail_password, mail_from_address, mail_encryption.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, array<string, mixed>>
     */
    public function updateEmail(array $data): array
    {
        $allowed = [
            'mail_driver',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_from_address',
            'mail_encryption',
        ];

        $filtered = array_filter(
            $data,
            fn (string $key) => in_array($key, $allowed, true),
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($filtered)) {
            $this->settingsRepository->updateMultiple($filtered);
        }

        return $this->getAll();
    }

    /**
     * Upload the company logo and persist its path in the settings table.
     *
     * @param  UploadedFile  $file
     * @return array<string, array<string, mixed>>
     *
     * @throws InvalidArgumentException  Propagated from FileUploadService on validation failure
     */
    public function uploadLogo(UploadedFile $file): array
    {
        $path = $this->fileUploadService->upload(
            $file,
            'logos',
            ['jpg', 'jpeg', 'png', 'webp', 'svg'],
            2048
        );

        $this->settingsRepository->updateByKey('company_logo', $path);

        return $this->getAll();
    }

    /**
     * Upload the company favicon and persist its path in the settings table.
     *
     * @param  UploadedFile  $file
     * @return array<string, array<string, mixed>>
     *
     * @throws InvalidArgumentException  Propagated from FileUploadService on validation failure
     */
    public function uploadFavicon(UploadedFile $file): array
    {
        $path = $this->fileUploadService->upload(
            $file,
            'favicons',
            ['ico', 'png', 'svg', 'webp'],
            512
        );

        $this->settingsRepository->updateByKey('company_favicon', $path);

        return $this->getAll();
    }
}
