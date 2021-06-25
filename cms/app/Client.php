<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Beat;
use App\Order;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{
    use SoftDeletes;
    use LogsActivity;
    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $fillable = ['company_id', 'company_name', 'client_type', 'superior', 'name', 'client_code', 'website', 'email', 'country', 'state', 'city','market_area','address_1','business_id', 'address_2', 'pin', 'phonecode', 'phone', 'mobile', 'pan', 'about', 'location','latitude','longitude', 'status', 'created_by','image_path','image','credit_days', 'outlet_id','opening_balance','credit_limit','rate_id'];


    /*protected $hidden = [
        'longitude', 'latitude'
    ];*/

    public function beats()
    {
        return $this->belongsTo('App\Beat');
    }

    public function business()
    {
        return $this->belongsTo('App\BusinessType');
    }

    public function beatplansdetails()
    {
        return $this->belongsToMany('App\BeatPlansDetails');
    }

    public function orders()
    {
        return $this->hasMany('App\Order')->orderBy('id', 'desc');
    }

    public function visits()
    {
        return $this->hasMany('App\ClientVisit')->orderBy('id', 'desc');
    }

    public function activities()
    {
        return $this->hasMany('App\Activity')->orderBy('id', 'desc');
    }

    public function stocks()
    {
        return $this->hasMany('App\Stock');
    }

    public function collections()
    {
        return $this->hasMany('App\Collection');
    }

    public function noorders()
    {
        return $this->hasMany('App\NoOrder');
    }

    public function notes()
    {
        return $this->hasMany('App\Note');
    }

    public function expenses()
    {
        return $this->hasMany('App\Expense');
    }

    public function partytypes()
    {
        return $this->belongsTo('App\PartyType', 'client_type', 'id');
    }

    public function employees()
    {
        return $this->belongsToMany('App\Employee', 'handles',
            'client_id', 'employee_id')->orderBy('name', 'ASC');
    }

     public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = 'Client';
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

    public function scopeCreatedBy($query, $id) {
        return $query->where('created_by', $id);
    }

    public function scopeCreatedAt($query, $startDate, $endDate) {
        if (isset($startDate) && isset($endDate)) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            return $query;
        }
        
    }

    public function outlets()
    {
        return $this->belongsTo('App\Outlet', 'outlet_id', 'id');
    }

    public function companies()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function rates()
    {
      return $this->belongsTo('App\RateSetup', 'rate_id', 'id');
    }

    public function uploads()
    {
        return $this->hasMany('App\PartyUpload', 'client_id', 'id');
    }

    public function appliedcategoryrates(){
      return $this->belongsToMany(CategoryRateType::class, 'client_category_rate_types', 'client_id', 'category_rate_type_id');
    }

    public function scopeTotalCreatedInGivenRange($query, $date_range, $client_type, $cols){
      $results = $query
                  ->whereDate('created_at', '>=', $date_range[0])
                  ->whereDate('created_at', '<=', $date_range[1])
                  ->whereClientType($client_type)
                  ->select($cols);

      return $results;
    }

    public function scopePartyNeverOrdered($query, $date_range, $client_type, $cols){
      $results = $query
                  ->where(function($query) use($date_range){
                      $query->whereDoesntHave('orders', function($subQuery) use($date_range){
                        $subQuery->whereBetween('order_date', $date_range);
                      });
                  })
                  ->whereClientType($client_type)
                  ->select($cols);

      return $results;
    }

    public function scopePartyUnVisited($query, $date_range, $client_type, $cols){
      $results = $query
                  ->where(function($query) use($date_range){
                    $query->whereDoesntHave('visits', function($subQuery) use($date_range){
                      $subQuery->whereBetween('date', $date_range);
                    });
                  })
                  ->whereClientType($client_type)
                  ->select($cols);

      return $results;
    }

    public function scopePartyNoAction($query, $date_range, $client_type, $cols){
      $results = $query
                  ->where(function($query) use($date_range){
                    $query->whereDoesntHave('orders', function($subQuery) use($date_range){
                      $subQuery->whereBetween('order_date', $date_range);
                    });
                    $query->whereDoesntHave('noorders', function($subQuery) use($date_range){
                      $subQuery->whereBetween('date', $date_range);
                    });
                    $query->whereDoesntHave('expenses', function($subQuery) use($date_range){
                      $subQuery->whereBetween('expense_date', $date_range);
                    });
                    $query->whereDoesntHave('notes', function($subQuery) use($date_range){
                      $subQuery->whereBetween('meetingdate', $date_range);
                    });
                    $query->whereDoesntHave('collections', function($subQuery) use($date_range){
                      $subQuery->whereBetween('payment_date', $date_range);
                    });
                    $query->whereDoesntHave('activities', function($subQuery) use($date_range){
                      $subQuery->whereDate('start_datetime', '>=', $date_range[0]);
                      $subQuery->whereDate('start_datetime', '<=', $date_range[0]);
                    });
                    $query->whereDoesntHave('activities', function($subQuery) use($date_range){
                      $subQuery->whereDate('completion_datetime', '>=', $date_range[0]);
                      $subQuery->whereDate('completion_datetime', '<=', $date_range[0]);
                    });
                    $query->whereDoesntHave('uploads', function($subQuery) use($date_range){
                      $subQuery->whereDate('created_at', '>=', $date_range[0]);
                      $subQuery->whereDate('created_at', '<=', $date_range[0]);
                    });
                    $query->whereDoesntHave('visits', function($subQuery) use($date_range){
                      $subQuery->whereBetween('date', $date_range);
                    });
                  })
                  ->whereClientType($client_type)
                  ->select($cols);
      return $results;
    }

    

}
