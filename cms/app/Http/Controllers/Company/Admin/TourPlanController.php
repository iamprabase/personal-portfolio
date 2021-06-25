<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Leave;
use App\TourPlan;
use App\Employee;
use App\LeaveType;
use App\Order;
use App\Collection;
use App\Expense;
use Session;
Use Auth;
use App;
use DB;
use Log;
use Barryvdh\DomPDF\Facade as PDF;

class TourPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:tourplan-create', ['only' => ['create','store']]);
        $this->middleware('permission:tourplan-view');
        $this->middleware('permission:tourplan-update', ['only' => ['edit','update']]);
        $this->middleware('permission:tourplan-delete', ['only' => ['destroy']]);
        $this->middleware('permission:tourplan-status', ['only' => ['changeStatus']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $company_id = config('settings.company_id');
      $tourplansCount =  Auth::user()->handleQuery('tour_plan')
                      ->orderBy('created_at', 'desc')
                      ->count();
      $nCal = config('settings.ncal');
      $employeesWithTourPlans = Auth::user()->handleQuery('employee')->with('tourplans')->pluck('name', 'id')->toArray();
      return view('company.tourplans.index', compact('tourplansCount', 'employeesWithTourPlans', 'nCal'));
    }

    public function ajaxDatatable(Request $request){
      $columns = array(
        0 => 'id',
        1 => 'employee_name',
        2 => 'visit_place',
        3 => 'start_date',
        4 => 'end_date',
        5 => 'date_diff',
        6 => 'status',
        7 => 'action',
      );

      $company_id = config('settings.company_id');
      $empVal = $request->empVal;
      $status = $request->status;
      $start_date = $request->startDate;
      $end_date = $request->endDate;
      $search = $request->input('search')['value'];
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      $prepQuery = Auth::user()->handleQuery('tour_plan')->leftJoin('employees', 'employees.id', 'tourplans.employee_id')->where('tourplans.start_date', '>=',$start_date)->where('tourplans.end_date', '<=',$end_date)->select('tourplans.id', 'tourplans.employee_id', 'tourplans.start_date','tourplans.end_date','tourplans.visit_place', 'tourplans.visit_purpose','tourplans.status', 'tourplans.remark',DB::raw("DATEDIFF(tourplans.end_date,tourplans.start_date)+1 AS date_diff"), 'employees.name as employee_name');
      if(!empty($empVal)){
        $empFilterQuery =  $prepQuery;
        $prepQuery = $empFilterQuery->where('tourplans.employee_id', $empVal);
      }
      if(!empty($status)){
        $statusFilterQuery =  $prepQuery;
        $prepQuery = $statusFilterQuery->where('tourplans.status', $status);
      }
     
      if(!empty($search)){
        $searchQuery = $prepQuery;
        $prepQuery = $searchQuery->where(function($query) use ($search){
                      $query->orWhere('tourplans.visit_place' ,'LIKE', "%{$search}%");
                      $query->orWhere('tourplans.status' ,'LIKE', "%{$search}%");
                      $query->orWhere(\DB::raw('DATEDIFF(tourplans.end_date,tourplans.start_date)+1'), 'LIKE',"%{$search}%");
                      $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
                     });
      }

      $totalData =  $prepQuery->count();
      $totalFiltered = $totalData;
      
      $data = array();
      $tourplans = $prepQuery->orderBy($order,$dir)->offset($start)
                            ->limit($limit)
                            ->get();
      if (!empty($tourplans)) {
          $i = $start;
          foreach ($tourplans as $tourplan) {
            $id = $tourplan->id;
            $employee_name = $tourplan->employee_name;
            $tourplan_start_date = $tourplan->start_date;
            $tourplan_end_date = $tourplan->end_date;
            $status = $tourplan->status;
            $employee_show = domain_route('company.admin.employee.show',[$tourplan->employee_id]);
            $show = domain_route('company.admin.tours.show', [$id]);

            $nestedData['id'] = ++$i;
            $nestedData['employee_name'] = "<a href='{$employee_show}' datasalesman='$tourplan->employee_name}'> {$tourplan->employee_name}</a>";
            $nestedData['visit_place'] = $tourplan->visit_place;
            $nestedData['start_date'] = getDeltaDate($tourplan_start_date);
            $nestedData['end_date'] = getDeltaDate($tourplan_end_date);
            $nestedData['date_diff'] = $tourplan->date_diff;
            if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
              if(Auth::user()->can('tourplan-status'))
              $classNameStatus = 'edit-modal';
              else
              $classNameStatus = 'alert-modal';
            }else{
              if((getEmployee($tourplan->employee_id)['superior'])==Auth::user()->EmployeeId() && Auth::user()->can('tourplan-status')){
                $classNameStatus = 'edit-modal';
              }else{
                $classNameStatus = 'alert-modal';
              }
            }

            if($status == 'Approved'){
              $spanTagClassName = 'label label-success';
            }elseif($status == 'Pending'){
              $spanTagClassName = 'label label-warning';
            }else{
              $spanTagClassName = 'label label-danger';
            }

            $spanTag = "<span class='{$spanTagClassName}'>{$status}</span>";
            $nestedData['status'] = "<a href='#' class='{$classNameStatus}' data-id='{$id}' data-status='{$status}' data-remark='{$tourplan->remark}'>".$spanTag."</a>";

            if(Auth::user()->EmployeeId() == $tourplan->employee_id && Auth::user()->can('tourplan-update')  &&  $status == "Pending"){
              $editUrl =  domain_route('company.admin.tourplan.update', [$id]);
              $editBtn = "<a href='#' class='btn btn-warning btn-sm update-modal' data-editurl='{$editUrl}' data-id={$id} data-place_of_visit='{$tourplan->visit_place}' data-visit_purpose='{$tourplan->visit_purpose}' data-start_date={$tourplan_start_date} data-end_date={$tourplan_end_date} data-remark='{$tourplan->remark}' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
            }else{
              $editBtn = null;
            }

            if(Auth::user()->EmployeeId() == $tourplan->employee_id && Auth::user()->can('tourplan-delete') &&  $status == "Pending"){
              $deleteUrl = domain_route('company.admin.tourplan.destroy', [$id]);
              $delBtn = "<a href='#' class='btn btn-danger btn-sm delete' data-delurl='{$deleteUrl}' data-id={$id} style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
            }else{
              $delBtn = null;
            }

            $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>{$editBtn}{$delBtn}";

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

    public function custompdfdexport(Request $request){
      $getExportData = json_decode($request->exportedData)->data;
      $pageTitle = $request->pageTitle;
      set_time_limit ( 300 );
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      $pdf = PDF::loadView('company.tourplans.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait');
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
      die;
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
            'start_date.required'     => 'From Date is required',
            'start_date.date'         => 'Start date should be a valid date',
            'end_date.required'       => 'To Date is required',
            'end_date.date'           => 'End date should be a valid date',
            'end_date.after_or_equal' => 'End date can not be before start date',
        ];

        $this->validate($request, [
            'start_date'=> 'required|date_format:Y-m-d',
            'end_date'  => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'place_of_visit' => 'required',
            'visit_purpose' => 'required',
        ], $customMessages);

      $company_id = config('settings.company_id');
      $start_date = $request->start_date;
      $end_date   = $request->end_date;
      $tourPlanExists = TourPlan::where('company_id',$company_id)->where('employee_id',Auth::user()->EmployeeId())->where(function($q)use($start_date, $end_date){

              $q= $q->where(function($q1)use($start_date){
                  $q1 = $q1->where('start_date','<=',$start_date)->where('end_date','>=',$start_date);
              })->orWhere(function($q2)use($end_date){
                  $q2=$q2->where('start_date','<=',$end_date)->where('end_date','>=',$end_date);
              })->orWhere(function($q3)use($start_date, $end_date){
                  $q3=$q3->where('start_date','>=',$start_date)->where('end_date','<=',$end_date);
              });
      })->first();

      if($tourPlanExists){
        session()->flash('alert','Tour Plan could not be created as it already exists between selected dates.');
        return redirect()->back();
      }

      $getInstance = new TourPlan;
      $place_of_visit = $request->place_of_visit;
      $visit_purpose = $request->visit_purpose;
      $remark = $request->remark;

      $getInstance->company_id = $company_id;

      $getInstance->employee_id = Auth::user()->EmployeeId();
      $getInstance->visit_place = $place_of_visit;
      $getInstance->visit_purpose = $visit_purpose;
      $getInstance->start_date = $start_date;
      $getInstance->end_date = $end_date;
      $getInstance->status = "Pending";
      // $getInstance->remark = $remark;

      $saved = $getInstance->save();

      //send notification to app
      if (!empty($saved)) {
          //send notification to specific employee
          $temp = Employee::where('id', $getInstance->employee_id)->first();
          if (!empty($temp)) {
              $notificationData = array(
                  "company_id" => $company_id,
                  "employee_id" => $temp->id,
                  "data_type" => "tourplan",
                  "data" => "",
                  "action_id" => $getInstance->id,
                  "title" => "Tour Plan Added",
                  "description" => "Your Tour Plan has been " . $request->status,
                  "created_at" => date('Y-m-d H:i:s'),
                  "status" => 1,
                  "to" => 1
              );

              $sendingNotificationData = $notificationData;
              $sendingNotificationData["unix_timestamp"] = time();
              if (!empty($temp->firebase_token)) {

                  $dataPayload = array("data_type" => "TourPlan", "tourplan" => $getInstance, "action" => "add", "tourplan_id" => $getInstance->id, "status" => 'Pending', "remark" => $getInstance->remark);
                  $msgID = sendPushNotification_([$temp->firebase_token], 18, $sendingNotificationData, $dataPayload);
              }
          }

      }

      \Session::flash('updated', 'Tourplan Created successfully');
      return back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $company_id = config('settings.company_id');
        $tourplan = Auth::user()->handleQuery('tour_plan',$request->id)->first();
        $start_date = $tourplan->start_date;
        $end_date = $tourplan->end_date;
        $expense_total = Expense::select('expenses.amount')
                            ->where('company_id', $company_id)
                            ->where('employee_id', $tourplan->employee_id)
                            ->whereBetween('expense_date', [$start_date, $end_date])
                            ->sum('amount');
        $collection_total = Collection::select('collections.payment_received')
                            ->where('company_id', $company_id)
                            ->where('employee_id', $tourplan->employee_id)
                            ->whereBetween('payment_date', [$start_date, $end_date])
                            ->sum('payment_received');
        $sales_total = Order::select('orders.grand_total')
                        ->where('company_id', $company_id)
                        ->where('employee_id', $tourplan->employee_id)
                        ->whereBetween('order_date', [$start_date, $end_date])
                        ->sum('grand_total');
                        
               $action=NULL;
              if(Auth::user()->EmployeeId() == $tourplan->employee_id && Auth::user()->can('tourplan-update')  &&  $tourplan->status == "Pending"){
                $editUrl= domain_route('company.admin.tourplan.update', [$request->id]);
                $action ="<button type='button' class='btn btn-warning btn-sm'  data-toggle='modal' data-target='#myEditModal' style='padding: 7px 6px; '><a href'{$editUrl}' style='color:#fff !important'><i class='fa fa-edit'></i>Edit</a> </button>";
              }
      
              if(Auth::user()->EmployeeId() == $tourplan->employee_id && Auth::user()->can('tourplan-delete')  &&  $tourplan->status == "Pending"){
                $delete = domain_route('company.admin.tourplan.destroy', [$request->id]);
                $action = $action."<button type='button' class='btn btn-danger btn-sm'  data-toggle='modal' data-target='#delete' style='padding: 7px 6px; ' data-mid='{$request->id}'><a href'{$delete}' style='color:#fff !important'><i class='fa fa-trash-o'></i>Delete</a>";
              }
        return view('company.tourplans.show', compact('tourplan', 'sales_total', 'collection_total', 'expense_total','action'));
    }

    public function download(Request $request)
    {
        $company_id = config('settings.company_id');
        $tourplan = TourPlan::where('id', $request->id)->where('company_id', $company_id)->first();
        $start_date = $tourplan->start_date;
        $end_date = $tourplan->end_date;
        
        $expense_total = Expense::select('expenses.amount')
                            ->where('company_id', $company_id)
                            ->where('employee_id', $tourplan->employee_id)
                            ->where('expense_date', $start_date)
                            ->sum('amount');
        $collection_total = Collection::select('collections.payment_received')
                            ->where('company_id', $company_id)
                            ->where('employee_id', $tourplan->employee_id)
                            ->whereBetween('payment_date', [$start_date, $end_date])
                            ->sum('payment_received');
        $sales_total = Order::select('orders.grand_total')
                        ->where('company_id', $company_id)
                        ->where('employee_id', $tourplan->employee_id)
                        ->whereBetween('order_date', [$start_date, $end_date])
                        ->sum('grand_total');
        
        $pdf = App::make('dompdf.wrapper');
        $pdf = PDF::loadView('company.tourplans.download', compact('tourplan', 'sales_total', 'collection_total', 'expense_total'));
        if(config('settings.ncal')==1){
            $pdf_name = getEmployeeName($tourplan->employee_id).'_' .getDeltaDate(date('Y-m-d', strtotime($tourplan->start_date))).' To '.getDeltaDate(date('Y-m-d', strtotime($tourplan->end_date)));
        }else{
            $pdf_name = getEmployeeName($tourplan->employee_id).'_' .date('d M Y', strtotime($tourplan->start_date)).' To '.date('d M Y', strtotime($tourplan->end_date));
        }
        return $pdf->download($pdf_name . '_tourreport.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
      die;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
      
      
        $customMessages = [
            'start_date.required'     => 'From Date is required',
            'start_date.date'         => 'Start date should be a valid date',
            'end_date.required'       => 'To Date is required',
            'end_date.date'           => 'End date should be a valid date',
            'end_date.after_or_equal' => 'End date can not be before start date',
        ];

        $this->validate($request, [
            'start_date'=> 'required|date_format:Y-m-d',
            'end_date'  => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'place_of_visit' => 'required',
            'visit_purpose' => 'required',
        ], $customMessages);
      $id = $request->tourplan_id;
      $company_id = config('settings.company_id');
      $getInstance = TourPlan::find($id);
      $tourPlanExists = TourPlan::where('company_id',$company_id)->where('id', '<>', $id)->where('employee_id',Auth::user()->EmployeeId())->where(function($q)use($request){

              $q= $q->where(function($q1)use($request){
                  $q1 = $q1->where('start_date','<=',$request->start_date)->where('end_date','>=',$request->start_date);
              })->orWhere(function($q2)use($request){
                  $q2=$q2->where('start_date','<=',$request->end_date)->where('end_date','>=',$request->end_date);
              })->orWhere(function($q3)use($request){
                  $q3=$q3->where('start_date','>=',$request->start_date)->where('end_date','<=',$request->end_date);
              });
      })->first();
      Session::flash('DT_Tour_filters', $request->DT_Tour_FILTER);
      if($tourPlanExists){
        session()->flash('alert','Tour Plan could not be edited as it already exists between selected dates.');
        return redirect()->back();
      }
      try{
        $place_of_visit = $request->place_of_visit;
        $visit_purpose = $request->visit_purpose;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        // $remark = $request->remark;

        $getInstance->visit_place = $place_of_visit;
        $getInstance->visit_purpose = $visit_purpose;
        $getInstance->start_date = $start_date;
        $getInstance->end_date = $end_date;
        // $getInstance->remark = $remark;

        $saved = $getInstance->update();
        //send notification to app
        if (!empty($saved)) {
            //send notification to specific employee
            $temp = Employee::where('id', $getInstance->employee_id)->first();
            if (!empty($temp)) {
                $notificationData = array(
                    "company_id" => $company_id,
                    "employee_id" => $temp->id,
                    "data_type" => "tourplan",
                    "data" => "",
                    "action_id" => $getInstance->id,
                    "title" => "Tour Plan Updated",
                    "description" => "Your Tour Plan has been Updated",
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => 1,
                    "to" => 1
                );

                $sendingNotificationData = $notificationData;
                $sendingNotificationData["unix_timestamp"] = time();
                if (!empty($temp->firebase_token)) {

                    $dataPayload = array("data_type" => "TourPlan", "tourplan" => $getInstance, "action" => "update", "tourplan_id" => $getInstance->id, "status" => $getInstance->status, "remark" => $getInstance->remark);
                    $msgID = sendPushNotification_([$temp->firebase_token], 18, $sendingNotificationData, $dataPayload);
                }
            }
        }
        \Session::flash('updated', 'Tourplan Updated successfully');
        return back();
      }catch(\Exception $e){
        \Session::flash('error', 'Some error occured');
        return back();
      }
    }

    public function changeStatus(Request $request)
    {
        $company_id = config('settings.company_id');
        $tourplan = TourPlan::findOrFail($request->tourplan_id);
        $tourplan->status = $request->status;
        $tourplan->remark = $request->remark;
        $saved = $tourplan->save();

        //send notification to app
        if (!empty($saved)) {
            //send notification to specific employee
            $temp = Employee::where('id', $tourplan->employee_id)->first();
            if (!empty($temp)) {
                $notificationData = array(
                    "company_id" => $company_id,
                    "employee_id" => $temp->id,
                    "data_type" => "tourplan",
                    "data" => "",
                    "action_id" => $tourplan->id,
                    "title" => "Tour Plan " . $request->get('status'),
                    "description" => "Your Tour Plan has been " . $request->status,
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => 1,
                    "to" => 1
                );

                $sendingNotificationData = $notificationData;
                $sendingNotificationData["unix_timestamp"] = time();
                if (!empty($temp->firebase_token)) {

                    $dataPayload = array("data_type" => "TourPlan", "tourplan" => $tourplan, "action" => "update_status", "tourplan_id" => $tourplan->id, "status" => $tourplan->status, "remark" => $tourplan->remark);
                    $msgID = sendPushNotification_([$temp->firebase_token], 18, $sendingNotificationData, $dataPayload);
                }
            }

        }


        return back()->with('success', 'Status Updated.');
    }

    public function approve(Request $request)
    {
      die;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
  
      $tourPlanInstance = TourPlan::findOrFail($request->del_id);
      if(Auth::user()->can('tourplan-delete')){
        $temp = Employee::where('id', $tourPlanInstance->employee_id)->first();
        $tourPlanInstance->delete();
        $dataPayload = array("data_type" => "TourPlan", "tourplan" => $tourPlanInstance, "action" => "delete");
        $sent = sendPushNotification_([$temp->firebase_token], 18, null, $dataPayload);
      }
      
      session()->flash('success', 'TourPlan deleted successfully.');
      return redirect()->route('company.admin.tours', ['domain' => domain()]);
            // return redirect()->back();
    }
}
