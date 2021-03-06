<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublisherRequest extends ApiFormRequest
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
          'name' => 'required|string|max:100',
        ];
    }

    public function message()
    {
        return [
          'name.required' => 'The :attribute is required.',
          'name.string' => 'The :attribute must be string.',
          'name.max' => 'The :attribute must not consist of more than 100 characters.',
        ];
    }
}
