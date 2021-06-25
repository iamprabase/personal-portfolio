<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Support\Facades\DB;
use View;
use App\Client;
use App\Employee;
use App\PartyType;
use App\PermissionCategory;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class PartyTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return redirect()->back();
        $company_id = config('settings.company_id');
        $partytypes = PartyType::where('company_id',$company_id)->where('parent_id',0)->get();
        return view('company.partytypes.index',compact('partytypes'));
    }

    public function store(Request $request)
    {
        $customMessage = [
          'name.regex' => "Party Type name can contain non special characters like '-' and '_' only. "
        ];
        $this->validate($request, [
          'name' => 'required|regex:/^[\w\s-]+$/',
        ], $customMessage);
        try{
            DB::BeginTransaction();

            $company_id = config('settings.company_id');
            $parentId = $request->get('parent_id');
            if($parentId) {
              $findChildrenLevel = PartyType::PartyLevel($parentId, array($parentId));
              if( count($findChildrenLevel) >= config('settings.allowed_party_type_levels') ) return response()->json(['two_party_level_exceeds' => 'Cannot add more than '. config("settings.allowed_party_type_levels"). ' levels of party type.', 'status' => true], 422);
            }
            $partyTypeExists = PartyType::where('company_id',$company_id)->where('name',$request->name)->first();
            if($partyTypeExists)
              return redirect()->back()->with(['error'=>'Party Type already exists.']);
            $input['name']      = $request->get('name');
            $input['short_name']= $request->get('short_name');
            $input['company_id']= $company_id;
            $input['parent_id'] = empty($request->get('parent_id')) ? 0 : $parentId;
            
            $partytype = PartyType::create($input);

            $permissionCategory                           = new PermissionCategory;
            $permissionCategory->company_id               = $company_id;
            $permissionCategory->permission_model_id      = $partytype->id;
            $permissionCategory->permission_model         = 'PartyType';
            $permissionCategory->permission_category_type = 'Company';
            $permissionCategory->name                     = str_replace(' ','_',$partytype->name);
            $permissionCategory->display_name             = 'Party Type: '.$partytype->name;
            $permissionCategory->indexing_priority        = 10;
            $permissionCategory->save();

            $create_id = $this->addPermission($partytype,$permissionCategory,'create');
            $view_id = $this->addPermission($partytype,$permissionCategory,'view');
            $update_id = $this->addPermission($partytype,$permissionCategory,'update');
            $delete_id = $this->addPermission($partytype,$permissionCategory,'delete');
            $status_id = $this->addPermission($partytype,$permissionCategory,'status');
            
            DB::Commit();

            $party_type_permissions = array($create_id, $view_id, $update_id, $delete_id, $status_id);

            $role = Role::whereCompanyId($company_id)->whereName('Full Access')->whereGuardName('web')->first();
            if($role){
              $role_id = $role->id;
              $exist_role_permissions = DB::table('role_has_permissions')->whereRoleId($role_id)->pluck('permission_id')->toArray();
              $non_exist_role_permission = array_merge($party_type_permissions, $exist_role_permissions);
              if(!empty($non_exist_role_permission)){
                $role->syncPermissions($non_exist_role_permission);
              }
            }

            $data = array();
            $non_permitted_users_data = array();

            $permissions = Permission::where('is_mobile',1)->where('permission_category_id', $permissionCategory->id)->where('permission_type','Company')->where('company_id',$company_id)->get();

            foreach($permissions as $permission){
              $data['pt-'.$permission->name] = ($role->hasPermissionTo($permission->id))?'1':'0'; 
              $non_permitted_users_data['pt-'.$permission->name] = '0';
            }

            if($partytype ){
              $fbIDs      = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('role', $role->id)->pluck('firebase_token');
              $partytypes =  PartyType::find($partytype->id);
              $dataPayload= array("data_type" => "partytype", "party_type" => empty($partytypes) ? null : $partytypes, "permissions" => $data, "action" => "add");
              $sent       = sendPushNotification_($fbIDs, 22,null, $dataPayload);

              $fbIds      = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('role', '!=', $role->id)->pluck('firebase_token');
              $dataPayLoad= array("data_type" => "partytype", "party_type" => empty($partytypes) ? null : $partytypes, "permissions" => $non_permitted_users_data, "action" => "add");
              $data_sent       = sendPushNotification_($fbIds, 22,null, $dataPayLoad);
            }
            $data  = [];
            $data['partytypes']= PartyType::where('parent_id', '=', 0)->where('company_id', $company_id)->get();
            $view['tree']      = View::make('company.partytypes.ajaxpartytype', $data)->render();
            $view['partytypes']= PartyType::where('company_id',$company_id)->orderBy('name','ASC')->get();
            return $view;

        }catch(\Exception $e){
            DB::rollback();
            return response(['error'=>'Error : '.$e->getMessage()],500);
        }
    }

    public function update($domain,Request $request,$id){

        
        $customMessage = [
          'party_type.regex' => "Party Type name can contain non special characters like '-' and '_' only. "
        ];
        $this->validate($request, [
          'party_type' => 'required|regex:/^[\w\s-]+$/',
        ]);
        try{

            DB::BeginTransaction();

            $company_id             = config('settings.company_id');
            $partyType              = PartyType::findOrFail($id);
            if(!empty($request->party_parent)){
              $parentId = $request->party_parent;
            }else{
              $parentId = $partyType->parent_id;
            }
            if($parentId) {
              $findChildrenLevel = PartyType::PartyLevel($parentId, array($parentId));
              if( count($findChildrenLevel) >= config('settings.allowed_party_type_levels') ) return response()->json(['two_party_level_exceeds' => 'Cannot add more than'. config('settings.allowed_party_type_levels') .' levels of party type.', 'status' => true], 422);
            }
            $partyTypeExists = PartyType::where('company_id',$company_id)->where('name',$request->party_type)->where('id','!=',$partyType->id)->first();
            if($partyTypeExists)
                return response(['error'=>'Party Type already exists.']);
            $oldPartyType           = $partyType->replicate();
            $data['company_id']     = $company_id;
            $partyType['name']      = $request->party_type;
            $partyType['short_name']= $request->party_type_short_name;
            if(!empty($request->party_parent)){
                $partyType['parent_id'] = $request->party_parent;
            }else{
                $partyType['parent_id'] = $partyType->parent_id;
            }
            
            $partytype = $partyType->save();

            $permissionCategory                   = PermissionCategory::where('permission_model','PartyType')
                                                    ->where('permission_model_id',$partyType->id)->first();
            if(!$permissionCategory){
              $permissionCategory                           = new PermissionCategory;
              $permissionCategory->company_id               = $company_id;
              $permissionCategory->permission_model_id      = $partyType->id;
              $permissionCategory->permission_model         = 'PartyType';
              $permissionCategory->permission_category_type = 'Company';
              $permissionCategory->name                     = str_replace(' ','_',$partyType->name);
              $permissionCategory->display_name             = 'Party Type: '.$partyType->name;
              $permissionCategory->indexing_priority        = 10;
              $permissionCategory->save();
            }
            $oldPermissionCategory                = $permissionCategory;

            $permissionCategory->name             = str_replace(' ','_',$partyType->name);
            
            $permissionCategory->display_name     = 'Party Type: '.$partyType->name;

            $permissionCategory->indexing_priority= 10;

            $permissionCategory->save();
            $this->updatePermission($partyType,$oldPartyType,$oldPermissionCategory,'create');
            $this->updatePermission($partyType,$oldPartyType,$oldPermissionCategory,'view');
            $this->updatePermission($partyType,$oldPartyType,$oldPermissionCategory,'update');
            $this->updatePermission($partyType,$oldPartyType,$oldPermissionCategory,'delete');
            $this->updatePermission($partyType,$oldPartyType,$oldPermissionCategory,'status');
            
            DB::Commit();

            $name = str_replace(' ','-',$permissionCategory->name );
            $permissions = Permission::where('is_mobile',1)->where('permission_category_id', $oldPermissionCategory->id)->where('permission_type','Company')->where('name', 'LIKE', $name.'-%')->where('company_id',$company_id)->get();

            $pdata = array();

            $allroles_with_permission = DB::table('role_has_permissions')->whereIn('permission_id', $permissions->pluck('id')->toArray())->pluck('role_id')->toArray();

            $roles_with_permission = Role::whereIn('id', $allroles_with_permission)->get();
            if($roles_with_permission->first()){              
              $permissions_data = array();
              if($permissions->first()){
                foreach($roles_with_permission as $role){
                  foreach($permissions as $permission){
                    $pdata['pt-'.$permission->name] = ($role->hasPermissionTo($permission->id))?'1':'0'; 
                  }
                  $permissions_data[$role->id] = $pdata;
                }
              }
            }

            if($partytype){               
              // $fbIDs      = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('role', $allroles_with_permission)->pluck('firebase_token', 'role');
              $partytypes =  PartyType::findOrFail($id);
              foreach($allroles_with_permission as $prole){
                $fbIDs      = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('role', $prole)->pluck('firebase_token');
                $dataPayload= array("data_type" => "partytype", "party_type" => empty($partytypes) ? null : $partytypes, "action" => "update", "permissions" => $permissions_data[$prole]);
                $sent       = sendPushNotification_($fbIDs, 22,null, $dataPayload);
              }
            }
            $data['partytypes']= PartyType::where('parent_id', '=', 0)->where('company_id', $data['company_id'])->get();
            $view['tree']      = View::make('company.partytypes.ajaxpartytype', $data)->render();
            $view['partytypes']= PartyType::where('company_id',$data['company_id'])->orderBy('name','ASC')->get();
            
            return $view;

        }catch(\Exception $e){
            DB::rollback();
            return response(['error'=>'Error : '.$e->getMessage()],500);
        }
    }

    public function destroy($domain,Request $request,$id){

        try{
            DB::BeginTransaction();

            $company_id  = config('settings.company_id');
            $partyType   = PartyType::findOrFail($id);
            $clientExists= Client::where('client_type',$partyType->id)->first();
            if($clientExists){
                return response(['status'=>false,'message'=>'This party type has clients and can\'t be deleted.']);
            }
            $data['company_id'] = $company_id;
            if($partyType->company_id == $company_id && $clientExists==null ){

                $permissionCategory = PermissionCategory::where('permission_model','PartyType')
                                        ->where('permission_model_id',$partyType->id)->first();

                $this->destroyPermission($permissionCategory,'create');
                $this->destroyPermission($permissionCategory,'view');
                $this->destroyPermission($permissionCategory,'update');
                $this->destroyPermission($permissionCategory,'delete');
                $this->destroyPermission($permissionCategory,'status');

                $permissionCategory->delete();
                $partytype = $partyType->delete();
                if($partytype ){
                    $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
                    $partytypes =  PartyType::where('company_id', $company_id)->get();
                    $dataPayload = array("data_type" => "partytype", "type_id" => $id, "party_type" => null , "action" => "delete");
                    $sent = sendPushNotification_($fbIDs, 22,null, $dataPayload);
                }
            }

            DB::Commit();
            $data['status'] = true;
            $data['partytypes'] = PartyType::where('parent_id', '=', 0)->where('company_id', $data['company_id'])->get();
            $view['tree'] = View::make('company.partytypes.ajaxpartytype', $data)->render();
            $view['partytypes'] = PartyType::where('company_id',$data['company_id'])->orderBy('name','ASC')->get();
            return $view;

        }catch(\Exception $e){
            DB::rollback();
            return response(['error'=>'Error : '.$e->getMessage().' on '.$e->getLine()],500);
        }
    }

    public function getPartyTypeList(Request $request){
        $data['company_id'] = config('settings.company_id');
        $view['partytypes'] = PartyType::where('company_id',$data['company_id'])->orderBy('name','ASC')->where('id','!=',$request->myId)->get();
        return $view;
    }

    private function addPermission($partytype,$permissionCategory,$permissionTag)
    {
        $stringName                         = str_replace(' ','-',$partytype->name);
        $permission                         = new Permission;
        $permission->permission_category_id = $permissionCategory->id;
        $permission->company_id             = config('settings.company_id');
        $permission->name                   = $stringName.'-'.$permissionTag;
        $permission->guard_name             = 'web';
        $permission->permission_type        = 'Company';
        $permission->enabled                = 1;
        $permission->is_mobile              = 1;
        $permission->save();

        return $permission->id;
    }

    private function updatePermission($partytype,$oldPartyType,$permissionCategory,$permissionTag)
    {
        $company_id      = config('settings.company_id');
        $stringName      = str_replace(' ','-',$oldPartyType->name).'-'.$permissionTag;
        $permission      = Permission::where('company_id',$company_id)->where('permission_category_id',$permissionCategory->id)
                                             ->where('name',$stringName)->first();
        if(!$permission){
          $id = $this->addPermission($partytype,$permissionCategory,$permissionTag);
        }else{
          $permission->name= str_replace(' ','-',$partytype->name).'-'.$permissionTag;
          $permission->save();
        }
    }

    private function destroyPermission($permissionCategory,$permissionTag)
    {
        $company_id= config('settings.company_id');
        $permission= Permission::where('company_id',$company_id)->where('permission_category_id',$permissionCategory->id)
                                             ->first();
        if($permission){
            DB::table('role_has_permissions')->where('permission_id',$permission->id)->delete();
            $permission->delete();
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
}