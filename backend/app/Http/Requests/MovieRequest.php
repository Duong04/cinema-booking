<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MovieRequest extends FormRequest
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
            'title'            => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'poster_url'       => ['nullable', 'url', 'max:500'],
            'trailer_url'      => ['nullable', 'url', 'max:500'],
            'description'      => ['nullable', 'string'],
            'content'          => ['nullable', 'string'],
            'release_date'     => ['nullable', 'date'],
            'rating'           => ['nullable', 'string', 'max:10'],
            'language'         => ['nullable', 'string', 'max:50'],
            'status'           => ['required', Rule::in(['coming_soon', 'now_showing', 'ended', 'cancelled'])],
            'genres'           => ['required', 'array', 'min:1'],
            'genres.*'         => ['required', 'uuid', 'exists:genres,id'],
        ];

        if ($this->method() === 'PUT') {
            $id = $this->route('id');

            $rules['title'] = [
                'sometimes',
                'required',
                'string',
                'max:255',
            ];

            $rules['duration_minutes'] = [
                'sometimes',
                'required',
                'integer',
                'min:1',
            ];

            $rules['status'] = [
                'sometimes',
                'required',
                Rule::in(['coming_soon', 'now_showing', 'ended', 'cancelled']),
            ];

            $rules['genres'] = ['sometimes', 'required', 'array', 'min:1'];
            $rules['genres.*'] = ['required', 'uuid', 'exists:genres,id'];
        }

        return $rules;
    }
}
