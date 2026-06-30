<?php

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name'  => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'email', 'max:255'],
            'timezone'      => ['required', 'string', 'timezone'],
            'date_format'   => ['required', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required'  => 'Company name is required.',
            'company_name.string'    => 'Company name must be a string.',
            'company_name.max'       => 'Company name must not exceed 255 characters.',
            'company_email.required' => 'Company email is required.',
            'company_email.email'    => 'Please provide a valid company email address.',
            'company_email.max'      => 'Company email must not exceed 255 characters.',
            'timezone.required'      => 'Timezone is required.',
            'timezone.string'        => 'Timezone must be a string.',
            'timezone.timezone'      => 'Please provide a valid timezone.',
            'date_format.required'   => 'Date format is required.',
            'date_format.string'     => 'Date format must be a string.',
            'date_format.max'        => 'Date format must not exceed 20 characters.',
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
