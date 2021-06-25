<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee;
use App\Bank;
use App\User;
use App\Designation;
use App\EmployeeGroup;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Product;
use App\Client;
Use Auth;

use DB;
use Carbon\Carbon;
use Storage;
use Log;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeesImport;
use App\Imports\ProductsImport;
use App\Imports\ClientsImport;
use App\Imports\OutstandingImport;


class ImportController extends Controller
{
    private $company_id;
    public function __construct()
    {
      $this->company_id = config('settings.company_id');
      $this->middleware('auth');
        // $this->middleware('permission:expense-create', ['only' => ['create','store']]);
      $this->middleware('permission:import-view');
        // $this->middleware('permission:expense-update', ['only' => ['edit','update']]);
        // $this->middleware('permission:expense-delete', ['only' => ['destroy']]);
    }

    public function index(){
      return view('company.import.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function importEmployees()
    {
      return view('company.import.employee');
    }

    private function sendNotification($company_id, $module){
      $dataPayload = array("data_type" => $module, "module" => $module, "action" => "import");
      
      $stats = sendPushNotification_(getFBIDs($company_id), 38, null, $dataPayload);     
      
      return $stats; 
    }

    public function addEmployees(Request $request)
    {

      $request->validate([
        'import_file' => 'required'
      ]);
      Excel::import(new EmployeesImport,request()->file('import_file'));
      
      $this->sendNotification($this->company_id, 'employees');

      return back()->with('success', 'Insert Record successfully.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function importProducts()
    {
        return view('company.import.product');
    }

    public function addProducts(Request $request)
    {

      $request->validate([
        'import_file' => 'required'
      ]);
      //echo "i am here"; die;
      $importdata=Excel::import(new ProductsImport,request()->file('import_file'));
      if($importdata){
        $this->sendNotification($this->company_id, 'products');
        return back()->with('success', 'Insert Record successfully.');
      }else{
        return back()->with('error', 'some error.');
      }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function importClients()
    {
      $hasClient = Client::whereCompanyId($this->company_id)->first();
        return view('company.import.client', compact('hasClient'));
    }

    
    public function addClients(Request $request)
    {

      $request->validate([
        'import_file2' => 'required'
      ]);
      // echo "i am here";
      // die;
      $importdata2=Excel::import(new ClientsImport,request()->file('import_file2'));
     // return back()->with('success', 'Insert Record successfully.');
      if($importdata2){

       // dd($importdata2->$request->all());



        $this->sendNotification($this->company_id, 'clients');
        return back()->with('success', 'Insert Record successfully.');
      }else{
        return back()->with('error', 'some error.');
      }
    }


    public function importOutstanding()
    {   
      $hasClient = Client::whereCompanyId($this->company_id)->first();
      if(config('settings.outstanding_amt_calculation')==0 || !$hasClient){
        $msg = config('settings.outstanding_amt_calculation') == 1 ? "Please add some parties first." : "You don\'t have sufficient permission to view this content. "; 
        return redirect()->route('company.admin.home', ['domain' => domain()])->with(['error'=> $msg]);
      }

        return view('company.import.clientoutstanding');
    }

    public function addOutstanding(Request $request)
    {

      $request->validate([
        'import_file3' => 'required'
      ]);
      $importdata3=Excel::import(new OutstandingImport,request()->file('import_file3'));
      if($importdata3){
        //$this->sendNotification($this->company_id, 'products');
        return back()->with('success', 'Insert Record successfully.');
      }else{
        return back()->with('error', 'some error.');
      }
    }

}
