<?php

namespace App\Services\Excel\Employee;

use App\Designation;
use App\Employee;
use Illuminate\Support\Facades\DB;

/**
 * class FetchDataForEmployee
 *
 * @package: App\Services\Excel\Employee
 * @author: Shahsank Jha <shashank.deltatech@gmail.com>
 */

class FetchDataForEmployee {

    public $companyId;

    public function __construct($companyId) {
        $this->companyId = $companyId;
    }

    /**
     * @param $designation
     * @return int
     */
    public function getDesignationId($designation) {
        // get id and name of the designation and convert the names into lowercase
        $designations = Designation::companyId($this->companyId)->pluck('name', 'id')->toArray();
        $designations = array_map('strtolower', $designations);

        // define designationId
        $designationId = 0;

        // check if designations exist in the table
        if (in_array($designation, $designations)) {
            // get the key from the designation array
            $designationId =(int) array_search($designation, $designations);
        } else {
            try {
                // start the DB transaction
                DB::beginTransaction();

                // add the designation to the table
                Designation::create([
                    'name' => ucwords($designation),
                    'company_id' => $this->companyId
                ]);

                // commit the transaction
                DB::commit();

                // fetch the newly created designation id
                $designationId =(int) Designation::companyId($this->companyId)->latest()->first()->id;
            } catch (\Exception $error) {
                // rollback the DB transaction
                DB::rollback();
            }
        }

        return $designationId;
    }

    /**
     * @param $superior
     * @return int|null
     */
    public function getSuperiorId($superior) {
        // get id and name of the employees from the Employee table
        $employees = Employee::companyId($this->companyId)->pluck('name', 'id')->toArray();
        $employees = array_map('strtolower', $employees);

        // return key if superior exists else return null
        if (in_array($superior, $employees)) return (int) array_search($superior, $employees);
        return null;
    }

}
