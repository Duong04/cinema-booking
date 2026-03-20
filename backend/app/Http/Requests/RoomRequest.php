<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
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
            'name' => ['required', Rule::unique('rooms', 'name')],
            'type' => ['required', Rule::in(['2D', '3D', 'IMAX', '4DX', 'VIP'])],
            'cinema_id' => ['required', 'exists:cinemas,id'],
        ];

        if ($this->method() === 'PUT') {
            $id = $this->route('id');

            $rules['name'] = [
                'sometimes',
                'required',
                Rule::unique('rooms', 'name')->ignore($id),
            ];

            $rules['type'] = [
                'sometimes',
                'required',
                Rule::in(['2D', '3D', 'IMAX', '4DX', 'VIP']),
            ];

            $rules['cinema_id'] = [
                'sometimes',
                'required',
                'exists:cinemas,id',
            ];
        }

        return $rules;
    }
}
