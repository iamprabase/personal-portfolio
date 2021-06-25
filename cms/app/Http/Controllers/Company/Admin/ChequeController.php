<?php

namespace App\Http\Controllers\Company\Admin;

use App\Bank;
use App\Client;
use App\Collection;
use App\Employee;
use App\Http\Controllers\Controller;
use App\ModuleAttribute;
use App\Order;
use Auth;
use Session;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use View;


class ChequeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:collection-create', ['only' => ['create','store']]);
        $this->middleware('permission:collection-view');
        $this->middleware('permission:collection-update', ['only' => ['edit','update']]);
        $this->middleware('permission:collection-delete', ['only' => ['destroy']]);
    }


    public function index()
    {
        
      $prepQuery = Auth::user()->handleQuery('collection')->where('payment_method', 'Cheque')->orderBy('id', 'desc')->get(['client_id', 'employee_id', 'payment_method']);
      $collectionsCount = $prepQuery->count();
      if($collectionsCount>0){
        
        $party_ids = $prepQuery->unique('client_id')
                    ->pluck('client_id')
                    ->toArray();
        $partiesWithCollections = Client::whereIn('id', $party_ids)
                              ->orderBy('company_name','asc')
                              ->pluck('company_name','id')->toArray();
      }else{
        $partiesWithCollections = array();
      }
      $nCal = config('settings.ncal');
      Log::info($partiesWithCollections);
      return view('company.cheques.index', compact('collectionsCount','partiesWithCollections', 'nCal'));
    }

    public function ajaxDatatable(Request $request){
       $columns = array(
        0 => 'id',
        1 => 'company_name',
        2 => 'bank_name',
        3 => 'employee_name',
        4 => 'cheque_date',
        5 => 'payment_date',
        6 => 'payment_received',
        7 => 'payment_status_note',
        8 => 'payment_status',
        9 => 'action',
      );

      $company_id = config('settings.company_id');
      $partyVal = $request->partyVal;
      $chequeStatusVal = $request->chequeStatusVal;
      $start_date = $request->startDate;
      $end_date = $request->endDate;
      $today = date('Y-m-d');
      $search = $request->input('search')['value'];
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      $prepQuery = Auth::user()->handleQuery('collection')->leftJoin('employees', 'employees.id', 'collections.employee_id')->leftJoin('clients', 'clients.id', 'collections.client_id')->leftJoin('banks', 'banks.id', 'collections.bank_id')->whereBetween('collections.cheque_date', [$start_date, $end_date])->where('payment_method', 'Cheque')->select('collections.id', 'collections.client_id', 'collections.employee_id', 'collections.cheque_date', 'collections.payment_date', 'collections.payment_received', 'collections.payment_status_note', 'collections.payment_status', 'collections.employee_type', 'employees.name as employee_name', 'clients.company_name as company_name', 'banks.name as bank_name');

      if(!empty($partyVal)){
        $partyFilterQuery = $prepQuery;
        $prepQuery = $partyFilterQuery->where('collections.client_id', $partyVal);
      }
      if(!empty($chequeStatusVal)){
        $paymentModeQuery = $prepQuery;
        if(sizeof($chequeStatusVal)==1 && (in_array("Overdue", $chequeStatusVal) || in_array("Pending", $chequeStatusVal))){
          if(in_array("Overdue", $chequeStatusVal)){
            $prepQuery = $paymentModeQuery->where('collections.cheque_date','<',$today)->where('collections.payment_status',"Pending");
          }elseif(in_array("Pending", $chequeStatusVal)){
            $prepQuery = $paymentModeQuery->where('collections.cheque_date','>=',$today)->where('collections.payment_status',"Pending");
          }
        }else{
          if(in_array("Overdue", $chequeStatusVal) && !in_array("Pending", $chequeStatusVal)){
            $index = array_search("Overdue", $chequeStatusVal);
            unset($chequeStatusVal[$index]);
            $prepQuery = $paymentModeQuery->whereIn('collections.payment_status', $chequeStatusVal)->orWhere(function($paymentModeQuery) use ($today, $start_date, $end_date){
              $paymentModeQuery->whereBetween('collections.cheque_date', [$start_date, $end_date])->where('collections.cheque_date','<',$today)->where('collections.payment_status',"Pending");
            });
          }elseif(!in_array("Overdue", $chequeStatusVal) && in_array("Pending", $chequeStatusVal)){
            $index = array_search("Pending", $chequeStatusVal);
            unset($chequeStatusVal[$index]);
            $prepQuery = $paymentModeQuery->whereIn('collections.payment_status', $chequeStatusVal)->orWhere(function($paymentModeQuery) use ($today, $start_date, $end_date){
              $paymentModeQuery->whereBetween('collections.cheque_date', [$start_date, $end_date])->where('collections.cheque_date','>=',$today)->where('collections.payment_status',"Pending");
            });
          }else{
            $prepQuery = $paymentModeQuery->whereIn('collections.payment_status', $chequeStatusVal);
          }
        }
      }
      if(!empty($search)){
        $searchQuery = $prepQuery;
        $prepQuery = $searchQuery->where(function($query) use ($search){
                      $query->orWhere('collections.payment_received' ,'LIKE', "%{$search}%");
                      $query->orWhere('collections.payment_status_note' ,'LIKE', "%{$search}%");
                      $query->orWhere('collections.payment_status' ,'LIKE', "%{$search}%");
                      $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
                      $query->orWhere('clients.company_name' ,'LIKE', "%{$search}%");
                      $query->orWhere('banks.name' ,'LIKE', "%{$search}%");
                     });
      }

      $totalData =  $prepQuery->count();
      $totalFiltered = $totalData;
      $data = array();
      $total = $prepQuery->sum('collections.payment_received');
      $collections = $prepQuery->orderBy($order,$dir)->offset($start)
                      ->limit($limit)
                      ->get();
      if (!empty($collections)) {
          $i = $start;
          $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
          foreach ($collections as $cheque) {
            $id = $cheque->id;
            $company_name = ucfirst($cheque->company_name);
            $received_payment = isset($cheque->payment_received)?config('settings.currency_symbol').'   '.number_format((float)$cheque->payment_received,2):null;
            $get_cheque_date = $cheque->cheque_date;
            $cheque_date = isset($get_cheque_date)?getDeltaDate(date('Y-m-d',strtotime($get_cheque_date))):null;
            $payment_date = isset($cheque->payment_date)?getDeltaDate(date('Y-m-d',strtotime($cheque->payment_date))):null;
            $status = $cheque->payment_status;
            $employee_type = $cheque->employee_type;
            $payment_status_note = strlen($cheque->payment_status_note)>15?substr($cheque->payment_status_note, 0,15).'...':$cheque->payment_status_note;
            $client_show = in_array($cheque->client_id, $viewable_clients)?domain_route('company.admin.client.show',[$cheque->client_id]):null;
            $employee_show = domain_route('company.admin.employee.show',[$cheque->employee_id]);
            $show = domain_route('company.admin.cheque.show', [$id]);
            $edit = domain_route('company.admin.cheque.edit', [$id]);

            $nestedData['id'] = ++$i;
            $nestedData['company_name'] = "<a href='{$client_show}' class='clientLinks' data-viewable='{$client_show}'>{$company_name}</a>";
            $nestedData['bank_name'] = isset($cheque->bank_name)?$cheque->bank_name:"<span hidden>{$id}</span>";
            $nestedData['employee_name'] = ($employee_type=='Admin')?"<a href='#'>".Auth::user()->name." (Admin)</a>":"<a href='{$employee_show}'>{$cheque->employee_name}</a>";
            $nestedData['cheque_date'] = $cheque_date;
            $nestedData['payment_date'] = $payment_date;
            $nestedData['payment_received'] = $received_payment;
            $nestedData['payment_received'] = $received_payment;
            $nestedData['payment_status_note'] = $payment_status_note;

            if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin())
              if(Auth::user()->can('pdc-status'))
                $hrefClassName = 'edit-modal';
              else
                $hrefClassName = 'alert-modal';
            else
              if(Auth::user()->can('pdc-status'))
              $hrefClassName = (getEmployee($cheque->employee_id)['superior']==Auth::user()->EmployeeId())?'edit-modal':'alert-modal';
              else
                $hrefClassName = 'alert-modal';

            if ($status == 'Pending') {
              if(isset($get_cheque_date)){
                if ($get_cheque_date < $today) {
                  $spanTagClassName = 'label label-primary';
                  $spanText = 'Overdue';
                }else{
                  $spanTagClassName = 'label label-warning';
                  $spanText = 'Pending';
                }
              }else{
                $spanTagClassName = 'label label-warning';
                $spanText = 'Pending';
              }
            }elseif($status == 'Deposited'){
              $spanTagClassName = 'label label-default';
              $spanText = $status;
            }elseif($status == 'Cleared'){
              $spanTagClassName = 'label label-success';
              $spanText = $status;
            }elseif($status == 'Bounced'){
              $spanTagClassName = 'label label-danger';
              $spanText = $status;
            }else{
              $spanTagClassName = 'label label-danger';
              $spanText = 'N/A';
            }

            $spanTag = "<span class='{$spanTagClassName}'>{$spanText}</span>";
            
            $nestedData['payment_status'] = "<a href='#' class='{$hrefClassName}' data-id='{$id}' data-status='{$status}' data-remark='{$payment_status_note}'>{$spanTag}</a>";

            $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
            if(Auth::user()->can('collection-update'))
            $nestedData['action'] =$nestedData['action'] ."<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";

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
      set_time_limit ( 300 );
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      $pdf = PDF::loadView('company.cheques.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }

    public function updateStatus(Request $request)
    {
        $company_id = config('settings.company_id');

        $checkCount = Auth::user()->handleQuery('collection',$request->cheque_id)->where('payment_method', 'Cheque')->get()->count();
        if ($checkCount > 0) {

            $cheque = Collection::where('id',$request->cheque_id)->first();
            $cheque->payment_status = $request->status;
            $cheque->payment_status_note = $request->remark;
            $cheque->save();
            
            $fbID = getFBIDs($company_id, null, $cheque->employee_id);
            $notificationData = array(
                "company_id" => $company_id,
                "employee_id" => $cheque->employee_id,
                "data_type" => "cheque",
                "data" => "",
                "action_id" => $cheque->id,
                "title" => "Cheque Status Changed to " . $cheque->payment_status,
                "description" => $cheque->payment_status_note,
                "created_at" => date('Y-m-d H:i:s'),
                "status" => 1,
                "to" => 1,
                "unix_timestamp" => time()
            );

            $cheque->company_name = getObjectValue($cheque->client,"company_name","");
            $cheque->bank_name = getObjectValue($cheque->bank,"name","");

            $dataPayload = array("data_type" => "collection", "collection" => $cheque, "action" => "update");
            $sent = sendPushNotification_($fbID, 3, $notificationData, $dataPayload);
            flash()->success('Payment Status has been Updated');
            return back();
        }
        return redirect()->route('company.admin.cheque.index', ['domain' => domain()])->with('success', 'You are not authorized to update this content');
    }

    public function show($domain, $id)
    {

        $company_id = config('settings.company_id');

        $checkCount =  Auth::user()->handleQuery('collection',$id)->where('payment_method', 'Cheque')->get()->count();
        if ($checkCount > 0) {
            $cheque = Collection::findOrFail($id);
            $images = DB::table('images')->where('company_id',$company_id)->where('type', '=', 'collection')->where('type_id', $id)->get();
            return view('company.cheques.show', compact('cheque','images'));
        }
        return redirect()->back()->withErrors(['msg', 'No record Found']);
    }

    public function edit($domain, $id)
    {

        $company_id = config('settings.company_id');

        $checkCount = Auth::user()->handleQuery('collection',$id)->where('payment_method', 'Cheque')->get()->count();

        if ($checkCount > 0) {

            $cheque = Collection::findOrFail($id);

            $banks = Bank::orderBy('name', 'ASC')->where('company_id', $company_id)->pluck('name', 'id')->toArray();

            $employees = Auth::user()->handleQuery('employee')->orderBy('name', 'ASC')->pluck('name', 'id')->toArray();

            return view('company.cheques.edit', compact('banks', 'cheque', 'employees'));

        } else {
            return back();
        }

    }


    public function update($domain, $id, Request $request)
    {
        $company_id = config('settings.company_id');
        $checkCount = Auth::user()->handleQuery('collection',$id)->where('payment_method', 'Cheque')->get()->count();
        if ($checkCount > 0) {
            $cheque = Collection::findOrFail($id);
            $customMessages = [
                'bank_id' => 'The Bank field is required.',
                'cheque_date' => 'Cheque Date is required',
                'receive_date' => 'Payment Date is required',
                'payment_received' => 'Received Amount is required',
                'cheque_no' => 'Cheque Number is required',
            ];

            $this->validate($request, [
                'bank_id' => 'required',
                'cheque_date' => 'required',
                'receive_date' => 'required',
                'payment_received' => 'required|numeric',
                'cheque_no' => 'required|numeric',
            ], $customMessages);

            $cheque->bank_id = $request->bank_id;
            $cheque->cheque_date = $request->cheque_date;
            $cheque->payment_date = $request->receive_date;
            $cheque->cheque_no = $request->cheque_no;

            if ($request->get('employee_id') == 'admin') {
                $cheque->employee_type = 'Admin';
            }else {
                $cheque->employee_type = 'Employee';
            }
            
            //$cheque->employee_id = Auth::user()->EmployeeId();
            $cheque->employee_id = $request->employee_id;
            $cheque->payment_received = $request->payment_received;
            $cheque->payment_status_note = $request->payment_status_note;
            $cheque->company_id = $company_id;
            $cheque->save();

            $fbID = getFBIDs($company_id, null, $cheque->employee_id);
            $notificationData = array(
                "company_id" => $company_id,
                "employee_id" => $cheque->employee_id,
                "data_type" => "cheque",
                "data" => "",
                "action_id" => $cheque->id,
                "title" => "Cheque Status Changed to " . $cheque->payment_status,
                "description" => $cheque->payment_status_note,
                "created_at" => date('Y-m-d H:i:s'),
                "status" => 1,
                "to" => 1,
                "unix_timestamp" => time()
            );

            $cheque->company_name = getObjectValue($cheque->client,"company_name","");
            $cheque->bank_name = getObjectValue($cheque->bank,"name","");

            $dataPayload = array("data_type" => "collection", "collection" => $cheque, "action" => "update");
            $sent = sendPushNotification_($fbID, 3, $notificationData, $dataPayload);
            Session::flash('DT_Cheq_filters', $request->DT_Cheq_FILTER);

            return redirect()->route('company.admin.cheque.index', ['domain' => domain()])->with('success', 'Information has been Updated');

        } else {
            return redirect()->route('company.admin.cheque.index', ['domain' => domain()])->with('success', 'You are not authorized to update this content');
        }
    }

}