<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MovieRequest extends FormRequest
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
            'title'            => ['required', 'string', 'max:255', Rule::unique('movies', 'title')],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'poster_url'       => ['nullable', 'url', 'max:500'],
            'trailer_url'      => ['nullable', 'url', 'max:500'],
            'description'      => ['nullable', 'string'],
            'content'          => ['nullable', 'string'],
            'release_date'     => ['nullable', 'date'],
            'rating'           => ['nullable', 'string', 'max:10'],
            'language'         => ['nullable', 'string', 'max:50'],
            'status'           => ['required', Rule::in(['coming_soon', 'now_showing', 'ended', 'cancelled'])],
            'genres'           => ['required', 'array', 'min:1'],
            'genres.*'         => ['required', 'uuid', 'exists:genres,id'],
        ];

        if ($this->method() === 'PUT') {
            $id = $this->route('id');

            $rules['title'] = [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('movies', 'title')->ignore($id)
            ];

            $rules['duration_minutes'] = [
                'sometimes',
                'required',
                'integer',
                'min:1',
            ];

            $rules['status'] = [
                'sometimes',
                'required',
                Rule::in(['coming_soon', 'now_showing', 'ended', 'cancelled']),
            ];

            $rules['genres'] = ['sometimes', 'required', 'array', 'min:1'];
            $rules['genres.*'] = ['required', 'uuid', 'exists:genres,id'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'title'            => 'Tiêu đề',
            'duration_minutes' => 'Thời lượng',
            'poster_url'       => 'Poster',
            'trailer_url'      => 'Trailer',
            'description'      => 'Mô tả',
            'content'          => 'Nội dung',
            'release_date'     => 'Ngày phát hành',
            'rating'           => 'Đánh giá',
            'language'         => 'Ngôn ngữ',
            'status'           => 'Trạng thái',
            'genres'           => 'Thể loại',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'unique' => ':attribute này đã tồn tại.',
            'string' => ':attribute phải là chuỗi.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'integer' => ':attribute phải là số nguyên.',
            'min' => ':attribute phải lớn hơn hoặc bằng :min.',
            'array' => ':attribute phải là mảng.',
            'exists' => ':attribute không tồn tại.',
            'in' => ':attribute không hợp lệ.',
            'url' => ':attribute không hợp lệ.',
            'date' => ':attribute không hợp lệ.',
        ];
    }
}
