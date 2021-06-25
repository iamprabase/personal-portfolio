<?php

namespace App\Imports;

use DB;
use Log;
use App\Beat;
use App\Client;
use App\Employee;
use App\PartyType;
use App\BusinessType;
use App\PermissionCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class OutstandingImport implements ToModel, WithHeadingRow, WithValidation
{
  use Importable;

  private $data; 
  /**
  * @param array $row
  *
  * @return \Illuminate\Database\Eloquent\Model|null
  */

  public function __construct(array $data = [])
    {
        $this->data = $data; 
    }


  public function model(array $row)
  {

    //dd($row);

     $company_id = config('settings.company_id');
     $clentname='';
     $clentcode='';


     if(isset($row['outstanding'])){
      $openingbalance=number_format($row['outstanding'], 2, '.', '');
    }

     if(isset($row['partyname']) && $row['partyname']!=''){
        $clentname=$row['partyname'];
     }

     if(isset($row['partycode']) && $row['partycode']!=''){
        $clentcode=$row['partycode'];
     }

     if($clentname!='' && $clentcode!=''){
        $client = Client::where('company_id',$company_id)->where('company_name',$clentname)->where('client_code',$clentcode)->first();
     }elseif($clentname!='' && $clentcode==''){
         $client = Client::where('company_id',$company_id)->where('company_name',$clentname)->first();
     }elseif($clentname=='' && $clentcode!=''){
         $client = Client::where('company_id',$company_id)->where('company_name',$clentname)->first();
     }else{
      $client=NULL;
     }
      
      if($client)
      {
        $data = Client::find($client->id);
        $data->opening_balance = $openingbalance;
        $data->save();
        return $data; 
      }
         
  }

  public function rules(): array
    {
        
        return [
            'outstanding' => 'required',
            'partyname' => 'required',
            //'partycode' => 'required_without:partyname',
            
        ];
    }
}