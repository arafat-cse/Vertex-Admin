<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email', 'max:255'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
            'status'                => ['nullable', 'string', 'in:active,inactive,pending'],
            'role_id'               => ['nullable', 'integer', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                  => 'Name is required.',
            'name.string'                    => 'Name must be a string.',
            'name.max'                       => 'Name must not exceed 255 characters.',
            'email.required'                 => 'Email address is required.',
            'email.email'                    => 'Please provide a valid email address.',
            'email.unique'                   => 'This email address is already taken.',
            'email.max'                      => 'Email address must not exceed 255 characters.',
            'password.required'              => 'Password is required.',
            'password.string'                => 'Password must be a string.',
            'password.min'                   => 'Password must be at least 8 characters.',
            'password.confirmed'             => 'Password confirmation does not match.',
            'password_confirmation.required' => 'Password confirmation is required.',
            'status.in'                      => 'Status must be one of: active, inactive, pending.',
            'role_id.integer'                => 'Role ID must be an integer.',
            'role_id.exists'                 => 'The selected role does not exist.',
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
