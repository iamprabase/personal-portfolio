<?php

namespace App\Http\Controllers\Company\Admin;

use App\Leave;
use App\Order;
use App\Client;
use App\Expense;
use App\NoOrder;
use App\Employee;
use App\Holiday;
use App\BeatVPlan;
use Carbon\Carbon;
use App\Attendance;
use App\Collection;
use Illuminate\Http\Request;
use App\Services\NepaliConverter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 

class AnalyticController extends Controller
{
    public function __construct(NepaliConverter $neDate)
    {
        $this->middleware('auth');
        $this->neDate = $neDate;
    }

    private function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message
        ];

        return response()->json($response, 200);
    }

    private function sendError($error, $errorMessage = [])
    {
        $response = [
            'success' => false,
            'message' => $error
        ];

        $response['data'] = (!empty($errorMessage)) ? $errorMessage : "";

        return response()->json($response, 404);
    }

    private function logicLayer($new_data)
    {
        $currencySymbol = ((config('settings.currency_symbol') == "Rs") || (config('settings.currency_symbol') == "")) ? "Rs." : config('settings.currency_symbol');
        $arr = [];
        foreach ($new_data as $new) {
            foreach ($new as $k => $v) {
                if (isset($arr[$k])) {
                    $arr[$k] += $v;
                } else {
                    $arr[$k] = $v;
                }
            }
        }
        $dates = array_keys($arr);
        $values = array_values($arr);

        $result_array = array(
            'dates' => $dates,
            'values' => $values,
            'currency_symbol' => $currencySymbol
        );

        return $result_array;
    }

    private function getTotalSeconds($employeeID,$date)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $attendances = Attendance::where('company_id', $company_id)
              ->where('employee_id', $employeeID)
              ->where('check_datetime', '>=', $date . ' 00:00:00')
              ->where('check_datetime', '<=', $date . ' 23:59:59')
              ->orderBy('check_datetime', 'Asc')
              ->get();
        //Set default flag 1 as looking for checkin and 2 for looking for checkout 
        $flagCheckType = 1;
        $workedTime = 0;
        foreach($attendances as $attendance){
            if($attendance->check_type==1 && $flagCheckType==1){
                $startTime = Carbon::parse($attendance->check_datetime);
                $flagCheckType = 2;
            }
            if($attendance->check_type==2 && $flagCheckType==2){
                $endTime = Carbon::parse($attendance->check_datetime);
                $flagCheckType = 1;
                $workedTime = $workedTime + (int)($startTime->diffInSeconds($endTime));
            }
        }

        //when looking for checkout time and if date is today setting endTime with current time
        if($flagCheckType==2 && $date==date('Y-m-d')){
            $endTime = Carbon::parse(date('Y-m-d H:i:s'));
            $workedTime = $workedTime + (int)($startTime->diffInSeconds($endTime));
        }

        //when looking for checkout time and if date is not today then setting endTime with endOfDay time
        if($flagCheckType==2 && $date!=date('Y-m-d')){
            $endTime = Carbon::parse($date.' 23:59:59');
            $workedTime = $workedTime + (int)($startTime->diffInSeconds($endTime));
        }
        //returning data in seconds
        return $workedTime;
    }

    private function getAbsentCount($startDate,$endDate,$empID,$company_id){     
        //Get all attendance between range for employee
        $attendances= Attendance::where('company_id',$company_id)->select('id','adate','employee_id')->groupBy('adate')->where('employee_id',$empID)->where('adate','>=',$startDate)->where('adate','<=',$endDate)->orderBy('adate','ASC')->get();

        //defining required varaibles
        $current_date = Carbon::now()->format('Y-m-d');
        $holiday_list =[];
        $beforeTodays = [];
        $data['absents']=[];

        //get employee created date
        $employeeFirstAttenDay = Employee::where('company_id',$company_id)->where('id',$empID)->first();
        $empCDate= Carbon::parse($employeeFirstAttenDay->created_at)->format('Y-m-d');
        $holidaysDatesLatestDays = Holiday::where('company_id',$company_id)->where('start_date','>',$empCDate)->where('start_date','<=',$current_date)->orderBy('start_date','ASC')->get();

        //getting holidays all dates 
        foreach($holidaysDatesLatestDays as $holiday){
            $start_date = Carbon::parse($holiday->start_date);
            $end_date = Carbon::parse($holiday->end_date);
            while($start_date<=$end_date){
                $holiday_list[] = $start_date->format('Y-m-d');
                $start_date = $start_date->addDays(1);
            }
        }


        //unsetting dates from beforeTodays
        if($empCDate>=$startDate){
            $start_date = Carbon::parse($empCDate);
        }else{
            $start_date = Carbon::parse($startDate);
        }        

        if($endDate<=$current_date){
            $end_date = Carbon::parse($endDate)->addDays(1);
        }else{
            $end_date = Carbon::parse($current_date);
        }
        while($start_date <= $end_date){
            $beforeTodays[] = $start_date->format('Y-m-d');
            $start_date = $start_date->addDays(1);
        }
        foreach($holiday_list as $holiday){
            if (($key = array_search($holiday, $beforeTodays)) !== false) {
                unset($beforeTodays[$key]);
            }
        }
        foreach ($attendances as $attendance) {
            if (($key = array_search($attendance->adate, $beforeTodays)) !== false) {
                unset($beforeTodays[$key]);
            }
        }
        foreach($beforeTodays as $absentdate){
            if($absentdate>=$startDate && $absentdate<=$endDate){
                $data['absents'][]=$absentdate;
            }
        }
        return count($data['absents']);
    }

    public function countParameters(Request $request)
    {
        $employeeInstance = Employee::findOrFail($request->id);
        $company_id = config('settings.company_id');

        $currencySymbol = ((config('settings.currency_symbol') == "Rs") || (config('settings.currency_symbol') == "")) ? "Rs." : config('settings.currency_symbol');

        try {
            if(empty($request->start_date)) {
                $startDate = Carbon::parse(Employee::where('id', $request->id)->oldest()->first()->created_at)->format("Y-m-d");
                $endDate = Carbon::now()->format("Y-m-d");
            } else {
                $startDate = $request->start_date;
                $endDate = $request->end_date;
            }

            $orders = $employeeInstance->orders()->orderDate($startDate, $endDate)->pluck('client_id')->toArray();
            $totalOrders = count($orders);

            $absentDays = ''.$this->getAbsentCount($startDate,$endDate,$request->id,$company_id).'';

            // if($startDate == "" && $endDate == "") {
            //     $leave1 = Leave::employeeId($request->id)->oldest()->first();
            //     $leave2 = Leave::employeeId($request->id)->latest()->first();
            //     if ($leave1 && $leave2) {
            //         $startDate2 = $leave1->start_date;
            //         $endDate2 = $leave2->end_date;
            //     } else {
            //         $startDate2 = "";
            //         $endDate2 = "";
            //     }
            //     $leaves = DB::select('select sum(datediff(l.end_date, l.start_date)+1) as total from leaves as l where employee_id = :id and status = :status and start_date >= :start_date and end_date <= :end_date', ['id' => $request->id, 'status' => "Approved", 'start_date' => $startDate2, 'end_date' => $endDate2]);
            // } else if($startDate == $endDate) {
            //     $leaves = DB::select('select count(start_date) as total from leaves where employee_id = :id and status = :status and start_date = :start_date', ['id' => $request->id, 'status' => "Approved", 'start_date' => $startDate]);
            //     if($leaves[0]->total == 0) {
            //         $leaves = DB::select('select count(end_date) as total from leaves where employee_id = :id and status = :status and end_date = :end_date', ['id' => $request->id, 'status' => "Approved", 'end_date' => $endDate]);
            //     }
            // } else {
            //     $leaves = DB::select('select sum(datediff(l.end_date, l.start_date)+1) as total from leaves as l where employee_id = :id and status = :status and start_date >= :start_date and end_date <= :end_date', ['id' => $request->id, 'status' => "Approved", 'start_date' => $startDate, 'end_date' => $endDate]);
            // }

            $totalOrderValue = Order::employeeId($request->id)->orderDate($startDate, $endDate)->sum('grand_total');

            $averageOrderValue = ($totalOrders == 0) ? 0 : $totalOrderValue/$totalOrders;

            //Average Work calculations
            $dates = [];
            if($startDate == "" && $endDate == "") {
                $clonedStartDate = Attendance::employeeId($request->id)->oldest()->first()->adate;
                $clonedEndDate = Attendance::employeeId($request->id)->latest()->first()->adate;
            } else {
                $clonedStartDate = $startDate;
                $clonedEndDate = $endDate;
            }
            if($clonedStartDate<=$clonedEndDate){
                //Dates Only Present Case Code
                $dates = Attendance::where('employee_id',$request->id)->where('adate','>=',$clonedStartDate)->where('adate','<=',$clonedEndDate)->orderBy('adate','ASC')->groupBy('adate')->pluck('adate')->toArray(); 
                
                //End Present Case
                $days = count($dates);

                $totalSeconds = 0;
                foreach($dates as $date){
                    $totalSeconds = $totalSeconds+(int)$this->getTotalSeconds($request->id,$date);
                }
                if($days>0){
                    $avgSeconds = (int)$totalSeconds/count($dates);
                    $hours = floor($avgSeconds / 3600);

                    $minutes = floor(($avgSeconds / 60) % 60);

                    $seconds = $avgSeconds % 60;

                    $avgWorkHour = $hours. ' hr '. $minutes. ' min '. $seconds. ' sec ('.$days. ' days)';
                }else{
                    $avgWorkHour = "No attendance found";
                }
            }else{
                return response(['status'=>false,'message'=>'Start Date can not be less than End Date']);
            }
            
            $company_id = config('settings.company_id');
            $getBeatPlanCalls = $this->salesmanBeatRouteReport($company_id, $startDate, $endDate, $request->id);

            $parameters = array(
                'total_orders' => $totalOrders,
                'total_zero_orders' => NoOrder::employeeId($request->id)->date($startDate, $endDate)->count(),
                'total_order_value' => number_format(round($totalOrderValue, 2), 2, ".", ","),
                'average_order_value' => number_format(round($averageOrderValue, 2), 2, ".", ","),
                'total_pending_collection_amount' => number_format(round(Collection::employeeId($request->id)->paymentDate($startDate, $endDate)->where('payment_method', "Cheque")->where('payment_status', "Pending")->sum('payment_received'), 2), 2, ".", ","),
                'total_cleared_collection_amount' => number_format(round(Collection::employeeId($request->id)->paymentDate($startDate, $endDate)->paymentStatus()->sum("payment_received"), 2), 2, ".", ","),
                'total_expense' => number_format(round(Expense::employeeId($request->id)->createdAt($startDate, $endDate." 23:59:59")->status()->sum('amount'), 2), 2, ".", ","),
                'present_days' => Attendance::employeeId($request->id)->adate($startDate, $endDate)->checkType()->distinct('adate')->count('adate'),
                'leaves' => $absentDays,
                'total_target_clients' => $getBeatPlanCalls['target_calls'],
                'scheduled_effective_calls' => $getBeatPlanCalls['employee_effective_calls'],
                'unscheduled_effective_calls' => $getBeatPlanCalls['unscheduled_effective_calls'],
                'average_working_hours'=>$avgWorkHour,
                'parties_added' => Client::createdBy($request->id)->createdAt($startDate, $endDate." 23:59:59")->count(),
                'currency_symbol' => $currencySymbol,
            );
            return $this->sendResponse($parameters, "Count parameters sent successfully");
        } catch (\Exception $error) {
            return $this->sendError($error, $error->getMessage());
        }
    }

    public function getNoOfOrders(Request $request)
    {
        $month_array = [];
        $custom_year_array = [];

        try {
            if(empty($request->start_date)) {
                  $employee=Employee::where('id', $request->id)->oldest()->first();
               $start = date('Y-m-d',strtotime($employee->created_at));
               $end = Carbon::now()->format('Y-m-d');
               $startDate = strtotime($start);
                $endDate = strtotime($end);
            } else {
                $startDate = strtotime($request->start_date);
                $endDate = strtotime($request->end_date);
            }

            $dateArray = [];

            for ($i=$startDate; $i <= $endDate; $i+=86400) { 
                array_push($dateArray, date("Y-m-d", $i));
            }

            $dates = $dateArray;

            for ($i = 0; $i < count($dates); $i++) {
                $orders = Order::employeeId($request->id)->where('order_date', $dates[$i])->count();
                $month = Carbon::parse($dates[$i])->format('M');
                $year = Carbon::parse($dates[$i])->format('Y');
                array_push($month_array, $month);
                if($request->type=="nepali"){
                    $expDate = explode('-',$dates[$i]);
                    $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
                    $nepDate = explode('-',$nepDate);
                    $month = $this->neDate->getNepaliMonthName($nepDate[1]);
                    $year = $nepDate[0];
                }
                array_push($custom_year_array, $year);
                $new_order_data_yearly[][$year] = (float) $orders;
                $new_order_data_monthly[][$month] =(float) $orders;
                $new_order_data[][$dates[$i]] =(float) $orders;
            }
            
            $diffStartDate = Carbon::parse($dates[0]);
            $diffEndDate = Carbon::parse($dates[count($dates)-1]);
            $diff = $diffStartDate->diffInMonths($diffEndDate);
            $diffDays = $diffStartDate->diffInDays($diffEndDate);

            if ($diffDays > 45) {
                $response = ($diff <= 11) ? $this->logicLayer($new_order_data_monthly) : $this->logicLayer($new_order_data_yearly);
            } else {
                $response = $this->logicLayer($new_order_data);
            }

            return $this->sendResponse($response, "Data sent successfully");
        } catch (\Exception $error) {
            return $this->sendError($error, $error->getMessage());
        }
    }

    public function getOrderValue(Request $request)
    {
        $month_array = [];
        $custom_year_array = [];

        try {

             if(empty($request->start_date)) {
                  $employee=Employee::where('id', $request->id)->oldest()->first();
               $start = date('Y-m-d',strtotime($employee->created_at));
               $end = Carbon::now()->format('Y-m-d');
               $startDate = strtotime($start);
                $endDate = strtotime($end);
            } else {
                $startDate = strtotime($request->start_date);
                $endDate = strtotime($request->end_date);
            }

            $dateArray = [];

            for ($i=$startDate; $i <= $endDate; $i+=86400) { 
                array_push($dateArray, date("Y-m-d", $i));
            }

            $dates = $dateArray;

            for ($i = 0; $i < count($dates); $i++) {
                $order = Order::employeeId($request->id)->where('order_date', $dates[$i])->sum('grand_total');
                $month = Carbon::parse($dates[$i])->format('M');
                $year = Carbon::parse($dates[$i])->format('Y');
                array_push($month_array, $month);
                if($request->type=="nepali"){
                    $expDate = explode('-',$dates[$i]);
                    $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
                    $nepDate = explode('-',$nepDate);
                    $month = $this->neDate->getNepaliMonthName($nepDate[1]);
                    $year = $nepDate[0];
                }
                array_push($custom_year_array, $year);
                $new_order_value_data_yearly[][$year] = round($order, 2);
                $new_order_value_data_monthly[][$month] = round($order, 2);
                $new_order_data[][$dates[$i]] = round($order, 2);
            }

            $diffStartDate = Carbon::parse($dates[0]);
            $diffEndDate = Carbon::parse($dates[count($dates)-1]);
            $diff = $diffStartDate->diffInMonths($diffEndDate);
            $diffDays = $diffStartDate->diffInDays($diffEndDate);
            if ($diffDays > 45) {
                $response = ($diff <= 11) ? $this->logicLayer($new_order_value_data_monthly) : $this->logicLayer($new_order_value_data_yearly);
            } else {
                $response = $this->logicLayer($new_order_data);
            }

            return $this->sendResponse($response, "Dates & Order Values sent successfully");
        } catch (\Exception $error) {
            return $this->sendError($error, $error->getMessage());
        }
    }

    public function getCollectionAmount(Request $request)
    {
        $month_array = [];
        $custom_year_array = [];

        try {
             if(empty($request->start_date)) {
                  $employee=Employee::where('id', $request->id)->oldest()->first();
               $start = date('Y-m-d',strtotime($employee->created_at));
               $end = Carbon::now()->format('Y-m-d');
               $startDate = strtotime($start);
                $endDate = strtotime($end);
            } else {
                $startDate = strtotime($request->start_date);
                $endDate = strtotime($request->end_date);
            }

            $dateArray = [];

            for ($i=$startDate; $i <= $endDate; $i+=86400) { 
                array_push($dateArray, date("Y-m-d", $i));
            }

            $dates = $dateArray;

            for ($i = 0; $i < count($dates); $i++) {
                $collection = Collection::employeeId($request->id)->where('payment_date', $dates[$i])->paymentStatus()->sum('payment_received');
                $month = Carbon::parse($dates[$i])->format('M');
                $year = Carbon::parse($dates[$i])->format('Y');
                array_push($month_array, $month);
                if($request->type=="nepali"){
                    $expDate = explode('-',$dates[$i]);
                    $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
                    $nepDate = explode('-',$nepDate);
                    $month = $this->neDate->getNepaliMonthName($nepDate[1]);
                    $year = $nepDate[0];
                }
                array_push($custom_year_array, $year);
                $new_collection_data_yearly[][$year] = (float) $collection;
                $new_collection_data_monthly[][$month] = (float) $collection;
                $new_collection_data[][$dates[$i]] = (float) $collection;
            }

            $diffStartDate = Carbon::parse($dates[0]);
            $diffEndDate = Carbon::parse($dates[count($dates)-1]);
            $diff = $diffStartDate->diffInMonths($diffEndDate);
            $diffDays = $diffStartDate->diffInDays($diffEndDate);
            if ($diffDays > 45) {
                $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
            } else {
                $response = $this->logicLayer($new_collection_data);
            }
            return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
        } catch (\Exception $error) {
            return $this->sendError($error, $error->getMessage());
        }
    }

    public function topParties(Request $request)
    {
        try {
            $OrderClients = [];
            $collectionClients = [];
            $parties = [];
            $collectionParties = [];
            $sum_array = [];
            $collection_sum_array = [];

            $currencySymbol = ((config('settings.currency_symbol') == "Rs") || (config('settings.currency_symbol') == "")) ? "Rs." : config('settings.currency_symbol');

            $clients = DB::table('handles')->where('employee_id', $request->id)->pluck('client_id')->toArray();

            for ($i = 0; $i < count($clients); $i++) {
                $order_sum = Order::where('client_id', $clients[$i])->orderDate($request->start_date, $request->end_date)->sum('grand_total');
                $collection_sum = Collection::where('client_id', $clients[$i])->paymentDate($request->start_date, $request->end_date)->paymentStatus()->sum("payment_received");
                if($order_sum != 0) {
                    $sum_array[][$clients[$i]] = (float) $order_sum;
                }
                if($collection_sum != 0) {
                    $collection_sum_array[][$clients[$i]] = (float) $collection_sum;
                }
            }

            foreach ($sum_array as $new) {
                foreach ($new as $k => $v) {
                    if (isset($parties[$k])) {
                        $parties[$k] += $v;
                    } else {
                        $parties[$k] = $v;
                    }
                }
            }

            foreach ($collection_sum_array as $new) {
                foreach ($new as $k => $v) {
                    if (isset($collectionParties[$k])) {
                        $collectionParties[$k] += $v;
                    } else {
                        $collectionParties[$k] = $v;
                    }
                }
            }

            uasort($parties, function ($a, $b) {
                if ($a == $b) {
                    return 0;
                }

                return ($a > $b) ? -1 : 1;
            });
            uasort($collectionParties, function ($a, $b) {
                if ($a == $b) {
                    return 0;
                }

                return ($a > $b) ? -1 : 1;
            });

            $parties = array_slice($parties, 0, 10, true);
            $collectionParties = array_slice($collectionParties, 0, 10, true);

            $client_ids = array_keys($parties);
            $client_ids_for_collection = array_keys($collectionParties);

            $order_sum = array_values($parties);
            $collection_sum = array_values($collectionParties);

            for ($i = 0; $i < count($client_ids); $i++) {
                $clients_collection = Client::where('id', $client_ids[$i])->get();
                foreach ($clients_collection as $client) {
                    array_push($OrderClients, $client->company_name);
                }
            }
            for ($i = 0; $i < count($client_ids_for_collection); $i++) {
                $clients_collection = Client::where('id', $client_ids_for_collection[$i])->get();
                foreach ($clients_collection as $client) {
                    array_push($collectionClients, $client->company_name);
                }
            }

            $response = array(
                'client_name' => $OrderClients,
                'order_sum' => $order_sum,
                'client_name_for_collection' => $collectionClients,
                'collection_sum' => $collection_sum,
                'currency_symbol' => $currencySymbol
            );
            return $this->sendResponse($response, "Top 10 parties sent successfully");
        } catch (\Exception $error) {
            return $this->sendError($error, $error->getMessage());
        }
    }

    public function countPartiesParameters(Request $request)
    {
        try {
            $currencySymbol = ((config('settings.currency_symbol') == "Rs") || (config('settings.currency_symbol') == "")) ? "Rs." : config('settings.currency_symbol');
            $lastVisited = Order::clientId($request->id)->latest()->first();
            $lastVisitedDate = ($lastVisited) ? $lastVisited->order_date : "No orders yet";
            $productsSold = Order::join('orderproducts', 'orders.id', '=', 'orderproducts.order_id')
                ->where('orders.client_id', '=', $request->id)
                ->orderDate($request->start_date, $request->end_date)
                ->distinct('product_id')
                ->pluck('product_id');

            $parameters = array(
                'total_orders' => Order::clientId($request->id)->orderDate($request->start_date, $request->end_date)->count(),
                'total_order_value' => number_format(Order::clientId($request->id)->orderDate($request->start_date, $request->end_date)->sum('grand_total'), 2, ".", ","),
                'total_pending_collection_amount' => number_format(Collection::clientId($request->id)->paymentDate($request->start_date, $request->end_date)->where('payment_method', "Cheque")->where('payment_status', "Pending")->sum('payment_received'), 2, ".", ","),
                'total_cleared_collection_amount' => number_format(Collection::clientId($request->id)->paymentDate($request->start_date, $request->end_date)->paymentStatus()->sum("payment_received"), 2, ".", ","),
                'products_sold' => count($productsSold),
                'last_visited' => $lastVisitedDate,
                'currency_symbol' => $currencySymbol
            );
            return $this->sendResponse($parameters, "Parties parameters sent successfully");
        } catch (\Exception $error) {
            return $this->sendError($error, $error->getMessage());
        }
    }

    public function getNoOfOrdersForParties(Request $request)
    {
        $month_array = [];
        $custom_year_array = [];

        try {
            if(empty($request->start_date)) {
                  $client=Client::where('id', $request->id)->oldest()->first();
               $start = date('Y-m-d',strtotime($client->created_at));
               $end = Carbon::now()->format('Y-m-d');
               $startDate = strtotime($start);
                $endDate = strtotime($end);
            } else {
                $startDate = strtotime($request->start_date);
                $endDate = strtotime($request->end_date);
            }

            $dateArray = [];

            for ($i=$startDate; $i <= $endDate; $i+=86400) { 
                array_push($dateArray, date("Y-m-d", $i));
            }

            $dates = $dateArray;

            for ($i = 0; $i < count($dates); $i++) {
                $orders = Order::clientId($request->id)->where('order_date', $dates[$i])->count();
                $month = Carbon::parse($dates[$i])->format('M');
                $year = Carbon::parse($dates[$i])->format('Y');
                array_push($month_array, $month);
                if($request->type=="nepali"){
                    $expDate = explode('-',$dates[$i]);
                    $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
                    $nepDate = explode('-',$nepDate);
                    $month = $this->neDate->getNepaliMonthName($nepDate[1]);
                    $year = $nepDate[0];
                }
                array_push($custom_year_array, $year);
                $new_order_data_yearly[][$year] = (float) $orders;
                $new_order_data_monthly[][$month] = (float) $orders;
                $new_order_data[][$dates[$i]] = (float) $orders;
            }

            $diffStartDate = Carbon::parse($dates[0]);
            $diffEndDate = Carbon::parse($dates[count($dates)-1]);
            $diff = $diffStartDate->diffInMonths($diffEndDate);
            $diffDays = $diffStartDate->diffInDays($diffEndDate);
            if ($diffDays > 45) {
                $response = ($diff <= 11) ? $this->logicLayer($new_order_data_monthly) : $this->logicLayer($new_order_data_yearly);
            } else {
                $response = $this->logicLayer($new_order_data);
            }

            return $this->sendResponse($response, "Data sent successfully");
        } catch (\Exception $error) {
            return $this->sendError($error, $error->getMessage());
        }
    }


    public function getOrderValueForParties(Request $request)
    {
        $month_array = [];
        $custom_year_array = [];

        try {
            if(empty($request->start_date)) {
                  $client=Client::where('id', $request->id)->oldest()->first();
               $start = date('Y-m-d',strtotime($client->created_at));
               $end = Carbon::now()->format('Y-m-d');
               $startDate = strtotime($start);
                $endDate = strtotime($end);
            } else {
                $startDate = strtotime($request->start_date);
                $endDate = strtotime($request->end_date);
            }

            $dateArray = [];

            for ($i=$startDate; $i <= $endDate; $i+=86400) { 
                array_push($dateArray, date("Y-m-d", $i));
            }

            $dates = $dateArray;
            for ($i = 0; $i < count($dates); $i++) {
                $orders = (float) Order::clientId($request->id)->where('order_date', $dates[$i])->sum('grand_total');
                $month = Carbon::parse($dates[$i])->format('M');
                $year = Carbon::parse($dates[$i])->format('Y');
                array_push($month_array, $month);
                if($request->type=="nepali"){
                    $expDate = explode('-',$dates[$i]);
                    $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
                    $nepDate = explode('-',$nepDate);
                    $month = $this->neDate->getNepaliMonthName($nepDate[1]);
                    $year = $nepDate[0];
                }
                array_push($custom_year_array, $year);
                $new_order_data_yearly[][$year] = (float) $orders;
                $new_order_data_monthly[][$month] = (float) $orders;
                $new_order_data[][$dates[$i]] = (float) $orders;
            }

            $diffStartDate = Carbon::parse($dates[0]);
            $diffEndDate = Carbon::parse($dates[count($dates)-1]);
            $diff = $diffStartDate->diffInMonths($diffEndDate);
            $diffDays = $diffStartDate->diffInDays($diffEndDate);
            if ($diffDays > 45) {
                $response = ($diff <= 11) ? $this->logicLayer($new_order_data_monthly) : $this->logicLayer($new_order_data_yearly);
            } else {
                $response = $this->logicLayer($new_order_data);
            }

            return $this->sendResponse($response, "Data sent successfully");
        } catch (\Exception $error) {
            return $this->sendError($error, $error->getMessage());
        }
    }

    public function getCollectionAmountForParties(Request $request)
    {
        $month_array = [];
        $custom_year_array = [];

        try {
            if(empty($request->start_date)) {
                 $client=Client::where('id', $request->id)->oldest()->first();
               $start = date('Y-m-d',strtotime($client->created_at));
               $end = Carbon::now()->format('Y-m-d');
               $startDate = strtotime($start);
                $endDate = strtotime($end);
            } else {
                $startDate = strtotime($request->start_date);
                $endDate = strtotime($request->end_date);
            }

            $dateArray = [];

            for ($i=$startDate; $i <= $endDate; $i+=86400) { 
                array_push($dateArray, date("Y-m-d", $i));
            }

            $dates = $dateArray;
            
            for ($i = 0; $i < count($dates); $i++) {
                $collection = (float) Collection::clientId($request->id)->whereDate('payment_date', $dates[$i])->PaymentStatus()->sum('payment_received');
                $month = Carbon::parse($dates[$i])->format('M');
                $year = Carbon::parse($dates[$i])->format('Y');
                array_push($month_array, $month);
                if($request->type=="nepali"){
                    $expDate = explode('-',$dates[$i]);
                    $nepDate =$this->neDate->eng_to_nep($expDate[0],$expDate[1],$expDate[2]);
                    $nepDate = explode('-',$nepDate);
                    $month = $this->neDate->getNepaliMonthName($nepDate[1]);
                    $year = $nepDate[0];
                }
                array_push($custom_year_array, $year);
                $new_collection_data_yearly[][$year] = (float) $collection;
                $new_collection_data_monthly[][$month] = (float) $collection;
                $new_collection_data[][$dates[$i]] = (float) $collection;
            }

            $diffStartDate = Carbon::parse($dates[0]);
            $diffEndDate = Carbon::parse($dates[count($dates)-1]);
            $diff = $diffStartDate->diffInMonths($diffEndDate);
            $diffDays = $diffStartDate->diffInDays($diffEndDate);
            if ($diffDays > 45) {
                $response = ($diff <= 11) ? $this->logicLayer($new_collection_data_monthly) : $this->logicLayer($new_collection_data_yearly);
            } else {
                $response = $this->logicLayer($new_collection_data);
            }

            return $this->sendResponse($response, "Dates & Collection Amount sent successfully");
        } catch (\Exception $error) {
            return $this->sendError($error, $error->getMessage());
        }
    }

    public function getEmployeeFirstDate(Request $request) {
        try {
            $response = Carbon::parse(Employee::where('id', $request->id)->oldest()->first()->created_at)->format('Y-m-d');
            return $this->sendResponse($response, "Employee first date sent successfully");
        } catch (\Exception $error) {
            return $this->sendError($error, $error->getMessage());
        }
    }

    public function getClientFirstDate(Request $request) {
        try {
            $response = Carbon::parse(Client::where('id', $request->id)->oldest()->first()->created_at)->format('Y-m-d');
            return $this->sendResponse($response, "Client first date sent successfully");
        } catch (\Exception $error) {
            return $this->sendError($error, $error->getMessage());
        }
    }

    private function salesmanBeatRouteReport($companyId, $startDate, $endDate, $empId) {

        $company_id = $companyId;
        $fromdate = $startDate;
        $todate = $endDate;

        $beats = BeatVPlan::where('company_id', $company_id)
                ->join('beatplansdetails','beatplansdetails.beatvplan_id','beatvplans.id')
                ->whereBetween('beatplansdetails.plandate', [$fromdate, $todate])
                ->where('employee_id', $empId)
                ->orderby('beatplansdetails.plandate', 'desc')
                ->get();

        $company_clients = Client::where('company_id', $company_id)
                            ->orderby('company_name', 'asc')
                            ->get();
        
        $orders = Order::where('company_id', $company_id)
                ->where('employee_id', $empId)
                ->whereBetween('order_date', [$fromdate, $todate])
                ->orderBy('client_id', 'asc')
                ->get();

        $no_orders = NoOrder::where('company_id', $company_id)
                    ->where('employee_id', $empId)
                    ->whereBetween('date', [$fromdate, $todate])
                    ->orderBy('client_id', 'asc')
                    ->get();
        $employee_target_visits = array();
        $employee_effective_calls = array();
        $unscheduled_effective_calls = array();
        if($beats->first()){
            
        foreach($beats as $beat){
            $client_ids = explode(',',$beat->client_id);
            $beat_ids = explode(',',$beat->beat_id);
            $date = $beat->plandate;
            $employeeTargetVisits = $company_clients->whereIn('id',$client_ids)
                                                        ->pluck('company_name','id')->toArray();
            $employee_target_visits = array_merge($employeeTargetVisits, $employee_target_visits);

            $orders_clients = $orders->where('order_date', '=', $date)->whereIn('client_id', $client_ids)->pluck('client_id')->toArray();

            $no_orders_clients = $no_orders->where('date', '=', $date)->whereIn('client_id', $client_ids)->pluck('client_id')->toArray(); 

            $employeeEffectiveCalls = $company_clients->whereIn('id',$orders_clients)
                                                        ->pluck('company_name','id')->toArray();
            $employee_effective_calls = array_merge($employeeEffectiveCalls, $employee_effective_calls);
            
            $party_added = $company_clients
                            ->where('created_by', $empId)
                            ->where('created_at', 'like', $date.'%')
                            ->pluck('id')
                            ->toArray();

            $unscheduled_effective_calls_clients = $orders
                                                    ->where('order_date', '=', $date)
                                                    ->whereNotIn('client_id', $client_ids)
                                                    ->pluck('client_id')
                                                    ->toArray();

            $unscheduledEffectiveCalls = $company_clients
                                                ->whereIn('id',$unscheduled_effective_calls_clients)
                                                ->pluck('company_name','id')
                                                ->toArray();
            $unscheduled_effective_calls = array_merge($unscheduledEffectiveCalls, $unscheduled_effective_calls);

            unset($orders_clients);
            unset($no_orders_clients);
            unset($unscheduled_effective_calls_clients);
            unset($party_added);
            unset($beat_ids);
        }
    }

    $result['target_calls'] = sizeof($employee_target_visits);
    $result['employee_effective_calls'] = sizeof($employee_effective_calls);
    $result['unscheduled_effective_calls'] = sizeof($unscheduled_effective_calls);

    return $result;
  }
}
