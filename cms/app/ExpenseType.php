<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseType extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function expenses()
    {
        return $this->hasMany('App\Expense', 'expense_type_id','id');
    }
}
