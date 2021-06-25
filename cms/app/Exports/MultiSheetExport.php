<?php

namespace App\Exports;

use App\Exports\ReportPerSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->data as $brand_name=>$data) {
          $sheets[] = new ReportPerSheet($brand_name, $data);
        }

        return $sheets;
    }
}