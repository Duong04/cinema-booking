<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['required', 'boolean'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other']
        ];

        if ($this->method() === 'PUT') {
            $id = $this->route('id');
            $rules['name'] = ['sometimes', 'required', 'string', 'max:255'];
            $rules['email'] = ['sometimes', 'required', 'email', Rule::unique('users', 'email')->ignore($id)];
            $rules['password'] = ['sometimes', 'nullable', 'string', 'min:8'];
            $rules['role_id'] = ['sometimes', 'required', 'exists:roles,id'];
            $rules['is_active'] = ['sometimes', 'required', 'boolean'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên người dùng',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'role_id' => 'Quyền',
            'is_active' => 'Trạng thái hoạt động',
            'phone' => 'Số điện thoại',
            'gender' => 'Giới tính',
        ];
    }

    public function messages(): array
    {
        return [
            'unique' => ':attribute đã tồn tại',
            'exists' => ':attribute không tồn tại',
            'required' => ':attribute là bắt buộc',
            'email' => ':attribute phải là một địa chỉ email hợp lệ',
            'min' => ':attribute phải có ít nhất :min ký tự',
            'boolean' => ':attribute phải là true hoặc false',
            'in' => ':attribute phải là một trong các giá trị: :values',
        ];
    }

}
