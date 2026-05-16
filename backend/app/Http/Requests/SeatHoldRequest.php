<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeatHoldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (str_contains($this->path(), 'seat-holds/release')) {
            return [
                'showtime_id' => ['required', 'uuid', 'exists:showtimes,id'],
            ];
        }

        return [
            'showtime_id' => ['required', 'uuid', 'exists:showtimes,id'],
            'seat_ids'    => ['required', 'array', 'min:1', 'max:8'],
            'seat_ids.*'  => ['required', 'uuid', 'exists:seats,id'],
        ];
    }
}
