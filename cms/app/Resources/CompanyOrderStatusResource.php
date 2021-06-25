<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyOrderStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
          "id" => $this->id,
          "status" => $this->title,
          "color" => $this->color,
          "color" => $this->color,
          "order_edit_flag" => $this->order_edit_flag,
          "order_delete_flag" => $this->order_delete_flag,
        ];
    }
}
