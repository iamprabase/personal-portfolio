<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleAttribute extends Model
{
    use SoftDeletes;
    protected $table = "module_attributes";
    protected $fillable = [
        'company_id','title','color','default','module_id'
    ];

}
