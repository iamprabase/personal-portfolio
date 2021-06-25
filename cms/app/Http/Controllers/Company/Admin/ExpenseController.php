<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Expense;
use App\ExpenseType;
use App\Employee;
use App\User;
Use Auth;
use DB;
use App\Client;
use Session;
use Carbon\Carbon;
use Storage;
use Log;
use Barryvdh\DomPDF\Facade as PDF;


class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:expense-create', ['only' => ['create','store']]);
        $this->middleware('permission:expense-view');
        $this->middleware('permission:expense-update', ['only' => ['edit','update']]);
        $this->middleware('permission:expense-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $company_id = config('settings.company_id');
        $nCal = config('settings.ncal');
        $expenses = Auth::user()->handleQuery('Expense')
            ->orderBy('created_at', 'desc')
            ->get();
        $expensesCount = $expenses->count();
        $employee_ids = $expenses->unique('employee_id')
                            ->pluck('employee_id')
                            ->toArray();
        $employees =Auth::user()->handleQuery('employee')
                                    ->whereIn('id', $employee_ids)
                                    ->orderBy('name','asc')
                                    ->pluck('name','id')
                                    ->toArray();
        $empId = Auth::user()->EmployeeId();
        if(Auth::user()->isCompanyManager()){
          $allSup = [];
        }else{
          $allSup = [];
          $allSup = Auth::user()->getUpChainUsers($empId,$allSup);
          array_pop($allSup);
          $empAdmins = Employee::where('company_id',$company_id)->select('id')->where('is_admin',1)->get();
          foreach($empAdmins as $emp){
            array_push($allSup,$emp->id);
          }          
        }
        return view('company.expenses.index', compact('expenses','employees','allSup','expensesCount', 'nCal'));
    }

    public function ajaxTable(Request $request){
        // $updateRecord = Expense::whereStatus('')->update([
        //   'status' => 'Rejected'
        // ]);
        // $updateColumn = DB::statement("ALTER TABLE `expenses` CHANGE `status` `status` ENUM ('Approved', 'Rejected', 'Pending')");
        $columns = array( 
            'id', 
            'expense_date',
            'created_at',
            'amount',
            'description',
            'AddedByName',
            'ApprovedByName',
            'status',
            'action',
            'ea',
            'e_date',
        );
        $totalData = Auth::user()->handleQuery('expense')->count();
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if($order=='description'){
          $order = 'expense_types.expensetype_name';
        }

        if($request->empVal){
          $employee_id = $request->empVal;
        }
        if($request->status){
          $status = $request->status;
        }
        if($request->startDate){
          $startDateFilter = $request->input('startDate');
        }
        if($request->endDate){
          $endDateFilter = $request->input('endDate');
          $endDateFilter = Carbon::parse($endDateFilter)->format('Y-m-d').' 23:59:59';
        }
        $empId = Auth::user()->EmployeeId();
        $company_id = config('settings.company_id');
        if(Auth::user()->isCompanyManager()){
          $allSup = [];
        }else{
          $allSup = [];
          $allSup = Auth::user()->getUpChainUsers($empId,$allSup);
          array_pop($allSup);
          $empAdmins = Employee::where('company_id',$company_id)->select('id')->where('is_admin',1)->get();
          foreach($empAdmins as $emp){
            array_push($allSup,$emp->id);
          }          
        }

        if($request->input('search.value')){
          $search = $request->input('search.value'); 
        }
        $expenses =  Auth::user()->handleQuery('expense')
                  ->leftJoin('employees as addedBy_tbl','expenses.employee_id','addedBy_tbl.id')
                  ->leftJoin('employees as approvedBy_tbl','expenses.approved_by','approvedBy_tbl.id')
                  ->leftJoin('expense_types','expenses.expense_type_id','expense_types.id')
                  ->select('expenses.*','addedBy_tbl.id as AddedById','addedBy_tbl.name as AddedByName','approvedBy_tbl.id as ApprovedById','approvedBy_tbl.name as ApprovedByName','expense_types.expensetype_name');
                  if($request->empVal){
                    $expenses = $expenses->where('employee_id',$employee_id);
                  }
                  if($request->status){
                    $expenses = $expenses->where('expenses.status',$status);
                  }
                  if($request->startDate){
                    $expenses = $expenses->where('expenses.created_at','>=',$startDateFilter);
                  }
                  if($request->endDate){
                    $expenses = $expenses->where('expenses.created_at','<=',$endDateFilter);
                  }
                  if($request->input('search.value')){
                    $expenses = $expenses->where('expenses.id','LIKE',"%{$search}%")
                    ->orWhere('expenses.created_at', 'LIKE',"%{$search}%")
                    ->orWhere('expenses.amount', 'LIKE',"%{$search}%")
                    ->orWhere('expenses.description', 'LIKE',"%{$search}%")
                    ->orWhere('addedBy_tbl.name', 'LIKE',"%{$search}%")
                    ->orWhere('approvedBy_tbl.name', 'LIKE',"%{$search}%")
                    ->orWhere('expenses.status', 'LIKE',"%{$search}%");
                  }
                  
                  $total = $expenses->sum('amount');
                  $totalFiltered = $expenses->get()->count();
                  $expenses = $expenses->orderBy($order,$dir)->offset($start)
                              ->limit($limit)
                              ->get();
        $data = array();
        if(!empty($expenses))
        {   
            $i = $start;
            foreach ($expenses as $expense)
            {
                $show =  domain_route('company.admin.expense.show',[$expense->id]);
                $edit =  domain_route('company.admin.expense.edit',[$expense->id]);

                $nestedData['id'] = ++$i;
                $nestedData['expense_date'] = getDeltaDate(Carbon::parse($expense->expense_date)->format('Y-m-d'));
                $nestedData['created_at'] = getDeltaDate(Carbon::parse($expense->created_at)->format('Y-m-d'));
                $nestedData['amount'] = config('settings.currency_symbol').' '.number_format((float)$expense->amount,2);
                $nestedData['description'] = $expense->expensetype_name;

                if(isset($expense->employee_id) && isset($expense->employee->name)){
                    $nestedData['AddedByName'] = '<a href="'.domain_route('company.admin.employee.show',[$expense->employee_id]).'" datasalesman="'. $expense->employee->name .'">'. getEmployee($expense->employee_id)['name'].'</a>';
                }else{
                    $nestedData['AddedByName'] = '';
                }

                if(isset($expense->approved_by) && isset($expense->approvedBy->name)){
                    if(in_array($expense->approved_by,$allSup)){
                        $nestedData['ApprovedByName'] = '<a href="#" class="alert-modal" datasalesman="'. $expense->ApprovedByName .'">'. $expense->ApprovedByName.'</a>';
                    }else{
                        $nestedData['ApprovedByName'] = '<a href="'.domain_route('company.admin.employee.show',[$expense->ApprovedById]).'" datasalesman="'. $expense->ApprovedByName .'">'. $expense->ApprovedByName.'</a>';
                    }
                }else{
                    $nestedData['ApprovedByName'] = '';
                }      

                if(Auth::user()->isCompanyManager()){
                    if(Auth::user()->can("expense-status")) 
                        $modalClass = 'edit-modal';
                    else 
                        $modalClass = 'alert-modal';

                    $nestedData['status'] = '<a href="#" class="'.$modalClass.'" data-id="'.$expense->id.'" data-status="'.$expense->status.'" data-id="'.$expense->id.'"
                       data-status="'.$expense->status.'" data-remark="'.$expense->remark.'">';

                    if($expense->status =='Approved'){
                      $nestedData['status'] = $nestedData['status'].'<span class="label label-success">'. $expense->status.'</span>';
                    }elseif($expense->status =='Pending'){
                      $nestedData['status'] = $nestedData['status'].'<span class="label label-warning">'. $expense->status.'</span>';
                    }else{
                      $nestedData['status'] =$nestedData['status'].'<span class="label label-danger">'. $expense->status.'</span>';
                    }
                    $nestedData['status'] =$nestedData['status'].'</a>';                    
                }else{
                  if(Auth::user()->can("expense-status")) 
                    $modalClass = ((getEmployee($expense->employee_id)['superior'])==Auth::user()->EmployeeId())?'edit-modal':'alert-modal';
                  else
                    $modalClass = 'alert-modal';

                    if($expense->status =='Approved'){
                      $modalClass = 'alert-modal';
                      $nestedData['status'] ='<a href="#" class="'.$modalClass.'" data-id="'.$expense->id.'" data-status="'.$expense->status.'" data-remark="'.$expense->remark.'">';
                      $nestedData['status'] = $nestedData['status'].'<span class="label label-success">'. $expense->status.'</span>';
                    }elseif($expense->status =='Pending'){
                      $nestedData['status'] ='<a href="#" class="'.$modalClass.'" data-id="'.$expense->id.'" data-status="'.$expense->status.'" data-remark="'.$expense->remark.'">';
                      $nestedData['status'] = $nestedData['status'].'<span class="label label-warning">'. $expense->status.'</span>';
                    }else{
                      $modalClass = 'alert-modal';
                      $nestedData['status'] ='<a href="#" class="'.$modalClass.'" data-id="'.$expense->id.'" data-status="'.$expense->status.'" data-remark="'.$expense->remark.'">';
                      $nestedData['status'] = $nestedData['status'].'<span class="label label-danger">'. $expense->status.'</span>';
                    }
                  $nestedData['status'] = $nestedData['status'].'</a>';
                }

                if($expense->employee_type=="Admin"){
                  $datatype = 'data-type="Admin';
                }else{
                  $datatype ='';
                }

                $nestedData['action']='<a href="'.$show.'" class="btn btn-success btn-sm" style="    padding: 3px 6px;"><i class="fa fa-eye"></i></a>';
                if(Auth::user()->can('expense-update') && $expense->status=='Pending')
                $nestedData['action']=$nestedData['action'].'<a href="'.$edit.'" class="btn btn-warning btn-sm" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a>';
                if(Auth::user()->can('expense-delete') && $expense->status=='Pending')
                $nestedData['action']=$nestedData['action'].'<a class="btn btn-danger btn-sm delete" data-mid="'.$expense->id.'" data-url="'.domain_route('company.admin.expense.destroy', [$expense->id]) .'"
                       data-toggle="modal" data-target="#delete"'.$datatype .' style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>';
                $nestedData['ea'] = $expense->amount;
                $nestedData['e_date'] = getDeltaDate(Carbon::parse($expense->created_at)->format('Y-m-d'));
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

    public function custompdfdexport(Request $request){
      $getExportData = json_decode($request->exportedData)->data;
      $pageTitle = $request->pageTitle;
      set_time_limit ( 300 );
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      $pdf = PDF::loadView('company.expenses.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait', 'properties', 'columns');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }

    public function create()
    {
        $company_id = config('settings.company_id');
        $clients = Auth::user()->handleQuery('client')->where('status', 'Active')->orderBy('company_name', 'asc')->get();
        $expenseTypes = ExpenseType::where('company_id',$company_id)->orderBy('expensetype_name','ASC')->get();
        return view('company.expenses.create', compact('clients','expenseTypes'));
    }

    public function store(Request $request)
    {
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;

        $customMessages = [
            'employee_id.required' => 'The Employee Name field is required.',
            'logo.mimes' => 'Upload correct file type.',
        ];


        $this->validate($request, [
            'amount'            => 'required|numeric',
            'description'       => 'required',
            'expense_date'      => 'required|date_format:Y-m-d',
            'expense_photo[]'   => 'sometimes|nullable|image|max:500'
        ], $customMessages);


        $expense = new \App\Expense;
        $expense->company_id = $company_id;
        
        $expense->employee_id = Auth::user()->EmployeeId();
        $expense->employee_type = "Employee";

        $expense->amount = $request->get('amount');
        $expense->description = $request->get('description');
        // $expense->remark = $request->get('remark');
        $expense->status = 'Pending';
        $expense->expense_date = $request->expense_date;
        $expense->expense_type_id = $request->expense_type_id;
        if (!empty($request->get('client_id')))
            $expense->client_id = $request->get('client_id');
        if ($expense->save()) {
            $expense->images = "";
            $expense->image_paths = "";
            $tempImageIds       = array();
            $tempImageArray     = array();
            $tempImagePathArray = array();
            if ($request->file('expense_photo')) {

                foreach ($request->file('expense_photo') as $expense_photo) {
                    $image = $expense_photo;
                    $realname = pathinfo($expense_photo->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $image->getClientOriginalExtension();
                    $new_name = $realname . "-" . time() . '.' . $extension;
                    $image->storeAs('public/uploads/' . $companyName . '/expense/', $new_name);
                    $path = Storage::url('app/public/uploads/' . $companyName . '/expense/' . $new_name);
                    $imgId = DB::table('images')->insertGetId([
                        'type' => 'expense',
                        'type_id' => $expense->id,
                        'company_id' => $company_id,
                        'client_id' => $expense->client_id,
                        'image' => $new_name,
                        'image_path' => $path,
                    ]);

                    array_push($tempImageIds,(string)$imgId);
                    array_push($tempImageArray, $new_name);
                    array_push($tempImagePathArray, $path);
                }

            }
            if (!empty($expense->employee_id)) {
                if (!empty($expense->client_id)) {
                    $client = DB::table('clients')->where('id', $expense->client_id)->first();
                    $expense->company_name = empty($client) ? "" : $client->company_name;
                }
                $expense->image_ids = json_encode($tempImageIds);
                $expense->images = json_encode($tempImageArray);
                $expense->image_paths = json_encode($tempImagePathArray);
                $dataPayload = array("data_type" => "expense", "expense" => $expense, "action" => "add");
                $msgID = sendPushNotification_(getFBIDs($company_id, null, $expense->employee_id), 7, null, $dataPayload);
            }
        }

        return redirect()->route('company.admin.expense', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    public function show(Request $request)
    {
        $company_id = config('settings.company_id');
         $expense = Expense::where('id',$request->id)->first();
        //$expense = Auth::user()->handleQuery('expense')->where('id',$request->id)->first();
        if(!$expense)
          return redirect()->back()->with(['error'=>'Model Not Found']);
        $images = DB::table('images')->where('type', '=', 'expense')->where('type_id', $request->id)->get();

        if($expense->employee_type=="Admin"){
          $datatype = 'data-type="Admin';
        }else{
          $datatype ='';
        }
        
        $action = null;
        if(Auth::user()->can('expense-update') && $expense->status=='Pending')
          $action  = '<a class="btn btn-warning btn-sm edit" data-mid="'.$expense->id.'" href="'.domain_route('company.admin.expense.edit', [$expense->id]) .'" style="padding: 7px 6px;"><i class="fa fa-edit"></i>Edit</a>';
        if(Auth::user()->can('expense-delete') && $expense->status=='Pending')
          $action  = $action.'<a class="btn btn-danger btn-sm delete" data-mid="'.$expense->id.'" data-url="'.domain_route('company.admin.expense.destroy', [$expense->id]) .'" data-toggle="modal" data-target="#delete"'.$datatype .' style="padding: 7px 6px;"><i class="fa fa-trash-o"></i>Delete</a>';
        if(!$expense)
            return redirect()->back()->withErrors(['msg', 'No record Found']);
        return view('company.expenses.show', compact('expense', 'images', 'action'));
    }

    public function edit(Request $request)
    {
        $company_id = config('settings.company_id');
        $expense = Expense::where('id',$request->id)->first();
        if($expense->status!="Pending") return redirect()->back();
        $clients = Auth::user()->handleQuery('client')->where('status', 'Active')->orderBy('company_name', 'asc')->get();
        $expenseTypes = ExpenseType::where('company_id',$company_id)->orderBy('expensetype_name','ASC')->get();
        $images = DB::table('images')->where('type', '=', 'expense')->where('type_id', $request->id)->get();
        $image_count = $images->count();
        if ($expense)
            return view('company.expenses.edit', compact('expense', 'clients', 'images', 'image_count','expenseTypes'));
        else
            return redirect()->back()->withErrors(['msg', 'No record Found']);
    }

    public function update(Request $request)
    {
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $expense = Expense::where('id',$request->id)->first();
        Session::flash('DT_Exp_filters', $request->DT_Exp_FILTER);
        if(!$expense){
            return redirect()->back()->withErrors(['msg', 'No record Found']);
        }
        $customMessages = [
            'employee_id.required' => 'The Employee Name field is required.',
        ];

        $this->validate($request, [
            'amount' => 'required|numeric',
            'description' => 'required',
            'expense_date' => 'required|date_format:Y-m-d',
            'expense_photo[]' => 'sometimes|nullable|image|max:500'
        ], $customMessages);
        

        $urlnew = $request->previous_url;
        $expense->amount = $request->get('amount');
        $expense->description = $request->get('description');
        // $expense->remark = $request->get('remark');
        $expense->expense_date = $request->expense_date;
        $expense->expense_type_id = $request->expense_type_id;
        $expense->client_id = $request->client_id;
        
        $tempImageIds       = array();
        $tempImageArray     = array();
        $tempImagePathArray = array();
        if ($expense->save()) {
            if(!empty($request->get('expense_photo_id'))){
              DB::table('images')->where('company_id',$company_id)->where('type','expense')->where('type_id',$expense->id)->whereNotIn('id',$request->get('expense_photo_id'))->delete();    
              $other_images = DB::table('images')->where('company_id',$company_id)->where('type','expense')->where('type_id',$expense->id)->get(['image', 'image_path', 'id']);
              if($other_images->first()){
                foreach($other_images as $other_image){
                  array_push($tempImageIds,(string)$other_image->id);
                  array_push($tempImageArray, $other_image->image);
                  array_push($tempImagePathArray, $other_image->image_path);
                }        
              }
            }else{
                DB::table('images')->where('company_id',$company_id)->where('type','expense')->where('type_id',$expense->id)->delete(); 
            }
            if ($request->file('expense_photo')) {
                foreach ($request->file('expense_photo') as $expense_photo) {
                    $image = $expense_photo;
                    $realname = pathinfo($expense_photo->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $image->getClientOriginalExtension();
                    $new_name = $realname . "-" . time() . '.' . $extension;
                    $image->storeAs('public/uploads/' . $companyName . '/expense/', $new_name);
                    $path = Storage::url('app/public/uploads/' . $companyName . '/expense/' . $new_name);
                    $imgId = DB::table('images')->insertGetId([
                        'type' => 'expense',
                        'type_id' => $expense->id,
                        'company_id' => $company_id,
                        'client_id' => $expense->client_id,
                        'image' => $new_name,
                        'image_path' => $path,
                    ]);
                    array_push($tempImageIds,(string)$imgId);
                    array_push($tempImageArray, $new_name);
                    array_push($tempImagePathArray, $path);
                }
            }

            if (!empty($expense->client_id)) {
                $client = DB::table('clients')->where('id', $expense->client_id)->first();
                $expense->company_name = empty($client) ? "" : $client->company_name;
            }

            $expense->image_ids = json_encode($tempImageIds);
            $expense->images = json_encode($tempImageArray);
            $expense->image_paths = json_encode($tempImagePathArray);

            $employee = Employee::findOrFail($expense->employee_id);
            $notificationData = array(
                "company_id" => $employee->company_id,
                "employee_id" => $employee->id,
                "data_type" => "expense",
                "data" => "",
                "action_id" => $expense->id,
                "title" => "Expense " . $expense->status,
                "description" => "Your Expense has been Updated",
                "created_at" => date('Y-m-d H:i:s'),
                "status" => 1,
                "to" => 1,
                "unix_timestamp" => time()
            );
            $dataPayload = array("action" => "update", "data_type" => "expense", "expense" => $expense);
            $sent = sendPushNotification_([$employee->firebase_token], 7, $notificationData, $dataPayload);
        }
        session()->flash('success', 'Expense Updated Successfully');

        $newprevious=explode('/',$request->previous_url);
        if($newprevious[4]=='employee' || $newprevious[4]=='client'){
            $previous = $request->previous_url;
                return redirect($previous);
        }else{
            return redirect()->route('company.admin.expense', ['domain' => domain()])->with('success', 'Information has been  Updated');
        }
    }

    public function changeStatus(Request $request)
    {
        $company_id = config('settings.company_id');
        $expense = Auth::user()->handleQuery('Expense')->where('id',$request->expense_id)->first();
        $expense->status = $request->status;
        $expense->remark = $request->remark;
        if ($expense->status == 'Approved' || $expense->status == 'Rejected' || $expense->status == "Pending") {
            if($request->status=='Approved' || $request->status=="Rejected"){
              $expense->approved_by =  Auth::user()->EmployeeId();        
            }else{
              $expense->approved_by=null;
            }
            $employee = Employee::findOrFail($expense->employee_id);
            $notificationData = array(
                "company_id" => $employee->company_id,
                "employee_id" => $employee->id,
                "data_type" => "expense",
                "data" => "",
                "action_id" => $expense->id,
                "title" => "Expense " . $request->status,
                "description" => "Your Expense has been " . $request->status,
                "created_at" => date('Y-m-d H:i:s'),
                "status" => 1,
                "to" => 1,
                "unix_timestamp" => time()
            );

            $dataPayload = array("action" => "update_status", "expense_id" => $expense->id, "status" => $expense->status, "remark" => $expense->remark, "employee_id" => $employee->id,'approved_by'=>$expense->approved_by);
            $sent = sendPushNotification_([$employee->firebase_token], 7, $notificationData, $dataPayload);
        }        
        $expense->save();
        return back();
    }

    public function destroy($domain,Request $request,$id)
    {
        $company_id = config('settings.company_id');
        $expense = Auth::user()->handleQuery('Expense',$id)->first();
        if($expense->status!="Pending") return redirect()->back();
        if (!empty($expense)) {
            $deleted = $expense->delete();
            if ($deleted && $request->account_type!="Admin") {
                $employee = Employee::findOrFail($expense->employee_id);
                $sendingExpense = $expense;
                $dataPayload = array("data_type" => "expense", "expense" => $sendingExpense, "action" => "delete", "expense_id" => $sendingExpense->id);
                $msgID = sendPushNotification_([$employee->firebase_token], 7, null, $dataPayload);
            }
        }
        \Session::flash('success', 'Expense has been deleted.');
        if($request->has('prev_url')) return redirect()->to($request->prev_url);
        return back();
    }
}
