<?php

namespace App\Http\Controllers\API;

use DB;
use Log;
use Auth;
use App\Image;
use App\Company;
use App\ClientVisit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ClientVisitController extends Controller
{

  private $user;
  private $company_id;
  private $employee;
  private $employee_id;

  public function __construct()
  {
    $this->middleware('auth:api');
    $this->middleware(function ($request, $next) {
      $this->user = Auth::user();
      $this->company_id = $this->user->company_id;
      $this->employee = $this->user->employee;
      $this->employee_id = $this->employee->id;

      return $next($request);
    });
  }

  private function getImagePath($companyID, $module, $imageName)
	{
    $domain = Company::where("id", $this->company_id)->where("is_active", 2)->pluck("domain")->first();
    if (empty($domain)) {
      return "";
    }

    $imagePath = "uploads/" . $domain . "/" . $module . "/" . $imageName;

    return $imagePath;
  }
  
  private function formatClientVisit($object){
    try{
      $formatted_images = $object->images->map(function($image) {
                      return $this->formatImages($image);
                    });
      $image_ids = array();
      $images = array();
      $image_paths = array();
      if(!empty($formatted_images)){
        foreach($formatted_images as $image){
          array_push($image_ids, strval( $image['id']));
          array_push($images, $image['image_name']);
          array_push($image_paths, $image['image_path']);
        }
      }
      
      $formatted_data = [
        'id' => $object->id,
        'unique_id' => $object->unique_id,
        'client_id' => $object->client_id,
        'client_name' => $object->client?$object->client->company_name:NULL,
        'employee_id' => $object->employee_id,
        'employee_name' => $object->employee->name,
        'visit_purpose_id' => $object->visitpurpose?$object->visitpurpose->id:null,
        'visit_purpose' => $object->visitpurpose?$object->visitpurpose->title:null,
        "date" => $object->date,
        "start_time" => $object->start_time,
        "end_time" => $object->end_time,
        "comments" => $object->comments,
        // 'images' => $object->images->map(function($image) {
        //               return $this->formatImages($image);
        //             }),
        "image_ids" => json_encode($image_ids, true),
        "images" => json_encode($images, true),
        "image_paths" => json_encode($image_paths, true),
        
      ];

      return $formatted_data;
    }catch(\Exception $e){
      Log::error(array("Format Client Visit Purpose () => "), array($e->getMessage()));
      return array();
    }
  }

  private function formatImages($image){
    try{
      $formatted_data = [
        'id' => $image->id,
        'image_name' => $image->image, 
        'image_path' => $image->image_path
      ];

      return $formatted_data;        
    }catch(\Exception $e){
      Log::error(array("Format Images () => "), array($e->getMessage()));
      Log::info($images);
      return null;
    }
  }

  public function index(){
    try{
      $companyId = $this->company_id;
      $employeeId = $this->employee_id;
      $data = ClientVisit::whereCompanyId($companyId)->whereEmployeeId($employeeId)->with(['employee' => function($query){
                  return $query->select('employees.name', 'employees.id');
                }])->with(['client' => function($query){
                  return $query->select('clients.company_name', 'clients.id');
                }])->with(['visitpurpose' => function($query){
                  return $query->select('visit_purposes.title', 'visit_purposes.id');
                }])->with('images')->get()->map(function($client_visit_purpose){
                  return $this->formatClientVisit($client_visit_purpose);
              });
      $response = array("status" => true, "message" => "Successfully Fetched", "data" => $data);
      
      return response()->json($response);
    }catch(\Exception $e){
      Log::error($e->getMessage());
      $response = array("status" => false, "message" => $e->getMessage(), "data" => null);
      
      return response()->json($response);
    }
  }

  public function store(Request $request)
  {
    $user = Auth::user();
    $this->validate($request, [
      'client_id' => 'required',
      'date' => 'required',
      'start_time' => 'required',
      'end_time' => 'required',
      'visit_purpose' => 'required'
    ]);
    
    try{
      $unique_id = $request->unique_id;
      $companyId = $this->company_id;
      $client_id = $request->client_id;
      $employee_id = $this->employee_id;
      $date = $request->date;
      $start_time = $request->start_time;
      $end_time = $request->end_time;
      $visit_purpose = $request->visit_purpose;
      $comments = $request->comments;
      $images = $request->has('images')?$request->images:null;

      $data = array(
          'unique_id' => $unique_id,
          'company_id' => $companyId,
          'client_id' => $client_id,
          'employee_id' => $employee_id,
          'date' => $date,
          'start_time' => $start_time,
          'end_time' => $end_time,
          'visit_purpose_id' => $visit_purpose,
          'comments' => $comments,
        );

      $instance = ClientVisit::whereCompanyId($companyId)
                  ->where('employee_id', $employee_id)
                  ->where('date', $date)
                  ->where('start_time',$start_time)
                  ->where('end_time',$end_time)
                  ->first();
      if($instance) $object_instance = $instance;
      else $object_instance = ClientVisit::create($data);
      
      $image_ids = array(); $images_names = array(); $images_paths = array();

      if($instance){
         $insert_image_data = Image::whereCompanyId($companyId)->whereType("client_visits")->whereTypeId($object_instance->id)->get(['id', 'image', 'image_path']);
        if ($insert_image_data->first()) {
            foreach ($insert_image_data as $image_data) {
                array_push($image_ids, $image_data->id);
                array_push($images_names, $image_data->image);
                array_push($images_paths, $image_data->image_path);
            }
        }
      }else{
        if (!empty($images)) {
          foreach ($images as $key => $value) {
            $tempImageName = time().$key.substr(uniqid(),3,6);
            $tempImageDir  = $this->getImagePath($companyId, 'client_visits', $tempImageName);
            $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
            $put = Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
            
            $insert_image = Image::create([
              "type"        => "client_visits",
              "type_id"     => $object_instance->id,
              "company_id"  => $companyId,
              "employee_id" => $employee_id,
              "image"       => $tempImageName,
              "image_path"  => $tempImagePath,
            ]);
  
            array_push($image_ids, $insert_image->id);
            array_push($images_names, $insert_image->image);
            array_push($images_paths, $insert_image->image_path);
            
          }
        }
      }

      $object_instance->image_ids = json_encode($image_ids);
      $object_instance->images_names = json_encode($images_names);
      $object_instance->images_paths = json_encode($images_paths);

      $response = array("status" => true, "message" => "Successfully Saved", "data" => $object_instance);
      
      return response()->json($response);

    }catch(\Exception $e){
      Log::error($e->getMessage());
      $response = array("status" => false, "message" => $e->getMessage(), "data" => null);
      
      return response()->json($response);
    }
  }

  public function update(Request $request){
    $user = Auth::user();
    $this->validate($request, [
      'client_id' => 'required',
      'date' => 'required',
      'start_time' => 'required',
      'end_time' => 'required',
      'visit_purpose' => 'required',
      'client_visit_id' => 'required'
    ]);

    try{
      $client_visit_id = $request->client_visit_id;
      $unique_id = $request->unique_id;
      $companyId = $this->company_id;
      $employee_id = $this->employee_id;

      $object_instance = ClientVisit::whereCompanyId($companyId)->where('id', $client_visit_id)->orWhere(function($query) use ($unique_id){
        if(!empty($unique_id)) $query->orWhere('unique_id', $unique_id);
      })->first();

      $client_id = $request->client_id;
      $date = $request->date;
      $start_time = $request->start_time;
      $end_time = $request->end_time;
      $visit_purpose = $request->visit_purpose;
      $comments = $request->comments;
      $images = $request->has('images')?$request->images:null;
      $deleted_images_id = $request->has('deleted_images_id')?$request->deleted_images_id:null;

      if(!empty($deleted_images_id) && !empty($client_visit_id)){
        $delete_removed_images = Image::whereCompanyId($companyId)->whereType("client_visits")->whereTypeId($client_visit_id)->whereIn('id', $deleted_images_id)->delete();
      }

      $data = array(
          'unique_id' => $unique_id,
          'company_id' => $companyId,
          'client_id' => $client_id,
          'employee_id' => $employee_id,
          'date' => $date,
          'start_time' => $start_time,
          'end_time' => $end_time,
          'visit_purpose_id' => $visit_purpose,
          'comments' => $comments,
        );

      if(!$object_instance){
        $object_instance = ClientVisit::create($data);
        $client_visit_id = $object_instance->id;
      }else{
        $data['employee_id'] = $object_instance->employee_id;
        $object_instance = tap($object_instance)->update($data);
      }
      
      $image_ids = array(); $images_names = array(); $images_paths = array();
      
      if (count($images)>0) {
        foreach ($images as $key => $value) {
          $tempImageName = time().$key.substr(uniqid(),3,6);
          $tempImageDir  = $this->getImagePath($companyId, 'client_visits', $tempImageName);
          $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
          $put = Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
          
          $insert_image = Image::create([
            "type"        => "client_visits",
            "type_id"     => $object_instance->id,
            "company_id"  => $companyId,
            "employee_id" => $employee_id,
            "image"       => $tempImageName,
            "image_path"  => $tempImagePath,
          ]);
        }
      }


      $inserted_image_datas = Image::whereCompanyId($companyId)->whereType("client_visits")->whereTypeId($client_visit_id)->get(['id', 'image', 'image_path']);
      if($inserted_image_datas->first()){
        foreach($inserted_image_datas as $insert_image_data){
          array_push($image_ids, $insert_image_data->id);
          array_push($images_names, $insert_image_data->image);
          array_push($images_paths, $insert_image_data->image_path);
        }
      }

      $object_instance->image_ids = json_encode($image_ids);
      $object_instance->images_names = json_encode($images_names);
      $object_instance->images_paths = json_encode($images_paths);

      if ($object_instance->employee_id != $this->employee_id) {
        $dataPayload = array("data_type" => "client_visit", "client_visit" => $object_instance, "action" => "update");
        $msgID       = sendPushNotification_(getFBIDs($companyId, null, $object_instance->employee_id), 43, null, $dataPayload);
      }

      $response = array("status" => true, "message" => "Successfully Updated", "data" => $object_instance);
      
      return response()->json($response);

    }catch(\Exception $e){
      Log::error($e->getMessage());
      Log::error($e->getLine());
      Log::error($e->getCode());
      $response = array("status" => false, "message" => $e->getMessage(), "data" => null);
      
      return response()->json($response);
    }
  }

  public function destroy(Request $request)
  {
    $user = Auth::user();
    $this->validate($request, [
      'client_visit_id' => 'required'
    ]);

    try{
      $client_visit_id = $request->client_visit_id;
      $unique_id = $request->has('unique_id')? $request->unique_id:null;
      $companyId = $this->company_id;
      DB::beginTransaction();

      $object_instance = ClientVisit::whereCompanyId($companyId)->where('id', $client_visit_id)->orWhere(function($query) use ($unique_id){
        if(!empty($unique_id)) $query->orWhere('unique_id', $unique_id);
      })->first();  

      if($object_instance){
        $object_instance->delete();
        $delete_removed_images = Image::whereCompanyId($companyId)->whereType("client_visits")->whereTypeId($client_visit_id)->delete();
        $image_ids = array(); $images_names = array(); $images_paths = array();
  
        $object_instance->image_ids = json_encode($image_ids);
        $object_instance->images_names = json_encode($images_names);
        $object_instance->images_paths = json_encode($images_paths);
        
        DB::commit();

        
        if ($object_instance->employee_id != $this->employee_id) {
          $dataPayload = array("data_type" => "client_visit", "client_visit" => $object_instance, "action" => "delete");
          $msgID       = sendPushNotification_(getFBIDs($companyId, null, $object_instance->employee_id), 43, null, $dataPayload);
        }
  
        $response = array("status" => true, "message" => "Successfully deleted", "data" => $object_instance);
      }else{
        $response = array("status" => true, "message" => "Data doesn't exists", "data" => null);
      }
      
      
      return response()->json($response);

    }catch(\Exception $e){
      Log::error($e->getMessage());
      $response = array("status" => false, "message" => $e->getMessage(), "data" => null);
      
      return response()->json($response);
    }
  }

  public function syncVisitModule(Request $request)
  {
    try{
      $non_synced_datas = $request->non_synced_visits;
      $decoded = json_decode($non_synced_datas, true);
      $syncJobs = $this->manageUnsyncedVisitModule($decoded);
      $syncedData = $syncJobs['synced_data'];
      $failedSyncedData = $syncJobs['unsynced_data'];
      $response = array("status" => true, "message" => "success", "data" => $syncedData, "failed_syncs" => $failedSyncedData);
      
      return response()->json($response);
    }catch(\Exception $e){
      Log::error($e->getMessage());
      $response = array("status" => false, "message" => $e->getMessage(), "data" => null, "failed_syncs" => null);
      
      return response()->json($response);
    }
  }
  
  public function manageUnsyncedVisitModule($data)
  {
    $user = Auth::user();
    $synced_data = array();
    $unsynced_data = array();
    foreach ($data as $key => $visit_data) {
      try{
        // $this->validate($visit_data, [
        //   'client_id' => 'required',
        //   'date' => 'required',
        //   'start_time' => 'required',
        //   'end_time' => 'required',
        //   'visit_purpose' => 'required'
        // ]);
        DB::beginTransaction();
        $unique_id = $visit_data['unique_id'];
        $companyId = $this->company_id;
        $client_id = $visit_data['client_id'];
        $employee_id = $this->employee_id;
        $date = $visit_data['date'];
        $start_time = $visit_data['start_time'];
        $end_time = $visit_data['end_time'];
        $visit_purpose = $visit_data['visit_purpose'];
        $comments = $visit_data['comments'];
        $images = json_decode($visit_data['images'], true);
        
        $data = array(
            'unique_id' => $unique_id,
            'company_id' => $companyId,
            'client_id' => $client_id,
            'employee_id' => $employee_id,
            'date' => $date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'visit_purpose_id' => $visit_purpose,
            'comments' => $comments,
          );

        if(!empty($unique_id)){
          $unique_to_unique_id = ClientVisit::whereCompanyId($companyId)->where('unique_id', $unique_id)->first();
          if(!$unique_to_unique_id) $object_instance = ClientVisit::create($data);
          else $object_instance = $unique_to_unique_id;
        }else{
          $object_instance = ClientVisit::create($data);
        }
        
        $image_ids = array(); $images_names = array(); $images_paths = array();

        if (!empty($images) && !$unique_to_unique_id) {
          foreach ($images as $key => $value) {
            $tempImageName = time().$key.substr(uniqid(),3,6);
            $tempImageDir  = $this->getImagePath($companyId, 'client_visits', $tempImageName);
            $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
            $put = Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
            
            $insert_image = Image::create([
              "type"        => "client_visits",
              "type_id"     => $object_instance->id,
              "company_id"  => $companyId,
              "employee_id" => $employee_id,
              "image"       => $tempImageName,
              "image_path"  => $tempImagePath,
            ]);

            array_push($image_ids, $insert_image->id);
            array_push($images_names, $insert_image->image);
            array_push($images_paths, $insert_image->image_path);
            
          }
        }elseif(!empty($images) && $unique_to_unique_id){
          $insert_image_data = Image::whereCompanyId($companyId)->whereType("client_visits")->whereTypeId($unique_to_unique_id->id)->get(['id', 'image', 'image_path']);
          if($insert_image_data->first()){
            foreach($insert_image_data as $image_data){
              array_push($image_ids, $image_data->id);
              array_push($images_names, $image_data->image);
              array_push($images_paths, $image_data->image_path);
            }
          }
        }
        DB::commit();
        $object_instance->image_ids = json_encode($image_ids);
        $object_instance->images_names = json_encode($images_names);
        $object_instance->images_paths = json_encode($images_paths);

        array_push($synced_data, $object_instance);
      }catch(\Exception $e){
        Log::error($e->getMessage());
        Log::info($visit_data);
        array_push($unsynced_data, $visit_data);
      }
    }

    $arraySyncedData['synced_data'] = $synced_data; 
    $arraySyncedData['unsynced_data'] = $unsynced_data; 
    
    return $arraySyncedData;	
  }
}
