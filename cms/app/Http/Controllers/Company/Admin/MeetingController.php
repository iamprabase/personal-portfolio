<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Meeting;
use App\Employee;
Use Auth;
use DB;
use App\Client;
use Storage;


class MeetingController extends Controller
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
        $meetings = Meeting::where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('company.meetings.index', compact('meetings'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company_id = config('settings.company_id');
        $employees = Employee::where('company_id', $company_id)->where('status', 'Active')->orderBy('name', 'asc')->get();
        $clients = Client::where('company_id', $company_id)->where('status', 'Active')->orderBy('company_name', 'asc')->get();
        return view('company.meetings.create', compact('employees', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $company_id = config('settings.company_id');
        // $companyName=Auth::user()->companyName($company_id)->domain;

        $customMessages = [
            'employee_id.required' => 'The Employee Name field is required.',
            'client_id.required' => 'The Party Name is required',
            'checkintime.required' => 'Check In Time is required',
            'checkintime.date_format' => 'Time Format is not correct',
            'meetingdate.required' => 'Meeting Date is required',
            'meetingdate.date_format' => 'Date Format is not correct',
            'comm_medium.required' => 'Communication Medium is required',
            'remark.required' => 'Remark from the meeting is required'
            //'logo.mimes' => 'Upload correct file type.',
        ];


        $this->validate($request, [
            'employee_id' => 'required',
            'client_id' => 'required',
            'checkintime' => 'required|date_format:H:i:s',
            'meetingdate' => 'required|date_format:Y-m-d',
            'comm_medium' => 'required',
        ], $customMessages);

        $meeting = new Meeting();
        $meeting->company_id = $company_id;
        $meeting->employee_id = $request->get('employee_id');
        $meeting->client_id = $request->get('client_id');
        $meeting->checkintime = $request->get('checkintime');
        $meeting->meetingdate = $request->get('meetingdate');
        $meeting->comm_medium = $request->get('comm_medium');
        $meeting->remark = $request->get('remark');

        $saved = $meeting->save();

        return redirect()->route('company.admin.meeting', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Expense $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $company_id = config('settings.company_id');
        $meeting = Meeting::findOrFail($request->id);
        //$images = DB::table('images')->where('type', '=', 'expense')->where('type_id', $request->id)->get();
        //$expense = Expense::where('company_id', $company_id)
        //      ->where('id', $request->id)
        //      ->get();
        return view('company.meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Expense $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $company_id = config('settings.company_id');
        $meeting = Meeting::where('id', $request->id)->where('company_id', $company_id)->first();
        $employees = Employee::where('company_id', $company_id)->where('status', 'Active')->orderBy('name', 'asc')->get();
        $clients = Client::where('company_id', $company_id)->where('status', 'Active')->orderBy('company_name', 'asc')->get();
        //$image_count = DB::table('images')->where('type','=', 'expense')->where('type_id', $request->id)->count();
        if ($meeting)
            return view('company.meetings.edit', compact('meeting', 'employees', 'clients'));
        else
            return redirect()->route('company.admin.meeting', ['domain' => domain()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Expense $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $company_id = config('settings.company_id');
        // $companyName=Auth::user()->companyName($company_id)->domain;

        $customMessages = [
            'employee_id.required' => 'The Employee Name field is required.',
            'client_id.required' => 'The Party Name is required',
            'checkintime.required' => 'Check In Time is required',
            'checkintime.date_format' => 'Time Format is not correct',
            'meetingdate.required' => 'Meeting Date is required',
            'meetingdate.date_format' => 'Date Format is not correct',
            'comm_medium.required' => 'Communication Medium is required',
            'remark.required' => 'Remark from the meeting is required'
            //'logo.mimes' => 'Upload correct file type.',
        ];


        $this->validate($request, [
            'employee_id' => 'required',
            'client_id' => 'required',
            'checkintime' => 'required|date_format:H:i:s',
            'meetingdate' => 'required|date_format:Y-m-d',
            'comm_medium' => 'required',
        ], $customMessages);

        $meeting = Meeting::where('id', $request->id)->first();
        $meeting->company_id = $company_id;
        $meeting->employee_id = $request->get('employee_id');
        $meeting->client_id = $request->get('client_id');
        $meeting->checkintime = $request->get('checkintime');
        $meeting->meetingdate = $request->get('meetingdate');
        $meeting->comm_medium = $request->get('comm_medium');
        $meeting->remark = $request->get('remark');

        $saved = $meeting->save();

        return redirect()->route('company.admin.meeting', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Expense $expense
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request)
    {
        $company_id = config('settings.company_id');
        $meeting = Meeting::findOrFail($request->id);
        if ($meeting) {
            $meeting->delete();
            flash()->success('Meeting has been deleted.');
        }
        return back();
    }
}
