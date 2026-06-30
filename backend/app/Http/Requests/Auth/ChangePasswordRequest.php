<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password'      => ['required', 'string'],
            'password'              => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'      => 'Current password is required.',
            'current_password.string'        => 'Current password must be a string.',
            'password.required'              => 'New password is required.',
            'password.string'                => 'New password must be a string.',
            'password.min'                   => 'New password must be at least 8 characters.',
            'password.confirmed'             => 'New password confirmation does not match.',
            'password.different'             => 'New password must be different from the current password.',
            'password_confirmation.required' => 'Password confirmation is required.',
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
