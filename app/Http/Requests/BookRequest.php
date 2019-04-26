<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Isbn;

class BookRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'isbn' => ['required', 'string', new Isbn],
            'title' => 'required|string|max:150',
            'year' => 'required|digits:4|integer|min:1900|max:'.(date('Y')+1),
            'publisher_id' => 'required|exists:publishers,id',
            'authors' => 'required|exists:authors,id',
        ];
    }

    public function message()
    {
      return[
        'isbn.required' => 'The :attribute number is required.',
        'title.required' => 'Book :attribute is required.',
        'year.required' => 'The :attribute is required.',
        'title.max' => 'The :attribute should be less than 200 characters',
        'year.integer' => 'The :attribute should be integer',
        'publisher_id.required' => 'The :attribute is required.',
        'publisher_id.exists' => 'The :attribute does not exist.',
        'authors.exists' =>  'The :attribute does not exist.',
      ];
    }
}
