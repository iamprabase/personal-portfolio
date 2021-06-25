<?php

namespace App\Jobs;

use App\Company;
use App\PartyType;
use App\Designation;
use Illuminate\Bus\Queueable;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DefaultLevelValues implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $companies = Company::with('clientsettings')->get(['id', 'company_name']);
      
      foreach($companies as $company){
        try{

          Log::info(array("Company:- ", $company));
          $client_setting = $company->clientsettings;
          if(!$client_setting) continue;
          $num_user_roles = Role::whereCompanyId($company->id)->count();
          $party_hierarchy_level = PartyType::whereCompanyId($company->id)
                                    ->where('parent_id', '<>', 0)
                                    ->count();
          if($party_hierarchy_level > 0){
            $num_party_hierarchy_level = PartyType::whereCompanyId($company->id)->orderBy('parent_id', 'desc')->CompanyMaxPartyTypeLevel();
          }else{
            $num_party_hierarchy_level = 1;
          }
          $designation_hierarchy_level = Designation::whereCompanyId($company->id)
                                          ->where('parent_id', '<>', 0)
                                          ->count();
          if($designation_hierarchy_level > 0){
            $num_designation_hierarchy_level = Designation::whereCompanyId($company->id)->orderBy('parent_id', 'desc')->CompanyMaxDesignationLevel();
          }else{
            $num_designation_hierarchy_level = 2;
          }
  
          Log::info(array(
            "NUM USER ROLES :- " . $num_user_roles,
            "MAX NUM PARTY TYPE HIERARCHY LEVEL :- " . $num_party_hierarchy_level,
            "MAX NUM DESIGNATION LEVEL :- " . $num_user_roles,
            
          ));
  
          $client_setting->update([
            'user_hierarchy_level' => $num_designation_hierarchy_level,
            'allowed_party_type_levels' => $num_party_hierarchy_level,
            'user_roles' => $num_user_roles
          ]);
        }catch(\Exception $e){
          Log::info(array("Error Updating Defaulting:-".$compay->id));
        }
      }
    }
}
