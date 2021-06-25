<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ReturnReason;
use App\ProductReturn;
use Auth;
use View;
use DB;

class ReturnReasonController extends Controller
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
        $company_id = config('settings.company_id');
        $returnReasons = ReturnReason::where('company_id', $company_id)->orderBy('name', 'ASC')->get();

        return view('company.returns.index', compact('returnReasons'));
    }

    public function create()
    {
        $data['titile'] = 'Add Return Reason';
        $data['row'] = (object)['name' => ''];
        $data['url'] = url(domain_route('company.admin.returnreason.store'));
        $data['method'] = 'POST';
        return view('company.admin.returnreason.create', $data);
    }//end create

    public function store(Request $request)
    {
        $company_id = config('settings.company_id');
        $this->validate($request, [
            'name' => 'required'
        ]);
        $input['name'] = $request->name;
        $input['company_id'] = $company_id;
        $existReturnReason = ReturnReason::where('company_id',$company_id)->where('name','LIKE',$request->name)->first();
        if($existReturnReason!=null){
            $data['result'] = false;
        }else{
            $returnReason = ReturnReason::create($input);

            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "returnreason", "returnreason" => $returnReason, "action" => "add");
            $sent = sendPushNotification_($fbIDs, 21,null, $dataPayload);

            $data = [];
            $returnReasons = ReturnReason::where('company_id', $company_id )->orderBy('id', 'ASC')->get();
            $data['returnReason'] = $returnReasons;
            if(!empty($returnReasons)){
                $data['count'] = $returnReasons->count();   
            }else{
                $data['count'] = 0;
            }
            $data['reasonData'] = ProductReturn::join('return_details', 'return_details.return_id', 'returns.id')
                                ->where('returns.company_id',  $company_id)
                                ->distinct('return_details.reason')
                                ->pluck('return_details.reason')->toArray();
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
    public function edit($id)
    {
        $row = ReturnReason::find($id);
        $company_id = config('settings.company_id');
        if (!$row)
            if ($row->company_id != $company_id || !$row) {
                return response()->json(['error' => "This returnreason doesn't exist", 'url' => domain_route('company.admin.activities-type.index')], 422);

            }
        $data['row'] = $row;
        $data['titile'] = 'Edit Return Reason';
        $data['url'] = url(domain_route('returnreason-type.update', [$row->id]));
        $data['method'] = 'PUT';
        return view('company.admin.returnreason-type.create', $data);
    }//end edit

    public function updateReturnReason(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'name' => 'required',
        ]);
        $company_id = config('settings.company_id');
        $row = ReturnReason::where('company_id',$company_id)->where('id',$request->id)->first();
        $rowExists = ReturnReason::where('company_id',$company_id)->where('name',$request->name)->where('id','!=',$request->id)->first();
        if($rowExists!=null){
            $data['result'] = false;
        }else{
            $input = $request->all();
            $input['company_id'] = config('settings.company_id');
            $row->update($input);

            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "returnreason", "returnreason" => $row, "action" => "update");
            $sent = sendPushNotification_($fbIDs, 21,null, $dataPayload);


            $data = [];
            $returnReasons = ReturnReason::where('company_id', $company_id)->orderBy('id', 'ASC')->get();
            $data['returnReason'] = $returnReasons;
            if(!empty($returnReasons)){
                $data['count'] = $returnReasons->count();   
            }else{
                $data['count'] = 0;
            }
            $data['reasonData'] =  ProductReturn::join('return_details', 'return_details.return_id', 'returns.id')
                                ->where('returns.company_id',  $company_id)
                                ->distinct('return_details.reason')
                                ->pluck('return_details.reason')->toArray();
            $data['result'] = true;        
        }
        return $data;

    }//end update


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteReturnReason(Request $request)
    {
        $company_id = config('settings.company_id');
        $row = ReturnReason::where('id',$request->id)->where('company_id', $company_id)->first();
        if (!$row || $row->company_id != $company_id ) {
            $data =false;
            return $data;
        }else {
            $row->delete();

             $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "returnreason", "returnreason" => $row, "action" => "delete");
            $sent = sendPushNotification_($fbIDs, 21,null, $dataPayload);

            $data = [];
            $returnReasons = ReturnReason::where('company_id', $company_id)->orderBy('id', 'ASC')->get();
            $returnreasons = $returnReasons;
            if($returnreasons){
                $data['count'] = $returnreasons->count();
            }else{
                $data['count'] = 0;
            }
            $data['reasonData'] =  ProductReturn::join('return_details', 'return_details.return_id', 'returns.id')
                                ->where('returns.company_id',  $company_id)
                                ->distinct('return_details.reason')
                                ->pluck('return_details.reason')->toArray();
            $existingReturnReasons = $data['reasonData'];
            $data['result']=true;
            return view('company.settings.returnreasonpartial', compact('returnreasons', 'existingReturnReasons'));
        }
    }//end destroy
}
