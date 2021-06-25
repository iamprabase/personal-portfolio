<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class UnitRelation extends Model
{
  use SoftDeletes;

  protected $table = 'unit_relations';
  public $timestamps = true;
  protected $guarded = [];
}
