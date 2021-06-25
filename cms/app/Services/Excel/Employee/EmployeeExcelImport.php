<?php


namespace App\Services\Excel\Employee;

use App\Employee;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;

/**
 * class EmployeeExcelImport
 *
 * @package: App\Services\Excel\Employee
 * @author: Shahsank Jha <shashank.deltatech@gmail.com>
 */


class EmployeeExcelImport implements ToArray
{
    public function array(array $row) {
        // extending the max execution time
        ini_set('max_execution_time', '0');

        // extending the memory limit
        ini_set('memory_limit', '-1');

        // get company Id
        $companyId = config('settings.company_id');

        // initialize empty employees array
        $employees = [];

        // instantiate the FetchDataForEmployee class
        $fetchDataForEmployee = new FetchDataForEmployee($companyId);

        foreach ($row as $key=>$value) if ($key != 0) {
            $employeeName = $value[0];
            $gender = $value[1];
            $phoneNumber = $value[2];
            $designationId = $fetchDataForEmployee->getDesignationId(strtolower($value[3]));
            $superiorId = $fetchDataForEmployee->getSuperiorId(strtolower($value[4]));

            array_push($employees, array('company_id' => $companyId, 'name' => $employeeName, 'gender' => ucwords($gender), 'phone' => $phoneNumber, 'status' => 'Active', 'designation' => $designationId, 'superior' => $superiorId));
        }

        try {
            // begin DB transaction
            DB::beginTransaction();

            // create multiple products
            Employee::insert($employees);

            // commit transaction
            DB::commit();
        } catch (\Exception $error) {
            dd($error->getMessage());
        }
    }
}