<?php

namespace App\Http\Requests\Permission;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'guard_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Permission name is required.',
            'name.string'       => 'Permission name must be a string.',
            'name.max'          => 'Permission name must not exceed 255 characters.',
            'name.unique'       => 'A permission with this name already exists.',
            'guard_name.string' => 'Guard name must be a string.',
            'guard_name.max'    => 'Guard name must not exceed 255 characters.',
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
