<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class AnalyticsSave extends Model
{
  protected $table = "analytics_usersave";
  protected $fillable = [
      'company_id','employee_id','mapkey','mapvalue'
  ];


}
