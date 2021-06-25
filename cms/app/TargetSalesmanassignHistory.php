<?php

namespace App; 

use Illuminate\Database\Eloquent\Model;

class TargetSalesmanassignHistory extends Model
{
  protected $table = "salesmantarget_assign_history";
  protected $fillable = [
      'company_id','targetid_original','targethist_newgroupid','salesman_id','target_rule','target_name','target_progress','target_interval','target_values','target_rule','target_startmonth','target_assigneddate'
  ];

  

}
