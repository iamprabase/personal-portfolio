<?php

namespace App\Http\Controllers\Company\Admin;

use DB;
use Log;
use URL;
use Auth;
use Storage;
use App\Beat;
use App\Note;
use DateTime;
use App\Order;
use Validator;
use App\Client;
use App\NoOrder;
use App\Activity;
use App\Category;
use App\Employee;
use App\BeatVPlan;
use App\PartyType;
use App\RateSetup;
use Carbon\Carbon;
use App\Attendance;
use App\Collection;
use App\MarketArea;
use App\ClientVisit;
use App\CustomField;
use App\PartyUpload;
use App\BusinessType;
use App\OrderDetails;
use App\ClientSetting;
use App\Traits\Upload;
use App\ModuleAttribute;
use App\BeatPlansDetails;
use App\PartyUploadFolder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\ClientCategoryRateType;
use App\Repository\CustomCheck;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    use Upload;
    private $company_id;
    private $companySettings;
    public function __construct()
    {
        $this->middleware('auth');
        $this->companySettings = getClientSetting();
        $this->company_id = $this->companySettings->company_id;
        $this->middleware('permission:party-create', ['only' => ['create','store']]);
        $this->middleware('permission:party-view');
        $this->middleware('permission:party-update', ['only' => ['edit','update','ajaxBasicUpdate','ajaxBusinessUpdate','ajaxContactUpdate','ajaxLocationUpdate','ajaxAccountingUpdate','ajaxMiscellaneousUpdate']]);
        $this->middleware('permission:party-delete', ['only' => ['destroy']]);
        $this->middleware('permission:party-status', ['only' => ['changeStatus']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function viewData($partyTypeId){
      $company_id = config('settings.company_id');
      $party_type_id = $partyTypeId;
      if($company_id == 55 && $party_type_id=="wholeseller-retailer"){
        $allClients = Auth::user()->handleQuery('client')->where('client_type', 2)->orWhere('client_type', 3)->orderBy('created_at', 'desc')->get(['business_id', 'created_by']);
        $clients = $allClients->count();

        $clienttype = "Wholeseller/Retailers";
        $party_type_id = 2;

      }else{
        if($party_type_id != 0){
          $clienttypeget = PartyType::select('name')->where('company_id', $company_id)->where('id', $party_type_id)->first();
          $stringName = str_replace(' ','-',$clienttypeget->name).'-view';
          $permission = Permission::where('company_id',$company_id)->where('name',$stringName)->first();
          if(!(Auth::user()->hasPermissionTo($permission->id))){
            return null;
          }
          
          $clienttype=$clienttypeget->name;
        }else{
          $clienttype = 'Parties';
        }
        $allClients = Auth::user()->handleQuery('client')->where('client_type', $party_type_id)->orderBy('created_at', 'desc')->get(['business_id', 'created_by']);
        $clients = $allClients->count();
      }
      $business_ids = array_unique($allClients->pluck('business_id')->toArray());
      $business_types = BusinessType::whereIn('id', $business_ids)
                        ->orderBy('business_name', 'asc')                  
                        ->pluck('business_name', 'id')
                        ->toArray();
      $created_by_ids = array_unique($allClients->pluck('created_by')->toArray());
      $created_by = Employee::whereIn('id', $created_by_ids)
      ->orderBy('name', 'asc')
      ->pluck('name', 'id')->toArray();

      $data = [
        'clients' => $clients, 
        'clienttype' => $clienttype, 
        'party_type_id' => $party_type_id, 
        'business_types' => $business_types, 
        'created_by' => $created_by
      ];

      return $data;
    }

    public function index(Request $request)
    {
      $id = $request->id ? $request->id : 0;
      $viewData = $this->viewData($id);
      if(empty($viewData)){
        return redirect()->route('company.admin.home', ['domain' => domain()])->with(['error'=>'you don\'t have sufficient permission to view this content. ']);
      }
      return view('company.clients.index')->with($viewData);
    }

    public function fileredResults(Request $request){
      $viewData = $this->viewData($request->id);
      $getType = explode('-', $request->type);
      $type = $getType[0];
      $viewData['clients'] = $getType[1];
      $viewData['type'] = $type;
      $viewData['dateRange'] = $request->range ? explode('_', $request->range) : array();
      
      switch ($type) {
        case 'ordered_party':
          $viewData['clienttype'] = $viewData['clienttype'].'(Not Ordered)';
          break;
        case 'never_ordered_party':
          $viewData['clienttype'] = $viewData['clienttype'].'(Never Ordered)';
          break;
        case 'unvisited_party':
          $viewData['clienttype'] = $viewData['clienttype'].'(UnVisited)';
          break;
        case 'no_action':
          $viewData['clienttype'] = $viewData['clienttype'].'(Inactive)';
          break;
        default:
          $viewData['clienttype'] = $viewData['clienttype'];
          break;
      }
      return view('company.clients.analytics')->with($viewData);
    }

    public function retailers()
    {
        $company_id = config('settings.company_id');
        if ($company_id == 55) {
            $clients = Auth::user()->handleQuery('client')->where('client_type', 2)->orWhere('client_type', 3)->orderBy('created_at', 'desc')->count();
            $clienttype = 'WholeSeller/Retailer';
            $party_type_id = 9;
        }
        if ($company_id == 56) {
            $clients = Auth::user()->handleQuery('client')->where('client_type', 9)->orWhere('client_type', 10)->orderBy('created_at', 'desc')->count();
            $clienttype = 'WholeSeller/Retailer';
            $party_type_id = 3;
        }
        return view('company.clients.index', compact('clients', 'clienttype', 'party_type_id'));
    }

    public function retailerslist(Request $request)
    {
        $company_id = config('settings.company_id');
        $party_type_id = $request->id;
        $clients = Auth::user()->handleQuery('client')->where('superior', $party_type_id)->orderBy('created_at', 'desc')->count();
        $cname=Auth::user()->handleQuery('client')->where('id', $party_type_id)->first();
        $clienttype = $cname->company_name;
        return view('company.clients.index', compact('clients', 'clienttype'));
    }

    public function ajaxDatatable(Request $request){
        $columns = array(
          'id',
          'company_name',
          'name',
          'phone',
          'mobile',
          'email',
          "location",
          "address_line1",
          "address_line2",
          "business_type",
          "added_by",
          "created_at",
          "status"
        );

        $company_id = config('settings.company_id');
        $client_type_id = $request->client_type_id;
        $start = $request->input('start');
        $limit = $request->input('length');
        $order = $columns[$request->input('order.0.column')];
        if($order == "business_type") $order = 'business_types.business_name';
        elseif($order == "added_by") $order = 'employees.name';
        else $order = 'clients.'.$order;
        $dir = $request->input('order.0.dir');
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $newAdded = $request->new_added;
        $no_gps = $request->no_gps;
        
        

        $selectCols = ['clients.id', 'clients.company_name', 'clients.name','clients.client_type', 'clients.phone', 'clients.mobile', 'clients.email', 'clients.status','clients.image','clients.image_path', 'clients.address_1', 'clients.address_2', 'clients.location', 'clients.business_id', 'clients.created_by', 'clients.created_at', 'business_types.business_name', 'employees.name as employee_name'];

        $initialQuery = Auth::user()->handleQuery('client');
        if(($request->not_placed_order || $request->not_visited) && !$request->no_action_taken) {
          
          if($request->not_placed_order) {
            $initialQuery = $initialQuery->PartyNeverOrdered(array($startDate, $endDate), $client_type_id, $selectCols);
          }

          if($request->not_visited) {
            $initialQuery = $initialQuery->PartyUnVisited(array($startDate, $endDate), $client_type_id, $selectCols);
          }
        }elseif($request->no_action_taken) {
          $initialQuery = $initialQuery->PartyNoAction(array($startDate, $endDate), $client_type_id, $selectCols);

        }

        if($no_gps){
          $initialQuery = $initialQuery->whereNULL('latitude')->whereNULL('longitude');
        }

        if ($company_id == 55 && ($client_type_id ==  2)) {
          $prepQuery = $initialQuery
          ->where(function($query) use($startDate, $endDate, $newAdded){
            if($startDate && $endDate && $newAdded){
              $query->whereDate('clients.created_at', '>=', $startDate);
              $query->whereDate('clients.created_at', '<=', $endDate);
            }
          })
          ->leftJoin('business_types', 'business_types.id', 'clients.business_id')->leftJoin('employees', 'employees.id', 'clients.created_by')->where(function($query) use($client_type_id){
            $query->orWhere('client_type', $client_type_id);
            $query->orWhere('client_type', 3);
          })->where(function($query) use($request){
            if($request->created_by) $query->where('created_by', $request->created_by);
            if($request->business_id) $query->where('created_by', $request->created_by);
          });
        }else{
          $prepQuery = $initialQuery
          ->where(function($query) use($startDate, $endDate, $newAdded){
            if($startDate && $endDate && $newAdded){
              $query->whereDate('clients.created_at', '>=', $startDate);
              $query->whereDate('clients.created_at', '<=', $endDate);
            }
          })
          ->leftJoin('business_types', 'business_types.id', 'clients.business_id')->leftJoin('employees', 'employees.id', 'clients.created_by')->where('client_type', $client_type_id)->where(function($query) use($request){
            if($request->created_by) $query->where('created_by', $request->created_by);
            if($request->business_id) $query->where('business_id', $request->business_id);
          });
        }

        if($request->has('analytics_filter')){
          $type = $request->type;
          switch ($type) {
            case 'ordered_party':
              $filterids = Auth::user()->handleQuery('client')->PartyNeverOrdered($request->dateRange, $request->client_type_id, array('id'))->pluck('id')->toArray();
              break;
            case 'never_ordered_party':
              $filterids = Auth::user()->handleQuery('client')->PartyNeverOrdered($request->dateRange, $request->client_type_id, array('id'))->pluck('id')->toArray();
              break;
            case 'unvisited_party':
              $filterids = Auth::user()->handleQuery('client')->PartyUnVisited($request->dateRange, $request->client_type_id, array('id'))->pluck('id')->toArray();
              break;
            case 'no_action':
              $filterids = Auth::user()->handleQuery('client')->PartyNoAction($request->dateRange, $request->client_type_id, array('id'))->pluck('id')->toArray();
              break;
            default:
              $filterids = array();
              break;
          }

          $prepQuery = $prepQuery->whereIn('clients.id', $filterids);
          
        }
        
        $totalData =  $request->clients_count;//$prepQuery->count();
        $totalFiltered = $prepQuery->count();
        
        $data = array();
        
        if (empty($request->input('search.value'))) {
            $parties = $prepQuery
                  ->offset($start)
                  ->limit($limit)
                  ->orderBy($order, $dir)
                  ->get($selectCols);
        } elseif (!(empty($request->input('search.value')))) {
            $search = $request->input('search.value');

            $partiesSearchQuery = $prepQuery
                          ->where(function ($query) use ($search) {
                              $query->orWhere('clients.company_name', 'LIKE', "%{$search}%");
                              $query->orWhere('clients.name', 'LIKE', "%{$search}%");
                              $query->orWhere('clients.phone', 'LIKE', "%{$search}%");
                              $query->orWhere('clients.mobile', 'LIKE', "%{$search}%");
                              $query->orWhere('clients.email', 'LIKE', "%{$search}%");
                              $query->orWhere('clients.status', 'LIKE', "%{$search}%");
                              $query->orWhere('business_types.business_name', 'LIKE', "%{$search}%");
                              $query->orWhere('employees.name', 'LIKE', "%{$search}%");
                          });
            $totalFiltered = $partiesSearchQuery->count();
            $parties =   $partiesSearchQuery
                          ->offset($start)
                          ->limit($limit)
                          ->orderBy($order, $dir)
                          ->get($selectCols);
        }

        $selIds = $request->selIds; 
        $selectThisPageCheckBox = true;

        if ($parties->first()) {
            $viewable_employees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();

            $i = $start;
            if($client_type_id!=0)
              $deleteLink = 'company.admin.subclient.destroy';
            else
              $deleteLink = 'company.admin.client.destroy';
            
            $partyUpdatePerm = Auth::user()->can('party-update');
            $partyDeletePerm = Auth::user()->can('party-delete');
            $partyStatusPerm = Auth::user()->can('party-status');
            
            $checkpartytypepermissionForDel = checkpartytypepermission($parties->first()->client_type,'delete');
            $checkpartytypepermissionForView = checkpartytypepermission($parties->first()->client_type,'view');
            $checkpartytypepermissionForUpdate = checkpartytypepermission($parties->first()->client_type,'update');
            $checkpartytypepermissionForStatus = checkpartytypepermission($parties->first()->client_type,'status');
            
              foreach ($parties as $party) {
                $id = $party->id;
                $company_name = ucfirst($party->company_name);
                $status = $party->status;
                $show = domain_route('company.admin.client.show', [$id]);
                $edit = domain_route('company.admin.client.edit', [$id]);
                $delete = domain_route($deleteLink, [$id]);
                $client_page = domain_route('company.admin.client.show', [$id]);
                
                $canUpdatePartyTypePemission = $partyStatusPerm && $checkpartytypepermissionForStatus;
                //Auth::user()->can('party-status') && checkpartytypepermission($party->client_type,'status');
                $canDeletePartyTypePemission = $checkpartytypepermissionForDel;
                //checkpartytypepermission($party->client_type,'delete');
                
                $checked = "";
                if(in_array($party->id, explode(',', $selIds))) $checked = "checked";
                else $selectThisPageCheckBox = false;
                
                if($canUpdatePartyTypePemission || $canDeletePartyTypePemission) $partyIdSpanTag = "<input type='checkbox' class='partyStatusCheckBox' data-update='{$canUpdatePartyTypePemission}' data-delete='{$canDeletePartyTypePemission}' name='update_party_status' value='{$party->id}' {$checked}>";
                else $partyIdSpanTag = null;

                if($party->image_path){
                  $path = URL::asset('cms/'.$party->image_path);
                  $image_path = "<div class='text-center'><img class='direct-chat-gotimg' src='$path' alt='User Image'>";
                }else{
                  $path = URL::asset('cms/storage/app/public/uploads/nopartyimage.png');
                  $image_path = "<div class='direct-chat-img text-center'><img style='width:99%' src='$path' alt='User Image'>";
                }
                

                $nestedData['id'] = $partyIdSpanTag.++$i;
                $nestedData['company_name'] = "<div class='partyImgName'><div>$image_path</div></div>"."<div><p style='margin-top:5px;'><a href='{$client_page}'>{$company_name}</a></p></div></div>";
                $nestedData['name'] = $party->name;
                $nestedData['phone'] = $party->phone;
                $nestedData['mobile'] = $party->mobile;
                $nestedData['email'] = $party->email;
                $nestedData['address_line1'] =$party->address_1;
                $nestedData['address_line2'] =$party->address_2;
                $nestedData['location']      =$party->location;

                $nestedData["business_type"] = $party->business_name;
                $empName = $party->employee_name;
                $empShow = in_array($party->created_by, $viewable_employees)?domain_route('company.admin.employee.show',[$party->created_by]):null;
                $empSection = '<a href="'.$empShow.'" data-viewable="'.$empShow.'" class="empLinks">'.$empName.'</a>';
                $nestedData["added_by"] = $empSection;
                $nestedData["created_at"] = $party->created_at? getDeltaDate( date("Y-m-d", strtotime($party->created_at)) ) : NULL;

                if ($status =='Active') {
                  $className = 'label label-success';
                } elseif ($status =='Inactive') {
                  $className = 'label label-danger';
                } else {
                  $className = 'label label-warning';
                }

                if($canUpdatePartyTypePemission)
                $nestedData['status'] = "<a href='#' class='edit-modal' data-id='{$id}' data-status='{$status}'><span class='{$className}'>{$status}</span></a>";
                else
                $nestedData['status'] = "<a href='#' class='alert-modal' data-id='{$id}' data-status='{$status}'><span class='{$className}'>{$status}</span></a>";

            
                if (getPartyActivity($id) && $canDeletePartyTypePemission) {
                  $deleteBtn = "<a class='btn btn-danger btn-sm delete' data-mid='{$id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
                } else {
                  $deleteBtn = null;
                }
                if($checkpartytypepermissionForView)
                $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                else
                $nestedData['action'] = "<a href='#' class='btn btn-success btn-sm alert-modal' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                
                if($partyUpdatePerm && $checkpartytypepermissionForUpdate)
                $nestedData['action'] = $nestedData['action'] ."<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                
                if($partyDeletePerm && $checkpartytypepermissionForDel)
                $nestedData['action'] =$nestedData['action'].$deleteBtn;
                
                $data[] = $nestedData;
            }
        }else{
          $selectThisPageCheckBox = false;
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
            "prevSelVal"      => $selIds,
            "selectThisPageCheckBox" => $selectThisPageCheckBox
        );

        return json_encode($json_data);
    }

    public function custompdfdexport(Request $request){
      $getExportData = json_decode($request->exportedData)->data;
      $pageTitle = $request->pageTitle;
      set_time_limit(300);
      ini_set("memory_limit", "256M");
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      
      $pdf = PDF::loadView('company.clients.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'landscape');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }

    public function pdfexports(Request $request){
      $getExportData = json_decode($request->exportedData);
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      $pageTitle = $request->pageTitle;
      $moduleName = $request->moduleName;
      $paperOrientation = "portrait";
      set_time_limit(300);
      ini_set("memory_limit", "256M");
      $pdf = PDF::loadView('company.clients.partials_show.exportpdf', compact('getExportData', 'pageTitle', 'columns', 'properties'))->setPaper('a4', $paperOrientation);
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }

    public function partyOrdersTable(Request $request){
        if(config('settings.order_with_amt')==0){
            $columns = array( 
                0 =>'id', 
                1 =>'order_no',
                2=> 'order_date',
                3=> 'empName',
                4=> 'grand_total',
                5=> 'delivery_status',
                6=> 'action',
            );
        }else{
            $columns = array( 
                0 =>'id', 
                1 =>'order_no',
                2=> 'order_date',
                3=> 'empName',
                4=> 'delivery_status',
                5=> 'action',
            );
        }
        $company_id = config('settings.company_id');
        $totalData =  Order::where('company_id',$company_id)->where('client_id',$request->clientID)->get()->count();
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if($request->input('search.value')){
              $search = $request->input('search.value'); 
        } 
        $orders = Order::select('orders.*','employees.id as empID','employees.name as empName','clients.id as clientID','clients.company_name as partyname','client_settings.order_prefix','module_attributes.id as moduleattributesId','module_attributes.title as status_name','module_attributes.color','module_attributes.order_amt_flag','module_attributes.order_edit_flag','module_attributes.order_delete_flag')
            ->leftJoin('employees', 'orders.employee_id', 'employees.id')
            ->leftJoin('clients', 'orders.client_id', 'clients.id')
            ->leftJoin('client_settings', 'orders.company_id','client_settings.company_id')
            ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
            ->where('orders.company_id',$company_id)->where('orders.client_id',$request->clientID);
            if($request->input('search.value')){
              $orders = $orders->where(function($query) use ($search){
                  $query->where('orders.id' ,'LIKE', "%{$search}%");
                  $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
                  $query->orWhere('orders.delivery_status' ,'LIKE', "%{$search}%");
                  $query->orWhere('orders.grand_total' ,'LIKE', "%{$search}%");
                  $query->orWhere('orders.order_date' ,'LIKE', "%{$search}%");
                  $query->orWhere('orders.order_no', 'LIKE',"%{$search}%");
                  $query->orWhere(\DB::raw("Concat(client_settings.order_prefix ,orders.order_no)"), 'LIKE', "%{$search}%");
              });
            }

            $total = $orders->sum('grand_total');
            $totalFiltered = $orders->get()->count();
            if($order=='order_date'){
              $orders = $orders->orderBy('id',$dir);  
            }
            $orders = $orders->orderBy($order,$dir)->offset($start)
                        ->limit($limit)
                        ->get();
            $data = array();
        if(!empty($orders))
        {   
            $i = $start;
            $viewable_employees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
            foreach ($orders as $order)
            {
                $show =  domain_route('company.admin.order.show',[$order->id]);
                $edit =  domain_route('company.admin.order.edit',[$order->id]);
                $delete = domain_route('company.admin.order.destroy', [$order->id]);
                if($order->employee_id!=0){
                  $empName = $order->empName;
                  $empShow = in_array($order->employee_id, $viewable_employees)?domain_route('company.admin.employee.show',[$order->employee_id]):null;
                  $empSection = '<a href="'.$empShow.'" data-viewable="'.$empShow.'" class="empLinks">'.$empName.'</a>';
                }else{
                  $outlet_details = $order->outlets()->withTrashed()->first();
                  $empName = ucfirst($outlet_details->contact_person);
                  $imgSrc = URL::asset('assets/dist/img/ret_logo.png');
                  $empSection = "<span><img class='retimg' src=$imgSrc></img>{$empName}</span>";
                }

                $nestedData['id'] = ++$i;
                $nestedData['orderno'] = "<a href='{$show}'>".getClientSetting()->order_prefix.$order->order_no."</a>";
                $nestedData['orderdate'] = getDeltaDate($order->order_date);
                $nestedData['empName'] = $empSection;

                if(config('settings.order_with_amt')==0){
                    $nestedData['grandtotal'] = config('settings.currency_symbol').' '.number_format((float)$order->grand_total,2);
                }

                $spanTag = NULL;
                $orderStatus = $order->status_name;
                $statusColor = $order->color;
                $spanTag = "<span class='label' style='background: {$statusColor};'>{$orderStatus}</span>";
                $delivery_date = Carbon::parse($order->delivery_date)->format('Y-m-d');
                $userLowerChain = Auth::user()->getChainUsers(Auth::user()->EmployeeId());
                if(in_array($order->employee_id, $userLowerChain) || Auth::user()->EmployeeId()==$order->employee_id){
                    $tempstatus = "edit-modal-order"; 
                }else{
                    $tempstatus = "alert-modal";
                }
                if(Auth::user()->can('order-status'))
                $nestedData['delivery_status'] = "<a href='#' class='edit-modal-order' data-id='{$order->id}' data-status='{$order->delivery_status_id}' data-orderdate='{$delivery_date}' data-nodate='{$delivery_date}' data-note='{$order->delivery_note}' data-place='{$order->delivery_place}' data-transport_name='{$order->transport_name}' data-transport_number='{$order->transport_number}' data-billty_number='{$order->billty_number}'>$spanTag";
                else
                $nestedData['delivery_status'] = "<a href='#' class='alert-modal' data-id='{$order->id}' data-status='{$order->delivery_status_id}' data-orderdate='{$delivery_date}' data-nodate='{$delivery_date}' data-note='{$order->delivery_note}' data-place='{$order->delivery_place}' data-transport_name='{$order->transport_name}' data-transport_number='{$order->transport_number}' data-billty_number='{$order->billty_number}'>$spanTag";
                
                // if(in_array($order->employee_id, $userLowerChain) || Auth::user()->EmployeeId()==$order->employee_id){
                  $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm'
                    style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";

                  if($order->order_edit_flag == 1 && Auth::user()->can('order-update')){
                    $nestedData['action'] = $nestedData['action']."<a href='{$edit}' class='btn btn-warning btn-sm'
                    style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                  }
                  if($order->order_delete_flag == 1 && Auth::user()->can('order-delete')){
                    $nestedData['action'] = $nestedData['action']."<a class='btn btn-danger btn-sm delete' data-mid='{ $order->id }' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
                  }                  
                // }else{
                //   $nestedData['action'] = '';
                // } 
                
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

        echo json_encode($json_data); 
    }

    public function partyOrdersReceivedTable(Request $request){
      if (config('settings.order_with_amt') == 0) {
        $columns = array(
            'id',
            'order_no',
            'partyName',
            'order_date',
            'empName',
            'grand_total',
            'delivery_status',
            'action',
        );
    } else {
        $columns = array(
            'id',
            'order_no',
            'partyName',
            'order_date',
            'empName',
            'delivery_status',
            'action',
        );
    }
        $company_id = config('settings.company_id');
        $totalData =  Order::where('company_id',$company_id)->where('order_to',$request->clientID)->get()->count();
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if($request->input('search.value')){
              $search = $request->input('search.value'); 
        }
        $orders = Order::select('orders.*','employees.id as empID','employees.name as empName','clients.id as clientID','clients.company_name as partyname','client_settings.order_prefix','module_attributes.id as moduleattributesId','module_attributes.title as status_name','module_attributes.color','module_attributes.order_amt_flag','module_attributes.order_edit_flag','module_attributes.order_delete_flag')
            ->leftJoin('employees', 'orders.employee_id', 'employees.id')
            ->leftJoin('clients', 'orders.client_id', 'clients.id')
            ->leftJoin('client_settings', 'orders.company_id','client_settings.company_id')
            ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
            ->where('orders.company_id',$company_id)->where('orders.order_to',$request->clientID);
            if($request->input('search.value')){
              $orders = $orders->where(function($query) use ($search){
                  $query->where('orders.id' ,'LIKE', "%{$search}%");
                  $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
                  $query->orWhere('orders.delivery_status' ,'LIKE', "%{$search}%");
                  $query->orWhere('orders.grand_total' ,'LIKE', "%{$search}%");
                  $query->orWhere('orders.order_date' ,'LIKE', "%{$search}%");
                  $query->orWhere('orders.order_no', 'LIKE',"%{$search}%");
                  $query->orWhere(\DB::raw("Concat(client_settings.order_prefix ,orders.order_no)"), 'LIKE', "%{$search}%");
              });
            }

            $total = $orders->sum('grand_total');
            $totalFiltered = $orders->get()->count();
            if($order=='order_date'){
              $orders = $orders->orderBy('id',$dir);  
            }
            $orders = $orders->orderBy($order,$dir)->offset($start)
                        ->limit($limit)
                        ->get();
            $data = array();
        if(!empty($orders))
        {   
            $i = $start;
            $viewable_employees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
            $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();

            foreach ($orders as $order)
            {
                $show =  domain_route('company.admin.order.show',[$order->id]);
                $edit =  domain_route('company.admin.order.edit',[$order->id]);
                $delete = domain_route('company.admin.order.destroy', [$order->id]);
                if($order->employee_id!=0){
                  $empName = $order->empName;
                  $empShow = in_array($order->employee_id, $viewable_employees)?domain_route('company.admin.employee.show',[$order->employee_id]):null;
                  $empSection = '<a href="'.$empShow.'" data-viewable="'.$empShow.'" class="empLinks">'.$empName.'</a>';
                }else{
                  $outlet_details = $order->outlets()->withTrashed()->first();
                  $empName = ucfirst($outlet_details->contact_person);
                  $imgSrc = URL::asset('assets/dist/img/ret_logo.png');
                  $empSection = "<span><img class='retimg' src=$imgSrc></img>{$empName}</span>";
                }

                $nestedData['id'] = ++$i;
                $nestedData['orderno'] = "<a href='{$show}'>".getClientSetting()->order_prefix.$order->order_no."</a>";
                $clientShow = in_array($order->client_id, $viewable_clients) && Auth::user()->can('party-view') ? domain_route('company.admin.client.show', [$order->client_id]) : '#';
                $partyName = $order->partyname;
                $isViewable = in_array($order->client_id, $viewable_clients) && Auth::user()->can('party-view') ?1:null;
                $nestedData['partyName'] = "<a class='clientLinks' href='{$clientShow}' data-viewable='{$isViewable}' dataparty='{$partyName}'>{$partyName}</a>";
                $nestedData['orderdate'] = getDeltaDate($order->order_date);
                $nestedData['empName'] = $empSection;

                if(config('settings.order_with_amt')==0){
                    $nestedData['grandtotal'] = config('settings.currency_symbol').' '.number_format((float)$order->grand_total,2);
                }

                $spanTag = NULL;
                $orderStatus = $order->status_name;
                $statusColor = $order->color;
                $spanTag = "<span class='label' style='background: {$statusColor};'>{$orderStatus}</span>";
                $delivery_date = Carbon::parse($order->delivery_date)->format('Y-m-d');
                $userLowerChain = Auth::user()->getChainUsers(Auth::user()->EmployeeId());
                if(in_array($order->employee_id, $userLowerChain) || Auth::user()->EmployeeId()==$order->employee_id){
                    $tempstatus = "edit-modal-order"; 
                }else{
                    $tempstatus = "alert-modal";
                }
                if(Auth::user()->can('order-status'))
                $nestedData['delivery_status'] = "<a href='#' class='edit-modal-order' data-id='{$order->id}' data-status='{$order->delivery_status_id}' data-orderdate='{$delivery_date}' data-nodate='{$delivery_date}' data-note='{$order->delivery_note}' data-place='{$order->delivery_place}' data-transport_name='{$order->transport_name}' data-transport_number='{$order->transport_number}' data-billty_number='{$order->billty_number}'>$spanTag";
                else
                $nestedData['delivery_status'] = "<a href='#' class='alert-modal' data-id='{$order->id}' data-status='{$order->delivery_status_id}' data-orderdate='{$delivery_date}' data-nodate='{$delivery_date}' data-note='{$order->delivery_note}' data-place='{$order->delivery_place}' data-transport_name='{$order->transport_name}' data-transport_number='{$order->transport_number}' data-billty_number='{$order->billty_number}'>$spanTag";
                
                // if(in_array($order->employee_id, $userLowerChain) || Auth::user()->EmployeeId()==$order->employee_id){
                  $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm'
                    style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";

                  if($order->order_edit_flag == 1 && Auth::user()->can('order-update')){
                    $nestedData['action'] = $nestedData['action']."<a href='{$edit}' class='btn btn-warning btn-sm'
                    style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                  }
                  if($order->order_delete_flag == 1 && Auth::user()->can('order-delete')){
                    $nestedData['action'] = $nestedData['action']."<a class='btn btn-danger btn-sm delete' data-mid='{ $order->id }' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
                  }                  
                // }else{
                //   $nestedData['action'] = '';
                // } 
                
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

        echo json_encode($json_data); 
    }

    public function partyZeroOrdersTable(Request $request){
        $getColumns = $request->columns;
        $columns = array();
        $sizeof = sizeof($getColumns);
        for($count = 0; $count<$sizeof; $count++){
          $columns[$count] = $getColumns[$count]["data"];
        }

        $units = $request->units;

        $company_id = config('settings.company_id');
        $partyVal = $request->get('clientID');
        $search = $request->input('search')['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $order_col_no = $request->input('order.0.column');
        $order = $columns[$order_col_no];
        if($order == "id"){
          $order = "no_orders."."id";
        }elseif($order == "employee_name"){
          $order = "employees."."name";
        }elseif($order == "date"){
          $order = "no_orders.id";//.$order;
        }elseif($order == "date"){
          $order = "no_orders.remark";
        }
        $dir = $request->input('order.0.dir');

        $prepQuery = NoOrder::select('clients.id as client_id', 'clients.name as contact_person_name', 'clients.company_name', 'clients.mobile as contact_number', 'clients.superior as party_superior', 'clients.client_type as party_type', 'partytypes.name as party_type_name','clients.address_1 as address', 'employees.name as employee_name', 'no_orders.employee_id as employee_id','no_orders.id as noorder_id', 'no_orders.company_id', 'no_orders.date', 'no_orders.remark')
                  ->leftJoin('clients', 'no_orders.client_id', '=', 'clients.id')
                  ->leftJoin('employees', 'no_orders.employee_id', '=', 'employees.id')
                  ->leftJoin('partytypes', 'clients.client_type', '=', 'partytypes.id')
                  ->leftJoin('client_settings', 'no_orders.company_id','client_settings.company_id')->where('no_orders.company_id',$company_id)->where('no_orders.client_id', $partyVal);

        if(!empty($search)){
          $searchQuery = (clone $prepQuery);
          $prepQuery = $searchQuery->where(function($query) use ($search){
                        $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
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

        $no_orders =  $prepQuery->orderBy($order,$dir)->offset($start)
                              ->limit($limit)->get();

        if (!empty($no_orders)) {
            $i = $start;
            $viewable_employees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
            foreach ($no_orders as $noorder) {
              $id = $noorder->noorder_id;

              $show = domain_route('company.admin.zeroorder.show', [$id]);
              $edit = domain_route('company.admin.zeroorder.edit', [$id]);
              $delete = domain_route('company.admin.zeroorder.destroy', [$id]);


              $employee_name = $noorder->employee_name;
              $employee_show = in_array($noorder->employee_id, $viewable_employees)?domain_route('company.admin.employee.show',[$noorder->employee_id]):null;
              $date = isset($noorder->date)?getDeltaDate(date('Y-m-d',strtotime($noorder->date))):null;
              $remark = $noorder->remark;
              
              $nestedData['id'] = ++$i;
              $nestedData['employee_name'] = "<a href='{$employee_show}' datasalesman='{$employee_name}' data-viewable='{$employee_show}' class='empLinks'> {$employee_name}</a>";
              $nestedData['date'] =$date;
              $nestedData['remark'] = $remark;

              $action = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";

              if(Auth::user()->can('zeroorder-update')){
                $action = $action."<a href='{$edit}' class='btn btn-warning btn-sm'
                style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
              }
              if(Auth::user()->can('zeroorder-delete')){
                $action = $action."<a class='btn btn-danger btn-sm delete' data-mid='{$id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
              }
              $nestedData['action'] = $action;

              
              // $client_company_name = $noorder->company_name;
              // $client_show = domain_route('company.admin.client.show',[$noorder->client_id]);
              // $client_name = $noorder->contact_person_name;
              // $party_type =  $noorder->party_type_name;
              // $contact =  $noorder->contact_number;
              // $address =  $noorder->address;
              // $nestedData['contact_person'] = $client_name;
              // $nestedData['party_type'] = $party_type;
              // $nestedData['contact_number'] = $contact;
              // $nestedData['address'] = $address;

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


    public function partyCollectionTable(Request $request)
    {
      $columns = array(
        0 => 'id',
        1 => 'payment_date',
        2 => 'empName',
        3 => 'payment_received',
        4 => 'payment_method',
        5 => 'action',
      );
      $company_id = config('settings.company_id');
      $totalData =  Collection::where('company_id',$company_id)->where('employee_id',$request->clientID)->get()->count();
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');
      if($request->input('search.value')){
            $search = $request->input('search.value'); 
      }
      $collections = Collection::select('collections.*','employees.id as empID','employees.name as empName')
                  ->leftJoin('employees', 'collections.employee_id', 'employees.id')
                  ->where('collections.client_id',$request->clientID)->where('collections.company_id',$company_id);
                  if($request->input('search.value')){
                    $collections=$collections->where(function($query) use ($search){
                        $query->where('collections.id' ,'LIKE', "%{$search}%");
                        $query->orWhere('collections.payment_received' ,'LIKE', "%{$search}%");
                        $query->orWhere('collections.payment_method' ,'LIKE', "%{$search}%");
                        $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
                       });
                  }

              $total = $collections->sum('payment_received');
              $totalFiltered = $collections->get()->count(); 
              $collections = $collections->orderBy($order,$dir)->orderBy('collections.id','desc')->offset($start)
                          ->limit($limit)
                          ->get();
          $data = array();
          if (!empty($collections)) {
            $i = $start;
            $viewable_employees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
            foreach ($collections as $collection) {
              $id = $collection->id;
              $received_payment = isset($collection->payment_received)?config('settings.currency_symbol').'   '.number_format((float)$collection->payment_received,2):null;
              $payment_date = isset($collection->payment_date)?getDeltaDate(date('Y-m-d',strtotime($collection->payment_date))):null;
              $status = $collection->status;
              $empShow = in_array($collection->employee_id, $viewable_employees)?domain_route('company.admin.employee.show',[$collection->employee_id]):NULL;
              $show = domain_route('company.admin.collection.show', [$id]);
              $edit = domain_route('company.admin.collection.edit', [$id]);
              $delete = domain_route('company.admin.collection.destroy', [$id]);

              $nestedData['id'] = ++$i;
              $nestedData['payment_date'] = $payment_date;
              $nestedData['empName'] = "<a class='empLinks' data-viewable='{$empShow}' dataparty='".$collection->empName."' href='".$empShow."'>".$collection->empName."</a>" ;
              $nestedData['payment_received'] = $received_payment;
              $nestedData['payment_method'] = $collection->payment_method;
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $company_id    = config('settings.company_id');
        $companyName = Auth::user()->companies->company_name;
        $countries     = DB::table("countries")->pluck("name", "id")->toArray();
        
        $superiors     = [];
        $states        = array();
        $cities        = array();
        $partytypes    = array();
        $partytypes    = PartyType::where('company_id', $company_id)->where('parent_id', 0)->get();
        $marketareas   = MarketArea::where('company_id', $company_id)->pluck('name', 'id')->toArray();
        $country_code  = DB::table('countries')->pluck('name', 'phonecode')->toArray();
        $employees     = Auth::user()->handleQuery('employee')->where('status', 'Active')->orderBy('name', 'asc')->get();
        $businessTypes = BusinessType::where('company_id',$company_id)->select('id','business_name')->get();
        $beats         = Beat::where('company_id', $company_id)->get();

        $hasPermission = false;
        $permissions   = Permission::where('permission_type','Company')->get();
        foreach($permissions as $permission){
            if(strpos($permission->name,'-create')!==false){
                if(Auth::user()->hasPermissionTo($permission->id)){
                    $hasPermission = true;
                }
            }
        }
        $permissionType = 'create';
        $custom_fields = CustomField::where('company_id',$company_id)->where('status',1)->where('for','Party')->get();
        $custom_fields->map(function($item){
          $item->visible = true;
        });

        if(config('settings.party_wise_rate_setup')==1){
          $rates = RateSetup::where('company_id', $company_id)->pluck('name', 'id')->toArray();
        }else{
          $rates = array();
        }
        if(config('settings.category_wise_rate_setup') == 1){
          $category_with_rates = Category::whereHas('categoryrates')->CompanyCategories(array('name', 'id'), array('categoryrates'))->toArray();
        }else{
          $category_with_rates = array();
        }
        return view('company.clients.create', compact('countries', 'companyName', 'states', 'cities', 'country_code', 'employees', 'partytypes', 'marketareas', 'superiors', 'beats','businessTypes','permissionType','hasPermission','custom_fields', 'rates', 'category_with_rates'));
    }

    public function validateCompanyName(Request $request)
    {
      $company_id = $this->company_id;
      $create = $request->has('create');
      $customMessages = [
        'company_name.required' => 'Party Name is required.',
        'company_name.unique' => 'Party Name already exists',
        'mobile.digits_between' => 'Mobile Number should be between 7 to 20 digits',
        'mobile.min' => 'Mobile Number should be between 7 to 20 digits',
        'mobile.max' => 'Mobile Number should be between 7 to 20 digits',
        'mobile.regex' => 'Mobile Number should not contain non-digit character',

      ];
      $regexphone="/^([0-9\s\-\+\/\(\)]*)$/|min:7|max:20";
      if($create){
          if($request->field_name == "company_name"){
              $this->validate($request, [
                  'company_name' => config('settings.allow_party_duplication') ==0 ? 'required|unique:clients,company_name,NULL,id,deleted_at,NULL,company_id,' . $company_id:'required',
              ], $customMessages);
          }else{
              $this->validate($request, [
                  'mobile' => 'sometimes|regex:'.$regexphone.'|unique:clients,mobile,NULL,id,deleted_at,NULL,company_id,' . $company_id,
              ], $customMessages);
          }
      }else{
          Log::info($request->all());
          if($request->field_name == "company_name"){
              $this->validate($request, [
                  'company_name' => config('settings.allow_party_duplication') ==0 ? 'required|unique:clients,company_name,'.$request->id.',id,company_id,'. $company_id.',deleted_at,NULL':'required',
              ], $customMessages);
          }else{
              $this->validate($request, [
                  'mobile' => 'sometimes|regex:'.$regexphone.'|unique:clients,mobile,'.$request->id.',id,deleted_at,NULL,company_id,' . $company_id,
              ], $customMessages);
          }
      }

      return response()->json([
        'msg' => 'Success',
        'statusCode' => 200
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
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $partytypes = PartyType::where('company_id', $company_id)->pluck('name', 'id')->toArray();
        $regex = "/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/";
        $regexphone="/^([0-9\s\-\+\/\(\)]*)$/|min:7|max:20";
        
        $customMessages = [
            'company_name.required' => 'Party Name is required.',
            'company_name.unique' => 'Party Name already exists',
            'name.required' => 'Contact Person Name is Required',
            'mobile.digits_between' => 'Mobile Number should be between 7 to 20 digits',
            'mobile.min' => 'Mobile Number should be between 7 to 20 digits',
            'mobile.max' => 'Mobile Number should be between 7 to 20 digits',
            'mobile.regex' => 'Mobile Number should not contain non-digit character',
            'mobile.unique' => 'Mobile Number already exists for another party',
            'country.required' => 'Country is required',
            'state.required' => 'State is required',
            'city.required' => 'City is required',
            'location.required' => 'Location Field is required',
            'client_type.required' => 'Client Type Field is required',
            'image.mimes'=>'Only jpeg, png file types are accepted.',
            'image.uploaded'=>'Selected File size exceeded',
        ];

        $this->validate($request, [
            'company_name' => config('settings.allow_party_duplication') ==0 ? 'required|unique:clients,company_name,NULL,id,deleted_at,NULL,company_id,' . $company_id : 'required',
            'name' => 'required',
            'country' => 'required',
            'email' => 'sometimes|nullable|email',
            'website' => 'sometimes|nullable|regex:' . $regex,
            'location' => 'required',
            'opening_balance' => 'sometimes|numeric|nullable',
            'credit_limit' => 'sometimes|numeric|nullable',
        ], $customMessages);

        // dd($request->all());

        // if (!empty($partytypes)) {
        //     $this->validate($request, [
        //         'client_type' => 'required',
        //     ], $customMessages);
        // }

        if (!empty($request->mobile)) {
            $this->validate($request, [
                'mobile' => 'sometimes|regex:'.$regexphone.'|unique:clients,mobile,NULL,id,deleted_at,NULL,company_id,' . $company_id,
            ], $customMessages);
        }

        DB::beginTransaction();

        $special_char = array("!", "@", "$", "%", "|", ":", ";");
        $client = new \App\Client;
        $client->company_id = $company_id;
        $client->company_name = trim($request->get('company_name'));
        $client->client_code = $request->get('client_code');
        $client->name = $request->get('name');
        $client->phonecode = $request->get('phonecode');
        $client->phone = str_replace($special_char,",",$request->get('phone'));
        $client->mobile = $request->get('mobile');
        $client->fax = $request->get('fax');
        $client->pan = $request->get('pan');
        $client->email = $request->get('email');
        $client->website = $request->get('website');
        $client->created_by = Auth::user()->EmployeeId();
        $citySel = $request->get('city');
        $client->city = $citySel;
        $client->state = $request->get('state');

        $client->country = $request->get('country');
        $client->pin = $request->get('pin');
        $client->about = str_replace('&nbsp;', '', $request->get('about'));
        if(Auth::user()->can('party-status'))
          $client->status = $request->get('status');
        else
          $client->status = 'Active';
        $client->address_1 = $request->get('address_1');
        $client->address_2 = $request->get('address_2');
        if($request->business_id)
        $client->business_id = $request->business_id;

        $client->location = $request->get('location');
        $client->latitude = $request->get('lat');
        $client->longitude = $request->get('lng');

        $client->opening_balance = $request->get('opening_balance');
        $client->credit_limit = $request->get('credit_limit');
        $client->credit_days = $request->get('credit_days')?$request->get('credit_days'):0;
        
        if (!empty($partytypes)) {
            $client->client_type = $request->get('client_type')?$request->get('client_type'):0;
            $client->superior = $request->get('superior');
        }
        $client->market_area = $request->get('market_area');
        if ($request->file('image')) {
            $this->validate($request,[
              'image' => 'mimes:jpeg,png,jpg|max:2000',
            ],$customMessages);
            $image2 = $request->file('image');
            $realname = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image2->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $image2->storeAs('public/uploads/' . $companyName . '/clients/', $new_name);
            $path = Storage::url('app/public/uploads/' . $companyName . '/clients/' . $new_name);
            $client->image = $new_name;
            $client->image_path = $path;
        }

        if($request->has('rate')){
          $client->rate_id = $request->get('rate');
        }
        $saved = $client->save();
        if ($saved) {

            $collec = CustomField::where('for','=', 'Party')->where('add','=', 'yes')->get();

            if($collec->count()):
                $array_field = (new  CustomCheck($collec, $request))->check();
            endif;

            if(isset($array_field) && !empty($array_field)):
                DB::table('party_meta')->insert([
                    'client_id' => $client->id,
                    'cf_value' => json_encode($array_field)
                ]);
            endif;

            DB::table('handles')->insert([
                    'company_id' => $company_id,
                    'employee_id' => $client->created_by,
                    'client_id' => $client->id,
                    'map_type' => 2,
                ]);
            $employeeInstance = Employee::where('company_id', $company_id)->where('is_admin', 1)->pluck('id')->toArray();
            if (!empty($employeeInstance)) {
                foreach ($employeeInstance as $adminId) {
                    if ($client->created_by!=$adminId) {
                        DB::table('handles')->insert([
                          'company_id' => $company_id,
                          'employee_id' => $adminId,
                          'client_id' => $client->id,
                          'map_type' => 2,
                        ]);
                    }
                }
            }
            $getSuperiors = $this->getAllEmployeeSuperior($client->created_by, $superiors=[]);
            if (!(empty($getSuperiors))) {
                foreach ($getSuperiors as $getSuperior) {
                    if (in_array($getSuperior, $employeeInstance)) {
                        break;
                    }
                    DB::table('handles')->insert([
                      'company_id' => $company_id,
                      'employee_id' => $getSuperior,
                      'client_id' => $client->id,
                      'map_type' => 2,
                      ]);
                }
            }

            if($request->beat != "0" && $request->beat != "Select Beat"){
              if ($request->beat != null ) {
                  DB::table('beat_client')->insert([
                      'client_id' => $client->id,
                      'beat_id' => $request->beat,
                  ]);
                  $client->beat_id = $request->beat;
                  if(isset($citySel)){
                    if($citySel<>0){
                      $beatInstance = Beat::where('id', $request->beat)->first();
                      $getBeatClients = DB::table('beat_client')->where('beat_id', $request->beat)->pluck('client_id')->toArray();
                      $clientsBeats = Client::where('company_id', $company_id)->whereIn('id', $getBeatClients)->whereNotNull('city')->distinct('city')->pluck('city')->toArray();
                      if($beatInstance && sizeof($clientsBeats)==1){
                        if( $citySel == $clientsBeats[0]){
                          if(!isset($beatInstance->city_id)){
                            $beatInstance->city_id = $citySel;
                            $beatInstance->update();
                          }
                        }
                      }
                    }
                  }
              }
            }

            $clientHandlingData = getClientHandlingData($company_id, $client->id, true);
            $encodedHandlingData = json_encode($clientHandlingData);
            $client->employee_ids = $encodedHandlingData;

            $country = getCountryName($client->country)->name;
            $client->country_name = empty($country) ? "" : $country;

            $state = getStateName($client->state);
            $client->state_name = empty($state) ? "" : $state->name;

            $city = getCityName($client->city);
            $client->city_name = empty($city) ? "" : $city;
            
            if($request->has('category_rates') && config('settings.category_wise_rate_setup') == 1){
              $category_rates = $request->category_rates;
              if(count($category_rates) > 0){
                $records = array();
                foreach($category_rates as $category_rate){
                  array_push($records, array('client_id' => $client->id, 'category_rate_type_id' => $category_rate));
                }
                ClientCategoryRateType::insert($records);
              }
            }

            DB::commit();
            
            $client->appliedcategoryrates = $client->appliedcategoryrates()->get(['id', 'category_id', 'name'])->pluck('id')->toJson();
            
            //sending push notification
            $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $client, "action" => "add"));
        }
        
        if ($request->client_type) {
            return redirect()->route('company.admin.client.subclients', ['domain' => domain(), 'id' => $client->client_type])->with('success', 'Information has been  Added');
        } else {
            return redirect()->route('company.admin.client', ['domain' => domain()])->with('success', 'Information has been  Added');
        }
    }

    public function getAllEmployeeSuperior($empId, $superiors)
    {
        $company_id = config('settings.company_id');
        $getSuperior = Employee::where('id', $empId)->where('company_id', $company_id)->first();
        if (!(empty($getSuperior->superior)) && !(in_array($getSuperior->superior, $superiors))) {
            $superiors[] = $getSuperior->superior;
            $superiors = $this->getAllEmployeeSuperior($getSuperior->superior, $superiors);
        }

        return $superiors;
    }

    public function getEmployeeSuperiors(Request $request)
    {
        $getSuperiors = $this->getAllEmployeeSuperior($request->employee_id, $superiors=[]);

        return $getSuperiors;
    }

    public function getEmployeeJuniors(Request $request)
    {
        $empID = $request->employee_id;
        $employeeInstance = Employee::findOrFail($empID);
        if ($employeeInstance) {
            $getJuniors = Auth::user()->getChainUsers($employeeInstance->id, $juniors=[]);
        }
        return $getJuniors;
    }

    public function getSuperiorList(Request $request)
    {
        $company_id = config('settings.company_id');
        if (Auth::user()->isCompanyEmployee()) {
            $employeeId = Auth::user()->EmployeeId();
        } else {
            $employeeId = null;
        }
        $cid = $request->cid;
        $allsuperiors = $this->getAllSuperiors($request->client_type, $cid);
        if ($employeeId) {
            $accessiblePartiesDetails = Client::leftJoin('partytypes', 'clients.client_type', 'partytypes.id')->leftJoin('accessibility_link', 'clients.id', 'accessibility_link.client_id')->where('clients.company_id', $company_id)->where('accessibility_link.employee_id', $employeeId)->orWhere(function($query) use($cid){
              if($cid){
                $current_clientSuperior = Client::find($cid);
                if($current_clientSuperior) $query->where('clients.id', $current_clientSuperior->superior);
              }
            })->get(['clients.*', 'partytypes.name as partytypename']);
            if ($accessiblePartiesDetails->first()) {
                foreach ($accessiblePartiesDetails as $accessiblePartiesDetail) {
                    if (array_key_exists($accessiblePartiesDetail->partytypename, $allsuperiors)) {
                        $allsuperiors[$accessiblePartiesDetail->partytypename][] = $accessiblePartiesDetail;
                    }
                }
            }
        } 
        return response()->json($allsuperiors);
    }

    public function getAllSuperiors($id, $cid)
    {
        $clients = [];
        $company_id = config('settings.company_id');
        $superior = PartyType::where('company_id', $company_id)->where('id', $id)->first();
        $allParent = [];
        if($superior){
          while ($superior->parent_id != 0) {
              $parent = $superior = PartyType::where('company_id', $company_id)->where('id', $superior->parent_id)->first();
              $allParent[$parent->id] = $parent->company_name;
              $clients[$parent->name] = Auth::user()->handleQuery('client')
                  ->where('client_type', $parent->id)
                  ->where('id', '!=', $cid)
                  ->orderBy('created_at', 'desc')
                  ->get();
              $this->getAllSuperiors($parent->id, $cid);
          }
        }
        return array_reverse($clients);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Client $client
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $companySettings = $this->companySettings; 
        $company_id = $companySettings->company_id;
        if (Auth::user()->isCompanyManager()) {
            $client = Client::select('clients.*', 'countries.id as country_id', 'countries.name as country_name', 'partytypes.id as partytypes_id', 'partytypes.name as partytype_name')
                              ->leftJoin('countries', 'clients.country', 'countries.id')
                              ->leftJoin('partytypes', 'clients.client_type', 'partytypes.id')
                              ->where('clients.company_id',$company_id)->where('clients.id',$request->id)->first();
        } else {
            $handles = DB::table('handles')
                            ->where('employee_id', Auth::user()->EmployeeId())
                            ->where('client_id', $request->id)
                            ->pluck('client_id')
                            ->toArray();
            $client = Auth::user()->handleQuery('client', $request->id)
                  ->select('clients.*', 'countries.id as country_id', 'countries.name as country_name', 'partytypes.id as partytypes_id', 'partytypes.name as partytype_name')
                  ->leftJoin('countries', 'clients.country', 'countries.id')
                  ->leftJoin('partytypes', 'clients.client_type', 'partytypes.id')
                  ->whereIn('clients.id', $handles)
                  ->first();
        }
        

        if(!$client){
          return redirect()->back()->withErrors(['msg', 'No record Found']);
        }

        if($client->client_type!='0'){
          $stringName = str_replace(' ','-',$client->partytype_name).'-view';
          $permission = Permission::where('company_id',$company_id)->where('name',$stringName)->first();
          if($permission){
            if(Auth::user()->hasPermissionTo($permission->id)){
                $details_tab_view = true;
            }else{
                $details_tab_view = false;
            }
          }else{
            $details_tab_view = false;
          }
        }else{
          $details_tab_view = true;
        }

        if ($client) {
            $userLowerChain = Auth::user()->getChainUsers(Auth::user()->EmployeeId());
            $orders = Order::where('client_id', $request->id)
            ->orderBy('created_at', 'desc')
            ->get();
            $last_order = $orders->first();
            $getOrderStatusFlag = ModuleAttribute::where('company_id', $company_id)->where('module_id', 1)->where('order_amt_flag', 1)->pluck('id')->toArray();
            if (!empty($getOrderStatusFlag)) {
                $tot_order_amount = (clone $orders)->whereIn('delivery_status_id', $getOrderStatusFlag)->sum('grand_total');
            } else {
                $tot_order_amount = 0;
            }
            $collections = Collection::where('company_id',$company_id)->where('client_id', $request->id)
            ->orderBy('created_at', 'desc')
            ->get();
            $cheque_collection_amount = $collections->where('payment_method', 'Cheque')->where('payment_status', 'Cleared')->sum('payment_received');
            $cash_collection_amount = $collections->where('payment_method', 'Cash')->sum('payment_received');
            $bank_collection_amount = $collections->where('payment_method', 'Bank Transfer')->sum('payment_received');
            $tot_collection_amount = $cheque_collection_amount + $cash_collection_amount + $bank_collection_amount;

            $meetings = Note::where('client_id', $request->id)->where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->get();
            $employees = Employee::where('company_id', $company_id)->where('status', 'Active')->orderBy('name', 'asc')->get();

            $employeesDesignations = array();
            $userSuperiors = array();
            $userJuniors = array();
            if (Auth::user()->isCompanyManager()) {
                $designationWiseEmployees = Employee::where('employees.company_id', $company_id)->leftJoin('designations', 'employees.designation', 'designations.id')->orderBy('designations.parent_id', 'asc')->where('employees.status', 'Active')->get(['employees.*', 'designations.name as designation_name']);
            } else {
                $userChain = Auth::user()->getAllChainUsers(Auth::user()->EmployeeId());
                $admins = Employee::where('company_id', $company_id)->where('is_admin', 1)->pluck('id')->toArray();
                $userChain = array_unique(array_merge($admins, $userChain));
                $userSuperiors = $this->getAllEmployeeSuperior(Auth::user()->EmployeeId(), $superiors=[]);
                $designationWiseEmployees = Employee::where('employees.company_id', $company_id)->leftJoin('designations', 'employees.designation', 'designations.id')->orderBy('designations.parent_id', 'asc')->where('employees.status', 'Active')->whereIn('employees.id', $userChain)->get(['employees.*', 'designations.name as designation_name']);
                $userJuniors = Auth::user()->getChainUsers(Auth::user()->EmployeeId());
            }
            foreach ($designationWiseEmployees as $designationWiseEmployee) {
                $employeesDesignations[$designationWiseEmployee->designation_name][] = $designationWiseEmployee;
            }
            if (Auth::user()->isCompanyManager()) {
                $handles = DB::table('handles')->where('company_id', $company_id)->where('client_id', $request->id)->pluck('employee_id')->toArray();
            } else {
                $userChain = Auth::user()->getAllChainUsers(Auth::user()->EmployeeId());
                $admins = Employee::where('company_id', $company_id)->where('is_admin', 1)->pluck('id')->toArray();
                $userChain = array_unique(array_merge($admins, $userChain));
                $handles = DB::table('handles')->where('company_id', $company_id)->where('client_id', $request->id)->whereIn('employee_id', $userChain)->pluck('employee_id')->toArray();
            }
            $empId = Auth::user()->EmployeeId();
            if (Auth::user()->isCompanyManager()) {
                $allSup = [];
            } else {
                $allSup = [];
                $allSup = Auth::user()->getUpChainUsers($empId, $allSup);
                array_pop($allSup);
                $empAdmins = Employee::where('company_id', $company_id)->select('id')->where('is_admin', 1)->get();
                foreach ($empAdmins as $emp) {
                    array_push($allSup, $emp->id);
                }
            }
            $links = DB::table('accessibility_link')->where('company_id', $company_id)->where('client_id', $request->id)->pluck('employee_id')->toArray();
            $expenses = Auth::user()->handleQuery('expense')->where('client_id', $request->id)->orderBy('expenses.id', 'DESC')->get();
            $delivered_order_amount = Order::select('grand_total')->where('client_id', $request->id)->where('company_id', $company_id)->where('delivery_status', '!=', 'Cancelled')->get();
            $new_order_amount = Order::select('grand_total')->where('client_id', $request->id)->where('company_id', $company_id)->where('delivery_status', 'New')->get();
            $complete_order_amount = Order::select('grand_total')->where('client_id', $request->id)->where('company_id', $company_id)->where('delivery_status', 'Complete')->get();
            $cancelled_order_amount = Order::select('grand_total')->where('client_id', $request->id)->where('company_id', $company_id)->where('delivery_status', 'Cancelled')->get();
            $in_process_order_amount = Order::select('grand_total')->where('client_id', $request->id)->where('company_id', $company_id)->where('delivery_status', 'In Process')->get();
            $company_id = config('settings.company_id');
            $subClient = [];
            $subClientsArray = $this->getSubClients($client->id, $subClient);
            foreach ($subClientsArray as $key => $value) {
                $pType = PartyType::where('id', $key)->select('id', 'name')->first();
                if($pType){
                  $permission_name = str_replace(" ", "-", $pType->name) . "-view";
                  $permission_id = DB::table('permissions')->where('name', 'LIKE', $permission_name)
                  ->whereCompanyId($company_id)->first();
                  if($permission_id){
                    if( Auth::user()->hasPermissionTo($permission_id->id)) $subClientsArray[$key]['name'][] = $pType->name;
                  }
                } 
            }
            $co_order_amount = 0.00;
            $ca_order_amount = 0.00;
            $ip_order_amount = 0.00;
            $n_order_amount = 0.00;
            $order_amount = 0.00;
            $collection_amount = 0.00;

            foreach ($new_order_amount as $order1) {
                $n_order_amount += $order1->grand_total;
            }
            foreach ($delivered_order_amount as $order2) {
                $order_amount += $order2->grand_total;
            }
            foreach ($complete_order_amount as $order3) {
                $co_order_amount += $order3->grand_total;
            }
            foreach ($cancelled_order_amount as $order4) {
                $ca_order_amount += $order4->grand_total;
            }
            foreach ($in_process_order_amount as $order5) {
                $ip_order_amount += $order5->grand_total;
            }
            foreach ($collections as $collection) {
                $collection_amount += $collection->payment_received;
            }
            $beats = Beat::where('company_id', $company_id)->get();
            $countries = DB::table('countries')->pluck('name', 'id')->toArray();
            $existBeatClient = DB::table('beat_client')->where('client_id', $client->id)->first();
            if ($existBeatClient) {
                $currentBeatID = $existBeatClient->beat_id;
                $currentbeat = Beat::where('company_id', $company_id)->where('id', $currentBeatID)->first();
                $currentBeatName = ($currentbeat)?$currentbeat->name:null;
            } else {
                $currentBeatID = null;
                $currentBeatName = null;
            }
        
            $states = DB::table('states')->where('country_id', $client->country)->pluck('name', 'id')->toArray();
            $cities = DB::table('cities')->where('state_id', $client->state)->pluck('name', 'id')->toArray();
            $parent = PartyType::where('company_id', $company_id)->where('id', $client->client_type)->first();

            $party_meta   = DB::table('party_meta')->where('client_id',$client->id)->first();
            if($party_meta){
              $custom_fields = CustomField::where('company_id',$company_id)->where('status',1)->where('for','Party')->get();
              $cf_value = (array)json_decode($party_meta->cf_value);
              $fieldIDs = array_keys($cf_value);
              foreach($custom_fields as $custom_field){
                if(in_array($custom_field->id,$fieldIDs)){
                  $custom_field->custom_value = $cf_value[$custom_field->id];
                  if($custom_field->deleted_at != Null && isset($custom_field->custom_value)){
                    $custom_field->visible = true;
                  }elseif($custom_field->deleted_at == Null){
                    $custom_field->visible = true;
                  }else{
                    $custom_field->visible = false;
                  }
                }else{
                  if($custom_field->deleted_at==Null)
                    $custom_field->visible = true;
                }
              }
            }else{
              $custom_fields = CustomField::where('company_id',$company_id)->where('status',1)->where('for','Party')->get();
              $custom_fields->map(function($item){
                $item->visible = true;
              });
            }

            $superiors = Client::where('company_id', $company_id)
                        ->where('client_type', $parent['parent_id'])
                        ->orderBy('created_at', 'desc')
                        ->pluck('company_name', 'id')->toArray();
            $partytypes = PartyType::where('company_id', $company_id)->where('parent_id', 0)->get();
            $activities = Activity::where('company_id',$company_id)->where('client_id', $request->id)->orderBy('id', 'DESC')->orderBy('start_datetime','DESC')->get();
            // $tasks = Task::where('client_id', $request->id)->where('due_date', '>=', date('Y-m-d'))->where('status', '!=', 'Cancelled')->orderBy('due_date', 'asc')->get();
            $client->client_city_name = ($client->city)?(getCityName($client->city)?getCityName($client->city):"NA"):"NA";
            $client->client_state_name = ($client->state)?(getStateName($client->state)?getStateName($client->state)->name:"NA"):"NA";
            $orderStatus = \App\ModuleAttribute::where('company_id', $company_id)
                ->where('module_id', 1)->get();
            $ordersCount=Order::where('company_id',$company_id)->where('client_id',$request->id)->get()->count();
            $collectionsCount=Collection::where('company_id',$company_id)->where('client_id',$request->id)->get()->count();
            $businessTypes = BusinessType::where('company_id',$company_id)->select('id','business_name')->get();
            $juniors = Employee::EmployeeChilds(Auth::user()->EmployeeId(),array());
            $permissionType = 'update';

            if($companySettings->party_wise_rate_setup==1){
              $rates = RateSetup::where('company_id', $company_id)->pluck('name', 'id')->toArray();
            }else{
              $rates = array();
            }

            $companyName = Auth::user()->companies->company_name;

            $folders = PartyUploadFolder::where('client_id', $client->id)->orderby('id', 'desc')->get(['name', 'type', 'id', 'created_at', 'updated_at']);
            $file_folders = $folders->where('type', 'files');
            $image_folders = $folders->where('type', 'images');

            $file_folders_id = PartyUploadFolder::whereCompanyId($client->company_id)->wherehas('files')->where('type', 'files')->pluck('id')->toArray();
            $total_size = PartyUpload::whereIn('party_upload_folder_id', $file_folders_id)->sum('file_size');
            $total_upload_allowed = $companySettings->total_file_size_gb;
            $total_size = $total_size > 0? round(($total_size / 1073741824),2) : $total_size; 
            
            $percent_used = $total_upload_allowed > 0 ? round(($total_size * 100 / $total_upload_allowed), 2) : 0;
            $total_size = round($total_size, 2);
            $globalSettings = $companySettings->toArray();
            $enabledAccounting = !getCompanyPartyTypeLevel($company_id);
            $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
            $due_or_overdue_text = "Due Amount";
            if($last_order){
              $last_order_date = $last_order->order_date;
              $numOfDays = $client->credit_days > 1 ? "+".$client->credit_days." days": "+".$client->credit_days." day";
              $lastCreditDate = date('Y-m-d', strtotime($numOfDays, strtotime($last_order_date)));
              if($lastCreditDate < date('Y-m-d')) $due_or_overdue_text = "OverDue Amount";
            }
            $current_categoryid  = array();
            $current_category_rates_id  = array();
            $category_with_rates = array();
            $current_category_rates_name = array();
            if(config('settings.category_wise_rate_setup') == 1){
              $category_with_rates = Category::whereHas('categoryrates')->CompanyCategories(array('name', 'id'), array('categoryrates'))->toArray();
              $current_category_rates = $client->appliedcategoryrates()->get(['id', 'name', 'category_id'])->toArray();
              if(count($current_category_rates) > 0){
                foreach($current_category_rates as $current_category_rate){
                    array_push($current_category_rates_id, $current_category_rate['id']);
                    $current_categoryid[$current_category_rate['category_id']] = $current_category_rate['id'];
                    array_push($current_category_rates_name, $current_category_rate['name']);
                }
              }
            }
            return view('company.clients.show', compact('client', 'companyName', 'orders', 'collections', 'meetings', 'employees', 'handles', 'expenses', 'order_amount', 'co_order_amount', 'ca_order_amount', 'ip_order_amount', 'n_order_amount', 'collection_amount', 'activities', 'subClientsArray', 'currentBeatID', 'currentBeatName', 'beats', 'superiors', 'countries', 'states', 'cities', 'partytypes', 'tot_order_amount', 'tot_collection_amount', 'links', 'orderStatus', 'employeesDesignations', 'userSuperiors', 'userJuniors', 'userLowerChain', 'allSup','ordersCount','collectionsCount','businessTypes','juniors','permissionType','custom_fields', 'details_tab_view', 'rates','party_meta', 'folders', 'file_folders', 'image_folders', 'total_size', 'total_upload_allowed', 'percent_used', 'globalSettings', 'enabledAccounting', 'viewable_clients', 'due_or_overdue_text', 'current_categoryid', 'current_category_rates_id', 'category_with_rates', 'current_category_rates_name'));
        } else {
            return redirect()->back()->withErrors(['msg', 'No record Found']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Client $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $company_id = config('settings.company_id');
        $states = array();
        $cities = array();
        $countries=array();
        $superiors=array();
        //$country_code=array();

        $client = Auth::user()->handleQuery('client', $request->id)->first();

        if (empty($client)) {
            return redirect()->back()->withErrors(['msg', 'No record Found']);
        }
        $countries = DB::table('countries')->pluck('name', 'id')->toArray();
        $country_code = DB::table('countries')->pluck('name', 'phonecode')->toArray();
        $employees = Auth::user()->handleQuery('employee')->where('status', 'Active')->orderBy('name', 'asc')->get();
        $partytypes = PartyType::where('company_id', $company_id)->where('parent_id', 0)->get();
        $marketareas = MarketArea::where('company_id', $company_id)->pluck('name', 'id')->toArray();
        $beats = [];
        if ($client):
          if(isset($client->city)){
            $beats = Beat::where('company_id', $company_id)->where('city_id', $client->city)->orWhere(function($query) use ($company_id){
              $query->where('company_id', $company_id)->whereNull('city_id');
            })->get();
          }else{
            $beats = Beat::where('company_id', $company_id)->get();
          }

        $existBeatClient = DB::table('beat_client')->where('client_id', $client->id)->first();
        if ($existBeatClient!=null) {
            $currentBeatID = $existBeatClient->beat_id;
        } else {
            $currentBeatID = null;
        }
        if ($client->country!=''):
                $states = DB::table('states')->where('country_id', $client->country)->pluck('name', 'id')->toArray();
        $cities = DB::table('cities')->where('state_id', $client->state)->pluck('name', 'id')->toArray();
        $parent = PartyType::where('company_id', $company_id)->where('id', $client->client_type)->first();
        if($parent){
            $superiors = Client::where('company_id', $company_id)
                        ->where('client_type', $parent->parent_id)
                        ->orderBy('created_at', 'desc')
                        ->pluck('company_name', 'id')->toArray();
        }

        endif;

        $hasPermission = false;
        $permissions   = Permission::where('permission_type','Company')->get();
        foreach($permissions as $permission){
            if(strpos($permission->name,'-update')!==false){
                if(Auth::user()->hasPermissionTo($permission->id)){
                    $hasPermission = true;
                }
            }
        }
        $businessTypes = BusinessType::where('company_id',$company_id)->select('id','business_name')->get();
        $party_meta   = DB::table('party_meta')->where('client_id',$client->id)->first();
        if($party_meta){
          $custom_fields = CustomField::where('company_id',$company_id)->where('status',1)->where('for','Party')->get();
          $cf_value = (array)json_decode($party_meta->cf_value);
          $fieldIDs = array_keys($cf_value);
          foreach($custom_fields as $custom_field){
            if(in_array($custom_field->id,$fieldIDs)){
              $custom_field->custom_value = $cf_value[$custom_field->id];
              if($custom_field->deleted_at != Null && isset($custom_field->custom_value)){
                $custom_field->visible = true;
              }elseif($custom_field->deleted_at == Null){
                $custom_field->visible = true;
              }else{
                $custom_field->visible = false;
              }
            }else{
              if($custom_field->deleted_at==Null)
                $custom_field->visible = true;
            }
          }
        }else{
          $custom_fields = CustomField::where('company_id',$company_id)->where('status',1)->where('for','Party')->get();
          $custom_fields->map(function($item){
            $item->visible = true;
          });
        }
        $permissionType = 'update';
        
        if(config('settings.party_wise_rate_setup')==1){
          $rates = RateSetup::where('company_id', $company_id)->pluck('name', 'id')->toArray();
        }else{
          $rates = array();
        }
        
        $companyName = Auth::user()->companies->company_name;
        
        $category_with_rates = array();
        $current_category_rates_id = array(); 
        $current_categoryid = array(); 
        
        if(config('settings.category_wise_rate_setup') == 1){
          $category_with_rates = Category::whereHas('categoryrates')->CompanyCategories(array('name', 'id'), array('categoryrates'))->toArray();
          $current_category_rates = $client->appliedcategoryrates()->get(['id', 'name', 'category_id'])->toArray();
          if(count($current_category_rates) > 0){
            foreach($current_category_rates as $current_category_rate){
                array_push($current_category_rates_id, $current_category_rate['id']);
                $current_categoryid[$current_category_rate['category_id']] = $current_category_rate['id'];
            }
          }
        }

        return view('company.clients.edit', compact('companyName', 'client', 'countries', 'states', 'cities', 'country_code', 'employees', 'partytypes', 'marketareas', 'superiors', 'beats', 'currentBeatID','businessTypes','permissionType','hasPermission', 'rates','custom_fields', 'category_with_rates', 'current_category_rates_id', 'current_categoryid')); else:
            return redirect()->route('company.admin.client', ['domain' => domain()]);
        endif;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Client $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $regex = "/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/";
        $regexphone="/^([0-9\s\-\+\/\(\)]*)$/|min:7|max:20";
        $client = Client::findOrFail($request->id);
        $partytypes = PartyType::where('company_id', $company_id)->pluck('name', 'id')->toArray();

        $customMessages = [
            'company_name.required' => 'Party Name is required.',
            'company_name.unique' => 'Party Name already exists',
            'name.required' => 'Contact Person Name is Required',
            'mobile.digits_between' => 'Mobile Number should be between 7 to 20 digits',
            'mobile.min' => 'Mobile Number should be between 7 to 20 digits',
            'mobile.max' => 'Mobile Number should be between 7 to 20 digits',
            'mobile.regex' => 'Mobile Number should not contain non-digit character',
            'country.required' => 'Country is required',
            'state.required' => 'State is required',
            'city.required' => 'City is required',
            'location.required' => 'Location Field is required',
            'client_type.required' => 'Client Type Field is required',
            'lng.required' => 'Given address does not have valid longitude',
            'lat.required' => 'Given address does not have valid latitude',
            'image.mimes' => 'Only jpeg, png file types are accepted.',
            'image.uploaded'=>'Selected File size exceeded',
        ];

        $this->validate($request, [
            'name' => 'required',
            'phone' => 'sometimes|nullable',
            'country' => 'required',
            'website' => 'sometimes|nullable|regex:' . $regex,
            'location' => 'required',
            'opening_balance' => 'sometimes|numeric|nullable',
            'credit_limit' => 'sometimes|numeric|nullable',
        ], $customMessages);
        
        // if (!empty($partytypes)) {
        //     $this->validate($request, [
        //         'client_type' => 'required',
        //     ], $customMessages);
        // }

        if ($client->company_name != $request->company_name) {
            $this->validate($request, [
                'company_name' => config('settings.allow_party_duplication') ==0 ?'required|unique:clients,company_name,' . $request->id . ',id,company_id,' . $company_id . ',deleted_at,NULL' : 'required',
            ], $customMessages);
        }

        if ($client->email != $request->email) {
            $this->validate($request, [
                'email' => 'sometimes|nullable|email|unique:clients,email,' . $request->id,
            ], $customMessages);
        }

        if (!empty($request->mobile)) {
            if ($request->mobile != $client->mobile) {
                $this->validate($request, [
                    'mobile' => 'sometimes|regex:'.$regexphone.'|unique:clients,mobile,'.$request->id.',id,deleted_at,NULL,company_id,' . $company_id,
                ], $customMessages);
            }
        }

        if ($request->get('client_code')) {
            $this->validate($request, [
                'client_code' => 'sometimes|nullable|unique:clients,client_code,' . $request->id . ',id,company_id,' . $company_id . ',deleted_at,NULL',
            ], $customMessages);
        }
        $special_char = array("!", "@", "$", "%", "|", ":", ";");
        $client->company_name = $request->get('company_name');
        
        $client->business_id = $request->business_id;
        $client->client_code = $request->get('client_code');
        $client->name = $request->get('name');
        $client->phonecode = $request->get('phonecode');
        $client->phone = str_replace($special_char,",",$request->get('phone'));
        $client->mobile = $request->get('mobile');
        $client->fax = $request->get('fax');
        $client->pan = $request->get('pan');
        $client->email = $request->get('email');
        $client->website = $request->get('website');
        $citySel = $request->get('city');
        $client->city = $citySel;
        $client->state = $request->get('state');
        $client->country = $request->get('country');
        $client->pin = $request->get('pin');
        $client->about = str_replace('&nbsp;', '', $request->get('about'));
        if(Auth::user()->can('party-status'))
          $client->status = $request->get('status');
        $client->address_1 = $request->get('address_1');
        $client->address_2 = $request->get('address_2');
        $client->location = $request->get('location');
        $client->latitude = $request->get('lat');
        $client->longitude = $request->get('lng');
        $client->opening_balance = $request->get('opening_balance');
        $client->client_type = $request->get('client_type')?$request->get('client_type'):0;
        $client->superior = $request->get('superior');
        $client->market_area = $request->get('market_area');
        $client->credit_limit = $request->get('credit_limit');
        $client->credit_days = $request->get('credit_days');
        if($request->confirmremove){
          $client->image = null;
          $client->image_path = null;
        }
        if ($request->file('image')) {
            $this->validate($request,[
              'image' => 'mimes:jpeg,png,jpg|max:2000',
            ],$customMessages);
            $image2 = $request->file('image');
            $realname = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image2->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $image2->storeAs('public/uploads/' . $companyName . '/clients/', $new_name);
            $path = Storage::url('app/public/uploads/' . $companyName . '/clients/' . $new_name);
            $client->image = $new_name;
            $client->image_path = $path;
        }

        if($request->has('rate')){
          $client->rate_id = $request->get('rate');
        }
        DB::beginTransaction();
        $saved = $client->save();
        $clientID = $client->id;

        if ($saved) {

            $party_meta = DB::table('party_meta')->where('client_id',$client->id)->first();
            if($party_meta){
                $collec = CustomField::where('company_id',$company_id)->where('status',1)->where('for','=', 'Party')->get();
                $imagesFilesCollections = $collec->where('type','Multiple Images');
                foreach($imagesFilesCollections as $imageCollection){
                    if(!$request[$imageCollection->slug]){
                        $request->merge([
                            $imageCollection->slug => true,
                        ]);
                    }
                }

                $imagesFilesCollections = $collec->where('type','File');
                foreach($imagesFilesCollections as $imageCollection){
                    if(!$request[$imageCollection->slug]){
                        $request->merge([
                            $imageCollection->slug => true,
                        ]);
                    }
                }

                $array_field = (new  CustomCheck($collec, $request, $party_meta))->check();

                if(isset($array_field) && !empty($array_field)):
                  DB::table('party_meta')->where('client_id',$client->id)->update(['cf_value'=>json_encode($array_field)]);
                endif;
            }else{
                $collec = CustomField::where('for','=', 'Party')->where('add','=', 'yes')->get();

                if($collec->count()):
                    $array_field = (new  CustomCheck($collec, $request))->check();
                endif;

                if(isset($array_field) && !empty($array_field)):
                    DB::table('party_meta')->insert([
                        'client_id' => $client->id,
                        'cf_value' => json_encode($array_field)
                    ]);
                endif;
            }

            if ($client->created_by != 0) {
                $emphandles = $suphandles = DB::table('handles')->where('company_id', $company_id)->where('client_id', $client->id)->get();
                $getSuperiors = Employee::where('id', $client->created_by)->value('superior');
                $handle = $emphandles->where('employee_id', $client->created_by)->first();
                if (empty($handle)) {
                    DB::table('handles')->insert([
                      'company_id' => $company_id,
                      'employee_id' => $client->created_by,
                      'client_id' => $client->id,
                      'map_type' => 2,
                  ]);
                }
                $getSuperiors = $this->getAllEmployeeSuperior($client->created_by, $superiors=[]);
                $employeeInstance = Employee::where('company_id', $company_id)->where('is_admin', 1)->pluck('id')->toArray();
                if (!empty($employeeInstance)) {
                    foreach ($employeeInstance as $adminId) {
                        if (!in_array($adminId, $getSuperiors)) {
                            array_push($getSuperiors, $adminId);
                        }
                    }
                }
                if (!(empty($getSuperiors))) {
                    foreach ($getSuperiors as $getSuperior) {
                        $suphandle = $suphandles->where('employee_id', $getSuperior)->first();
                        if (empty($suphandle)) {
                            DB::table('handles')->insert([
                        'company_id' => $company_id,
                        'employee_id' => $getSuperior,
                        'client_id' => $client->id,
                        'map_type' => 2,
                      ]);
                        }
                        unset($suphandle);
                    }
                }
            }

            if($request->beat!="0" && isset($request->beat)){
              if ($request->beat != null && $request->beat != "Select Beat") {
                  $prevBeat =  DB::table('beat_client')->where('client_id', $client->id)->value('beat_id');
                  if ((int)$request->beat!=$prevBeat) {
                      $today = date('Y-m-d');
                      $clientBeatsPlans = BeatVPlan::join('beatplansdetails', 'beatplansdetails.beatvplan_id', 'beatvplans.id')->where('beatvplans.company_id', $company_id)->select('beatvplans.id')->where('beatplansdetails.plandate', '>=', $today)->get();
                      $clientBeatPlans = BeatPlansDetails::whereIn('beatvplan_id', $clientBeatsPlans)
                                                  ->get();
                      if ($clientBeatPlans->first()) {
                          if ($clientBeatPlans->count()>0) {
                              foreach ($clientBeatPlans as $clientBeatPlan) {
                                  $beatPlanBeats = explode(',', $clientBeatPlan->beat_id);
                                  foreach ($beatPlanBeats as $beatPlanBeat) {
                                      if ((int)$beatPlanBeat==$prevBeat) {
                                          $_beatClients = json_decode($clientBeatPlan->beat_clients, true);
                                          $_beats = explode(',', $clientBeatPlan->beat_id);
                                          if(is_array($_beatClients) && !empty($_beatClients)){
                                            if(array_key_exists($prevBeat, $_beatClients)){
                                              if (count($_beatClients[$prevBeat])==1) {
                                                  if (array_key_exists($prevBeat, $_beatClients)) {
                                                      $_beatClients[$request->beat][] = $request->id;
                                                      ;
                                                  } else {
                                                      $_beatClients[$request->beat] = $_beatClients[$prevBeat];
                                                  }
                                                  unset($_beatClients[$prevBeat]);
                                              } else {
                                                  if (array_key_exists($request->beat, $_beatClients)) {
                                                      $_beatClients[$request->beat][] = $request->id;
                                                  } else {
                                                      $_beatClients[$request->beat][] = $request->id;
                                                  }
                                                  $_ind = array_search($request->id, $_beatClients[$prevBeat]);
                                                  unset($_beatClients[$prevBeat][$_ind]);
                                              }
                                              $newBeatIDs = array();
                                              foreach ($_beatClients as $key=>$beatClient) {
                                                  array_push($newBeatIDs, $key);
                                              }
                                              $clientBeatPlan->beat_id = implode(',', $newBeatIDs);
                                              $clientBeatPlan->beat_clients = json_encode($_beatClients);
                                              $clientBeatPlan->save();
                                            }
                                          }
                                      }
                                  }
                              }
                          }
                      }
                  }
                  DB::table('beat_client')->where('client_id', $client->id)->delete();
                  DB::table('beat_client')->insert([
                      'client_id' => $client->id,
                      'beat_id' => $request->beat,
                  ]);
                  $client->beat_id = $request->beat;
                  if(isset($citySel)){
                    $beatInstance = Beat::where('id', $request->beat)->first();
                    $getBeatClients = DB::table('beat_client')->where('beat_id', $request->beat)->pluck('client_id')->toArray();
                    $clientsBeats = Client::where('company_id', $company_id)->whereIn('id', $getBeatClients)->whereNotNull('city')->distinct('city')->pluck('city')->toArray();
                    if($beatInstance && sizeof($clientsBeats)==1){
                      if($citySel == $clientsBeats[0]){
                        if(!isset($beatInstance->city_id)){
                          if($citySel<>0){
                            $beatInstance->city_id = $citySel;
                            $beatInstance->update();
                          }
                        }
                      }
                    }
                  }
              }
            }else{
              DB::table('beat_client')->where('client_id', $client->id)->delete();
            }

            $clientHandlingData = getClientHandlingData($company_id, $client->id, true);
            $encodedHandlingData = json_encode($clientHandlingData);
            $client->employee_ids = $encodedHandlingData;

            $country = getCountryName($client->country)->name;
            $client->country_name = empty($country) ? "" : $country;

            $state = getStateName($client->state);
            $client->state_name = empty($state) ? "" : $state->name;

            $city = getCityName($client->city);
            $client->city_name = empty($city) ? "" : $city;

            if($request->has('category_rates') && config('settings.category_wise_rate_setup') == 1){
              ClientCategoryRateType::whereClientId($client->id)->delete();
              $category_rates = $request->category_rates;
              if(count($category_rates) > 0){
                $records = array();
                foreach($category_rates as $category_rate){
                  array_push($records, array('client_id' => $client->id, 'category_rate_type_id' => $category_rate));
                }
                ClientCategoryRateType::insert($records);
              }
            }
            
            DB::commit();
            $client->appliedcategoryrates = $client->appliedcategoryrates()->get(['id', 'category_id', 'name'])->pluck('id')->toJson();
            
            $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $client, "action" => "update"));

        }
        
        if ($request->client_type) {
            return redirect()->route('company.admin.client.subclients', ['domain' => domain(), 'id' => $client->client_type])->with('success', 'Information has been  Updated');
        } else {
            return redirect()->route('company.admin.client', ['domain' => domain()])->with('success', 'Information has been  Updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Client $client
     * @return \Illuminate\Http\Response
     */

    public function showorder(Request $request)
    {
        $company_id = config('settings.company_id');
        $order = Order::where('id', $request->id)->where('company_id', $company_id)->first();
        $orderdetails = OrderDetails::where('order_id', $request->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('company.clients.showorder', compact('order', 'orderdetails'));
    }

    public function showcollection(Request $request)
    {
        $company_id = config('settings.company_id');
        $collection = Collection::where('id', $request->id)->first();
        return view('company.clients.showcollection', compact('collection'));
    }

    public function changeStatus(Request $request)
    {
        $company_id = config('settings.company_id');
        $today = date("Y-m-d");
        if(is_array($request->client_id)){
          $clientIds = explode(',', $request->client_id[0]);
          if(!empty($clientIds)){
            $updatedParties = Client::where('company_id', $company_id)->whereIn('id', $clientIds)->update(['status' => $request->status]);
            if ($request->status=="Inactive") {
                $beatPlans = BeatPlansDetails::where('beatvplans.company_id', $company_id)->leftJoin('beatvplans', 'beatplansdetails.beatvplan_id', 'beatvplans.id')->where('beatplansdetails.plandate', '>=', $today)->get([
                'beatplansdetails.id',
                'beatplansdetails.beat_clients',
                'beatplansdetails.beat_id',
                'beatplansdetails.client_id',
              ]);
            }
            foreach($clientIds as $clientId){
              $client = Client::find($clientId);
              if($client){
                $client->employee_ids = getClientHandlingData($company_id, $client->id);
                $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $client, "action" => "update_status"));
              }
              if ($request->status=="Inactive") 
              {
                if($beatPlans->first()) $this->updateTodayOrFutureBeatPlans($clientId, $beatPlans);
              }
            }
            \Session::flash('success', "Parties status has been updated succesfully.");

          }
          return back();
        }else{
          $id = $request->client_id;
          $client = Client::findOrFail($id);
          $client->status = $request->status;
          if ($client->status=="Inactive") {
            $beatPlans = BeatPlansDetails::where('beatvplans.company_id',  $company_id)->leftJoin('beatvplans', 'beatplansdetails.beatvplan_id', 'beatvplans.id')->where('beatplansdetails.plandate', '>=', $today)->get([
                  'beatplansdetails.id',
                  'beatplansdetails.beat_clients',
                  'beatplansdetails.beat_id',
                  'beatplansdetails.client_id',
                ]);
            if($beatPlans->first()) $this->updateTodayOrFutureBeatPlans($id, $beatPlans);
          }
          $saved = $client->save();
          if ($saved) {
            $client->employee_ids = getClientHandlingData($company_id, $client->id);
            $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $client, "action" => "update_status"));
          }
          return back();
        }
    }

    private function updateTodayOrFutureBeatPlans($clientId, $beatPlans){
      foreach($beatPlans as $beatPlan){
        try{
          $decodedBeatClients = json_decode($beatPlan->beat_clients);
          $updatedBeatClients = array();
          $updatedClients = array();
          $shouldUpdate = false;
          foreach($decodedBeatClients as $beat_id=>$beat_client_ids){
            if(in_array($clientId, $beat_client_ids)){
              $shouldUpdate = true;
              $clients_without_current = array_diff($beat_client_ids, array($clientId));
              if(!empty($clients_without_current)){
                $updatedBeatClients[$beat_id] = array_values($clients_without_current);
                $updatedClients = $clients_without_current;
              } 
            }else{
              $updatedBeatClients[$beat_id] = $beat_client_ids;
              $updatedClients = $beat_client_ids;
            }
          }
          if($shouldUpdate){
            $beat_id = implode(',', array_keys($updatedBeatClients));
            $beat_clients = json_encode($updatedBeatClients);
            $client_id = implode(',', $updatedClients);
  
            BeatPlansDetails::find($beatPlan->id)->update([
              'beat_id' => $beat_id,
              'beat_clients' => $beat_clients,
              'client_id' => $client_id,
            ]);
          }
        }catch(\Exception $e){
          Log::info($e->getCode());
          Log::info($e->getLine());
          Log::info($e->getMessage());
        }
      }
    }

    public function massdestroy(Request $request)
    {
      $company_id = config('settings.company_id');
      $deletedClients = false;
      $nondeletedClients = false;
      if(is_array($request->party_id)){
        $clientIds = explode(',', $request->party_id[0]);
        if(!empty($clientIds)){
          foreach($clientIds as $id){
            $client = Client::find($id, ['clients.id']);
            $instance = Client::find($id, ['clients.id']);
            
            if ($client) {
              $handleTableData = $client->employees; 
              if (!getPartyActivity($client->id)) {
                $nondeletedClients = true;
                continue;
              }
              if($handleTableData->count()>0){
                DB::table('handles')->where('client_id', $client->id)->delete();
              }
              $deleted = $client->delete();
              $deletedClients = true;
              if ($deleted) {
                $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $instance, "action" => "delete"));
              }
            }
          }
        }
      }
      if($deletedClients) session()->flash('success', 'Parties have been deleted.');
      if($nondeletedClients) session()->flash('warning', 'Some parties could not be deleted as they have data associated.');
      return redirect()->back();
    }

    public function destroy($domain,Request $request,$id)
    {
        $company_id = config('settings.company_id');
        $client = Client::find($request->id, ['clients.id']);
        $instance = Client::find($request->id, ['clients.id']);
        if ($client) {
            $handleTableData = $client->employees; 
            if (!getPartyActivity($client->id)) {
                session()->flash("success", "This client has been used and can't be deleted");
                return redirect()->back();
            }

            if((clone $handleTableData)->count()>0){
              DB::table('handles')->where('client_id', $client->id)->delete();
            }
            $deleted = $client->delete();
            if ($deleted) {
                $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $instance, "action" => "delete"));
            }
        }
        session()->flash('success', 'Client has been deleted.');
        return redirect()->back();
    }

    public function getStates($id)
    {
        $data = DB::table("states")->where("country_id", $id)->pluck("name", "id");
        return json_encode($data);
    }

    public function getcities($id)
    {
        $cities = DB::table("cities")->where("state_id", $id)->pluck("name", "id");
        return json_encode($cities);
    }

    public function getPhoneCode($id)
    {
        $country = DB::table("countries")->select('name', 'phonecode')->where("id", $id)->first();
        return $country->name . ',+' . $country->phonecode;
    }

    public function addEmployee(Request $request)
    {
        $company_id = config('settings.company_id');
        $employees = $request->employees;
        $client_id = $request->client_id;
        $map_type = 2;

        $employeeInstance = Employee::where('company_id', $company_id)->where('is_admin', 1)->pluck('id')->toArray();

        DB::table('handles')->where('company_id', $company_id)->where('client_id', $client_id)->delete();

        $client = Client::findOrFail($client_id);
        if (!empty($employees)) {
            foreach ($employees as $employee_id) {
                $handle = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $employee_id)->where('client_id', $client_id)->first();
                if (empty($handle)) {
                    $saved = DB::table('handles')->insert([
                        'company_id' => $company_id,
                        'employee_id' => $employee_id,
                        'client_id' => $client_id,
                        'map_type' => $map_type
                    ]);
                }
                unset($handle);

                $getSuperiors = $this->getAllEmployeeSuperior($employee_id, $superiors=[]);
                if (!empty($employeeInstance)) {
                    foreach ($employeeInstance as $adminId) {
                        if (!in_array($adminId, $getSuperiors)) {
                            array_push($getSuperiors, $adminId);
                        }
                    }
                }
                if (!(empty($getSuperiors))) {
                    foreach ($getSuperiors as $getSuperior) {
                        $suphandle = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $getSuperior)->where('client_id', $client_id)->first();
                        if (empty($suphandle)) {
                            DB::table('handles')->insert([
                            'company_id' => $company_id,
                            'employee_id' => $getSuperior,
                            'client_id' => $client_id,
                            'map_type' => 1,
                            ]);
                        }
                    }
                }
            }

            //sending push notification
            $client->employee_ids = implode(",", $employees);
            $sent = sendPushNotification_(getFBIDs($company_id), 5, null, array("data_type" => "client", "client" => $client, "action" => "client_view_update"));
        } else {
            $client->employee_ids = "";
            if (!empty($employeeInstance)) {
                foreach ($employeeInstance as $adminId) {
                    $adminHandle = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $adminId)->where('client_id', $client_id)->first();
                    if (empty($adminHandle)) {
                        DB::table('handles')->insert([
                  'company_id' => $company_id,
                  'employee_id' => $adminId,
                  'client_id' => $client_id,
                  'map_type' => 1,
                  ]);
                    }
                }
            }
            $sent = sendPushNotification_(getFBIDs($company_id), 5, null, array("data_type" => "client", "client" => $client, "action" => "client_view_update"));
        }
        $hiddenemployees = $request->hiddenemployees;
        if (!empty($hiddenemployees)) {
            foreach ($hiddenemployees as $employee_id) {
                $handle = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $employee_id)->where('client_id', $client_id)->first();
                if (empty($handle)) {
                    $saved = DB::table('handles')->insert([
                           'company_id' => $company_id,
                           'employee_id' => $employee_id,
                           'client_id' => $client_id,
                           'map_type' => $map_type
                       ]);
                }
                unset($handle);
            }
        }

        return redirect()->back();
    }

    public function addLinkEmployee(Request $request)
    {
        $company_id = config('settings.company_id');
        $employees = $request->employees;
        $client_id = $request->client_id;
        $map_type = 2;

        DB::table('accessibility_link')->where('company_id', $company_id)->where('client_id', $client_id)->delete();

        $client = Client::findOrFail($client_id);
        if (!empty($employees)) {
            foreach ($employees as $employee_id) {
                $handle = DB::table('accessibility_link')->where('company_id', $company_id)->where('employee_id', $employee_id)->where('client_id', $client_id)->first();
                if (empty($handle)) {
                    $saved = DB::table('accessibility_link')->insert([
                        'company_id' => $company_id,
                        'employee_id' => $employee_id,
                        'client_id' => $client_id,
                        'map_type' => $map_type
                    ]);
                }
                unset($handle);
            }

            //sending push notification
            $client->employee_ids = implode(",", $employees);
            $sent = sendPushNotification_(getFBIDs($company_id), 5, null, array("data_type" => "client", "client" => $client, "action" => "client_view_update_for_link_access"));
        } else {
            $client->employee_ids = "";
            $sent = sendPushNotification_(getFBIDs($company_id), 5, null, array("data_type" => "client", "client" => $client, "action" => "client_view_update_for_link_access"));
        }

        if (!Auth::user()->isCompanyManager()) {
            if (!empty($employees)) {
                $hiddenemployees = $request->hiddenemployees;
                if (!empty($hiddenemployees)) {
                    foreach ($hiddenemployees as $employee_id) {
                        $handle = DB::table('accessibility_link')->where('company_id', $company_id)->where('employee_id', $employee_id)->where('client_id', $client_id)->first();
                        if (empty($handle)) {
                            $saved = DB::table('accessibility_link')->insert([
                          'company_id' => $company_id,
                          'employee_id' => $employee_id,
                          'client_id' => $client_id,
                          'map_type' => $map_type
                      ]);
                        }
                        unset($handle);
                    }
                }
            }
        }

        return redirect()->back();
    }

    public function removeEmployee(Request $request)
    {
        $handle_id = $request->handle_id;
        $employeeID = $request->employee_id;
        $clientID = $request->client_id;

        $deleted = DB::table('handles')->where('id', $handle_id)->delete();

        $employee = Employee::findOrFail($employeeID);
        if ($employee && !empty($employee->firebase_token)) {
            $sent = sendPushNotification_([$employee->firebase_token], 5, null, array("action" => "remove", "client_id" => $clientID, "employee_id" => $employeeID));
        }
        
        return "Employee Removed";
    }

    public function ajaxBasicUpdate($domain, Request $request)
    {
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $client = Client::where('company_id',$company_id)->where('id',$request->client_id)->first();
        if ($client) {
            $validator= Validator::make($request->all(), [
                'company_name' => config('settings.allow_party_duplication') ==0 ? 'required|unique:clients,company_name,' . $request->client_id . ',id,company_id,' . $company_id . ',deleted_at,NULL' : 'required',
                'name' => 'required',
                'client_code' => 'sometimes|nullable|unique:clients,client_code,' . $request->client_id . ',id,company_id,' . $company_id . ',deleted_at,NULL',
            ]);

            if ($validator->fails()) {
                return response()->json(['code'=>201,'error'=>$validator->errors()->all()]);
            } else {
                if($request->confirmremove){
                  $client->image = null;
                  $client->image_path = null;
                }
                if ($request->file('image')) {
                  $vimage = Validator::make($request->all(),[
                    'image' => 'mimes:jpeg,png,jpg|max:2000',
                  ]);
                  if($vimage->fails()){
                    return response()->json(['code'=>201,'error'=>$vimage->errors()->all()]);
                  }
                  $image2 = $request->file('image');
                  $realname = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME);
                  $extension = $image2->getClientOriginalExtension();
                  $new_name = $realname . "-" . time() . '.' . $extension;
                  $image2->storeAs('public/uploads/' . $companyName . '/clients/', $new_name);
                  $path = Storage::url('app/public/uploads/' . $companyName . '/clients/' . $new_name);
                  $client->image = $new_name;
                  $client->image_path = $path;
                }
                $client->name=ucfirst($request->name);
                $client->company_name=$request->company_name;
                $client->client_code = $request->client_code;              
                $client->about = str_replace('&nbsp;', '', $request->get('about'));
                $client->save();
                $clientHandlingData = getClientHandlingData($company_id, $client->id, true);
                $encodedHandlingData = json_encode($clientHandlingData);
                $client->employee_ids = $encodedHandlingData;

                $country = getCountryName($client->country)->name;
                $client->country_name = empty($country) ? "" : $country;

                $state = getStateName($client->state);
                $client->state_name = empty($state) ? "" : $state->name;

                $city = getCityName($client->city);
                $client->city_name = empty($city) ? "" : $city;
                $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $client, "action" => "update"));
                return response()->json(['code'=>200,'success'=>"Updated Successfully",'clientData'=>$client]);
            }
        }
    }

    public function ajaxBusinessUpdate($domain, Request $request)
    {
        $company_id = config('settings.company_id');
        $partytypes = PartyType::where('company_id', $company_id)->pluck('name', 'id')->toArray();
        $client = Client::where('company_id',$company_id)->where('id',$request->client_id)->first();
        if ($client) {
          if(!empty($partytypes)){
            $validator= Validator::make($request->all(), [
                'client_type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['code'=>201,'error'=>$validator->errors()->all()]);
            }
          }
          if($request->client_type)
            $client->client_type = $request->client_type;
          // if($request->superior)
            $client->superior = $request->superior;
          if($request->business_id)
            $client->business_id = $request->business_id;
          $client->pan = $request->pan;
          $client->save();
          $clientHandlingData = getClientHandlingData($company_id, $client->id, true);
          $encodedHandlingData = json_encode($clientHandlingData);
          $client->employee_ids = $encodedHandlingData;

          $country = getCountryName($client->country)->name;
          $client->country_name = empty($country) ? "" : $country;

          $state = getStateName($client->state);
          $client->state_name = empty($state) ? "" : $state->name;

          $city = getCityName($client->city);
          $client->city_name = empty($city) ? "" : $city;
          $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $client, "action" => "update"));
          if($client->business_id!=NULL){
            $businessType = BusinessType::where('company_id',$company_id)->where('id',$client->business_id)->first();
            if($businessType) 
              $client->business_name = $businessType->business_name;
          }
          $partytypeRow = PartyType::where('company_id', $company_id)->where('id', $client->client_type)->first();
          if($partytypeRow)
          $client->partytype = $partytypeRow->name;
          $superiorRow = Client::where('company_id', $company_id)->where('id', $client->superior)->first();
          if ($superiorRow) {
              $client->superior_name = $superiorRow->company_name;
          }
          return response()->json(['code'=>200,'success'=>"Updated Successfully",'clientData'=>$client]);
        }
    }

    public function ajaxContactUpdate($domain, Request $request)
    {
        $company_id = config('settings.company_id');
        $partytypes = PartyType::where('company_id', $company_id)->pluck('name', 'id')->toArray();
        $client = Client::where('company_id',$company_id)->where('id',$request->client_id)->first();
        $regexphone="/^([0-9\s\-\+\/\(\)]*)$/|min:7|max:20";
        
        if ($client) {
          if($request->phone || $request->mobile){
            if($request->phone){
              $validator = Validator::make($request->all(), [
                  'phone' => 'sometimes|nullable',
              ]);      
              if($validator->fails()){
                return response()->json(['code'=>201,'error'=>$validator->errors()->all()]);
              }        
            }
            if($request->mobile){
              $validator = Validator::make($request->all(), [
                  // 'mobile' => 'sometimes|digits_between:7,14|unique:clients,phone,'.$request->client_id.',id,deleted_at,NULL,company_id,' . $company_id ,
                  'mobile' => 'sometimes|regex:'.$regexphone.'|unique:clients,mobile,'.$request->client_id.',id,deleted_at,NULL,company_id,' . $company_id,
              ]);
              if($validator->fails()){
                return response()->json(['code'=>201,'error'=>$validator->errors()->all()]);
              }
            }
          }
          if($request->email) {
              if ($client->email!=$request->email) {
                  $vemail = Validator::make($request->all(), [
                    'email' => 'sometimes|nullable|email|unique:clients,email,' . $request->client_id,
                  ]);
                  if ($vemail->fails()) {
                      return response()->json(['code'=>201,'error'=>$vemail->errors()->all()]);
                  }
              }
          }
          if($request->website){
            $regex = "/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/";
            $vwebsite = Validator::make($request->all(), [
                'website' => 'sometimes|nullable|regex:' . $regex,
            ]);
            if ($vwebsite->fails()) {
                return response()->json(['code'=>201,'error'=>$vwebsite->errors()->all()]);
            }
          }
           $special_char = array("!", "@", "$", "%", "|", ":", ";");
          $client->email=$request->email;
          $client->phone=str_replace($special_char,",",$request->phone);
          $client->mobile=$request->mobile;
          $client->website = $request->website;
          $client->save();
          $clientHandlingData = getClientHandlingData($company_id, $client->id, true);
          $encodedHandlingData = json_encode($clientHandlingData);
          $client->employee_ids = $encodedHandlingData;

          $country = getCountryName($client->country)->name;
          $client->country_name = empty($country) ? "" : $country;

          $state = getStateName($client->state);
          $client->state_name = empty($state) ? "" : $state->name;

          $city = getCityName($client->city);
          $client->city_name = empty($city) ? "" : $city;
          $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $client, "action" => "update"));
          return response()->json(['code'=>200,'success'=>"Updated Successfully",'clientData'=>$client]);
        }
    }

    public function ajaxLocationUpdate($domain, Request $request)
    {
        $company_id = config('settings.company_id');
        $partytypes = PartyType::where('company_id', $company_id)->pluck('name', 'id')->toArray();
        $client = Client::where('company_id',$company_id)->where('id',$request->client_id)->first();
        if ($client) {
          
          $validator = Validator::make($request->all(), [
              'country' => 'required',
          ]);

          if($validator->fails()){
            return response()->json(['code'=>201,'error'=>$validator->errors()->all()]);
          }
          $client->country = $request->country;
          $country = DB::table('countries')->where('id',$request->country)->first();
          if($country)
          $client->phonecode = $country->name . ',+' . $country->phonecode;
          $client->state = $request->state;
          $client->city = $request->city;
          $client->address_1 = $request->address_1;
          $client->address_2 = $request->address_2;
          $client->location = $request->location;
          $client->latitude = $request->lat;
          $client->longitude = $request->lng;
          if ($request->beat != "null") {
            $prevBeat =  DB::table('beat_client')->where('client_id', $client->id)->value('beat_id');
            if ((int)$request->beat!=$prevBeat) {
                $today = date('Y-m-d');
                $clientBeatsPlans = BeatVPlan::join('beatplansdetails', 'beatplansdetails.beatvplan_id', 'beatvplans.id')->where('beatvplans.company_id', $company_id)->select('beatvplans.id')->where('beatplansdetails.plandate', '>=', $today)->get();
                $clientBeatPlans = BeatPlansDetails::whereIn('beatvplan_id', $clientBeatsPlans)
                                              ->get();
                if ($clientBeatPlans->count()>0) {
                    foreach ($clientBeatPlans as $clientBeatPlan) {
                        $beatPlanBeats = explode(',', $clientBeatPlan->beat_id);
                        foreach ($beatPlanBeats as $beatPlanBeat) {
                            if ((int)$beatPlanBeat==$prevBeat) {
                                $_beatClients = json_decode($clientBeatPlan->beat_clients, true);
                                $_beats = explode(',', $clientBeatPlan->beat_id);
                                if(array_key_exists($prevBeat, $_beatClients)){
                                  if (count($_beatClients[$prevBeat])==1) {
                                      if (array_key_exists($prevBeat, $_beatClients)) {
                                          $_beatClients[$request->beat][] = $request->id;
                                          ;
                                      } else {
                                          $_beatClients[$request->beat] = $_beatClients[$prevBeat];
                                      }
                                      unset($_beatClients[$prevBeat]);
                                  } else {
                                      if (array_key_exists($request->beat, $_beatClients)) {
                                          $_beatClients[$request->beat][] = $request->id;
                                      } else {
                                          $_beatClients[$request->beat][] = $request->id;
                                      }
                                      $_ind = array_search($request->id, $_beatClients[$prevBeat]);
                                      unset($_beatClients[$prevBeat][$_ind]);
                                  }
                                  $newBeatIDs = array();
                                  foreach ($_beatClients as $key=>$beatClient) {
                                      array_push($newBeatIDs, $key);
                                  }
                                  $clientBeatPlan->beat_id = implode(',', $newBeatIDs);
                                  $clientBeatPlan->beat_clients = json_encode($_beatClients);
                                  $clientBeatPlan->save();
                                }
                            }
                        }
                    }
                }
            }
            if(config('settings.beat')==1){
              DB::table('beat_client')->where('client_id', $client->id)->delete();
              DB::table('beat_client')->insert([
                    'client_id' => $client->id,
                    'beat_id' => $request->beat,
              ]);
            }
          }
          $client->save();
          $clientHandlingData = getClientHandlingData($company_id, $client->id, true);
          $encodedHandlingData = json_encode($clientHandlingData);
          $client->employee_ids = $encodedHandlingData;

          $country = getCountryName($client->country)->name;
          $client->country_name = empty($country) ? "" : $country;

          $state = getStateName($client->state);
          $client->state_name = empty($state) ? "" : $state->name;

          $city = getCityName($client->city);
          $client->city_name = empty($city) ? "" : $city;
          $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $client, "action" => "update"));
          $beatRow = DB::table('beats')->where('id', $request->beat)->first();
          if ($beatRow) {
              $client->beat_name = $beatRow->name;
          }
          $client->client_country_name = ($client->country)?(getCountryName($client->country)?getCountryName($client->country)->name:"N/A"):"N/A";
          $client->client_city_name = ($client->city)?(getCityName($client->city)?getCityName($client->city):"N/A"):"N/A";
          $client->client_state_name = ($client->state)?(getStateName($client->state)?getStateName($client->state)->name:"N/A"):"N/A";
          return response()->json(['code'=>200,'success'=>"Updated Successfully",'clientData'=>$client]);
        }
    }

    public function ajaxAccountingUpdate($domain, Request $request)
    {
        $validator = Validator::make($request->all(), [
          'opening_balance' => 'sometimes|numeric|nullable',
          'credit_limit' => 'sometimes|numeric|nullable',
          'credit_days' => 'sometimes|numeric|nullable',
        ]);

        if($validator->fails()){
            return response()->json(['code'=>201,'error'=>$validator->errors()->all()]);
        }
        $company_id = config('settings.company_id');
        $client = Client::where('company_id',$company_id)->where('id',$request->client_id)->first();
        if ($client) {
          $client->opening_balance = $request->opening_balance;
          $client->credit_limit = $request->credit_limit;
          if($request->credit_days)
            $client->credit_days = $request->credit_days;
          $client->save();
          $clientHandlingData = getClientHandlingData($company_id, $client->id, true);
          $encodedHandlingData = json_encode($clientHandlingData);
          $client->employee_ids = $encodedHandlingData;

          $country = getCountryName($client->country)->name;
          $client->country_name = empty($country) ? "" : $country;

          $state = getStateName($client->state);
          $client->state_name = empty($state) ? "" : $state->name;

          $city = getCityName($client->city);
          $client->city_name = empty($city) ? "" : $city;
          $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $client, "action" => "update"));
          return response()->json(['code'=>200,'success'=>"Updated Successfully",'clientData'=>$client]);
        }
    }

    public function ajaxMiscellaneousUpdate($domain, Request $request)
    {
        $company_id = config('settings.company_id');
        $client = Client::where('company_id',$company_id)->where('id',$request->client_id)->first();
        if ($client) {
          $client->status = $request->status; 
          if(config('settings.party_wise_rate_setup')==1){
            $client->rate_id = $request->rate_id?$request->rate_id:NULL; 
          }

          $client->save();
          $category_rates = null;
          if($request->has('category_rates') && config('settings.category_wise_rate_setup') == 1){
            ClientCategoryRateType::whereClientId($client->id)->delete();
            $category_rates = $request->category_rates;
            if(count($category_rates) > 0){
              $records = array();
              foreach($category_rates as $category_rate){
                array_push($records, array('client_id' => $client->id, 'category_rate_type_id' => $category_rate));
              }
              ClientCategoryRateType::insert($records);
            }
            $client->appliedcategoryrates = $client->appliedcategoryrates()->get(['id', 'category_id', 'name'])->pluck('id')->toJson();
            $category_rates = $client->appliedcategoryrates()->get(['id', 'category_id', 'name'])->pluck('name', 'id')->toJson();
          }
          
          $client->rate_id = $client->rate_id;
          $clientHandlingData = getClientHandlingData($company_id, $client->id, true);
          $encodedHandlingData = json_encode($clientHandlingData);
          $client->employee_ids = $encodedHandlingData;

          $country = getCountryName($client->country)->name;
          $client->country_name = empty($country) ? "" : $country;

          $state = getStateName($client->state);
          $client->state_name = empty($state) ? "" : $state->name;

          $city = getCityName($client->city);
          $client->city_name = empty($city) ? "" : $city; 
          $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $client, "action" => "update"));
          $client->rate_name = $client->rate_id?$client->rates->name:"Default";
          return response()->json(['code'=>200,'success'=>"Updated Successfully",'clientData'=>$client, 'categoryRates' => $category_rates]);
        }
    }
    

    public function ajaxCustomFieldUpdate($domain, Request $request)
    {
        $company_id = config('settings.company_id');
        $client = Client::where('company_id',$company_id)->where('id',$request->client_id)->first();
        if ($client) {
            $party_meta = DB::table('party_meta')->where('client_id',$client->id)->first();
            if($party_meta){
                $collec = CustomField::where('company_id',$company_id)->where('status',1)->where('for','=', 'Party')->withTrashed()->get();
                $imagesFilesCollections = $collec->where('type','Multiple Images');
                foreach($imagesFilesCollections as $imageCollection){
                    if(!$request[$imageCollection->slug]){
                        $request->merge([
                            $imageCollection->slug => true,
                        ]);
                    }
                }

                $imagesFilesCollections = $collec->where('type','File');
                foreach($imagesFilesCollections as $imageCollection){
                    if(!$request[$imageCollection->slug]){
                        $request->merge([
                            $imageCollection->slug => true,
                        ]);
                    }
                }
                $array_field = (new  CustomCheck($collec, $request,$party_meta))->check();
                if(isset($array_field) && !empty($array_field)):
                  DB::table('party_meta')->where('client_id',$client->id)->update(['cf_value'=>json_encode($array_field)]);
                endif;
            }else{
                $collec = CustomField::where('for','=', 'Party')->where('company_id',$company_id)->get();

                if($collec->count()):
                    $array_field = (new  CustomCheck($collec, $request))->check();
                endif;

                if(isset($array_field) && !empty($array_field)):
                    DB::table('party_meta')->insert([
                        'client_id' => $client->id,
                        'cf_value' => json_encode($array_field)
                    ]);
                endif;
            }
            $party_meta   = DB::table('party_meta')->where('client_id',$request->client_id)->first();
            $custom_fields = CustomField::where('company_id',$company_id)->where('status',1)->where('for','Party')->get();
            if($party_meta){
              $cf_value = (array)json_decode($party_meta->cf_value);
              $fieldIDs = array_keys($cf_value);
              foreach($custom_fields as $custom_field){
                if(in_array($custom_field->id,$fieldIDs)){
                  $custom_field->custom_value = $cf_value[$custom_field->id];
                }
              }
            }

            $data = [];

            foreach($custom_fields as $field):
                switch($field->type):
                        case "Monetary":                          
                              $currencies= Cache::rememberforever('currencies', function()
                              {
                                  return \App\Currency::orderBy('currency', 'ASC')->get()->unique('code');
                              });
                       
                              if(isset($field->custom_value)):
                               $arrayMonetory = explode(" ",$field->custom_value);  
                              endif;
                              foreach ($currencies as $currency):
                                if(isset($field->custom_value) && ($arrayMonetory[0]==$currency->id)): 
                                  $data[$field->slug]= $currency->code." ".$arrayMonetory[1];
                                endif;
                              endforeach;
                          break;
                        case 'Single option':
                          foreach (json_decode($field->options) as $item):
                              if ($item):
                                if($item==$field->custom_value)
                                  $data[$field->slug]=urldecode($item);
                              endif;
                          endforeach;

                          break;

                        case "User":
                            $users= \App\Employee::where('status','Active')->where('company_id',config('settings.company_id'))->orderBy('name', 'ASC')->get(['id', 'name']);
                            foreach ($users as $user):
                            if(isset($field->custom_value) && $field->custom_value==$user->id):
                              $data[$field->slug] = $user->name;
                            endif;
                            endforeach;
                          break;

                        case "Multiple options":
                            if(isset($field->custom_value)){
                                $arrayMultiple = json_decode($field->custom_value);
                                $data[$field->slug] = '';
                            }else{
                                $data[$field->slug] = 'N/A';
                            }
                            foreach (json_decode($field->options) as $item):
                                if ($item):
                                  if(isset($field->custom_value) && in_array($item,$arrayMultiple)):   
                                    $data[$field->slug] = $data[$field->slug]. urldecode($item).', ';
                                  endif; 
                                endif;
                            endforeach;
                          break;

                        case "Multiple Images":
                            if(isset($field->custom_value)){
                                $arrayMultiple = json_decode($field->custom_value);
                                $data[$field->slug]='';
                                $data[$field->slug.'-editedImages'] ='';
                                foreach($arrayMultiple as $key => $image){
                                  $data[$field->slug] = $data[$field->slug].'<div class="col-xs-6"><img style="width:100px;" src="'.asset('cms/').$image[0].'"><span style="display:block;width:100%;"><a href="'.asset('cms/').$image[0].'">'.$key.'</a></span><br></div>';
                                  $data[$field->slug.'-editedImages'] .= '<div class="col-xs-6"><img style="width:100px;" src="'.asset('cms/').$image[0].'"><span style="display:block;width:100%;">'.$key.'<span class="custom_image_remove" style="color:red;" data-action="'.$field->slug.'-deleted" data-field="'.$field->slug.'" data-name="'.$key.'"><i class="fa fa-trash"></i></span></span><br></div>';
                                }
                            }else{
                                $data[$field->slug] = "N/A";
                            }
                            break;

                        case "File":
                            if(isset($field->custom_value)){
                                $arrayMultiple = json_decode($field->custom_value);
                                $data[$field->slug] = '';
                                $data[$field->slug.'-editedFiles'] ='';
                                foreach($arrayMultiple as $key => $file){
                                  $data[$field->slug] .= '<span><a style="width:100px;" href="'.asset('cms/').$file[0].'">'.$key.'</a></span><br>';
                                  $data[$field->slug.'-editedFiles'] .='<div class="col-xs-12"><span><a style="width:100px;" href="'.asset('cms/').$file[0].'">'.$key.'</a><span class="custom_image_remove" style="color:red;" data-action="'.$field->slug.'-deleted" data-field="'.$field->slug.'" data-name="'.$key.'"><i class="fa fa-trash"></i></span></span></div>';
                                }
                            }else{
                                $data[$field->slug] = "N/A";
                            }
                            break;

                        default:
                          $data[$field->slug] = ($field->custom_value)?$field->custom_value:'N/A';
                          break;

                      endswitch;

            endforeach;

            $clientHandlingData = getClientHandlingData($company_id, $client->id, true);
            $encodedHandlingData = json_encode($clientHandlingData);
            $client->employee_ids = $encodedHandlingData;
          $sent = sendPushNotification_(getFBIDs($company_id), 10, null, array("data_type" => "client", "client" => $client, "action" => "update"));
          return response()->json(['code'=>200,'success'=>"Updated Successfully",'fieldData'=>$data]);
        }
    }

    public function getPartyTypeClients(Request $request)
    {
        $company_id = config('settings.company_id');
        $clients = Client::with('partytypes')->where('company_id', $company_id)->where('superior', $request->client_id)->where('client_type', $request->party_type)->orderBy('created_at', 'desc')->get();
        foreach ($clients as $client) {
            if (hasChild($client->id)) {
                $client->childs = true;
            } else {
                $client->childs = false;
            }
            if (getPartyActivity($client->id)) {
                $client->partyactivity = false;
            } else {
                $client->partyactivity = true;
            }
            $permName = str_replace(' ','-',$client->partytypes->name);
            $client->canview = Auth::user()->can($permName.'-view');
            $client->canedit = Auth::user()->can($permName.'-update');
            $client->candelete = Auth::user()->can($permName.'-delete');
            $client->canstatus = Auth::user()->can($permName.'-status');
        }
        $data['count'] = $clients->count();
        $data['clients'] = $clients->toArray();
        return $data;
    }

    private function getSubClients($client_id, $subClient)
    {
        $company_id = config('settings.company_id');
        $subclients = Client::where('company_id', $company_id)->where('superior', $client_id)->groupBy('client_type')->get();
        foreach ($subclients as $subclient) {
            $subClient[$subclient->client_type]['id'][]= $subclient->id;
            $subClient[$subclient->client_type]['company_name'][]= $subclient->company_name;
            $superior = Client::where('company_id', $company_id)->where('id', $subclient->superior)->first();
            $subClient[$subclient->client_type]['superior'][] = $superior->company_name;
            $subclientss = Client::where('company_id', $company_id)->select('id', 'client_type')->where('superior', $subclient->id)->groupBy('client_type')->get();
            if ($subclientss->count()>0) {
                $subClient = $this->getSubClients($subclient->id, $subClient);
            }
        }
        return $subClient;
    }

    public function fetchcitywisebeats(Request $request){
      $company_id = config('settings.company_id');
      if(isset($request->selCity)){
        $beats =  Beat::where('company_id', $company_id)->where('city_id', $request->selCity)->orWhere(function($query) use ($company_id){
          $query->where('company_id', $company_id)->whereNull('city_id');
        })->orderBy('name', 'asc')->get(['name', 'id']);
      }else{
        $beats =  Beat::where('company_id', $company_id)->orderBy('name', 'asc')->get(['name', 'id']);
      }

      return json_encode($beats);
    }

    public function updateCreditDays($domain,Request $request)
    {
      $company_id = config('settings.company_id');
      $clientSetting = ClientSetting::where('company_id',$company_id)->first();
      if($clientSetting){
          $clientSetting->credit_days = $request->days;
          $clientSetting->save();
          $clientSetting = ClientSetting::select('credit_days')->where('company_id',$company_id)->first();
        return response(['status'=>true,'message'=>'Credit Days Updated Successfully','data'=>$clientSetting->credit_days]);
      }
      return response(['status'=>false,'message'=>'Credit Days Updating Failed']);
    }

    public function getClientOrderCreditDetails($domain, Request $request) {
        $data = array();
        $i = 1;
        // $creditDays = Client::find($request->id)->credit_days ?? config('settings.credit_days');
        $orders = Order::clientId($request->id)->orderBy('order_date', 'asc')->get();
        foreach($orders as $order) {
            $nestedData['id'] = $i;
            $nestedData['orderNo'] = config('settings.order_prefix').$order->order_no;
            $nestedData['orderAmount'] = $order->grand_total;
            $nestedData['orderDate'] = Carbon::parse($order->order_date)->format('d M, Y');
            $nestedData['dueDate'] = Carbon::parse($order->due_date)->format('d M, Y');

            $i++;
            $data[] = $nestedData;
        }

        $respone = [
            'data' => $data
        ];
        echo json_encode($respone);
    }

    public function getClientPaymentCreditDetails($domain, Request $request) {
        $data = array();
        $company_id = config('settings.company_id');
        // $orders = Order::clientId($request->id)->orderBy('order_date', 'asc')->get();
        $orders = Order::select('orders.id','orders.company_id','orders.order_no','due_date','orders.delivery_status_id','grand_total','module_attributes.id as module_attributes_id','module_attributes.order_amt_flag')
                    ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
                    ->where('orders.company_id',$company_id)
                    ->where('client_id',$request->id)
                    ->where('order_amt_flag',1)
                    ->orderBy('due_date','ASC')->get();
        $collectionSum = Collection::clientId($request->id)->paymentStatus()->sum('payment_received');
        $i=1;
        foreach($orders as $order) {
            if($collectionSum > 0) {
                if ($collectionSum >= $order->grand_total) {
                    $nestedData['payment'] = "P-".$i;
                    $nestedData['amount'] = $order->grand_total;
                    $nestedData['orderNo'] = config('settings.order_prefix').$order->order_no;
                } else if($collectionSum != 0 && $collectionSum < $order->grand_total) {
                    $nestedData['payment'] = "P-".$i;
                    $nestedData['amount'] = round($collectionSum, 2);
                    $nestedData['orderNo'] = config('settings.order_prefix').$order->order_no;
                } else {
                    $nestedData = [];
                }
            } else {
                $nestedData = [];
            }
            $collectionSum -= $order->grand_total;
            if (empty($nestedData)) {
                //
            } else {
                $data[] = $nestedData;
            }
        }
        $respone = [
            'data' => $data
        ];

        echo json_encode($respone);
    }

    public function getOrderOnCreditDays(Request $request) {
        $data = array();
        $company_id = config('settings.company_id');
        $today = Carbon::now();
        // $orders = Order::clientId($request->id)->orderBy('order_date', 'asc')->get();
        $orders = Order::select('orders.id','orders.company_id','orders.order_no','due_date','orders.delivery_status_id','grand_total','module_attributes.id as module_attributes_id','module_attributes.order_amt_flag')
                    ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
                    ->where('orders.company_id',$company_id)
                    ->where('client_id',$request->id)
                    ->where('order_amt_flag',1)
                    ->where('due_date','<',$today->format('Y-m-d'))
                    ->orderBy('due_date','ASC')->get();
        $collectionSum = Collection::clientId($request->id)->paymentStatus()->sum('payment_received'); 

        foreach($orders as $order) {
            $orderDate = Carbon::parse($order->order_date);
            $dueDate = Carbon::parse($order->due_date);
            $todayDate = Carbon::now();
            if($dueDate->lte($todayDate)) {
                if($collectionSum >= $order->grand_total) {
                    $nestedData = [];
                } else if($collectionSum < 0) {
                    $nestedData['orderNo'] = config('settings.order_prefix').$order->order_no;
                    $nestedData['orderAmount'] = number_format($order->grand_total, 2);
                    $nestedData['dueDays'] = $dueDate->diffInDays($todayDate);
                } else {
                    $nestedData['orderNo'] = config('settings.order_prefix').$order->order_no;
                    $nestedData['orderAmount'] = round($order->grand_total - $collectionSum, 2);
                    $nestedData['dueDays'] = $dueDate->diffInDays($todayDate);
                }                
            } else {
                $nestedData = [];
            }
            $collectionSum -= $order->grand_total;
            if (empty($nestedData)) {
                //
            } else {
                $data[] = $nestedData;
            }
        }

        $respone = [
            'data' => $data
        ];

        echo json_encode($respone);
    }

    public function getUpcomingPaymentDetails(Request $request) {
        $data = array();
        $company_id = config('settings.company_id');
        $creditDays = Client::find($request->id)->credit_days ?? config('settings.credit_days');
        $today = Carbon::now()->addDays($creditDays);
        // $orders = Order::clientId($request->id)->orderBy('order_date', 'asc')->get();
        $orders = Order::select('orders.id','orders.order_no','orders.company_id','due_date','orders.delivery_status_id','grand_total','module_attributes.id as module_attributes_id','module_attributes.order_amt_flag')
                    ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
                    ->where('orders.company_id',$company_id)
                    ->where('due_date','<=',$today->format('Y-m-d'))
                    ->where('client_id',$request->id)
                    ->where('order_amt_flag',1)
                    ->orderBy('due_date','ASC')->get();
        $collectionSum = Collection::clientId($request->id)->paymentStatus()->sum('payment_received'); 

        foreach($orders as $order) {
            if($collectionSum >= $order->grand_total) {
                $nestedData = [];
            } else if($collectionSum < 0) {
                $nestedData['orderNo'] = config('settings.order_prefix').$order->order_no;
                $nestedData['amount'] = number_format($order->grand_total, 2);
                $nestedData['date'] = Carbon::parse($order->due_date)->format('d M, Y');
            } else {
                $nestedData['orderNo'] = config('settings.order_prefix').$order->order_no;
                $grandTotal = round($order->grand_total - $collectionSum, 2);
                $nestedData['amount'] = number_format($grandTotal, 2);
                $nestedData['date'] = Carbon::parse($order->due_date)->format('d M, Y');
            }                
            $collectionSum -= $order->grand_total;
            if (empty($nestedData)) {
                //
            } else {
                $data[] = $nestedData;
            }
        }

        $respone = [
            'data' => $data
        ];

        echo json_encode($respone);
    }

    public function clientVisitTable(Request $request)
    {
        $columns = array( 'id', 'date', 'time', 'duration', 'employee_name', 'action' );

        $company_id = config('settings.company_id');
        $clientVal = $request->clientID;
        $search = $request->input('search')['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $order = $columns[$request->input('order.0.column')];
        if($order=='employee_name'){
          $order = "employees.name";
        }elseif($order=='time'){
          $order = "start_time";
        }elseif($order=='id'){
          $order = "client_visits.id";
        }elseif($order == "duration"){
          $order = \DB::raw('DATEDIFF(MINUTE,  client_visits.start_time, client_visits.end_time) AS MinuteDiff');
        }
        $dir = $request->input('order.0.dir');

        $prepQuery = ClientVisit::where('client_visits.company_id', $company_id)
                      ->whereClientId($clientVal)->whereNull('client_visits.deleted_at')
                      ->leftJoin('employees', 'employees.id', 'client_visits.employee_id');

        if (!empty($search)) {
            $searchQuery = $prepQuery;
            $prepQuery = $searchQuery->where(function ($query) use ($search) {
                $query->orWhere('employees.name', 'LIKE', "%{$search}%");
                $query->orWhere('start_time', 'LIKE', "%{$search}%");
                $query->orWhere('end_time', 'LIKE', "%{$search}%");
            });
        }

        $totalData =  $prepQuery->count();
        if($limit==-1) $limit = $totalData;
        $totalFiltered = $totalData;
      
        $data = array();
        if($order=='date'){
          $prepQuery = $prepQuery->orderBy('client_visits.id', 'desc');
        }
        $clientvisits = $prepQuery->orderBy($order, $dir)->offset($start)
                            ->limit($limit)
                            ->get([
                              'client_visits.id as visit_id',
                              'date',
                              'start_time',
                              'end_time',
                              'employees.name',
                              'employees.id as empId',
                            ]);
        
        if (!empty($clientvisits)) {
            $i = $start;
            $viewable_employees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();

            foreach ($clientvisits as $clientvisit) {
                $id = $clientvisit->visit_id;
                $clientvisit_date = $clientvisit->date;
                $time = date('H:i A', strtotime($clientvisit->start_time)) . '-' . date('H:i A', strtotime($clientvisit->end_time));
                $start_time = new DateTime($clientvisit->start_time);
                $end_time = new DateTime($clientvisit->end_time);
                $interval = $end_time->diff($start_time);
                $hr = $interval->h;
                $min = $interval->i;
                $sec = $interval->s;
                $duration = $this->getTotalDuration($hr, $min, $sec);
                $total_duration = $duration?$duration:0 ." second";
                $employee_name = $clientvisit->name; 
                $employee_show = in_array($clientvisit->empId, $viewable_employees)?domain_route('company.admin.employee.show',[$clientvisit->empId]):null;
                
                $nestedData['id'] = ++$i;
                $nestedData['date'] = getDeltaDate($clientvisit_date);
                $nestedData['time'] = $time;
                $nestedData['duration'] = $total_duration;
                $nestedData['employee_name'] =  "<a href='{$employee_show}' data-viewable='{$employee_show}' class='empLinks'>{$clientvisit->name}</a>";

                $detail = domain_route('company.admin.clients.clientVisitDetail', ['id'=>$clientVal, 'visit_id'=> $id]);
                $nestedData['action'] = "<a href='{$detail}' class='btn btn-success btn-sm' style='color: #05c16b!important;padding: 3px 6px;border: none;background-color: #05c16b00!important;'><i class='fa fa-eye'></i></a>";
            
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

    private function getTotalDuration($hr, $min, $sec){
      try{
        if($sec>60){
          $secToMin = (int)($sec/60);
          $min += $secToMin;
          $sec = (int)($sec%60);
        }

        if($min>60) {
          $minToHr = (int)($min/60);
          $hr += $minToHr;
          $min = (int)($min%60);
        }
        $hr_text = $hr > 1 ? " Hours" : " Hour";
        $min_text = $min > 1 ? " Minutes" : " Minute";
        $sec_text = $sec > 1 ? " Seconds" : " Second";
        $hr_duration = $hr > 0 ? $hr . $hr_text : "";
        $min_duration = $min > 0 ? $min . $min_text : "";
        $sec_duration = $sec > 0 ? $sec . $sec_text : "";
        $total_duration = ($hr_duration?$hr_duration." ":$hr_duration).$min_duration;
        if(!$hr_duration) $total_duration = ($min_duration?$min_duration." ":$min_duration).$sec_duration;

        return $total_duration;
      }catch(\Exception $e){
        Log::error($e->getFile());
        Log::error($e->getMessage());
        Log::error($e->getLine());
        
        return "";
      }
    }

    public function clientVisitDetail(Request $request)
    {
      $data = $this->getVisitData($request);

      $gps_path = $this->getWorkedHourDetails($this->company_id, $data['clientVisits']['employee_id'], $data['clientVisits']['date'], $data['clientVisits']['clean_start_time'], $data['clientVisits']['clean_end_time']);
      $client_location = $this->getPartyLocation($this->company_id, $data['client_id']);

      $data['gps_path'] = $gps_path;
      $data['client_location'] = $client_location;
      
      return view('company.clients.partials_show.client-visit-detail')->with($data);
    }

    private function getVisitData($request){
      $company_id = $this->company_id;
      $id = $request->visit_id;
      $clientId = $request->id;
      $hr = 0;
      $min = 0;
      
      $clientVisits = ClientVisit::where('id', $id)->with(['employee' => function($query){
                      return $query->select('employees.name', 'employees.id');
                    }])->with(['client' => function($query){
                      return $query->select('clients.company_name', 'clients.id');
                    }])->with(['visitpurpose' => function($query){
                      return $query->select('visit_purposes.title', 'visit_purposes.id');
                    }])->with('images')->whereNull('client_visits.deleted_at')->first();
      $formattedClientVisits = $this->formatClientVisit($clientVisits);
      // $total_duration = "";
      // foreach($formattedClientVisits as $formatted_data){
      //   $hr += $formatted_data['duration']['hr'];
      //   $min += $formatted_data['duration']['min'];
      //   $total_duration = $this->getTotalDuration($hr, $min);
      // }
                      
      // $action = NULL;
      // $download = domain_route('company.admin.employee.empClientVisitDetailDownload', ['id' => $clientId, 'date' => $date]);
      
      // $action  = '<a class="btn btn-default btn-sm" href="'.$download.'" style="padding: 7px 6px;margin-right: 5px;"><i class="fa fa-book"></i>PDF</a>';
          
      // $action  = $action.'<button class="btn btn-default btn-sm" href="#" onclick="print();" style="padding: 7px 6px;"><i class="fa fa-print"></i>Print</button>';
      // whereCompanyId($company_id)->whereClientId($clientId)
      $date = $formattedClientVisits['date'];
      $data = [
        // 'action' => $action,
        // 'checkin' => $checkin, 
        // 'checkout' => $checkout, 
        'date' => $date, 
        'client_id' => $clientId, 
        'clientVisits' => $formattedClientVisits, 
        'client_name' => $formattedClientVisits['client_name'], 
        // 'total_duration' => $total_duration, 
      ];

      return $data;
    }

    private function formatClientVisit($object){
      try{
        $start_time = new DateTime($object->start_time);
        $end_time = new DateTime($object->end_time);
        $interval = $end_time->diff($start_time);
        $hr = $interval->h;
        $min = $interval->i;
        $sec = $interval->s;
        $total_duration = $this->getTotalDuration($hr, $min, $sec);
        
        $formatted_data = [
          'id' => $object->id,
          'client_id' => $object->client_id,
          'client_name' => $object->client->company_name,
          'employee_id' => $object->employee_id,
          'employee_name' => $object->employee->name,
          'visit_purpose_id' => $object->visitpurpose?$object->visitpurpose->id:null,
          'visit_purpose' => $object->visitpurpose?$object->visitpurpose->title:null,
          "date" => $object->date,
          "start_time" => date("h:i A", strtotime($object->start_time)),
          "clean_start_time" => $object->start_time,
          "end_time" => date("h:i A", strtotime($object->end_time)),
          "clean_end_time" => $object->end_time,
          "duration" => array("hr" => $hr, "min" => $min),
          "total_duration" => $total_duration?$total_duration : 0 . " second", 
          "comments" => $object->comments,
          'images' => $object->images->map(function($image) {
                        return $this->formatImages($image);
                      }),
        ];
        return $formatted_data;
      }catch(\Exception $e){
        Log::error(array("Format Client Visit Purpose () => "), array($e->getMessage()));
        Log::error($e->getFile());
        Log::error($e->getMessage());
        Log::error($e->getLine());
        
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
        Log::error($e->getFile());
        Log::error($e->getMessage());
        Log::error($e->getLine());
        return null;
      }
    }

    public function customPdfExport(Request $request){
      $getExportData = json_decode($request->exportedData)->data;
      $pageTitle = $request->pageTitle;
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      set_time_limit ( 300 );
      $pdf = PDF::loadView('company.client-visits.exportpdf', compact('getExportData', 'pageTitle', 'columns', 'properties'))->setPaper('a4', 'portrait');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }


    public function getWorkedHourDetails_old($company_id, $empId, $date, $startTime, $endTime){
        
      $nearestCheckIn = Attendance::whereCompanyId($this->company_id)
                        ->whereEmployeeId($empId)->where('adate', $date)->where('check_type', 1)
                        ->where('atime', '<=', $startTime)->orderBy('atime', 'desc')
                        ->first();
      
      $nearestCheckOut = Attendance::whereCompanyId($this->company_id)
                        ->whereEmployeeId($empId)->where('adate', $date)->where('check_type', 2)
                        ->where('atime', '>=', $endTime)->orderBy('atime', 'asc')
                        ->first();
      if($nearestCheckIn) $time1 = $nearestCheckIn->atime;
      if($nearestCheckOut) $time2 = $nearestCheckOut->atime;
      else $time2 = "23:59:59";

      $attendances = $this->getAllEmployeeAttendanceTime($company_id, $empId, $date, $time1, $time2);

      $fileName = getFileName($company_id, $empId, $date);

      try{
        $newlocations = array();
        $newLocationHasValue = false;
        $i = 0;
        
        foreach ($attendances as $attendance) {
        
          $time1 = strtotime($attendance['checkin']) * 1000;
          $time2 = strtotime($attendance['checkout']) * 1000;
          if($time1 == $time2){
            $maxDatetime = $date . " " . "23:59:59";
            $time2 = strtotime($maxDatetime) * 1000;
          }
                  
          $locations = getFileLocationWithRange($fileName, $time1, $time2, 60);
          
          if (empty($locations)) {
            $i++;
            continue;
          }
          if(count($locations) > 1){
            $params = array("raw_data" => json_encode($locations));
          }
          
          if (!empty($locations) && !$newLocationHasValue) $newLocationHasValue = true;
          if(!empty($locations)){
            foreach($locations as $location){
              array_push($newlocations, $location);
            }
          }

          $i++;
        }
                
        $encodedLocations = json_encode($newlocations);

        return $encodedLocations;
      }catch(\Exception $e){
        Log::info($e->getMessage());
        Log::info($e->getLine());
        Log::info($e->getFile());

        return json_encode(array());
      }
      
      // $finalArray = array();
      // try{
      //   foreach ($attendances as $key => $attendance) {

      //     $tempArray = array();

      //     $cinDatetime =  $attendance['checkin'];
      //     $coutDatetime =  $attendance['checkout'];

      //     $dateTime1 = strtotime($cinDatetime);
      //     $dateTime2 = strtotime($coutDatetime);

      //     $cinTime = substr($cinDatetime, 11);
      //     $coutTime = substr($coutDatetime, 11);

      //     $time1 = $dateTime1 * 1000;
      //     $time2 = $dateTime2 * 1000;
      //     $locations = getFileLocationWithRange($fileName, $time1, $time2, 60);

      //     if (!empty($locations) && count($locations) > 1) {
      //       $params = array(
      //         "raw_data" => json_encode($locations)
      //       );
      //     }
      //     $tempArray["checkin"] = $time1;
      //     $tempArray["checkout"] = $time2;
      //     $tempArray["cin_time"] = $cinTime;
      //     $tempArray["cout_time"] = $coutTime;
      //     $tempArray["locations"] = $locations;
      //     array_push($finalArray,$tempArray);
      //   }
      // }catch(\Exception $e){
      //   Log::info($e->getMessage());
      //   Log::info($e->getLine());
      //   Log::info($e->getFile());
      // }
      // $encodedLocations = json_encode($finalArray);
      // return $encodedLocations;
    }
    
    public function getWorkedHourDetails($company_id, $empId, $date, $start_time, $end_time){
      
      $time1 = strtotime($date." ".$start_time) * 1000;
      $time2 = strtotime($date." ".$end_time) * 1000;
      $fileName = getFileName($this->company_id, $empId, $date);
      if($fileName) $locations = getFileLocationWithRange($fileName, $time1, $time2);
      else $locations = array();

      $encodedLocations = json_encode($locations);

      return $encodedLocations;
    }

    private function getAllEmployeeAttendanceTime($company_id, $empId, $date, $checkIn, $checkOut){
      $emp_date_attendance = Attendance::whereCompanyId($company_id)
                              ->whereEmployeeId($empId)
                              ->where('check_datetime', '>=', $date.' '.$checkIn)
                              ->where('check_datetime', '<=', $date.' '.$checkOut)
                              ->orderBy('check_datetime', 'asc')
                              ->get(['employee_id', 'check_datetime', 'check_type'])->toArray();
      
      $newatten = array();
      $i = 0;

      try{
        foreach ($emp_date_attendance as $attendance) {
          if ($attendance['check_type'] == 1) {
            $newatten[$i]['checkin'] = $attendance['check_datetime'];
          }
          if ($attendance['check_type'] == 2 && !empty($newatten[$i]['checkin'])) {
            $newatten[$i]['checkout'] = $attendance['check_datetime'];
            $newatten[$i]['checkout_state'] = 'complete';
            $i++;
          }

          if (empty($newatten[$i]['checkout']) && !empty($newatten[$i]['checkin'])) {
            if (date('Y-m-d') == $date) {
              $newatten[$i]['checkout_state'] = 'current';
              $newatten[$i]['checkout'] = $attendance['check_datetime'];
            } else {
              $newatten[$i]['checkout'] = $date . ' 23:59:59';
            }
          }
        }
      }catch(\Exception $e){
        Log::info($e->getMessage());
        Log::info($e->getLine());
        Log::info($e->getFile());
      }

      return $newatten;
    }

    private function getPartyLocation($company_id, $client_id){
      
      $partyLocation = Client::select('id', 'company_name','latitude', 'longitude')
                        ->where('company_id', $company_id)
                        ->where('id', $client_id)
                        ->where('status', 'Active')
                        ->orderBy('id', 'asc')
                        ->first()->toArray();
      return $partyLocation;
    }
    /**
     * Create Folders 
     * */
    public function createUploadFolders(Request $request){
      $companyID = config('settings.company_id');
      $clientID = $request->id;
      $type = $request->type;

      $customMessages = [
        'folder_name.required' => 'Please specify name for folder.',
        'folder_name.unique' => 'Folder name already exists.',
      ];

      $this->validate($request, [
          "folder_name" => "required|unique:party_upload_folders,name,NULL,deleted_at,client_id,$clientID,type,$type",
      ],$customMessages);

      try{
        $folderName = $request->folder_name;
        $folder_instance = new PartyUploadFolder;
        $folder_instance->company_id = $companyID;
        $folder_instance->client_id = $clientID;
        $folder_instance->type = $type;
        $folder_instance->name = $folderName;
        $saved = $folder_instance->save();
        // $domain_directory = "cms/storage/app/public/uploads/".$domain."/invoices";
        // $urls = "cms/storage/app/public/uploads/".$domain."/invoices/".$pdfName;
        // if(!is_dir($domain_directory)){
        //   if(!is_dir("cms/storage/app/public/uploads/".$domain)) mkdir("cms/storage/app/public/uploads/".$domain);
        //   mkdir($domain_directory);
        // }
        $data = [
          'status' => 200,
          'message' => 'Folder Created successfully.',
          'data' => $folder_instance
        ];
        // $dataNotf = $this->formatUploads($folder_instance->with('files')->first());
        $instance = PartyUploadFolder::find($folder_instance->id);
        $dataNotf = $this->formatUploads($instance);
        

        $this->uploadNotification($dataNotf, 'add_folder', $companyID, $type, 'add');
      }catch(\Exception $e){
        Log::info($e->getMessage());
        Log::info($e->getCode());
        $data = [
          'status' => 404,
          'message' => 'Folder cannot be created.',
          'data' => $saved
        ];
      }

      return response()->json($data);
      
    }
    /**
     * Update Folders 
     * */
    public function updateUploadFolders(Request $request){
      $companyID = config('settings.company_id');
      $clientID = $request->id;
      $type = $request->type;
      $folderId = $request->folderId;

      $customMessages = [
        'folder_name.required' => 'Please specify name for folder.',
        'folder_name.unique' => 'Folder name already exists.',
      ];

      $this->validate($request, [
          "folder_name" => "required|unique:party_upload_folders,name,$folderId,id,deleted_at,NULL,client_id,$clientID,type,$type",
      ],$customMessages);

      try{
        $folder_instance = PartyUploadFolder::find($folderId);
        $folderName = $request->folder_name;
        $folder_instance->company_id = $companyID;
        $folder_instance->client_id = $clientID;
        $folder_instance->type = $type;
        $folder_instance->name = $folderName;
        $saved = $folder_instance->save();
        // $domain_directory = "cms/storage/app/public/uploads/".$domain."/invoices";
        // $urls = "cms/storage/app/public/uploads/".$domain."/invoices/".$pdfName;
        // if(!is_dir($domain_directory)){
        //   if(!is_dir("cms/storage/app/public/uploads/".$domain)) mkdir("cms/storage/app/public/uploads/".$domain);
        //   mkdir($domain_directory);
        // }
        $data = [
          'status' => 200,
          'message' => 'Folder Updated successfully.',
          'data' => $folder_instance
        ];

        // $dataNotf = $this->formatUploads($folder_instance->with('files')->first());
        $instance = PartyUploadFolder::find($folderId);
        $dataNotf = $this->formatUploads($instance);
        $this->uploadNotification($dataNotf, 'update_folder', $companyID, $type, 'update');

      }catch(\Exception $e){
        Log::info($e->getMessage());
        Log::info($e->getCode());
        $data = [
          'status' => 404,
          'message' => 'Folder cannot be created.',
          'data' => $saved
        ];
      }

      return response()->json($data);
      
    }
    /**
     * Delete Folders 
     * */
    public function deleteUploadFolders(Request $request){
      $companyID = config('settings.company_id');
      $clientID = $request->id;
      $type = $request->type;
      $folderId = $request->folderId;

      $customMessages = [
        'folderId.required' => 'Missing required parameters.',
      ];

      $this->validate($request, [
          "folderId" => "required",
      ],$customMessages);

      try{
        $folder_instance = PartyUploadFolder::find($folderId);
        $hasFiles = $folder_instance->files;
        if($hasFiles->first()){
          $companyName = Auth::user()->companyName($companyID)->domain;
          $client = Client::find($clientID);
          if($client){
            $client_name = $client->company_name;
          }else{
            return response()->json([
              'data' => null, 
              'status' => 403,
              'message' => 'Party doesn\'t exists',
              'type' => $type
            ]);
          }
          $aws_upload_folder = env('AWS_UPLOADS');
          $upload_folder = $aws_upload_folder. '/' . $companyName . '/' . $client_name . '/' . $type . '/folder_id_' . $folderId; 
          $exists_folder = Storage::disk('s3')->exists($upload_folder);
          if($exists_folder){
            Storage::disk('s3')->deleteDirectory($upload_folder);
          }
          $folder_instance->files()->delete();
        }
        $folder_instance->delete();
        $data = [
          'status' => 200,
          'message' => 'Folder Deleted successfully.',
          'data' => $folder_instance
        ];

        $this->uploadNotification($folder_instance, 'delete_folder', $companyID, $type, 'delete');
      }catch(\Exception $e){
        Log::info($e->getMessage());
        Log::info($e->getCode());
        $data = [
          'status' => 404,
          'message' => 'Folder cannot be deleted.',
          'data' => $e->getMessage()
        ];
      }

      return response()->json($data);
      
    }
    /**
     * Folder HTML View
     * Images and Files
     */
    public function getFolderDetails(Request $request){
      $folder_id = $request->folderId;
      $type =  $request->type;
      $client_id = $request->id;

      $folder = PartyUploadFolder::find($folder_id);
      $file_count = $folder->files->count();
      if($type=="files") $view = 'company.clients.partials_show.fileview';
      else $view = 'company.clients.partials_show.imageview';
      $make_view = view($view)
                        ->with('folder_id', $folder->id)
                        ->with('folder_title', $folder->name)
                        ->with('type', $type)->with('client_id', $client_id)
                        ->with('file_count', $file_count);
      
      return response()->json([
        'data' => null, 
        'view' => $make_view->render(),
        'status' => 200,
        'message' => 'success',
        'type' => $type
      ]);
    }
    /** 
     * Render Folder View when going back from uploads view
     */
    public function getFolderView(Request $request){
      $type =  $request->type;
      $client = Client::find($request->id);
      $title = $type == "files"? "File" : "Image";
      
      $file_folders = PartyUploadFolder::whereCompanyId($client->company_id)->wherehas('files')->where('type', $type)->orderby('id', 'desc')->pluck('id')->toArray();
      $total_size = PartyUpload::whereIn('party_upload_folder_id', $file_folders)->sum('file_size');
      $total_size = $total_size > 0? round(($total_size / 1073741824),2) : $total_size; 
      
      if($type == "images" && $request->firstLoad){
        $total_upload_allowed = config('settings.total_image_size_gb');
        $columns = array(
          'id',
          'folder_name',
          'last_modified',
          'added_by',
          'action',
        );
        $start = $request->input('start');
        $limit = $request->input('length');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        $prepQuery = PartyUploadFolder::whereClientId($client->id)->whereType($type);
        $totalData = (clone $prepQuery)->get()->count();
        $data = array();
      
        if (empty($request->input('search.value'))) {
            $totalFiltered = (clone $prepQuery)->get()->count();
            $results = $prepQuery
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } elseif (!(empty($request->input('search.value')))) {
            // $search = $request->input('search.value');

            // $productsSearchQuery = $prepQuery
            //                     ->where(function ($query) use ($search) {
            //                       $query->orWhere('products.product_name', 'LIKE', "%{$search}%");
            //                     });
            // $totalFiltered = (clone $productsSearchQuery)->get()->count();
            // $products =  $productsSearchQuery
            //               ->offset($start)
            //               ->limit($limit)
            //               ->orderBy($order, $dir)
            //               ->get();
        }

        if (!empty($results)) {
          $i = $start;
          foreach ($results as $result) {
              $nestedData['id'] = ++$i;
              $show = domain_route('company.admin.client.getFolderDetails', [$client->id, $result->id]);
              $nestedData['folder_name'] = "{$result->name} ";
              $nestedData['updated_at'] = getDeltaDate(date('Y-m-d', strtotime($result->updated_at)));
              $nestedData['action'] = "<a href='#' data-id='{$result->id}'  data-type='{$result->type}' data-folder_name='{$result->name}' data-href='{$show}' class='folderTitleClick' onClick='openFiles(this); return false;'><i class='fa fa-eye'></i></a>";
              $edit = '#';
              $delete = '#';

              $edit_btn = "<a class='partyUploadsfolderEdit' data-type='{$result->type}' data-id='{$result->id}' data-folder_name='{$result->name}'><i class='fa fa-edit'></i></a>";
              
              $delete_btn = "<a class='partyUploadsfolderDelete' data-type='{$result->type}' href='#' data-id='{$result->id}' data-folder_name='{$result->name}'><i class='fa fa-trash'></i></a>";

              if(Auth::user()->can(Str::singular($type).'uploads-update')) $nestedData['action'] .= $edit_btn;
              if(Auth::user()->can(Str::singular($type).'uploads-delete')) $nestedData['action'] .= $delete_btn;

              $data[] = $nestedData;
          }
        }

        $percent_used = $total_upload_allowed > 0 ?round(($total_size * 100 / $total_upload_allowed), 2) : 0;
        $total_size = round($total_size, 2);
                
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
            "total_size"      => $total_size,
            "total_upload_allowed" => $total_upload_allowed,
            "percent_used" => $percent_used
        );

        return json_encode($json_data);
      }else{
        $folders = PartyUploadFolder::whereClientId($client->id)->orderby('id', 'desc')->whereType($type)->get();
        $total_upload_allowed = config('settings.total_file_size_gb');
        $percent_used = round(($total_size * 100 / $total_upload_allowed), 2);
        $total_size = round($total_size, 2);

        $make_view = view('company.clients.partials_show.foldersview')->with('client', $client)
                          ->with('title', $title)
                          ->with('type', $type)->with('folders', $folders)
                          ->with('total_size', $total_size)
                          ->with('total_upload_allowed', $total_upload_allowed)
                          ->with('percent_used', $percent_used);
        
        return response()->json([
          'data' => $folders, 
          'view' => $make_view->render(),
          'status' => 200,
          'message' => 'success',
          'type' => $type
        ]);
      }

    }
    /** 
     * GET Uploaded Objects of particular Folder
     */
    public function getFolderDetailsDT(Request $request){
      $columns = array(
        'id',
        'file_name',
        'last_modified',
        'added_by',
        'action',
      );
      $company_id = config('settings.company_id');
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');
      $folderId = $request->folder_id;
      $client_id = $request->id;
      $type = Str::singular($request->type);
      $types = $request->type;

      $prepQuery = PartyUpload::whereClientId($client_id)->wherePartyUploadFolderId($folderId);
      $totalData = (clone $prepQuery)->get()->count();
      $data = array();
      
      if (empty($request->input('search.value'))) {
          $totalFiltered = (clone $prepQuery)->get()->count();
          $results = $prepQuery
                  ->offset($start)
                  ->limit($limit)
                  ->orderBy($order, $dir)
                  ->get();
      } elseif (!(empty($request->input('search.value')))) {
          $search = $request->input('search.value');

          $productsSearchQuery = $prepQuery
                              ->where(function ($query) use ($search) {
                                $query->orWhere('products.product_name', 'LIKE', "%{$search}%");
                              });
          $totalFiltered = (clone $productsSearchQuery)->get()->count();
          $products =  $productsSearchQuery
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
      }

      if (!empty($results)) {
        $i = $start;
        foreach ($results as $result) {
            $nestedData['id'] = ++$i;
            if(in_array($result->extension, array("doc","docx","odt"))){
              $img = "assets/dist/img/word-icon.jpg";
            }elseif(in_array($result->extension, array("xlsx","xls"))){
              $img = "assets/dist/img/excel-icon.png";
            }elseif(in_array($result->extension, array("csv","txt"))){
              $img = "assets/dist/img/txt-icon.png";
            }elseif(in_array($result->extension, array("pdf"))){
              $img = "assets/dist/img/pdf-icon.png";
            }else{
              $img = "assets/dist/img/image-file.png";
            }
            $download_path = Storage::disk('s3')->url($result->url);
            if($img){
              $imgTag = "<img src='".asset($img)."' style='height:30px;width:30px;cursor: auto;'/> ";
            }else{
              $imgTag = "<img src='{$download_path}' style='height:30px;width:30px;cursor: auto;'/> ";
            }
            $nestedData['file_name'] = $imgTag.$result->original_file_name;
            $nestedData['last_modified'] = getDeltaDate(date('Y-m-d', strtotime($result->updated_at)));
            $nestedData['added_by'] = $result->employee->name;
            $edit = "#";
            $delete = "#";
            $download_direct = domain_route('company.admin.client.downloadUploadedItems', [$result->id]);
            $download = "<a href='{$download_direct}' target='_blank' style='padding: 3px 6px;'><i class='fa fa-download'></i></a>";
            $edit_btn = "<a data-href='{$edit}' class='btn btn-warning btn-sm partyUploadsfileEdit' data-file_name='{$result->original_file_name}' data-type='{$types}' data-file-id='{$result->id}' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
            $delete_btn = "<a data-href='{$delete}' class='btn btn-danger btn-sm partyUploadsfileDelete' data-file_name='{$result->original_file_name}' data-type='{$types}' data-file-id='{$result->id}' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
                        
            $nestedData['action'] = "";
            if(Auth::user()->can($type.'uploads-view')) $nestedData['action'] .= $download;
            if(Auth::user()->can($type.'uploads-update')) $nestedData['action'] .= $edit_btn;
            if(Auth::user()->can($type.'uploads-delete')) $nestedData['action'] .= $delete_btn;

            $data[] = $nestedData;
        }
      }

      $json_data = array(
          "draw"            => intval($request->input('draw')),
          "recordsTotal"    => intval($totalData),
          "recordsFiltered" => intval($totalFiltered),
          "data"            => $data
      );

      return json_encode($json_data);
    }
    /** 
     * Refetch Images after uploading in that folder
     */
    public function getImagesDetails(Request $request){
      $folderId = $request->folder_id;
      $client_id = $request->id;
      $query = PartyUpload::whereClientId($client_id)
                  ->wherePartyUploadFolderId($folderId)
                  ->get();
      $data = array();
      foreach($query as $result){
        $results = array();
        $results['id'] = $result->id;
        $path = Storage::disk('s3')->url($result->url);
        $results['url'] = $path;
        $results['added_by'] = $result->employee->name;
        $results['file_name'] = $result->original_file_name.'.'.$result->extension;
        $results['last_updated'] = getDeltaDate(date('Y-m-d', strtotime($result->updated_at)));
        
        $results['download_action'] = '';
        if(Auth::user()->can('imageuploads-view')){
          $down = domain_route('company.admin.client.downloadUploadedItems', [$result->id]);
          $downloadAction = "<a href='{$down}' target='_blank' style='padding: 3px 6px;font-size: 30px;color: #00ff14;'><i class='fa fa-download'></i></a>";
          $results['download_action'] = $downloadAction;
        } 
        $results['delete_action'] = '';
        if(Auth::user()->can('imageuploads-delete')){
          $delete_btn = "<a data-href='#' class='partyUploadsfileDelete' data-file_name='{$result->original_file_name}' data-type='{$request->type}' data-file-id='{$result->id}' style='padding: 3px 6px;font-size: 30px;color: #f00;cursor: pointer;'><i class='fa fa-trash-o'></i></a>";
          $results['delete_action'] = $delete_btn;
        } 
        
        
        $data [] = $results;
      }
      $json_data = array(
          "status" => 200,
          "data" => $data
      );

      return response()->json($json_data);
    }
    /**
     * Store Objects
     */
    public function uploadObject(Request $request){
      $company_id = config('settings.company_id');
      $type = $request->type;
      if(!$request->newChosenUpload && $type == "files") $request['newChosenUpload'] = $request->chosenUpload; 
      $this->validate($request, [
        'newChosenUpload'=>'required'
      ], [
        'newChosenUpload.required' => 'Please upload a file.',
      ]);
      $uploadedFiles = $request->only('newChosenUpload');
      if($type=="files"){ $upload_types = config('settings.party_file_upload_types');$upload_size = config('settings.file_upload_size');$total_upload_size = config('settings.total_file_size_gb');}
      elseif($type=="images"){  $upload_types = config('settings.party_image_upload_types');$upload_size = config('settings.party_image_upload_size'); $total_upload_size = config('settings.total_image_size_gb');}

      $uploaded_files = PartyUploadFolder::whereCompanyId($company_id)->whereType($type)->whereHas('files')->whereNull('deleted_at')->pluck('client_id')->toArray();
      $uploaded_till_now = PartyUpload::whereIn('client_id', $uploaded_files)->sum('file_size');
      
      $customMessages = [
        'newChosenUpload.required' => 'Please upload a file.',
        'newChosenUpload.max_uploaded_file_size' => 'Maximum uploads reached.',
        'newChosenUpload.*.mimes' => 'File format not supported. The file :attribute must be a file of type: '.$upload_types.'. ',
        !'newChosenUpload.*' => "The file :attribute must be less than or equal to ". $upload_size ." kb.",
      ];
      $attributes = [];
      $lenofUploadedFiles = sizeof($uploadedFiles["newChosenUpload"]);
      $ind = 0;
      try{
        for($ind; $ind<$lenofUploadedFiles; $ind++){
          $attributes["newChosenUpload.".$ind] = "<strong>{$uploadedFiles['newChosenUpload'][$ind]->getClientOriginalName()}</strong>";
        }
      }catch(\ErrorException $e){
        throw ValidationException::withMessages(['newChosenUpload' => 'Please upload a file.', 'exception' => $e->getMessage()]);
      }
      $this->validate($request, [
        'newChosenUpload'=>'required|max_uploaded_file_size:'.($total_upload_size- ($uploaded_till_now / 1073741824)),
        'newChosenUpload.*' => 'mimes:'.$upload_types.'|max:'.$upload_size,
      ], $customMessages, $attributes);

      $companyName = Auth::user()->companyName($company_id)->domain;
      $uploadedBy = Auth::user()->EmployeeId();
      
      $client_id = $request->id;
      $folder_id = $request->folder_id;
      
      $folder = PartyUploadFolder::find($folder_id);
      $folder_files_count = $folder->files->count();
      if($folder_files_count + count($uploadedFiles["newChosenUpload"]) > 20){
        throw ValidationException::withMessages(['count_upload' => 'Cannot upload more than 20 items in one folder.']);
      }
      if($folder){
        $folder_name = $folder->name;
      }else{
        return response()->json([
          'data' => null, 
          'status' => 403,
          'message' => 'Folder doesn\'t exists',
          'type' => $type
        ]);
      }
      
      $client = Client::find($client_id);
      if($client){
        $client_name = $client->company_name;
      }else{
        return response()->json([
          'data' => null, 
          'status' => 403,
          'message' => 'Party doesn\'t exists',
          'type' => $type
        ]);
      }
      $aws_upload_folder = env('AWS_UPLOADS');
      $upload_folder = $aws_upload_folder. '/' . $companyName . '/' . $client_name . '/' . $type . '/folder_id_' . $folder_id; 
      $storedFiles = array();
      $sendNotfdata = array();
      
      foreach ($uploadedFiles["newChosenUpload"] as $file) {
        try{
          $realname = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
          $extension = $file->getClientOriginalExtension();
          $file_size = $file->getClientSize();
          $new_name = $realname . "-" . Str::random(25) . "-" . time();
          $data = $this->upload($file, $upload_folder, 's3', $new_name);
          
          $file_name = $data['file_name'];
          $file_path = $data['path'];
          if(!empty($file_path)){
            $inserted = PartyUpload::create([
              'party_upload_folder_id' => $folder_id, 
              'client_id' => $client_id, 
              'employee_id' => $uploadedBy, 
              'original_file_name' => $realname,
              'file_name' => $file_name, 
              'url' => $file_path, 
              'extension' => $extension, 
              'file_size' => $file_size,
            ]);
            $inserted['url'] = Storage::disk('s3')->url($inserted->url);
            $inserted['added_by'] = $inserted->employee->name;
            $inserted['file_name'] = $inserted->original_file_name.'.'.$inserted->extension;
            $inserted['last_updated'] = getDeltaDate(date('Y-m-d', strtotime($inserted->updated_at)));
            $inserted['download_action'] = '';
            if(Auth::user()->can('imageuploads-view')){
              $down = domain_route('company.admin.client.downloadUploadedItems', [$inserted->id]);
              $downloadAction = "<a href='{$down}' target='_blank' style='padding: 3px 6px;font-size: 30px;color: #00ff14;'><i class='fa fa-download'></i></a>";
              $inserted['download_action'] = $downloadAction;
            } 
            $inserted['delete_action'] = '';
            if(Auth::user()->can('imageuploads-delete')){
              $delete_btn = "<a data-href='#' class='partyUploadsfileDelete' data-file_name='{$inserted->original_file_name}' data-type='{$type}' data-file-id='{$inserted->id}' style='padding: 3px 6px;font-size: 30px;color: #f00;cursor: pointer;'><i class='fa fa-trash-o'></i></a>";
              $inserted['delete_action'] = $delete_btn;
            } 
            
            array_push($storedFiles, $inserted);
            array_push($sendNotfdata,  $this->formatUploadedItems($inserted));
          }

          $folder->updated_at = date('Y-m-d H:i:s');
          $folder->update();
        }catch(\Exception $e){
          DB::rollback();
          Log::info($e->getMessage());
        }
      }
      if(sizeof($sendNotfdata)>0) $this->uploadNotification($sendNotfdata, 'uploaded_object', $company_id, $type, 'add');

      $files_count = PartyUpload::where('party_upload_folder_id', $folder_id)->count();
      return response()->json([
        'data' => $storedFiles, 
        'status' => 200,
        'message' => 'Successfully Uploaded',
        'type' => $type,
        'objectCount' => $files_count
      ]);
    }
    /**
     * Update Files Only
     */
    public function updateFile(Request $request){
      $file_id = $request->file_id;
      $client_id = $request->id;
      $type = $request->type;
      try{
        $file_instance = PartyUpload::find($file_id);
        $updateParams = [
          'original_file_name' => $request->file_name
        ];
        $folder_id = $file_instance->party_upload_folder_id;
        $folder = PartyUploadFolder::find($folder_id);
        $folder_files_count = $folder->files->count();
        if($type == "files"){
          $uploadedFile = $request->chosenUpload;
          if(!empty($uploadedFile)){
            $company_id = config('settings.company_id');
            $upload_types = config('settings.party_file_upload_types');
            $upload_size = config('settings.file_upload_size');
            $total_upload_size = config('settings.total_file_size_gb');
            $customMessages = [
              'chosenUpload.required' => 'Please upload a file.',
              'chosenUpload.max_uploaded_file_size' => 'Maximum uploads reached.',
              'chosenUpload.*.mimes' => 'File format not supported. The file :attribute must be a file of type: '.$upload_types.'. ',
              !'chosenUpload.*' => "The file :attribute must be less than or equal to ". $upload_size ." Kb.",
            ];
            
            $uploaded_files = PartyUploadFolder::whereCompanyId($company_id)->whereType($type)->whereHas('files')->whereNull('deleted_at')->pluck('client_id')->toArray();
            $uploaded_till_now = PartyUpload::whereIn('client_id', $uploaded_files)->where('id', '<>', $file_id)->sum('file_size');
      
            $this->validate($request, [
              'chosenUpload'=>'required|max_uploaded_file_size:'.($total_upload_size- ($uploaded_till_now / 1073741824)),
              'chosenUpload.*' => 'mimes:'.$upload_types.'|max:'.$upload_size,
            ], $customMessages);
            $companyName = Auth::user()->companyName($company_id)->domain;
            $client = Client::find($file_instance->client_id);
            $client_name = $client->company_name;
            $uploadedBy = Auth::user()->EmployeeId();
            // $folder_id = $file_instance->party_upload_folder_id;
            $aws_upload_folder = env('AWS_UPLOADS');
            $upload_folder = $aws_upload_folder. '/' . $companyName . '/' . $client_name . '/' . $type . '/folder_id_' . $folder_id; 
            $storedFiles = array();

            // $folder = PartyUploadFolder::find($folder_id);
            // $folder_files_count = $folder->files->count();
            if($folder_files_count > 20){
              throw ValidationException::withMessages(['count_upload' => 'Cannot upload more than 20 items in one folder.']);
            }
            
            try{
              $file = $request->chosenUpload;
              $realname = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
              $extension = $file->getClientOriginalExtension();
              $file_size = $file->getClientSize();

              $new_name = $realname . "-" . Str::random(25) . "-" . time();
              $data = $this->upload($file, $upload_folder, 's3', $new_name);
              
              $file_name = $data['file_name'];
              $file_path = $data['path'];
              $updateParams['employee_id'] = $uploadedBy;
              $updateParams['file_name'] = $file_name;
              $updateParams['original_file_name'] = $realname;
              $updateParams['url'] = $file_path;
              $updateParams['extension'] = $extension;
              $updateParams['file_size'] = $file_size;
              $exists_folder = Storage::disk('s3')->exists($file_instance->url);
              if($exists_folder){
                Storage::disk('s3')->delete($file_instance->url);
              }

              $this->uploadNotification($file_instance, 'update_uploaded_object', $company_id, $type, 'update');
              $folder->updated_at = date('Y-m-d H:i:s');
              $folder->update();
            }catch(\Exception $e){
              Log::alert($e->getMessage());
            }
          }            
        }
        $file_instance->update($updateParams);
        $files_count = $files_count = PartyUpload::where('party_upload_folder_id', $folder_id)->count();
        $data = [
        'data' => $file_instance, 
        'status' => 200,
        'message' => 'Successfully Updated',
        'type' => $type,
        'objectCount' => $files_count
        ];
      }catch(\Exception $e){
        $data = [
          'status' => 404,
          'message' => $e->getMessage(),
        
        ];
      }
      return response()->json($data);
    }
    /**
     * Delete Files
     */
    public function deleteFile(Request $request){
      $company_id = Auth::user()->company_id;
      $file_id = $request->file_id;
      $client_id = $request->id;
      $type = $request->type;
      $companyName = Auth::user()->companyName($company_id)->domain;
      $client = Client::find($client_id);
      if($client){
        $client_name = $client->company_name;
      }else{
        return response()->json([
          'data' => null, 
          'status' => 403,
          'message' => 'Party doesn\'t exists',
          'type' => $type
        ]);
      }
      try{
        $file_instance = PartyUpload::find($file_id);
        
        
        $file_instance->folder->update([
          "updated_at" => date('Y-m-d H:i:s')
        ]);
        $aws_upload_folder = env('AWS_UPLOADS');
        $upload_folder = $aws_upload_folder. '/' . $companyName . '/' . $client_name . '/' . $type . '/folder_id_' . $file_instance->party_upload_folder_id; 
        $exists_folder = Storage::disk('s3')->exists($file_instance->url);

        if($exists_folder){
          Storage::disk('s3')->delete($file_instance->url);
        }

        $file_instance->delete();
        $folder_instance = PartyUploadFolder::find($file_instance->party_upload_folder_id);
        if($folder_instance){
          $folder_files_count = $folder_instance->files->count();
        }else{
          $folder_files_count = 0;
        }
        $data = [
        'data' => $file_instance, 
        'status' => 200,
        'message' => 'Successfully Deleted',
        'type' => $type,
        'objectCount' => $folder_files_count,
        'folderId' => $file_instance->party_upload_folder_id
        ];
        $this->uploadNotification($file_instance, 'delete_uploaded_object', $company_id, $type, 'delete');
      }catch(\Exception $e){
        $data = [
          'status' => 404,
          'message' => $e->getMessage(),
        
        ];
      }
      return response()->json($data);
    }

    private function uploadNotification($sendData, $data_type, $companyID, $type, $action){
      try{
        $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => $data_type, $data_type => $sendData, "type" => $type,  "action" => $action);
        $sent = sendPushNotification_($fbIDs, 42, null, $dataPayload);
        Log::info($dataPayload);
        Log::info($sent);
      }catch(\Exception $e){
        Log::info($e->getMessage());
      }
    }

    /**
     * Download Uploaded Items
     */
    public function downloadUploadedItems(Request $request){
      $file_url = PartyUpload::whereId($request->item_id)->first();
      if($file_url){
        return redirect(Storage::disk('s3')->temporaryUrl(
          $file_url->url,
          now()->addHour(),
          ['ResponseContentDisposition' => 'attachment']
        ));
      } 
    }

    private function formatUploads($object){

      if($object->files->first()){
        $uploaded_item = $object->files->map(function($item){
                            return $this->formatUploadedItems($item);
                          });
      }else{
        $uploaded_item = null;
      }

      $data = [
        'id' => $object->id,
        'client_id' => $object->client->id,
        'client_name' => $object->client->company_name,
        'folder_name' => $object->name,
        'type' => $object->type,
        'uploaded_items' => $uploaded_item
      ];

      return $data;
    }

    private function formatUploadedItems($object){
      
      $data = [
        'id' => $object->id,
        'folder_id' => $object->party_upload_folder_id,
        'employee_id' => $object->employee_id,
        'employee_name' => $object->employee->name,
        'file_name' => $object->file_name,
        'original_file_name' => $object->original_file_name,
        'extension' => $object->extension,
        'size_in_bytes' => $object->file_size,
        'path' => Storage::disk('s3')->url($object->url),
        'last_modified' => date('Y-m-d H:i:s', strtotime($object->updated_at))
      ];

      return $data;
    }

    public function getTotalPartyCreated(Request $request){

      try{
        $total_result = Auth::user()->handleQuery('client')->TotalCreatedInGivenRange($request->dateRange, $request->partyType, array('id'))->count();

      }catch(\Exception $e){
        Log::info(array($e->getLine() => $e->getMessage()));
        $total_result = 'N/A';
      }

      return response()->json([
        'count' => $total_result,
        'details_link' => $total_result < 1? null : domain_route('company.admin.client.fileredResults', ['id' => $request->partyType, 'type' => 'new_added-'.$total_result, 'filter' => implode('_', $request->dateRange)]) 
      ], 200);
    }

    public function getNeverOrderedParty(Request $request){
      try{
        $total_result = Auth::user()->handleQuery('client')->PartyNeverOrdered($request->dateRange, $request->partyType, array('id'))->count();

      }catch(\Exception $e){
        Log::info(array($e->getLine() => $e->getMessage()));
        $total_result = 'N/A';
      }

      return response()->json([
        'count' => $total_result,
        'details_link' => $total_result < 1? null : domain_route('company.admin.client.fileredResults', ['id' => $request->partyType, 'type' => empty($request->dateRange)? "never_ordered_party-".$total_result : "ordered_party-".$total_result, 'filter' => ''])
      ], 200);
    }

    public function getPartyUnVisited(Request $request){
      try{
        $total_result = Auth::user()->handleQuery('client')->PartyUnVisited($request->dateRange, $request->partyType, array('id'))->count();

      }catch(\Exception $e){
        Log::info(array($e->getLine() => $e->getMessage()));
        $total_result = 'N/A';
      }

      return response()->json([
        'count' => $total_result,
        'details_link' => $total_result < 1? null : domain_route('company.admin.client.fileredResults', ['id' => $request->partyType, 'type' => 'unvisited_party-'.$total_result, 'filter' => implode('_', $request->dateRange)])
      ], 200);
    }

    public function getPartyNoAction(Request $request){
      try{
        $total_result = Auth::user()->handleQuery('client')->PartyNoAction($request->dateRange, $request->partyType, array('id'))->count();

      }catch(\Exception $e){
        Log::info(array($e->getLine() => $e->getMessage()));
        $total_result = 'N/A';
      }

      return response()->json([
        'count' => $total_result,
        'details_link' => $total_result < 1? null : domain_route('company.admin.client.fileredResults', ['id' => $request->partyType, 'type' => 'no_action-'.$total_result, 'filter' => implode('_', $request->dateRange)])
      ], 200);
    }
}