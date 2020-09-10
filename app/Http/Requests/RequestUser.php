<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestUser extends FormRequest
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
          'username' => 'required|max:30|unique:users',
          'email' => 'required|max:30|unique:users',
          'dependence_id' => 'nullable',
          'dependencePerson_id' => 'nullable',
          'state' => 'required'
        ];
    }
}
