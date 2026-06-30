<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            [
                'key'   => 'company_name',
                'value' => 'Vertex-Admin',
                'type'  => 'string',
                'group' => 'general',
            ],
            [
                'key'   => 'company_email',
                'value' => 'admin@vertex.dev',
                'type'  => 'string',
                'group' => 'general',
            ],
            [
                'key'   => 'timezone',
                'value' => 'Asia/Dhaka',
                'type'  => 'string',
                'group' => 'general',
            ],
            [
                'key'   => 'date_format',
                'value' => 'd M Y',
                'type'  => 'string',
                'group' => 'general',
            ],
            [
                'key'   => 'theme',
                'value' => 'light',
                'type'  => 'string',
                'group' => 'general',
            ],
            [
                'key'   => 'logo',
                'value' => null,
                'type'  => 'string',
                'group' => 'general',
            ],
            [
                'key'   => 'favicon',
                'value' => null,
                'type'  => 'string',
                'group' => 'general',
            ],

            // Mail
            [
                'key'   => 'mail_driver',
                'value' => 'smtp',
                'type'  => 'string',
                'group' => 'mail',
            ],
            [
                'key'   => 'mail_host',
                'value' => 'smtp.mailtrap.io',
                'type'  => 'string',
                'group' => 'mail',
            ],
            [
                'key'   => 'mail_port',
                'value' => '2525',
                'type'  => 'integer',
                'group' => 'mail',
            ],
            [
                'key'   => 'mail_username',
                'value' => null,
                'type'  => 'string',
                'group' => 'mail',
            ],
            [
                'key'   => 'mail_password',
                'value' => null,
                'type'  => 'string',
                'group' => 'mail',
            ],
            [
                'key'   => 'mail_encryption',
                'value' => 'tls',
                'type'  => 'string',
                'group' => 'mail',
            ],
            [
                'key'   => 'mail_from_address',
                'value' => 'admin@vertex.dev',
                'type'  => 'string',
                'group' => 'mail',
            ],
            [
                'key'   => 'mail_from_name',
                'value' => 'Vertex-Admin',
                'type'  => 'string',
                'group' => 'mail',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
