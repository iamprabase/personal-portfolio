<?php

namespace App;

use App\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Beat extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];

    public function clients()
    {
        return $this->belongsToMany(Client::class);
    }

    public function parties()
    {
        return $this->belongsToMany('App\Client', 'beat_client',
            'beat_id', 'client_id')->orderBy('company_name', 'ASC');
    }

}
