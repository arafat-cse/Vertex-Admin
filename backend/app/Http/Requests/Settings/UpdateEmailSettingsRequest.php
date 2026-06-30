<?php

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmailSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mail_mailer'        => ['required', 'string', 'in:smtp,sendmail,mailgun,ses,postmark'],
            'mail_host'          => ['required', 'string', 'max:255'],
            'mail_port'          => ['required', 'integer', 'between:1,65535'],
            'mail_username'      => ['nullable', 'string', 'max:255'],
            'mail_password'      => ['nullable', 'string', 'max:255'],
            'mail_from_address'  => ['required', 'email', 'max:255'],
            'mail_encryption'    => ['nullable', 'string', 'in:tls,ssl,null'],
        ];
    }

    public function messages(): array
    {
        return [
            'mail_mailer.required'       => 'Mail mailer is required.',
            'mail_mailer.string'         => 'Mail mailer must be a string.',
            'mail_mailer.in'             => 'Mail mailer must be one of: smtp, sendmail, mailgun, ses, postmark.',
            'mail_host.required'         => 'Mail host is required.',
            'mail_host.string'           => 'Mail host must be a string.',
            'mail_host.max'              => 'Mail host must not exceed 255 characters.',
            'mail_port.required'         => 'Mail port is required.',
            'mail_port.integer'          => 'Mail port must be an integer.',
            'mail_port.between'          => 'Mail port must be between 1 and 65535.',
            'mail_username.string'       => 'Mail username must be a string.',
            'mail_username.max'          => 'Mail username must not exceed 255 characters.',
            'mail_password.string'       => 'Mail password must be a string.',
            'mail_password.max'          => 'Mail password must not exceed 255 characters.',
            'mail_from_address.required' => 'Mail from address is required.',
            'mail_from_address.email'    => 'Please provide a valid from email address.',
            'mail_from_address.max'      => 'Mail from address must not exceed 255 characters.',
            'mail_encryption.string'     => 'Mail encryption must be a string.',
            'mail_encryption.in'         => 'Mail encryption must be one of: tls, ssl, null.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'data'    => null,
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
