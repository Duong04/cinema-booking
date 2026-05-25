<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionRequest extends FormRequest
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
            'name' => ['required', Rule::unique('actions', 'name')],
            'key' => ['required', Rule::unique('actions', 'key')],
            'permissions' => ['nullable', 'array'],
            'permissions.*.permission_id' => ['required', 'exists:permissions,id'],
        ];

        if ($this->method() === 'PUT') {
            $id = $this->route('id');
            $rules['name'] = [
                'sometimes',
                'required',
                Rule::unique('actions', 'name')->ignore($id),
            ];

            $rules['key'] = [
                'sometimes',
                'required',
                Rule::unique('actions', 'key')->ignore($id),
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên hành động',
            'key' => 'Key hành động',
            'permissions' => 'Quyền hạn',
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
