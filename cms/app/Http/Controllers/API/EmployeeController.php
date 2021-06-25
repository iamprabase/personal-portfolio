<?php

namespace App\Http\Controllers\API;

use App\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\User;
use Log;

class EmployeeController extends Controller
{
    public function __construct()
	{
		$this->middleware('auth:api');
	}
	
    public function index(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $authEmp = Employee::where('company_id',$company_id)->where('user_id',$user->id)->first();
    	$employees = Employee::select('employees.id','employees.user_id','employees.company_id','employees.is_admin','employees.role','employees.firebase_token','employees.device','employees.imei','employees.name','employees.employee_code','employees.employee_code1','employees.employeegroup','employees.country_code','employees.employeegroup','employees.country_code','employees.alt_country_code','employees.e_country_code','employees.phone','employees.email','employees.firebase_token','employees.image','employees.image_path','employees.b_date','employees.gender','employees.status','employees.client_ids','employees.designation','employees.local_add','employees.per_add','employees.recent_location','employees.superior','employees.e_name','employees.father_name','employees.a_phone','employees.e_phone','employees.e_relation','employees.total_salary','employees.permitted_leave','employees.doj','employees.lwd','employees.acc_holder','employees.acc_number','employees.bank_id','employees.ifsc_code','employees.pan','employees.pan','employees.branch','employees.resume','employees.offer_letter','employees.joining_letter','employees.contract','employees.id_proof','employees.created_at','employeegroups.name as group_name')->where('employees.company_id',$company_id)
            ->leftJoin('employeegroups','employees.employeegroup','employeegroups.id')
            ->where('employees.status','Active')->get();        
    	return response(['status'=>true,'message'=>'employees','data'=>$employees]);
    }

    private function getAllChainUsers($id, array $finalresult=[]){
            $finalresult[]=$id;
            $finalresult=$this->getChainUsers($id,$finalresult);
            $finalresult = $this->getUpChainUsers($id,$finalresult);
            return $finalresult;               
    }

    public function getChainUsers($id, array $finalresult=[]){
            $results = Employee::where('superior', $id)->get();
            foreach( $results as $result){
                $finalresult[]=$result->id;
                $finalresult = $this->getChainUsers($result->id,$finalresult);

            }
            return $finalresult;
    }

    public function getUpChainUsers($id, array $finalresult=[]){

            $results = Employee::where('id', $id)->get();
            foreach( $results as $result){
                $finalresult[]=$result->superior;
                $finalresult = $this->getUpChainUsers($result->superior,$finalresult);
            }
            return $finalresult;                  
    }
}
