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

        if ($this->is('api/v1/bookings/*/cancel')) {
            $rules = [
                'cancellation_reason' => ['nullable', 'string', 'max:500'],
            ];
        } elseif ($this->method() === 'PUT') {
            $rules = [
                'status' => ['nullable', 'in:pending,confirmed,cancelled,expired,refunded'],
                'cancellation_reason' => ['nullable', 'string', 'max:500'],
                'expired_at' => ['nullable', 'date'],
                'confirmed_at' => ['nullable', 'date'],
            ];
        } else {
            $rules = [
                'showtime_id' => ['required', 'uuid', 'exists:showtimes,id'],
                'seat_ids'    => ['required', 'array', 'min:1', 'max:8'],
                'seat_ids.*'  => ['uuid', 'exists:seats,id'],
                'combos' => ['nullable', 'array'],
                'combos.*.combo_id' => ['required_with:combos', 'uuid', 'exists:combos,id'],
                'combos.*.quantity' => ['required_with:combos', 'integer', 'min:1', 'max:20'],
                'promotion_code' => ['nullable', 'string', 'max:255'],
            ];
        }

        return $rules;
    }
}
