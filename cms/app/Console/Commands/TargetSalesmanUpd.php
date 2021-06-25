<?php

namespace App\Console\Commands;

use DB;
use Log;
use Auth;
use View;
use App\User;
use DateTime;
use App\TargetSalesman;
use App\TargetSalesmanassign;
use App\TargetSalesmanassignHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Console\Command;

class TargetSalesmanUpd extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'TargetSalesmanUpd:report';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Update TargetSalesmanUpdate';

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
    // $company_id = config('settings.company_id');
    $today_date = Carbon::yesterday()->format('Y-m');
    // $targetsalesman = TargetSalesmanassign::where('target_startmonth','like',$today_date.'%')->get()->toArray();
    // $targetsalesman_ids = TargetSalesmanassign::where('target_startmonth','like',$today_date.'%')->pluck('id')->toArray();
    $targetsalesman = TargetSalesmanassign::get()->toArray();
    $targetsalesman_ids = TargetSalesmanassign::pluck('id')->toArray();
    // $totalgroupids = array_unique(TargetSalesmanassign::where('target_startmonth','like',$today_date.'%')->pluck('salesman_id')->toArray());
    $totalgroupids = array_unique(TargetSalesmanassign::pluck('salesman_id')->toArray());
    $targetshist_count = DB::table('salesmantarget_assign_history')->count();
    if($targetshist_count==0){
      $targethist_newgroupid = 1;
    }else{
      $tgid = DB::table('salesmantarget_assign_history')->orderby('targethist_newgroupid','desc')->first();
      $targethist_newgroupid = ((int)$tgid->targethist_newgroupid)+1;
    }
    $tgids = (count($totalgroupids)>0)?($totalgroupids[0]):0;
    $tgids2 = (count($totalgroupids)>0)?($totalgroupids[0]):0;
    $tghistids = (count($totalgroupids)>0)?($totalgroupids[0]):0;
    $targetsassignedit_count = DB::table('salesmantarget_assignedit_history')->count();
    if($targetsassignedit_count==0){
      $targetassedithist_newgroupid = 1;
    }else{
      $tghstid = DB::table('salesmantarget_assignedit_history')->orderby('targethist_newgroupid','desc')->first();
      $targetassedithist_newgroupid = ((int)$tghstid->targethist_newgroupid)+1;
    }
    $targetsassign_count = DB::table('salesmantarget_assign')->count();
    if($targetsassign_count==0){
      $target_newgroupid = 1;
    }else{
      $tgid = DB::table('salesmantarget_assign')->orderby('target_groupid','desc')->first();
      $target_newgroupid = ((int)$tgid->target_groupid)+1;
    }
    if(count($targetsalesman)>0){ 
      $tgthist_groupid = $targethist_newgroupid;
      $tgthistedit_groupid = $targetassedithist_newgroupid;
      foreach($targetsalesman as $k=>$val){
        if($tgids!=0 && $tgids==$val['salesman_id']){
          $tgthist_groupid = $targethist_newgroupid;
        }else{
          $tgthist_groupid = $targethist_newgroupid = ($tgthist_groupid+1);
          $tgids = $val['salesman_id'];
        }
        $targetsalesmanhistory = new TargetSalesmanassignHistory;
        $targetsalesmanhistory->targethist_newgroupid = $tgthist_groupid;
        $targetsalesmanhistory->targetid_original = $val['id'];
        $targetsalesmanhistory->company_id = $val['company_id'];
        $targetsalesmanhistory->salesman_id = $val['salesman_id'];
        $targetsalesmanhistory->target_groupid = $val['target_groupid'];
        $targetsalesmanhistory->target_name = $val['target_name'];
        $targetsalesmanhistory->target_subid = $val['target_subid'];
        $targetsalesmanhistory->target_rule = $val['target_rule'];
        $targetsalesmanhistory->target_interval = $val['target_interval'];
        $targetsalesmanhistory->target_values = $val['target_values'];
        $targetsalesmanhistory->target_tot_workingdays = $val['target_tot_workingdays'];
        $targetsalesmanhistory->target_progress = $val['target_progress'];
        $targetsalesmanhistory->target_startmonth = $val['target_startmonth'];
        $targetsalesmanhistory->target_assigneddate = $val['created_at'];
        $targetsalesmanhistory->target_changeddate = Carbon::now()->format('Y-m-d H:i:s');
        $targetsalesmanhistory->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $targetsalesmanhistory->updated_at = Carbon::now()->format('Y-m-d H:i:s');
        $targetsalesmanhistory->save();

        if($tghistids!=0 && $tghistids==$val['salesman_id']){
          $tgthistedit_groupid = $targetassedithist_newgroupid;
        }else{
          $tgthistedit_groupid = $targetassedithist_newgroupid = ($tgthistedit_groupid+1);
          $tghistids = $val['salesman_id'];
        }
        DB::table('salesmantarget_assignedit_history')->insert([
          'targetid_original' => $val['id'],
          'targethist_newgroupid' => $tgthistedit_groupid,
          'company_id' => $val['company_id'],
          'salesman_id' => $val['salesman_id'],
          'target_groupid' => $val['target_groupid'],
          'target_name' => $val['target_name'],
          'target_subid' => $val['target_subid'],
          'target_rule' => $val['target_rule'],
          'target_interval' => $val['target_interval'],
          'target_values' => $val['target_values'],
          'target_tot_workingdays' => $val['target_tot_workingdays'],
          'target_progress' => $val['target_progress'],
          'target_assigneddate' => $val['target_startmonth'],
          'target_changeddate' => Carbon::now()->toDateTimeString(),
          'target_startmonth' => $val['target_startmonth'],
        ]);



      }

      foreach($targetsalesman as $k=>$val){
        // if($tgids2!=0 && $tgids2==$val['salesman_id']){
        //   $tgt_groupid = $target_newgroupid;
        // }else{
        //   $tgt_groupid = $target_newgroupid = ($tgt_groupid+1);
        //   $tgids2 = $val['salesman_id'];
        // }
        $targetsalesmanassign = new TargetSalesmanassign;
        $targetsalesmanassign->target_groupid = $val['target_groupid'];
        $targetsalesmanassign->company_id = $val['company_id'];
        $targetsalesmanassign->salesman_id = $val['salesman_id'];
        $targetsalesmanassign->target_name = $val['target_name'];
        $targetsalesmanassign->target_subid = $val['target_subid'];
        $targetsalesmanassign->target_rule = $val['target_rule'];
        $targetsalesmanassign->target_interval = $val['target_interval'];
        $targetsalesmanassign->target_values = $val['target_values'];
        $targetsalesmanassign->target_tot_workingdays = $val['target_tot_workingdays'];
        $targetsalesmanassign->target_progress = $val['target_progress'];
        $targetsalesmanassign->target_startmonth = Carbon::now()->format('Y-m-d');
        $targetsalesmanassign->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $targetsalesmanassign->updated_at = Carbon::now()->format('Y-m-d H:i:s');

        $targetsalesmanassign->save();

      }

      TargetSalesmanassign::destroy($targetsalesman_ids);
    }
  }


  
}
