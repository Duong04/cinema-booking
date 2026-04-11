<?php

namespace App\Http\Requests\Upload;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
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
            'file' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'folder' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Vui lòng chọn ảnh cần upload.',
            'file.image' => 'File phải là ảnh.',
            'file.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'file.max' => 'Ảnh không được vượt quá 5MB.',
        ];
    }
}
