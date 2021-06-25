<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CustomModuleField extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->company_id = config('settings.company_id');
            $model->status = 1;
            $model->user_id = auth()->id();
        });
    }


//    public function setSlugAttribute($slug)
//    {
//        $this->attributes['slug'] = $this->slug($slug);
//    }


//    public function slug($slug)
//    {
//        $slug = Str::slug($slug, '_');
//
//        $count = CustomModuleField::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
//
//        if (request()->method() == 'PATCH') {
//            return $this->slug == $slug ? $slug : "{$slug}-{$count}";
//        }
//        return $count ? "{$slug}_{$count}" : $slug;
//    }


    public function getStatusAttribute()
    {
        return $this->attributes['status'] == 1 ? 'Active' : 'Inactive';
    }


}
