<?php 
namespace App\Classes;


use Illuminate\Http\Request;
use App\Order;
use App\Client;
use App\PartyType;
use App\PermissionCategory;
use App\BusinessType;
use App\Beat;
use App\Product;
use App\RateSetup;
use App\RateDetail;
use App\Brand;
use App\Category;
use App\UnitTypes;
use App\ProductVariant;
use App\Employee;
use Spatie\Permission\Models\Permission;
use DOMDocument;
use DB;
use Storage;
use DateTime;
use DateTimeZone;
use Spatie\Permission\Models\Role;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Ftp as Adapter;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Spatie\Dropbox\WriteMode;

class Tally {
    
  public function __construct() {
    return "construct function was initialized.";
  }

  public function putXml()
  {
          
    $tally_details = DB::table('tally_schedule')
      ->where('order_outward',1)
      ->whereNotNull('source')
      ->whereNull('deleted_at')
      ->select('company_id','company_name','username','password','url','duration','order_outward','order_path_outward','source','get_order_by')
      ->get();   
      //dd($tally_details);

    foreach($tally_details as $tally_detail){
      if($tally_detail->order_outward=1 && !empty($tally_detail->source)){
        if($tally_detail->get_order_by=='Beat'){
             $ordercheck= DB::table('orders')
            ->where('orders.company_id',$tally_detail->company_id)
            ->where('orders.tallysync' ,0)
            ->where('orders.employee_id',0)
            ->where('orders.created_at', '>','2021-03-23 00:00:00')
            ->get();
           // dd($ordercheck);
             if(count($ordercheck)>0){     
          $orders= DB::table('orders')
            ->join('clients', 'orders.client_id', '=', 'clients.id')
            ->join('outlets', 'orders.outlet_id', '=', 'outlets.id')
            ->join('beat_client', 'orders.client_id', '=', 'beat_client.client_id')
            ->join('beats', 'beat_client.beat_id','=', 'beats.id')
            ->where('orders.company_id',$tally_detail->company_id)
            ->where('orders.tallysync' ,0)
            ->where('orders.created_at', '>','2021-03-23 00:00:00')
            //->whereNull('orders.deleted_at')
            ->select('orders.id','orders.order_no','orders.order_date', 'orders.order_note as remark','clients.company_name as party_name','outlets.contact_person as created_by','beats.name as region')
                  ->get();
             }else{
                $orders= DB::table('orders')
            ->join('clients', 'orders.client_id', '=', 'clients.id')
            ->join('employees', 'orders.employee_id', '=', 'employees.id')
            ->join('beat_client', 'orders.client_id', '=', 'beat_client.client_id')
            ->join('beats', 'beat_client.beat_id','=', 'beats.id')
            ->where('orders.company_id',$tally_detail->company_id)
            ->where('orders.tallysync' ,0)
            ->where('orders.created_at', '>','2021-03-23 00:00:00')
            //->whereNull('orders.deleted_at')
            ->select('orders.id','orders.order_no','orders.order_date', 'orders.order_note as remark','clients.company_name as party_name','employees.name as created_by','beats.name as region')
                  ->get(); 
                 
             }
           // dd($orders);
           foreach($orders as $order){

              try {
               
                $sales = DB::table('orderproducts')
                ->where('orderproducts.order_id',$order->id)
                ->select('orderproducts.product_id','orderproducts.product_name','orderproducts.unit_name','orderproducts.rate','orderproducts.quantity','orderproducts.amount')
                  ->get();
                 // dd($sales);
          
                if(count($sales)>0){
                   $defaulttimezone='Asia/kathmandu';
                  $timezone = DB::table('client_settings')->select('company_id','time_zone','order_prefix')->whereNotNull('time_zone')->where('company_id', $tally_detail->company_id)->first();
                  if($timezone){
                  $localTime = new DateTime('NOW', new DateTimeZone($timezone->time_zone));
                }else{
                  $localTime = new DateTime('NOW', new DateTimeZone($defaulttimezone));
                }
                  $localtime=$localTime->format('dmY');
                  $order_no=$timezone->order_prefix.$order->order_no;
                  $fileName =$tally_detail->order_path_outward.$order->region.'-'.$order_no.'_'.$localtime.".xml";
               // die;
                  $xml='';
                  $xml = new DOMDocument("1.0"); 
                  $xml->formatOutput=true;  
                  $orderxml=$xml->createElement("XML"); 
                  $xml->appendChild($orderxml); 

                 // dd($sales);
                  foreach ($sales as $s){ 
                       $unitconversion= DB::table('product_unit_conversion')
                      ->where('product_id', $s->product_id)
                  ->get();
                 // echo $s->product_id;
                 // dd($unitconversion);
                    $regionxml=$xml->createElement("Records"); 
                    $orderxml->appendChild($regionxml); 

                    $regionnew=$xml->createElement("REGION", htmlspecialchars($order->region)); 
                    $regionxml->appendChild($regionnew); 
                      
                    $date=$xml->createElement("DATE", date('d-m-Y',htmlspecialchars(strtotime($order->order_date)))); 
                    $regionxml->appendChild($date); 
                    
                    $partyname=$xml->createElement("PARTYNAME", htmlspecialchars($order->party_name)); 
                    $regionxml->appendChild($partyname); 
                      
                    $orderno=$xml->createElement("ORDERNO", htmlspecialchars($order_no)); 
                    $regionxml->appendChild($orderno); 
                      
                    $createdby=$xml->createElement("CREATEDBY", htmlspecialchars($order->created_by)); 
                    $regionxml->appendChild($createdby); 
                      
                    $prodname=$xml->createElement("STOCKITEMNAME", htmlspecialchars($s->product_name)); 
                    $regionxml->appendChild($prodname); 
                      
                    $totalpkt=$xml->createElement("TOTALPKTS", htmlspecialchars($s->quantity)); 
                    $regionxml->appendChild($totalpkt); 
                  
                    $unit=$xml->createElement("UNIT", htmlspecialchars($s->unit_name)); 
                    $regionxml->appendChild($unit); 
                    
                    // if(trim($s->unit_name)=='Pkt'){
                    // $orderqty=$xml->createElement("ORDERQTY", '0-'.htmlspecialchars($s->quantity)); 
                    // $regionxml->appendChild($orderqty);  
                    // }
                    // elseif(trim($s->unit_name)=='Kg'){
                    // $orderqty=$xml->createElement("ORDERQTY", htmlspecialchars($s->quantity)); 
                    // $regionxml->appendChild($orderqty);  
                    // }
                    // // elseif(trim($s->unit_name)=='bag'){
                    // //   $orderqty=$xml->createElement("ORDERQTY", htmlspecialchars($s->quantity).'-0'); 
                    // //   $regionxml->appendChild($orderqty); 
                    // // }
                    // else{
                    //   $orderqty=$xml->createElement("ORDERQTY", htmlspecialchars($s->quantity).'-0'); 
                    //   $regionxml->appendChild($orderqty); 
                    // }
                    
                    
                    if(count($unitconversion)>0){
                    if($s->unit_name=='Pkt'){
                    $orderqty=$xml->createElement("ORDERQTY", '0-'.htmlspecialchars($s->quantity)); 
                    $regionxml->appendChild($orderqty);  
                    }else{
                      $orderqty=$xml->createElement("ORDERQTY", htmlspecialchars($s->quantity).'-0'); 
                      $regionxml->appendChild($orderqty); 
                    }
                  }else{
                    $orderqty=$xml->createElement("ORDERQTY", htmlspecialchars($s->quantity)); 
                    $regionxml->appendChild($orderqty);  
                  }
                    
                    
                    $orderremark=$xml->createElement("ORDERREMARKS", htmlspecialchars($order->remark)); 
                    $regionxml->appendChild($orderremark); 
          
                    $affected = DB::table('orders')->where('id', $order->id)->update(['tallysync' => 1]);    
                  
                      if($tally_detail->source=='Dropbox'){
                    Storage::disk('dropbox')->put($fileName,$xml->saveXML());
                  }else{
                    $filesystem = new Filesystem(new Adapter([
                      'host'     => $tally_detail->url,
                      'username' => $tally_detail->username,
                      'password' => $tally_detail->password,
                    ]));
                  
                    $filesystem->put($fileName, $xml->saveXML());
                  }
                  } 
                  // dd($xml);
                  
              
                }
              }catch(Exception $e) {
                echo $e->getMessage();
              }
            }
          }elseif($tally_detail->get_order_by=='Distributor'){

          }else{
            try {
                 $ordercheck= DB::table('orders')
            ->where('orders.company_id',$tally_detail->company_id)
            ->where('orders.tallysync' ,0)
            ->where('orders.employee_id',0)
            ->where('orders.created_at', '>','2021-03-23 00:00:00')
            ->get();
             if(count($ordercheck)>0){  
              
                  $sales = DB::table('orders')
                ->join('orderproducts', 'orders.id', '=', 'orderproducts.order_id')
                ->join('clients', 'orders.client_id', '=', 'clients.id')
                ->join('outlets', 'orders.outlet_id', '=', 'outlets.id')
                ->join('partytypes', 'clients.client_type', '=', 'partytypes.id')
                ->where('orders.company_id',$tally_detail->company_id)
                //->where('orders.order_date','>','2021-01-15')
                ->where('orders.tallysync' ,0)
                ->select('orders.id','orders.employee_id','orders.client_id','orders.company_id','orders.order_date','orders.tallysync', 'orderproducts.product_name','orderproducts.unit_name','orderproducts.rate','orderproducts.quantity','orderproducts.amount', 'clients.company_name', 'clients.name','clients.client_type','partytypes.name as partytype','outlets.contact_person as ename')
                ->get();
             }else{
                 $sales = DB::table('orders')
                ->join('orderproducts', 'orders.id', '=', 'orderproducts.order_id')
                ->join('clients', 'orders.client_id', '=', 'clients.id')
                ->join('employees', 'orders.employee_id', '=', 'employees.id')
                ->join('partytypes', 'clients.client_type', '=', 'partytypes.id')
                ->where('orders.company_id',$tally_detail->company_id)
                //->where('orders.order_date','>','2021-01-15')
                ->where('orders.tallysync' ,0)
                ->select('orders.id','orders.employee_id','orders.client_id','orders.company_id','orders.order_date','orders.tallysync', 'orderproducts.product_name','orderproducts.unit_name','orderproducts.rate','orderproducts.quantity','orderproducts.amount', 'clients.company_name', 'clients.name','clients.client_type','partytypes.name as partytype','employees.name as ename')
                ->get();
             }
               // dd($sales);
              if(count($sales)>0){
                $xml='';
                $xml = new DOMDocument("1.0"); 
                $xml->formatOutput=true;  
                $order=$xml->createElement("Records"); 
                $xml->appendChild($order); 
                $fileName =$tally_detail->order_path_outward.'/order_'.date('dmYHi').".xml";

                foreach ($sales as $s){ 
                  $region=$xml->createElement("REGION", htmlspecialchars($s->partytype)); 
                  $order->appendChild($region); 
                    
                  $date=$xml->createElement("DATE", htmlspecialchars($s->order_date)); 
                  $region->appendChild($date); 
                  
                  $partyname=$xml->createElement("PARTYNAME", htmlspecialchars($s->company_name)); 
                  $region->appendChild($partyname); 
                    
                  $orderno=$xml->createElement("ORDERNO", htmlspecialchars($s->id)); 
                  $region->appendChild($orderno); 
                    
                  $createdby=$xml->createElement("CREATEDBY", htmlspecialchars($s->ename)); 
                  $region->appendChild($createdby); 
                      
                  $prodname=$xml->createElement("STOCKITEMNAME", htmlspecialchars($s->product_name)); 
                  $region->appendChild($prodname); 
                    
                  $totalpkt=$xml->createElement("TOTALPKTS", htmlspecialchars($s->quantity)); 
                  $region->appendChild($totalpkt); 
                
                  $unit=$xml->createElement("UNIT", htmlspecialchars($s->unit_name)); 
                  $region->appendChild($unit); 
        
                  $orderqty=$xml->createElement("ORDERQTY", htmlspecialchars($s->quantity)); 
                  $region->appendChild($orderqty);  
          
                  $affected = DB::table('orders')->where('id', $s->id)->update(['tallysync' => 1]);    
                  
                } 

                // dd($Client);
                if($tally_detail->source=='Dropbox'){
                  Storage::disk('dropbox')->put($fileName, $xml->saveXML());
                }else{
                  $filesystem = new Filesystem(new Adapter([
                    'host'     => $tally_detail->url,
                    'username' => $tally_detail->username,
                    'password' => $tally_detail->password,
                  ]));
                  $filesystem->put($fileName, $xml->saveXML());
                }
              }
            }catch(Exception $e) {
              echo $e->getMessage();
            }
          }
        }
      }
    }

  public function getPartyXml(){
    ini_set('memory_limit', '1G');
    $tally_details = DB::table('tally_schedule')
      ->whereNotNull('username')
      ->whereNotNull('password')
      ->whereNull('deleted_at')
      ->whereNotNull('source')
      ->select('company_id','company_name','username','password','url','duration','product_inward','product_path_inward','party_inward','party_path_inward','source','get_order_by')
      ->get();

     // dd($tally_details);
    foreach($tally_details as $tally_detail){
      //****** PARTIES IMPORT ********////
      if($tally_detail->party_inward==1 && !empty($tally_detail->source)){
        try{
          if($tally_detail->source=='Dropbox'){
            $contents = Storage::disk('dropbox')->get($tally_detail->party_path_inward);
          }else{
            $filesystem = new Filesystem(new Adapter([
              'host'     => $tally_detail->url,
              'username' => $tally_detail->username,
              'password' => $tally_detail->password,
            ]));
          
            $contents=$filesystem->read($tally_detail->party_path_inward);
          }
       // dd($contents);
          $new = simplexml_load_string($contents);  
          $con = json_encode($new); 
          $newArr = json_decode($con, true);         
          $company_id=$tally_detail->company_id;
          //dd($newArr);

          foreach($newArr['CUSTOMER'] as $arr){
            $partytypeid='';
            $superiorid='NULL';
            $businessid='';
            $countryid='';
            $stateid='';
            $cityid='';
            $pin='';
            $partycode='';
            $pan='';
            $aboutcompany='';
            $website='';
            $email='';
            $phonecode='';
            $phone='';
            $mobile='';
            $addressline1='';
            $addressline2='';
            $openingbalance='';
            $creditlimit='';
            $creditdays=0;
            $latitude='';
            $longitude='';
            $beatid='';
            $contactpersonname='';
            $companyname='';
            $closingbalance='';
            $dueamount='';

            if(isset($arr['CUSTOMERTYPE']) && !empty($arr['CUSTOMERTYPE'])){
              $partytypename=$arr['CUSTOMERTYPE'];}
            else{
              $partytypename='Direct';
            }
            
            $partytypeid=$this->createPartyType($company_id, $partytypename);
            
            if(isset($arr['REGION']) && !empty($arr['REGION'])){
              $beatname=$arr['REGION'];
              $beatid=$this->createBeat($company_id, $beatname);
            }

            if(isset($arr['STATENAME']) && !empty($arr['STATENAME'])){
              $statename=$arr['STATENAME'];
              $state= DB::table('states')->where('name',$statename)->first();
              if($state){
                $stateid=$state->id;
                $countryid=$state->country_id;
              }
            }

            if(isset($arr['GROUPNAME']) && !empty($arr['GROUPNAME'])){
              $businesstypename=$arr['GROUPNAME'];
              $businessid=$this->createGroup($company_id, $businesstypename);
            }
            
            
            if(isset($arr['CUSTOMER_TALLYID']) && !empty($arr['CUSTOMER_TALLYID'])){
              $partycode=$arr['CUSTOMER_TALLYID'];
            }
            if(isset($arr['ADDRESS1']) && !empty($arr['ADDRESS1'])){
              $addressline1=$arr['ADDRESS1'];
            }
            if(isset($arr['ADDRESS2']) && !empty($arr['ADDRESS2'])){
              $addressline2=$arr['ADDRESS2'];
            }
            if(isset($arr['PINCODE']) && !empty($arr['PINCODE'])){
              $pin=$arr['PINCODE'];
            }
            if(isset($arr['PHONENO']) && !empty($arr['PHONENO'])){
              $phone=$arr['PHONENO'];
            }
            if(isset($arr['MOBILENO']) && !empty($arr['MOBILENO'])){
              $mobile=$arr['MOBILENO'];
            }
            if(isset($arr['EMAIL']) && !empty($arr['EMAIL'])){
              $email=$arr['EMAIL'];
            }
            if(isset($arr['GSTNO']) && !empty($arr['GSTNO'])){
              $pan=$arr['GSTNO'];
            }
            if(isset($arr['CREDITLIMIT']) && !empty($arr['CREDITLIMIT'])){
              $creditlimit=$arr['CREDITLIMIT'];
            }
            if(isset($arr['CLOSING']) && !empty($arr['CLOSING'])){
              $closingbalance=$arr['CLOSING'];
            }
            if(isset($arr['OVERDUE']) && !empty($arr['OVERDUE'])){
              $dueamount=$arr['OVERDUE'];
            }
            if(isset($arr['CONTACTPERSON']) && !empty($arr['CONTACTPERSON'])){
              $contactpersonname=$arr['CONTACTPERSON'];
            }
            if(isset($arr['CUSTOMERNAME']) && !empty($arr['CUSTOMERNAME'])){
              $companyname=$arr['CUSTOMERNAME'];
            }

            $oldparty = DB::table('clients')->where('client_code',$partycode)->where('company_id',$company_id)->whereNull('deleted_at')->first();
            if($oldparty){
              DB::table('clients')
              ->where("id", '=',  $oldparty->id)
              ->update(['company_name' =>$companyname,'name' => $contactpersonname, 'client_type' => $partytypeid, 'country' =>$countryid, 'state' => $stateid, 'city' =>$cityid, 'pin'=> $pin,'client_code' => $partycode,'superior' => $superiorid, 'business_id'=> $businessid, 'pan' => $pan,'email' => $email,'phone' => $phone, 'mobile' => $mobile, 'address_1' => $addressline1, 'address_2' => $addressline2, 'closing_balance'=> $closingbalance,'due_amount'=> $dueamount,'credit_limit'=> $creditlimit]);

              $assignBeat=$this->assignBeat($oldparty->id, $beatid);
            
            }else{
              $data = Client::create(['company_id' =>$company_id, 'company_name' =>$companyname,'name' => $contactpersonname, 'client_type' => $partytypeid, 'country' =>$countryid, 'state' => $stateid, 'city' =>$cityid, 'pin'=> $pin,'client_code' => $partycode,'superior' => $superiorid, 'business_id'=> $businessid, 'pan' => $pan,'email' => $email,'phone' => $phone, 'mobile' => $mobile, 'address_1' => $addressline1, 'address_2' => $addressline2, 'closing_balance'=> $closingbalance,'due_amount'=> $dueamount,'credit_limit'=> $creditlimit,'status'=> 'Active']); 

              $assignBeat=$this->assignBeat($data->id, $beatid);

              $this->updateHandle($company_id, $data["id"]);
            }
          }   
        }catch(Exception $e) {
          echo $e->getMessage();
        }
      }
      //***** PARTY IMPORT ENDS ******//
      
    }
  }
  
  
  public function getProductXml(){

    $tally_details = DB::table('tally_schedule')
      ->whereNotNull('username')
      ->whereNotNull('password')
      ->whereNull('deleted_at')
      ->whereNotNull('source')
      ->select('company_id','company_name','username','password','url','duration','product_inward','product_path_inward','party_inward','party_path_inward','source','get_order_by')
      ->get();
     foreach($tally_details as $tally_detail){
      //****** PRODUCT IMPORT ********////
      
      if($tally_detail->product_inward==1 && !empty($tally_detail->source)){
        try {
          if($tally_detail->source=='Dropbox'){
            $contents2 = Storage::disk('dropbox')->get($tally_detail->product_path_inward);
          }else{
            $filesystem2 = new Filesystem(new Adapter([
              'host'     => $tally_detail->url,
              'username' => $tally_detail->username,
              'password' => $tally_detail->password,
            ]));
            $contents2=$filesystem2->read($tally_detail->product_path_inward);
          }

          $new2 = simplexml_load_string($contents2);  
          $con2 = json_encode($new2); 
          $newArr2 = json_decode($con2, true);  
         // dd($newArr2);       
          $company_id=$tally_detail->company_id;
          if(isset($newArr2['PRODUCT']) && !empty($newArr2['PRODUCT'])){
            foreach($newArr2['PRODUCT'] as $arr2){
              $categoryid='';
              $brandid=''; 
              $details='';
              $variantname='';
              $shortdesc=''; 
              $variantattr='';

              if(isset($arr2['UNITS']) && !empty($arr2['UNITS'])){
                  $unitarrs=explode(" ",$arr2['UNITS']);
                   unset($unitarrs[1]); 
                unset($unitarrs[2]); 
                // $unitstr1=str_replace('of','',$arr2['UNITS']);
                // $unitstr=preg_replace('/[0-9]+/', '', $unitstr1);
                // $unitarrs=explode("  ",$unitstr);
                //dd($unitarrs);
                foreach($unitarrs as $unitarr){
                  $unit = UnitTypes::where('company_id',$company_id)->where('name',$unitarr)->whereNull('deleted_at')->first();
                  if($unit){
                    $unitid=$unit->id;
                  }else{
                    $unitadd = new UnitTypes;
                    $unitadd->name = $unitarr;
                    $unitadd->symbol = $unitarr;
                    $unitadd->company_id = $company_id;
                    $unitadd->status = 'Active';
                    $unitadd->save();
                    $unitid=$unitadd->id;
                  }
                }
              }

              if(isset($arr2['SUBGROUP']) && !empty($arr2['SUBGROUP'])){
                $catname=$arr2['SUBGROUP'];
                $category = Category::where('company_id',$company_id)->where('name',$catname)->first();
                if($category){
                  $category_id=$category->id;
                }else{
                  $catadd = new Category;
                  $catadd->name = $catname;
                  $catadd->company_id = $company_id;
                  $catadd->status = 'Active';
                  $catadd->save();
                  $category_id=$catadd->id;
                }
              }

              if(isset($arr2['PRODUCT_TALLYID']) && !empty($arr2['PRODUCT_TALLYID'])){
                $productcode=$arr2['PRODUCT_TALLYID'];
              }
              
              if(isset($arr2['TEITMMASDIRECTCUSTOMER']) && !empty($arr2['TEITMMASDIRECTCUSTOMER'])){
                $mrp=$arr2['TEITMMASDIRECTCUSTOMER'];
              }else{
                $mrp=0;
              }
              //
            //   if(isset($arr2['TEITMMASDIRECTCUSTOMER']) && !empty($arr2['TEITMMASDIRECTCUSTOMER'])){
            //     $ratename='DIRECTCUSTOMER';
            //   // $custommrp=$arr2['TEITMMASDIRECTCUSTOMER'];
            //     $rateid=$this->createRate($company_id, $ratename);
            //   }

            //   if(isset($arr2['TEITMMASDISTRIBUTOR']) && !empty($arr2['TEITMMASDISTRIBUTOR'])){
            //     $ratename='DISTRIBUTOR';
            //   // $custommrp=$arr2['TEITMMASDISTRIBUTOR'];
            //     $rateid=$this->createRate($company_id, $ratename);
            //   }

            //   if(isset($arr2['TEITMMASRETAILER']) && !empty($arr2['TEITMMASRETAILER'])){
            //     $ratename='RETAILER';
            //   // $custommrp=$arr2['TEITMMASRETAILER'];
            //     $rateid=$this->createRate($company_id, $ratename);
            //   }
              
              // if(isset($arr2['TEITMMASUPCOUNTRY']) && !empty($arr2['TEITMMASUPCOUNTRY'])){
              //   $ratename='UPCOUNTRY';
              //   $custommrp=$arr2['TEITMMASUPCOUNTRY'];
              //   $rateid=$this->createRate($company_id, $ratename);
              // }
              //
              $custommrp='';
              $oldproduct = DB::table('products')->where('product_code',$productcode)->where('company_id',$company_id)->first();
              if($oldproduct){
                DB::table('products')
                  ->where("id", '=',  $oldproduct->id)
                  ->update(['product_name' =>$arr2['PRODUCTNAME'], 'category_id' => $category_id,'unit' => $unitid,'mrp'=>$mrp]);
                
                if(isset($arr2['TEITMMASDIRECTCUSTOMER']) && !empty($arr2['TEITMMASDIRECTCUSTOMER'])){
                   // if($mrp!=$arr2['TEITMMASDIRECTCUSTOMER']){
                $ratename='DIRECTCUSTOMER';
                $custommrp=$arr2['TEITMMASDIRECTCUSTOMER'];
                 $rateid=$this->createRate($company_id, $ratename);
                $ratedetailid=$this->createRateDetail($rateid, $oldproduct->id, $custommrp);
                   // }
              }

              if(isset($arr2['TEITMMASDISTRIBUTOR']) && !empty($arr2['TEITMMASDISTRIBUTOR'])){
                   if($mrp!=$arr2['TEITMMASDISTRIBUTOR']){
                $ratename='DISTRIBUTOR';
                $custommrp=$arr2['TEITMMASDISTRIBUTOR'];
                 $rateid=$this->createRate($company_id, $ratename);
                 $ratedetailid=$this->createRateDetail($rateid, $oldproduct->id, $custommrp);
                   }
              }

              if(isset($arr2['TEITMMASRETAILER']) && !empty($arr2['TEITMMASRETAILER'])){
                   if($mrp!=$arr2['TEITMMASRETAILER']){
                $ratename='RETAILER';
                $custommrp=$arr2['TEITMMASRETAILER'];
                 $rateid=$this->createRate($company_id, $ratename);
                $ratedetailid=$this->createRateDetail($rateid, $oldproduct->id, $custommrp);
                   }
              }
                  
                 
              }else{
                $productadd = new Product;
                $productadd->product_name = $arr2['PRODUCTNAME'];
                $productadd->product_code = $arr2['PRODUCT_TALLYID'];
                $productadd->company_id = $company_id;
                $productadd->category_id = $category_id;
                $productadd->unit = $unitid;
                $productadd->mrp=$mrp;
                $productadd->variant_flag = 0;
                $productadd->star_product = 0;
                $productadd->moq = 1;
                $productadd->app_visibility = 1;
                $productadd->status = 'Active';
                $productadd->save();
                
                if(isset($arr2['TEITMMASDIRECTCUSTOMER']) && !empty($arr2['TEITMMASDIRECTCUSTOMER'])){
                $ratename='DIRECTCUSTOMER';
                $custommrp=$arr2['TEITMMASDIRECTCUSTOMER'];
                $rateid=$this->createRate($company_id, $ratename);
                $ratedetailid=$this->createRateDetail($rateid, $productadd->id, $custommrp);
              }

              if(isset($arr2['TEITMMASDISTRIBUTOR']) && !empty($arr2['TEITMMASDISTRIBUTOR'])){
                $ratename='DISTRIBUTOR';
                $custommrp=$arr2['TEITMMASDISTRIBUTOR'];
                 $rateid=$this->createRate($company_id, $ratename);
                 $ratedetailid=$this->createRateDetail($rateid, $productadd->id, $custommrp);
              }

              if(isset($arr2['TEITMMASRETAILER']) && !empty($arr2['TEITMMASRETAILER'])){
                $ratename='RETAILER';
                $custommrp=$arr2['TEITMMASRETAILER'];
                 $rateid=$this->createRate($company_id, $ratename);
                $ratedetailid=$this->createRateDetail($rateid, $productadd->id, $custommrp);
              }

                // $ratedetailid=$this->createRateDetail($rateid, $productadd->id, $custommrp);
              } 
            }
          }
        }catch(Exception $e) {
          echo $e->getMessage();
        }
      }
      //***** PRODUCT IMPORT ENDS ******//
    }
  }

  private function addPermission($partytype,$permissionCategory,$permissionTag,$roleAdmin){
    $stringName                         = str_replace(' ','-',$partytype->name);
    $permission                         = new Permission;
    $permission->permission_category_id = $permissionCategory->id;
    $permission->company_id             = $partytype->company_id;
    $permission->name                   = $stringName.'-'.$permissionTag;
    $permission->guard_name             = 'web';
    $permission->permission_type        = 'Company';
    $permission->enabled                = 1;
    $permission->is_mobile              = 1;
    $permission->save();

    $roleAdmin->givePermissionTo($permission->id);
    //Log::info('info', array("permission"=>print_r($permission->name,true)));
    return $permission->id;
  }

  private function updateHandle($company_id, $id){
    try{
      $admins = Employee::whereCompanyId($company_id)->whereIsAdmin(1)->pluck('id')->toArray();
      foreach($admins as $admin){
        DB::table('handles')->insert([
          'company_id' => $company_id,
          'employee_id' => $admin,
          'client_id' => $id,
          'map_type' => 1
        ]);
      }
    }catch(\Exception $e){
      Log::info(array("Import Error :-"), $e->getMessage());
    }
  }

  protected function createPartyType($company_id, $partytypename){
    if (str_contains($partytypename, 'Distributor') || str_contains($partytypename, 'R-Distributor') || str_contains($partytypename, 'Upcountry')) { 
        $ptypename='Distributor';
    } elseif (str_contains($partytypename, 'Retail')) { 
      $ptypename='Retail';
    }else{
      $ptypename='Direct';
    }
    $partytype = PartyType::where('company_id',$company_id)
      ->where('name',$ptypename)
      ->first();

    if($partytype){
      $partytypeid=$partytype->id;
    }else{
      $partytypeadd = new PartyType;
      $partytypeadd->name = $ptypename;
      $partytypeadd->parent_id = 0;
      $partytypeadd->company_id = $company_id;
      $partytypeadd->save();
      $partytypeid=$partytypeadd->id;

      $permissionCategory                           = new PermissionCategory;
      $permissionCategory->company_id               = $company_id;
      $permissionCategory->permission_model_id      = $partytypeid;
      $permissionCategory->permission_model         = 'PartyType';
      $permissionCategory->permission_category_type = 'Company';
      $permissionCategory->name                     = str_replace(' ','_',$partytypeadd->name);
      $permissionCategory->display_name             = 'Party Type: '.$partytypeadd->name;
      $permissionCategory->indexing_priority        = 5;
      $permissionCategory->save();

      $roleAdmin=Role::where('name','Full Access')->where('company_id',$company_id)->first();

      $create_id = $this->addPermission($partytypeadd,$permissionCategory,'create',$roleAdmin);
      $view_id = $this->addPermission($partytypeadd,$permissionCategory,'view',$roleAdmin);
      $update_id = $this->addPermission($partytypeadd,$permissionCategory,'update',$roleAdmin);
      $delete_id = $this->addPermission($partytypeadd,$permissionCategory,'delete',$roleAdmin);
      $status_id = $this->addPermission($partytypeadd,$permissionCategory,'status',$roleAdmin);
    }
    return $partytypeid;
  }

  protected function createBeat($company_id, $beatname){
    $beat =Beat::where('company_id',$company_id)
      ->where('name',$beatname)
      ->first();

    if($beat){
      $beatid=$beat->id;
    }else{
      $newbeat = new Beat;
      $newbeat->name = $beatname;
      $newbeat->company_id = $company_id;
      $newbeat->status = 'Active';
      $newbeat->save();
      $beatid=$newbeat->id;
    }
    return $beatid;
  }

  private function createGroup($company_id, $businesstypename){
    $businesstype = BusinessType::where('company_id',$company_id)->where('business_name',$businesstypename)->first();
    if($businesstype){
      $businessid=$businesstype->id;
    }else{
      $businesstypeadd = new BusinessType;
      $businesstypeadd->business_name = $businesstypename;
      $businesstypeadd->company_id = $company_id;
      $businesstypeadd->save();
      $businessid=$businesstypeadd->id;
    }
    return $businessid;
  }

  private function assignBeat($client_id, $beat_id){
    DB::table('beat_client')
    ->where('client_id', $client_id)
    ->delete();

    DB::table('beat_client')->insert([
        'client_id' => $client_id,
        'beat_id' => $beat_id,
      ]);
  }

  private function createRate($company_id, $ratename){
    $rate =RateSetup::where('company_id',$company_id)->where('name',$ratename)->first();
    if($rate){
      $rateid=$rate->id;
    }else{
      $newrate = new RateSetup;
      $newrate->name = $ratename;
      $newrate->company_id = $company_id;
      $newrate->save();
      $rateid=$newrate->id;
    }
    return $rateid;
  }

  private function createRateDetail($rate_id, $product_id, $custommrp){
    $ratedetail =RateDetail::where('rate_id',$rate_id)
    ->where('product_id',$product_id)
    ->first();
    if($ratedetail){
        DB::table('rate_details')
                ->where('id', $ratedetail->id)
                ->update(['mrp' => $custommrp]);
      $rateid=$ratedetail->id;
    }else{
      $newratedetail = new RateDetail;
      $newratedetail->rate_id = $rate_id;
      $newratedetail->product_id = $product_id;
      $newratedetail->mrp = $custommrp;
      $newratedetail->save();
      $rateid=$newratedetail->id;
    }
    return $rateid;
  }

}