<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomModule extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->company_id = config('settings.company_id');
            $model->employee_id = auth()->user()->EmployeeId();
            $model->status = 1;
            $model->order = CustomModule::count() + 1;
            $model->user_id = auth()->id();
        });
    }

    public function getStatusAttribute()
    {
        return $this->attributes['status'] == 1 ? 'Active' : 'Inactive';
    }

    //custom field relationship
    public function customFields()
    {
        return $this->hasMany(CustomModuleField::class)->orderBy('order', 'asc')->where('status', 1);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
