<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Permission\Models\Permission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AssignFullAccessPermission implements ShouldQueue
{
    use Dispatchable, SerializesModels;
    private $company_id;
    private $client_setting;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($companyId, $clientSetting)
    {
      $this->company_id = $companyId;
      $this->client_setting = $clientSetting;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $company_id = $this->company_id;
      $clientSetting = $this->client_setting;
      try{
        $roles = array();
        $role = Role::whereCompanyId($company_id)->where('name', 'LIKE', 'Full Access')->whereGuardName('web')->first();
        if($role){
          array_push($roles, $role->id);

          $allGlobalPermissions = Permission::wherePermissionType('Global')->orWhere(function($query) use($company_id){
            $query->where('company_id', $company_id);
          })->get();
          $role->syncPermissions($allGlobalPermissions);
        }

        $permission_names = array('odometer-create','odometer-view', 'PartyVisit-view', 'PartyVisit-create');
        $salesmanPermission = [
          22,41,42,43,36,37,16,17,66,67,1,2,3,4,26,27,31,32,33,34,47,48,50,11,12,13,56,57,81,82,91,92,96,97,102,107,112,111
        ];
        $roleLimited = Role::whereCompanyId($company_id)->where('name', 'LIKE', 'Limited Access')->whereGuardName('web')->first();
        if($roleLimited){
          array_push($roles, $roleLimited->id);
          
          $allLimitedPermissions = Permission::wherePermissionType('Global')->whereIn('id', $salesmanPermission)->orWhere(function($query) use($permission_names){
            $query->whereIn('name', $permission_names);
          })->get();
          $roleLimited->syncPermissions($allLimitedPermissions);
        }

        if($clientSetting && !empty($roles)){
          $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('role', $roles)->pluck('firebase_token');
          $dataPayload = array("data_type" => "company_setting", "company_setting" => json_encode($clientSetting), "action" => "update");
          sendPushNotification_($fbIDs, 12, null, $dataPayload);
        }
        if(!empty($roles)){
          $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('role', $roles)->pluck('firebase_token');
          if (!empty($fbIDs)) {
            $notificationDataLimited = array(
                'company_id' => $company_id,
                'title' => $roleLimited->name,
                'created_at' => date('Y-m-d H:i:s'),
                'permissions' => null,
                'unix_timestamp' => time()
            );
            sendPushNotification_($fbIDs, 12, null, $notificationDataLimited);

            $notificationDataFull = array(
              'company_id' => $company_id,
              'title' => $role->name,
              'created_at' => date('Y-m-d H:i:s'),
              'permissions' => null,
              'unix_timestamp' => time()
            );
            sendPushNotification_($fbIDs, 12, null, $notificationDataFull);
          }

        }

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
      }catch(\Exception $e){
        Log::error(array("Assign Permission Queue Driver Exception", $e->getMessage()));
      }
    }
}