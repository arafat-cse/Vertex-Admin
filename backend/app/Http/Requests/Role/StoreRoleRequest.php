<?php

namespace App\Http\Requests\Role;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255', 'unique:roles,name'],
            'guard_name'     => ['nullable', 'string', 'max:255'],
            'permissions'    => ['nullable', 'array'],
            'permissions.*'  => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Role name is required.',
            'name.string'          => 'Role name must be a string.',
            'name.max'             => 'Role name must not exceed 255 characters.',
            'name.unique'          => 'A role with this name already exists.',
            'guard_name.string'    => 'Guard name must be a string.',
            'guard_name.max'       => 'Guard name must not exceed 255 characters.',
            'permissions.array'    => 'Permissions must be an array.',
            'permissions.*.string' => 'Each permission must be a string.',
            'permissions.*.exists' => 'One or more selected permissions do not exist.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('guard_name') || $this->guard_name === null) {
            $this->merge(['guard_name' => 'web']);
        }
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
