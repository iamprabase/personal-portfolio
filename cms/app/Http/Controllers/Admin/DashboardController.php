<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Manager;
use App\Employee;
use App\user;
use DB;

class DashboardController extends Controller
{

    public function home()
    {
        $companies = Company::all();
        return view('admin.dashboard', ['companies' => $companies]);
    }

    public function mgrtouser(){
        $i=0;
        $disptexts='Records Updated: ';
        $managers=Manager::all();
        foreach($managers as $manager){
            $user = User::find($manager->user_id);
            if($user){
                $company = Company::find($manager->company_id);
                if($company){
                $user->phone = $company->contact_phone;
                $user->company_id = $manager->company_id;
                $user->save();

            $admin= new Employee;
            $admin->name = $user->name;
            $admin->email = $user->email;
            $admin->is_admin = 1;
            $admin->phone = $user->contact_phone;
           // $admin->password = $request->get('password');
            if($user->is_active==2)
                $stat='Active';
            else
            $stat='Inactive'; 
            $admin->status = $stat;
            $save_admin = $admin->save();


                }

            }

            echo $records=$disptexts.$i++;
            echo "</br>";
        }
        echo "Completed";
    }

    public function emptouser(){
        $i=0;
        $disptexts='Records Updated: ';
    	$employees = Employee::where('user_id',0)->get();
    	foreach($employees as $employee){
            if($employee->status=='Active' && $employee->deleted_at==NULL ){
                $is_active= 2;
            }else{
                $is_active= 1;
            }
            
            if($employee->password){
                $password= $employee->password;
            }else{
                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $otp = $this->generate_string($permitted_chars, 8);
                $password= $otp;
            }

            if($employee->deleted_at!=NULL){
                $deleted_at=$employee->deleted_at;
            } else{
                $deleted_at=NULL;
            }
            
            $user = new User;
            $user->company_id = $employee->company_id;
            $user->name = $employee->name;
            $user->phone = $employee->phone;
            $user->email = $employee->email;
            $user->password = bcrypt($password);
            $user->profile_image = $employee->image;
            $user->profile_imagePath = $employee->image_path;
            $user->is_active = $is_active;
            $user->created_at = $employee->created_at;
            $user->updated_at = $employee->updated_at;
            $user->deleted_at = $deleted_at;
            $user->save();

            $user_id= $user->id;

            $employee = Employee::findOrFail($employee->id);
            $employee->user_id=$user_id;
            $employee->password=$password;
            $employee->save();

            echo $records=$disptexts.$i++;
            echo "</br>";
        }
	    echo "Completed";
    }


    public function generate_string($input, $strength = 16)
    {
        $input_length = strlen($input);
        $random_string = '';
        for ($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    public function extractName($name)
{
  // Common/expected prefixes.
  $prefix_list = array(
    'mr',
    'mrs',
    'miss',
    'ms',
    'dr',
    'doctor',
  );

  // Common/expected suffixes.
  $suffix_list = array(
    'md',
    'phd',
    'jr',
    'sr',
    'III',
  );

  $parts = explode(' ', $name);

  // Grab the first name in the string.
  do
  {
    $first_name = array_shift($parts);
  } while ($first_name && in_array(str_replace('.', '', strtolower($first_name)), $prefix_list));

  // If the first name ends with a comma it is actually the last name. Adjust.
  if (strpos($first_name, ',') === (strlen($first_name) - 1))
  {
    $last_name = substr($first_name, 0, strlen($first_name) - 1);
    $first_name = array_shift($parts);

    // Only want the middle initial so grab the next text in the array.
    $middle_name = array_shift($parts);

    // If the text is a suffix clear the middle name.
    if (in_array(str_replace('.', '', strtolower($middle_name)), $suffix_list))
    {
      $middle_name = '';
    }
  }
  else
  {
    // Retrieve the last name if not the leading value.
    do
    {
      $last_name = array_pop($parts);
    } while ($last_name && in_array(str_replace('.', '', strtolower($last_name)), $suffix_list));

    // Only want the middle initial so grab the next text in the array.
    $middle_name = array_pop($parts);
  }


  //return array($first_name, $last_name, substr($middle_name, 0, 1));
  return $first_name.$middle_name.$last_name;
}
}