<?php

namespace App\Exports;

use App\Employee;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeExports implements FromArray, WithHeadings
{
    protected $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array 
     */

    public function headings(): array
    {
      return [
          'Employee Name',
          'Pasword',
          'Email',
          'Phone Number'
      ];
    }

    /**
     * @return array
     */
    public function array(): array
    {
      return  $this->data;
    }
}
