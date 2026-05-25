<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $promotionId = $this->route('id');

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('promotions', 'code')->ignore($promotionId),
            ],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', 'in:percentage,fixed_amount'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
            'applicable_to' => ['required', 'in:booking,ticket,combo'],
            'status' => ['required', 'in:active,paused,expired'],
        ];
    }
}
