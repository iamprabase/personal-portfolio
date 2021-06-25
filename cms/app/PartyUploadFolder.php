<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartyUploadFolder extends Model
{
  protected $table = "party_upload_folders";
  protected  $guarded = [];

  public function files()
  {
    return $this->hasMany('App\PartyUpload', 'party_upload_folder_id', 'id');
  }

  public function client()
  {
      return $this->belongsTo('App\Client', 'client_id', 'id');
  }
}
