<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CinemaRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('cinemas', 'name')],
            'address' => ['required', 'string', 'max:500'],
            'city_id' => ['required', 'uuid', 'exists:cities,id'],
            'cinema_chain_id' => ['required', 'uuid', 'exists:cinema_chains,id'],
        ];

        if ($this->method() === 'PUT') {
            $id = $this->route('id');

            $rules['name'] = [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('cinemas', 'name')->ignore($id),
            ];

            $rules['address'] = [
                'sometimes',
                'required',
                'string',
                'max:500',
            ];

            $rules['city_id'] = [
                'sometimes',
                'required',
                'uuid',
                'exists:cities,id',
            ];

            $rules['cinema_chain_id'] = [
                'sometimes',
                'required',
                'uuid',
                'exists:cinema_chains,id',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên',
            'address' => 'Địa chỉ',
            'city_id' => 'Thành phố',
            'cinema_chain_id' => 'Chi nhánh',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'unique' => ':attribute này đã tồn tại.',
            'string' => ':attribute phải là chuỗi.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'exists' => ':attribute không tồn tại.',
        ];
    }
}
