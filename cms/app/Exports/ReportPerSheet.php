<?php

namespace App\Exports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportPerSheet implements FromArray, WithTitle
{
    private $brand_name;
    private $data;

    public function __construct(String $brand_name, array $data)
    {
        $this->brand_name = $brand_name;
        $this->data  = $data;
    }

    /**
     * @return Builder
     */
    public function array():array
    {
      return $this->data;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->brand_name;
    }
}