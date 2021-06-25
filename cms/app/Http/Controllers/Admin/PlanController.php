<?php

namespace App\Http\Controllers\Admin;

use App\Plan;
use App\Company;
use App\MainModule;
use App\ClientSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Jobs\AssignFullAccessPermission;
use App\Jobs\AssignLimitedAccessPermission;

class PlanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        $plans = Plan::all()->sortByDesc("created_at");
        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mainmodules = MainModule::orderBy('position','ASC')->get();
        return view('admin.plans.create',compact('mainmodules'));
    }

    public function store(Request $request)
    {
      $customMessages = [
        'name' => 'The Plan Name field is required.',
        'description' => 'The description field is required.',
        // 'users.numeric' => 'Users field should be in number.',
        // 'duration.required' => 'Duration field is required.',
        // 'duration.numeric' => 'Duration field should be in number.',
      ];
      $this->validate($request, [
        'name' => 'required|unique:plans',
        'description' => 'required',
        // 'duration' => 'required|numeric',
      ], $customMessages);

      $plan = new Plan;
      $plan->name = $request->get('name');
      $plan->description = $request->get('description');
      $plan->status = 'Active';
      // $plan->users = $request->get('users');
      // $plan->duration = $request->get('duration');
      // $plan->duration_in = $request->get('duration_in');
      $plan->save();
      $mainmodules = MainModule::all();
      // DB::table('plan_has_modules')->where('plan_id', $plan->id)->delete();
      $insertData = array();
      foreach($mainmodules as $module){
        $field = $module->field;
        $enabled = $request->$field ? 1 : 0;
        $data = array(
          'plan_id'=>$plan->id,
          'module_id'=>$module->id,
          'enabled'=>$enabled
        ); 
        array_push($insertData, $data);
        // DB::table('plan_has_modules')->insert(['plan_id'=>$plan->id,'module_id'=>$module->id,'enabled'=>$enabled]);
        // if($request->$field){
        // }else{
        //     DB::table('plan_has_modules')->insert(['plan_id'=>$plan->id,'module_id'=>$module->id,'enabled'=>0]);
        // }
      }
      if(!empty($insertData)) DB::table('plan_has_modules')->insert($insertData);
      return redirect()->route('app.plan')->with('success', 'Information has been  Added');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plan = Plan::find($id);
        return view('admin.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        $mainmodules = MainModule::orderBy('position','ASC')->get();
        foreach($mainmodules as $module){
            $collect = DB::table('plan_has_modules')->where('plan_id',$id)->where('module_id',$module->id)->pluck('enabled')->first();
            $module->value = $collect;
        }
        return view('admin.plans.edit', compact('plan','mainmodules'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    
    public function update($id, Request $request)
    {
      $customMessages = [
        'name' => 'The Plan Name field is required.',
        'description' => 'The description field is required.',
      ];
      $this->validate($request, [
        'name' => 'required|unique:plans,name,'.$request->id.',id',
        'description' => 'required',
      ], $customMessages);
      
      $plan = Plan::findOrFail($id);
      $plan->name = $request->get('name');
      $plan->description = $request->get('description');
      if($request->has('status')) $plan->status = $request->get('status');
      $plan->save();
      $mainmodules = MainModule::all();
      try{
        $datetime = date('Y-m-d H:i:s');
        $has_tally_integration = $request->has('tally');
        DB::beginTransaction();
        $plans_has_modules = DB::table('plan_has_modules')->where('plan_id', $plan->id)->first();
        if($plans_has_modules) DB::table('plan_has_modules')->where('plan_id', $plan->id)->delete();

        $insertData = array();
        $clientSettingUpdatedArray = array();
        foreach($mainmodules as $module){
          $field = $module->field;
          $enabled = $request->$field ? 1 : 0;
          $clientSettingUpdatedArray[$field] = $enabled;
          $data = array(
            'plan_id'=>$plan->id,
            'module_id'=>$module->id,
            'enabled'=>$enabled
          ); 
          array_push($insertData, $data);
        }
        if(!empty($insertData)) DB::table('plan_has_modules')->insert($insertData);
        $companiesIDs = DB::table('company_plan')->where('plan_id',$plan->id)->pluck('company_id');
        
        if(!empty($companiesIDs)){
          $clientSettings = ClientSetting::whereIn('company_id',$companiesIDs)->get();
          
          foreach($clientSettings as $clientSetting){
            $clientSetting->update($clientSettingUpdatedArray);
            $company_id = $clientSetting->company_id;
           // AssignFullAccessPermission::dispatch($company_id, $clientSetting);
            // $limited_access_assign_permission_name = array('PartyVisit-view', 'PartyVisit-create');
            
            // AssignLimitedAccessPermission::dispatch($company_id, $limited_access_assign_permission_name);

            $tallyInt = DB::table('tally_schedule')->where('company_id', $company_id)->first();
            $companyname = Company::find($company_id);
            
            $domain = $companyname ? $companyname->domain : NULL;
            if($domain){
              if (!$has_tally_integration && !empty($tallyInt)) {
                  $affected = DB::table('tally_schedule')
                      ->where('company_id', $company_id)
                      ->update(['updated_at' => $datetime, 'deleted_at' => $datetime]);
              } elseif ($has_tally_integration) {
                if($tallyInt) $affected = DB::table('tally_schedule')->where('company_id', $company_id)->update(['updated_at' => $datetime, 'deleted_at' => NULL]);
                else $affected = DB::table('tally_schedule')->insert([
                      'company_id' => $company_id,
                      'company_name' => $domain,
                      'created_at' => $datetime]);
              }
            }
          }
        }
        DB::commit();
        if(isset($clientSettings)){
          foreach($clientSettings as $clientSetting){
            $fbIDs = DB::table('employees')->where(array(array('company_id', $clientSetting->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "company_setting", "company_setting" => json_encode($clientSetting), "action" => "update");
            sendPushNotification_($fbIDs, 12, null, $dataPayload);  
          }
        }
        return redirect()->route('app.plan')->with('success', 'Information has been  Updated');
      }catch(\Exception $e){
        DB::rollback();
        dd($e->getMessage());
        Log::error($e->getMessage());
      }
    }

    // private function enableForFullAccess($company_id, $module, $permission_category_name){
    //   $role = Role::whereCompanyId($company_id)->whereName('Full Access')->whereGuardName('web')->first();
    //   $permission_category_party_uploads_modules = DB::table('permission_categories')->where('name', 'LIKE', $permission_category_name)->get();
    //   if($permission_category_party_uploads_modules->first()){
    //     try{
    //       foreach($permission_category_party_uploads_modules as  $permission_category_party_uploads_module){
    //         $permission_category_party_uploads_module_id = $permission_category_party_uploads_module->id;
    //         $permissions_partyuploads = DB::table('permissions')->wherePermissionCategoryId($permission_category_party_uploads_module_id)->pluck('name')->toArray();
    //         foreach($permissions_partyuploads as $permission_name){
    //           $role->givePermissionTo($permission_name);
    //         }
    //       }
    //     }catch(\Exception $e){
    //       Log::info($e->getCode());
    //     }
    //     return true;
    //   }
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $plan = Plan::find($request->id);
        if($plan){
            if(count($plan->companies)>0){
                return redirect()->back()->with('alert','Plan has companies and can not be deleted');
            }
            $plan->delete();
        }
        flash()->success('plan has been deleted.');
        return back();
    }

}
