<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeatRequest extends FormRequest
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
            'rows'                   => ['required', 'array', 'min:1'],
            'rows.*.label'           => ['required', 'string', 'max:5', 'distinct'],
            'rows.*.seats_per_row'   => ['required', 'integer', 'min:1', 'max:50'],
            'rows.*.seat_type_id'    => ['required', 'uuid', 'exists:seat_types,id'],
        ];
        
        return $rules;
    }
}
