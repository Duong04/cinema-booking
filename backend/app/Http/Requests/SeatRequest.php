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
        if ($this->isMethod('put')) {
            $rules = [
                'seat_type_id' => ['required', 'string', 'exists:seat_types,id'],
                'seats_per_row' => ['required', 'integer', 'min:1', 'max:50']
            ];
        } else {
            $rules = [
                'rows' => ['required', 'array', 'min:1'],
                'rows.*.label' => ['required', 'string', 'max:5', 'distinct'],
                'rows.*.seats_per_row' => ['required', 'integer', 'min:1', 'max:50'],
                'rows.*.seat_type_id' => ['required', 'uuid', 'exists:seat_types,id'],
            ];
        }


        return $rules;
    }

    public function attributes(): array
    {
        return [
            'rows' => 'Rạp',
            'seat_type_id' => 'Loại ghế',
            'seats_per_row' => 'Số ghế mỗi hàng',   
            'label' => 'Nhãn',
        ];
    }

    public function messages(): array
    {   
        return [
            'required' => ':attribute là bắt buộc.',
            'string' => ':attribute phải là chuỗi.',
            'integer' => ':attribute phải là số nguyên.',
            'min' => ':attribute phải lớn hơn hoặc bằng :min.',
            'max' => ':attribute phải nhỏ hơn hoặc bằng :max.',
            'exists' => ':attribute không tồn tại.',
            'distinct' => ':attribute phải khác nhau.',
            'array' => ':attribute phải là mảng.',  
        ];
    }
}
