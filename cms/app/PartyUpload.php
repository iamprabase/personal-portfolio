<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartyUpload extends Model
{
  protected $table = "party_uploads";
  protected  $guarded = [];

  public function employee()
  {
    return $this->belongsTo('App\Employee', 'employee_id', 'id');
  }

  public function folder()
  {
    return $this->belongsTo('App\PartyUploadFolder', 'party_upload_folder_id', 'id');
  }
}
