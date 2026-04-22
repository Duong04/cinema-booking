<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
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
            'name' => ['required', Rule::unique('permissions', 'name')],
            'key' => ['required', Rule::unique('permissions', 'key')],
            'actions' => ['nullable', 'array'],
        ];

        if ($this->method() === 'PUT') {
            $id = $this->route('id');
            $rules['name'] = [
                'sometimes',
                'required',
                Rule::unique('permissions', 'name')->ignore($id),
            ];

            $rules['key'] = [
                'sometimes',
                'required',
                Rule::unique('permissions', 'key')->ignore($id),
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên',
            'key' => 'Key',
            'actions' => 'Hành động',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'unique' => ':attribute này đã tồn tại.',
            'array' => ':attribute phải là mảng.',
        ];
    }
}
