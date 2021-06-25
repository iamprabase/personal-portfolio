<?php

namespace App\Http\Requests\API;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class OutletRequest extends FormRequest
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
      if ($this->getMethod() == 'POST') {
        return [
          'email' => 'nullable|email|regex:/gmail/',
          'outlet_name' => 'required'
        ];
      }elseif($this->getMethod() == 'PATCH'){
        return [
          'email' => 'nullable|email|regex:/gmail/|unique:outlets,email,'.$this->outlet->id,
          'outlet_name' => 'required',
          'contact_person' => 'required',
          'country' => 'required',
          'city' => 'required',
          'phone_ext' => 'required',
          'phone' => 'required',
          'email' => 'required',
          'address' => 'required',
          'gps_location' => 'required',
        ];
      }
    }

    public function messages()
    {
        return [
          'email.regex' => 'Gmail sign in is only allowed.'
        ];
    }
}
