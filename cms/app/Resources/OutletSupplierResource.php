<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OutletSupplierResource extends JsonResource
{

    public function __construct($resource) {
        // Ensure we call the parent constructor
        parent::__construct($resource);
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    { 
        return [
          'supplier_id' => $this->id,
          'supplier_name' => $this->company_name,
        ];
    }
}
