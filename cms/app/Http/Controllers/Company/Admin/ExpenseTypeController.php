<?php

namespace App\Http\Controllers\Company\Admin;

use View;
use App\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ExpenseTypeController extends Controller
{
    public function __construct()
    {
    	$this->middleware('auth');
    }

    public function store($domain,Request $request)
    {
    	$company_id = config('settings.company_id');
    	$expenseType = ExpenseType::where('company_id',$company_id)->where('expensetype_name',$request->name)->first();
    	if($expenseType){
    		$data= ['status'=>false,'message'=>'Expense Category already exists'];
    	}else{
    		$expenseType = new ExpenseType;
    		$expenseType->expensetype_name = $request->name;
    		$expenseType->company_id = $company_id;
    		$expenseType->save();
    		$expense_types = ExpenseType::where('company_id',$company_id)->orderBy('expensetype_name','ASC')->get();
            $expenseTypes = View::make('company.settings.ajaxexpensetypelists',compact('expense_types'))->render();
    		$data= ['status'=>true,'message'=>'Expense Category Added Successfully','expenseTypes'=>$expenseTypes];
            $dataPayload = array("data_type" => "expense_type", "expense_type" => $expenseType, "action" => "add");
            $msgID = sendPushNotification_(getFBIDs($company_id), 33, null, $dataPayload);
        }
    	return $data;
    }

    public function update($domain,$id,Request $request)
    {
    	$company_id = config('settings.company_id');
    	$expenseType = ExpenseType::where('company_id',$company_id)->where('expensetype_name',$request->name)->where('id','!=',$id)->first();
    	if($expenseType){
    		$data= ['status'=>false,'message'=>'Expense Category already exists'];
    	}else{
    		$expenseType = ExpenseType::where('company_id',$company_id)->where('id',$id)->first();
    		$expenseType->expensetype_name = $request->name;
    		$expenseType->company_id = $company_id;
    		$expenseType->save();
    		$expense_types = ExpenseType::where('company_id',$company_id)->orderBy('expensetype_name','ASC')->get();
            $expenseTypes = View::make('company.settings.ajaxexpensetypelists',compact('expense_types'))->render();
            $data= ['status'=>true,'message'=>'Expense Category Updated Successfully','expenseTypes'=>$expenseTypes];
            $dataPayload = array("data_type" => "expense_type", "expense_type" => $expenseType, "action" => "update");
            $msgID = sendPushNotification_(getFBIDs($company_id), 33, null, $dataPayload);
    	}
    	return $data;
    }

    public function destroy($domain,$id)
    {
    	$company_id = config('settings.company_id');
    	$expenseType = ExpenseType::where('company_id',$company_id)->where('id',$id)->first();
    	if($expenseType){
    		if(count($expenseType->expenses)>0){
    			$data= ['status'=>false,'message'=>'Sorry! Could not delete this Expense Category because it been used by clients'];
    		}else{
          $clonedExpenseType = $expenseType;
                $expenseType->delete();
                	$expense_types = ExpenseType::where('company_id',$company_id)->orderBy('expensetype_name','ASC')->get();
                $expenseTypes = View::make('company.settings.ajaxexpensetypelists',compact('expense_types'))->render();
                	$data= ['status'=>true,'message'=>'Expense Category Deleted Successfully','expenseTypes'=>$expenseTypes];
                $dataPayload = array("data_type" => "expense_type", "expense_type" => $clonedExpenseType, "action" => "delete");
                $msgID = sendPushNotification_(getFBIDs($company_id), 33, null, $dataPayload);
            }
    	}else{
    		$data= ['status'=>false,'message'=>'Sorry! Request ExpenseType was not found.'];
    	}
    	return $data;
    }

}
