<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QueryRequest extends FormRequest
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
        return [
            'limit' => 'nullable|integer|min:1|max:100',
            'q' => 'nullable|string|max:255',
            'room_id' => 'nullable|uuid|exists:rooms,id',
            'movie_id' => 'nullable|uuid|exists:movies,id',
            'show_date' => 'nullable|date',
            'city_id' => 'nullable|exists:cities,id',
            'cinema_chain_id' => 'nullable|exists:cinema_chains,id',
            'status' => 'nullable|string'
        ];
    }
}
