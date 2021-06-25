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
use App\Expense;
use App\Note;
use DatePeriod;
use App\Manager;
use App\NoOrder;
use App\Product; 
use App\Category;
use App\Employee;
use App\Location;
use App\Collection;
use App\ClientVisit;
use App\Activity;
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

class PartyDetReports extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'PartyDetails:report';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate excel PartyDetails report';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
      parent::__construct();
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
      $processing = GenerateReport::whereReportType('partydetails_report')
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
          $clientids =json_decode($processing->party_id);
          $startDate = $processing->start_date;
          $endDate = $processing->end_date;
          if($startDate==$endDate){
            if($this->getCompanySettings($company_id)['ncal']==0){
              $date = $startDate;
            }else{
              $date = getDeltaDateForReport_mr($company_id,$startDate);
            }
          }else{
            if($this->getCompanySettings($company_id)['ncal']==0){
              $date = $startDate.'-'.$endDate;
            }else{
              $date = getDeltaDateForReport_mr($company_id,$startDate).'-'.getDeltaDateForReport_mr($company_id,$endDate);
            }
          }

          $header = array();
          $available_module = array('ord'=>'Orders','zo'=>'Zero Orders','tc'=>'Total Calls','ov'=>'Order Value','coll'=>'Collection','ch2dep'=>'Cheques to be Deposited','tvst'=>'No. of Visits','ttmvist'=>'Time spent on Visits','psld'=>'Total Products Sold','ectc'=>'EC:TC(Productivity %)','lod'=>'Last Order Date','nts'=>'Latest note','nts_cby'=>'Last note created by','nts_con'=>'Last note created on','lca'=>'Last completed activity','lacb'=>'Last Activity completed By','lcat'=>'Last Completed Activity Time','na'=>'Next activity','nat'=>'Next Activity Time','lact'=>'Last Action Date','ccby'=>'Party Created by','ccon'=>'Party Created on');
          array_push($header,'PartyName');
          foreach($available_module as $aab=>$baa){
            if(in_array($aab,$module_access)){
              array_push($header,$baa);
            }
          }

          $currency_symbol = ClientSetting::where('company_id',$company_id)->get(['currency_symbol','default_currency'])->toArray();
          $currency_sym = $currency_symbol[0]['currency_symbol'];

          // $header = array("PartyName","Orders","Zero Orders","Total Calls","Order Value","Collection","Cheques to be Deposited","No. of Visits","Time spent on Visits","Total Products Sold","EC:TC(Productivity %)","Last Order Date");

          if(strcmp($startDate,$endDate)==0){
            $startDate = $startDate.' 00:00:00';
            $endDate = $endDate.' 23:59:59';
          }
          
          $excel_data[] = $header;          
          $partyIDs = Client::whereIn('id',$clientids)->where('company_id',$company_id)->pluck('company_name', 'id')->toArray();
          $lastactiondate = array();
          foreach($partyIDs as $partyid => $company_name){
              $data['partyname'] = $company_name;
              $totalorder = Order::where('client_id',$partyid)->where('company_id',$company_id)->whereBetween('order_date',[$startDate,$endDate])->count();
              $zeroorder = NoOrder::where('client_id',$partyid)->where('company_id',$company_id)->whereBetween('date',[$startDate,$endDate])->count();
              if(in_array('ord',$module_access)){
                $data['totalorder'] = $totalorder;
              }
              if(in_array('zo',$module_access)){
                $data['zeroorder'] = $zeroorder;
              }
              if(in_array('tc',$module_access)){
                $data['totalcalls'] = $totalorder+$zeroorder;
              }
              if(in_array('ov',$module_access)){
                $data['ordervalue'] = $currency_sym.' '.Order::where('client_id',$partyid)->whereBetween('order_date',[$startDate,$endDate])->where('company_id',$company_id)->sum('grand_total');   
              }
              if(in_array('coll',$module_access)){
                $data['collectionvalue'] = $currency_sym.' '.Collection::where('client_id',$partyid)->whereBetween('payment_date',[$startDate,$endDate])->where('company_id',$company_id)->where('payment_status','Cleared')->get()->sum('payment_received');
              } 
              if(in_array('ch2dep',$module_access)){
                $data['chq_2deposit'] = $currency_sym.' '.Collection::where('company_id',$company_id)->where('client_id',$partyid)->whereBetween('cheque_date',[$startDate,$endDate])->where('payment_method','Cheque')->where('payment_status','Pending')->sum('payment_received');
              }
              if(in_array('tvst',$module_access)){
                $totalvisits = ClientVisit::where('client_id',$partyid)->whereBetween('date',[$startDate,$endDate])->where('company_id',$company_id)->get();
                $data['total_visits'] = $totalvisits->count();
              }
              if(in_array('ttmvist',$module_access)){
                $totalvisits = ClientVisit::where('client_id',$partyid)->whereBetween('date',[$startDate,$endDate])->where('company_id',$company_id)->get();
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
                                      ->where('orders.client_id', $partyid)
                                      ->whereBetween('orders.order_date',[$startDate,$endDate])
                                      ->groupby('orderproducts.product_id')
                                      ->orderby('totcount','desc')
                                      ->get()->toArray();
                // $productssold_count = 0;
                // if(count($products_sold)>0){
                //   foreach($products_sold as $ps=>$sp){
                //     $productssold_count += (int)$sp['totcount'];
                //   }
                // }
                $data['products_sold'] = count($products_sold);
              }
              if(in_array('ectc',$module_access)){
                $totalorder = Order::where('client_id',$partyid)->where('company_id',$company_id)->whereBetween('order_date',[$startDate,$endDate])->count();
                $zeroorder = NoOrder::where('client_id',$partyid)->where('company_id',$company_id)->whereBetween('date',[$startDate,$endDate])->count();
                $total_calls = $totalorder+$zeroorder;
                $total_order = $totalorder;
                $data['productivity_perc'] = ($total_calls!=0)?round(($total_order/$total_calls)*100,2).'%':'0%';
              }
              if(in_array('lod',$module_access)){
                $last_order_taken = Order::where('client_id',$partyid)->where('company_id',$company_id)->whereBetween('order_date',[$startDate,$endDate])->latest()->first();
                $lastorder_takendate = '';
                if($last_order_taken){
                  $lastorder_takendate = getDeltaDateForReport_mr($company_id,$last_order_taken->order_date);
                }
                $data['lastorder_taken'] = $lastorder_takendate;
              }
              if(in_array('nts',$module_access)){
                $notes = Note::where('client_id', $partyid)->where('company_id', $company_id)->latest('id')->get()->toArray();
                $data['latest_note'] = '';
                if(count($notes)>0){
                    $data['latest_note'] = strip_tags($notes[0]['remark']);
                }
              }
              if(in_array('nts_cby',$module_access)){
                $notes = Note::with('employee')->where('client_id', $partyid)->where('company_id', $company_id)->latest('id')->get()->toArray();
                $data['notes_createdby'] = '';
                if(count($notes)>0){
                    $data['notes_createdby'] = $notes[0]['employee']['name'];
                }
              }
              if(in_array('nts_con',$module_access)){
                $notes = Note::where('client_id', $partyid)->where('company_id', $company_id)->latest('id')->get()->toArray();
                $data['notes_createdon'] = '';
                if(count($notes)>0){
                    $data['notes_createdon'] = getDeltaDateForReport_mr($company_id,Carbon::parse($notes[0]['created_at'])->format('Y-m-d')).' '.Carbon::parse($notes[0]['created_at'])->format('g:i A');
                }
              }
              if(in_array('lca',$module_access)){
                $data['lastcomp_activity'] = '';
                $activities = Activity::where('company_id',$company_id)->where('client_id', $partyid)->whereNotNull('completion_datetime')->latest('completion_datetime')->get()->toArray();                
                if(count($activities)>0){
                  $data['lastcomp_activity'] = $this->strip_tags_content(strip_tags($activities[0]['title']));
                }
              }
              if(in_array('lacb',$module_access)){
                $data['lastcomp_activityby'] = '';
                $activities = Activity::with('completedByEmployee')->where('company_id',$company_id)->where('client_id', $partyid)->whereNotNull('completion_datetime')->latest()->get()->toArray();       
                if(count($activities)>0){
                  $data['lastcomp_activityby'] = $activities[0]['completed_by_employee']['name'];
                }
              }
              if(in_array('lcat',$module_access)){
                $data['lastcomp_activity_time'] = '';
                $activities = Activity::where('company_id',$company_id)->where('client_id', $partyid)->whereNotNull('completion_datetime')->latest()->get()->toArray();                
                if(count($activities)>0){
                  $date_aa = getDeltaDateForReport_mr($company_id,Carbon::parse($activities[0]['completion_datetime'])->format('Y-m-d'));
                  $date_ba = Carbon::parse($activities[0]['completion_datetime'])->format('g:i A');
                  $data['lastcomp_activity_time'] = $date_aa.' '.$date_ba;
                }
              }
              if(in_array('na',$module_access)){
                $data['next_activity'] = '';
                $curdate = Carbon::now()->format('Y-m-d').' 23:59:59';
                $activities = Activity::where('company_id', $company_id)->where('client_id', $partyid)->whereNull('completion_datetime')->orderby('start_datetime','asc')->get()->toArray();
                if(count($activities)>0){
                  $data['next_activity'] = $this->strip_tags_content(strip_tags($activities[0]['title']));
                }
              }
              if(in_array('nat',$module_access)){
                $data['next_activity_time'] = '';
                $curdate = Carbon::now()->format('Y-m-d').' 23:59:59';
                $activities = Activity::where('company_id', $company_id)->where('client_id', $partyid)->whereNull('completion_datetime')->orderby('start_datetime','asc')->get()->toArray();
                if(count($activities)>0){
                  $date_ab = getDeltaDateForReport_mr($company_id,Carbon::parse($activities[0]['start_datetime'])->format('Y-m-d'));
                  $date_bb = Carbon::parse($activities[0]['start_datetime'])->format('g:i A');
                  $data['next_activity_time'] = $date_ab.' '.$date_bb;
                }
              }

              if(in_array('lact',$module_access)){
                $data['last_action_date'] = '';
                $lastactiondate = array();
                $allmodules = ['ord','zord','coll','cvst','actvy','nts','exp','outlet'];
                foreach($allmodules as $ma){
                  if($ma=='ord'){
                    $order = Order::where('client_id',$partyid)->where('company_id',$company_id)->latest()->get()->toArray();
                    if(count($order)>0){
                      $lastactiondate[] = $order[0]['updated_at'];
                    }
                  }else if($ma=='zord'){
                    $zeroorder = NoOrder::where('client_id',$partyid)->where('company_id',$company_id)->latest()->get()->toArray();
                    if(count($zeroorder)>0){
                      $lastactiondate[] = $zeroorder[0]['updated_at'];
                    }
                  }else if($ma=='coll'){
                    $collection = Collection::where('client_id',$partyid)->where('company_id',$company_id)->latest()->get()->toArray();
                    if(count($collection)>0){
                      $lastactiondate[] = $collection[0]['created_at'];
                    }
                  }else if($ma=='cvst'){
                    $clientvisit = ClientVisit::where('client_id',$partyid)->where('company_id',$company_id)->latest()->get()->toArray();
                    if(count($clientvisit)>0){
                      $lastactiondate[] = $clientvisit[0]['updated_at'];
                    }
                  }else if($ma=='actvy'){
                    $activity = Activity::where('client_id',$partyid)->where('company_id',$company_id)->latest()->get()->toArray();
                    if(count($activity)>0){
                      $lastactiondate[] = $activity[0]['updated_at'];
                    }
                  }else if($ma=='nts'){
                    $note = Note::where('client_id',$partyid)->where('company_id',$company_id)->latest()->get()->toArray();
                    if(count($note)>0){
                      $lastactiondate[] = $note[0]['created_at'];
                    }
                  }else if($ma=='exp'){
                    $expense = Expense::where('client_id',$partyid)->where('company_id',$company_id)->latest()->get()->toArray();
                    if(count($expense)>0){
                      $lastactiondate[] = $expense[0]['updated_at'];
                    }
                  }else if($ma=='outlet'){
                    $outlet = Client::where('id',$partyid)->with('outlets')->where('company_id',$company_id)->get()->toArray();
                    if(count($outlet)>0){
                      $lastactiondate[] = $outlet[0]['outlets']['updated_at'];
                    }
                  }
                }
                rsort($lastactiondate);
                $result = array_filter($lastactiondate,'strlen');
                if(count($result)>0){
                  $res = array_values($result);
                  $date_ac = getDeltaDateForReport_mr($company_id,Carbon::parse($res[0])->format('Y-m-d'));
                  $date_bc = Carbon::parse($res[0])->format('g:i A');
                  $data['last_action_date'] = $date_ac.' '.$date_bc;
                }
              }


              if(in_array('ccby',$module_access)){
                $data['created_by'] = '';
                $cc = Client::where('id', $partyid)->where('company_id', $company_id)->get()->toArray();
                if(count($cc)>0){
                    $empid = $cc[0]['created_by'];
                    $employee = Employee::where('id',$empid)->get()->toArray();
                    if(count($employee)>0){
                        $data['created_by']  = $employee[0]['name'];
                    }
                }
              }
              if(in_array('ccon',$module_access)){
                $data['created_on'] = '';
                $cc = Client::where('id', $partyid)->where('company_id', $company_id)->get()->toArray();
                if(count($cc)>0){
                    $con = Carbon::parse($cc[0]['created_at'])->format('Y-m-d');
                    $date_fc = Carbon::parse($cc[0]['created_at'])->format('g:i A');
                    $data['created_on']  = getDeltaDateForReport_mr($company_id,$con).' '.$date_fc;
                }
              }

              $excel_data[] = $data; 
            
          }
          
          if(count($partyIDs)==1){
            try{
              $name = str_replace(" ","",getClient($partyIDs)->company_name);
            }catch(\Exception $e){
              $name = str_replace(" ","","Client Company Name");
            }
          }else{
            $name = "Multiple";
          }
          $time = time();
          
          $filename_download = 'PartyReports_'.$date;
          
          $exportSheet['Consolidated'] = $excel_data;
          $_excel = Excel::store(new MultiSheetExport($exportSheet), urlencode($filename_download.$time).'.'.$type, 'partyDetReport');
          
          $processing->filename = urlencode($filename_download.$type);
          $processing->download_link = env('APP_URL').'storage/excel/reports/partyDetReport/'.urlencode($filename_download.$time).'.'.$type;
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

  public function strip_tags_content($text) {
    return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
  }
  
}
