<?php

namespace App\Http\Controllers\Company\Admin;

use DB;
use Auth;
use Session;
use Storage;
use App\User;
use App\Client;
use App\Company;
use App\NoOrder;
use App\Employee;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ZeroOrderResource;


class ZeroOrderController extends Controller
{
  use Upload;

  public function __construct()
  {
      $this->middleware('auth');
      $this->middleware('permission:zeroorder-create', ['only' => ['create','store']]);
      $this->middleware('permission:zeroorder-view');
      $this->middleware('permission:zeroorder-update', ['only' => ['edit','update']]);
      $this->middleware('permission:zeroorder-delete', ['only' => ['destroy']]);
      // $this->middleware('permission:order-status', ['only' => ['changeDeliveryStatus']]);
  }

  public function index(Request $request){ 
    if(config('settings.zero_orders')==0){
      return redirect()->back();
    }
    $aggrunitprod = array();
    $company_id = config('settings.company_id');
    $nCal = config('settings.ncal');
    $partiesWithNoOrders = Auth::user()->handleQuery('client')->with('noorders')->orderBy('company_name', 'asc')->pluck('company_name', 'id')->toArray();
    $employeesWithNoOrders = Auth::user()->handleQuery('employee')->with('noorders')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    return view('company.zeroorder.index', compact('partiesWithNoOrders', 'employeesWithNoOrders', 'nCal'));
  }

  public function zeroorderlistDataTable(Request $request){
    $getColumns = $request->datacolumns;
    $columns = array();
    $sizeof = sizeof($getColumns);
    for($count = 0; $count<$sizeof; $count++){
      $columns[$count] = $getColumns[$count]["data"];
    }

    $units = $request->units;

    $company_id = config('settings.company_id');
    $empVal = $request->empVal;
    $partyVal = $request->partyVal;
    $start_date = $request->startDate;
    $end_date = $request->endDate;
    $search = $request->input('search')['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $order_col_no = $request->input('order.0.column');
    $order = $columns[$order_col_no];
    if($order == "company_name"){
      $order = "clients.".$order;
    }elseif($order == "contact_person"){
      $order = "clients."."name";
    }elseif($order == "party_type"){
      $order = "partytypes."."name";
    }elseif($order == "contact_number"){
      $order = "clients."."mobile";
    }elseif($order == "address"){
      $order = "clients."."address_1";
    }elseif($order == "employee_name"){
      $order = "employees."."name";
    }elseif($order == "date" ){
      $order = "no_orders.date";//.$order;
    }elseif($order == "remark"){
      $order = "no_orders.remark";
    }
    $dir = $request->input('order.0.dir');

    $prepQuery = Auth::user()->handleQuery('no_order')->select('clients.id as client_id', 'clients.name as contact_person_name', 'clients.company_name', 'clients.mobile as contact_number', 'clients.superior as party_superior', 'clients.client_type as party_type', 'partytypes.name as party_type_name','clients.address_1 as address', 'employees.name as employee_name', 'no_orders.employee_id as employee_id','no_orders.id as noorder_id', 'no_orders.company_id', 'no_orders.date', 'no_orders.remark')
              ->leftJoin('clients', 'no_orders.client_id', '=', 'clients.id')
              ->leftJoin('employees', 'no_orders.employee_id', '=', 'employees.id')
              ->leftJoin('partytypes', 'clients.client_type', '=', 'partytypes.id')
              ->leftJoin('client_settings', 'no_orders.company_id','client_settings.company_id')
              ->whereBetween('no_orders.date', [$start_date, $end_date])->where('no_orders.company_id',$company_id);

    if(!empty($empVal)){
      $empFilterQuery =  (clone $prepQuery);
      $prepQuery = $empFilterQuery->where('no_orders.employee_id', $empVal);
    }
    if(!empty($partyVal)){
      $partyFilterQuery =  (clone $prepQuery);
      $prepQuery = $partyFilterQuery->where('no_orders.client_id', $partyVal);
    }
    if(!empty($search)){
      $searchQuery = (clone $prepQuery);
      $prepQuery = $searchQuery->where(function($query) use ($search){
                    $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
                    $query->orWhere('clients.name' ,'LIKE', "%{$search}%");
                    $query->orWhere('clients.company_name' ,'LIKE', "%{$search}%");
                    $query->orWhere('clients.mobile' ,'LIKE', "%{$search}%");
                    $query->orWhere('partytypes.name' ,'LIKE', "%{$search}%");
                    $query->orWhere('no_orders.remark' ,'LIKE', "%{$search}%");
                    });
    }

    $totalData = $prepQuery->count();
    if($limit==-1){
      $limit = $totalData;
    }
    $totalFiltered = $totalData;

    $data = array();

    $no_orders =  $prepQuery->orderBy($order,$dir)->orderBy('no_orders.id','desc')->offset($start)
                          ->limit($limit)->get();
    
    if (!empty($no_orders)) {
        $i = $start;
        $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
        foreach ($no_orders as $noorder) {
          $id = $noorder->noorder_id;

          $show = domain_route('company.admin.zeroorder.show', [$id]);
          $edit = domain_route('company.admin.zeroorder.edit', [$id]);
          $delete = domain_route('company.admin.zeroorder.destroy', [$id]);

          $client_name = $noorder->contact_person_name;
          $client_company_name = $noorder->company_name;
          $client_show = in_array($noorder->client_id, $viewable_clients)?domain_route('company.admin.client.show',[$noorder->client_id]):null;
          // $client_show = domain_route('company.admin.client.show',[$noorder->client_id]);
          $party_type =  $noorder->party_type_name;
          $contact =  $noorder->contact_number;
          $address =  $noorder->address;
          $employee_name = $noorder->employee_name;
          $employee_show = domain_route('company.admin.employee.show',[$noorder->employee_id]);
          $date = isset($noorder->date)?getDeltaDate(date('Y-m-d',strtotime($noorder->date))):null;
          $remark = $noorder->remark;

          $nestedData['id'] =  ++$i;
          $nestedData['company_name'] = "<a class='clientLinks' href='{$client_show}' data-viewable='{$client_show}' datasalesman='{$client_company_name}'> {$client_company_name}</a>";
          // $nestedData['contact_person'] = $client_name;
          // $nestedData['party_type'] = $party_type;
          // $nestedData['contact_number'] = $contact;
          // $nestedData['address'] = $address;
          $action = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";

          if(Auth::user()->can('zeroorder-update')){
            $action = $action."<a href='{$edit}' class='btn btn-warning btn-sm'
            style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
          }
          if(Auth::user()->can('zeroorder-delete')){
            $action = $action."<a class='btn btn-danger btn-sm delete' data-mid='{$id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
          }
          $nestedData['employee_name'] = "<a href='{$employee_show}' datasalesman='{$employee_name}'> {$employee_name}</a>";
          $nestedData['date'] =$date;
          $nestedData['remark'] = $remark;
          $nestedData['action'] = $action;

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
  
  public function create(){
    $company_id= config('settings.company_id');
    $clients= Auth::user()->handleQuery('client')->orderBy('company_name', 'asc')->get();
    return view('company.zeroorder.create',compact('clients'));
  }

  public function store(Request $request){
      
    $company_id = config('settings.company_id');
    $companyName = Auth::user()->companyName($company_id)->domain;

    $this->validate($request, [
        'remark'       => 'required',
        'client_id' => 'required',
        'zero_order_date' => 'required|date_format:Y-m-d',
        'noorder_photo[]' => 'sometimes|nullable|image|max:500'
    ]);


    $no_order = new \App\NoOrder;
    $no_order->company_id = $company_id; 
    $no_order->employee_id = Auth::user()->EmployeeId();
    $no_order->remark = $request->get('remark');
    $no_order->date = $request->get('zero_order_date');
    $no_order->client_id = $request->get('client_id');
    $no_order->unix_timestamp = round(microtime(true) * 1000);
    $no_order->datetime = date("Y-m-d H:i:s",($no_order->unix_timestamp/1000));
    $no_order->date = date("Y-m-d",($no_order->unix_timestamp/1000));

    $save = $no_order->save();
    $noorder = NoOrder::where('id', $no_order->id)->select('id', 'company_id', 'employee_id', 'client_id', 'remark', 'unix_timestamp', 'datetime', 'date')->first();
    if ($save) {
      $noorder->images = "";
      $noorder->image_paths = "";
      $tempImageIds       = array();
      $tempImageArray     = array();
      $tempImagePathArray = array();
      if ($request->file('noorder_photo')) {
        $upload_folder = 'uploads/' . $companyName . '/noorders'; 
        foreach ($request->file('noorder_photo') as $noorder_photo) {
          $image = $noorder_photo;
          $realname = pathinfo($noorder_photo->getClientOriginalName(), PATHINFO_FILENAME);
          $extension = $image->getClientOriginalExtension();
          $new_name = $realname . "-" . time();
          $response_upload = $this->upload($image, $upload_folder, 'public', $new_name);
          $path = '/storage/app/public/'.$response_upload['path'];
          if($path) {
            $imgId = DB::table('images')->insertGetId([
              'type' => 'noorders',
              'type_id' => $noorder->id,
              'company_id' => $company_id,
              'client_id' => $noorder->client_id,
              'image' => $new_name.$extension,
              'image_path' => $path,
            ]);

            array_push($tempImageIds,(string)$imgId);
            array_push($tempImageArray, $new_name);
            array_push($tempImagePathArray, $path);
          }
          // $realname = pathinfo($noorder_photo->getClientOriginalName(), PATHINFO_FILENAME);
          // $extension = $image->getClientOriginalExtension();
          // $new_name = $realname . "-" . time() . '.' . $extension;
          // $image->storeAs('public/uploads/' . $companyName . '/noorders/', $new_name);
          // $path = Storage::url('app/public/uploads/' . $companyName . '/noorders/' . $new_name);
          // $imgId = DB::table('images')->insertGetId([
          //     'type' => 'noorders',
          //     'type_id' => $noorder->id,
          //     'company_id' => $company_id,
          //     'client_id' => $noorder->client_id,
          //     'image' => $new_name,
          //     'image_path' => $path,
          // ]);
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
        $employee = Employee::findOrFail($noorder->employee_id);
        $noorder->employee_name = $employee?$employee->name:"";
        
        $superiors = Employee::employeeParents($noorder->employee_id, array());
        $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('id', $superiors)->pluck('firebase_token');

        $dataPayload = array("data_type" => "noorder", "noorder" => $noorder, "action" => "add");
        $msgID = sendPushNotification_($fbIDs, 36, null, $dataPayload);
      }
    }

    // return back()->with('success', 'Information has been  Added');
    $newprevious=explode('/',$request->previous_url);
    if($newprevious[4]=='employee' || $newprevious[4]=='client'){
      $previous = $request->previous_url;
      return redirect($previous)->with('success', 'Information has been  Added');
    }else{
      return redirect()->route('company.admin.zeroorders', ['domain' => domain()])->with('success', 'Information has been  Added');
    }
  }

  public function show(Request $request){
    $company_id = config('settings.company_id');
    $id = $request->id;

    $zeroorder = NoOrder::find($id);

    //$zeroorder = Auth::user()->handleQuery('no_order')->where('id', $id)->firstOrFail();

    $images = DB::table('images')->where('type', '=', 'noorders')->where('type_id', $id)->get();

    $action = null;
    if(Auth::user()->can('zeroorder-update'))
      $action  = '<a class="btn btn-warning btn-sm edit" data-mid="'.$zeroorder->id.'" href="'.domain_route('company.admin.zeroorder.edit', [$zeroorder->id]) .'" style="padding: 7px 6px;"><i class="fa fa-edit"></i>Edit</a>';
    if(Auth::user()->can('zeroorder-delete'))
      $action  = $action.'<a class="btn btn-danger btn-sm delete" data-mid="'.$zeroorder->id.'" data-url="'.domain_route('company.admin.zeroorder.destroy', [$zeroorder->id]) .'" data-toggle="modal" data-target="#delete" style="padding: 7px 6px;"><i class="fa fa-trash-o"></i>Delete</a>';
    if(!$zeroorder)
        return redirect()->back()->withErrors(['msg', 'No record Found']);
    return view('company.zeroorder.show', compact('zeroorder', 'images', 'action')); 
  }

   public function edit(Request $request){
    $company_id= config('settings.company_id');
    $clients= Auth::user()->handleQuery('client')->orderBy('company_name', 'asc')->get();

    $zeroorder = NoOrder::find($request->id);

    $images = DB::table('images')->where('type', '=', 'noorders')->where('type_id', $request->id)->get();
    $image_count = $images->count();
    // dd("Here");
    if ($zeroorder)
      return view('company.zeroorder.edit', compact('zeroorder', 'clients', 'images', 'image_count'));
    else
      return redirect()->back()->withErrors(['msg', 'No record Found']);

  }

  public function update(Request $request){
    $company_id = config('settings.company_id');
    $companyName = Auth::user()->companyName($company_id)->domain;
    $no_order = NoOrder::find($request->id);
    Session::flash('DT_ZeroOrd_filters', $request->DT_ZeroOrd_FILTER);
    if(!$no_order){
      return redirect()->back()->withErrors(['msg', 'No record Found']);
    }

    $this->validate($request, [
        'remark' => 'required',
        'zero_order_date' => 'required|date_format:Y-m-d',
        'noorder_photo[]' => 'sometimes|nullable|image|max:500'
    ]);
    
    $urlnew = $request->previous_url;
    $no_order->company_id = $company_id; 
    $no_order->employee_id = $no_order->employee_id;
    $no_order->remark = $request->get('remark');
    $no_order->date = $request->get('zero_order_date');
    $no_order->client_id = $request->get('client_id');

    $save = $no_order->update();
    $noorder = NoOrder::where('id', $no_order->id)->select('id', 'company_id', 'employee_id', 'client_id', 'remark', 'unix_timestamp', 'datetime', 'date')->first();
    $tempImageIds       = array();
    $tempImageArray     = array();
    $tempImagePathArray = array();
    if ($save) {
        if(!empty($request->get('noorder_photo_id'))){
          DB::table('images')->where('company_id',$company_id)->where('type','noorders')->where('type_id',$noorder->id)->whereNotIn('id',$request->get('noorder_photo_id'))->delete();    
          $other_images = DB::table('images')->where('company_id',$company_id)->where('type','noorders')->where('type_id',$noorder->id)->get(['image', 'image_path', 'id']);
          if($other_images->first()){
            foreach($other_images as $other_image){
              array_push($tempImageIds,(string)$other_image->id);
              array_push($tempImageArray, $other_image->image);
              array_push($tempImagePathArray, $other_image->image_path);
            }        
          }
        }else{
            DB::table('images')->where('company_id',$company_id)->where('type','noorders')->where('type_id',$noorder->id)->delete(); 
        }
        if ($request->file('noorder_photo')) {
          $upload_folder = 'uploads/' . $companyName . '/noorders';
            foreach ($request->file('noorder_photo') as $noorder_photo) {
                $image = $noorder_photo;
                $realname = pathinfo($noorder_photo->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $image->getClientOriginalExtension();
                $new_name = $realname . "-" . time();
                $response_upload = $this->upload($image, $upload_folder, 'public', $new_name);
                $path = '/storage/app/public/'.$response_upload['path'];
                if($path) {
                  $imgId = DB::table('images')->insertGetId([
                    'type' => 'noorders',
                    'type_id' => $noorder->id,
                    'company_id' => $company_id,
                    'client_id' => $noorder->client_id,
                    'image' => $new_name.$extension,
                    'image_path' => $path,
                  ]);

                  array_push($tempImageIds,(string)$imgId);
                  array_push($tempImageArray, $new_name);
                  array_push($tempImagePathArray, $path);
                }

                // $image = $noorder_photo;
                // $realname = pathinfo($noorder_photo->getClientOriginalName(), PATHINFO_FILENAME);
                // $extension = $image->getClientOriginalExtension();
                // $new_name = $realname . "-" . time() . '.' . $extension;
                // $image->storeAs('public/uploads/' . $companyName . '/noorder/', $new_name);
                // $path = Storage::url('app/public/uploads/' . $companyName . '/noorder/' . $new_name);
                // $imgId = DB::table('images')->insertGetId([
                //     'type' => 'noorders',
                //     'type_id' => $noorder->id,
                //     'company_id' => $company_id,
                //     'client_id' => $noorder->client_id,
                //     'image' => $new_name,
                //     'image_path' => $path,
                // ]);
                // array_push($tempImageIds,(string)$imgId);
                // array_push($tempImageArray, $new_name);
                // array_push($tempImagePathArray, $path);
            }
        }

        $client = DB::table('clients')->where('id', $noorder->client_id)->first();
        $noorder->company_name = empty($client) ? "" : $client->company_name;

        $noorder->image_ids = json_encode($tempImageIds);
        $noorder->images = json_encode($tempImageArray);
        $noorder->image_paths = json_encode($tempImagePathArray);

        $employee = Employee::findOrFail($noorder->employee_id);
        $noorder->employee_name = $employee?$employee->name:"";
        $notificationData = array(
            "company_id" => $employee->company_id,
            "employee_id" => $employee->id,
            "data_type" => "noorder",
            "data" => "",
            "action_id" => $noorder->id,
            "title" => "noorder " . $noorder->status,
            "description" => "Your noorder has been Updated",
            "created_at" => date('Y-m-d H:i:s'),
            "status" => 1,
            "to" => 1,
            "unix_timestamp" => time()
        );

        $superiors = Employee::employeeParents($noorder->employee_id, array());
        $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('id', $superiors)->pluck('firebase_token');

        $dataPayload = array("action" => "update", "data_type" => "noorder", "noorder" => $noorder);
        $sent = sendPushNotification_($fbIDs, 36, $notificationData, $dataPayload);
    }
    session()->flash('success', 'ZeroOrder Updated Successfully.');

    $newprevious=explode('/',$request->previous_url);
    if($newprevious[4]=='employee' || $newprevious[4]=='client' ||$newprevious[4]=='zeroorder'  ){
        $previous = $request->previous_url;
            return redirect($previous);
    }else{
        return redirect()->route('company.admin.zeroorders', ['domain' => domain()])->with('success', 'Information has been  Updated');
    }
  }

  public function custompdfreport(Request $request){
    ini_set('max_execution_time', 300); 
    $getExportData = json_decode($request->exportedData)->data;
    $pageTitle = $request->pageTitle;
    $columns = json_decode($request->columns);
    $properties = json_decode($request->properties);
    set_time_limit ( 300 );
    ini_set("memory_limit", "256M");
    $pdf = PDF::loadView('company.zeroorder.export', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'landscape');
    unset($getExportData);
    return $pdf->download($pageTitle.'.pdf');
  }

  public function destroy(Request $request){
    $id = $request->id;
    $company_id = config('settings.company_id');
    $noorder = NoOrder::find($id);
    $employee_id = $noorder->employee_id;
    if ($noorder) {
        DB::table('images')->where('company_id',$company_id)->where('type','noorders')->where('type_id',$noorder->id)->delete(); 
        $deleted = $noorder->delete();

        $superiors = Employee::employeeParents($employee_id, array());
        $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('id', $superiors)->pluck('firebase_token');

        $dataPayload = array("data_type" => "noorder", "noorder" => $id, "action" => "delete", "noorder_id" => $id);
        $msgID = sendPushNotification_($fbIDs, 36, null, $dataPayload);
        
    }
    \Session::flash('success', 'ZeroOrder has been deleted.');
    if($request->has('prev_url')) return redirect()->back();
    return redirect()->route('company.admin.zeroorders', ['domain' => domain()]);
  }

        
}


