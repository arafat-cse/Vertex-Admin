<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * Disable both created_at and updated_at — settings are managed explicitly.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    // -------------------------------------------------------------------------
    // Casts
    // -------------------------------------------------------------------------

    /**
     * The "value" column is stored as a plain string in the database.
     * Callers should use Setting::get() which returns the properly-typed value.
     * The raw $casts array is kept minimal intentionally; type coercion is
     * handled in the get() / set() static helpers below.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // No automatic casting here — we cast dynamically in get().
        ];
    }

    // -------------------------------------------------------------------------
    // Static helpers
    // -------------------------------------------------------------------------

    /**
     * Retrieve a setting value by key, with optional default.
     *
     * The raw string stored in the database is coerced to the correct PHP type
     * according to the row's `type` column:
     *   - boolean : '1','true','yes','on'  → true, everything else → false
     *   - integer : cast with (int)
     *   - json    : decoded with json_decode (associative)
     *   - string  : returned as-is
     *
     * @param  string  $key
     * @param  mixed   $default  Returned when the key does not exist.
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        /** @var self|null $setting */
        $setting = Cache::rememberForever(
            'setting:' . $key,
            fn () => static::where('key', $key)->first()
        );

        if ($setting === null) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Persist a setting value. Creates the row if it does not yet exist.
     *
     * The $value is serialised to a string before storage:
     *   - array / object : json_encode
     *   - boolean        : '1' or '0'
     *   - everything else: (string) cast
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        $serialised = static::serialiseValue($value);

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $serialised]
        );

        Cache::forget('setting:' . $key);
        Cache::forget('settings:group');
    }

    /**
     * Retrieve all settings that belong to a given group.
     *
     * Returns an associative array [ key => typed_value, … ].
     *
     * @param  string  $group
     * @return array<string, mixed>
     */
    public static function getGroup(string $group): array
    {
        $rows = static::where('group', $group)->get();

        $result = [];

        foreach ($rows as $row) {
            $result[$row->key] = static::castValue($row->value, $row->type);
        }

        return $result;
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Coerce a raw string value to the appropriate PHP type.
     *
     * @param  string|null  $raw
     * @param  string|null  $type  One of: string, boolean, integer, json
     * @return mixed
     */
    protected static function castValue(?string $raw, ?string $type): mixed
    {
        if ($raw === null) {
            return null;
        }

        return match ($type) {
            'boolean' => in_array(strtolower($raw), ['1', 'true', 'yes', 'on'], true),
            'integer' => (int) $raw,
            'json'    => json_decode($raw, true),
            default   => $raw,
        };
    }

    /**
     * Serialise a PHP value to a plain string for database storage.
     *
     * @param  mixed  $value
     * @return string
     */
    protected static function serialiseValue(mixed $value): string
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
