<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', Rule::unique('roles', 'name')],
            'description' => ['nullable'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*.id' => ['required', 'exists:permissions,id'],

            'permissions.*.actions' => ['sometimes', 'array'],
            'permissions.*.actions.*.id' => ['required', 'exists:actions,id'],
        ];

        if ($this->method() === 'PUT') {
            $id = $this->route('id');
            $rules['name'] = [
                'sometimes',
                'required',
                Rule::unique('roles', 'name')->ignore($id),
            ];
        }

        return $rules;
    }
}
