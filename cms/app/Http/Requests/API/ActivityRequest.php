<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ActivityRequest extends FormRequest
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
        $validation = [];
        $validation['type'] = 'required|string';
        $validation['title'] = 'required';
        $validation['note'] = 'max:500';
        $validation['start_datetime'] = 'required|date_format:Y-m-d H:i:s';
        $validation['priority'] = 'required|string';
        $validation['assigned_to'] = 'required';
        return $validation;
    }

    public function messages()
    {
        return [
            'type.required' => 'Type is required.',
            'type.string' => 'Type must be interger value',
            'title.required' => 'Title is required.',
            'title.min' => 'Title must be minimum 3 characters',
            'title.max' => 'Title must be maximum 191 characters',
            'note.max' => 'Note must be maximum 500 characters',
            'start_datetime.required' => 'Date is required.',
            'duration.required' => 'Duration is required.',
            'priority.required' => 'Priority is required.',
            'assigned_to.required' => 'Assign To is required.',
        ];
    }
}
