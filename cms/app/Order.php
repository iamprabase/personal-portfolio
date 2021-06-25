<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;
use App\Client;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;


class Order extends Model
{
    //
    use LogsActivity,SoftDeletes;

    protected static $logFillable = ['name', 'text'];
    protected static $logAttributes = ['name', 'text'];
    protected static $logOnlyDirty = true;
    protected $guarded = [];

    public function clients()
    {
        return $this->belongsTo('App\Client', 'client_id', 'id');
    }

    public function ordertos()
    {
        return $this->belongsTo('App\Client', 'order_to', 'id');
    }

    public function outlets()
    {
        return $this->belongsTo('App\Outlet', 'outlet_id', 'id');
    }


    public function employee()
    {
        return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }

    public function orderScheme()
    {
        return $this->hasMany(OrderScheme::class);
    }

    public function status()
    {
        return $this->hasOne('App\ModuleAttribute','id','delivery_status_id');
    }

    public function module_status()
    {
        return $this->belongsTo('App\ModuleAttribute', 'delivery_status_id', 'id');
    }

    public function orderdetails(){
      return $this->hasMany('App\OrderDetails', 'order_id', 'id');
    }

    public function taxes()
    {
      return $this->belongsToMany('App\TaxType', 'tax_on_orders', 'order_id', 'tax_type_id');
    }

    public function companies()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = 'Order';
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
	public function scopeEmployeeId($query, int $id)
    {
        return $query->where('employee_id', $id);
    }

    public function scopeClientId($query, int $id)
    {
        return $query->where('client_id', $id);
    }

    public function scopeCreatedAt($query, $startDate, $endDate)
    {
        if (isset($startDate) && isset($endDate)) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            return $query;
        }
    }

    public function scopeOrderDate($query, $value1, $value2)
    {
        if (isset($value1) && isset($value2)) {
            return $query->whereBetween('order_date', [$value1, $value2]);
        } else {
            return $query;
        }
    }

    public function scopeCheckDuplicateOrderNumberExist($query, $order_no, $company_id){
      $findDuplicateByID = $query->whereCompanyId($company_id)->whereOrderNo($order_no)->first();
      $data = array("exists" => false, "new_order_no" => 0);
      if($findDuplicateByID){
        $data["exists"] = true;
        $data["new_order_no"] = getOrderNo($company_id);
      }

      return $data;
    }

    public function deliverystatus()
    {
        return $this->belongsTo('App\ModuleAttribute', 'delivery_status_id', 'id');
    }
}
