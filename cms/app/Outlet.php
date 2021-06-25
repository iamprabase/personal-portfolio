<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

// class Outlet extends Authenticatable
class Outlet extends Model
{
  use HasApiTokens;
  use SoftDeletes;
  protected $table = "outlets";

  protected $guarded = ['client_id'];
  
  public function suppliers()
  {
      return $this->belongsToMany('App\Company', 'company_outlet', 'outlet_id', 'company_id');
  }

  public function clients()
  {
    return $this->hasMany('App\Client');
  }

  public function validateForPassportPasswordGrant($email)
  {
      return $this->where('email', $email)->first();
  }

  public function users()
  {
      return $this->hasOne('App\User');
  }

  public function orders()
  {
      return $this->hasMan('App\Order');
  }
}
