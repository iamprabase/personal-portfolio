<?php

namespace App\Http\Controllers\Company\Admin;

use App\CustomField;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomFieldController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store($domain,Request $request)
    {
        $company_id = config('settings.company_id');
        $customFieldExists = CustomField::where('company_id',$company_id)->where('title',$request->title)->first();
        if($customFieldExists)
            return response(['status'=>false,'message'=>'This custom field already exists']);
        if($request->type == "Multiple Images" || $request->type == "File"){
            $customFieldCount = CustomField::where('company_id',$company_id)->where('for',$request->module)->where('type',$request->type)->count();
            if($customFieldCount>2){
                return response(['status'=>false,'message'=>'This custom field can\'t be created more than three times']);
            }
        }
        
        $customField = new CustomField;
        $customField->company_id = $company_id;
        $customField->type = $request->type;

        if ($request->type == "Single option" || $request->type == "Multiple options") {
            //$opt_json =explode(',',$request->options);
            $customField->options = json_encode(array_filter($request->options,'strlen'));
        }
        $customField->title = $request->title;
        $customField->for = $request->module;
        $customField->add = 'Yes';
        $customField->details = 'Yes';
        $customField->slug = Str::slug($request->title . '-' . $request->module);
        Log::info( Str::slug($request->title . '-' . $request->module));
        $customField->save();
        $customField->slug = Str::slug($request->title . '-' . $request->module.'-'.$customField->id);
        Log::info(Str::slug($request->title . '-' . $request->module.'-'.$customField->id));
        $customField->save();
        $dataPayload = array("data_type" => "custom_field", "custom_field" => $customField, "action" => "add");
        $msgID = sendPushNotification_(getFBIDs($company_id), 34, null, $dataPayload);
        switch ($customField->for) {
            case 'Party':
                return view('company.settings.ajaxCustomField')->with('custom_fields', CustomField::where('company_id',$company_id)->where('for', 'Party')->orderBy('id', 'DESC')->get());
                break;

            default:
                # code...
                die();
                break;
        }
    }

    public function updateVisibility($domain,Request $request)
    {
        $company_id = config('settings.company_id');
        $customField = CustomField::where('company_id',$company_id)->where('id',$request->id)->first();
        if(!$customField){
            return response(['status'=>false,'message'=>'No Custom Field Found']);
        }

        if($request->type=="add"){
            $customField->add = $request->value;
        }

        if($request->type=="details"){
            $customField->details = $request->value;
        }

        $customField->save();
        return response(['status'=>true,'message'=>'Updated Field Visibility','data'=>$customField]);
    }

    public function updateTitle($domain,Request $request)
    {
        $company_id = config('settings.company_id');
        $customField = CustomField::where('company_id',$company_id)->where('id',$request->id)->first();
        if(!$customField)
            return response(['status'=>false,'message'=>'No Custom Field Found']);
        $customFieldExists = CustomField::where('company_id',$company_id)->where('title',$request->title)->where('id','!=',$customField->id)->first();
        if($customFieldExists)
            return response(['status'=>false,'message'=>'This custom field already exists']);
        $customField->title = $request->title;
        $customField->slug = Str::slug($request->title . '-' . $request->module);
        $customField->save();
        $customField->slug = Str::slug($request->title . '-' . $request->module.'-'.$customField->id);
        $customField->save();
        $dataPayload = array("data_type" => "custom_field", "custom_field" => $customField, "action" => "update");
        $msgID = sendPushNotification_(getFBIDs($company_id), 34, null, $dataPayload);
        return response(['status'=>true,'message'=>'Updated Title Successfully','title'=>$customField->title]);
    }

    public function updateStatus($domain,Request $request)
    {
        $company_id = config('settings.company_id');

        $customField = CustomField::where('company_id',$company_id)->where('id',$request->id)->first();

        $customField->status=$request->status;
        
        $customField->save();
        $dataPayload = array("data_type" => "custom_field", "custom_field" => $customField, "action" => "status update");
        $msgID = sendPushNotification_(getFBIDs($company_id), 34, null, $dataPayload);
        return view('company.settings.ajaxCustomField')->with('custom_fields', CustomField::where('company_id',$company_id)->where('for', 'Party')->orderBy('id', 'DESC')->get());
    }

    public function custom_edit(Request $request){
       //return "i am in contraoler"; 
        $id=$request->id;
        $company_id = config('settings.company_id');

        $request->validate([
            'title' => 'required|unique:custom_fields,title,' . $id,
        ]);
        // $validator=$request->validate([
        //     'title'=> 'required|title|unique:custom_fields,title,'. $id .'',
        // ]);
        //  $validator = \Validator::make($request->all(), [
        //     'title' => 'required|title|unique:custom_fields,title,'. $id .'',
        // ]);
        //  if ($validator->fails())
        // {
        //     return response()->json(['errors'=>$validator->errors()->all()]);
        // }
               
        $customFieldExists = CustomField::where('company_id',config('settings.company_id'))->where('title',$request->title)->where('id','!=',$id)->first();
        if($customFieldExists)
           return view('company.settings.ajaxCustomField')->with('custom_fields', CustomField::where('for', 'Party')->orderBy('id', 'DESC')->get());
            //return response(['status'=>false,'message'=>'This custom field already exists']);

          $customField = CustomField::where('company_id',$company_id)->where('id',$request->id)->first();
          if(!$customField)
             return response(['status'=>false,'message'=>'No Custom Field Found']);
         
         if ($customField->type == "Single option" || $customField->type == "Multiple options") {
            $request->validate([
                'options' => 'required',
            ]);
           // $opt_json = array_filter(explode('%0A', str_replace('%2F','/',htmlspecialchars_decode($request->options))));
           // $customField->options = json_encode($opt_json);
           
            $customField->options = json_encode(array_filter($request->options,'strlen'));
        }

        $customField->title = $request->title;
        $customField->slug = Str::slug($request->title . '-' . $request->module);
        $customField->save();
        // $customField->slug = Str::slug($request->title . '-' . $request->module.'-'.$customField->id);
        // $customField->save();
        $dataPayload = array("data_type" => "custom_field", "custom_field" => $customField, "action" => "update");
        $msgID = sendPushNotification_(getFBIDs($company_id), 34, null, $dataPayload);
       return view('company.settings.ajaxCustomField')->with('custom_fields', CustomField::where('company_id',$company_id)->where('for', 'Party')->orderBy('id', 'DESC')->get());
    }

    public function destroy($domain,Request $request)
    {
        $company_id = config('settings.company_id');
        $customField = CustomField::where('company_id',$company_id)->where('id',$request->id)->first();
        if(!$customField)
            return response(['status'=>false,'message'=>'No Custom Field Found']);
        $customField->delete();
        $dataPayload = array("data_type" => "custom_field", "custom_field" => $customField, "action" => "delete");
        $msgID = sendPushNotification_(getFBIDs($company_id), 34, null, $dataPayload);
        switch ($customField->for) {
            case 'Party':
                return view('company.settings.ajaxCustomField')->with('custom_fields', CustomField::where('company_id',$company_id)->where('for', 'Party')->orderBy('id', 'DESC')->get());
                break;

            default:
                # code...
                die();
                break;
        }

    }
}