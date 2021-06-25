<?php

namespace App\Http\Controllers\API;

use App\Employee;
use App\OdometerReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OdometerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getOdometerData()
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $employee = Employee::where('user_id', $user->id)->where('company_id', $company_id)->select('id')->first();

        $odometerReport = OdometerReport::where('company_id', $company_id)->where('employee_id', $employee->id)->get();

        return response([
            'reports' => $odometerReport
        ]);
    }

    public function store(Request $request)
    {

        $odometer_rate = DB::table('client_settings')->where('company_id', $request->company_id)->pluck('odometer_rate')->first();
        $odometer_distance_unit = DB::table('client_settings')->where('company_id', $request->company_id)->pluck('odometer_distance_unit')->first();


        $distance = $request->stop_meter - $request->start_meter;
        if ($odometer_distance_unit == $request->isKm) {
            $amount = $distance * $odometer_rate;
        } else {
            if ($odometer_distance_unit == 0) {
                $amount = ($distance * 0.62137119223) * $odometer_rate;
            } else {
                $amount = ($distance * 1.609344) * $odometer_rate;
            }
        }

        $odometer = OdometerReport::create([
            'company_id' => $request->company_id,
            'employee_id' => $request->employee_id,
            'uuid' => $request->unique_id,
            'start_reading' => $request->start_meter,
            'end_reading' => $request->stop_meter,
            'start_time' => Carbon::parse($request->start_time)->toDateTimeString(),
            'end_time' => Carbon::parse($request->stop_time)->toDateTimeString(),
            'start_location' => $request->start_loc,
            'end_location' => $request->stop_loc,
            'notes' => $request->notes,
            'distance_unit' => $request->isKm,
            'distance' => $request->stop_meter - $request->start_meter,
            'amount' => $amount
        ]);

        return response([
            'status' => $odometer ? true : false,
            'odometer_id' => $odometer->id,
            'unique_id' => $odometer->uuid
        ]);
    }

    public function syncOdometerRecord(Request $request)
    {

        $unsyncedData = json_decode($request->unsynced_data);
        $response = array();
        foreach ($unsyncedData as $data) {
            $odometer = $this->saveOdometerReport($data);
            $response[] = array(
                'odometer_id' => $odometer->id,
                'unique_id' => $odometer->uuid,
            );
        }

        return response(['response' => $response]);
    }


    private function saveOdometerReport($odometer)
    {

        $odometer_rate = DB::table('client_settings')->where('company_id', $odometer->company_id)->pluck('odometer_rate')->first();
        $odometer_distance_unit = DB::table('client_settings')->where('company_id', $odometer->company_id)->pluck('odometer_distance_unit')->first();

        $distance = $odometer->stop_meter - $odometer->start_meter;
        if ($odometer_distance_unit == $odometer->isKm) {
            $amount = $distance * $odometer_rate;
        } else {
            if ($odometer_distance_unit == 0) {
                $amount = ($distance * 0.62137119223) * $odometer_rate;
            } else {
                $amount = ($distance * 1.609344) * $odometer_rate;
            }
        }

        return OdometerReport::create([
            'company_id' => $odometer->company_id,
            'employee_id' => $odometer->employee_id,
            'uuid' => $odometer->unique_id,
            'start_reading' => $odometer->start_meter,
            'end_reading' => $odometer->stop_meter,
            'start_time' => Carbon::parse($odometer->start_time)->toDateTimeString(),
            'end_time' => Carbon::parse($odometer->stop_time)->toDateTimeString(),
            'start_location' => $odometer->start_loc,
            'end_location' => $odometer->stop_loc,
            'notes' => $odometer->notes,
            'distance_unit' => $odometer->isKm,
            'distance' => $odometer->stop_meter - $odometer->start_meter,
            'amount' => $amount
        ]);


    }


}
