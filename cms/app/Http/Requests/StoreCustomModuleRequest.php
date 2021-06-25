<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomModuleRequest extends FormRequest
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
            'name' => ['required',
                Rule::unique('custom_modules')
                    ->where('company_id', config('settings.company_id')
                    )],
            'field_type' => 'required|array',
            'field_type.*' => 'required |distinct'
        ];
    }

    public function messages()
    {
        return [
            'field_type.*' => 'Same Field Should not be repeated',
        ];
    }
}
