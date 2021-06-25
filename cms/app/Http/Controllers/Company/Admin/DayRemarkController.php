<?php

namespace App\Http\Controllers\Company\Admin;

use DB;
use App;
use Log;
use Auth;
use Session;
use App\Employee;
use App\DayRemark;
use Carbon\Carbon;
use App\DayRemarkDetail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class DayRemarkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dayremark-create', ['only' => ['store']]);
        $this->middleware('permission:dayremark-view');
        $this->middleware('permission:dayremark-update', ['only' => ['update']]);
        $this->middleware('permission:dayremark-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_id = config('settings.company_id');
        $employeesWithDayRemarks = Auth::user()->handleQuery('employee')->whereHas('dayremarks')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        $dayRemarksCount =  DayRemark::where('company_id', $company_id)
                            ->whereIn('employee_id', array_keys($employeesWithDayRemarks))
                            ->orderBy('created_at', 'desc')
                            ->count();
        return view('company.dayremarks.index', compact('dayRemarksCount', 'employeesWithDayRemarks'));
    }

    public function ajaxDatatable(Request $request){
        $columns = array(
            0 => 'id',
            1 => 'employee_name',
            2 => 'remark_date',
            3 => 'remarks',
            4 => 'action',
        );

        $auth_id = Auth::user()->EmployeeId();

        $company_id = config('settings.company_id');
        $empVal = $request->empVal;
        $start_date = $request->startDate;
        $end_date = $request->endDate;
        $search = $request->input('search')['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $juniors = Employee::EmployeeChilds($auth_id,array());

        $prepQuery = DayRemark::select('day_remarks.id', 'day_remarks.employee_id', 'day_remarks.remark_date', 'day_remarks.remark_datetime', 'day_remarks.remarks', 'employees.name as employee_name')
                ->leftJoin('employees', 'employees.id', 'day_remarks.employee_id')
                ->where('day_remarks.company_id',$company_id)
                ->whereBetween('day_remarks.remark_date', [$start_date, $end_date]);
        if(!(Auth::user()->isCompanyManager())){
            $prepQuery = $prepQuery->whereIn('employee_id',$juniors);
        }

        if(!empty($empVal)){
            $empFilterQuery =  $prepQuery;
            $prepQuery = $empFilterQuery->whereIn('day_remarks.employee_id', $empVal);
        }

        if(!empty($search)){
            $searchQuery = $prepQuery;
            $prepQuery = $searchQuery->where(function($query) use ($search){
                $query->orWhere('day_remarks.remarks' ,'LIKE', "%{$search}%");
                $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
            });
        }

        $totalData =  $prepQuery->count();
        $totalFiltered = $totalData;

        $data = array();
        $dayremarks =   $prepQuery->orderBy($order,$dir)->offset($start)
                        ->limit($limit)
                        ->get();
        if (!empty($dayremarks)) {
            $i = $start;
            foreach ($dayremarks as $dayremark) {
                $id = $dayremark->id;
                $employee_name = $dayremark->employee_name;
                $dayremark_date = $dayremark->remark_date;
                $dayremark_time = date("H:i A", strtotime($dayremark->remark_datetime));
                $employee_show = domain_route('company.admin.employee.show',[$dayremark->employee_id]);
                $show = domain_route('company.admin.remark.show', [$id]);

                $nestedData['id'] = ++$i;
                $nestedData['employee_name'] = "<a href='{$employee_show}' datasalesman='$dayremark->employee_name}'> {$dayremark->employee_name}</a>";
                $dayremark_count=$dayremark->remarkdetails->count()-1;

                    if($dayremark_count>0) {
                         $badge = '<span class=" badge label bg-teal pull-right" >'.'<a style="color:white!important;" href="'.$show.'" >' . $dayremark_count .' '.'more </a>' ;
                    }
                    else {
                         $badge = NULL;
                        }
                 $nestedData['remarks'] = strip_tags(utf8_decode( str_limit($dayremark->remarks, 300,'...'))).$badge ;    

                // $nestedData['remarks'] =utf8_decode(getShortName(utf8_encode($dayremark->remarks), 300 ))  .'<span class=" badge label bg-teal pull-right" > <a style="color:white!important;" href="'.$show.'" > '.$dayremark->remarkdetails->count().'  '.'more</a>';
                $nestedData['remark_date'] = getDeltaDate($dayremark_date);

                $nestedData['action'] = '';
                if(Auth::user()->can('dayremark-view')){
                    $nestedData['action'] = $nestedData['action'].'<a href="'.$show.'" class="btn btn-success btn-sm" style="padding: 3px 6px;"><i class="fa fa-eye"></i></a>';
                }

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
        $columns = json_decode($request->columns);
        $properties = json_decode($request->properties);
        set_time_limit ( 300 );
        $pdf = PDF::loadView('company.dayremarks.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait');
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
        try{
            DB::BeginTransaction();
            $remark = trim($request->parsed_remark);
            if(strlen($remark) == 0 ){
              throw ValidationException::withMessages(['parsed_remark' => 'The remark field is required.']);
            }
            $customMessage = [
              'parsed_remark.*' => "The remark field is required."
            ];
            $this->validate($request, [
              'parsed_remark' => 'required|string|min:1',
            ], $customMessage);

            $emp_id = Auth::user()->EmployeeId();
            $company_id = config('settings.company_id');

            $dayremark = Dayremark::where('company_id',$company_id)->where('employee_id',$emp_id)->where('remark_date',date('Y-m-d'))->first();
            if(!$dayremark){
                $dayremark  = new DayRemark;
                $dayremark->company_id  = $company_id;
                $dayremark->employee_id = $emp_id;                
            }
            $dayremark->remarks              = $request->remark;
            $dayremark->remark_date          = $request->start_date;
            $dayremark->remark_date_unix     = date('m/d/Y H:i:s', time());
            $dayremark->remark_datetime      = date("Y-m-d H:i:s");
            $dayremark->remark_datetime_unix = $dayremark->remark_date_unix;
            $dayremark->save();

            $remarkDetail = new DayRemarkDetail;
            $remarkDetail->remark_id      = $dayremark->id;
            $remarkDetail->company_id     = $company_id;
            $remarkDetail->remark_details = $dayremark->remarks;
            $remarkDetail->save();
            
            DB::commit();

            $remarkDetail->employee_id    = $dayremark->employee_id;
            $remarkDetail->remark_date = $dayremark->remark_date;
            $remarkDetail->remark_date_unix = $dayremark->remark_date_unix;
            $remarkDetail->remark_datetime = $dayremark->remark_datetime;
            $remarkDetail->remark_datetime_unix = $dayremark->remark_datetime_unix;

            $fbIds = Employee::where('id',$dayremark->employee_id)->pluck('firebase_token');

            $dataPayload = array("data_type" => "dayremark","dayremark_details"=>$remarkDetail,"action" => "add");
            $msgID = sendPushNotification_($fbIds, 35, null, $dataPayload);


            return redirect()->back()->with(['success'=>'Dayremark Added Successfully']);

        }catch(Exception $e)
        {
            DB::rollback();
            return redirect()->back()->with(['error'=>'Server Error please contact developer']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\DayRemark $DayRemark
     * @return \Illuminate\Http\Response
     */
    public function show($domain,$id)
    {
        $authEmp = Auth::user()->EmployeeId();
        $company_id = config('settings.company_id');
        $dayremark = DayRemark::where('company_id',$company_id)->where('id',$id)->first();
        if(Auth::user()->isCompanyManager()){
            $dayremark = DayRemark::where('company_id',$company_id)->where('id',$id)->first();
        }else{
            $juniors = Employee::EmployeeChilds($authEmp,array());
            $dayremark = DayRemark::where('company_id',$company_id)->where('id',$id)->whereIn('employee_id',$juniors)->first();
        }
        if(!$dayremark)
            return redirect()->back()->with(['error'=>'No Dayremark found or no permission access to view this day remark.']);
        $dayremarksOnDate = DayRemarkDetail::where('remark_id',$dayremark->id)->orderBy('created_at','DESC')->get();
        return view('company.dayremarks.show', compact('dayremark','dayremarksOnDate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\DayRemark $DayRemark
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
     * @param \App\DayRemark $DayRemark
     * @return \Illuminate\Http\Response
     */
    public function update($domain,Request $request)
    {
      $remark = trim($request->parsed_remark);
      if(strlen($remark) == 0 ){
        return response()->json([
          'message' => "The given data was invalid.",
          'errors' => array(
            "remark" => ["The remark field is required."],
          ),
        ], 422);
      }
      $this->validate($request, [
        'parsed_remark' => 'required|string|min:1',
      ]);
        $authEmpId = Auth::user()->EmployeeId();
        $company_id = config('settings.company_id');
        $dayremark  = DayRemarkDetail::where('company_id',$company_id)->where('id',$request->id)->first();
        if(!$dayremark)
            return response(['status'=>false,'message'=>'No DayRemark Found or you may not have access to update it.']);

        $dayremark->remark_details = $request->remark;
        $dayremark->save();

        $latestDayremark = DayRemarkDetail::where('company_id',$company_id)->where('remark_id',$dayremark->remark_id)->orderBy('id','DESC')->first();

        $remarkHeader = DayRemark::where('id',$dayremark->remark_id)->first();
        // if($latestDayremark->remark_id == $dayremark->remark_id){
            $remarkHeader->remarks = $latestDayremark->remark_details;
            $saved = $remarkHeader->update();     
        // }

        $dayremark->employee_id = $remarkHeader->employee_id;

        $updated_at  = getDeltaDate(Carbon::parse($dayremark->updated_at)->format('Y-m-d'));
        $remark_time = $dayremark->remark_details.
                    '<span class="updated_time">( last updated at: '.$updated_at.
                    '&nbsp;'.Carbon::parse($dayremark->updated_at)->format('h:i A').')</span><span class="pull-right">'.
                    Carbon::parse($dayremark->created_at)->format('h:i A').'</span>';

        $fbIds = Employee::where('id',$remarkHeader->employee_id)->pluck('firebase_token');

        $dayremark->remark_id      = $request->id;
        $dayremark->remark_details = $request->remark;

        $dataPayload = array("data_type" => "dayremark","dayremark_details"=>$dayremark,"action" => "update");

        $msgID = sendPushNotification_($fbIds, 35, null, $dataPayload);


        return response([
                      'status'=>true,
                      'message'=>'Dayremark Updated Successfullly',
                      'remark_time'=>$remark_time, 
                      "len" => $remark]);
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param \App\DayRemark $DayRemark
     * @return \Illuminate\Http\Response
     */
    public function destroy($domain,Request $request)
    {
        $authEmpId = Auth::user()->EmployeeId();
        $company_id = config('settings.company_id');

        $dayremarkDetail = DayRemarkDetail::where('company_id',$company_id)->where('id',$request->id)->first();
        if(!$dayremarkDetail)
            return response(['status'=>false,'message'=>'No Dayremark found or no permission access to delete this day remark.']);  

        $dayremarkDetail->delete();
        $remarksCount = DayRemarkDetail::where('remark_id',$dayremarkDetail->remark_id)->count();
        $dayremark = DayRemark::where('id',$dayremarkDetail->remark_id)->first();
        if(!$dayremark)
            return response(['status'=>false,'message'=>'No Dayremark found or no permission access to delete this day remark.']);
        if($remarksCount<1){
            $dayremark->delete();
            return response(['status'=>true,'exists'=>false,'message'=>'DayRemark Deleted']);
        }else{
            $dayremarkDetailLatest = DayRemarkDetail::where('company_id',$company_id)->where('remark_id',$dayremark->id)->orderBy('id','DESC')->first();
            $dayremark->remarks = $dayremarkDetailLatest->remark_details;
            $dayremark->save();
        }
        $dayremarkDetail->delete();
        $fbIds = Employee::where('id',$dayremark->employee_id)->pluck('firebase_token');

        $dataPayload = array("data_type" => "dayremark", "dayremark_details"=>$request->id,"action" => "delete");
        $msgID = sendPushNotification_($fbIds, 35, null, $dataPayload);
        return response(['status'=>true,'exists'=>true,'message'=>'DayRemark Deleted']);
    }
}