<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user') instanceof \App\Models\User
            ? $this->route('user')->id
            : (int) $this->route('user');

        return [
            'name'                  => ['sometimes', 'string', 'max:255'],
            'email'                 => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string'],
            'status'                => ['nullable', 'string', 'in:active,inactive,pending'],
            'role_id'               => ['nullable', 'integer', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string'        => 'Name must be a string.',
            'name.max'           => 'Name must not exceed 255 characters.',
            'email.email'        => 'Please provide a valid email address.',
            'email.max'          => 'Email address must not exceed 255 characters.',
            'email.unique'       => 'This email address is already taken.',
            'password.string'    => 'Password must be a string.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'status.in'          => 'Status must be one of: active, inactive, pending.',
            'role_id.integer'    => 'Role ID must be an integer.',
            'role_id.exists'     => 'The selected role does not exist.',
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
