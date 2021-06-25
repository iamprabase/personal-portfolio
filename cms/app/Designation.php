<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    
     protected $fillable = ['name' , 'company_id' ,'parent_id'];

     public function employees()
    {
        return $this->hasMany('App\Employee', 'designation','id');
    }

    public function parent(){
    	return $this->hasOne('App\Designation','id','parent_id');
    }

    public function scopeDesignationLevel($query, $id, $seniors)
    {
       $superiorInstance = $query->where('id', $id)->first();
        if($superiorInstance){
            $superior_id = $superiorInstance->parent_id;
            if($superior_id!=0){
                $seniors[] = $superior_id;
                $seniors = self::DesignationLevel($superior_id, $seniors);

            }
        }
        return $seniors;
    }

    public function scopeCompanyMaxDesignationLevel($query, $level = 1){
      $designations = $query->get(['id', 'name', 'parent_id'])->pluck('parent_id', 'id')->toArray();
      if(count($designations) < 1) return 0;
      $max_hierarchy = 1;
      foreach($designations as $key=>$parent){
        $max_hierarchy = $this->getParents($designations, $key, $max_hierarchy, $hierarchy = 1 );
      }
      return $max_hierarchy;
    }

    private function getParents($designations, $id, $max_hierarchy, $hierarchy){
      $parent_id = $designations[$id];
      if($parent_id == 0){
        if($hierarchy > $max_hierarchy) $max_hierarchy = $hierarchy;
        return $max_hierarchy;
      }
      $hierarchy += 1;
      $new_designations_array = array_filter($designations, function($e) use ($id) {
        return ($e !== $id);
      });

      return $this->getParents($new_designations_array, $parent_id, $max_hierarchy, $hierarchy);
    }
}
 