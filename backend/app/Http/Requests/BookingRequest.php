<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
        $rules = [];

        if ($this->method() === 'PUT') {
            $rules = [
                'cancellation_reason' => ['nullable', 'string', 'max:500'],
            ];
        }else {
            $rules = [
                'showtime_id' => ['required', 'uuid', 'exists:showtimes,id'],
                'seat_ids'    => ['required', 'array', 'min:1', 'max:8'],
                'seat_ids.*'  => ['uuid', 'exists:seats,id']
            ];

        }

        return $rules;
    }
}
