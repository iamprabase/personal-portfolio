<?php

namespace App\Http\Controllers\Company\Admin;

use App\Product;
use App\Category;
use App\Employee;
use Illuminate\Http\Request;
Use Auth;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;


class CategoryController extends Controller
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

    private function categoryNotification ($company_id, $category_id, $action) {
      
      $fbID = Employee::where('company_id', config('settings.company_id'))->where('status', 'Active')->whereNotNull('firebase_token')->pluck('firebase_token');
      $dataPayload = array("data_type" => "category_rate", 'category_id' => $category_id, "action" => $action);
      sendPushNotification_($fbID, config('firebaseNotification.NOTIFICATION_CASE_CATEGORY_PRICING'), null, $dataPayload);

    }


    public function index()
    {
        if(config('settings.product')==1 && Auth::user()->can('settings-view')|| Auth::user()->employee()->first()->role()->first()->name=="Full Access"){  
            $company_id = config('settings.company_id');
            $categories = Category::where('company_id', $company_id)
                ->orderBy('created_at', 'desc')
                ->get();
            return view('company.categories.index', compact('categories'));
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
          
       return view('company.categories.create');
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
        $company_id = config('settings.company_id');

        $customMessages = [
            'name.required' => 'Category Name already exists',
            'name.unique' => 'Category Name must be unique',
        ];
        $this->validate($request, [

            'name' => 'required|unique:categories,name,NULL,id,company_id,' . $company_id . ',deleted_at,NULL',

        ], $customMessages);

        $category = new \App\Category;
        $category->company_id = $company_id;
        $category->name = $request->get('name');
        $category->desc = str_replace('&nbsp;', '', $request->get('desc'));
                
        $category->status = $request->get('status');

        $category->save();
        
        $this->categoryNotification($company_id, $category->id, "add");

        return redirect()->route('company.admin.category', ['domain' => domain()])->with('success', 'Information has been  Added');
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
        $category = Category::where('id', $request->id)->where('company_id', $company_id)->first();
        if ($category)
            return view('company.categories.edit', compact('category'));
        else
            return redirect()->route('company.admin.category', ['domain' => domain()]);
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
        $category = Category::findOrFail($request->id);
        $customMessages = [
            'name.required' => 'Category Name already exists',
            'name.unique' => 'Category Name must be unique',
        ];
        $this->validate($request, [
            'name' => 'required|unique:categories,name,' . $request->id . ',id,company_id,' . $company_id . ',deleted_at,NULL',
        ], $customMessages);

        $category->name = $request->get('name');
        $category->desc = str_replace('&nbsp;', '', $request->get('desc'));
        $category->status = $request->get('status');

        $saved = $category->save();

        $this->categoryNotification($company_id, $category->id, "update");

        if ($saved) {
            $products = Product::where('company_id', $company_id)->where('category_id', $category->id)->get();
            if ($category->status == 'Inactive') {
                foreach ($products as $product) {
                    $product->status = 'Inactive';
                    $product->save();
                }
            } elseif ($category->status == 'Active') {
                foreach ($products as $product) {
                    $product->status = 'Active';
                    $product->save();
                }
            }
        }
        return redirect()->route('company.admin.category', ['domain' => domain()])->with('success', 'Information has been  Updated');
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
        $category = Category::findOrFail($request->id);
        if (!empty($category)) {
            $products = Product::where('category_id', $request->id)->where('company_id', $company_id)->get();
            if($products->first()){
              Session::flash('warning', 'Category cannot be deleted as it has products under it.');
              return back();
            }
            foreach ($products as $product) {
                $product->delete();
            }
            $category->delete();
        }
        Session::flash('success', 'Category has been deleted.');
        return back();
    }

    public function changeStatus(Request $request)
    {
        $company_id = config('settings.company_id');
        $category = Category::findOrFail($request->category_id);
        $category->status = $request->status;
        $saved = $category->save();
        if ($saved) {
            $products = Product::where('company_id', $company_id)->where('category_id', $category->id)->get();
            if ($category->status == 'Inactive') {
                foreach ($products as $product) {
                    $product->status = 'Inactive';
                    $product->save();
                }
            } elseif ($category->status == 'Active') {
                foreach ($products as $product) {
                    $product->status = 'Active';
                    $product->save();
                }
            }
        }
        return back();
    }


    public function custompdfdexport(Request $request){
      $getExportData = json_decode($request->exportedData);
      $pageTitle = $request->pageTitle;
      set_time_limit ( 300 );
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      $pdf = PDF::loadView('company.categories.exportpdf', compact('getExportData', 'pageTitle', 'columns',
      'properties'))->setPaper('a4', 'portrait');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }
}
