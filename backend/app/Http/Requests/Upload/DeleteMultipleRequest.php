<?php

namespace App\Http\Requests\Upload;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleRequest extends FormRequest
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
            'urls' => ['required', 'array', 'min:1'],
            'urls.*' => ['required', 'string', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'urls.required' => 'Vui lòng cung cấp danh sách URL cần xóa.',
            'urls.array' => 'Dữ liệu urls không hợp lệ.',
            'urls.min' => 'Vui lòng cung cấp ít nhất 1 URL.',
            'urls.*.url' => 'Một hoặc nhiều URL không hợp lệ.',
        ];
    }
}
