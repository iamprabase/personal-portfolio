<?php

namespace App\Imports;

use DB;
use Log;
use App\Beat;
use App\Client;
use App\Employee;
use App\PartyType;
use App\BusinessType;
use App\PermissionCategory;
use App\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientsImport implements ToModel, WithHeadingRow, WithValidation
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
    $is_categoryrate = config('settings.category_wise_rate_setup');
    $partytypeid='';
    $superiorid='NULL';
    $businessid='';
    $countryid='';
    $stateid='';
    $cityid='';
    $pin='';
    $partycode='';
    $pan='';
    $aboutcompany='';
    $website='';
    $email='';
    $phonecode='';
    $phone='';
    $mobile='';
    $addressline1='';
    $addressline2='';
    $openingbalance='';
    $closingbalance='';
    $creditlimit='';
    $creditdays=0;
    $latitude='';
    $longitude='';
    $beatid='';
   $category_rate_type_ids=[];
    $category_rate_type_id='';
   
    if($is_categoryrate){
      foreach($row as $key=>$value){
        if(strpos($key, 'category_') !== false){
          $categoryname=trim(str_replace('category_','',$key));
          $checkcategoryname = Category::where('company_id',$company_id)
          ->where('name','LIKE',$categoryname)->first();
          if(!empty($checkcategoryname) && $row[$key]!=''){   
            $category_rate_type= DB::table('category_rate_types')
            ->where('category_id', $checkcategoryname->id)->where('name',$row[$key])->first();
            if($category_rate_type){
                $category_rate_type_id[]=$category_rate_type->id;
            }
          }
        }
      }
      $category_rate_type_ids=$category_rate_type_id;
    }

    if(isset($row['businesstype'])){
      if($row['businesstype']!=''){
        $businesstype = BusinessType::where('company_id',$company_id)->where('business_name',$row['businesstype'])->first();
        if($businesstype){
          $businessid=$businesstype->id;
        }else{
          $businesstypeadd = new BusinessType;
          $businesstypeadd->business_name = $row['businesstype'];
          $businesstypeadd->company_id = $company_id;
          $businesstypeadd->save();
          $businessid=$businesstypeadd->id;
        }
      }
    }

    if(isset($row['partytype'])){
      if($row['partytype']!=''){
        $partytype = PartyType::where('company_id',$company_id)->where('name',$row['partytype'])->first();
        if($partytype){
          $partytypeid=$partytype->id;
        }else{
          $partytype = PartyType::where('company_id',$company_id)->where('name','Super Distributor')->first();
          $partytypeadd = new PartyType;
          // if(!preg_match("/^[\w\s-]+$/", $row['partytype'])){
          //   $row['partytype'] = preg_replace("/[\/\&%#@()\$]/", "-", $row['partytype']);
          //   $row['partytype'] = preg_replace ("/[\"\']/", "-", $row['partytype']);
          // }
          $partytypeadd->name = trim(preg_replace('/[^a-zA-Z0-9_.]/', '-', str_replace(')','',$row['partytype'])));
          $partytypeadd->parent_id = 0;
          $partytypeadd->company_id = $company_id;
          $partytypeadd->save();
          $partytypeid=$partytypeadd->id;

          $permissionCategory                           = new PermissionCategory;
          $permissionCategory->company_id               = $company_id;
          $permissionCategory->permission_model_id      = $partytypeid;
          $permissionCategory->permission_model         = 'PartyType';
          $permissionCategory->permission_category_type = 'Company';
          $permissionCategory->name                     = str_replace(' ','_',$partytypeadd->name);
          $permissionCategory->display_name             = 'Party Type: '.$partytypeadd->name;
          $permissionCategory->indexing_priority        = 5;
          $permissionCategory->save();

          $create_id = $this->addPermission($partytypeadd,$permissionCategory,'create');
          $view_id = $this->addPermission($partytypeadd,$permissionCategory,'view');
          $update_id = $this->addPermission($partytypeadd,$permissionCategory,'update');
          $delete_id = $this->addPermission($partytypeadd,$permissionCategory,'delete');
          $status_id = $this->addPermission($partytypeadd,$permissionCategory,'status');
        }
      }
    }

    if(isset($row['country'])){
      if($row['country']!=''){
        $country  = DB::table('countries')->where('name', $row['country'])->first();
        if($country){
          $countryid=$country->id;
        }
      }
    }

    if(isset($row['state'])){
      if($row['state']!=''){
        $state = DB::table('states')->where('name',$row['state'])->first();
        if($state){
          $stateid=$state->id;
        }
      }
    }

    if(isset($row['city'])){
      if($row['city']!=''){
        $city = DB::table('cities')->where('name',$row['city'])->first();
        if($city){
          $cityid=$city->id;
        }
      }
    }

    if(isset($row['beat'])){
      $beat = Beat::where('company_id',$company_id)->where('name',$row['beat'])->first();
      if($beat){
        $beatid=$beat->id;
      }else{
        $newbeat = new Beat;
        $newbeat->name = $row['beat'];
        $newbeat->company_id = $company_id;
        $newbeat->city_id = $cityid;
        $newbeat->status = 'Active';
        $newbeat->save();
        $beatid=$newbeat->id;
      }
    }

    if(isset($row['creditdays'])){
      if($row['creditdays']!=''){
        $creditdays=$row['creditdays'];
      }
    }

    if(isset($row['superior'])){
      if($row['superior']!=''){
        $superior = Client::where('company_id',$company_id)->where('company_name',$row['superior'])->first();
        if($superior){
          $superiorid=$superior->id;
        }
      }
    }

    if(isset($row['pin'])){
      $pin=$row['pin'];
    }
    if(isset($row['partycode'])){
      $partycode=$row['partycode'];
    }
    if(isset($row['pan'])){
      $pan=$row['pan'];
    }
    if(isset($row['aboutcompany'])){
      $aboutcompany=$row['aboutcompany'];
    }
    if(isset($row['website'])){
      $website=$row['website'];
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
    if(isset($row['mobile'])){
      $mobile=$row['mobile'];
    }
    if(isset($row['addressline1'])){
      $addressline1=$row['addressline1'];
    }
    if(isset($row['addressline2'])){
      $addressline2=$row['addressline2'];
    }
    if(isset($row['openingbalance'])){
      $openingbalance=$row['openingbalance'];
    }
    if(isset($row['closingbalance'])){
      $closingbalance=$row['closingbalance'];
    }
    if(isset($row['creditlimit'])){
      $creditlimit=$row['creditlimit'];
    }
    if(isset($row['latitude'])){
      $latitude=$row['latitude'];
    }
    if(isset($row['longitude'])){
      $longitude=$row['longitude'];
    }
    
    $data = Client::create([
     'company_id' =>$company_id, 'company_name' =>$row['partyname'],'name' => $row['contactpersonname'], 'client_type' => $partytypeid, 'country' =>$countryid, 'state' => $stateid, 'city' =>$cityid, 'pin'=> $row['pin'],'client_code' => $partycode,'superior' => $superiorid, 'business_id'=> $businessid, 'pan' => $pan,'about'=> $aboutcompany,'website'=> $website,'email' => $email, 'phonecode'=>$phonecode, 'phone' => $phone, 'mobile' => $mobile, 'address_1' => $addressline1, 'address_2' => $addressline2, 'opening_balance'=> $openingbalance, 'closing_balance'=> $closingbalance,'credit_limit'=> $creditlimit, 'credit_days'=> $creditdays,'latitude' => $latitude, 'longitude' => $longitude,'status'=> 'Active']); 
    
    $this->updateHandle($company_id, $data["id"]);
    if($beatid){
        DB::table('beat_client')->insert(['client_id' => $data["id"], 'beat_id' => $beatid]);
    }
    
    //dd($category_rate_type_ids);
    if(!empty($category_rate_type_ids)){
      $idcount=count($category_rate_type_ids);
      foreach($category_rate_type_ids as $assign_category_rate_type_id){
       // echo $assign_category_rate_type_id;
       // die;
        DB::table('client_category_rate_types')->insert(['client_id' => $data["id"], 'category_rate_type_id' => $assign_category_rate_type_id]);
      }
    }
    
    
   return $data;  
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

  private function updateHandle($company_id, $id){
    try{
      $admins = Employee::whereCompanyId($company_id)->whereIsAdmin(1)->pluck('id')->toArray();
      foreach($admins as $admin){
        DB::table('handles')->insert([
          'company_id' => $company_id,
          'employee_id' => $admin,
          'client_id' => $id,
          'map_type' => 1
        ]);
      }
    }catch(\Exception $e){
      Log::info(array("Import Error :-"), $e->getMessage());
    }
  }


  public function rules(): array
    {
        $allow_party_duplication = config('settings.allow_party_duplication');
      if($allow_party_duplication==0){
        return [
            'partyname' => 'unique:clients,company_name,NULL,id,deleted_at,NULL',
            'contactpersonname' => 'required',
            'partytype' => 'required',
            'superior' => 'required',
            'country' => 'required',
        ];
      }else{
        return [
            'partyname' => 'required',
            'contactpersonname' => 'required',
            'partytype' => 'required',
            'superior' => 'required',
            'country' => 'required',
          ];
      }
    }
}

