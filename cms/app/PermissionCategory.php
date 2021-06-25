<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class PermissionCategory extends Model
{
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
