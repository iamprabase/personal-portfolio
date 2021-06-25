<?php

use App\MainModule;
use Illuminate\Database\Seeder;

class MainModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $modules = array([
        'name' => 'Orders', 'position' => 60, 'field' => 'orders' 
      ], [
        'name' => 'Attendance', 'position' => 10, 'field' => 'attendance' 
      ], [
        'name' => 'Live Tracking', 'position' => 20, 'field' => 'livetracking' 
      ], [
        'name' => 'Party', 'position' => 30, 'field' => 'party' 
      ], [
        'name' => 'Party Visit', 'position' => 40, 'field' => 'visit_module' 
      ], [
        'name' => 'Products', 'position' => 50, 'field' => 'product' 
      ], [
        'name' => 'Zero Orders', 'position' => 70, 'field' => 'zero_orders' 
      ], [
        'name' => 'Ageing Payments', 'position' => 80, 'field' => 'ageing' 
      ], [
        'name' => 'Collections', 'position' => 90, 'field' => 'collections' 
      ], [
        'name' => 'Notes', 'position' => 100, 'field' => 'notes' 
      ], [
        'name' => 'Activites', 'position' => 110, 'field' => 'activities' 
      ], [
        'name' => 'Expenses', 'position' => 120, 'field' => 'expenses' 
      ], [
        'name' => 'Leaves', 'position' => 130, 'field' => 'leaves' 
      ], [
        'name' => 'Announcements', 'position' => 140, 'field' => 'announcement' 
      ], [
        'name' => 'Beatplans', 'position' => 150, 'field' => 'beat' 
      ], [
        'name' => 'Tourplans', 'position' => 160, 'field' => 'tour_plans' 
      ], [
        'name' => 'Day Remarks', 'position' => 170, 'field' => 'remarks' 
      ], [
        'name' => 'Returns', 'position' => 180, 'field' => 'returns' 
      ], [
        'name' => 'Analytics', 'position' => 190, 'field' => 'analytics' 
      ], [
        'name' => 'Report Odometer', 'position' => 200, 'field' => 'odometer_report' 
      ], [
        'name' => 'Stocks', 'position' => 210, 'field' => 'stock_report' 
      ], [
        'name' => 'Collaterals', 'position' => 220, 'field' => 'collaterals' 
      ], [
        'name' => 'Accounting', 'position' => 230, 'field' => 'accounting' 
      ], [
        'name' => 'Custom Module', 'position' => 240, 'field' => 'custom_module' 
      ], [
        'name' => 'Party Files', 'position' => 250, 'field' => 'party_files' 
      ], [
        'name' => 'Party Images', 'position' => 260, 'field' => 'party_images' 
      ], [
        'name' => 'Tally', 'position' => 270, 'field' => 'tally' 
      ], [
        'name' => 'Schemes', 'position' => 280, 'field' => 'schemes' 
      ], [
        'name' => 'Target Setup', 'position' => 290, 'field' => 'targets' 
      ], [
        'name' => 'Taget vs Achievements', 'position' => 300, 'field' => 'targets_rep' 
      ], [
        'name' => 'Report: Salesman GPS Path', 'position' => 310, 'field' => 'gpsreports' 
      ], [
        'name' => 'Report: Check In Check Out', 'position' => 320, 'field' => 'cincout' 
      ], [
        'name' => 'Report: Daily Sales Order', 'position' => 330, 'field' => 'dso' 
      ], [
        'name' => 'Report: Daily Sales Order (By Unit)', 'position' => 340, 'field' => 'dsobyunit' 
      ], [
        'name' => 'Report: Order', 'position' => 350, 'field' => 'ordersreport' 
      ], [
        'name' => 'Report: Product Sales Order', 'position' => 360, 'field' => 'psoreport' 
      ], [
        'name' => 'Report: Salesman Party Wise', 'position' => 370, 'field' => 'spwise' 
      ], [
        'name' => 'Report: Daily Party', 'position' => 380, 'field' => 'dpartyreport' 
      ], [
        'name' => 'Report: Monthly Attendance', 'position' => 390, 'field' => 'monthly_attendance' 
      ], [
        'name' => 'Report: Daily Employee', 'position' => 400, 'field' => 'dempreport' 
      ], [
        'name' => 'Import', 'position' => 410, 'field' => 'import' 
      ], [
        'name' => 'Retailer App', 'position' => 420, 'field' => 'retailer_app' 
      ]);
      MainModule::insert($modules);
    }
}
