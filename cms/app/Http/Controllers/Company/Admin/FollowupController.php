<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Followup;

class FollowupController extends Controller
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
        $followups = Followup::all()->sortByDesc("created_at");;
        return view('company.followups.index', compact('followups'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('company.followups.create');
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
            'companyName.required' => 'The Name field is required.',
            'c_password.same' => 'Password do not match.',
            'c_password.required' => 'Confirm Password field is required.',
        ];


        $this->validate($request, [
            'companyName' => 'required',
            'alias' => 'required|unique:companies',
            'name' => 'required',
            'email' => 'required|email|unique:companies',
            'phone' => 'required|digits_between:7,14',
            'mobile' => 'required|digits_between:7,14',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
        ], $customMessages);


        $followup = new \App\Followup;
        $followup->followupName = $request->get('followupName');
        $followup->alias = $request->get('alias');
        $followup->name = $request->get('name');
        $followup->email = $request->get('email');
        $followup->password = bcrypt($request->get('password'));
        $followup->phone = $request->get('phone');
        $followup->extNo = $request->get('extNo');
        $followup->mobile = $request->get('mobile');
        $followup->fax = $request->get('fax');
        $followup->pan = $request->get('pan');
        $followup->whitelabel = $request->get('whitelabel');
        $followup->customize = $request->get('customize');
        $followup->startdate = $request->get('startdate');
        $followup->enddate = $request->get('enddate');
        $followup->aboutCompany = $request->get('aboutCompany');
        $followup->status = $request->get('status');


        $followup->save();

        return redirect()->route('followup', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Followup $followup
     * @return \Illuminate\Http\Response
     */
    public function show(Followup $followup)
    {
        $followup = Company::find($followup->id);
        return view('company.followups.show', compact('followup'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Followup $followup
     * @return \Illuminate\Http\Response
     */
    public function edit(Followup $followup)
    {
        $followup = Followup::findOrFail($followup);
        return view('company.followups.edit', compact('followup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Followup $followup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Followup $followup)
    {
        $followup = Company::findOrFail($id);
        $customMessages = [
            'followupName.required' => 'The Name field is required.',
            'c_password.same' => 'Password do not match.',
            'c_password.required' => 'Confirm Password field is required.',
        ];


        $this->validate($request, [
            'followupName' => 'required',
            'alias' => 'required|unique:companies,alias,' . $id,
            'name' => 'required',
            'email' => 'required|email|unique:companies,email,' . $id,
            'phone' => 'required|digits_between:7,14',
            'mobile' => 'required|digits_between:7,14',
        ], $customMessages);


        // 'email' => 'required|email|unique:users,email,' . $id,
        //    'roles' => 'required|min:1'
        //$followup= new \App\Company;
        $followup->followupName = $request->get('followupName');
        $followup->alias = $request->get('alias');
        $followup->name = $request->get('name');
        $followup->email = $request->get('email');
        $followup->password = bcrypt($request->get('password'));
        $followup->phone = $request->get('phone');
        $followup->extNo = $request->get('extNo');
        $followup->mobile = $request->get('mobile');
        $followup->fax = $request->get('fax');
        $followup->pan = $request->get('pan');
        $followup->whitelabel = $request->get('whitelabel');
        $followup->customize = $request->get('customize');
        $followup->startdate = $request->get('startdate');
        $followup->enddate = $request->get('enddate');
        $followup->aboutCompany = $request->get('aboutCompany');
        $followup->status = $request->get('status');

        if (!empty($request->input('password'))) {
            $this->validate($request, [
                'password' => 'required|min:8',
                'c_password' => 'same:password',

            ], $customMessages);
            $followup->password = bcrypt($request->get('password'));
        }


        $followup->save();

        return redirect()->route('followup', ['domain' => domain()])->with('success', 'Information has been  Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Followup $followup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Followup $followup)
    {
        $followup = Followup::findOrFail($followup->id);
        $followup->delete();
        flash()->success('Followup has been deleted.');
        return back();
    }
}
