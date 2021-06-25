<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    public function assignUser(User $user)
    {
        $this->user_id = $user->id;
        $this->contact_email = $user->email;
    }

    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}