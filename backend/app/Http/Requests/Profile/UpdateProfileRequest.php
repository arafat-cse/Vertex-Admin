<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Name is required.',
            'name.string'    => 'Name must be a string.',
            'name.max'       => 'Name must not exceed 255 characters.',
            'email.required' => 'Email address is required.',
            'email.email'    => 'Please provide a valid email address.',
            'email.max'      => 'Email address must not exceed 255 characters.',
            'email.unique'   => 'This email address is already in use by another account.',
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
