<?php

namespace App\Http\Controllers\Company\Admin;

use DB;
use Illuminate\Support\Facades\Log;
use Auth;
use Storage;
use Session;
use App\Bank;
use App\User;
use App\Order;
use App\Client;
use App\Employee;
use App\Collection;
use App\ModuleAttribute;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;

class CollectionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:collection-create', ['only' => ['create','store']]);
        $this->middleware('permission:collection-view');
        $this->middleware('permission:collection-update', ['only' => ['edit','update']]);
        $this->middleware('permission:collection-delete', ['only' => ['destroy']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
//<<<<<<< HEAD
      $nCal = config('settings.ncal');
//=======
//
//>>>>>>> custom-modules
      $prepQuery = Auth::user()->handleQuery('collection')->orderBy('payment_method', 'desc')->orderBy('id', 'desc')->get(['client_id', 'employee_id', 'payment_method']);
      $collectionsCount = $prepQuery->count();
      if($collectionsCount>0){
        $employee_ids = $prepQuery->unique('employee_id')
                        ->pluck('employee_id')
                        ->toArray();
        $employeesWithCollections = Employee::whereIn('id', $employee_ids)
                              ->orderBy('name','asc')
                              ->pluck('name','id')
                              ->toArray();

        $party_ids = $prepQuery->unique('client_id')
                    ->pluck('client_id')
                    ->toArray();
        $partiesWithCollections = Client::whereIn('id', $party_ids)
                              ->orderBy('company_name','asc')
                              ->pluck('company_name','id')
                              ->toArray();
        $paymentModes = $prepQuery->unique('payment_method')
                      ->pluck('payment_method')
                      ->toArray();
      }else{
        $employeesWithCollections = array();
        $partiesWithCollections = array();
        $paymentModes = array();
      }
      return view('company.collections.index', compact('collectionsCount', 'employeesWithCollections', 'partiesWithCollections', 'paymentModes', 'nCal'));
    }

    public function ajaxDatatable(Request $request){
      $columns = array(
        0 => 'id',
        1 => 'company_name',
        2 => 'payment_received',
        3 => 'payment_date',
        4 => 'payment_method',
        5 => 'note',
        6 => 'employee_name',
        7 => 'action',
      );

      $company_id = config('settings.company_id');
      $empVal = $request->empVal;
      $partyVal = $request->partyVal;
      $paymentModeVal = $request->paymentModeVal;
      $start_date = $request->startDate;
      $end_date = $request->endDate;
      Log::info($start_date);
      $search = $request->input('search')['value'];
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      if($order=='note'){
        $order = 'payment_status_note';
      }
      $dir = $request->input('order.0.dir');



      $prepQuery = Auth::user()->handleQuery('collection')->leftJoin('employees', 'employees.id', 'collections.employee_id')->leftJoin('clients', 'clients.id', 'collections.client_id')->whereBetween('collections.payment_date', [$start_date, $end_date])->select('collections.id', 'collections.client_id', 'collections.employee_id', 'collections.payment_received','collections.payment_method','collections.payment_date', 'employees.name as employee_name', 'clients.company_name as company_name','collections.payment_status_note');

      if(!empty($empVal)){
        $empFilterQuery =  $prepQuery;
        $prepQuery = $empFilterQuery->where('collections.employee_id', $empVal);
      }
      if(!empty($partyVal)){
        $partyFilterQuery = $prepQuery;
        $prepQuery = $partyFilterQuery->where('collections.client_id', $partyVal);
      }
      if(!empty($paymentModeVal)){
        $paymentModeQuery = $prepQuery;
        $prepQuery = $paymentModeQuery->where('collections.payment_method', $paymentModeVal);
      }
      if(!empty($search)){
        $searchQuery = $prepQuery;
        $prepQuery = $searchQuery->where(function($query) use ($search){
                      $query = $query->where('collections.id','LIKE',"%{$search}%")
                          ->orWhere('collections.payment_received' ,'LIKE', "%{$search}%")
                          ->orWhere('collections.payment_method' ,'LIKE', "%{$search}%")
                          ->orWhere('collections.payment_note' ,'LIKE', "%{$search}%")
                          ->orWhere('employees.name' ,'LIKE', "%{$search}%")
                          ->orWhere('clients.company_name' ,'LIKE', "%{$search}%");
                     });
      }

      $totalData =  $prepQuery->count();
      $totalFiltered = $totalData;
      $data = array();
      $total = $prepQuery->sum('collections.payment_received');
      if($limit==-1){
        $limit = $totalData;
      }
      $collections = $prepQuery->orderBy($order,$dir)->offset($start)
                            ->limit($limit)
                            ->get();

      if (!empty($collections)) {
          $i = $start;
          $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
          foreach ($collections as $collection) {
            $id = $collection->id;
            $company_name = ucfirst($collection->company_name);
            $received_payment = isset($collection->payment_received)?config('settings.currency_symbol').'   '.number_format((float)$collection->payment_received,2):null;
            $payment_date = isset($collection->payment_date)?getDeltaDate(date('Y-m-d',strtotime($collection->payment_date))):null;
            $status = $collection->status;
            $client_show = in_array($collection->client_id, $viewable_clients) && Auth::user()->can('party-view')?domain_route('company.admin.client.show',[$collection->client_id]):null;
            $employee_show = domain_route('company.admin.employee.show',[$collection->employee_id]);
            $show = domain_route('company.admin.collection.show', [$id]);
            $edit = domain_route('company.admin.collection.edit', [$id]);
            $delete = domain_route('company.admin.collection.destroy', [$id]);

            $nestedData['id'] = ++$i;
            $nestedData['company_name'] = "<a dataparty='{$company_name}' class='clientLinks' data-viewable='{$client_show}' href='{$client_show}'>{$company_name}</a>" ;
            $nestedData['payment_received'] = $received_payment;
            $nestedData['payment_date'] = $payment_date;
            $nestedData['payment_method'] = $collection->payment_method;
            $nestedData['note'] = $collection->payment_status_note;
            $nestedData['employee_name'] = "<a href='{$employee_show}' datasalesman='{$collection->employee_name}'> {$collection->employee_name}</a>";
            $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
            if(Auth::user()->can('collection-update'))
            $nestedData['action'] =$nestedData['action'] ."<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
            if(Auth::user()->can('collection-delete'))
            $nestedData['action'] =$nestedData['action'] ."<a class='btn btn-danger btn-sm delete' data-mid='{$id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
            $data[] = $nestedData;
          }
      }

      $json_data = array(
          "draw"            => intval($request->input('draw')),
          "recordsTotal"    => intval($totalData),
          "recordsFiltered" => intval($totalFiltered),
          "data"            => $data,
          "total"           => config('settings.currency_symbol').' '.number_format((float)$total,2),
      );

      return json_encode($json_data);
    }

    public function custompdfdexport(Request $request){
      $getExportData = json_decode($request->exportedData)->data;
      $pageTitle = $request->pageTitle;
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      set_time_limit(300);
      ini_set("memory_limit", "256M");
      $pdf = PDF::loadView('company.collections.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company_id = config('settings.company_id');
        
        $employees = Auth::user()->handleQuery('employee')->get();

        $orders = Auth::user()->handleQuery('order')->pluck('id', 'order_no')->toArray();
        
        $clients=Auth::user()->handleQuery('client')->pluck('company_name', 'id')->toArray();
        $banks = Bank::select('id', 'name')->where('company_id', $company_id)->orderBy('name', 'asc')->get();
        return view('company.collections.create', compact('employees', 'clients', 'banks', 'orders'));
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
            'payment_received.' => 'Amount field is required.',
            'payment_received.numeric' => 'Amount field should be in number.',
            'payment_date.required' => 'Date field is required.',
            'client_id.required' => 'Client field is required.',
            'employee_id.required' => 'Client field is required.',
            'bank.required' => 'Bank field is required',
        ];
        $this->validate($request, [
            'payment_received' => 'required|numeric',
            'payment_date' => 'required',
            'client_id' => 'required',
            'client_id' => 'required',
            'receipt[]' => 'sometimes|nullable|image|max:500'
        ], $customMessages);

        if($request->payment_method == "Cheque"){
            $this->validate($request,[
                'bank' => 'required',
                'payment_status' => 'required',
            ],$customMessages);
        }
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $collection = new \App\Collection;
        $collection->company_id = $company_id;
        $collection->client_id = $request->get('client_id');
        $collection->bank_id = $request->get('bank');
        Session::flash('DT_Collec_filters', $request->DT_Collec_FILTER);
        $collection->employee_id = Auth::user()->EmployeeId();
        $collection->employee_type = 'Employee';
        
        $collection->payment_received = $request->get('payment_received');
        $collection->payment_method = $request->get('payment_method');
        if(isset($request->englishDate)){
            $collection->payment_date = $request->get('englishDate');
        }else{
            $collection->payment_date = $request->get('payment_date');
        }
        $collection->payment_status_note = $request->get('payment_status_note');
        if(isset($request->chequeenglishDate)){
            if($request->get('chequeenglishDate')!='0000-00-00'){
                $collection->cheque_date = $request->get('chequeenglishDate');
            }
        }elseif($request->get('chequeenglishDate')!='0000-00-00'){
            $collection->cheque_date = $request->get('cheque_date');
        }
        $collection->cheque_no = $request->get('cheque_no');
        if($request->get('payment_method')=='Cash' || $request->get('payment_method')=='Bank Transfer'){
          $collection->payment_status = 'Cleared';
        }else{
          $collection->payment_status = $request->get('payment_status');
        }
        $collection->updated_at = NULL;

        if ($collection->save()) {
            $collection->images = "";
            $collection->image_paths = "";
            if ($request->file('receipt')) {
                $tempImageArray = array();
                $tempImagePathArray = array();
                $tempImageIdArray = array();

                foreach ($request->file('receipt') as $receipt) {
                    $receipt2 = $receipt;
                    $realname = pathinfo($receipt->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $receipt2->getClientOriginalExtension();
                    $new_name = $realname . "-" . time() . '.' . $extension;
                    $receipt2->storeAs('public/uploads/' . $companyName . '/collection/', $new_name);
                    $path = Storage::url('app/public/uploads/' . $companyName . '/collection/' . $new_name);
                    $saved = DB::table('images')->insertGetId([
                        'type' => 'collection',
                        'type_id' => $collection->id,
                        'company_id' => $company_id,
                        'client_id' => $collection->client_id,
                        'image' => $new_name,
                        'image_path' => $path,
                    ]);
                    array_push($tempImageArray, $new_name);
                    array_push($tempImagePathArray, $path);
                    array_push($tempImageIdArray, $saved);
                }
                $collection->images = json_encode($tempImageArray);
                $collection->image_paths = json_encode($tempImagePathArray);
                $collection->image_ids = json_encode($tempImageIdArray);

            }

            $sendingCollection = $collection;

            $sendingCollection->company_name = getObjectValue($collection->client,"company_name","");
            $sendingCollection->bank_name = getObjectValue($collection->bank,"name","");
            $dataPayload = array("data_type" => "collection", "collection" => $sendingCollection, "action" => "add");
            $msgID = sendPushNotification_(getFBIDs($company_id, null, $collection->employee_id), 3, null, $dataPayload);
        }
        return redirect()->route('company.admin.collection', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $company_id = config('settings.company_id');
        
        $collection =Auth::user()->handleQuery('collection',$request->id)->first();
        $images = DB::table('images')->where('company_id',$company_id)->where('type', '=', 'collection')->where('type_id', $request->id)->get();

        $action = null;

        if(Auth::user()->can('collection-update')){
          $edit_link = domain_route('company.admin.collection.edit', [$request->id]);
          $action = "<a class='btn btn-warning btn-sm edit' href='{$edit_link}'  style='padding: 7px 6px;'><i class='fa fa-edit'></i>Edit</a>";
        }

        if(Auth::user()->can('collection-delete')){
          $delete = domain_route('company.admin.collection.destroy', [$request->id]);
          $action = $action."<a class='btn btn-danger btn-sm delete' data-mid='{$request->id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 7px 6px;'><i class='fa fa-trash-o'></i>Delete</a>";
        }

        if($collection){
            return view('company.collections.show', compact('collection', 'images','action'));
        }else{
            return redirect()->back()->withErrors(['msg', 'No record Found']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $company_id = config('settings.company_id');
        $collection =Collection::where('id',$request->id)->first();
        
        $employees = Auth::user()->handleQuery('employee')->get();
        $clients=Auth::user()->handleQuery('client')->pluck('company_name', 'id')->toArray();
        $images = DB::table('images')->where('company_id',$company_id)->where('type', '=', 'collection')->where('type_id', $request->id)->get();
        $banks = Bank::select('id', 'name')->where('company_id', $company_id)->orderBy('name', 'asc')->get();
        $image_count = $images->count();
        if ($collection)
            return view('company.collections.edit', compact('collection', 'employees', 'clients', 'image_count', 'images', 'banks'));
        else
            return redirect()->route('company.admin.collection', ['domain' => domain()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {   
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $collection = Collection::find($request->id);
        $customMessages = [
            'payment_received.' => 'Amount fielsd is required.',
            'payment_received.numeric' => 'Amount field should be in number.',
            'payment_date.required' => 'Date field is required.',
            'bank.required' => 'Bank field is required',
        ];


        $this->validate($request, [
            'payment_received' => 'required|numeric',
            'payment_date' => 'required',
            'receipt[]' => 'sometimes|nullable|image|max:500'
        ], $customMessages);

        if($request->payment_method == "Cheque"){
            $this->validate($request,[
                'bank' => 'required',
                
            ],$customMessages);
        }

        $collection->client_id = $request->get('client_id');
        $collection->employee_type = 'Employee';
        $collection->payment_received = $request->get('payment_received');
        $collection->payment_method = $request->get('payment_method');
        $collection->payment_date = $request->get('payment_date');
        $collection->payment_status_note = $request->get('payment_status_note');
        $collection->bank_id = $request->get('bank');
        if(isset($request->chequeenglishDate)){
            $collection->cheque_date = $request->get('chequeenglishDate');
        }else{
            $collection->cheque_date = $request->get('cheque_date');
        }
        $collection->cheque_no = $request->get('cheque_no');
        if($request->get('payment_method')=='Cash' || $request->get('payment_method')=='Bank Transfer'){
          $collection->payment_status = 'Cleared';
        }else{
          $collection->payment_status = $request->get('payment_status');
        }
        if ($collection->save()) {
            $collection->images = "";
            $collection->image_paths = "";
            $imageArray = getImageArray("collection", $collection->id,$company_id);
            // $tempImageArray = getArrayValue($imageArray, "images", array());
            // $tempImagePathArray = getArrayValue($imageArray, "image_paths", array());
            if($request->get('img_ids') != NULL){
                DB::table('images')->where('company_id',$company_id)->where('type','collection')->where('type_id',$collection->id)->whereNotIn('id',$request->get('img_ids'))->delete();           
            }else{
                DB::table('images')->where('company_id',$company_id)->where('type','collection')->where('type_id',$collection->id)->delete(); 
            }
            if ($request->file('receipt')) {
                foreach ($request->file('receipt') as $receipt) {
                    $receipt2 = $receipt;
                    $realname = pathinfo($receipt->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $receipt2->getClientOriginalExtension();
                    $new_name = $realname . "-" . time() . '.' . $extension;
                    $receipt2->storeAs('public/uploads/' . $companyName . '/collection/', $new_name);
                    $path = Storage::url('app/public/uploads/' . $companyName . '/collection/' . $new_name);
                    DB::table('images')->insert([
                        'type' => 'collection',
                        'type_id' => $collection->id,
                        'company_id' => $company_id,
                        'client_id' => $collection->client_id,
                        'image' => $new_name,
                        'image_path' => $path,
                    ]);
                }

            }
            $tempImageIds = [];
            $tempImageArray = [];
            $tempImagePathArray = [];

            $images = DB::table('images')->where('type','collection')->where('type_id',$collection->id)->get();
            foreach($images as $image){
              array_push($tempImageIds,$image->id);
              array_push($tempImageArray,$image->image);
              array_push($tempImagePathArray,$image->image_path);
            }

            $collection->image_ids = json_encode($tempImageIds);
            $collection->images = json_encode($tempImageArray);
            $collection->image_paths = json_encode($tempImagePathArray);

            // Log::info('info', array("message"=>print_r($collection,true)));

            $collection->company_name = getObjectValue($collection->client,"company_name","");
            $collection->bank_name = getObjectValue($collection->bank,"name","");
            $dataPayload = array("data_type" => "collection", "collection" => $collection, "action" => "update");
            $msgID = sendPushNotification_(getFBIDs($collection->company_id, null, $collection->employee_id), 3, null, $dataPayload);
            // Log::info('info', array("push collection update"=>print_r($msgID,true)));
        }
        session()->flash('success', 'Collection Updated Successfully');
        Session::flash('DT_Collec_filters', $request->DT_Collec_FILTER);
        $newprevious=explode('/',$request->previous_url);
        if($newprevious[4]=='employee' || $newprevious[4]=='client'){
          $previous = $request->previous_url;
          return redirect($previous);
        }else{
            return redirect()->route('company.admin.collection', ['domain' => domain()])->with('success', 'Information has been  Updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $collection = Auth::user()->handleQuery('collection',$request->id)->first();
        if (!empty($collection)) {
            $deleted = $collection->delete();
            if ($deleted) {
                $dataPayload = array("data_type" => "collection", "collection" => $collection, "action" => "delete");
                $msgID = sendPushNotification_(getFBIDs($collection->company_id, null, $collection->employee_id), 3, null, $dataPayload);
            }
        }
        \Session::flash('success', 'collection has been deleted.');
         Session::flash('DT_Collec_filters', $request->DT_Collec_FILTER);
        
        if($request->has('prev_url')) return redirect()->to($request->prev_url);
        return back();
    }


    public function download(Request $request)
    {
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $images = DB::table('images')->where('type_id', $request->id)->where('company_id', $company_id)->get();

        foreach ($images as $image) {
            $filename = $image->image;
            $file_path = storage_path() . '/app/public/uploads/' . $companyName . '/collection/' . $image->image;
            $headers = array(
                'Content-Type: csv',
                'Content-Disposition: attachment; filename=' . $filename,
            );
            if (file_exists($file_path)) {
                // Send Download
                return \Response::download($file_path, $filename, $headers);
            } else {
                // Error
                exit('Requested file does not exist on our server!');
            }
        }

    }
    
}
