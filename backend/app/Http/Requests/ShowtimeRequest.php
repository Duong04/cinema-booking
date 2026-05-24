<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowtimeRequest extends FormRequest
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
            'movie_id'   => ['required', 'uuid', 'exists:movies,id'],
            'room_id'    => ['required', 'uuid', 'exists:rooms,id'],
            'show_date'  => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:Y-m-d H:i:s', 'after_or_equal:today'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'prices'     => ['required', 'array', 'min:1'],
            'prices.*.seat_type_id' => ['required', 'uuid', 'exists:seat_types,id'],
            'prices.*.price'        => ['required', 'numeric', 'min:0'],
        ];

        if ($this->method() === 'PUT') {
            $rules['base_price'] = ['sometimes', 'required', 'numeric', 'min:0'];
            $rules['prices'] = ['sometimes', 'required', 'array', 'min:1'];
            $rules['prices.*.seat_type_id'] = ['required', 'uuid', 'exists:seat_types,id'];
            $rules['prices.*.price'] = ['required', 'numeric', 'min:0'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'movie_id'   => 'Phim',
            'room_id'    => 'Rạp',
            'show_date'  => 'Ngày chiếu',
            'start_time' => 'Giờ chiếu',
            'base_price' => 'Giá gốc',
            'prices'     => 'Giá ghế',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'string' => ':attribute phải là chuỗi.',
            'integer' => ':attribute phải là số nguyên.',
            'min' => ':attribute phải lớn hơn hoặc bằng :min.',
            'exists' => ':attribute không tồn tại.',
            'array' => ':attribute phải là mảng.',
            'date' => ':attribute không hợp lệ.',
            'date_format' => ':attribute không hợp lệ.',
            'after_or_equal' => ':attribute phải sau hoặc bằng ngày hiện tại.',
            'numeric' => ':attribute phải là số.',
        ];
    }
}
