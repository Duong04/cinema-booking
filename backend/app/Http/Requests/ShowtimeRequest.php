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
            $rules = [
                'base_price'            => ['sometimes', 'required', 'numeric', 'min:0'],
                'prices'                => ['sometimes', 'required', 'array', 'min:1'],
                'prices.*.seat_type_id' => ['required', 'uuid', 'exists:seat_types,id'],
                'prices.*.price'        => ['required', 'numeric', 'min:0'],
            ];
        }

        return $rules;
    }
}
