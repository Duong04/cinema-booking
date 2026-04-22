<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'name' => 'required|string',
            'password' => 'required|min:8'
        ];

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'name' => 'Tên',
            'password' => 'Mật khẩu',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'email' => ':attribute không hợp lệ.',
            'string' => ':attribute phải là chuỗi.',
            'min' => ':attribute phải có ít nhất :min ký tự.',
        ];
    }
}
