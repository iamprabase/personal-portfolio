<?php

namespace App\Http\Controllers\Company\Admin;

use DB;
use Log;

Use Auth;
use App\Employee;
use App\VisitPurpose;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class VisitPurposeController extends Controller
{
    private $company_id;
    public function __construct()
    {
        $this->middleware('auth');
        $this->company_id = config('settings.company_id');
        // $this->middleware('permission:PartyVisit-create', ['only' => ['store']]);
        // $this->middleware('permission:PartyVisit-view');
        // $this->middleware('permission:PartyVisit-update', ['only' => ['update']]);
        // $this->middleware('permission:PartyVisit-delete', ['only' => ['destroy']]);
    }

    private function pushNotification($instance, $action){
      try{
        $fbIDs = Employee::where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "visitpurpose", "visitpurpose" => $instance, "action" => $action);
        Log::info($dataPayload);
        $sent = sendPushNotification_($fbIDs, 41, null, $dataPayload);
        Log::info($sent);
        
        return $sent;
      }catch(\Exception $e){
        return false;
      }
    }

    private function makeView(){
      $visit_purposes = VisitPurpose::whereCompanyId($this->company_id)->orderBy('title','ASC')->get(['id', 'title'])->map(function($visit_purpose) {
          $visit_purpose->deleteable = $visit_purpose->client_visit->count()==0;
          return $visit_purpose;
        })->toArray();
      $view = View::make('company.settings.ajaxVisitPurpose',compact('visit_purposes'))->render();

      return $view;
    }

    public function store(Request $request, $domain)
    {

      $this->validate($request, [
        'title' => 'required|unique:visit_purposes,title,NULL,id,company_id,' . $this->company_id . ',deleted_at,NULL',
      ]);

      $formData = $request->title;
      $visit_instance = VisitPurpose::create([
        'company_id' => $this->company_id,
        'title' => $formData
      ]);
      $this->pushNotification($visit_instance, "add");

      $view = $this->makeView();
      $response =  array('status' => true, 'data' => $visit_instance, 'body' => $view, 'msg' => 'Visit Purpose Added.');
      return response()->json($response);
      
    }

    public function update(Request $request, $domain)
    {
      $id = $request->id;
      $this->validate($request, [
        'title' => 'required|unique:visit_purposes,title,' . $id . ',id,company_id,' . $this->company_id . ',deleted_at,NULL',
      ]);
      $get_visit_instance = VisitPurpose::find($id);

      $formData = $request->title;
      $visit_instance = $get_visit_instance->update([
        'title' => $formData
      ]);
      $this->pushNotification($visit_instance, "update");

      $view = $this->makeView();
      $response =  array('status' => true, 'data' => $visit_instance, 'body' => $view, 'msg' => 'Visit Purpose Updated.');
      return response()->json($response);
    }

    public function destroy(Request $request, $domain)
    {
      $id = $request->id;
      $get_visit_instance = VisitPurpose::find($id);
      if ($get_visit_instance) {
        if($get_visit_instance->client_visit->count()>0) {
          $view = $this->makeView();
          $response =  array('status' => true, 'data' => $get_visit_instance, 'body' => $view, 'msg' => 'Visit Purpose cannot be deleted.');
          return response()->json($response);
        }
        $get_visit_instance->delete();
        $this->pushNotification($get_visit_instance, "delete");
      }
     
      $view = $this->makeView();
     
      $response =  array('status' => true, 'data' => $get_visit_instance, 'body' => $view, 'msg' => 'Visit Purpose Deleted.');
      return response()->json($response);
    }
}