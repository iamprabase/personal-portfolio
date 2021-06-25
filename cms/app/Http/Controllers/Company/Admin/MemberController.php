<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
//use App\company;
use App\Plan;
use App\Company;
//use App\Member;
use Storage;
use DB;

//use App\role;

class MemberController extends Controller
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
        $companies = Company::all()->sortByDesc("created_at");;
        return view('members.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $plans = Plan::pluck('name', 'id')->toArray();
        return view('members.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.ssss
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customMessages = [
            'company_name.required' => 'The Name field is required.',
            'c_password.same' => 'Password do not match.',
            'c_password.required' => 'Confirm Password field is required.',
        ];


        $this->validate($request, [
            'company_name' => 'required',
            'alias' => 'required|unique:companies',
            'name' => 'required',
            'email' => 'required|email|unique:companies',
            'phone' => 'required|digits_between:7,14',
            'mobile' => 'required|digits_between:7,14',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
        ], $customMessages);


        $company = new \App\Company;
        $company->company_name = $request->get('company_name');
        $company->alias = $request->get('alias');
        $company->name = $request->get('name');
        $company->email = $request->get('email');
        $company->password = bcrypt($request->get('password'));
        $company->phone = $request->get('phone');
        $company->extNo = $request->get('extNo');
        $company->mobile = $request->get('mobile');
        $company->fax = $request->get('fax');
        $company->pan = $request->get('pan');
        $company->whitelabel = $request->get('whitelabel');
        $company->customize = $request->get('customize');
        $company->start_date = $request->get('start_date');
        $company->end_date = $request->get('end_date');
        $company->aboutCompany = $request->get('aboutCompany');
        $company->status = $request->get('status');


        $company->save();
        $plan = Plan::where('id', $request->get('plan'))->first();
        $company->plans()->attach($plan);
        return redirect()->route('app.member', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::find($id);
        return view('members.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $plans = Plan::pluck('name', 'id')->toArray();
        //$companyplan = $company->plans->pluck('name','name')->all();
        return view('members.edit', compact('company', 'plans'));
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

        $company = Company::findOrFail($id);
        $customMessages = [
            'company_name.required' => 'The Name field is required.',
            'c_password.same' => 'Password do not match.',
            'c_password.required' => 'Confirm Password field is required.',
        ];


        $this->validate($request, [
            'company_name' => 'required',
            'alias' => 'required|unique:companies,alias,' . $id,
            'name' => 'required',
            'email' => 'required|email|unique:companies,email,' . $id,
            'phone' => 'required|digits_between:7,14',
            'mobile' => 'required|digits_between:7,14',
        ], $customMessages);


        // 'email' => 'required|email|unique:users,email,' . $id,
        //    'roles' => 'required|min:1'
        //$company= new \App\Company;
        $company->company_name = $request->get('company_name');
        $company->alias = $request->get('alias');
        $company->name = $request->get('name');
        $company->email = $request->get('email');
        $company->password = bcrypt($request->get('password'));
        $company->phone = $request->get('phone');
        $company->extNo = $request->get('extNo');
        $company->mobile = $request->get('mobile');
        $company->fax = $request->get('fax');
        $company->pan = $request->get('pan');
        $company->whitelabel = $request->get('whitelabel');
        $company->customize = $request->get('customize');
        $company->start_date = $request->get('start_date');
        $company->end_date = $request->get('end_date');
        $company->aboutCompany = $request->get('aboutCompany');
        $company->status = $request->get('status');

        if (!empty($request->input('password'))) {
            $this->validate($request, [
                'password' => 'required|min:8',
                'c_password' => 'same:password',

            ], $customMessages);
            $company->password = bcrypt($request->get('password'));
        }


        $company->save();
        // CompanyPlan::destroy($request->get('plan'));
        // $company->plans()->attach($request->get('plan'));
        // $plan = Plan::where('id', $request->get('plan'))->first();
        //  $company->plans()->attach($plan);
        DB::table('company_plan')->where('company_id', $id)->delete();


        //foreach ($request->input('p') as $key => $value) {
        $plan = Plan::where('id', $request->get('plan'))->first();
        $company->plans()->attach($plan);
        //}

        return redirect()->route('app.member', ['domain' => domain()])->with('success', 'Information has been  Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $company = Company::findOrFail($request->id);
        $company->delete();
        flash()->success('Post has been deleted.');
        return back();
    }

}
