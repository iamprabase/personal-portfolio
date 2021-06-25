<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Attendance;
use Auth;
use DB;
use DateTime;
use DateTimeZone;

class AutoCheckOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:autocheckout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically checkout date entry when users are not able to checkout yesterday. Runtime 1:30 to 2 AM';

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
    public function handle()
    {
        
       $timezones = DB::table('client_settings')->select('company_id','time_zone')
            ->whereNotNull('time_zone')->get();
             if($timezone){
                 date_default_timezone_set($timezone->time_zone);
            }else{
                 date_default_timezone_set(Asia/Kathmandu);
            }
        try{
          foreach($timezones as $timezone){
              $localTime = new DateTime('NOW', new DateTimeZone($timezone->time_zone));
              $h=$localTime->format('H');
              $m=$localTime->format('i');
              $s=$localTime->format('s');
              
              if( $h == 0 && $m < 59) {
                  $yesterday = Carbon::now()->subDays(1)->format('Y-m-d');
                  $yesterdayAttendances = Attendance::where('adate',$yesterday)->where('company_id',$timezone->company_id)->select('id','employee_id','company_id')->get();
                  foreach($yesterdayAttendances as $yesterdayAttendance){
                      $attendances = Attendance::where('adate',$yesterday)->where('employee_id',$yesterdayAttendance->employee_id)->orderBy('check_datetime','ASC')->get();
                      $flagCheckType = 1;
                      foreach($attendances as $attendance){
                          if($attendance->check_type==1 && $flagCheckType==1){
                              $flagCheckType = 2;
                          }
                          if($attendance->check_type==2 && $flagCheckType==2){
                              $flagCheckType = 1;
                          }
                      }
                      if($flagCheckType==2){
                          $CheckoutDateTime = $yesterday." 23:59:59";
                          $checkOutAttendance = new Attendance;
                          $checkOutAttendance->unique_id = uniqid().'-'.time();
                          $checkOutAttendance->employee_id = $yesterdayAttendance->employee_id;
                          $checkOutAttendance->company_id = $yesterdayAttendance->company_id;
                          $checkOutAttendance->check_type = 2;
                          $checkOutAttendance->auto_checkout = 1;
                          $checkOutAttendance->check_datetime = $yesterday.' 23:59:59';
                          $checkOutAttendance->adate = $yesterday;
                          $checkOutAttendance->atime = "23:59:59";
                          $checkOutAttendance->save();
                      }            
                 }
             }
          }
        }catch(\Exception $e){
          \Log::error(array("AutoCheckout ", $e->getMessage()));
        }

    }
}
