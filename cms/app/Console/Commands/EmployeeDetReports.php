<?php

namespace App\Console\Commands;

use DB;
use Log;
use Auth;
use View;
use Excel;
use Storage;
use App\Beat;
use App\User;
use DateTime;
use App\Brand;
use App\Order;
use App\Stock;
use App\Client;
use DatePeriod;
use App\Manager;
use App\NoOrder;
use App\Product;
use App\Category;
use App\Employee;
use App\Location;
use App\Holiday;
use App\Collection;
use App\ClientVisit;
use DateInterval;
use App\BeatVPlan;
use App\PartyType;
use App\UnitTypes;
use Carbon\Carbon;
use App\Attendance;
use App\OrderDetails;
use App\ReturnDetail;
use App\ClientSetting;
use App\ProductReturn;
use App\GenerateReport;
use App\ProductVariant;
use App\ModuleAttribute;
use App\BeatPlansDetails;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Exports\MultiSheetExport;
use Barryvdh\DomPDF\Facade as PDF;
use App\Leave;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\API\AnalyticController;

class EmployeeDetReports extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'EmployeeDetails:report';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate excel EmployeeDetails report';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
      parent::__construct();
      // $this->middleware('auth');
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */

  private function getCompanySettings($company_id){
    try{
      $setting = ClientSetting::whereCompanyId($company_id)->first()->toArray();
      
      return $setting;
    }catch(\Exception $e){
      Log::error($e->getMessage());
      return 0;
    }
  }

 
  public function handle()
  {
    
    $processing_stock_reports_count = GenerateReport::where('processing','1')->count();
    
    if($processing_stock_reports_count<3){
      $processing = GenerateReport::whereReportType('employeedetails_report')
                ->whereNULL('download_link')
                ->whereNULL('processing')
                ->first();
      if($processing){
        try{
          $reportid = $processing->id;

          $processing->processing = 1;
          $processing->save();
          
          $company_id = $processing->company_id;
          $report_type = $processing->report_type;
          $report_cat = $processing->report_cat;
          $generated_by = $processing->generated_by;
          $module_access = json_decode($processing->module_access);
          $type = 'xlsx';
          $today = date('Y-m-d');
          $empids =json_decode($processing->employee_id);
          $startDate = $processing->start_date;
          $endDate = $processing->end_date;
          if($startDate==$endDate){
            if($this->getCompanySettings($company_id)['ncal']==0){
              $date = $startDate;
            }else{
              $date = getDeltaDateForReport($startDate);
            }
          }else{
            if($this->getCompanySettings($company_id)['ncal']==0){
              $date = $startDate.'-'.$endDate;
            }else{
              $date = getDeltaDateForReport($startDate).'-'.getDeltaDateForReport($endDate);
            }
          }

          $header = array();
          $available_module = array('stc'=>'Target Calls','tc'=>'Total Calls','to'=>'Orders','zo'=>'Zero Orders','ov'=>'Order Value','coll'=>'Collection','ch2dep'=>'Cheques to be Deposited','tvst'=>'Total Visits','ttmvist'=>'Time spent on Visits','psld'=>'Total Products Sold','ectc'=>'EC:TC(Productivity %)','prstdays'=>'Present Days','lvs'=>'Leaves','newprt'=>'New Parties Added');
          array_push($header,'Employee Name');
          foreach($available_module as $aab=>$baa){
            if(in_array($aab,$module_access)){
              array_push($header,$baa);
            }
          }

          $currency_symbol = ClientSetting::where('company_id',$company_id)->get(['currency_symbol','default_currency'])->toArray();
          $currency_sym = $currency_symbol[0]['currency_symbol'];
          
          // $header = array("EmployeeName","Scheduled Target Calls","Total Calls","Total Orders","Zero Orders","Order Value","Collection","Cheques to be Deposited","Total Visits","Total Time spent on Visits","Total Products Sold","EC:TC(Productivity %)","Present Days","Leaves","New Parties Added");
          $excel_data[] = $header;
          $employeeIDS = Employee::whereIn('id',$empids)->where('company_id',$company_id)->pluck('name','id')->toArray();

          if(strcmp($startDate,$endDate)==0){
            $startDate = $startDate.' 00:00:00';
            $endDate = $endDate.' 23:59:59';
          }

          foreach($employeeIDS as $empid=>$name){
            $data['empname'] = $name;
            $schedulet_tgtcalls = BeatVPlan::where('employee_id',$empid)
                                  ->Join('beatplansdetails','beatvplans.id','beatplansdetails.beatvplan_id')
                                  ->whereBetween('plandate',[$startDate,$endDate])->get()->toArray();
            $totalclients = 0;
            foreach($schedulet_tgtcalls as $y=>$tt){
              $client_all = explode(',',$tt['client_id']);
              $client_status = Client::whereIn('id',$client_all)->get()->toArray();
              if(count($client_status)>0){
                $totalclients += count($client_status);
              }
            }
            if(in_array('stc',$module_access)){
              $data['scheduledcalls'] = $totalclients;
            }
            $totalorder = Order::where('employee_id',$empid)->where('company_id',$company_id)->whereBetween('order_date',[$startDate,$endDate])->count();
            $zeroorder = NoOrder::where('employee_id',$empid)->where('company_id',$company_id)->whereBetween('date',[$startDate,$endDate])->count();
            if(in_array('tc',$module_access)){
              $data['totalcalls'] = $totalorder+$zeroorder;
            }
            if(in_array('to',$module_access)){
              $data['totalorder'] = $totalorder;
            }
            if(in_array('zo',$module_access)){
              $data['zeroorder'] = $zeroorder;
            }
            if(in_array('ov',$module_access)){
              $data['ordervalue'] = $currency_sym.' '.Order::where('employee_id',$empid)->whereBetween('order_date',[$startDate,$endDate])->where('company_id',$company_id)->sum('grand_total');
            }
            if(in_array('coll',$module_access)){
              $data['collectionvalue'] = $currency_sym.' '.Collection::where('employee_id',$empid)->whereBetween('payment_date',[$startDate,$endDate])->where('company_id',$company_id)->where('payment_status','Cleared')->get()->sum('payment_received');
            }
            if(in_array('ch2dep',$module_access)){
              $data['chq_2deposit'] = $currency_sym.' '.Collection::where('company_id',$company_id)->where('employee_id',$empid)->whereBetween('cheque_date',[$startDate,$endDate])->where('payment_method','Cheque')->where('payment_status','Pending')->sum('payment_received');
            }
            if(in_array('tvst',$module_access)){
              $totalvisits = ClientVisit::where('employee_id',$empid)->whereBetween('date',[$startDate,$endDate])->where('company_id',$company_id)->get();
              $data['total_visits'] = $totalvisits->count();
            }
            if(in_array('ttmvist',$module_access)){
              $totalvisits = ClientVisit::where('employee_id',$empid)->whereBetween('date',[$startDate,$endDate])->where('company_id',$company_id)->get();
              $tot_visittime = '';$tot_time = 0;
              foreach($totalvisits->toArray() as $ret=>$ter){
                $stime = strtotime($ter['start_time']);
                $etime = strtotime($ter['end_time']);
                $tot_time += $etime-$stime;
              }
              if($tot_time>=0 && $tot_time<=60){
                $tot_visittime = $tot_time.' seconds ';
              }else if($tot_time>60 && $tot_time<=3600){
                $minutes = round((int)($tot_time/60),2).' minutes ';
                $second = round((int)($tot_time%60),2).' seconds';
                if($second==0){
                $tot_visittime = $minutes;
                }else{
                $tot_visittime = $minutes.$second;
                }
              }else{
                $hours = round((int)($tot_time/3600),2).' Hrs ';
                $minutes = round((int)($tot_time%60),2).' minutes';
                if($minutes==0){
                    $tot_visittime = $hours;
                }else{
                    $tot_visittime = $hours.$minutes;
                }
              }
              $data['totalspent_onvisit'] = $tot_visittime;
            }
            if(in_array('psld',$module_access)){
              $products_sold = Order::select('products.product_name',DB::raw('count(orderproducts.id) as totcount'))
                                  ->Join('orderproducts', 'orders.id', 'orderproducts.order_id')
                                  ->Join('products', 'orderproducts.product_id', 'products.id')
                                  ->where('orders.company_id', $company_id)
                                  ->where('orders.employee_id', $empid)
                                  ->whereBetween('orders.order_date',[$startDate,$endDate])
                                  ->groupby('orderproducts.product_id')
                                  ->get()->toArray();
              // $productssold_count = 0;
              // if(count($products_sold)>0){
              //   foreach($products_sold as $ps=>$sp){
                  // $productssold_count += (int)$sp['totcount'];
              //   }
              // }
              $data['products_sold'] = count($products_sold);
            }
            if(in_array('ectc',$module_access)){
              $totalorder = Order::where('employee_id',$empid)->where('company_id',$company_id)->whereBetween('order_date',[$startDate,$endDate])->count();
              $zeroorder = NoOrder::where('employee_id',$empid)->where('company_id',$company_id)->whereBetween('date',[$startDate,$endDate])->count();
              $total_calls = $totalorder+$zeroorder;
              $total_order = $totalorder;
              $data['productivity_perc'] = ($total_calls!=0)?round(($total_order/$total_calls)*100,2).'%':'0%';
            }
            if(in_array('prstdays',$module_access)){
              // $data['present_days'] = Attendance::where('employee_id',$empid)->whereBetween('adate',[$startDate,$endDate])->count('id');
              $data['present_days'] = Attendance::employeeId($empid)->adate($startDate, $endDate)->checkType()->distinct('adate')->count('adate');
            }
            if(in_array('lvs',$module_access)){
              // $leaves = Leave::where('employee_id',$empid)->where('status','Approved')->where('start_date','>=',$startDate)->get()->toArray();
              // $actual_leaves = 0;
              // foreach($leaves as $l=>$v){
              //   $leaves_starttime = Carbon::parse($v['start_date']);
              //   $leaves_endtime = Carbon::parse($v['end_date']);
              //   $formatted_enddate = Carbon::parse($endDate);
              //   $total_leaves = 0;
              //   if($formatted_enddate<$leaves_endtime){
              //     $total_leaves = $leaves_endtime->diffInDays($formatted_enddate);
              //   }
              //   $leaves_calc = $leaves_starttime->diffInDays($leaves_endtime);
              //   $actual_leaves += $leaves_calc-$total_leaves;
              // }
              // $data['leaves'] = $actual_leaves;
              $data['leaves'] = $this->getAbsentCount($startDate,$endDate,$empid,$company_id);
            }
            if(in_array('newprt',$module_access)){
              $part_sdate = $startDate.' 00:00:00';
              $part_edate = $endDate.' 23:59:59';
              $data['newpartiesadded'] = Client::where('created_by',$empid)->whereBetween('created_at',[$part_sdate,$part_edate])->count();
            }
            
            $excel_data[] = $data; 
          }
          
          if(count($employeeIDS)==1){
            try{
              $name = str_replace(" ","",getClient($employeeIDS)->company_name);
            }catch(\Exception $e){
              $name = str_replace(" ","","Client Company Name");
            }
          }else{
            $name = "Multiple";
          }
          $time = time();
          $filename_download = 'EmployeeReports_'.$date;
          
          $exportSheet['Consolidated'] = $excel_data;
          $_excel = Excel::store(new MultiSheetExport($exportSheet), urlencode($filename_download.$time).'.'.$type, 'EmployeeDetReport');
          
          $processing->filename = urlencode($filename_download.$type);
          $processing->download_link = env('APP_URL').'storage/excel/reports/EmployeeDetReport/'.urlencode($filename_download.$time).'.'.$type;
          $processing->processing = 2;
          $processing->update();
        }catch(\Exception $e){
          Log::error($e->getMessage());
          $processing->processing = 0;
          $processing->save();
        }
      }
    }


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


  
}
