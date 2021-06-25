<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeGroup extends Model
{
    use SoftDeletes;

    protected $table = 'employeegroups';

    protected $fillable = ['name' , 'company_id' ,'status'];


    protected $dates = ['deleted_at'];

    public function employees()
    {
        return $this->hasMany('App\Employee');
    }


}
