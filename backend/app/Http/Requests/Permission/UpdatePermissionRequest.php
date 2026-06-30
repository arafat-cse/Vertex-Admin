<?php

namespace App\Http\Requests\Permission;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $permissionId = $this->route('permission') instanceof \Spatie\Permission\Models\Permission
            ? $this->route('permission')->id
            : (int) $this->route('permission');

        return [
            'name' => ['sometimes', 'string', 'max:255', 'unique:permissions,name,' . $permissionId],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Permission name must be a string.',
            'name.max'    => 'Permission name must not exceed 255 characters.',
            'name.unique' => 'A permission with this name already exists.',
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
