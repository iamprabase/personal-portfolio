<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class PartyType extends Model 
{
    protected $table = 'partytypes';
    public $fillable = ['name','short_name', 'parent_id', 'company_id','allow_salesman'];

 
    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function childs()
    {
        return $this->hasMany('App\PartyType', 'parent_id', 'id');
    }

    public function scopePartyTypeParents($query, $id, $seniors, $level = 3)
    {
        $superiorInstance = $query->where('id', $id)->first();
        if(count($seniors)>=$level) return $seniors;
        if($superiorInstance){
            $superior_id = $superiorInstance->parent_id;
            if($superior_id!=0){
                $seniors[] = $superior_id;
                $seniors = self::partyTypeParents($superior_id, $seniors);

            }
        }
        return $seniors;
    }

    public function scopePartyTypeAllParents($query, $id, $seniors)
    {
        $superiorInstance = $query->where('id', $id)->first();
        if($superiorInstance){
            $superior_id = $superiorInstance->parent_id;
            if($superior_id!=0){
                $seniors[] = $superior_id;
                $seniors = self::partyTypeParents($superior_id, $seniors);

            }
        }
        return $seniors;
    }

    public function scopePartyLevel($query, $id, $seniors){
      $superiorInstance = $query->where('id', $id)->first();
      if($superiorInstance){
            $superior_id = $superiorInstance->parent_id;
            if($superior_id!=0){
                $seniors[] = $superior_id;
                $seniors = self::partyTypeParents($superior_id, $seniors);

            }
        }
        return $seniors;
    }

    public function scopeCompanyMaxPartyTypeLevel($query, $level = 1){
      $partyTypes = $query->get(['id', 'name', 'parent_id'])->pluck('parent_id', 'id')->toArray();
      if(count($partyTypes) < 1) return 0;
      $max_hierarchy = 1;
      foreach($partyTypes as $key=>$parent){
        $max_hierarchy = $this->getParents($partyTypes, $key, $max_hierarchy, $hierarchy = 1 );
      }
      return $max_hierarchy;
    }

    private function getParents($partyTypes, $id, $max_hierarchy, $hierarchy){
      $parent_id = $partyTypes[$id];
      if($parent_id == 0){
        if($hierarchy > $max_hierarchy) $max_hierarchy = $hierarchy;
        return $max_hierarchy;
      }
      $hierarchy += 1;
      $new_designations_array = array_filter($partyTypes, function($e) use ($id) {
        return ($e !== $id);
      });

      return $this->getParents($new_designations_array, $parent_id, $max_hierarchy, $hierarchy);
    }

    public function scopeAccessiblePartyTypes($query, $companyId, $returnIds){
      $allPartytypes = self::where('company_id', $companyId)->pluck('name', 'id')->toArray();
      if(Auth::user()->can('party-view')) array_push($returnIds, 0); 
      foreach ($allPartytypes as $id => $name) {
        $stringName = str_replace(" ", "-", $name) . "-view";
        $permission_id = DB::table('permissions')->where('name', 'LIKE', $stringName)
        ->whereCompanyId($companyId)->first();
        if($permission_id){
          if (Auth::user()->hasPermissionTo($permission_id->id)) {
              array_push($returnIds, $id);
          }else{
            unset($allPartytypes[$id]);
          }
        }
      }

      return array('accessible_party_types' => $allPartytypes, 'accessible_party_types_id' => $returnIds);
    }

    public function clients(){
        return $this->hasMany(Client::class, 'client_type');
    }

}
