<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Employee;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
    	$this->middleware('auth:api');
    }

    public function index()
    {
    	$user = Auth::user();
      $company_id = $user->company_id;
      $role = Employee::where('company_id', $company_id)->where('user_id', $user->id)->first()->role;
      $role_instance = Role::where('id',$role)->where('company_id',$company_id)->first();
    	$data['permissions'] = [];    
        $permissions = Permission::where('is_mobile',1)->where(function($q)use($company_id){
            $q = $q->where('permission_type','Global')->orWhere('company_id',$company_id);
        })->get();
        foreach($permissions as $permission){
          if($permission->permission_type=='Company')
            $data['permissions']['pt-'.$permission->name] = ($role_instance->hasPermissionTo($permission->id))?'1':'0'; 
          else
            $data['permissions'][$permission->name] = ($role_instance->hasPermissionTo($permission->id))?'1':'0';
        }
        return response(['status'=>true,'data'=>$data]);
    }
}