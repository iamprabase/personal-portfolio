<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenerateReport extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $table = 'generatereports';
    protected $fillable = ['company_id', 'party_name', 'employee_name', 'date_range', 'generated_by', 'download_link', 'download_count'];
}
