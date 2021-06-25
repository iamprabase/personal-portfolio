<?php

namespace App\Http\Controllers\Company\Admin;

use App\Designation;
use App\Employee;
use App\OdometerReport;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OdometerReportsController extends Controller
{

    public $company_id;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:odometer-report-view', ['only' => ['index','show']]);
        $this->middleware('permission:odometer-report-update', ['only' => ['update']]);
        $this->company_id = config('settings.company_id');
    }

    public function index()
    {

        return view('company.odometer-report.index', [
            'salesMens' => Auth::user()->handleQuery('employee')->whereHas('odometerReport')->get(['name', 'id']),
            'reports' => OdometerReport::where('company_id', $this->company_id)->count()
        ]);
    }

    public function ajaxDatatable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'employee_id',
            2 => 'date',
            3 => 'km',
            4 => 'amount',
            5 => 'action',
        );

        $empVal = $request->empVal;
        $start_date = Carbon::createFromFormat('Y-m-d', $request->startDate)->startOfDay()->toDateTimeString();
        $end_date = Carbon::createFromFormat('Y-m-d', $request->endDate)->endOfDay()->toDateTimeString();

        $query = Auth::user()->handleQuery('odometer_report');
        if (!empty($empVal)) {
            $prepQuery = $query->whereIn('employee_id', $empVal)->groupBy(['employee_id'])->whereBetween('start_time', [$start_date, $end_date])->select('id', 'employee_id');
        } else {
            $prepQuery =  $query->groupBy(['employee_id'])->whereBetween('start_time', [$start_date, $end_date])->select('id', 'employee_id');
        }

        if (empty($request->input('search.value'))) {
            $odometerReports = $prepQuery
                ->get();
        } elseif (!(empty($request->input('search.value')))) {
            $search = $request->input('search.value');
            $odometerReports = $prepQuery
                ->Wherehas('employees', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->get();
        }

        $data = array();
        if (!empty($odometerReports)) {
            foreach ($odometerReports as $key => $report) {

                $distance_in_km = OdometerReport::where('employee_id', $report->employee_id)->whereBetween('start_time', [$start_date, $end_date])->where('distance_unit', 1)->sum('distance');
                $distance_in_mile = OdometerReport::where('employee_id', $report->employee_id)->whereBetween('start_time', [$start_date, $end_date])->where('distance_unit', 0)->sum('distance');
                $amount = OdometerReport::where('employee_id', $report->employee_id)->whereBetween('start_time', [$start_date, $end_date])->sum('amount');


                $employee_show = domain_route('company.admin.employee.show', [$report->employees->id]);
                $view = domain_route('company.admin.odometer.report.show', ['employee_id' => $report->employees->id, 'from' => $request->startDate, 'to' => $request->endDate]);

                $nestedData['id'] = $key + 1;
                $nestedData['name'] = "<a href='{$employee_show}' datasalesman='{{$report->employees->name}}'> {$report->employees->name}</a>";
                $nestedData['date'] = getDeltaDate(date('Y-m-d', strtotime($start_date))) . ' - ' . getDeltaDate(date('Y-m-d', strtotime($end_date)));

                if (config('settings.odometer_distance_unit') == 1) {
                    $nestedData['km'] = $distance_in_km + round($distance_in_mile * 1.609344, 2);
                } else {
                    $nestedData['mile'] = $distance_in_mile + round($distance_in_km * 0.62137119223, 2);
                }
                $nestedData['amount'] = round($amount,2);

                if (\auth()->user()->can('odometer-report-view')) {
                    $view = "<a href='{$view}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                } else {
                    $view = Null;
                }
                $nestedData['action'] = $view;
                $data[] = $nestedData;
            }
        }

        return view('company.odometer-report.report_partial', [
            'data' => $data
        ]);

    }

    public function show(Request $request)
    {

        $start_date = Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay()->toDateTimeString();
        $end_date = Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay()->toDateTimeString();
        $employee_id = $request->employee_id;
        $countData = Auth::user()->handleQuery('odometer_report')->where('employee_id', $request->employee_id)->whereBetween('start_time', [$start_date, $end_date])->orderBy('id','desc')->count();

        if( $countData == 0 ){
            Session::flash('alert', 'Sorry! You do not have permission to view this content.');
            return redirect(domain_route('company.admin.home'));
        }

        $download = domain_route('company.admin.odometer.single.date.downloadPdf', ['employee_id' => $employee_id, 'from' => $request->from, 'to' => $request->to]);

        $action = '<a class="btn btn-default btn-sm" href="' . $download . '" style="padding: 7px 6px;margin-right: 5px;"><i class="fa fa-book"></i>PDF</a>';

        $action = $action . '<button class="btn btn-default btn-sm" href="#" onclick="print();" style="padding: 7px 6px;"><i class="fa fa-print"></i>Print</button>';
        $distance_in_mile = OdometerReport::where('employee_id', $employee_id)->where('distance_unit', 0)->whereBetween('start_time', [$start_date, $end_date])->sum('distance');
        $distance_in_km = OdometerReport::where('employee_id', $employee_id)->where('distance_unit', 1)->whereBetween('start_time', [$start_date, $end_date])->sum('distance');

        return view('company.odometer-report.show', [
            'reports' => OdometerReport::where('employee_id', $employee_id)->whereBetween('start_time', [$start_date, $end_date])->orderBy('id','desc')->get(),
            'action' => $action,
            'start_date' => getDeltaDate(date('Y-m-d', strtotime($start_date))),
            'end_date' => getDeltaDate(date('Y-m-d', strtotime($end_date))),
            'distance_in_km' => $distance_in_km + round($distance_in_mile * 1.609344, 2),
            'distance_in_mile' => $distance_in_mile + round($distance_in_km * 0.62137119223, 2)
        ]);
    }

    public function generateSingleDatePdf(Request $request)
    {
        $start_date = Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay()->toDateTimeString();
        $end_date = Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay()->toDateTimeString();
        $reports = OdometerReport::with('employees')->where('employee_id', $request->employee_id)->whereBetween('start_time', [$start_date, $end_date])->get();
        $employeeName = Employee::find($request->employee_id)->name;

        $startDate = getDeltaDate(date('Y-m-d', strtotime($request->from)));
        $endDate = getDeltaDate(date('Y-m-d', strtotime($request->to)));

        $distance_in_mile = OdometerReport::where('employee_id', $request->employee_id)->where('distance_unit', 0)->whereBetween('start_time', [$start_date, $end_date])->sum('distance');
        $distance_in_km = OdometerReport::where('employee_id', $request->employee_id)->where('distance_unit', 1)->whereBetween('start_time', [$start_date, $end_date])->sum('distance');

        $total_distance_in_km = $distance_in_km + round($distance_in_mile * 1.609344, 2);
        $total_distance_in_mile = $distance_in_mile + round($distance_in_km * 0.62137119223, 2);

        try {
            $pdf = PDF::loadView('company.odometer-report.pdf.single-report-download', compact('reports', 'start_date', 'end_date', 'total_distance_in_km', 'total_distance_in_mile'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('Odometer Report of' . ' ' . $employeeName . ' from ' . $startDate . ' ' . $endDate . '.pdf');
        } catch (\Exception $e) {
            Log::error($e->getFile());
            Log::error($e->getMessage());
            Log::error($e->getLine());
            return redirect()->back();
        }
    }

    public function custompdf(Request $request)
    {
        $columns = json_decode($request->columns);
        $properties = json_decode($request->properties);
        $getExportData = json_decode($request->exportedData)->data;

        $pageTitle = $request->pageTitle;
        set_time_limit(300);
        $pdf = PDF::loadView('pdf.custompdf', compact('getExportData', 'pageTitle', 'columns', 'properties'))->setPaper('a4', 'portrait');
        $download = $pdf->download($pageTitle . '.pdf');
        return $download;
    }

    public function update(Request $request)
    {
        $odometerReport = OdometerReport::find($request->id);
        $odometerRate =config('settings.odometer_rate');

        $distance = $request->end_reading - $request->start_reading;
        if (config('settings.odometer_distance_unit') == $request->distance_unit) {
            $amount = $distance * $odometerRate;
        } else {
            if ($request->distance_unit == 1) {
                $amount = ($distance * 0.62137119223) * $odometerRate;
            } else {
                $amount = ($distance * 1.609344) * $odometerRate;
            }
        }

        $odometerReport->notes = $request->notes;
        $odometerReport->start_reading = $request->start_reading;
        $odometerReport->end_reading = $request->end_reading;
        $odometerReport->distance = $request->end_reading - $request->start_reading;
        $odometerReport->amount = $amount;
        $odometerReport->distance_unit = $request->distance_unit;
        $odometerReport->save();
        Session::flash('message', 'Updated Success');

        $dataPayload = array("data_type" => "odometer", "odometer" => $odometerReport, "action" => 'update');
        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        sendPushNotification_($fbIDs, 44, null, $dataPayload);
        return back();
    }

}
