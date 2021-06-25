<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarketArea extends Model
{
    protected $table = 'marketareas';

    public $fillable = ['name', 'parent_id', 'company_id'];


    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function childs()
    {
        return $this->hasMany('App\MarketArea', 'parent_id', 'id');
    }
}
