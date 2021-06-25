<?php

namespace App\Http\Controllers\Company\Admin;

use Session;
use App\Client;
use App\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Auth;

class RetailerOutletController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->company_id = config('settings.company_id');
        $this->middleware('permission:outlet-view');
        $this->middleware('permission:outlet-create', ['only' => ['store']]);
        $this->middleware('permission:outlet-delete', ['only' => ['destroy']]);
        if(config('settings.monthly_attendance')==0){
          return redirect()->back();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $company_id = $this->company_id;
      $clients = Client::whereCompanyId($company_id)->whereStatus('Active')->whereNull('outlet_id')->orderby('company_name', 'asc')->pluck('company_name', 'id')->toArray();
      $outlets = Outlet::get(['unique_code', 'outlet_name']);
      return view('company.outlets_setup.index')->with([
        'clients' => $clients
      ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $customMessages = [
        'client_id.required' => 'The Party field is required.',
        'secret_code.required' => 'The secret code is required.',
        'secret_code.min' => 'The secret code must be exactly 16 characters.',
        'secret_code.max' => 'The secret code must be exactly 16 characters.',
      ];
      $this->validate($request, [
        'client_id' => 'required',
        'secret_code' => 'required|min:16|max:16'
      ], $customMessages);

      $client_id = $request->client_id;
      $secret_code = $request->secret_code;
      try{
        $outlet_instance = Outlet::whereUniqueCode($secret_code)->first();
        if($outlet_instance){
          $connected_client = Client::whereOutletId($outlet_instance->id)->pluck('company_id')->toArray();
          if(in_array($this->company_id, $connected_client)){
            // Session::flash('error', 'Cannot connect an outlet with multiple clients. Please disconnect the current client first.');
            // return redirect()->back();  
            return response()->json([
              "message" => "Cannot connect an outlet with multiple clients. Please disconnect the current client first.",
              "status" => 400,
              "append_to" => "secret_code"
            ]);
          }else{
            $client = Client::findOrFail($client_id);
            $client->update([
              'outlet_id' => $outlet_instance->id
            ]);
            DB::table('company_outlet')->insert([
              'company_id' => $this->company_id,
              'outlet_id' => $outlet_instance->id,
            ]);
            $outlet_instance->update([
              "status"=> "Active"
            ]);
            // Session::flash('success', 'Connection has been established.');
            // return redirect()->back();

            return response()->json([
              "message" => "Connection has been established.",
              "status" => 200
            ]);
          }
        }else{
          // Session::flash('error', 'Cannot find retailer with given secret key.');
          // return redirect()->back();

          return response()->json([
            "message" => "Cannot find retailer with given secret key.",
            "status" => 400,
            "append_to" => "secret_code"
          ]);
        }

      } catch(Exception $e){
        Session::flash('error', $e->getMessage());
        return redirect()->back();
      }     
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
      die;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Order $order
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
      die;
    }

    public function update($domain,$id,Request $request)
    {
      $customMessages = [
        'client_id.required' => 'The Party field is required.',
        'secret_code.required' => 'The secret code is required.',
        'secret_code.min' => 'The secret code must be exactly 16 characters.',
        'secret_code.max' => 'The secret code must be exactly 16 characters.',
      ];
      $this->validate($request, [
        'client_id' => 'required',
        'secret_code' => 'required|min:16|max:16'
      ], $customMessages);

      $client_id = $request->client_id;
      $secret_code = $request->secret_code;
      try{
        $outlet_instance = Outlet::whereUniqueCode($secret_code)->first();
        if($outlet_instance){
          $connected_client = Client::whereOutletId($outlet_instance->id)->first();
          if($connected_client && $connected_client->company_id==$this->company_id){
            // Session::flash('error', 'Cannot connect an outlet with multiple clients. Please disconnect the current client first.');
            // return redirect()->back();  
            return response()->json([
              "message" => "Cannot connect an outlet with multiple clients. Please disconnect the current client first.",
              "status" => 400,
              "append_to" => "secret_code"
            ]);
          }else{
            $client = Client::findOrFail($client_id);
            $client->update([
              'outlet_id' => $outlet_instance->id
            ]);
            // Session::flash('success', 'Connection has been established.');
            // return redirect()->back();

            return response()->json([
              "message" => "Connection has been established.",
              "status" => 200
            ]);
          }
        }else{
          // Session::flash('error', 'Cannot find retailer with given secret key.');
          // return redirect()->back();

          return response()->json([
            "message" => "Cannot find retailer with given secret key.",
            "status" => 400,
            "append_to" => "secret_code"
          ]);
        }

      } catch(Exception $e){
        Session::flash('error', $e->getMessage());
        return redirect()->back();
      }  
    }

    public function destroy($domain,$id)
    {
      $client = Client::findOrFail($id);
      $conntected_outlet_id = $client->outlet_id;
      try{
        $outlet_instance = Outlet::find($conntected_outlet_id);
        DB::beginTransaction();
        $client->outlet_id = NULL;

        $client->update();
        if($outlet_instance){
          if($outlet_instance->clients->count()==0){
            $outlet_instance->update([
              'status' => 'New'
            ]);
          }
        }
        DB::table('company_outlet')->where('company_id', $this->company_id)->whereOutletId($conntected_outlet_id)->delete();
        DB::commit();
        Session::flash('success', 'Connection removed successfully.');

        return back();
      }catch(Exception $e){
        Session::flash('error', $e->getMessage());

        return back();
      }
    }


    public function fetchData(Request $request)
    { 
      $company_id = $this->company_id;
      $columns = array( 'id', 'outlet_name', 'partyname', 'action');       

      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      if($order=='id') $order = "outlets.id";
      if($order=='partyname') $order = "clients.company_name";
      $dir = $request->input('order.0.dir');
      $search = strtoupper( $request->input('search.value') );

      $clients_query = Client::where('clients.company_id', $company_id)->where('clients.outlet_id', '!=', NULL)->leftJoin('outlets', 'clients.outlet_id', 'outlets.id');
      
      $totalData = (clone $clients_query)->get()->count();

      if(isset($search)){
        $clients_query = $clients_query->where(function($query) use ($search){
          $query->orWhere(\DB::raw("UPPER(outlets.outlet_name)"), 'LIKE', "%{$search}%");
          $query->orWhere(\DB::raw("UPPER(outlets.contact_person)"), 'LIKE', "%{$search}%");
          $query->orWhere('clients.company_name', 'LIKE', "%{$search}%");
        });
      }

      $totalFiltered = (clone $clients_query)->get()->count();
      
      $clients = $clients_query->orderBy($order,$dir)->offset($start)
                        ->limit($limit)->get(['clients.company_name', 'clients.outlet_id', 'clients.id', 'outlets.outlet_name', 'outlets.contact_person']);
      
      // $cients = Client::whereCompanyId($company_id)->whereHas('outlets', function($q) use ($search){
      //   if(isset($search)){
      //     $q->where(function($query) use ($search){
      //       $query->orWhere('outlets.outlet_name', 'LIKE', "%{$search}%");
      //       $query->orWhere('clients.company_name', 'LIKE', "%{$search}%");
      //       $query->orWhere('outlets.unique_code', 'LIKE', "%{$search}%");
      //     });
      //   }
      // })->get();

      // $totalFiltered = $clients->count();
      
      $data = array();
      if(!empty($clients))
      {
        $i = $start;
        foreach ($clients as $client){
          $edit =  domain_route('company.admin.outlets.connection.update',[$client->id]);
          $delete = domain_route('company.admin.outlets.connection.delete', [$client->id]);
          $clientShow = domain_route('company.admin.client.show',[$client->id]);
          
          $related_outlet = $client->outlets;
          $partyName = $client->company_name;
          $nestedData['id'] = ++$i;
          if($related_outlet->status=="Disabled") $spanLabel = " <span class='label label-default'>{$related_outlet->status}</span>";
          else $spanLabel = null;
          $nestedData['outlet_name'] = $related_outlet->outlet_name." (". $related_outlet->contact_person .")".$spanLabel;
          // $nestedData['secret_code'] = $related_outlet->unique_code;
          $nestedData['partyname'] = "<a href='{$clientShow}' dataparty='{$partyName}'>{$partyName}</a>";
          if(Auth::user()->can('outlet-delete')) $nestedData['action'] = "<a href='javascript:void(0)' class='btn btn-warning btn-sm hidden' id='connection-edit' data-code='{$related_outlet->unique_code}' data-selclient='{$client->id}' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a> <a class='btn btn-danger btn-sm delete' id='connection-delete-btn' data-selclient='{$client->id}' data-url='{$delete}' data-toggle='modal' data-target='#deleteModal' style='padding: 3px 6px;' title='Remove Connection'><i class='fa fa-trash-o'></i></a>";
          else $nestedData['action'] = null;
          $data[] = $nestedData;
        }
      }

      $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data,
      );

      return json_encode($json_data); 
    }

    // public function customPdfExport(Request $request){
    //   $getExportData = json_decode($request->exportedData)->data;
    //   $pageTitle = $request->pageTitle;
    //   set_time_limit ( 300 );
    //   ini_set("memory_limit", "256M");
    //   $pdf = PDF::loadView('company.orders.exportpdf', compact('getExportData', 'pageTitle'))->setPaper('a4', 'portrait');
    //   unset($getExportData);
    //   return $pdf->download($pageTitle.'.pdf');
    // }
}
