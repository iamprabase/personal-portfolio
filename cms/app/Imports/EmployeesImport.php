<?php

namespace App\Imports;

use App\Employee;
use App\User;
use App\Bank;
use App\Designation;
use App\EmployeeGroup;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;


class EmployeesImport implements ToModel, WithHeadingRow, WithValidation
{ 
   use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
      $company_id = config('settings.company_id');
      $isadmin=0;
      $empgroupid='';
      $bankid='';
      $superiorid='';
      $email='';
      $phonecode='';        
      $phone='';
      $password='';
      $designationid='';
      $birthdate='';
      $gender='';
      $fathername='';
      $employeecode='';
      $employeegroup='';
      $totalsalary='';
      $permittedleave='';
      $joiningdate='';
      $lastworkingday='';
      $contactphonecode='';
      $alternatephone='';
      $localaddress='';
      $permanentaddress='';
      $emergencycontactname='';
      $relationtoyou='';
      $emergencyphonecode='';
      $emergencyphone='';
      $accountholdername='';
      $accountnumber='';
      $ifsccode='';
      $pan='';
      $branch='';

      $phone = Employee::where('company_id',$company_id)->where('phone',$row['phone'])->whereNull('deleted_at')->first();
      $phone2 = User::where('company_id',$company_id)->where('phone',$row['phone'])->whereNull('deleted_at')->first();
      if(empty($phone) && empty($phone2)){
        if($row['role']!=''){
          $role = Role::where('company_id',$company_id)->where('name',$row['role'])->first();
          if($role){
            $roleid=$role->id;
          }else{
            $roleadd = new Role;
            $roleadd->name = $row['role'];
            $roleadd->company_id = $company_id;
            $roleadd->save();
            $roleid=$roleadd->id;
          }
        }

        if(isset($row['employeegroup'])){
          if($row['employeegroup']!=''){
            $empData = EmployeeGroup::where('company_id',$company_id)->where('name',$row['employeegroup'])->first(); 
            if($empData){
              $empgroupid=$empData->id;
            }else{
              $employeegroup = new EmployeeGroup;
              $employeegroup->company_id = $company_id;
              $employeegroup->name = $row['employeegroup'];
              $employeegroup->status = 'Active';
              $employeegroup->save();
              $empgroupid=$employeegroup->id;
            }   
          }
        }

        if(isset($row['bankname'])){
          if($row['bankname']!=''){
            $bank = Bank::where('company_id',$company_id)->where('name',$row['bankname'])->first();
            if($bank){
              $bankid=$bank->id;
            }else{
              $bankadd = new Bank;
              $bankadd->name = $row['bankname'];
              $bankadd->company_id = $company_id;
              $bankadd->save();
              $bankid=$bankadd->id;
            }
          }
        }
      
        if(isset($row['designation'])){
          if($row['designation']!=''){
            $designation = Designation::where('company_id',$company_id)->where('name',$row['designation'])->first();
            if($designation){
              $designationid=$designation->id;
              if($designation->name=='Admin'){
                $isadmin=1;
              }
            }else{
              $designation = Designation::where('company_id',$company_id)->where('name','Admin')->first();
              $superior=$designation->id;
              $isadmin=0;
              $designationadd= new Designation;
              $designationadd->name=$row['designation'];
              $designationadd->parent_id = $superior;
              $designationadd->company_id=$company_id;    
              $designationadd->save();
              $designationid=$designationadd->id;
            }
          }
        }

        $user = new User;
        $user->email = $row['email'];
        $user->name = $row['name'];
        $user->phone = $row['phone'];
        $user->company_id= $company_id;
        $user->is_active = 1;
        $user->password = bcrypt($row['password']);
        $user->save();
        $userid=$user->id;
        $user->assignRole($roleid);
      
        if(isset($row['superior'])){
          if($row['superior']!=''){ 
            $superior = Employee::where('company_id',$company_id)->where('name',$row['superior'])->first();
            
            if($superior){
              $superiorid=$superior->id;
            }
          }
        }

        if(isset($row['email'])){
          $email=$row['email'];
        }
        if(isset($row['phonecode'])){
          $phonecode=$row['phonecode'];
        }
        if(isset($row['phone'])){
          $phone=$row['phone'];
        }
        if(isset($row['password'])){
          $password=$row['password'];
        }
        if(isset($row['birthdate'])){
          $birthdate=$row['birthdate'];
        }
        if(isset($row['gender'])){
          $gender=$row['gender'];
        }
        if(isset($row['fathername'])){
          $fathername=$row['fathername'];
        }
        if(isset($row['employeecode'])){
          $employeecode=$row['employeecode'];
        }
         if(isset($row['employeegroup'])){
          $employeegroup=$row['employeegroup'];
        }
         if(isset($row['totalsalary'])){
          $totalsalary=$row['totalsalary'];
        }
         if(isset($row['permittedleave'])){
          $permittedleave=$row['permittedleave'];
        }
         if(isset($row['joiningdate'])){
          $joiningdate=$row['joiningdate'];
        }
         if(isset($row['lastworkingday'])){
          $lastworkingday=$row['lastworkingday'];
        }
         if(isset($row['contactphonecode'])){
          $contactphonecode=$row['contactphonecode'];
        }
        if(isset($row['alternatephone'])){
          $alternatephone=$row['alternatephone'];
        }
         if(isset($row['localaddress'])){
          $localaddress=$row['localaddress'];
        }
         if(isset($row['permanentaddress'])){
          $permanentaddress=$row['permanentaddress'];
        }
         if(isset($row['emergencycontactname'])){
          $emergencycontactname=$row['emergencycontactname'];
        }
         if(isset($row['relationtoyou'])){
          $relationtoyou=$row['relationtoyou'];
        }
         if(isset($row['emergencyphonecode'])){
          $emergencyphonecode=$row['emergencyphonecode'];
        }
         if(isset($row['emergencyphone'])){
          $emergencyphone=$row['emergencyphone'];
        }
        if(isset($row['accountholdername'])){
          $accountholdername=$row['accountholdername'];
        }
         if(isset($row['accountnumber'])){
          $accountnumber=$row['accountnumber'];
        }
         if(isset($row['ifsccode'])){
          $ifsccode=$row['ifsccode'];
        }
         if(isset($row['pan'])){
          $pan=$row['pan'];
        }
         if(isset($row['branch'])){
          $branch=$row['branch'];
        }

                 
        return new Employee(['user_id'=>$userid,'company_id'=>$company_id,'name'=>$row['name'],'email'=>$email,'country_code'=>$phonecode,'phone'=>$phone,'password'=>$password,'role'=>$roleid,'designation'=>$designationid,'superior'=>$superiorid,'b_date'=>$birthdate,'gender'=>$gender,'father_name'=>$fathername,'employee_code'=>$employeecode,'employeegroup'=>$employeegroup,'total_salary'=>$totalsalary,'permitted_leave'=>$permittedleave,'doj'=>$joiningdate,'lwd'=>$lastworkingday,'alt_country_code'=>$contactphonecode,'a_phone'=>$alternatephone,'local_add'=>$localaddress,'per_add'=>$permanentaddress,'e_name'=>$emergencycontactname,'e_relation'=>$relationtoyou,'e_country_code'=>$emergencyphonecode,'e_phone'=>$emergencyphone,'acc_holder'=>$accountholdername,'acc_number'=>$accountnumber,'bank_id'=>$bankid,'ifsc_code'=>$ifsccode,'pan'=>$pan,'branch'=>$branch,'status'=>'Active']);

    }
  }

  public function rules(): array
    {
        return [
            'name' => 'required|string',
            'phonecode' => 'required',
            'phone' => 'unique:employees,phone,NULL,deleted_at',
            'role' => 'required',
            'designation' => 'required',
            'superior' => 'required|string',
            'password' => 'required',
        ];
    }
}
