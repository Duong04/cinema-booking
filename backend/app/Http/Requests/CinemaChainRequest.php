<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CinemaChainRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('cinema_chains', 'name')],
            'logo' => ['required', 'string'],
        ];

        if ($this->method() === 'PUT') {
            $id = $this->route('id');

            $rules['name'] = [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('cinema_chains', 'name')->ignore($id),
            ];

            $rules['logo'] = [
                'sometimes',
                'required',
                'string',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên',
            'logo' => 'Logo',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'unique' => ':attribute này đã tồn tại.',
            'string' => ':attribute phải là chuỗi.',
            'max' => ':attribute không được vượt quá :max ký tự.',
        ];
    }
}
