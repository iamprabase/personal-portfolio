<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientSettingsNew extends Model
{
    use SoftDeletes;

    protected $fillable = ['company_id', 'option', 'value'];

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }
}
