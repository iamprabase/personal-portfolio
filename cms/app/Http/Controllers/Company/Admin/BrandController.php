<?php

namespace App\Http\Controllers\Company\Admin;


use DB;

use Session;

use App\Brand;

Use Auth;

use App\Product;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;


class BrandController extends Controller

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
        if(config('settings.product')==1 && Auth::user()->can('settings-view')|| Auth::user()->employee()->first()->role()->first()->name=="Full Access"){
            $company_id = config('settings.company_id');
            $brands = Brand::where('company_id', $company_id)
            // ->orWhere('company_id', 0)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('company.brands.index', compact('brands'));
        }
     return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()

    {
        if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
        return view('company.brands.create');
    }
     return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
    }



    // /**

    //  * Store a newly created resource in storage.

    //  *

    //  * @param  \Illuminate\Http\Request  $request

    //  * @return \Illuminate\Http\Response

    //  */

    public function store(Request $request)

    {

        $customMessages = [

            'name.required' => 'The brand Name is required',

        ];


        $company_id = config('settings.company_id');

        $this->validate($request, [

            'name' => 'required|unique:brands,name,NULL,id,company_id,' . $company_id,

        ], $customMessages);


        $brand = new \App\Brand;

        $brand->company_id = $company_id;

        $brand->name = $request->get('name');

        $brand->status = $request->get('status');


        $brand->save();

        return redirect()->route('company.admin.brand', ['domain' => domain()])->with('success', 'Information has been  Added');

    }



    // /**

    //  * Display the specified resource.

    //  *

    //  * @param  int  $id

    //  * @return \Illuminate\Http\Response

    //  */

    // public function show($id)

    // {

    //     $plan = plan::find($id);

    //     return view('plans.show',compact('plan'));

    // }


    // /**

    //  * Show the form for editing the specified resource.

    //  *

    //  * @param  int  $id

    //  * @return \Illuminate\Http\Response

    //  */

    public function edit(Request $request)

    {
        if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
        $company_id = config('settings.company_id');

        $brand = Brand::where('id', $request->id)->where('company_id', $company_id)->first();

        if ($brand)

            return view('company.brands.edit', compact('brand'));

        else

            return redirect()->route('company.admin.brand', ['domain' => domain()]);
    }
     return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);

    }



    // /**

    //  * Update the specified resource in storage.

    //  *

    //  * @param  \Illuminate\Http\Request  $request

    //  * @param  int  $id

    //  * @return \Illuminate\Http\Response

    //  */

    public function update(Request $request)

    {

        $company_id = config('settings.company_id');

        $brand = Brand::findOrFail($request->id);


        // // checking number of active users exceeded

        // if($brand->status == 'Inactive' && $request->status == 'Active'){

        //     $comp_plan=getCompanyPlan($company_id);

        //     $comp_plan->users;

        //     $empgroupmembercount = Employee::where('company_id', $company_id)->where('brand', $brand->id)->where('status', 'Inactive')->count();

        //     $employeescount = Employee::where('company_id', $company_id)->where('status','Active')->count();

        //     if( ($employeescount + $empgroupmembercount) > $comp_plan->users){

        //         Session::flash('message', 'This Group cannot be activated. Number of users exceed the number of employees allowed in current plan');

        //         return redirect()->route('company.admin.brand', ['domain' => domain()]);

        //     }

        // }


        $customMessages = [

            'name.required' => 'The brand Name is required',

        ];


        $this->validate($request, [

            'name' => 'required|unique:brands,name,' . $request->id . ',id,company_id,' . $company_id,

        ], $customMessages);


        $brand->name = $request->get('name');

        $brand->status = $request->get('status');


        $saved = $brand->save();


        // if($saved && $brand->status == "Inactive")

        // {

        //     $employees = Employee::where('company_id', $company_id)->where('status', 'Active')->where('brand', $brand->id)->get();

        //     foreach ($employees as $employee) {

        //         $employee->status = "Inactive";

        //         if(!empty($employee->firebase_token)){

        //             //$dataPayload = array("data_type"=>"Logout","employee"=>$employee);

        //             //$msgSent = sendPushNotification(null,$employee->firebase_token,$dataPayload,true);

        //             $msgSent = sendPushNotification_([$employee->firebase_token],4,null,null);

        //         }

        //         $employee->save();

        //     }

        // } elseif ($saved && $brand->status == "Active") {

        //     $employees = Employee::where('company_id', $company_id)->where('status', 'Inactive')->where('brand', $brand->id)->get();

        //     foreach ($employees as $employee) {

        //         $employee->status = "Active";

        //         $employee->save();

        //     }

        // }

        return redirect()->route('company.admin.brand', ['domain' => domain()])->with('success', 'Information has been  Updated');

    }





    // /**

    //  * Remove the specified resource from storage.

    //  *

    //  * @param  int  $id

    //  * @return \Illuminate\Http\Response

    //  */

    public function destroy(Request $request)

    {

        $company_id = config('settings.company_id');

        $brand = Brand::where('company_id', $company_id)->findOrFail($request->id);

        if (!empty($brand)) {
          $brands = Product::where('brand', $request->id)->where('company_id', $company_id)->first();
          if($brands){
            Session::flash('warning', 'Brand cannot be deleted as it has products under it.');
            return back();
          }
      }
      
      $brand->delete();
      
      Session::flash('success', 'Brand has been deleted.');

        return back();

    }


    public function changeStatus(Request $request)

    {

        $company_id = config('settings.company_id');

        $brand = Brand::findOrFail($request->brand_id);


        // checking number of active users exceeded

        // if($brand->status == 'Inactive' && $request->status == 'Active'){

        //     $comp_plan=getCompanyPlan($company_id);

        //     $comp_plan->users;

        //     $empgroupmembercount = Employee::where('company_id', $company_id)->where('brand', $brand->id)->where('status', 'Inactive')->count();

        //     $employeescount = Employee::where('company_id', $company_id)->where('status','Active')->count();

        //     if( ($employeescount + $empgroupmembercount) > $comp_plan->users){

        //         Session::flash('message', 'This Group cannot be activated. Number of users exceed the number of employees allowed in current plan');

        //         return redirect()->route('company.admin.brand', ['domain' => domain()]);

        //     }

        // }


        $brand->status = $request->status;

        $saved = $brand->save();


        // if($saved && $brand->status == "Inactive")

        // {

        //     $employees = Employee::where('company_id', $company_id)->where('status', 'Active')->where('brand', $brand->id)->get();

        //     foreach ($employees as $employee) {

        //         $employee->status = "Inactive";

        //         if(!empty($employee->firebase_token)){

        //             $dataPayload = array("data_type"=>"Logout","employee"=>$employee);

        //             //$msgSent = sendPushNotification(null,$employee->firebase_token,$dataPayload,true);

        //             $msgSent = sendPushNotification_([$employee->firebase_token],4,null,null);

        //         }

        //         $employee->save();

        //     }

        // } elseif ($saved && $brand->status == "Active") {

        //     $employees = Employee::where('company_id', $company_id)->where('status', 'Inactive')->where('brand', $brand->id)->get();

        //     foreach ($employees as $employee) {

        //         $employee->status = "Active";

        //         $employee->save();

        //     }

        // }


        return back();

    }

    public function custompdfdexport(Request $request){
      $getExportData = json_decode($request->exportedData);
      $pageTitle = $request->pageTitle;
      set_time_limit ( 300 );
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      $pdf = PDF::loadView('company.brands.exportpdf', compact('getExportData', 'pageTitle', 'columns',
      'properties'))->setPaper('a4', 'portrait');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }

}

