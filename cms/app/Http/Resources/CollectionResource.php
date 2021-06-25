<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $currency = $this->company->clientsettings->currency_symbol;
        return [
          'salesman_name' => $this->employees->name,
          'payment_amount' => $currency.' '.$this->payment_received,
          'due' => $this->due_payment,
          'payment_method' => $this->payment_method,
          'payment_date' => $this->payment_date,
          'cheque_date' => $this->payment_method=='Cheque'?date('d/m/Y', strtotime($this->cheque_date)):null,
          'bank_name' => $this->payment_method=='Cheque' && $this->bank?$this->bank->name:null,
          'status' => $this->payment_status,
        ];
    }
}
