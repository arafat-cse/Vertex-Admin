<?php

namespace App\Http\Requests\Role;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role') instanceof \Spatie\Permission\Models\Role
            ? $this->route('role')->id
            : (int) $this->route('role');

        return [
            'name'          => ['sometimes', 'string', 'max:255', 'unique:roles,name,' . $roleId],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string'          => 'Role name must be a string.',
            'name.max'             => 'Role name must not exceed 255 characters.',
            'name.unique'          => 'A role with this name already exists.',
            'permissions.array'    => 'Permissions must be an array.',
            'permissions.*.string' => 'Each permission must be a string.',
            'permissions.*.exists' => 'One or more selected permissions do not exist.',
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
