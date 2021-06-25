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

class AssignLimitedAccessPermission implements ShouldQueue
{
    use Dispatchable, SerializesModels;
    private $company_id;
    private $permission_names;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($companyId, $permissionNames)
    {
      $this->company_id = $companyId;
      $this->permission_names = $permissionNames;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $company_id = $this->company_id;
      $permission_names = $this->permission_names;
      array_push($permission_names, 'odometer-create','odometer-view');
      try{
        $role = Role::whereCompanyId($company_id)->where('name', 'LIKE', 'Limited Access')->whereGuardName('web')->first();
        
        if($role){
          $salesmanPermission = [
              22,41,42,43,36,37,16,17,66,67,1,2,3,4,26,27,31,32,33,34,47,48,50,11,12,13,56,57,81,82,91,92,96,97,102,107,112,111
          ];
          $allLimitedPermissions = Permission::wherePermissionType('Global')->whereIn('id', $salesmanPermission)->orWhere(function($query) use($permission_names){
            $query->whereIn('name', $permission_names);
          })->get();
          $role->syncPermissions($allLimitedPermissions);
          
          $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('role', $role)->pluck('firebase_token');
          if (!empty($fbIDs)) {
            $notificationData = array(
                'company_id' => $company_id,
                'title' => $role->name,
                'created_at' => date('Y-m-d H:i:s'),
                'permissions' => null,
            );
            $sendingNotificationData = $notificationData;
            $sendingNotificationData['unix_timestamp'] = time(); //need to manage server/client 
            sendPushNotification_($fbIDs, 12, null, $sendingNotificationData);
          }
        }
      }catch(\Exception $e){
        Log::error(array("Assign Limited Permission Queue Driver Exception", $e->getMessage()));
      }
    }
}