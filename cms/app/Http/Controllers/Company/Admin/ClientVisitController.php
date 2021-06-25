<?php

namespace App\Http\Controllers\Company\Admin;

use DB;
use App;

use DateTime;
use App\Client;
Use Auth;
use App\Employee;
use App\Location;
use App\Attendance;
use App\ClientVisit;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ClientVisitController extends Controller
{
    private $company_id;
    public function __construct()
    {
        $this->middleware('auth');
        $this->company_id = config('settings.company_id');
        // $this->middleware('permission:PartyVisit-create', ['only' => ['create','store']]);
        $this->middleware('permission:PartyVisit-view');
        // $this->middleware('permission:PartyVisit-update', ['only' => ['edit','update']]);
        // $this->middleware('permission:PartyVisit-delete', ['only' => ['destroy']]);
        // $this->middleware('permission:PartyVisit-status', ['only' => ['changeStatus']]);
        date_default_timezone_set('Asia/Kathmandu');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $company_id = config('settings.company_id');
      $clientvisitsCount = 0;
      $employeesWithClientVisits = Auth::user()->handleQuery('employee')->whereHas('clientvisit')->pluck('name', 'id')->toArray();
      return view('company.client-visits.index', compact('clientvisitsCount', 'employeesWithClientVisits'));
    }

    public function ajaxDatatable(Request $request){
      $columns = array( 'id', 'date', 'employee_name', 'no_of_visits', 'view_detail' );

      $company_id = config('settings.company_id');
      $empVal = $request->empVal;
      $search = $request->input('search')['value'];
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      if($order == 'no_of_visits'){
        $order = \DB::raw('COUNT(*)');
      }
      if($order == 'employee_name'){
        $order = "employees.name";
      }
      if($order == 'id'){
        $order = "client_visits.id";
      }
      $dir = $request->input('order.0.dir');

      $prepQuery = Auth::user()->handleQuery('client_visit')->where('client_visits.company_id', $company_id)->leftJoin('employees', 'employees.id', 'client_visits.employee_id')
                    ->groupBy(['client_visits.employee_id', 'client_visits.date']);

      if (!empty($search)) {
          $searchQuery = $prepQuery;
          $prepQuery = $searchQuery->where(function ($query) use ($search) {
            $query->orWhere('employees.name', 'LIKE', "%{$search}%");
          });
      }

      if($request->startDate && $request->endDate){
        $startDateFilter = $request->input('startDate');
        $endDateFilter = $request->input('endDate');
        $dateFilterQuery = $prepQuery;
        $prepQuery = $dateFilterQuery->whereBetween('date',[$startDateFilter, $endDateFilter]);
      }

      if(!empty($empVal)){
        $empFilterQuery = $prepQuery;
        $prepQuery = $empFilterQuery->where('employees.id', $empVal);
      }

      $totalData =  (clone $prepQuery)->get()->count();
      if($limit==-1) $limit = $totalData;
      $totalFiltered = $totalData;
    
      $data = array();
      $clientvisits = $prepQuery->orderBy($order, $dir)->offset($start)
                          ->limit($limit)
                          ->get(['employees.name', 'employees.id as empId', 'client_visits.date', \DB::raw('COUNT(*) as no_of_visits', 'employees.name')]);

      if (!empty($clientvisits)) {
          $i = $start;
          $viewable_employees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();

          foreach ($clientvisits as $clientvisit) {
              $id = $clientvisit->id;
              $clientvisit_date = $clientvisit->date;
              $no_of_visits = $clientvisit->no_of_visits;
              $detail = domain_route('company.admin.employee.empClientVisitDetail', ['id' => $clientvisit->empId, 'date' => $clientvisit_date]);
              $employee_show = in_array($clientvisit->empId, $viewable_employees)?domain_route('company.admin.employee.show',[$clientvisit->empId]):null;
              
              $nestedData['id'] = ++$i;
              $nestedData['date'] = getDeltaDate($clientvisit_date);
              $nestedData['employee_name'] = "<a href='{$employee_show}' data-viewable='{$employee_show}' class='empLinks'>{$clientvisit->name}</a>";
              $nestedData['no_of_visits'] = $no_of_visits;

              $nestedData['view_detail'] = "<a href='{$detail}' class='btn btn-success btn-sm' style='color: #05c16b!important;padding: 3px 6px;border: none;background-color: #05c16b00!important;'><i class='fa fa-eye'></i></a>";
          
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

    public function empClientVisitDetail(Request $request)
    {
      $data = $this->getVisitData($request);

      return view('company.client-visits.visit_details')->with($data);
    }

    private function getVisitData($request){
      $company_id = $this->company_id;
      $empId = $request->id;
      $date = $request->date;
      $hr = 0;
      $min = 0;
      $sec = 0;

      $empVisits = ClientVisit::whereCompanyId($company_id)->whereEmployeeId($empId)->where('date', '=',$date)->with(['employee' => function($query){
                      return $query->select('employees.name', 'employees.id');
                    }])->with(['client' => function($query){
                      return $query->withTrashed()->select('clients.company_name', 'clients.id');
                    }])->with(['visitpurpose' => function($query){
                      return $query->select('visit_purposes.title', 'visit_purposes.id');
                    }])->with('images')->orderby('start_time', 'desc')->orderby('id', 'desc')->get()->map(function($client_visit_purpose) use($hr, $min){
                      $formatted_data = $this->formatClientVisit($client_visit_purpose); 
                      return $formatted_data;
                  });
      
      // $empVisits = Auth::user()->handleQuery('client_visit')->whereEmployeeId($empId)->where('date', '=',$date)->with(['employee' => function($query){
      //                 return $query->select('employees.name', 'employees.id');
      //               }])->with(['client' => function($query){
      //                 return $query->select('clients.company_name', 'clients.id');
      //               }])->with(['visitpurpose' => function($query){
      //                 return $query->select('visit_purposes.title', 'visit_purposes.id');
      //               }])->with('images')->orderby('start_time', 'desc')->orderby('id', 'desc')->get()->map(function($client_visit_purpose) use($hr, $min){
      //                 $formatted_data = $this->formatClientVisit($client_visit_purpose); 
      //                 return $formatted_data;
      //             });
      $total_duration = "";
      $visited_clients = array();
      foreach($empVisits as $formatted_data){
        if(empty($formatted_data) ) continue;
        $hr += $formatted_data['duration']['hr'];
        $min += $formatted_data['duration']['min'];
        $sec += $formatted_data['duration']['sec'];
        $total_duration = $this->getTotalDuration($hr, $min, $sec);

        if(!in_array($formatted_data['client_id'], $visited_clients))array_push($visited_clients, $formatted_data['client_id']);
      }
                      
      $attendance_details = Attendance::whereEmployeeId($empId)->where('adate', '=',$date);
      $checkin = (clone $attendance_details)->where('check_type', 1)->first([\DB::raw('MIN(atime) as checkIn')])->checkIn;
      $checkout = (clone $attendance_details)->where('check_type', 2)->where('atime', '>=', $checkin)->first([\DB::raw('MAX(atime) as checkOut')])->checkOut;
      $employee_name = $empVisits->first()['employee_name'];
      $employee_show = $empVisits->first()['employee_show'];
      $action = NULL;
      $download = domain_route('company.admin.employee.empClientVisitDetailDownload', ['id' => $empId, 'date' => $date]);
      
      $action  = '<a class="btn btn-default btn-sm" href="'.$download.'" style="padding: 7px 6px;margin-right: 5px;"><i class="fa fa-book"></i>PDF</a>';
          
      $action  = $action.'<button class="btn btn-default btn-sm" href="#" onclick="print();" style="padding: 7px 6px;"><i class="fa fa-print"></i>Print</button>';
      
      $total_distance = $this->getWorkedHourDetails($company_id, $empId, $date, $checkin, $checkout);
      $decoded = json_decode($total_distance);
      $workedHours = $decoded->checkinsCheckouts;
      if($workedHours) $totalWorkedHours = $this->getTotalDuration($workedHours->hr, $workedHours->min, $workedHours->sec);
      else $totalWorkedHours = 'N/A';
      $client_locations = $this->getEmpPartyLocation($company_id, $visited_clients);
      if(!empty($checkout)){
        $checkout = date('h:i A', strtotime($checkout));
      }else{
        if($date<date('Y-m-d')) $checkout = "11:59 PM"; 
        else $checkout = "Not Checked Out";
      }
      $data = [
        'action' => $action,
        'checkin' => date('h:i A', strtotime($checkin)), 
        'checkout' => $checkout, 
        'date' => $date, 
        'employee_id' => $empId,
        'employee_name' => $employee_name, 
        'employee_show' => $employee_show, 
        'empVisits' => $empVisits, 
        'partyLoc' => json_encode($client_locations),
        'total_distance' => $total_distance,
        'distance_travelled' => $decoded->status ==404 ? "NA" : $this->getDistanceTravelled($total_distance),
        'total_duration' => $total_duration, 
        'totalWorkedHours' => $totalWorkedHours
      ];

      return $data;
    }

    public function empClientVisitDetailDownload(Request $request){
      $data = $this->getVisitData($request);

      try{
        // return view('company.client-visits.partial_show.visit_detail_download', $data);
        $pdf = PDF::loadView('company.client-visits.partial_show.visit_detail_download', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($data['employee_name'].'_'.$data['date'].'_'.'_Visit_Report.pdf');
      }catch(\Exception $e){
        Log::error($e->getFile());
        Log::error($e->getMessage());
        Log::error($e->getLine());

        return redirect()->back();
      }
    }

    public function empClientVisitDetailPrint(Request $request){
      $data = $this->getVisitData($request);

      try{
        return view('company.client-visits.partial_show.visit_detail_download', $data);
      }catch(\Exception $e){
        Log::error($e->getFile());
        Log::error($e->getMessage());
        Log::error($e->getLine());

        return redirect()->back();
      }
    }

    public function employeePeriodicLocation(Request $request){
      $empId = $request->employee_id;
      $date = $request->date;
      $start_time = $request->start_time;
      $end_time = $request->end_time;
      $checkIn = Attendance::whereCompanyId($this->company_id)
                  ->whereEmployeeId($empId)->where('adate', $date)->where('check_type', 1)
                  ->where('atime', '<=', $start_time)->orderBy('atime', 'desc')
                  ->first();
      
      $checkOut = Attendance::whereCompanyId($this->company_id)
                  ->whereEmployeeId($empId)->where('adate', $date)->where('check_type', 2)
                  ->where('atime', '>=', $end_time)->orderBy('atime', 'asc')
                  ->first();
      
      // if($checkIn) $time1 = strtotime($checkIn->check_datetime) * 1000;
      // if($checkOut) $time2 = strtotime($checkOut->check_datetime) * 1000;
      // else $time2 = strtotime($date." "."23:59:59") * 1000;
      $time1 = strtotime($date." ".$start_time) * 1000;
      $time2 = strtotime($date." ".$end_time) * 1000;
      $fileName = getFileName($this->company_id, $empId, $date);
      if($fileName) $locations = getFileLocationWithRange($fileName, $time1, $time2);
      else $locations = array();

      return json_encode($locations);
    }

    private function formatClientVisit($object){
      $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
      $viewable_employees = Auth::user()->handleQuery('employee')->pluck('id')->toArray();

      try{
        $start_time = new DateTime($object->start_time);
        $end_time = new DateTime($object->end_time);
        $interval = $end_time->diff($start_time);
        $hr = $interval->h;
        $min = $interval->i;
        $sec = $interval->s;
        $total_duration = $this->getTotalDuration($hr, $min, $sec);
        // $client_show = domain_route('company.admin.client.show',[$object->client_id]);
        // $employee_show = domain_route('company.admin.employee.show',[$object->employee_id]);
        $employee_show = in_array($object->employee_id, $viewable_employees)?domain_route('company.admin.employee.show',[$object->employee_id]):null;
        $client_show = in_array($object->client_id, $viewable_clients)?domain_route('company.admin.client.show',[$object->client_id]):null;

        $formatted_data = [
          'id' => $object->id,
          'client_id' => $object->client_id,
          'client_name' => $object->client?$object->client->company_name:null,
          'employee_id' => $object->employee_id,
          'employee_name' => $object->employee?$object->employee->name:null,
          'client_show' => $client_show,
          'employee_show' => $employee_show,
          'visit_purpose_id' => $object->visitpurpose?$object->visitpurpose->id:null,
          'visit_purpose' => $object->visitpurpose?$object->visitpurpose->title:null,
          "date" => $object->date,
          "start_time" => date("h:i A", strtotime($object->start_time)),
          "unformatted_start_time" => $object->start_time,
          "end_time" => date("h:i A", strtotime($object->end_time)),
          "unformatted_end_time" => $object->end_time,
          "duration" => array("hr" => $hr, "min" => $min, "sec" => $sec),
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
        $total_duration = ($hr_duration?$hr_duration." ":$hr_duration).$min_duration. " " . $sec_duration;
        if(!$hr_duration) $total_duration = ($min_duration?$min_duration." ":$min_duration).$sec_duration;

        return $total_duration;
      }catch(\Exception $e){
        Log::error($e->getFile());
        Log::error($e->getMessage());
        Log::error($e->getLine());
        
        return "";
      }
    }

    private function getWorkHourDetails($attendances){
        $hr = 0;
        $min = 0;
        $sec = 0;
        $current_state_checkout = false;
        $has_last_checkout = false;
        foreach ($attendances as $attendance) {
          if(array_key_exists('checkout_state', $attendance)){

            if(!$has_last_checkout && $attendance['checkout_state'] == 'current'){
              $current_state_checkout = true;
              break;
            }elseif($has_last_checkout && $attendance['checkout_state'] == 'current'){
              break;
            }
          }
          $has_last_checkout = true;
          $strtime1 = strtotime($attendance['checkin']);
          $time1 = $strtime1 * 1000;
          $strtime2 = strtotime($attendance['checkout']);
          $time2 = $strtime2 * 1000;
          $timeDateTime1 = new DateTime($attendance['checkin']);
          $timeDateTime2 = new DateTime($attendance['checkout']);
          $time_diff = $timeDateTime2->diff($timeDateTime1);
          $hr += $time_diff->h;
          $min += $time_diff->i;
          $sec += $time_diff->s;
        }
        if($current_state_checkout && !$has_last_checkout) return null;
        else return array("hr" => $hr, "min" => $min, "sec" => $sec);
    }

    public function getWorkedHourDetails($company_id, $empId, $date, $checkIn, $checkOut){
        
      $attendances = $this->getAllEmployeeAttendanceTime($company_id, $empId, $date, "00:00:00", "23:59:59");

      $fileName = getFileName($company_id, $empId, $date);
      $data = array();

      $exists = Storage::disk("local")->exists($fileName);
      if(!$exists) {
        $msg = "GPS could not be captured. Possible Reasons:\n 1. Low Battery\n 2. Short check-in duration\n 3.GPS off\n 4. Phone went into optimization mode to save battery";
        
        return json_encode([
          'status' => 404,
          'message' => $msg,
          'checkinsCheckouts' => $this->getWorkHourDetails($attendances)
        ]);
      }elseif($exists){
        $fileContents = Storage::get($fileName);
        $decodedContents = empty($fileContents) ? array() : json_decode($fileContents, true);
        if(empty($decodedContents)){
          $msg = "GPS could not be captured. Possible Reasons:\n 1. Low Battery\n 2. Short check-in duration\n 3.GPS off\n 4. Phone went into optimization mode to save battery";
          
          return json_encode([
            'status' => 404,
            'message' => $msg,
            'checkinsCheckouts' => $this->getWorkHourDetails($attendances)
          ]);
        }else{
          $accurate_points = array();
          $worked_hours_details = $this->getCheckedInMinutes($attendances); 
          $minutes_checked_in = $worked_hours_details['worked_hours'];
          $expected_points = $minutes_checked_in * 4;
          foreach($decodedContents as $fileContent){
            if(ceil($fileContent['accuracy']) > 0 && ceil($fileContent['accuracy']) < 60) array_push($accurate_points, $fileContent);
          }
          $num_accurate_points = $expected_points > 0 ? ceil( (sizeof($accurate_points) * 100) / $expected_points ) : 0; 
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
          ];
        }
      }

      try{
        $newlocations = array();
        $checkinsCheckouts = array();
        $hr = 0;
        $min = 0;
        $sec = 0;
        $newLocationHasValue = false;
        $i = 0;
        
        foreach ($attendances as $attendance) {
          $strtime1 = strtotime($attendance['checkin']);
          $time1 = $strtime1 * 1000;
          $strtime2 = strtotime($attendance['checkout']);
          $time2 = $strtime2 * 1000;
          $timeDateTime1 = new DateTime($attendance['checkin']);
          $timeDateTime2 = new DateTime($attendance['checkout']);
          $time_diff = $timeDateTime2->diff($timeDateTime1);
          if(array_key_exists('checkout_state', $attendance)){
            if($attendance['checkout_state'] != 'current'){
              $hr += $time_diff->h;
              $min += $time_diff->i;
              $sec += $time_diff->s;
            }
          }else{
            $hr += $time_diff->h;
            $min += $time_diff->i;
            $sec += $time_diff->s;
          }
          
          if($time1 == $time2){
            $maxDatetime = $date . " " . $checkOut;
            $time2 = strtotime($maxDatetime) *1000;
          }
                  
          $locations = getFileLocationWithRange($fileName, $time1, $time2, 60);
          if (empty($locations)) {
            $i++;
            continue;
          }
          if(count($locations) > 1){
            $params = array("raw_data" => json_encode($locations));
            $rawDataFromPython = callPythonApi(null,null, $params);
            $locations = json_decode($rawDataFromPython, true); 
          }
          
          if (!empty($locations) && $newLocationHasValue == false) $newLocationHasValue = true;
          
          array_push($newlocations, $locations);
          $i++;
        }

        $encodedLocations = json_encode($newlocations);
        $checkinsCheckouts = array("hr" => $hr, "min" => $min, "sec" => $sec);
        $data['fileloc'] = $encodedLocations;
        $data['checkinsCheckouts'] = $checkinsCheckouts;
        
        return json_encode($data);
      }catch(\Exception $e){
        Log::info($e->getMessage());
        Log::info($e->getLine());
        Log::info($e->getFile());

        return json_encode(array("fileloc" => json_encode(array())));
      }
    }

    private function getCheckedInMinutes($empAttendances){
    $hr = 0;
    $min = 0;
    $sec = 0;
    $isCheckedIn = false;
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
    }  

    if( $hr > 0 ) {
      $min += $hr * 60;
    }
    if($sec > 0){
      $min += 1 / $sec; 
    }

    $data = array('worked_hours' => ceil($min), 'is_checked_in' => $isCheckedIn); 

    return $data;
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
              //$newatten[$i]['checkout'] = $attendance['check_datetime'];
              date_default_timezone_set(config('settings.time_zone'));
              $newatten[$i]['checkout'] = date('Y-m-d H:i:s');
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

    private function getEmpPartyLocation($company_id, $visited_clients){
      
      $partyLocation = Client::select('id', 'company_name','latitude', 'longitude')
                        ->where('company_id', $company_id)
                        ->whereIn('id', $visited_clients)
                        ->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->where('status', 'Active')
                        ->orderBy('id', 'asc')
                        ->get()->toArray();
      return $partyLocation;
    }

    private function getDistanceTravelled($totalDistanceArray){
      $total_distance = json_decode(json_decode($totalDistanceArray)->fileloc);

      $totalDistanceStr = "";
      $totalDistance = 0;
      $r = 6371; // km (change this constant to get miles)
      $iteration = 0;
      $lat1 = 0;
      $lon1 = 0;
      $lat2 = 0;
      $lon2 = 0;

      if(empty($total_distance)) return round($totalDistance, 3);
      
      foreach($total_distance as $locations){
        if(empty($locations)) continue;
        foreach($locations as $tempLocation){
          if($iteration == 0){
            $lat1 = $tempLocation->latitude;
            $lon1 = $tempLocation->longitude;
          }
  
          $lat2 = $tempLocation->latitude;
          $lon2 = $tempLocation->longitude;
  
          $dLat = ($lat2-$lat1) * pi() / 180;
          $dLon = ($lon2-$lon1) * pi()  / 180;
          $a = sin($dLat/2) * sin($dLat/2) + cos($lat1 * pi() / 180 ) * cos($lat2 * pi() / 180 ) * sin($dLon/2) * sin($dLon/2);
          $c = 2 * atan2(sqrt($a), sqrt(1-$a));
          $distance = $r * $c;
          $totalDistance = $totalDistance + $distance;
          $lat1 = $lat2;
          $lon1 = $lon2;
          $iteration++;
        }
        $iteration = 0;
        $lat1 = 0;
        $lon1 = 0;
        $lat2 = 0;
        $lon2 = 0;

      }

      return round($totalDistance, 3);
    }
    
}