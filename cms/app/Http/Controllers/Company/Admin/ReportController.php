<?php

namespace App\Http\Controllers\Company\Admin;


use DB;
use Log;
use URL;
use Mail;
use Excel;
use Storage;
use App\User;
use DateTime;
use App\Leave;
use App\Order;
use App\Client;
use App\Outlet;
use DatePeriod;
use App\Holiday;
use App\Meeting;
Use Auth;
use App\NoOrder;
use App\Product;
use App\Category;
use App\Employee;
use App\Location;
use DateInterval;
use DateTimeZone;
use App\UnitTypes;
use Carbon\Carbon;
use App\Attendance;
use App\Collection;
use App\GenerateReport;
use App\ModuleAttribute;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;

Builder::macro('if', function ($condition, $column, $operator, $value) {
    if ($condition) {
        return $this->where($column, $operator, $value);
    }

    return $this;
});

class ReportController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */

  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware('permission:report-view');
  }

  public function routemap()
  {
    return view('company.reports.routemap');
  }

  /*** 
   * Salesman GPS Path 
  ***/

  public function employeerattendancegps(Request $request){
    if(config('settings.gpsreports')==0 || !(Auth::user()->can('salesmangps-view'))){
      return redirect()->back();
    }
    if(!Auth::user()->isCompanyManager()){
      $loggedInEmployeeId = Auth::user()->EmployeeId();
      $employeesWithAttendances = Auth::user()->handleQuery('employee')->with('employee_attendances')->where('id', '<>', $loggedInEmployeeId)->pluck('name', 'id')->toArray();
    }else{
      $employeesWithAttendances = Auth::user()->handleQuery('employee')->with('employee_attendances')->pluck('name', 'id')->toArray();

    }
    
    return view('company.reports.employeeattendancegps', compact('employeesWithAttendances'));
  }

  public function salesmanGPSPathDatatable(Request $request){
    $columns = array(
      'id',
      'name',
      'check_datetime',
      'processed_path',
      'raw_path',
      'path_details',
    );

    $company_id = config('settings.company_id');
    $empVal = $request->empVal;
    $start_date = $request->startDate;
    $end_date = $request->endDate;
    $search = $request->input('search')['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    $loggedInEmployeeId = Auth::user()->EmployeeId();

    $countQuery = $prepQuery = Auth::user()->handleQuery('employee')->select("employees.name", "employees.status", "attendances.employee_id", "attendances.check_datetime")
                    ->join("attendances", "attendances.employee_id", "=", "employees.id")
                    ->where('employees.status', 'Active')
                    ->whereBetween(DB::raw("DATE(attendances.check_datetime)"), [$start_date, $end_date])
                    ->groupBy(DB::raw("DATE(attendances.check_datetime)"), "attendances.employee_id");

    if (!Auth::user()->isCompanyManager()) {
      $countQuery = $prepQuery = $prepQuery->where('employees.id', '<>', $loggedInEmployeeId);
    }

    if(!empty($empVal)){
      $empFilterQuery =  $prepQuery;
      $prepQuery = $empFilterQuery->where('attendances.employee_id', $empVal);
    }
    
    if(!empty($search)){
      $searchQuery = $prepQuery;
      $prepQuery = $searchQuery->where(function($query) use ($search){
                    $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
                    if (config('settings.ncal')==0) {
                      $query->orWhere((DB::raw("DATE_FORMAT(attendances.adate, '%e %b %Y')")), 'LIKE', "%{$search}%");
                      $query->orWhere((DB::raw("DATE_FORMAT(attendances.adate, '%e %b')")), 'LIKE', "%{$search}%");
                      $query->orWhere((DB::raw("DATE_FORMAT(attendances.adate, '%b %Y')")), 'LIKE', "%{$search}%");
                    }
                  });
    }

    $totalData =  $countQuery->get()->count();
    if($limit==-1) 
      $limit = $totalData;
    
    $totalFiltered = $totalData;
    
    $data = array();
    $attendances = $prepQuery->orderBy($order,$dir)->offset($start)
                          ->limit($limit)
                          ->get();

    if (!empty($attendances)) {
        $i = $start;
        foreach ($attendances as $attendance) {
          $id = $attendance->id;
          $employee_id = $attendance->employee_id;
          $employee_name = ucfirst($attendance->name);
          $employee_show = domain_route('company.admin.employee.show',[$employee_id]);
          $check_date = date('Y-m-d', strtotime($attendance->check_datetime));
          $ndate = (config('settings.ncal')==0)?$check_date:getDeltaDateFormat($check_date);
          $date = getDeltaDate($check_date);
          $nestedData['id'] = ++$i;
          $nestedData['name'] = "<a href='{$employee_show}' datasalesman='{$employee_name}'> {$employee_name}</a>";
          $nestedData['check_datetime'] = $date;

          // $nestedData['processed_path'] = "<a class='btn btn-primary mapgenerate' data-gps_type='py_processed' data-rowid={$i} data-date='{$check_date}' data-ndate='{$ndate}' data-eid='{$employee_id}' data-ename='{$employee_name}' data-toggle='tooltip' title='Gps Locations'><i class='fa fa-map-marker'></i><i class='fa fa-spinner fa-pulse fa-fw' style='display:none;'></i></a>";
          $nestedData['processed_path'] = "<a class='btn btn-primary mapgenerate' data-gps_type='py_processed_path' data-rowid={$i} data-date='{$check_date}' data-ndate='{$ndate}' data-eid='{$employee_id}' data-ename='{$employee_name}' data-toggle='tooltip' title='Path'><i class='fa fa-map-marker' style='color:red'></i><i class='fa fa-spinner fa-pulse fa-fw' style='display:none;'></i></a>";

          $nestedData['raw_path'] = "<a class='btn btn-primary raw_location' data-gps_type='raw' data-rowid={$i}    data-date='{$check_date}' data-ndate='{$ndate}' data-eid='{$employee_id}' data-ename='{$employee_name}' data-toggle='tooltip' title='Raw GPS Locations'><i class='fa fa-map-marker'></i><i class='fa fa-spinner fa-pulse fa-fw' style='display:none;'></i></a>";
          
          $nestedData['path_details'] = "<a class='btn btn-success btn-sm hourdetail2' data-mid='{$employee_id}' data-mdate='{$check_date}' data-employee_name='{$employee_name}' data-row_date='{$ndate}' style='padding: 3px 6px;' data-toggle='tooltip' title='Working Hours/Traveled (KM)'><i class='fa fa-eye'></i></a>";

          if(env('APP_ENV') == 'development'){
            // $nestedData['processed_path'] .="<a class='btn btn-primary mapgenerate' data-gps_type='raw' data-rowid={$i}    data-date='{$check_date}' data-ndate='{$ndate}' data-eid='{$employee_id}' data-ename='{$employee_name}' data-toggle='tooltip' title='Raw GPS Locations'><i class='fa fa-map-marker'></i><i class='fa fa-spinner fa-pulse fa-fw' style='display:none;'></i></a>
            // <a class='btn btn-primary mapgenerate' data-gps_type='path' data-rowid={$i} data-date='{$check_date}' data-ndate='{$ndate}' data-eid='{$employee_id}' data-ename='{$employee_name}' data-toggle='tooltip' title='Raw Path'><i class='fa fa-map-marker' style='color:red'></i><i class='fa fa-spinner fa-pulse fa-fw' style='display:none;'></i></a>";
            $filepath=url('cms/storage/app/locations/company_').$company_id."/".$check_date."/empyee_".$employee_id.".json";
            $devMapDisplay = "<a class='btn btn-success btn-sm' href='".$filepath."' target='_blank'>Raw File</a>";
            $nestedData['path_details'] .= $devMapDisplay;
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

  
  public function getRawLocation(Request $request){
    $company_id = config('settings.company_id');
    $employee_id = $request->eid;
    $date =  $request->mapdate;
    $fileName = getFileName($company_id, $employee_id, $date);
    $exists = Storage::disk("local")->exists($fileName);
    if(!$exists) {
      $msg = "GPS could not be captured. Possible Reasons:\n 1. Low Battery\n 2. Short check-in duration\n 3.GPS off\n 4. Phone went into optimization mode to save battery";
      return response()->json([
        'status' => 404,
        'message' => $msg,
      ]);
    }elseif($exists){
      $fileContents = Storage::get($fileName);
      $decodedContents = empty($fileContents) ? array() : json_decode($fileContents, true);
      if(empty($decodedContents)){
        $msg = "GPS could not be captured. Possible Reasons:\n 1. Low Battery\n 2. Short check-in duration\n 3.GPS off\n 4. Phone went into optimization mode to save battery";
        return response()->json([
          'status' => 404,
          'message' => $msg,
        ]);
      }else{
        $accurate_points = array();
        $attendances = $this->getEmployeeAttendances($company_id,$employee_id ,$date);
        $worked_hours_details = $this->getCheckedInMinutes($attendances); 
        $minutes_checked_in = $worked_hours_details['worked_hours'];
        $expected_points = $minutes_checked_in * 4;
        foreach($decodedContents as $fileContent){
          if(ceil($fileContent['accuracy']) > 0 && ceil($fileContent['accuracy']) < 60) array_push($accurate_points, $fileContent);
        }
        /**
         * ceil(num of $accurate_points * 100 / $expected_points)
         * 70%+ => good, 40~70% => average, <40% => poor 
         */
        try{
          $num_accurate_points = ceil( (sizeof($accurate_points) * 100) / $expected_points ); 
        }catch(\Exception $e){
          $num_accurate_points = 0; 
          return response()->json([
            'status' => 404,
            'message' => "GPS could not be captured. Possible Reasons:\n 1. Low Battery\n 2. Short check-in duration\n 3.GPS off\n 4. Phone went into optimization mode to save battery",
            'attendances' => $attendances,
          ]);
        }
        
        
        $data_received = $expected_points > 0 ? ceil(( sizeof($decodedContents) / $expected_points ) * 100) : 0;
        $percent_accurate_points = ceil( (sizeof($accurate_points) / sizeof($decodedContents) ) * 100);
      
        $party_locations =$this->getEmpPartyLocation($employee_id);
        
        return response()->json([
          'status' => 200,
          'message' => 'success',
          'minutes_checked_in' => $minutes_checked_in,
          'is_checked_in' => $worked_hours_details['is_checked_in'],
          'accuracy' => $num_accurate_points,
          'dataReceived' => $data_received,
          'percentAccuratePoints' => $percent_accurate_points,
          'locations' => $decodedContents,
          'partyLocations' => array()//$party_locations,
        ]);
      }
    }

    return json_encode($data);
  }

  private function getEmployeeAttendances($compid, $eid, $date){
    $attendances = Employee::select("employees.name", "employees.status", "attendances.employee_id", "attendances.check_datetime", "attendances.check_type")
                  ->join("attendances", "attendances.employee_id", "=", "employees.id")
                  ->where('attendances.employee_id', $eid)
                  ->where('employees.status', 'Active')
                  ->where('employees.company_id', $compid)
                  ->where('attendances.check_datetime', '>=', $date . ' 00:00:00')
                  ->where('attendances.check_datetime', '<=', $date . ' 23:59:59')
                  ->orderBy('check_datetime', 'asc')
                  ->get()->toArray();
    $newatten = array();
    $i = 0;
    $company_timezone = config('settings.time_zone');
    if(!$company_timezone) $company_timezone = "Asia/Kathmandu"; 
    
    foreach ($attendances as $attendance) {
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
          $current_datetime = new DateTime("now", new DateTimeZone($company_timezone));
          $newatten[$i]['checkout'] = $current_datetime->format('Y-m-d H:i:s');
        } else {
          $newatten[$i]['checkout'] = $date . ' 23:59:59';
        }
      }
    }
    return $newatten;
  }

  private function getCheckedInMinutes($empAttendances){
    $hr = 0;
    $min = 0;
    $sec = 0;
    $isCheckedIn = false;
    $finalArray = array();
    foreach ($empAttendances as $key => $attendance) {
      $strtime1 = strtotime($attendance['checkin']);
      $strtime2 = strtotime($attendance['checkout']);
      $timeDateTime1 = new DateTime($attendance['checkin']);
      $timeDateTime2 = new DateTime($attendance['checkout']);
      $time_diff = $timeDateTime2->diff($timeDateTime1);
      
      $hr += $time_diff->h;
      $min += $time_diff->i;
      $sec += $time_diff->s;
      if(array_key_exists('checkout_state', $attendance)){
        if($attendance['checkout_state'] == 'current') $isCheckedIn = true;
      }

      $tempArray = array();

      $dateTime1 = strtotime($strtime1);
      $dateTime2 = strtotime($strtime2);

      $cinTime = substr($strtime1, 11);
      $coutTime = substr($strtime2, 11);

      $time1 = $dateTime1 * 1000;
      $time2 = $dateTime2 * 1000;
      
      $tempArray["checkin"] = $time1;
      $tempArray["checkout"] = $time2;
      $tempArray["cin_time"] = $cinTime;
      $tempArray["cout_time"] = $coutTime;
      array_push($finalArray,$tempArray);
    }  

    if( $hr > 0 ) {
      $min += $hr * 60;
    }
    if($sec > 0){
      $min += 1 / $sec; 
    }

    $data = array('worked_hours' => ceil($min), 'is_checked_in' => $isCheckedIn, "distance_travelled" => $finalArray); 

    return $data;
  }
  /*** 
   * Monthly Attendance Report
  ***/
  function monthlyattendancereport(){
      if(config('settings.monthly_attendance')==0 || !(Auth::user()->can('monthly-attendance-view'))){
        return redirect()->back();
      }
      $company_id = config('settings.company_id');
      $getNepDate = getMthYrDeltaDate(date('Y-m-d'));
      $getMinAttendanceDate = Attendance::where('company_id', $company_id)
                              ->where('check_type', 1)
                              ->min('check_datetime');
      return view('company.reports.monthlyattendancereport',compact('getNepDate', 'getMinAttendanceDate'));
  }

  function getmonthlyattendancereport(Request $request){
    $company_id = config('settings.company_id');
    $dates = $request->format_date;
    $today = date('Y-m-d');

    $employeeWithAttendances = Auth::user()->handleQuery('attendance')->has('employees')->whereIn('adate', $dates)->distinct('employee_id')->pluck('employee_id')->toArray();

    $employeeWithLeaves = Auth::user()->handleQuery('leave')->has('employee')->where('status', 'Approved')->where('end_date', '<=',$dates[sizeof($dates)-1])->where('end_date', '>=',$dates[0])->distinct('employee_id')->pluck('employee_id')->toArray();
  
    $employeeWithAttendancesandLeaves = array_unique(array_merge($employeeWithAttendances, $employeeWithLeaves));

    $employees_only = Auth::user()->handleQuery('employee')->select("employees.id", "employees.company_id", "employees.name", "employees.status", "employees.created_at")
                                ->where('employees.company_id', $company_id)
                                ->whereIn('employees.id', $employeeWithAttendancesandLeaves)
                                ->orderby('employees.name','asc')
                                ->get();
    if($employees_only->first()){
        if(config('settings.ncal')==0){
          $holidays = Holiday::where('company_id', $company_id)
                      ->whereYear('start_date', $request->year)
                      ->whereMonth('start_date', $request->month)
                      ->where('name', '<>','Weekly Off')
                      ->pluck('end_date','start_date');
        }else{
          $holidays = Holiday::where('company_id', $company_id)
                      ->whereIn('start_date', $dates)
                      ->where('name', '<>','Weekly Off')
                      ->pluck('end_date','start_date');
        }
        $holidays_list = array();
        if (!empty($holidays)) {
          foreach ($holidays as $start_date => $end_date) {
            if ($start_date == $end_date) {
              $holidays_list[] = $start_date;
            } else {
              $start_date = strtotime($start_date);
              $end_date = strtotime($end_date);
              for ($currdate = $start_date; $currdate <= $end_date; $currdate += (86400)) {
                $holidays_list[] = date('Y-m-d', $currdate);
              }
            }
          }
        }

        if(config('settings.ncal')==0){
          $weekly_off = Holiday::where('company_id', $company_id)
                          ->whereYear('end_date', $request->year)
                          ->whereMonth('end_date', $request->month)
                          ->where('name', '=', 'Weekly Off')
                          ->pluck('start_date')
                          ->toArray();
        }else{
          $weekly_off = Holiday::where('company_id', $company_id)
                        ->whereIn('end_date', $dates)
                        ->where('name', '=', 'Weekly Off')
                        ->pluck('start_date')
                        ->toArray();
        }

        foreach ($employees_only as $employee) {
          $arr = [];
          if(config('settings.ncal')==0){
              $employee_attendance = Auth::user()->handleQuery('attendance')->select("company_id", "employee_id", "adate as check_datetime", "check_type")
                                      ->whereIn('adate', $dates)
                                      ->where('check_type', 1)
                                      ->where('employee_id', $employee->id)
                                      ->get();
          }else{
              $employee_attendance = Auth::user()->handleQuery('attendance')->select("company_id", "employee_id", "adate as check_datetime", "check_type")
                                      ->whereIn('adate', $dates)
                                      ->where('check_type', 1)
                                      ->where('employee_id', $employee->id)
                                      ->get();
          }
            
          if(config('settings.ncal')==0){
            $leave_days = Auth::user()->handleQuery('leave')->where('employee_id', $employee->id)
                            // ->whereYear('start_date', $request->year)
                            ->where('end_date', '>=', $dates[0])
                            ->where('end_date', '<=', $dates[sizeof($dates)-1])
                            ->where('status','Approved')
                            ->pluck('end_date', 'start_date');
          }else{
              $leave_days = Auth::user()->handleQuery('leave')
                              ->where('employee_id', $employee->id)
                              ->where('end_date', '>=', $dates[0])
                              ->where('end_date', '<=', $dates[sizeof($dates)-1])
                              ->where('status','Approved')
                              ->pluck('end_date', 'start_date');
          }

          $leave_dates_list = array();
          foreach($leave_days as $start_date=>$end_date){
            if($start_date == $end_date){
              array_push( $leave_dates_list, $start_date );
            }
            else{
              $start_date = strtotime($start_date);
              $end_date = strtotime($end_date);
              for ($currdate = $start_date; $currdate <= $end_date; $currdate += (86400)) {
                if(!(in_array($currdate, $holidays_list))){
                  array_push( $leave_dates_list, date('Y-m-d',$currdate) );
                }
              }
            }
          }

          $employee_group_attendance = $employee_attendance->groupBy('check_datetime');
          foreach ($employee_group_attendance as $key => $value) {
            $arr[] = $key;
          }
          $employee_created = Carbon::parse($employee->created_at)->format('Y-m-d');
          foreach ($dates as $r) {
            if (empty($arr)) {
                if($employee_created > $r){
                    $employee[$r] = "-";
                }else{
                  if (in_array($r, $weekly_off)) {
                    $employee[$r] = "W";
                  }elseif(in_array($r, $holidays_list)) {
                    $employee[$r] = "H";
                  }elseif($r > $today){
                    $employee[$r] = "-";
                  }else{
                    if(in_array($r, $leave_dates_list)){
                      $employee[$r] = "L";
                    }else{
                      $employee[$r] = "A";
                    }
                  }
                }
            } else {
              foreach ($arr as $a) {
                if($employee_created > $r){
                  $employee[$r] = "-";
                }
                elseif (in_array($r, $weekly_off)) {
                  if ($a == $r) {
                    $employee[$r] = "W(P)";
                    break;
                  } else {
                    $employee[$r] = "W";
                  }
                } elseif (!empty($holidays_list)) {
                    if (in_array($r, $holidays_list)) {
                      if ($a == $r) {
                        $employee[$r] = "H(P)";
                        break;
                      } else {
                        $employee[$r] = "H";
                      }
                    } elseif ($a == $r) {
                      $employee[$r] = "P";
                      break;
                    } elseif ($r === $a) {
                      $employee[$r] = "P";
                      break;
                    } elseif ($r > $today) {
                      $employee[$r] = "-";
                      break;
                    } else {
                      if(in_array($r, $leave_dates_list)){
                        $employee[$r] = "L";
                      }else{
                        $employee[$r] = "A";
                      }
                    }
                } elseif (empty($holidays_list)) {
                    if ($a == $r) {
                      $employee[$r] = "P";
                      break;
                    } elseif ($r === $a) {
                      $employee[$r] = "P";
                      break;
                    } elseif ($r > $today) {
                      $employee[$r] = "-";
                      break;
                    } else{
                      if(in_array($r, $leave_dates_list)){
                        $employee[$r] = "L";
                      }else{
                        $employee[$r] = "A";  
                      }
                    }
                } elseif ($r > $today) {
                    $employee[$r] = "-";
                    break;
                } else{
                  if(in_array($r, $leave_dates_list)){
                    $employee[$r] = "L";
                  }else{
                    $employee[$r] = "A";
                  }
                }
              }
            }
          }
          $num_absent_days = 0;
          foreach ($dates as $r) {
              if($employee[$r] == 'A' || $employee[$r] == 'L'){
                $num_absent_days = $num_absent_days + 1;
              }
          }
          $employee_present_days  = count(array_unique($arr));
          $employee["Present_Days"] = $employee_present_days;
          $employee["Absent_Days"] = $num_absent_days;
        }
        return view('company.reports.monthlyattendancereport_partial', compact(['dates', 'employees_only', 'employee_attendance','leave_dates_list']));
    }else{
        $employees_only = null;
        $employee_attendance = null;
        $leave_dates_list = null;
        return view('company.reports.monthlyattendancereport_partial', compact(['dates', 'employees_only', 'employee_attendance','leave_dates_list']));
    }
  }

  /*** 
   * Checkin-Checkout Location Report
  ***/
  function checkin_checkoutlocationreport(Request $request){
    if(config('settings.cincout')==0 || !(Auth::user()->can('cincout-view'))){
      return redirect()->back();
    }
    if (!Auth::user()->isCompanyManager()) {
      
      $loggedInEmployeeId = Auth::user()->EmployeeId();
  
      $employeesWithAttendances = Auth::user()->handleQuery('employee')->where('id', '<>', $loggedInEmployeeId)->with('employee_attendances')->pluck('name', 'id')->toArray();
    }else{
      $employeesWithAttendances = Auth::user()->handleQuery('employee')->with('employee_attendances')->pluck('name', 'id')->toArray();
    }

    
    // $company_id = config('settings.company_id');
    // $attns = array();
    // $attendances = Auth::user()->handleQuery('employee')->select("employees.name", "employees.status", "attendances.employee_id", "attendances.check_datetime as date","attendances.adate as adate")
    //               ->join("attendances", "attendances.employee_id", "=", "employees.id")
    //               ->where('employees.status', 'Active')
    //               ->orderBy('attendances.check_datetime', 'desc')
    //               ->groupBy(DB::raw("DATE(check_datetime)"), "employee_id")
    //               ->get()->toArray();

    // $i = 0;
    // foreach ($attendances as $attendance) {
    //   $attns[$i] = $attendance;
    //   $attns[$i]['checkin'] = Auth::user()->handleQuery('attendance')->select("attendances.check_type as checkin", "attendances.check_datetime as checkintime", "attendances.address as checkinloc")->where('employee_id', $attendance['employee_id'])
    //       ->where('check_datetime', '=', date($attendance['date']))
    //       ->where('check_type','=',1)
    //       ->orderBy('attendances.check_datetime', 'asc')
    //       ->limit(1)
    //       ->get()->toArray();

    //   $attns[$i]['checkout'] = Auth::user()->handleQuery('attendance')->select("attendances.check_type as checkout", "attendances.check_datetime as checkouttime", "attendances.address as checkoutloc")->where('employee_id', $attendance['employee_id'])
    //       ->where('attendances.adate', '=', $attendance['adate'])
    //       ->where('check_datetime', '>', date($attendance['date']))
    //       ->where('check_type','=',2)
    //       ->orderBy('attendances.check_datetime', 'desc')
    //       ->limit(1)
    //       ->get()->toArray();
    //   $i++;
    // }
    return view('company.reports.checkin-checkoutlocationreport', compact('employeesWithAttendances'));
  }

  public function checkin_checkoutReportDataTable(Request $request){
    $columns = array(
      0 => 'id',
      1 => 'employee_name',
      2 => 'date',
      3 => 'checkin_time',
      4 => 'checkin_address',
      5 => 'checkout_time',
      6 => 'checkout_address',
    );

    $company_id = config('settings.company_id');
    $empVal = $request->empVal;
    $start_date = $request->startDate;
    $end_date = $request->endDate;
    $search = $request->input('search')['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');
    if($order == "employees_name"){
      $order = "A.".$order;
    }elseif($order == "checkin_time" || $order == "checkin_address"){
      $order = "A.".$order;
    }elseif($order == "checkout_time" || $order == "checkout_address"){
      $order = "B.".$order;
    }elseif($order == "date" ){
      $order = "A.check_datetime";
    }

    $loggedInEmployeeId = Auth::user()->EmployeeId();

    $visibleEmployees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();
    $prepQuery = DB::table('employee_checkin_views as A')
                ->select('E.name as employee_name', 'A.employee_id', 'A.date', 'A.checkin_time', 'A.checkin_address', 'B.checkout_time', 'B.checkout_address')
                ->leftJoin('employee_checkout_views as B', function($join)
                          {
                            $join->on('B.employee_id', 'A.employee_id');
                            $join->on('B.date', 'A.date');
                          })
                ->join('employees as E',  'A.employee_id' , 'E.id')->where('A.company_id', $company_id)
                ->whereIn('A.employee_id', $visibleEmployees)
                ->whereBetween('A.date', [$start_date, $end_date]);
     if (!Auth::user()->isCompanyManager()) {
      $prepQuery = $prepQuery->where('A.employee_id', '<>', $loggedInEmployeeId);
     }

    
    if(!empty($empVal)){
      $empFilterQuery =  (clone $prepQuery);
      $prepQuery = $empFilterQuery->where('A.employee_id', $empVal);
    }
    if(!empty($search)){
      $searchQuery = (clone $prepQuery);
      $prepQuery = $searchQuery->where(function($query) use ($search){
                      $query->orWhere('E.name' ,'LIKE', "%{$search}%");
                      $query->orWhere('A.checkin_address' ,'LIKE', "%{$search}%");
                      $query->orWhere('A.checkin_time', 'LIKE', "%{$search}%");
                      $query->orWhere('B.checkout_address' ,'LIKE', "%{$search}%");
                      $query->orWhere('B.checkout_time', 'LIKE', "%{$search}%");

                    });
    }

    $totalData = $prepQuery->count();
    if($limit==-1){
      $limit = $totalData;
    }
    $totalFiltered = $totalData;
    
    $data = array();
    $attendances = $prepQuery->orderBy($order,$dir)->offset($start)
                          ->limit($limit)->get();

    if (!empty($attendances)) {
        $i = $start;
        foreach ($attendances as $attendance) {
          $employee_name = $attendance->employee_name;
          $employee_show = domain_route('company.admin.employee.show',[$attendance->employee_id]);
          $date = isset($attendance->date)?getDeltaDate(date('Y-m-d',strtotime($attendance->date))):null;
          $checkInTime = isset($attendance->checkin_time)?date('g:i A', strtotime($attendance->checkin_time)):'N/A';
           $checkOutTime = isset($attendance->checkout_time)?date('g:i A', strtotime($attendance->checkout_time)):'Not Checked Out';
           
           if($attendance->checkin_address=='0'){
           $checkInLocation ='-';
           }else{
           $checkInLocation = isset($attendance->checkin_address)?$attendance->checkin_address:'-';
           }
           if($attendance->checkout_address=='0'){
           $checkOutLocation ='-';
           }else{
          $checkOutLocation = isset($attendance->checkout_address)?$attendance->checkout_address:'-';
          }

          $nestedData['id'] = ++$i;
          $nestedData['employee_name'] = "<a href='{$employee_show}' datasalesman='{$employee_name}'> {$employee_name}</a>";
          $nestedData['date'] = $date;
          $nestedData['checkin_time'] = $checkInTime;
          $nestedData['checkin_address'] = $checkInLocation;
          $nestedData['checkout_time'] = $checkOutTime;
          $nestedData['checkout_address'] = $checkOutLocation;

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

  /*** 
   * Daily Sales Order Report (by unit)
  ***/
  public function dailysalesorderreport(Request $request)
  {
    if(config('settings.dsobyunit')==0 || !(Auth::user()->can('dsorbunit-view'))){
      return redirect()->back();
    }
    $aggrunitprod = array();
    $company_id = config('settings.company_id');
    $units = DB::table('unit_types')->where('company_id', $company_id)->get();

    $partiesWithOrders = Auth::user()->handleQuery('client')->with('orders')->orderBy('company_name', 'asc')->pluck('company_name', 'id')->toArray();
    $employeesWithOrders = Auth::user()->handleQuery('employee')->with('orders')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();

    $order_by_outlet = Order::where('company_id', $company_id)->where('employee_id', 0)->pluck('outlet_id')->toArray();

    if(!empty($order_by_outlet)){
      $outlets = Outlet::whereIn('id', $order_by_outlet)->pluck('contact_person', 'id')->toArray();
    }else{
      $outlets = array();
    }
    $order_statuses = ModuleAttribute::where('company_id', $company_id)->orderby('title', 'desc')
                  ->pluck('title', 'id')
                  ->toArray();

    return view('company.reports.dailysalesorderreport', compact('partiesWithOrders', 'employeesWithOrders', 'units', 'outlets', 'order_statuses'));
  }

  public function dailysalesorderreportDataTable(Request $request){
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
    $order_status_select= $request->order_status_select;
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
    }elseif($order == "order_date"){
      $order = "orders."."id";
    }
    $dir = $request->input('order.0.dir');

     $prepQuery = Auth::user()->handleQuery('order')->select('clients.id as client_id', 'clients.name as contact_person_name', 'clients.company_name', 'clients.mobile as contact_number', 'clients.superior as party_superior', 'clients.client_type as party_type', 'partytypes.name as party_type_name','clients.address_1 as address', 'employees.name as employee_name', 'orders.employee_id as employee_id','orders.id as order_id', 'orders.order_no', 'orders.company_id', 'orders.order_date', 'outlets.contact_person', 'outlets.outlet_name')->whereIn('orders.delivery_status_id', $order_status_select)
              ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
              ->leftJoin('employees', 'orders.employee_id', '=', 'employees.id')
              ->leftJoin('outlets', 'orders.outlet_id', '=', 'outlets.id')
              ->leftJoin('partytypes', 'clients.client_type', '=', 'partytypes.id')
              ->leftJoin('client_settings', 'orders.company_id','client_settings.company_id')
              ->whereBetween('orders.order_date', [$start_date, $end_date])
              ->where('clients.client_type', '<>', 1);

    if(!empty($empVal)){
      $empFilterQuery =  (clone $prepQuery);
      if($request->outlet_search==1){
        $prepQuery = $empFilterQuery->where('orders.employee_id', $empVal);
      } elseif($request->outlet_search==0){
        $prepQuery = $empFilterQuery->where('orders.employee_id', 0)->where('orders.outlet_id', $empVal);
      } 
    }
    if(!empty($partyVal)){
      $partyFilterQuery =  (clone $prepQuery);
      $prepQuery = $partyFilterQuery->where('orders.client_id', $partyVal);
    }
    if(!empty($search)){
      $searchQuery = (clone $prepQuery);
      $prepQuery = $searchQuery->where(function($query) use ($search){
                  $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
                  $query->orWhere('outlets.contact_person' ,'LIKE', "%{$search}%");
                  $query->orWhere('clients.name' ,'LIKE', "%{$search}%");
                  $query->orWhere('clients.company_name' ,'LIKE', "%{$search}%");
                  $query->orWhere('clients.company_name' ,'LIKE', "%{$search}%");
                  $query->orWhere('clients.mobile' ,'LIKE', "%{$search}%");
                  $query->orWhere('partytypes.name' ,'LIKE', "%{$search}%");
                  $query->orWhere('orders.order_no', 'LIKE',"%{$search}%");
                  $query->orWhere(\DB::raw("Concat(client_settings.order_prefix ,orders.order_no)"), 'LIKE', "%{$search}%");
                  });
    }

    $totalData = $prepQuery->count();
    if($limit==-1){
      $limit = $totalData;
    }
    $totalFiltered = $totalData;

    $data = array();

    $orders =  $prepQuery->orderBy($order,$dir)->offset($start)
                          ->limit($limit)->get();

    if (!empty($orders)) {
        $i = $start;
        $order_prefix = getClientSetting()->order_prefix;
        $order_ids = $orders->pluck('order_id')->toArray();
        $units_order = DB::table('orderproducts')->whereIn('order_id', $order_ids)->get();
        $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
        foreach ($orders as $order) {
          $id = $order->order_id;
          $client_name = $order->contact_person_name;
          $client_company_name = $order->company_name;
          $client_show = in_array($order->client_id, $viewable_clients)?domain_route('company.admin.client.show',[$order->client_id]):null;
          $party_type =  $order->party_type_name;
          $contact =  $order->contact_number;
          $address =  $order->address;

          $employee_show = domain_route('company.admin.employee.show',[$order->employee_id]);

          if($order->employee_id==0){
            $empName = ucfirst($order->contact_person);
            $imgSrc = URL::asset('assets/dist/img/ret_logo.png');
            $empSection = "<span><img src=$imgSrc></img>&nbsp;{$empName}</span>";

            $nestedData['employee_name'] = $empSection;
          }else{
            $employee_name = $order->employee_name;
            $nestedData['employee_name'] = "<a href='{$employee_show}' datasalesman='{$employee_name}'> {$employee_name}</a>";
          }

          $date = isset($order->order_date)?getDeltaDate(date('Y-m-d',strtotime($order->order_date))):null;
          $order_no = $order_prefix.$order->order_no;
          $order_show =  domain_route('company.admin.order.show',[$order->order_id]);

          $nestedData['company_name'] = "<a href='{$client_show}' class='clientLinks' data-viewable='{$client_show}' datasalesman='{$client_company_name}'> {$client_company_name}</a>";
          $nestedData['contact_person'] = $client_name;
          $nestedData['party_type'] = $party_type;
          $nestedData['contact_number'] = $contact;
          $nestedData['address'] = $address;
          // $nestedData['employee_name'] = "<a href='{$employee_show}' datasalesman='{$employee_name}'> {$employee_name}</a>";
          $nestedData['order_date'] =$date;
          foreach($units as $unit){
            $totQty = 0;
            $nestedData["$unit"] =  $totQty + (clone $units_order)->where('unit', $unit)->where('order_id', $id)->SUM('quantity');
          }
          $nestedData['order_no'] = "<a href='{$order_show}'>{$order_no}</a>";

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

  /*** 
   * Zero Order List
  ***/
  public function zeroorderlist(Request $request){
    if(config('settings.zero_orders')==0 || !(Auth::user()->can('zeroorder-view'))){
      return redirect()->back();
    }
    $aggrunitprod = array();
    $company_id = config('settings.company_id');

    $partiesWithNoOrders = Auth::user()->handleQuery('client')->with('noorders')->orderBy('company_name', 'asc')->pluck('company_name', 'id')->toArray();
    $employeesWithNoOrders = Auth::user()->handleQuery('employee')->with('noorders')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    return view('company.reports.zeroorderlist', compact('partiesWithNoOrders', 'employeesWithNoOrders'));
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
      $order = "no_orders.id";//.$order;
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

    $no_orders =  $prepQuery->orderBy($order,$dir)->offset($start)
                          ->limit($limit)->get();

    if (!empty($no_orders)) {
        $i = $start;
        $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
        foreach ($no_orders as $noorder) {
          $id = $noorder->noorder_id;
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

          $nestedData['company_name'] = "<a class='clientLinks' href='{$client_show}' data-viewable='{$client_show}' datasalesman='{$client_company_name}'> {$client_company_name}</a>";
          $nestedData['contact_person'] = $client_name;
          $nestedData['party_type'] = $party_type;
          $nestedData['contact_number'] = $contact;
          $nestedData['address'] = $address;
          $nestedData['employee_name'] = "<a href='{$employee_show}' datasalesman='{$employee_name}'> {$employee_name}</a>";
          $nestedData['date'] =$date;
          $nestedData['remark'] = $remark;

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

  /*** 
   *Daily Salesman Order Report 
  ***/
  public function dailysalesmanorderreport(Request $request){
    if(config('settings.dso')==0 || !(Auth::user()->can('dsor-view'))){
      return redirect()->back();
    }
    $aggrunitprod = array();
    $company_id = config('settings.company_id');
    $employees = Auth::user()->handleQuery('employee')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    $units = UnitTypes::where('company_id', $company_id)->orderby('name', 'asc')->where('status', 'Active')->get();
    $order_statuses = ModuleAttribute::where('company_id', $company_id)->orderby('title', 'desc')
                    ->pluck('title', 'id')
                    ->toArray();
    return view('company.reports.dailysalesmanorderreport', compact('units', 'employees', 'order_statuses'));
  }

  public function searchdailysalesreportbysalesman(Request $request){
    $company_id = config('settings.company_id');
    $units = UnitTypes::where('company_id', $company_id)->orderby('name', 'asc')->where('status', 'Active')->get();
    $company_id = config('settings.company_id');

    $empid = $request->employee_id;
    $orddate = $request->order_date;
    $orderstatusselect = $request->order_status_select;
    $output = "";
    $orders = Order::leftJoin('orderproducts', 'orderproducts.order_id', 'orders.id')
              ->where('orders.company_id', $company_id)
              ->where('orders.employee_id', $empid)
              ->where('orders.order_date', $orddate)
              ->whereIn('orders.delivery_status_id', $orderstatusselect)
              ->whereNull('orders.deleted_at')
              ->whereNull('orderproducts.deleted_at')
              ->groupBy('orderproducts.product_id', 'orderproducts.product_variant_id', 'orderproducts.unit')
              ->get(['orderproducts.product_id', 'orderproducts.product_variant_id', 'orderproducts.product_name', 'orderproducts.product_variant_name', 'orderproducts.unit', DB::raw('SUM(orderproducts.quantity) as quantity')]);

    if($orders->first()){
      $emp_orders = array();
      $index_count = 0;
      foreach($orders as $order){
        if($order->product_variant_id){ 
          $emp_orders[$order->product_id][$order->product_variant_id][$order->unit]['quantity'] = $order->quantity;
          $emp_orders[$order->product_id][$order->product_variant_id][$order->unit]['product_name'] = $order->product_name. '('.$order->product_variant_name.')';
        }else{
          $index_count += 1;
          $emp_orders[$order->product_id][0][$order->unit]['quantity'] = $order->quantity;
          $emp_orders[$order->product_id][0][$order->unit]['product_name'] = $order->product_name;
        } 
      }
      
      $row_data = "";
      foreach ($emp_orders as $product_id=>$order_details) {
        foreach($order_details as $variant_id=>$order_detail){
          $product_name = "";
          $table_data =""; 
          foreach ($units as $unit) {
            if(array_key_exists($unit->id, $order_detail)){
              $quantity = $order_detail[$unit->id]['quantity'];
              $product_name = $order_detail[$unit->id]['product_name'];
            }else{
              $quantity = '-';
            }
            $table_data .= "<td>{$quantity}</td>";
          }
          $row_data .= "<tr><td>{$product_name}</td>{$table_data}</tr>";
        }
      }
      
      return Response($row_data);
    }else{
      $output = '<tr>' . '<td colspan="'.($units->count()+1).'" class="text-center">No Record Found</td></tr>';
      return Response($output);
    }
    // if ($request->ajax()) {
    //   $empid = $request->employee_id;
    //   $orddate = $request->order_date;
    //   $output = "";
    //   $neworder = array();
    //   $orders = DB::table('orderproducts as op')
    //             ->select('op.id as order_id', 'op.product_id', 'op.unit', 'op.unit_name', 'op.product_id', 'op.product_name', DB::raw('SUM(op.quantity) as quantity'))
    //             ->join('orders', 'orders.id', '=', 'op.order_id')
    //             ->where('employee_id', $empid)
    //             ->where('order_date', $orddate)
    //             ->groupBy('op.product_id')
    //             ->groupBy('op.unit')
    //             ->orderBy('op.product_id', 'desc')
    //             ->get();

    //   if ($orders) {
    //     foreach ($orders as $key => $order) {
    //       if (empty($neworder[$order->product_id])) {
    //         $neworder[$order->product_id] = [
    //           'product_name' => $order->product_name,
    //           $order->unit_name => $order->quantity,
    //           'order_id' => $order->order_id,
    //         ];
    //       } else {
    //         $neworder[$order->product_id][$order->unit_name] = $order->quantity;
    //       }
    //     }

    //     if ($neworder) {
    //       foreach ($neworder as $key => $order) {
    //         $output .= '<tr>' .
    //         '<td>' . $order['product_name'] . '</td>';
    //         foreach ($units as $unit) {
    //           $output .= '<td>' . (isset($order[$unit->name]) ? $order[$unit->name] : '-') . '</td>';
    //         }
    //         $output .= '</tr>';
    //       }
    //       return Response($output);
    //     } else {
    //       $output2 = '<tr>' .
    //           '<td colspan="'.(count($units)+1).'" class="text-center">No Record Found</td></tr>';
    //       return Response($output2);
    //     }
    //   } else {
    //     $output2 = '<tr>' .
    //     '<td>No Orders</td></tr>';
    //     return Response($output2);
    //   }
    // }
  }

  /*** 
   *Daily Party Report
  ***/
  public function dailypartyreport(Request $request){
    if(config('settings.dpartyreport')==0 || !(Auth::user()->can('dailypr-view'))){
      return redirect()->back();
    }
    
    $company_id = config('settings.company_id');

    $partyWithOrders = Auth::user()->handleQuery('client')->with('orders')->pluck('company_name', 'id')->toArray();
    $partyWithCollections = Auth::user()->handleQuery('client')->with('collections')->pluck('company_name', 'id')->toArray();
    $partiesWithOrdersCollections = $partyWithOrders + $partyWithCollections;
    DB::statement("DROP VIEW IF exists party_order_collection_views");
    $status_flag = ModuleAttribute::where('company_id', $company_id)
                    ->where('order_amt_flag', 1)
                    ->pluck('id')->toArray();
    $ids = implode(",", $status_flag); 
    $query = "CREATE OR REPLACE VIEW party_order_collection_views_$company_id AS
                SELECT
                    `orders`.`company_id` AS `company_id`,
                    `orders`.`client_id` AS `client_id`,
                    `orders`.`order_date` AS `date`,
                    0 AS `collection_total_amount`,
                    SUM(`orders`.`grand_total`) AS `order_total_amount`
                FROM
                    `orders` WHERE `orders`.`company_id` = $company_id AND `orders`.`client_id` IS NOT NULL 
                    AND `orders`.`delivery_status_id` IN ($ids) AND `orders`.`deleted_at` IS NULL
                GROUP BY
                    `orders`.`company_id`,
                    `orders`.`client_id`,
                    `orders`.`order_date`,
                    `orders`.`delivery_status_id`
                UNION ALL
                SELECT
                    `collections`.`company_id` AS `company_id`,
                    `collections`.`client_id` AS `client_id`,
                    `collections`.`payment_date` AS `payment_date`,
                    SUM(`collections`.`payment_received`) AS `collection_total_amount`,
                    0 AS `order_total_amount`
                FROM
                    `collections` WHERE `collections`.`company_id` = $company_id AND `collections`.`client_id` IS NOT NULL AND `collections`.`deleted_at` IS NULL
                GROUP BY
                    `collections`.`company_id`,
                    `collections`.`client_id`,
                    `collections`.`payment_date`";
    $views_sql_query = DB::select(DB::raw($query));
                          
    return view('company.reports.dailypartyreport', compact( 'partiesWithOrdersCollections'));
  }

  public function dailypartyreportDataTable(Request $request){
    $getColumns = $request->datacolumns;
    $columns = array();
    $sizeofColumns = sizeof($getColumns);
    for($count = 0; $count<$sizeofColumns; $count++){
      $columns[$count] = $getColumns[$count]["data"];
    }
    
    $company_id = config('settings.company_id');
    $view_table_name =  'party_order_collection_views_'.$company_id;
    $partyVal = $request->partyVal;
    $start_date = $request->startDate;
    $end_date = $request->endDate;
    $total = array();
    $search = $request->input('search')['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $order_col_no = $request->input('order.0.column');
    $order = $columns[$order_col_no];
    if($order == "company_name"){
      $order = "clients.".$order;
    }elseif($order == "date" || ($order == "order_total_amount" || $order == "collection_total_amount")){
      $order = $view_table_name.".".$order;
    }
    $dir = $request->input('order.0.dir');

    $accessibleParties = Auth::user()->handleQuery('client')->pluck('id')->toArray();
    // $delivery_status_id = $request->order_status_select;
    // $ids = implode(",", $delivery_status_id); 
    // $query = "CREATE OR REPLACE VIEW party_order_collection_views AS
    //                         SELECT
    //                             `orders`.`company_id` AS `company_id`,
    //                             `orders`.`client_id` AS `client_id`,
    //                             `orders`.`order_date` AS `date`,
    //                             0 AS `collection_total_amount`,
    //                             SUM(`orders`.`grand_total`) AS `order_total_amount`,
    //                             `orders`.`delivery_status_id` AS `delivery_status_id`,
    //                             `orders`.`delivery_status_id` AS `collection_delivery_status_id`
    //                         FROM
    //                             `orders` WHERE `orders`.`client_id` IS NOT NULL 
    //                             AND `orders`.`delivery_status_id` IN ($ids)
    //                         GROUP BY
    //                             `orders`.`company_id`,
    //                             `orders`.`client_id`,
    //                             `orders`.`order_date`,
    //                             `orders`.`delivery_status_id`
    //                         UNION ALL
    //                         SELECT
    //                             `collections`.`company_id` AS `company_id`,
    //                             `collections`.`client_id` AS `client_id`,
    //                             `collections`.`payment_date` AS `payment_date`,
    //                             SUM(`collections`.`payment_received`) AS `collection_total_amount`,
    //                             0 AS `order_total_amount`,
    //                             NULL AS `delivery_status_id`,
    //                             NULL AS `collection_delivery_status_id`
    //                         FROM
    //                             `collections` WHERE `collections`.`client_id` IS NOT NULL 
    //                         GROUP BY
    //                             `collections`.`company_id`,
    //                             `collections`.`client_id`,
    //                             `collections`.`payment_date`
    //                       ";
    // $views_sql_query = DB::select(DB::raw($query));
    $prepQuery = DB::table($view_table_name)->leftJoin('clients', 'clients.id', $view_table_name.'.client_id')->select('clients.company_name', $view_table_name.'.client_id',$view_table_name.'.date', DB::raw("SUM($view_table_name.collection_total_amount) as totalCollection"), DB::raw("SUM($view_table_name.order_total_amount) as totalOrder"))->whereBetween($view_table_name.'.date', [$start_date, $end_date])->whereIn($view_table_name.'.client_id', $accessibleParties);

    // $totalOrder = (clone $prepQuery);
    // $getTotalOrder = $totalOrder->sum('order_total_amount');
    // $orders = config('settings.currency_symbol').'   '.number_format((float) $getTotalOrder, 2);
    // $totalCollection = (clone $prepQuery);
    // $getTotalCollection = $totalCollection->sum('collection_total_amount');
    // $collections = config('settings.currency_symbol').'   '.number_format((float) $getTotalCollection, 2);

    // array_push( $total, $orders, $collections);
    if(!empty($partyVal)){
      $partyFilterQuery =  (clone $prepQuery);
      $prepQuery = $partyFilterQuery->where($view_table_name.'.client_id', $partyVal);
    }
    if(!empty($search)){
      $searchQuery = (clone $prepQuery);
      $prepQuery = $searchQuery->where(function($query) use ($search){
                      $query->orWhere('clients.company_name' ,'LIKE', "%{$search}%");
                    });
    }
    $totalOrder = (clone $prepQuery);
    $getTotalOrder = $totalOrder->sum('order_total_amount');
    $orders = config('settings.currency_symbol').'   '.number_format((float) $getTotalOrder, 2);
    $totalCollection = (clone $prepQuery);
    $getTotalCollection = $totalCollection->sum('collection_total_amount');
    $collections = config('settings.currency_symbol').'   '.number_format((float) $getTotalCollection, 2);
    array_push( $total, $orders, $collections);
    $totalData = $prepQuery->count();

    $prepQuery = $prepQuery->groupBy($view_table_name.'.client_id', $view_table_name.'.date');

    if($limit==-1){
      $limit = $totalData;
    }
    $totalFiltered = $totalData;

    $data = array();

    $ordersCollections =  $prepQuery->orderBy($order,$dir)->offset($start)
                          ->limit($limit)->get();

    if (!empty($ordersCollections)) {
      $i = $start;
      $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
      foreach ($ordersCollections as $orderCollection) {
        $date = isset($orderCollection->date)?getDeltaDate(date('Y-m-d',strtotime($orderCollection->date))):null;
        $client_company_name = $orderCollection->company_name;
        // $client_show = domain_route('company.admin.client.show',[$orderCollection->client_id]);
        $client_show = in_array($orderCollection->client_id, $viewable_clients)?domain_route('company.admin.client.show',[$orderCollection->client_id]):null;

        $totalOrder = config('settings.currency_symbol').'   '.number_format((float)$orderCollection->totalOrder, 2);
        $totalCollection = config('settings.currency_symbol').'   '.number_format((float)$orderCollection->totalCollection, 2);

        $nestedData['id'] = ++$i;
        $nestedData['company_name'] = "<a class='clientLinks' href='{$client_show}' data-viewable='{$client_show}' datasalesman='{$client_company_name}'> {$client_company_name}</a>";
        $nestedData['date'] =$date;
        $nestedData['order_total_amount'] = $totalOrder;
        $nestedData['collection_total_amount'] = $totalCollection;

        $data[] = $nestedData;
      }
    }

    $json_data = array(
        "draw"            => intval($request->input('draw')),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "total" => $total,
        "data"  => $data,
    );

    return json_encode($json_data);
  }

  /*** 
   *Daily Employee Report
  ***/
  public function dailyemployeereport(Request $request){
    // $company_id = config('settings.company_id');
    if(config('settings.dempreport')==0 || !(Auth::user()->can('dempr-view'))){
      return redirect()->back();
    }

    $company_id = config('settings.company_id');
    
    $employeeWithOrders = Auth::user()->handleQuery('employee')->with('orders')->pluck('name', 'id')->toArray();
    $employeeWithCollections = Auth::user()->handleQuery('employee')->with('collections')->pluck('name', 'id')->toArray();
    $employeesWithOrdersCollections = $employeeWithOrders + $employeeWithCollections;

    $status_flag = ModuleAttribute::where('company_id', $company_id)
                      ->where('order_amt_flag', 1)
                      ->pluck('id')
                      ->toArray();
    $ids = implode(",", $status_flag); 
    DB::statement("DROP VIEW IF exists employee_order_collection_views");
    $query = "CREATE OR REPLACE VIEW employee_order_collection_views_$company_id AS
                SELECT
                    `orders`.`company_id` AS `company_id`,
                    `orders`.`employee_id` AS `employee_id`,
                    `orders`.`order_date` AS `date`,
                    '0' AS `collection_total_amount`,
                    SUM(`orders`.`grand_total`) AS `order_total_amount`
                FROM
                    `orders`  WHERE `orders`.`company_id` = $company_id AND `orders`.`employee_id` IS NOT NULL 
                    AND `orders`.`delivery_status_id` IN ($ids) AND `orders`.`deleted_at` IS NULL
                GROUP BY
                    `orders`.`company_id`,
                    `orders`.`employee_id`,
                    `orders`.`order_date`,
                    `orders`.`delivery_status_id`
                UNION ALL
                SELECT
                    `collections`.`company_id` AS `company_id`,
                    `collections`.`employee_id` AS `employee_id`,
                    `collections`.`payment_date` AS `payment_date`,
                    SUM(`collections`.`payment_received`) AS `collection_total_amount`,
                    '0' AS `order_total_amount`
                FROM
                    `collections` WHERE `collections`.`company_id` = $company_id AND `collections`.`employee_id` IS NOT NULL AND `collections`.`deleted_at` IS NULL
                GROUP BY
                    `collections`.`company_id`,
                    `collections`.`employee_id`,
                    `collections`.`payment_date`";         
    $views_sql_query = DB::select(DB::raw($query));
    return view('company.reports.dailyemployeereport', compact('employeesWithOrdersCollections'));
  }

  public function dailyemployeereportDataTable(Request $request){
    $getColumns = $request->datacolumns;
    $columns = array();
    $sizeofColumns = sizeof($getColumns);
    for($count = 0; $count<$sizeofColumns; $count++){
      $columns[$count] = $getColumns[$count]["data"];
    }

    $company_id = config('settings.company_id');
    $view_table_name =  'employee_order_collection_views_'.$company_id;
    $employeeVal = $request->employeeVal;
    $start_date = $request->startDate;
    $end_date = $request->endDate;
    $total = array();
    $search = $request->input('search')['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $order_col_no = $request->input('order.0.column');
    $order = $columns[$order_col_no];
    // $delivery_status_id = $request->order_status_select;
    if($order == "company_name"){
      $order = "clients.".$order;
    }elseif($order == "date" || ($order == "order_total_amount" || $order == "collection_total_amount")){
      $order = $view_table_name.".".$order;
    }
    $dir = $request->input('order.0.dir');
    // $ids = implode(",", $delivery_status_id); 
    // $query = "CREATE OR REPLACE VIEW employee_order_collection_views AS
    //             SELECT
    //                 `orders`.`company_id` AS `company_id`,
    //                 `orders`.`employee_id` AS `employee_id`,
    //                 `orders`.`order_date` AS `date`,
    //                 '0' AS `collection_total_amount`,
    //                 SUM(`orders`.`grand_total`) AS `order_total_amount`,
    //                 `orders`.`delivery_status_id` AS `delivery_status_id`,
    //                 `orders`.`delivery_status_id` AS `collection_delivery_status_id`
    //             FROM
    //                 `orders`  WHERE `orders`.`employee_id` IS NOT NULL 
    //                 AND `orders`.`delivery_status_id` IN ($ids)
    //             GROUP BY
    //                 `orders`.`company_id`,
    //                 `orders`.`employee_id`,
    //                 `orders`.`order_date`,
    //                 `orders`.`delivery_status_id`
    //             UNION ALL
    //             SELECT
    //                 `collections`.`company_id` AS `company_id`,
    //                 `collections`.`employee_id` AS `employee_id`,
    //                 `collections`.`payment_date` AS `payment_date`,
    //                 SUM(`collections`.`payment_received`) AS `collection_total_amount`,
    //                 '0' AS `order_total_amount`,
    //                 NULL AS `delivery_status_id`,
    //                 NULL AS `collection_delivery_status_id`
    //             FROM
    //                 `collections` WHERE `collections`.`employee_id` IS NOT NULL 
    //             GROUP BY
    //                 `collections`.`company_id`,
    //                 `collections`.`employee_id`,
    //                 `collections`.`payment_date`
    //                       ";
    // $views_sql_query = DB::select(DB::raw($query));

    $prepQuery = Auth::user()->handleQuery('employee')->rightJoin($view_table_name, $view_table_name.'.employee_id', 'employees.id')->select('employees.name', $view_table_name.'.employee_id', $view_table_name.'.date', DB::raw("SUM($view_table_name.collection_total_amount) as totalCollection"), DB::raw("SUM($view_table_name.order_total_amount) as totalOrder"))->whereBetween($view_table_name.'.date', [$start_date, $end_date]);

    // $totalOrder = (clone $prepQuery);
    // $getTotalOrder = $totalOrder->sum('order_total_amount');
    // $orders = config('settings.currency_symbol').'   '.number_format((float) $getTotalOrder, 2);
    // $totalCollection = (clone $prepQuery);
    // $getTotalCollection = $totalCollection->sum('collection_total_amount');
    // $collections = config('settings.currency_symbol').'   '.number_format((float) $getTotalCollection, 2);

    // array_push( $total, $orders, $collections);
    if(!empty($employeeVal)){
      $employeeFilterQuery =  (clone $prepQuery);
      $prepQuery = $employeeFilterQuery->where($view_table_name.'.employee_id', $employeeVal);
    }
    if(!empty($search)){
      $searchQuery = (clone $prepQuery);
      $prepQuery = $searchQuery->where(function($query) use ($search){
                      $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
                    });
    }
    $totalOrder = (clone $prepQuery);
    $getTotalOrder = $totalOrder->sum('order_total_amount');
    $orders = config('settings.currency_symbol').'   '.number_format((float) $getTotalOrder, 2);
    $totalCollection = (clone $prepQuery);
    $getTotalCollection = $totalCollection->sum('collection_total_amount');
    $collections = config('settings.currency_symbol').'   '.number_format((float) $getTotalCollection, 2);
    array_push( $total, $orders, $collections);
    $totalData = $prepQuery->count();

    $prepQuery = $prepQuery->groupBy($view_table_name.'.employee_id', $view_table_name.'.date');

    if($limit==-1){
      $limit = $totalData;
    }
    $totalFiltered = $totalData;

    $data = array();

    $ordersCollections =  $prepQuery->orderBy($order,$dir)->offset($start)
                          ->limit($limit)->get();

    if (!empty($ordersCollections)) {
      $i = $start;
      foreach ($ordersCollections as $orderCollection) {
        $date = isset($orderCollection->date)?getDeltaDate(date('Y-m-d',strtotime($orderCollection->date))):null;
        $employee_name = $orderCollection->name;
        $employee_show = domain_route('company.admin.employee.show',[$orderCollection->employee_id]);

        $totalOrder = config('settings.currency_symbol').'   '.number_format((float)$orderCollection->totalOrder, 2);
        $totalCollection = config('settings.currency_symbol').'   '.number_format((float)$orderCollection->totalCollection, 2);

        $nestedData['id'] = ++$i;
        $nestedData['name'] = "<a href='{$employee_show}' datasalesman='{$employee_name}'> {$employee_name}</a>";
        $nestedData['date'] =$date;
        $nestedData['order_total_amount'] = $totalOrder;
        $nestedData['collection_total_amount'] = $totalCollection;

        $data[] = $nestedData;
      }
    }

    $json_data = array(
        "draw"            => intval($request->input('draw')),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "total" => $total,
        "data"  => $data,
    );

    return json_encode($json_data);
  }


  /*** 
   * Beat GPS COMPARISON MAP
  ***/
  public function beatgpscomparison(Request $request){
    $company_id = config('settings.company_id');
    $employee_id = $request->emp_id;
    $date = $request->date;
    $gps_type = "py_processed_path";
    $fileName = getFileName($company_id, $employee_id, $date);

    $attendances = $this->getAllEmployeeAttendanceTime($company_id, $employee_id, $date);

    $newlocations = array();
    $newLocationHasValue = false;
    $needPyProcessing = ($gps_type == "py_processed" || $gps_type == "py_processed_path")?true:false;
    
    $i = 0;
    foreach ($attendances as $attendance) {
        $time1 = strtotime($attendance['checkin']) * 1000;
        $time2 = strtotime($attendance['checkout']) * 1000;
        if($time1 == $time2){
            $maxDatetime = $request->mapdate." 23:59:59";
            $time2 = strtotime($maxDatetime) *1000;
        }

        if ($needPyProcessing) {
            $locations = getFileLocationWithRange($fileName, $time1, $time2, 60); 
            if (empty($locations) || count($locations) < 2) {
                $i++;
                continue;
            }
            $params = array(
                "raw_data" => json_encode($locations)
            );
          //  $loc=env("PYTHON_API_URL");
            $rawDataFromPython = callPythonApi(null,null, $params);
            $locations = json_decode($rawDataFromPython, true);
        } 

        if (!empty($locations) && $newLocationHasValue == false) $newLocationHasValue = true;
        array_push($newlocations, $locations);
        $i++;
    }
    $eff_calls_client_ids = (isset($request->effective_calls)?explode(',',$request->effective_calls):'');
    $non_eff_calls_client_ids = (isset($request->non_effective_calls)?explode(',',$request->non_effective_calls):'');
    $not_covered_client_ids = (isset($request->not_covered)?explode(',',$request->not_covered):'');
    $eff_calls_client_locations = "";
    $non_eff_calls_client_locations = "";
    $not_covered_client_locations = "";

    if(!empty($eff_calls_client_ids)){
        $eff_calls_client_locations = $this->getPartyLocation($eff_calls_client_ids);
    }
    if(!empty($non_eff_calls_client_ids)){
        $non_eff_calls_client_locations = $this->getPartyLocation($non_eff_calls_client_ids);
    }
    if(!empty($not_covered_client_ids)){
        $not_covered_client_locations = $this->getPartyLocation($not_covered_client_ids);
    }

    $data["visit_locations"] = !empty($encodedLocations)?json_encode($encodedLocations):null;
    $data["eff_location"] = isset($eff_calls_client_locations)?json_encode($eff_calls_client_locations):null;
    $data["non_eff_location"] = !empty($non_eff_calls_client_locations)?json_encode($non_eff_calls_client_locations):null;
    $data["not_covered_location"] = !empty($not_covered_client_locations)?json_encode($not_covered_client_locations):null;
    return $data;
  }

  /*** 
   * End Of Reports FROM Section Reports 
  ***/

  private function getlocation(Request $request){
    $company_id = config('settings.company_id');
    $attendances = $this->getAllEmployeeAttendanceTime($company_id, $request->eid, $request->mapdate);
    $newlocations = array();
    $i = 0;
    $needPyProcessing = ($request->gpsType == "py_processed" || $request->gpsType == "py_processed_path") ? true : false;
    foreach ($attendances as $attendance) {
      if ($request->gpsType == "raw") {
        $locations = Location::select('raw_latitude as latitude', 'raw_longitude as longitude', 'altitude', 'datetime', 'distance_from_last_gps')->where('company_id', $company_id)
                  ->where('employee_id', $request->eid)
                  ->where('unix_timestamp', '>=', (strtotime($attendance['checkin'])) * 1000)
                  ->where('unix_timestamp', '<=', (strtotime($attendance['checkout'])) * 1000)
                  ->orderBy('unix_timestamp', 'asc')
                  ->get()->toArray();
      } else if ($needPyProcessing) {
        $locations = Location::select('latitude', 'longitude', 'altitude', 'datetime', 'distance_from_last_gps')->where('company_id', $company_id)
                  ->where('employee_id', $request->eid)
                  ->where('unix_timestamp', '>=', (strtotime($attendance['checkin'])) * 1000)
                  ->where('unix_timestamp', '<=', (strtotime($attendance['checkout'])) * 1000)
                  ->where('accuracy', '<', (60))
                  ->orderBy('unix_timestamp', 'asc')
                  ->get()->toArray();
        if (empty($locations) || count($locations) < 2) {
          $i++;
          continue;
        }
        $params = array(
                  "raw_data" => json_encode($locations)
                  );

        $rawDataFromPython = callPythonApi(null,null, $params);
        $locations = json_decode($rawDataFromPython, true);
      } else {
        $locations = Location::select('latitude', 'longitude', 'altitude', 'datetime', 'distance_from_last_gps')->where('company_id', $company_id)
                  ->where('employee_id', $request->eid)
                  ->where('unix_timestamp', '>=', (strtotime($attendance['checkin'])) * 1000)
                  ->where('unix_timestamp', '<=', (strtotime($attendance['checkout'])) * 1000)
                  ->orderBy('unix_timestamp', 'asc')
                  ->get()->toArray();

      }
      array_push($newlocations, $locations);
      $i++;
    }

      $encodedLocations = json_encode($newlocations);
      return $encodedLocations;
  }

  public function getFileLocation(Request $request){
    $company_id = config('settings.company_id');
    $attendances = $this->getAllEmployeeAttendanceTime($company_id, $request->eid, $request->mapdate);
    
    $newlocations = array();
    $data = array();
    $newLocationHasValue = false;
    $i = 0;
    $needPyProcessing = ($request->gpsType == "py_processed" || $request->gpsType == "py_processed_path") ? true : false;
    $fileName = getFileName($company_id, $request->eid, $request->mapdate);
    if($needPyProcessing){
      $exists = Storage::disk("local")->exists($fileName);
      if(!$exists) {
        $msg = "GPS could not be captured. Possible Reasons:\n 1. Low Battery\n 2. Short check-in duration\n 3.GPS off\n 4. Phone went into optimization mode to save battery";
        return response()->json([
          'status' => 404,
          'message' => $msg,
        ]);
      }elseif($exists){
        $fileContents = Storage::get($fileName);
        $decodedContents = empty($fileContents) ? array() : json_decode($fileContents, true);
        if(empty($decodedContents)){
          $msg = "GPS could not be captured. Possible Reasons:\n 1. Low Battery\n 2. Short check-in duration\n 3.GPS off\n 4. Phone went into optimization mode to save battery";
          return response()->json([
            'status' => 404,
            'message' => $msg,
          ]);
        }else{
          $accurate_points = array();
          $worked_hours_details = $this->getCheckedInMinutes($attendances); 
          $minutes_checked_in = $worked_hours_details['worked_hours'];
          $expected_points = $minutes_checked_in * 4;
          foreach($decodedContents as $fileContent){
            if(ceil($fileContent['accuracy']) > 0 && ceil($fileContent['accuracy']) < 60) array_push($accurate_points, $fileContent);
          }
          if(empty($accurate_points)) return response()->json([
              'status' => 404,
              'message' => "Received inaccurate locations from device. Possible Reasons:\n 1. User was indoor\n 2. User was not moving\n 3.Battery level was low\n You could check the Raw Locations map.",
              'attendances' => $attendances,
            ]);
          try{
            $num_accurate_points = ceil( (sizeof($accurate_points) * 100) / $expected_points ); 
            $data_received = ceil(( sizeof($decodedContents) / $expected_points ) * 100);
          }catch(\Exception $e){
            $num_accurate_points = 0; 
            $data_received = 0;
            // return response()->json([
              //   'status' => 404,
              //   'message' => "Received inaccurate locations from device. Possible Reasons:\n 1. User was indoor\n 2. User was not moving\n 3.Battery level was low\n You could check the Raw Locations map.",
              //   'attendances' => $attendances,
              // ]);
          }
          
          $data_received = $expected_points > 0 ? ceil(( sizeof($decodedContents) / $expected_points ) * 100) : 0;
          $percent_accurate_points = ceil( (sizeof($accurate_points) / sizeof($decodedContents) ) * 100);
          
          $data = [
            'status' => 200,
            'message' => 'success',
            'minutes_checked_in' => $minutes_checked_in,
            'is_checked_in' => $worked_hours_details['is_checked_in'],
            'accuracy' => $num_accurate_points,
            'dataReceived' => $data_received,
            'percentAccuratePoints' => $percent_accurate_points,
            'locations' => $decodedContents,
            'distance_travelled' => $worked_hours_details['distance_travelled'],
          ];
        }
      }
    }
    foreach ($attendances as $attendance) {
      $time1 = strtotime($attendance['checkin']) * 1000;
      $time2 = strtotime($attendance['checkout']) * 1000;
      if($time1 == $time2){
        $maxDatetime = $request->mapdate." 23:59:59";
        $time2 = strtotime($maxDatetime) *1000;
      }
      $locations = getFileLocationWithRange($fileName, $time1, $time2);
      
      if ($request->gpsType == "raw") {
        $locations = getFileLocationWithRange($fileName, $time1, $time2);
      } else if ($needPyProcessing) {
        $locations = getFileLocationWithRange($fileName, $time1, $time2, 60);
        if (empty($locations)) {
          $i++;
          continue;
        }
        if(count($locations) > 1){
          $params = array(
                    "raw_data" => json_encode($locations)
                    );
          $rawDataFromPython = callPythonApi(null,null, $params);
          $locations = json_decode($rawDataFromPython, true); 
        }
      } else {
        $locations = getFileLocationWithRange($fileName, $time1, $time2);
      }
      
      if (!empty($locations) && $newLocationHasValue == false) $newLocationHasValue = true;
      array_push($newlocations, $locations);
      $i++;
    }

    if ($newLocationHasValue) {
      $encodedLocations = json_encode($newlocations);
    } else {
      $encodedLocations = $this->getLocation($request);
    }

    $data['fileloc']=$encodedLocations;
    $data['partyLoc'] =$this->getEmpPartyLocation($request->eid);

    return json_encode($data);
  }

  public function getEmpPartyLocation($empid){
    $company_id = config('settings.company_id');
    $emp_handles = DB::table('handles')->where('employee_id', Auth::user()->EmployeeId())->pluck('client_id')->toArray();
    $partyLocation = Client::select('company_name','latitude', 'longitude')
                    ->where('company_id', $company_id)
                    ->whereIn('id', $emp_handles)
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->where('status', 'Active')
                    ->orderBy('id', 'asc')
                    ->get()->toArray();
    return $partyLocation;
  }

  public function getPartyLocation(){
    $company_id = config('settings.company_id');

    $partyLocation = Client::select('company_name','latitude', 'longitude')
    ->where('company_id', $company_id)
    ->whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->where('status', 'Active')
    ->orderBy('id', 'asc')
    ->get()->toArray();
    return $partyLocation;
  }

  public function getAllRecentlocation(Request $request){
    if(config('settings.livetracking')==0){
      return redirect()->back();
    }
    $company_id = config('settings.company_id');
    $emplocations = $this->getActiveLocations($company_id);
    $data['emploc']=$emplocations;
    // $data['partyLoc'] =$this->getPartyLocation();
    $data['partyLoc'] =$this->getEmpPartyLocation(Auth::user()->EmployeeId());

    echo json_encode($data);

    exit;
  }

  public function getActiveLocations($tempCompanyID){

    $finalArray = array();
      
    if (empty($tempCompanyID)) return $finalArray;
    $currentDate = date('Y-m-d');
    $companyID = $tempCompanyID;
    if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
      $mSql = "SELECT employees.id, employees.name, attendances.adate, attendances.check_type, attendances.check_datetime, attendances.employee_id, attendances.latitude, attendances.longitude,attendances.unix_timestamp FROM attendances INNER JOIN employees ON attendances.employee_id = employees.id WHERE attendances.company_id = " . $companyID . " AND attendances.adate =  '" . $currentDate . "' AND attendances.check_type = '1' AND employees.status = 'Active' AND attendances.check_datetime IN ((SELECT MAX(check_datetime) FROM attendances GROUP BY attendances.employee_id))";
    }else{
      $logged_user=Auth::user()->EmployeeId();
      $visibleusers=Auth::user()->getChainUsers($logged_user);
      if(!empty($visibleusers)){
        $visibleusers = implode(',', $visibleusers);
        $mSql = "SELECT employees.id, employees.name, attendances.adate, attendances.check_type, attendances.check_datetime, attendances.employee_id, attendances.latitude, attendances.longitude, attendances.unix_timestamp FROM attendances INNER JOIN employees ON attendances.employee_id = employees.id WHERE employees.id IN (". $visibleusers .")AND  attendances.company_id = " . $companyID . " AND attendances.adate =  '" . $currentDate . "' AND attendances.check_type = '1' AND employees.status = 'Active' AND attendances.check_datetime IN ((SELECT MAX(check_datetime) FROM attendances GROUP BY attendances.employee_id))";
      }else{
        $mSql = "SELECT employees.id, employees.name, attendances.adate, attendances.check_type, attendances.check_datetime, attendances.employee_id, attendances.latitude, attendances.longitude, attendances.unix_timestamp FROM attendances INNER JOIN employees ON attendances.employee_id = employees.id WHERE attendances.company_id = " . $companyID . " AND attendances.adate =  '" . $currentDate . "' AND attendances.check_type = '1' AND employees.status = 'Active' AND attendances.check_datetime IN ((SELECT MAX(check_datetime) FROM attendances GROUP BY attendances.employee_id))";
      }
    }
    $employees = $result = DB::select(DB::raw($mSql));
    if (empty($employees)) return $finalArray;
    foreach ($employees as $key => $value) {
    $start = $value->unix_timestamp;
    $recentEmpLocation = getRecentLocationFromFile($companyID, $value->employee_id, $currentDate, $start);
    $unixTimestamp = empty($recentEmpLocation) ? $value->unix_timestamp : getArrayValue($recentEmpLocation, "unix_timestamp");

    $tempArray = array(
                  "emp_id" => $value->id,
                  "name" => $value->name,
                  "lat" => empty($recentEmpLocation) ? $value->latitude : getArrayValue($recentEmpLocation, "latitude"),
                  "lng" => empty($recentEmpLocation) ? $value->longitude : getArrayValue($recentEmpLocation, "longitude"),
                  "datetime" => empty($recentEmpLocation) ? $value->check_datetime : getArrayValue($recentEmpLocation, "datetime"),
                  "unix_timestamp" => (double)$unixTimestamp
                );
    array_push($finalArray, $tempArray);
    }
    return $finalArray;
  }


  public function gettotalhour(Request $request){
    $company_id = config('settings.company_id');
    $attendances = Attendance::where('company_id', $company_id)
          ->where('employee_id', $request->eid)
          ->where('check_datetime', '>=', $request->mapdate . ' 00:00:00')
          ->where('check_datetime', '<=', $request->mapdate . ' 23:59:59')
          ->orderBy('check_datetime', 'Asc')
          ->get()->toArray();
    $seconds = $minutes = $hours = 0;

    $checkin = $workedhours = '';

    $checkout = '';

    if (!empty($attendances)) {
      $allrec = count($attendances);
      if ($attendances[$allrec - 1]['check_type'] == 1) {
        array_pop($attendances);
        $allrec--;
      }

    }

    if (!empty($attendances)) {
      if ($attendances[0]['check_type'] == 2) {
        array_shift($attendances);
        $allrec--;
      }
    }


    if ($attendances) {
      for ($i = 0; $i < $allrec; $i++) {
        $starttimestamp = strtotime($attendances[$i]['check_datetime']);
        $endtimestamp = strtotime($attendances[++$i]['check_datetime']);
        $seconds += abs($endtimestamp - $starttimestamp);
      }
      $hours = floor($seconds / 3600);

      $minutes = floor(($seconds / 60) % 60);

      $seconds = $seconds % 60;
    }
    return $hours . ' hr ' . $minutes . ' min';
  }

  public function getWorkedHourDetails(Request $request){
    $companyID = config('settings.company_id');
    $date = $request->mapdate;
    $employeeID = $request->eid;
        
    $attendances = $this->getAllEmployeeAttendanceTime($companyID,$employeeID ,$date );

    $fileName = getFileName($companyID, $employeeID, $date);
    $finalArray = array();
    foreach ($attendances as $key => $attendance) {

      $tempArray = array();

      $cinDatetime =  $attendance['checkin'];
      $coutDatetime =  $attendance['checkout'];

      $dateTime1 = strtotime($cinDatetime);
      $dateTime2 = strtotime($coutDatetime);

      $cinTime = substr($cinDatetime, 11);
      $coutTime = substr($coutDatetime, 11);

      $time1 = $dateTime1 * 1000;
      $time2 = $dateTime2 * 1000;
      $locations = getFileLocationWithRange($fileName,$time1,$time2, 60);

      if (!empty($locations) && count($locations) > 1) {
        $params = array(
          "raw_data" => json_encode($locations)
        );

        $locations = callPythonApi(null,null, $params);
        $locations = json_decode($locations, true);
      }
      $tempArray["checkin"] = $time1;
      $tempArray["checkout"] = $time2;
      $tempArray["cin_time"] = $cinTime;
      $tempArray["cout_time"] = $coutTime;
      $tempArray["locations"] = $locations;
      array_push($finalArray,$tempArray);
    }

    $encodedLocations = json_encode($finalArray);
    echo $encodedLocations;
    exit();
  }

  function getDistanceTravelled(Request $request){
    $company_id = config('settings.company_id');
    $attendances = $this->getAllEmployeeAttendanceTime($company_id, $request->eid, $request->mapdate);
    $totdist = 0.00;
    $newlocations = array();
    foreach ($attendances as $attendance) {
      $locations = Location::select('latitude as lat', 'longitude as lng')->where('company_id', $company_id)
              ->where('employee_id', $request->eid)
              ->where('unix_timestamp', '>=', (strtotime($attendance['checkin'])) * 1000)
              ->where('unix_timestamp', '<=', (strtotime($attendance['checkout'])) * 1000)
              ->orderBy('unix_timestamp', 'asc')
              ->get()->toArray();

      $newlocations = array_merge($newlocations, $locations);

    }
    $ptcount = count($newlocations);

    for ($i = 1; $i < $ptcount; $i++) {
      echo $newlocations[$i - 1]['lat'] . ',' . $newlocations[$i - 1]['lng'] . '--' . $newlocations[$i]['lat'] . ',' . $newlocations[$i]['lng'];
      echo "=";
      echo $distravelled = number_format($this->distance($newlocations[$i - 1]['lat'], $newlocations[$i - 1]['lng'], $newlocations[$i]['lat'], $newlocations[$i]['lng'], 'K'), 2);
      if ($distravelled > 1.0) {
        $distravelled = 0.0;
      }
      echo "</br>";
      $totdist = $totdist + isValid($distravelled);
    }
    echo "</br>Total: ";
    echo $disttravelled = number_format($totdist, 2) . ' KM ---';
    die;
  }

  function getDistanceTravelled2(Request $request){
    $locations = Location::select('latitude as lat', 'longitude as lng')
          ->where('employee_id', $request->eid)
          ->where('unix_timestamp', '>=', strtotime($request->mapdate . ' 00:00:00') * 1000)
          ->where('unix_timestamp', '<=', strtotime($request->mapdate . ' 23:59:59') * 1000)
          ->orderBy('unix_timestamp', 'asc')
          ->get();

    $ptcount = count($locations);
    $totdist = 0.00;
    $traveltable = '';
    for ($i = 1; $i < $ptcount; $i++) {
      echo $locations[$i - 1]->lat . ',' . $locations[$i - 1]->lng . '--' . $locations[$i]->lat . ',' . $locations[$i]->lng;
      echo "=";
      echo $distravelled = number_format($this->distance($locations[$i - 1]->lat, $locations[$i - 1]->lng, $locations[$i]->lat, $locations[$i]->lng, 'K'), 2);
      echo "</br>";
      $totdist = $totdist + isValid($distravelled);
    }
    echo "</br>Total: ";
    echo $disttravelled = number_format($totdist, 2) . ' KM ---';
    die;
    $traveltable = "<div class='modal-header'>
                      <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                    </div>
                    <div class='modal-body'><div class='box box-info'>
                      <div class='box-body'>
                      <div class='table-responsive'><h4 class='modal-title text-center' id='myModalLabel'>Distance Travelled</h4><p class='text-center'>" . getEmployee($request->empid)->name . " (" . date('d M Y', strtotime($request->tdate)) . ")";

    $traveltable .= "</p><p class='text-center'><b>Total Distance Travelled: " . $disttravelled . "</b></p>";
    $traveltable .= "</div></div></div></div>";
    echo $traveltable;
  }

  public function getlocationbycompany(Request $request){
    $company_id = config('settings.company_id');
    $currentdate = date('Y-m-d');
    $locations = DB::table('locations')
          ->leftJoin('employees', 'locations.employee_id', '=', 'employees.id')
          ->select('employees.name', 'locations.latitude', 'locations.longitude', 'locations.unix_timestamp')
          ->where('locations.company_id', $company_id)
          ->where('locations.created_at', '>=', $currentdate . ' 00:00:00')
          ->groupBy('locations.employee_id')
          ->orderBy('locations.unix_timestamp', 'desc')
          ->get();
    echo json_encode($locations);
  }

  public function groupArray($arr, $group, $preserveGroupKey = false, $preserveSubArrays = false){
    $temp = array();
    foreach ($arr as $key => $value) {
      $groupValue = $value->$group;
      if (!$preserveGroupKey) {
        unset($arr[$key]->$group);
      }
      if (!array_key_exists($groupValue, $temp)) {
        $temp[$groupValue] = array();
      }
      if (!$preserveSubArrays) {
        $data = count($arr[$key]) == 1 ? current($arr[$key]) : $arr[$key];
      } else {
        $data = $arr[$key];
      }

      $temp[$groupValue][] = $data;
    }
    return $temp;
  }

  public function group_by($key, $array){
    $result = array();
    foreach ($array as $val) {
      if (array_key_exists($key, $val)) {
        $result[$val->$key][] = $val;
      } else {
        $result[""][] = $val;
      }
    }
    return $result;
  }

  public function getdistance(Request $request){
    $company_id = config('settings.company_id');
    $attendances = Attendance::select('check_datetime', 'check_type')
                  ->where('company_id', $company_id)
                  ->where('employee_id', '=', $request->eid)
                  ->where('check_datetime', '>=', $request->mapdate . ' 00:00:00')
                  ->where('check_datetime', '<=', $request->mapdate . ' 23:59:59')
                  ->orderBy('created_at', 'asc')
                  ->get();

    foreach ($attendances as $attendance) {
      if ($attendance->check_type == 1) {
        $checkin[] = $attendance->check_datetime;
      }
      if ($attendance->check_type == 2) {
        if ($attendance->check_datetime != '') {
          $checkout[] = $attendance->check_datetime;
        } else {
          $checkout[] = now();
        }
      }
    }

    for ($j = 0; $j < count($checkin); $j++) {
      $locations = Location::select('latitude as lat', 'longitude as lng')->where('company_id', $company_id)
                    ->where('employee_id', $request->eid)
                    ->where('created_at', '>=', $checkin[$j])
                    ->where('created_at', '<=', $checkout[$j])
                    ->orderBy('unix_timestamp', 'asc')
                    ->get();
      $ptcount = count($locations);
      $totdist = 0.00;
      if ($ptcount >= 2) {
        for ($i = 1; $i < $ptcount; $i++) {
          $distyravelled = $this->distance($locations[$i - 1]->lat, $locations[$i - 1]->lng, $locations[$i]->lat, $locations[$i]->lng, 'k');
          $totdist += $distyravelled;
        }
      }
    }
    $tot = round($totdist, 2) . 'KM';
    return $tot;
  }

  function distance($lat1, $lon1, $lat2, $lon2, $unit){
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);
    if ($unit == "K") {
      return ($miles * 1.609344);
    } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
      return $miles;
    }

  }

  public function getdistanceold(Request $request){
    $company_id = config('settings.company_id');
    $checkin = array();
    $checkout = array();
    $attendances = Attendance::select('check_datetime', 'check_type')
                  ->where('company_id', $company_id)
                  ->where('employee_id', '=', $request->eid)
                  ->where('check_datetime', '>=', $request->mapdate . ' 00:00:00')
                  ->where('check_datetime', '<=', $request->mapdate . ' 23:59:59')
                  ->orderBy('created_at', 'asc')
                  ->get();

    foreach ($attendances as $attendance) {
      if ($attendance->check_type == 1) {
        $checkin[] = $attendance->check_datetime;
      }
      if ($attendance->check_type == 2) {
        $checkout[] = $attendance->check_datetime;
      }
    }
    $totaldist = array();
    for ($j = 0; $j < count($checkin); $j++) {
      $locations[$j] = Location::select('latitude as lat', 'longitude as lng')
              ->where('company_id', $company_id)
              ->where('employee_id', $request->eid)
              ->where('created_at', '>=', $checkin[$j])
              ->where('created_at', '<=', $checkout[$j])
              ->orderBy('unix_timestamp', 'asc')
              ->get();

      $ptcount = '';
      $ptcount = count($locations[$j]);
      $totdist = 0;
      for ($i = 1; $i < $ptcount; $i++) {
        $distyravelled = distance($locations[$j][$i - 1]->lat, $locations[$j][$i - 1]->lng, $locations[$j][$i]->lat, $locations[$j][$i]->lng, 'K');
        $totdist = $totdist + isValid($distyravelled);
      }
      $totaldist[] = $totdist;
    }
    return $locations;
  }

  private function getAllEmployeeAttendanceTime($compid, $eid, $date){
    $attendances = Employee::select("employees.name", "employees.status", "attendances.employee_id", "attendances.check_datetime", "attendances.check_type")
                  ->join("attendances", "attendances.employee_id", "=", "employees.id")
                  ->where('attendances.employee_id', $eid)
                  ->where('employees.status', 'Active')
                  ->where('employees.company_id', $compid)
                  ->where('attendances.check_datetime', '>=', $date . ' 00:00:00')
                  ->where('attendances.check_datetime', '<=', $date . ' 23:59:59')
                  ->orderBy('check_datetime', 'asc')
                  ->get()->toArray();
    $newatten = array();
    $i = 0;

    foreach ($attendances as $attendance) {
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
    return $newatten;
  }

  public function getsalesreportdaily(){
    $type = 'csv';
    $reportType = 'orderReportType1';
    $startDate = date('Y-m-d');
    $endDate = date("Y-m-d", strtotime("+1 day"));
    $ordrows = array();

    $distributors = Client::where('email', '!=', '')
                    ->where('client_type', 3)
                    ->orwhere('client_type', 2)
                    ->get();

    if (!empty($distributors)) {
      foreach ($distributors as $distributor) {
        $companyID = $distributor->company_id;
        $emails = $distributor->email;
        $dist_name = $distributor->company_name;
        $retailers = Client::select('id')->where('superior', $distributor->id)->where('client_type', 1)->get()->toArray();
        foreach ($retailers as $retailer) {
          $rets[] = $retailer['id'];
        }


        if (!empty($rets)) {
          $categories = DB::table('categories')->where('company_id', $companyID)->where('status', 'Active')->orderBy('id', 'desc')->get()->toArray();

          $cat[] = '';$cat[] = '';$cat[] = '';$cat[] = '';

          $totalProducts = 0;

          $prod = array('Retailer Name', 'Contact No', 'Address', 'Salesman');

          foreach ($categories as $categpry) {
            $cat[] = $categpry->name;
            $products = '';
            $products = Product::where('category_id', $categpry->id)->where('company_id', $companyID)->where('status', 'Active')->orderBy('category_id', 'desc')->get();
            $totalProducts = count($products) + $totalProducts;

            if (($products->count())) {
              $i = 0;
              foreach ($products as $product) {
                if ($i > 0)
                    $cat[] = '';
                $prod[] = $product->product_name;
                $prodt[] = $product->id;
                $i++;
              }
            } else {
              $prod[] = '';
              $prodt[] = '';
            }
          }
          $orders = Order::select('clients.company_name', 'clients.mobile', 'clients.address_1', 'employees.name', 'orders.id')
                      ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
                      ->leftJoin('employees', 'orders.employee_id', '=', 'employees.id')
                      ->where('orders.company_id', $companyID)
                      ->whereIn('client_id', $rets)
                      ->where('orders.order_date', '>=', $startDate)
                      ->where('orders.order_date', '<=', $endDate)
                      ->orderby('orders.id', 'desc')
                      ->get();

          if ($orders) {
            foreach ($orders as $order) {
              $ordrows[$order->id] = array($order->company_name, $order->mobile, $order->address_1, $order->name);
              $orderprods = DB::table('orderproducts')
                        ->select('orderproducts.quantity', 'orderproducts.product_id', 'orderproducts.order_id')
                        ->where('orderproducts.order_id', $order->id)
                        ->get();
              foreach ($orderprods as $orderprod) {
                $qty[$orderprod->product_id][$orderprod->order_id] = $orderprod->quantity;
              }
              foreach ($prodt as $pt) {
                if (!empty($qty[$pt][$order->id])) {
                  array_push($ordrows[$order->id], $qty[$pt][$order->id]);
                } else {
                  array_push($ordrows[$order->id], '');
                }
              }
            }
          }
          unset($data);
          $data[] = array($dist_name);
          $data[] = array();
          $data[] = $cat;
          $data[] = $prod;
          foreach ($ordrows as $orderrow) {
            $data[] = $orderrow;
          }
          $data2 = json_decode(json_encode($data), true);
          $file = strtolower(str_replace(' ', '_', $dist_name));
          $filename = time() . '_' . $file;
          Excel::create($filename, function ($excel) use ($data2) {
              $excel->sheet('mySheet', function ($sheet) use ($data2) {
                  $sheet->fromArray($data2);
              });
          })->store($type, storage_path('excel/exports'));
          unset($cat);
          unset($prod);
          unset($prodt);
          unset($orderrow);
          unset($qty);
          unset($retailers);
          unset($orderprods);
          $filepath = 'http://' . $_SERVER['HTTP_HOST'] . '/cms/storage/excel/exports/' . $filename . '.csv';
          $subject = 'Sales Report';

          Mail::send('mails.demo_plain', ['url' => '', 'company_name' => ''], function ($message) use ($emails, $subject, $filepath) {

              $message->from('support@deltasalesapp.com', 'Deltatech Nepal');

              $message->to($emails);

              $message->bcc('bikash.deltatech@gmail.com');

              $message->subject($subject);

              $message->attach($filepath);

          });
        } else {
          echo "No retailer Found";
        }
      }
    } else {
      echo "No distrubutor Found";
    }
    echo "File Downloaded";
  }

  public function getaddress($lat,$lng){
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&key=AIzaSyDR6v2elDctrDptLyvTjpTBEs6z7CLSfW8';

    $json = @file_get_contents($url);
    $data=json_decode($json);
    $status = $data->status;
    if($status=="OK"){
      return $data->results[0]->formatted_address;
    }
    else{
      return false;
    }
  }

  public function updatelocation(Request $request){
    $nolocs = Attendance::select('id','latitude', 'longitude', 'address')->whereNull('address')
      ->whereNotNull('latitude')
      ->whereNotNull('longitude')
      ->where('latitude','!=',0)
      ->where('longitude','!=',0)
      ->where('latitude','!=',0.000000)
      ->where('longitude','!=',0.000000)
      ->orderBy('id', 'desc')
      ->limit(1000)
      ->get();
      
    $i=1;
    foreach($nolocs as $noloc){
        if($noloc->latitude || $noloc->longitude){
        $address= $this->getaddress($noloc->latitude,$noloc->longitude);
        if($address){
          $updateAttendance = Attendance::findOrFail($noloc->id);
          $updateAttendance->address = $address;
          $updateAttendance->save();
        }  
      }
      echo " ".$i++." ";
    }

    echo "Done";
  }

  public function generateorderreport(){

    $aggrunitprod = array();
    $company_id = config('settings.company_id');
    $employees = Employee::where('company_id', $company_id)->pluck('name', 'id')->toArray();
    $clients = Client::where('company_id', $company_id)->pluck('company_name', 'id')->toArray();
    $units = DB::table('unit_types')->where('company_id', $company_id)->get();

    $orders = Order::select('clients.id as clientid', 'clients.name as contact_person_name', 'clients.company_name', 'clients.mobile', 'clients.superior', 'clients.client_type', 'clients.address_1', 'employees.name as salesman', 'orders.id', 'orders.company_id', 'orders.order_date')
        ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
        ->leftJoin('employees', 'orders.employee_id', '=', 'employees.id')
        ->where('orders.company_id', $company_id)
        ->where('clients.client_type', '<>', 1)
        ->orderby('orders.id', 'desc')
        ->get();

    if ($orders) {
      foreach ($orders as $order) {
        foreach ($units as $unit) {
          $totqty = 0;
          $aggrunitprod[$order->id]['contact_person_name'] = $order->contact_person_name;
          $aggrunitprod[$order->id]['party_type'] = $order->client_type;
          $aggrunitprod[$order->id]['partyname'] = $order->company_name;
          $aggrunitprod[$order->id]['salesman'] = $order->salesman;
          $aggrunitprod[$order->id]['partyid'] = $order->clientid;
          $aggrunitprod[$order->id]['contactno'] = $order->mobile;
          $aggrunitprod[$order->id]['address'] = $order->address_1;
          $aggrunitprod[$order->id]['date'] = $order->order_date;
          $aggrunitprod[$order->id]['order_id'] = $order->id;
          $aggrunitprod[$order->id]['order_no'] = $order->order_no;
          $totqty = $totqty + DB::table('orderproducts')->where('unit_name', $unit->name)->where('order_id', $order->id)->SUM('quantity');

          $aggrunitprod[$order->id][$unit->name] = $totqty;
        }
      }
    }
    return view('company.reports.generatereport', compact('clients', 'employees', 'aggrunitprod', 'units'));
  }

  public function custompdfdexport(Request $request){
    $getExportData = json_decode($request->exportedData)->data;
    $columns = json_decode($request->columns);
    $properties = json_decode($request->properties);
    $pageTitle = $request->pageTitle;
    $reportName = $request->reportName;
    if($reportName == "checkin-checkout" || $reportName == "dailysalesorderreport" || $reportName == "monthlyattendancereport"){
      $paperOrientation = "landscape";
    }elseif($reportName == "zeroorderlist" || $reportName == "salesmangps" || $reportName == "dailypartyreport" || $reportName == "dailyemployeereport"){
      $paperOrientation = "portrait";
    }else{
      $paperOrientation = "portrait";
    }
    set_time_limit(300);
    ini_set("memory_limit", "256M");
    $pdf = PDF::loadView('company.reports.exportpdf', compact('getExportData', 'pageTitle', 'columns', 'properties'))->setPaper('a4', $paperOrientation);
    $download = $pdf->download($pageTitle.'.pdf');
    return $download;
  }
}
