<?php

namespace App\Console\Commands;

use Excel;
use App\ClientSetting;
use App\GenerateReport;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Exports\MultiSheetExport;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ProductOrderDetailReportService;

class ProductOrderDetailReport extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'product-order-detail:report';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate excel product-order detail report(Product Order Detail Report)';

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
    $processing_stock_reports_count = GenerateReport::where('processing','1')->count();
    
    if($processing_stock_reports_count<3){
      $processing = GenerateReport::whereReportType('OrderDetails')
                ->whereNULL('download_link')
                ->whereNULL('processing')
                ->first();
      if($processing){
        try{
          $type = 'xlsx';
          $report_type = $processing->report_cat;
          $reportid = $processing->id;
          $processing->processing = 1;
          $processing->save();

          $startDate = $processing->start_date;
          $endDate = $processing->end_date;
          $group_by = array('orders.order_no', 'orders.order_date', 'orders.client_id', 'orderproducts.product_id', 'orderproducts.product_variant_id','orderproducts.unit', 'orderproducts.pdiscount_type');

          $instance = new ProductOrderDetailReportService($processing->company_id, $startDate, $endDate, $processing->order_status, null, $processing->party_id, $group_by, $processing->include_dispatch_detail);

          $orders = $instance->getOrders();
          $mappedWithProduct = $instance->mapWithProductNames($orders);
          $mappedWithParty = $instance->mapWithParties($mappedWithProduct);

          

          $exportData = $instance->getMappeddata($mappedWithParty);

          $filename_download = "OrderBreakdownReport";
          $filename = $filename_download.'_'.time();

          try{
            $store = Excel::store(new MultiSheetExport($exportData), $filename.'.'.$type, 'productOrderDetailsReports');
          }
          catch(\Maatwebsite\Excel\Exceptions\LaravelExcelException $ex){
            \Log::info("Spreadsheet requires a minimal record.");
          }

          if($store){
            $storagePath = 'excel/reports/productOrderDetailsReports';
            
            $updatereport = GenerateReport::find($reportid);
            $updatereport->processing = 2;
            $updatereport->filename = $filename_download.'.'.$type;
            $updatereport->download_link = env('APP_URL').'storage/' .$storagePath.'/' . $filename . '.' . $type;
            $updatereport->save();
          }

        }catch(\Exception $e){
          Log::error(
            print_r(array(
              "Product Order Details Report", 
              $e->getMessage()), 
              true)
          );
          $processing->processing = 0;
          $processing->save();
        }
        
      }
    }
  }

}