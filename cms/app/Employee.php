<?php

namespace App;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\CausesActivity;

class Employee extends Model
{
    use SoftDeletes;
    use LogsActivity, CausesActivity;

    protected $fillable = ['user_id' , 'company_id' ,'is_admin' ,'role' ,'name' ,'employee_code' ,'employeegroup' ,'country_code' ,'e_country_code' ,'phone' ,'email' ,'password' ,'b_date' ,'gender','status' ,'designation' ,'local_add' ,'per_add' ,'superior', 'e_name' ,'father_name' ,'a_phone' ,'e_phone' ,'e_relation' ,'total_salary' ,'permitted_leave' ,'doj' ,'lwd' ,'acc_holder' ,'acc_number' ,'bank_id' ,'ifsc_code' ,'pan' ,'branch'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role', 'id');
    }

    protected $dates = ['deleted_at'];

    public function beatvplans()
    {
        return $this->hasMany('App\BeatVPlan');
    }

    public function orders()
    {
        return $this->hasMany('App\Order')->orderBy('order_date', 'DESC')->orderBy('orders.id','DESC');
    }

    public function noorders()
    {
        return $this->hasMany('App\NoOrder')->orderBy('date', 'DESC')->orderBy('id','DESC');
    }

    public function collections()
    {
        return $this->hasMany('App\Collection')->orderBy('payment_date', 'DESC');
    }

    public function activities()
    {
        return $this->hasMany('App\Activity', 'assigned_to', 'id')->orderBy('start_datetime', 'DESC');
    }

    public function expenses()
    {
        return $this->hasMany('App\Expense')->orderBy('created_at', 'DESC');
    }

    public function leaves()
    {
        return $this->hasMany('App\Leave')->whereNull('deleted_at')->orderBy('start_date', 'DESC');
    }

    public function attendances()
    {
        return $this->hasMany('App\Attendance')->groupBy('adate')->orderBy('check_datetime', 'DESC');
    }

    public function parties()
    {
        return $this->belongsToMany('App\Client', 'handles',
            'employee_id', 'client_id')->where('status','Active')->orderBy('company_name', 'ASC');
    }

    public function superior()
    {
        return $this->hasOne('App\Employee','id','superior_id');
    }

    public function assignUser(User $user)
    {
        $this->user_id = $user->id;
    }

    public function company()
    {
        return $this->belongsTo('App\Company');
    }
 
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function employee_groups()
    {
        return $this->belongsTo('App\EmployeeGroup', 'employeegroup', 'id');
    }

    public function designations(){
      return $this->hasOne('App\Designation', 'id', 'designation');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = 'Employee';
        if ($eventName == 'created')
        {
            return "Created $modelName";
        }

        if ($eventName == 'updated')
        {
            return "Updated $modelName";
        }

        if ($eventName == 'deleted')
        {
            return "Deleted $modelName";
        }

        return '';
    }

    public function tourplans()
    {
      return $this->hasMany('App\TourPlan', 'employee_id', 'id');
    }

    public function clientvisit()
    {
      return $this->hasMany('App\ClientVisit', 'employee_id', 'id');
    }

    public function dayremarks() 
    {
      return $this->hasMany('App\DayRemark', 'employee_id', 'id');
    }

    public function employee_attendances()
    {
      return $this->hasMany('App\Attendance', 'employee_id', 'id');
    }

    public function childs()
    {
        return $this->hasMany('App\Employee', 'superior', 'id')->where('status','Active');
    }

     /**
     * Returns all ids of junior chains of particular employee
     */
    public function scopeEmployeeChilds($query, $id, $juniors)
    {
      $juniors[] = $id;
      $juniorInstances = $query->where('superior', $id)->get(['id']);
      
      foreach($juniorInstances as $juniorInstance){
        $juniorId = $juniorInstance->id;
        $juniors = self::employeeChilds($juniorId, $juniors);
      }

      return $juniors;
    }

    /**
     * Returns all ids of senior chains of
     * particular employee including
     * all admins
     */
    public function scopeEmployeeParents($query, $id, $seniors)
    {
      $seniors[] = $id;
      $superiorInstance = $query->where('id', $id)->first();
      if($superiorInstance){
        $superior_id = $superiorInstance->superior;
        $seniors = self::employeeParents($superior_id, $seniors);
        
        $admins = self::where('is_admin', 1)->whereCompanyId($superiorInstance->company_id)->pluck('id')->toArray();
        if(count($admins)>0){
          foreach($admins as $admin){
            if(!in_array($admin, $seniors)){
              $seniors[] =  $admin;
            }
          }
        }
      }      


      return $seniors;
    }

    /**
     * Returns all ids of senior chains of 
     * particular employee excluding admins
     */

    public function scopeEmployeeSeniors($query, $id, $seniors)
    {
      $superiorInstance = $query->where('id', $id)->first();
      if($superiorInstance){
        if($superiorInstance->is_admin!=1){
          $seniors[] = $id;
          $superior_id = $superiorInstance->superior;
          $seniors = self::employeeParents($superior_id, $seniors);
        }
      }      

      return $seniors;
    }

    public function targetsalesmanassign(){
      return $this->hasMany('App\TargetSalesmanassign','salesman_id','id');
    }

    public function targetsalesmanassignhistory(){
      return $this->hasMany('App\TargetSalesmanassignHistory','salesman_id','id');
    }

    public function odometerReport()
    {
        return $this->hasMany(OdometerReport::class);
    }


  }
 