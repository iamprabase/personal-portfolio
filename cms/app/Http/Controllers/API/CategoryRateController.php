<?php

namespace App\Http\Controllers\API;

use App\Employee;
use Illuminate\Http\Request;
use App\CategoryRateTypeRate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CategoryRateController extends Controller
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
    $category_rate_type_id = json_decode($request->category_rate_type_id);

    $results = CategoryRateTypeRate::whereIn('category_rate_type_id', $category_rate_type_id)
                  ->get(['id', 'category_id', 'category_rate_type_id', 'product_id', 'product_variant_id', 'mrp']);

    return response()->json([
      $results
    ]);
  }

  public function show(Request $request){
    $user = Auth::user();
    $companyID = $user->company_id;
    $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
    $employeeID = $employee->id;

    $results = CategoryRateTypeRate::where('category_rate_type_id', $request->category_rate_type_id)
                  ->get(['id', 'category_id', 'category_rate_type_id', 'product_id', 'product_variant_id', 'mrp']);

    return response()->json([
      $results
    ]);
  }

  public function edit(Request $request){
    $user = Auth::user();
    $companyID = $user->company_id;
    $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
    $employeeID = $employee->id;

    $results = CategoryRateType::where('category_rate_type_id', $request->category_rate_type_id)
                  ->get(['id', 'category_id', 'name']);

    return response()->json([
      $results
    ]);
  }
}
