<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255'],
            'ticket_amount' => ['nullable', 'numeric', 'min:0'],
            'combo_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
