<?php

namespace App\Http\Requests\Upload;

use Illuminate\Foundation\Http\FormRequest;

class UploadMultipleRequest extends FormRequest
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
        return [
            'files' => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => ['required', 'file', 'max:10240'],
            'folder' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'files.required' => 'Vui lòng chọn ít nhất một file.',
            'files.array' => 'Dữ liệu files không hợp lệ.',
            'files.min' => 'Vui lòng chọn ít nhất 1 file.',
            'files.max' => 'Chỉ được upload tối đa 10 file cùng lúc.',
            'files.*.file' => 'Mỗi phần tử phải là một file hợp lệ.',
            'files.*.max' => 'Mỗi file không được vượt quá 10MB.',
        ];
    }
}
