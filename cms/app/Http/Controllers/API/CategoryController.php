<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{

    public function __construct()
    {
		  $this->middleware('auth:api');
    }

    
    public function index(Request $request){
      $user = Auth::user();
      $companyID = $user->company_id;
      $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
      $employeeID = $employee->id;

      $categories = Category::select('name', 'id', 'status')->with(['categoryrates' => function($query) {
        $query->with(['itemrates' => function($subquery){
          $subquery->select('category_rate_type_id', 'product_id', 'product_variant_id as variant_id', 'mrp');
        }])->select('name', 'id', 'category_id');
      }])->whereCompanyId($companyID)->where( function($query) use($request){
        if($request->has('id')){
          $query->where('id', $request->id);
        }
      });
      if($request->has('id')){
        $results = $categories->first();
      }else{
        $results = $categories->get();
      }

      return $results;
    }
}
