<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use App\TargetSalesmanassign;

class TargetSalesman extends Model
{
  protected $table = "salesmantarget";
  protected $fillable = [
      'company_id','target_name','target_rules','target_groupid','target_interval','target_value','created_at'
  ];


  
  


}
