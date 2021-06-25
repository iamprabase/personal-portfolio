<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NoOrder;
use App\Client;
use App\Company;
use App\Employee;
use App\User;
use DB;
use Auth;
use Storage;


class ZeroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function create(){
        $company_id= config('settings.company_id');
        $clients= Client::where('company_id',$company_id)->select('company_name','id')->get();
        return view('company.zeroorder.create',compact('clients'));
    }

    public function store(Request $request){
       
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;

        $customMessages = [
            'employee_id.required' => 'The Employee Name field is required.',
            'logo.mimes' => 'Upload correct file type.',
        ];

        $this->validate($request, [
            'remark'       => 'required',
            'date'      => 'required|date_format:Y-m-d',
        ], $customMessages);


        $noorder = new \App\NoOrder;
        $noorder->company_id = $company_id; 
        $noorder->employee_id = Auth::user()->EmployeeId();
        $noorder->remark = $request->get('remark');
        // $noorder->remark = $request->get('remark');
        $noorder->date = $request->date;
        if (!empty($request->get('client_id')))
            $noorder->client_id = $request->get('client_id');
        if ($noorder->save()) {
            $noorder->images = "";
            $noorder->image_paths = "";
            $tempImageIds       = array();
            $tempImageArray     = array();
            $tempImagePathArray = array();
            if ($request->file('noorder_photo')) {

                foreach ($request->file('noorder_photo') as $noorder_photo) {
                    $image = $noorder_photo;
                    $realname = pathinfo($noorder_photo->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $image->getClientOriginalExtension();
                    $new_name = $realname . "-" . time() . '.' . $extension;
                    $image->storeAs('public/uploads/' . $companyName . '/noorders/', $new_name);
                    $path = Storage::url('app/public/uploads/' . $companyName . '/noorders/' . $new_name);
                    $imgId = DB::table('images')->insertGetId([
                        'type' => 'noorder',
                        'type_id' => $noorder->id,
                        'company_id' => $company_id,
                        'client_id' => $noorder->client_id,
                        'image' => $new_name,
                        'image_path' => $path,
                    ]);

                    array_push($tempImageIds,(string)$imgId);
                    array_push($tempImageArray, $new_name);
                    array_push($tempImagePathArray, $path);
                }

            }
            if (!empty($noorder->employee_id)) {
                if (!empty($noorder->client_id)) {
                    $client = DB::table('clients')->where('id', $noorder->client_id)->first();
                    $noorder->company_name = empty($client) ? "" : $client->company_name;
                }
                $noorder->image_ids = json_encode($tempImageIds);
                $noorder->images = json_encode($tempImageArray);
                $noorder->image_paths = json_encode($tempImagePathArray);
                $dataPayload = array("data_type" => "noorder", "noorder" => $noorder, "action" => "add");
                $msgID = sendPushNotification_(getFBIDs($company_id, null, $noorder->employee_id), 7, null, $dataPayload);
            }
        }

        return redirect()->route('company.admin.noorders', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

        
    }


