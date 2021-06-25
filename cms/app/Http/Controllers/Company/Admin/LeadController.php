<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lead;
use App\Employee;
use App\Leadssources;
use Auth;

class LeadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        $company_id = config('settings.company_id');
        $leads = Lead::where('company_id', $company_id)->get();
        return view('company.leads.index', compact('leads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company_id = config('settings.company_id');
        $leadssources = Leadssources::pluck('name', 'id')->toArray();
        $employees = Employee::where('company_id', $company_id)->pluck('name', 'id')->toArray();
        //print_r($leadssources);
        //die;
        return view('company.leads.create', compact('employees', 'leadssources'));
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
            'company.required' => 'Company Name field is required.',
            'name.required' => 'Contact Person Name is required.',
            'payment_date.required' => 'Date field is required.',
            'payment_received.' => 'Amount field is required.',
            'payment_received.numeric' => 'Amount field should be in number.',
            'payment_date.required' => 'Date field is required.',
            'payment_received.' => 'Amount field is required.',
            'payment_received.numeric' => 'Amount field should be in number.',
            'payment_date.required' => 'Date field is required.',
        ];


        $this->validate($request, [
            'company' => 'required',
            'name' => 'required',
            'phone' => 'required|numeric',
            'source' => 'required',
            'employee_id' => 'required',
            'status' => 'required',
        ], $customMessages);

        $company_id = config('settings.company_id');
        $lead = new \App\Lead;
        $lead->company_id = $company_id;
        $lead->company = $request->get('company');
        $lead->name = $request->get('name');
        $lead->email = $request->get('email');
        $lead->phone = $request->get('phone');
        $lead->source = $request->get('source');
        $lead->employee_id = $request->get('employee_id');
        $lead->status = $request->get('status');
        $lead->description = $request->get('description');
        $lead->save();
        return redirect()->route('company.admin.lead', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lead = lead::find($id);
        return view('company.leads.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $company_id = config('settings.company_id');
        $lead = Lead::where('id', $request->id)->where('company_id', $company_id)->first();
        $leadssources = Leadssources::pluck('name', 'id')->toArray();
        $employees = Employee::where('company_id', $company_id)->pluck('name', 'id')->toArray();
        if ($lead)
            return view('company.leads.edit', compact('lead', 'employees', 'leadssources'));
        else
            return redirect()->route('company.admin.lead', ['domain' => domain()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {

        $company_id = config('settings.company_id');
        $lead = Lead::findOrFail($request->id);

        $customMessages = [
            'company.required' => 'Company Name field is required.',
            'name.required' => 'Contact Person Name is required.',
            'payment_date.required' => 'Date field is required.',
            'payment_received.' => 'Amount field is required.',
            'payment_received.numeric' => 'Amount field should be in number.',
            'payment_date.required' => 'Date field is required.',
            'payment_received.' => 'Amount field is required.',
            'payment_received.numeric' => 'Amount field should be in number.',
            'payment_date.required' => 'Date field is required.',
        ];


        $this->validate($request, [
            'company' => 'required',
            'name' => 'required',
            'phone' => 'required|numeric',
            'source' => 'required',
            'employee_id' => 'required',
            'status' => 'required',
        ], $customMessages);


        $lead->company = $request->get('company');
        $lead->name = $request->get('name');
        $lead->email = $request->get('email');
        $lead->phone = $request->get('phone');
        $lead->source = $request->get('source');
        $lead->employee_id = $request->get('employee_id');
        $lead->status = $request->get('status');
        $lead->description = $request->get('description');
        $lead->save();

        return redirect()->route('company.admin.lead', ['domain' => domain()])->with('success', 'Information has been  Updfated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $lead = Lead::findOrFail($request->id);
        $lead->delete();
        flash()->success('lead has been deleted.');
        return back();
    }

}
