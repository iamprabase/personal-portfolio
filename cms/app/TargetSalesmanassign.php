<?php

namespace App; 

use Illuminate\Database\Eloquent\Model;

class TargetSalesmanassign extends Model
{
  protected $table = "salesmantarget_assign";
  protected $fillable = [
      'company_id','salesman_id','target_rules', 'target_tot_workingdays','target_name','target_progress','target_interval','target_values','target_rule'
  ];

  

}
