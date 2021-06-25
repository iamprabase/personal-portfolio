<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Attendance;
use App\Employee;
use Auth;
use DB;
use Log;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_id = config('settings.company_id');
        $attendances = Attendance::where('company_id', $company_id)
            ->groupBy('employee_id', DB::raw("DATE_FORMAT(check_datetime, '%Y-%m-%d')"))
            ->orderBy('check_datetime', 'asc')
            ->get();
        //Log::info('info', array("attendances"=>print_r(json_decode(json_encode($attendances)),true)));
        //echo "<pre>";
        //echo $attendances[0]['check_type'];

        if ($attendances[0]['check_type'] == 2) {
            $attendances->shift();
        }

        // $allrec=count($attendances);
        //  echo $allrec;
        // if($allrec>=2){
        // if($attendances[$allrec-1]['check_type']==1){
        //   array_pop($attendances);
        // } 
        //}
        //  echo "<pre>";
        // print_r($attendances);

        return view('company.attendance.index', compact('attendances'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company_id = config('settings.company_id');
        $employees = Employee::where('company_id', $company_id)->where('status', 'Active')->pluck('name', 'id')->toArray();
        return view('company.attendance.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customMessages = [
            'employee_id.required' => 'The Employee Name field is required.',
            'adate.required' => ' Date Field is required.',
            'check_in.required' => 'CheckIn Time Field is required.',
            'check_out.required' => 'CheckOut Time Field is required.',
        ];

        $this->validate($request, [
            'employee_id' => 'required',
            'adate' => 'required',
            'check_in.*' => 'required',
            'check_out.*' => 'required',
            // 'check_type' => 'required',
        ], $customMessages);

        $company_id = config('settings.company_id');
        // $attendance = new \App\Attendance;
        $employee_id = $request->get('employee_id');
        $i = 0;
        foreach ($request->check_in as $check_in) {
            DB::table('attendances')->insert([
                'company_id' => $company_id,
                'employee_id' => $employee_id,
                'check_datetime' => $request->get('adate') . ' ' . $check_in,
                'check_type' => 1
            ]);
            DB::table('attendances')->insert([
                'company_id' => $company_id,
                'employee_id' => $employee_id,
                'check_datetime' => $request->get('adate') . ' ' . $request->check_out[$i],
                'check_type' => 2
            ]);
            $i++;
        }

        //  $attendance->company_id = $company_id;
        //  $attendance->employee_id = $request->get('employee_id');
        //  $attendance->check_datetime = $request->get('adate').' '.$request->get('check_in');
        //  $attendance->check_type = $request->get('check_type');
        //  // $attendance->remark = $request->get('remark');

        // $attendance->save();

        return redirect()->route('company.admin.attendance', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $company_id = config('settings.company_id');
        $attendances = Attendance::where('employee_id', $request->id)->where('check_datetime', '>=', $request->date . ' 00:00:00')
            ->where('check_datetime', '<=', $request->date . ' 23:59:59')->where('company_id', $company_id)->orderBy('check_datetime', 'desc')->get()->toArray();
        if ($attendances)
            return view('company.attendance.show', compact('attendances'));
        else
            return redirect()->route('company.admin.attendance', ['domain' => domain()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $company_id = config('settings.company_id');
        $attendance = Attendance::where('id', $request->id)->where('company_id', $company_id)->first();
        $employees = Employee::where('company_id', $company_id)->where('status', 'Active')->pluck('name', 'id')->toArray();
        if ($attendance)
            return view('company.attendance.edit', compact('attendance', 'employees'));
        else
            return redirect()->route('company.admin.attendance', ['domain' => domain()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $company_id = config('settings.company_id');
        $attendance = Attendance::findOrFail($request->id);
        $customMessages = [
            'employee_id.required' => 'The Employee Name field is required.',
            'adate.required' => ' Date Field is required.',
            'check_in.required' => 'CheckIn Time Field is required.',
        ];


        $this->validate($request, [
            'employee_id' => 'required',
            'adate' => 'required',
            'check_in' => 'required',
            'check_type' => 'required',
        ], $customMessages);


        $attendance->employee_id = $request->get('employee_id');
        $attendance->check_datetime = $request->get('adate') . ' ' . $request->get('check_in');
        $attendance->check_type = $request->get('check_type');
        $attendance->remark = $request->get('remark');

        $attendance->save();

        return redirect()->route('company.admin.attendance', ['domain' => domain()])->with('success', 'Information has been  Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $company_id = config('settings.company_id');
        $attendance = Attendance::findOrFail($request->id);
        $attendance->delete();
        flash()->success('Attendance has been deleted.');
        return back();
    }
}
