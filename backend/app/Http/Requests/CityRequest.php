<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('cities', 'name')],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];

        if ($this->method() === 'PUT') {
            $id = $this->route('id');

            $rules['name'] = [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('cities', 'name')->ignore($id),
            ];
        }

        return $rules;
    }

    public function attribute(): array
    {
        return [
            'name' => 'Tên',
            'latitude' => 'Vĩ độ',
            'longitude' => 'Kinh độ',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'unique' => ':attribute này đã tồn tại.',
            'string' => ':attribute phải là chuỗi.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'numeric' => ':attribute phải là số.',
            'between' => ':attribute phải nằm trong khoảng :min đến :max.',
        ];
    }
}
