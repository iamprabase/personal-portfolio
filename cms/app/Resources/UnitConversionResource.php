<?php

namespace App\Http\Resources;

use App\Http\Resources\UnitResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitConversionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      $conversion_details = new UnitResource($this->conversionunittypes);
      $converted_details = new UnitResource($this->convertedunittypes);
        return [
          'conversion_quantity' => $this->quantity,
          'conversion_unit_id' => $this->unit_type_id,
          'conversion_unit_details' => $conversion_details,
          'converted_quantity' => $this->converted_quantity,
          'converted_unit_id' => $this->converted_unit_type_id,
          'converted_unit_details' => $converted_details,
        ];
    }
}
