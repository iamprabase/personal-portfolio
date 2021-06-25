<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Holiday;
use Carbon\Carbon;
use Auth;
use DB;

class HolidayController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->back();
        $company_id = config('settings.company_id');
        $holidays = Holiday::where('company_id', $company_id)->get();
        $data[] = "";
        foreach ($holidays as $holiday) {
            $end_date = Carbon::parse($holiday->end_date);
            $data['nextday_end'][$holiday->id] = $end_date->addDays(1);
        }
        $today = Carbon::now();
        $upcomingHolidays = Holiday::where('company_id', $company_id)->where('start_date', '>', $today)->orderBy('start_date', 'ASC')->take(15)->get();
        foreach ($upcomingHolidays as $holiday) {
            $start_date = Carbon::parse($holiday->start_date);
            $end_date = Carbon::parse($holiday->end_date);
            $data['days_diff'][] = $start_date->diffInDays($end_date) + 1;
        }
        return view('company.holiday.index', compact('holidays', 'data', 'upcomingHolidays'));
    }

    public function create()
    {
        return redirect()->back();
    }

    public function store($domain, Request $request)
    {
        if ($request->start_date > $request->end_date) {
            $data['result'] = "End date can't be less than start date";
            return $data;
        }
        $company_id = config('settings.company_id');
        $input['name'] = $request->name;
        $input['company_id'] = $company_id;
        $input['description'] = $request->description;
        $input['start_date'] = $request->start_date;
        $input['end_date'] = $request->end_date;
        $holiday=Holiday::Create($input);
        $notificationData = [];

        $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');

        $dataPayload = array("data_type" => "holiday", "holiday" => $holiday, "action" => "add");
        $sent = sendPushNotification_($fbIDs, 11, $notificationData, $dataPayload);


        if(config('settings.ncal')!=0){
          $holidays = Holiday::where('company_id',$company_id)->where('start_date','<=',$request->engLastDate)->where('end_date','>=',$request->engFirstDate)->orderBy('start_date','ASC')->get();
          $data=$this->ajaxEvents($request->engFirstDate,$holidays);
          $data['year'] = $request->getYear;
          $data['month'] = $request->getMonth;
          
        }else{
          $data['id'] = $holiday['id'];
          $data['name'] = $request->name;
          $data['description'] = $request->description;
          $data['start_date'] = $request->start_date;
          $data['end_date'] =$request->end_date;
          $end_date = Carbon::parse($request->end_date);
          $data['nextday'] = $end_date->addDays(1)->format('Y-m-d'); 
          $data['result'] ="Created Successfully";         
        }
        return $data;
    }

    public function edit($domain, Request $request)
    {
        $Holiday = Holiday::findOrFail($request->edit_id);
        $company_id = config('settings.company_id');
        if ($Holiday->company_id != $company_id || !$Holiday) {
            return $data['result'] = "You are not authorized to update this data";
        }
        if(!empty($request->name))
        $input['name'] = $request->name;
        if(!empty($request->description))
        $input['description'] = $request->description;
        if(!empty($request->start_date))
        $input['start_date'] = $request->start_date;
        if(!empty($request->end_date))
        $input['end_date'] = $request->end_date;
        $Holiday->Update($input);
        $notificationData = [];

        $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');

        $dataPayload = array("data_type" => "holiday", "holiday" => $Holiday, "action" => "update");
        $sent = sendPushNotification_($fbIDs, 11, $notificationData, $dataPayload);


        if(config('settings.ncal')!=0){
          $holidays = Holiday::where('company_id',$company_id)->where('start_date','<=',$request->engLastDate)->where('end_date','>=',$request->engFirstDate)->orderBy('start_date','ASC')->get();
          $data=$this->ajaxEvents($request->engFirstDate,$holidays);
          $data['year'] = $request->getYear;
          $data['month'] = $request->getMonth;
          $data['result'] = "Successfully Updated";
        }else{
          $data['name'] = $request->name;
          $data['id'] = $Holiday->id;
          $data['description'] = $request->description;
          $data['start_date'] = $request->start_date;
          $data['end_date'] = $request->end_date;
          $data['nextday'] = Carbon::parse($request->end_date)->addDays(1)->format('Y-m-d');
          $data['result'] ="Updated Successfully";
        }
        return $data;
    }

    public function delete($domain, Request $request)
    {
        $holly = Holiday::findOrFail($request->del_id);
        $company_id = config('settings.company_id');
        if ($holly->company_id != $company_id || !$holly) {
            return $holly['result'] = "You are not authorized to delete this data";
        }
        $holly->delete();
        if(config('settings.ncal')!=0){
          $holidays = Holiday::where('company_id',$company_id)->where('start_date','<=',$request->engLastDate)->where('end_date','>=',$request->engFirstDate)->orderBy('start_date','ASC')->get();
          $data=$this->ajaxEvents($request->engFirstDate,$holidays);
          $data['year'] = $request->getYear;
          $data['month'] = $request->getMonth;
        }else{
          $data['result']="success";
        }
        $notificationData = [];
        $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "holiday", "holiday" => $data, "action" => "delete");
        $sent = sendPushNotification_($fbIDs, 11, $notificationData, $dataPayload);
        return $data;
    }

    public function populate($domain, Request $request)
    {
        session()->flash('active', 'holiday');
        $company_id = config('settings.company_id');
        $dateTime = Carbon::parse($request->currentDate);
        $currentYear = Carbon::now()->format('Y');
        $givenYear = $dateTime->format('Y');
        if(config('settings.ncal')==0){
            if ($givenYear > $currentYear) {
                $startOfYear = $dateTime->copy()->startOfYear();
                $endOfYear = $dateTime->copy()->endOfYear();
            } elseif ($givenYear < $currentYear) {
                $data['result'] = "Oops! Sorry, we can't populate previous years.";
                return $data;
            } elseif ($request->rangetype == "whole_year") {
                $startOfYear = $dateTime->copy()->startOfYear();
                $endOfYear = $dateTime->copy()->endOfYear();
            } elseif ($request->rangetype == "end_of_year") {
                $startOfYear = $dateTime;
                $endOfYear = $dateTime->copy()->endOfYear();
            } elseif ($request->rangetype == "end_of_month") {
                $startOfYear = $dateTime;
                $endOfYear = $dateTime->copy()->endOfMonth();
            } elseif ($request->rangetype == "end_of_next_month") {
                $startOfYear = $dateTime;
                $endOfYear = $dateTime->copy()->endOfMonth();
                $endOfYear = $endOfYear->addDay();
                $endOfYear = $endOfYear->endOfMonth();
            } elseif ($request->rangetype == "end_of_next_two_month") {
                $startOfYear = $dateTime;
                $endOfYear = $dateTime->copy()->endOfMonth();
                $endOfYear = $endOfYear->addDay()->endOfMonth()->addDay()->endOfMonth();
            } else {
                $data['result'] = "Please Enter Valid Options";
                return $data;
            }
        }else{
            $startOfYear = $request->start;
            $endOfYear = $request->yearEndDate;
        }
        if($request->offtype == 'saturday') {
            $startDate = Carbon::parse($startOfYear)->next(Carbon::SATURDAY);
            $endDate = Carbon::parse($endOfYear);
            $tempdata['result'] = "failed";
            for ($date = $startDate; $date->lte($endDate); $date->addWeek()) {
                $saturdays[] = $date->format('Y-m-d');
            }
            foreach($saturdays as $saturday) {
                $exitsHoliday = Holiday::where('start_date', $saturday)->where('company_id',$company_id)->where('name', 'Weekly Off')->where('end_date', $saturday)->get()->count();
                if ($exitsHoliday == 0) {
                    $input['name'] = "Weekly Off";
                    $input['description'] = "Weekly Holiday by Company";
                    $input['start_date'] = $saturday;
                    $input['end_date'] = $saturday;
                    $input['company_id'] = $company_id;
                    Holiday::Create($input);
                    $tempdata['result'] = "success";
                }
            }
            $startRemoveDate = Carbon::parse($startOfYear)->next(Carbon::SUNDAY);
            for($date = $startRemoveDate; $date->lte($endDate); $date->addWeek()) {
                $sundays[] = $date->format('Y-m-d');
            }
            foreach ($sundays as $sunday) {
                $holidays = Holiday::where('company_id',$company_id)->where('start_date', $sunday)->where('name', 'Weekly Off')->where('end_date', $sunday)->get();
                $exitsHoliday = $holidays->count();
                if ($exitsHoliday != 0) {
                    foreach ($holidays as $holiday) {
                        $row = Holiday::findOrFail($holiday->id);
                        $row->delete();
                        $tempdata['result'] = "success";
                    }
                }
            }
            if(config('settings.ncal')!=0){
              $holidayss = Holiday::where('company_id',$company_id)->where('start_date','<=',$request->engLastDate)->where('end_date','>=',$request->engFirstDate)->orderBy('start_date','ASC')->get();
              $data=$this->ajaxEvents($request->engFirstDate,$holidayss);
              $data['year'] = $request->getYear;
              $data['month'] = $request->getMonth;
              $data['result'] = $tempdata['result'];
              return $data;
            }else{
              return $tempdata;
            }
        } elseif($request->offtype == 'sunday') {
            $startDate = Carbon::parse($startOfYear)->next(Carbon::SUNDAY);
            $endDate = Carbon::parse($endOfYear);
            $tempdata['result'] = "failed";
            for ($date = $startDate; $date->lte($endDate); $date->addWeek()) {
                $saturdays[] = $date->format('Y-m-d');
            }
            foreach ($saturdays as $saturday) {
                $exitsHoliday = Holiday::where('company_id',$company_id)->where('start_date', $saturday)->where('name', 'Weekly Off')->where('end_date', $saturday)->get()->count();
                if ($exitsHoliday == 0) {
                    $input['name'] = "Weekly Off";
                    $input['description'] = "Weekly Holiday by Company";
                    $input['start_date'] = $saturday;
                    $input['end_date'] = $saturday;
                    $input['company_id'] = $company_id;
                    Holiday::Create($input);
                    $tempdata['result'] = "success";
                }
            }
            $startRemoveDate = Carbon::parse($startOfYear)->next(Carbon::SATURDAY);
            for ($date = $startRemoveDate; $date->lte($endDate); $date->addWeek()) {
                $sundays[] = $date->format('Y-m-d');
            }
            foreach ($sundays as $sunday) {
                $holidays = Holiday::where('company_id',$company_id)->where('start_date', $sunday)->where('name', 'Weekly Off')->where('end_date', $sunday)->get();
                $exitsHoliday = $holidays->count();
                if ($exitsHoliday != 0) {
                    foreach ($holidays as $holiday) {
                        $row = Holiday::findOrFail($holiday->id);
                        $row->delete();
                        $tempdata['result'] = "success";
                    }
                }
            }
            if(config('settings.ncal')!=0){
              $holidayss = Holiday::where('company_id',$company_id)->where('start_date','<=',$request->engLastDate)->where('end_date','>=',$request->engFirstDate)->orderBy('start_date','ASC')->get();
              $data=$this->ajaxEvents($request->engFirstDate,$holidayss);
              $data['year'] = $request->getYear;
              $data['month'] = $request->getMonth;
              $data['result'] = $tempdata['result'];
              return $data;
            }else{
              return $tempdata;
            }
        } elseif($request->offtype == "both") {
            $startDate['sunday'] = Carbon::parse($startOfYear)->next(Carbon::SUNDAY);
            $startDate['saturday'] = Carbon::parse($startOfYear)->next(Carbon::SATURDAY);
            $endDate = Carbon::parse($endOfYear);
            $tempdata['result'] = "failed";
            for ($date = $startDate['sunday']; $date->lte($endDate); $date->addWeek()) {
                $sundays[] = $date->format('Y-m-d');
            }
            for ($date = $startDate['saturday']; $date->lte($endDate); $date->addWeek()) {
                $saturdays[] = $date->format('Y-m-d');
            }
            foreach ($saturdays as $saturday) {
                $exitsHoliday = Holiday::where('company_id',$company_id)->where('start_date', $saturday)->where('name', 'Weekly Off')->where('end_date', $saturday)->get()->count();
                if ($exitsHoliday == 0) {
                    $input['name'] = "Weekly Off";
                    $input['description'] = "Weekly Holiday by Company";
                    $input['start_date'] = $saturday;
                    $input['end_date'] = $saturday;
                    $input['company_id'] = $company_id;
                    Holiday::Create($input);
                    $tempdata['result'] = "success";
                }
            }
            foreach ($sundays as $saturday) {
                $exitsHoliday = Holiday::where('company_id',$company_id)->where('start_date', $saturday)->where('name', 'Weekly Off')->where('end_date', $saturday)->get()->count();
                if ($exitsHoliday == 0) {
                    $input['name'] = "Weekly Off";
                    $input['description'] = "Weekly Holiday by Company";
                    $input['start_date'] = $saturday;
                    $input['end_date'] = $saturday;
                    $input['company_id'] = $company_id;
                    Holiday::Create($input);
                    $tempdata['result'] = "success";
                }
            }
            if(config('settings.ncal')!=0){
              $holidayss = Holiday::where('company_id',$company_id)->where('start_date','<=',$request->engLastDate)->where('end_date','>=',$request->engFirstDate)->orderBy('start_date','ASC')->get();
              $data=$this->ajaxEvents($request->engFirstDate,$holidayss);
              $data['year'] = $request->getYear;
              $data['month'] = $request->getMonth;
              $data['result'] = $tempdata['result'];
              return $data;
            }else{
              return $tempdata;
            }

        } else {
            return $data['result'] = "failed";
        }


    }

    public function getCalendar(Request $request){
        $company_id = config('settings.company_id');
        $holidays = Holiday::where('company_id',$company_id)->where('start_date','<=',$request->engLastDate)->where('end_date','>=',$request->engFirstDate)->orderBy('start_date','ASC')->get();
        $data=$this->ajaxEvents($request->engFirstDate,$holidays);
        $data['maxCount'] = Holiday::where('company_id',$company_id)->where('start_date','>=',$request->engFirstDate)->where('end_date','<=',$request->engLastDate)->orderBy('start_date','ASC')->get()->count();
        $data['year'] = $request->getYear;
        $data['month'] = $request->getMonth;
        return $data;
    }

    private function ajaxEvents($engFirstDate,$holidays){
        $FirstRowFirstDate = $engFirstDate;
        $FirstRowLastDate = Carbon::parse($engFirstDate)->addDays(6)->format('Y-m-d');
        $SecondRowFirstDate = Carbon::parse($engFirstDate)->addDays(7)->format('Y-m-d');
        $SecondRowLastDate = Carbon::parse($engFirstDate)->addDays(13)->format('Y-m-d');
        $ThirdRowFirstDate = Carbon::parse($engFirstDate)->addDays(14)->format('Y-m-d');
        $ThirdRowLastDate = Carbon::parse($engFirstDate)->addDays(20)->format('Y-m-d');
        $FourthRowFirstDate = Carbon::parse($engFirstDate)->addDays(21)->format('Y-m-d');
        $FourthRowLastDate = Carbon::parse($engFirstDate)->addDays(27)->format('Y-m-d');
        $FifthRowFirstDate = Carbon::parse($engFirstDate)->addDays(28)->format('Y-m-d');
        $FifthRowLastDate = Carbon::parse($engFirstDate)->addDays(34)->format('Y-m-d');
        $SixthRowFirstDate = Carbon::parse($engFirstDate)->addDays(35)->format('Y-m-d');
        $SixthRowLastDate = Carbon::parse($engFirstDate)->addDays(41)->format('Y-m-d');
        $i=0;
        $data = [];
        foreach($holidays as $holiday){

          if($holiday->start_date<=$FirstRowFirstDate && $holiday->end_date>=$FirstRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $FirstRowFirstDate;
            $data['holidays'][$i]['end_date'] = $FirstRowLastDate;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }elseif($holiday->start_date<=$FirstRowFirstDate && $holiday->end_date>=$FirstRowFirstDate && $holiday->end_date<=$FirstRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $FirstRowFirstDate;
            $data['holidays'][$i]['end_date'] = $holiday->end_date;            
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;

          }elseif($holiday->start_date>=$FirstRowFirstDate && $holiday->start_date<=$FirstRowLastDate && $holiday->end_date>=$FirstRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $holiday->start_date;
            $data['holidays'][$i]['end_date'] = $FirstRowLastDate;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }elseif($holiday->start_date>=$FirstRowFirstDate && $holiday->end_date<=$FirstRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $holiday->start_date;
            $data['holidays'][$i]['end_date'] = $holiday->end_date;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }

          // Second row
          if($holiday->start_date<=$SecondRowFirstDate && $holiday->end_date>=$SecondRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $SecondRowFirstDate;
            $data['holidays'][$i]['end_date'] = $SecondRowLastDate;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }elseif($holiday->start_date<=$SecondRowFirstDate && $holiday->end_date>=$SecondRowFirstDate && $holiday->end_date<=$SecondRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $SecondRowFirstDate;
            $data['holidays'][$i]['end_date'] = $holiday->end_date;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;

          }elseif($holiday->start_date>=$SecondRowFirstDate && $holiday->start_date<=$SecondRowLastDate && $holiday->end_date>=$SecondRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $holiday->start_date;
            $data['holidays'][$i]['end_date'] = $SecondRowLastDate;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }elseif($holiday->start_date>=$SecondRowFirstDate && $holiday->end_date<=$SecondRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $holiday->start_date;
            $data['holidays'][$i]['end_date'] = $holiday->end_date;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }

          // Third Row
          if($holiday->start_date<=$ThirdRowFirstDate && $holiday->end_date>=$ThirdRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $ThirdRowFirstDate;
            $data['holidays'][$i]['end_date'] = $ThirdRowLastDate;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }elseif($holiday->start_date<=$ThirdRowFirstDate && $holiday->end_date>=$ThirdRowFirstDate && $holiday->end_date<=$ThirdRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $ThirdRowFirstDate;
            $data['holidays'][$i]['end_date'] = $holiday->end_date;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;

          }elseif($holiday->start_date>=$ThirdRowFirstDate && $holiday->start_date<=$ThirdRowLastDate && $holiday->end_date>=$ThirdRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $holiday->start_date;
            $data['holidays'][$i]['end_date'] = $ThirdRowLastDate;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }elseif($holiday->start_date>=$ThirdRowFirstDate && $holiday->end_date<=$ThirdRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $holiday->start_date;
            $data['holidays'][$i]['end_date'] = $holiday->end_date;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }

          //Fourth Row
          if($holiday->start_date<=$FourthRowFirstDate && $holiday->end_date>=$FourthRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $FourthRowFirstDate;
            $data['holidays'][$i]['end_date'] = $FourthRowLastDate;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }elseif($holiday->start_date<=$FourthRowFirstDate && $holiday->end_date>=$FourthRowFirstDate && $holiday->end_date<=$FourthRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $FourthRowFirstDate;
            $data['holidays'][$i]['end_date'] = $holiday->end_date;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;

          }elseif($holiday->start_date>=$FourthRowFirstDate && $holiday->start_date<=$FourthRowLastDate && $holiday->end_date>=$FourthRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $holiday->start_date;
            $data['holidays'][$i]['end_date'] = $FourthRowLastDate;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }elseif($holiday->start_date>=$FourthRowFirstDate && $holiday->end_date<=$FourthRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $holiday->start_date;
            $data['holidays'][$i]['end_date'] = $holiday->end_date;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }
          //Fifth Row
          if($holiday->start_date<=$FifthRowFirstDate && $holiday->end_date>=$FifthRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $FifthRowFirstDate;
            $data['holidays'][$i]['end_date'] = $FifthRowLastDate;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }elseif($holiday->start_date<=$FifthRowFirstDate && $holiday->end_date>=$FifthRowFirstDate && $holiday->end_date<=$FifthRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $FifthRowFirstDate;
            $data['holidays'][$i]['end_date'] = $holiday->end_date;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;

          }elseif($holiday->start_date>=$FifthRowFirstDate && $holiday->start_date<=$FifthRowLastDate && $holiday->end_date>=$FifthRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $holiday->start_date;
            $data['holidays'][$i]['end_date'] = $FifthRowLastDate;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }elseif($holiday->start_date>=$FifthRowFirstDate && $holiday->end_date<=$FifthRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $holiday->start_date;
            $data['holidays'][$i]['end_date'] = $holiday->end_date;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }

          // Sixth row
          if($holiday->start_date<=$SixthRowFirstDate && $holiday->end_date>=$SixthRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $SixthRowFirstDate;
            $data['holidays'][$i]['end_date'] = $SixthRowLastDate;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }elseif($holiday->start_date<=$SixthRowFirstDate && $holiday->end_date>=$SixthRowFirstDate && $holiday->end_date<=$SixthRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $SixthRowFirstDate;
            $data['holidays'][$i]['end_date'] = $holiday->end_date;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;

          }elseif($holiday->start_date>=$SixthRowFirstDate && $holiday->start_date<=$SixthRowLastDate && $holiday->end_date>=$SixthRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $holiday->start_date;
            $data['holidays'][$i]['end_date'] = $SixthRowLastDate;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }elseif($holiday->start_date>=$SixthRowFirstDate && $holiday->end_date<=$SixthRowLastDate){
            $data['holidays'][$i]['id'] = $holiday->id;
            $data['holidays'][$i]['name'] = $holiday->name;
            $data['holidays'][$i]['description'] = $holiday->description;
            $data['holidays'][$i]['start_date'] = $holiday->start_date;
            $data['holidays'][$i]['end_date'] = $holiday->end_date;
            $data['holidays'][$i]['ostart_date'] = $holiday->start_date;
            $data['holidays'][$i]['oend_date'] = $holiday->end_date;
            $data['holidays'][$i]['color'] = "#F96954";
            $i++;
          }
          // End Of rows data

        }

        return $data;

    }

}
