<?php

namespace App\Http\Requests\API;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class OutletOrderRequest extends FormRequest
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
        'company_id' => 'required|integer',
        'client_id' => 'required|integer',
        'sub_total_amount' => 'required|regex:/^\d+(\.\d{1,})?$/',
        'tax_amount' => 'sometimes|regex:/^\d+(\.\d{1,})?$/',
        'discount' => 'sometimes|regex:/^\d+(\.\d{1,})?$/',
        'grand_total' => 'required|regex:/^\d+(\.\d{1,})?$/',
        // 'order_datetime' => 'required|date_format:Y-m-d H:i:s',
        'product_level_tax' => 'required|integer'
      ];
    }
}
