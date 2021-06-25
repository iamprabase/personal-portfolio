<?php

namespace App\Http\Controllers\Company\Admin;

use Auth;
use View;
use App\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class BankController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_id = config('settings.company_id');
        $banks = Bank::all()->sortByDesc("created_at");
        return view('company.banks.index', compact('banks'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('company.banks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $company_id = config('settings.company_id');
        
        $box = $request->all();        
        $requests=  array();
        parse_str($box['data'], $requests);
        if($requests['name']==""){
            return $data['result']="Can't Create Bank with empty name";
        }

        $bankExists = Bank::where('company_id',$company_id)->where('name',$requests['name'])->first();
        if($bankExists==null){
            $bank = new \App\Bank;
            $bank['name'] = $requests['name'];
            // $bank->status = $request->get('status');
            $bank['company_id'] = $company_id;

            $bank->save();

            $banks = Bank::where('company_id',$company_id)->orderBy('name','ASC')->get();
            $data['banks'] = View::make('company.settings.ajaxbanklists',compact('banks'))->render();
            $data['result'] = true;

            $dataPayload = array("data_type" => "bank", "bank" => $bank, "action" => "add");
            $msgSent = sendPushNotification_(getFBIDs($company_id), 39, null, $dataPayload);
        }else{
            $data['result']=false;
        }
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\bank $bank
     * @return \Illuminate\Http\Response
     */
    public function show(bank $bank)
    {
        $bank = Bank::find($bank->id);
        return view('company.banks.show', compact('bank'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\bank $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(bank $bank)
    {
        $bank = Bank::findOrFail($bank);
        return view('company.banks.edit', compact('bank'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\bank $bank
     * @return \Illuminate\Http\Response
     */
    public function update($domain,$id,Request $request)
    {
        $box = $request->all();        
        $requests=  array();
        parse_str($box['data'], $requests);
        $company_id = config('settings.company_id');
        $bankExists = Bank::where('company_id',$company_id)->where('name',$requests['name'])->first();
        if($bankExists==null){
            $bank = Bank::findOrFail($id);
            $bank->name = $requests['name'];
            $bank->save();
            $banks = Bank::where('company_id',$company_id)->orderBy('name','ASC')->get();
            $data['banks'] = View::make('company.settings.ajaxbanklists',compact('banks'))->render();
            $data['result']=true;

            
            $dataPayload = array("data_type" => "bank", "bank" => $bank, "action" => "update");
            $msgSent = sendPushNotification_(getFBIDs($company_id), 39, null, $dataPayload);
        }else{
            $data['result']=false;
        }
        return $data;

        // return redirect()->route('company.admin.setting', ['domain' => domain()])->with('success', 'Information has been  Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\bank $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy($domain,$id,Request $request)
    {
        $bank = Bank::findOrFail($id);
        if($bank->cheques->count()==0){
            $bank->delete();
            $company_id = config('settings.company_id');
            $banks = Bank::where('company_id',$company_id)->orderBy('name','ASC')->get();
            $data['banks'] = View::make('company.settings.ajaxbanklists',compact('banks'))->render();
            $data['result']=true;

            $dataPayload = array("data_type" => "bank", "bank" => $id, "action" => "delete");
            $msgSent = sendPushNotification_(getFBIDs($company_id), 39, null, $dataPayload);
        }else{
            $data['result'] = false;
        }
        return $data;
    }
}
