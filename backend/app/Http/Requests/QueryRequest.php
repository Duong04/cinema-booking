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
            'genre_id' => 'nullable|uuid|exists:genres,id',
            'show_date' => 'nullable|date',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'city_id' => 'nullable|exists:cities,id',
            'cinema_id' => 'nullable|exists:cinemas,id',
            'cinema_chain_id' => 'nullable|exists:cinema_chains,id',
            'status' => 'nullable|string',
            'applicable_to' => 'nullable|string|in:booking,ticket,combo',
            'sort' => 'nullable|string|in:created_at_desc,best_selling,top_rated,release_date_desc,duration_desc',
            'period' => 'nullable|string|in:7d,30d,all',
            'is_active' => 'nullable|boolean',
            'role_id' => 'nullable|exists:roles,id',
            'ignore_role_id' => 'nullable|exists:roles,id',
        ];
    }
}
