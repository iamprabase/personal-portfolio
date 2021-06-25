<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ActivityPriority;
use Auth;
use View;
use DB;

class ActivityPriorityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
            if(config('settings.activities') || !Auth::user()->can('settings-view'))
            $company_id = config('settings.company_id');
            $activityPriority = ActivityPriority::where('company_id', $company_id)->orderBy('name', 'ASC')->withTrashed()->get();
            return view('company.activity-priority.index', compact('activityPriority'));
        }
        return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
    }

    public function create()
    {
        if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
        $data['titile'] = 'Add Activity Priority';
        $data['row'] = (object)['name' => ''];
        $data['url'] = url(domain_route('activity-priority.store'));
        $data['method'] = 'POST';
        return view('company.activity-priority.create', $data);
        }
            
        return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
    }//end create

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        $company_id = config('settings.company_id');
        $existsPriority = ActivityPriority::where('company_id',$company_id)->where('name',$request->name)->first();
        if($existsPriority!=null){
            $data['result'] = false;
        }else{
            $input['name'] = $request->name;
            $input['company_id'] = $company_id;
            $activityPriority = ActivityPriority::create($input);

            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "activity_priority", "activity_priority" => $activityPriority, "action" => "add");
            $sent = sendPushNotification_($fbIDs, 16,null, $dataPayload);




            $data = [];
            $data['activityPriorities'] = ActivityPriority::where('company_id', $input['company_id'])->orderBy('name', 'ASC')->get();
            $data['count'] = ActivityPriority::where('company_id', $input['company_id'])->get()->count();
            $data['result'] = true;            
        }
        return $data;
    }//end store

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($domain, $id)
    {
        if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
        $row = ActivityPriority::find($id);
        $company_id = config('settings.company_id');
        if ($row->company_id != $company_id || !$row) {
            return response()->json(['error' => "This activity priority doesn't exist", 'url' => domain_route('company.admin.activities-priority.index')], 422);

        }
        $data['row'] = $row;
        $data['titile'] = 'Edit Activity Priority';
        $data['url'] = url(domain_route('activity-priority.update', [$row->id]));
        $data['method'] = 'PUT';
        return view('customer.activity-priority.create', $data);
        }
            
        return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
    }//end edit

    public function updateActivityPriority(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $row = ActivityPriority::where('id',$request->id)->withTrashed()->first();
        $company_id = config('settings.company_id');
        $existsPriority = ActivityPriority::where('company_id',$company_id)->where('name',$request->name)->first();
        if ($existsPriority!=null) {
            $data['result'] = false;

        }else{
            $input = $request->all();
            $input['company_id'] = config('settings.company_id');
            $row->update($input);

            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "activity_priority", "activity_priority" => $row, "action" => "update");
            $sent = sendPushNotification_($fbIDs, 16,null, $dataPayload);


            $data = [];
            $data['activityPriorities'] = ActivityPriority::where('company_id', $company_id)->orderBy('name', 'ASC')->get();
            $data['count'] = ActivityPriority::where('company_id', $company_id)->get()->count();
            $data['result'] = true;
        }    
        return $data;
    }//end update


    public function deleteActivityPriority(Request $request)
    {
        $company_id = config('settings.company_id');
        $row = ActivityPriority::where('company_id',$company_id)->where('id',$request->id)->withTrashed()->first();
        if ($row==null  || $row->activities->count()!=0 ) {
            $data['result'] = false;
        } else {
            $row->forceDelete();


            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "activity_priority", "activity_priority" => $row, "action" => "delete");
            $sent = sendPushNotification_($fbIDs, 16,null, $dataPayload);

            $data = [];
            $data['activityPriorities'] = ActivityPriority::where('company_id', $company_id)->orderBy('name', 'ASC')->get();;
            $data['count'] = ActivityPriority::where('company_id', $company_id)->get()->count();
            $data['result'] = true;
        }
        return $data;
    }//end destroy
}
