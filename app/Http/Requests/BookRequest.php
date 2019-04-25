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
            'isbn' => ['required', 'string', 'unique:books', new Isbn],
            'title' => 'required|string|max:150',
            'year' => 'required|digits:4|integer|min:1900|max:'.(date('Y')+1),
            'publisher' => 'required|exists:publishers, id',
            'author' => 'required|exists:authors, id',
        ];
    }

    public function message()
    {
      'isbn.required' => 'The :attribute number is required.',
      'title.required' => 'Book :attribute is required.',
      'year.required' => 'The :attribute is required.',
      'isbn.unique' => 'The :attribute is duplicated.',
      'title.max' => 'The :attribute should be less than 200 characters',
      'year.integer' => 'The :attribute should be integer',
      'publisher.required' => 'The :attribute is required.',
      'publisher.exists' => 'The :attribute does not exist.',
      'author.exists' =>  'The :attribute does not exist.',
    }
}
