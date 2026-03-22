<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeatTypeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('seat_types', 'name')],
            'base_multiplier' => ['required', 'numeric', 'min:0.01', 'max:999.99'],
        ];

        if ($this->method() === 'PUT') {
            $id = $this->route('id');

            $rules['name'] = [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('seat_types', 'name')->ignore($id),
            ];

            $rules['base_multiplier'] = [
                'sometimes',
                'required',
                'numeric', 'min:0.01', 'max:999.99'
            ];
        }

        return $rules;
    }
}
