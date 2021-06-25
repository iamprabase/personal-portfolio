<?php

namespace App\Http\Controllers\Company\Admin;

use Auth;
use View;
use App\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DesignationController extends Controller
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
        $company_id=config('settings.company_id');
        $designations = Designation::all()->sortByDesc("created_at");
        return view('company.designation.index',compact('designations'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('company.designations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $box = $request->all();        
        $requests=  array();
        parse_str($box['data'], $requests);

        $company_id=config('settings.company_id');
        $designationExists = Designation::where('company_id',$company_id)->where('name',$requests['name'])->first();
        if($requests['name']=="" || $designationExists != null){
          $data['result'] = false;
          $data['message'] = "Designation name already exists.";
          return $data;
        }
        if($requests['superior']==0){
            $data['result']=false;
            $data['message'] = "There can only be one designation of Admin Level.";
            return $data;
        }
        $allowed_user_hierarchy_level = config('settings.user_hierarchy_level');
        $current_level = Designation::DesignationLevel($requests['superior'], array($requests['superior']));
        if(count($current_level) >= $allowed_user_hierarchy_level){
          $data['result']=false;
          $data['message'] = "User hierarchy cannot be greater than ". $allowed_user_hierarchy_level ." levels. Upgrade to add more levels.";
          
          return $data;
        }

        $designation= new Designation;
        $designation->name=$requests['name'];
        $designation->parent_id = $requests['superior'];
        $designation->company_id=$company_id;    
        $designation->save();
        session()->flash('active', 'designation');
        $designations = Designation::where('company_id',$company_id)->get();
        $data['designations'] = View::make('company.settings.ajaxdesignation',compact('designations'))->render(); 
        $data['alldesignations'] = $designations;
        $data['result']=true;
        return $data;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(designation $designation)
    {
        $designation = Company::find($designation->id);
        return view('company.designation.show',compact('designation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(designation $designation)
    {
        $designation = Designation::findOrFail($designation);
        return view('company.designation.edit', compact('designation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\bank  $bank
     * @return \Illuminate\Http\Response
     */

    public function update($domain,$id,Request $request)
    {
        $company_id=config('settings.company_id');
        if(Auth::user()->isCompanyEmployee()){
            $data['result'] = "You don't have permission to delete this designation";
            return $data;
        }

        $designationExists = Designation::where('company_id',$company_id)->where('name',$request->name)->first();
        if($request->name=="" || $designationExists != null){
          $data['result'] = "Designation name already Exists";
        }else{
          $designation = Designation::where('company_id',$company_id)->where('id',$id)->first();
          if($designation && $designation->parent_id==0){
            $data['result'] = "Sorry! You can't update admin designation.";
          }else{
            $designation->name = $request->name;
            $designation->save();
            $data['result'] = "Designation has been updated";
          }
        }
        $designations = Designation::where('company_id',$company_id)->get();
        $data['designations'] = View::make('company.settings.ajaxdesignation',compact('designations'))->render(); 
        $data['alldesignations'] = $designations;
        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy($domain,$id,Request $request)
    {
        $company_id=config('settings.company_id');
        if(Auth::user()->isCompanyEmployee()){
            $data['result'] = "You don't have permission to delete this designation";
            return $data;
        }
        $designation = Designation::where('company_id',$company_id)->where('id',$id)->first();
        if($designation && $designation->parent_id==0){
          $data['result'] = "Sorry! You can't remove admin designation.";
        }else{          
          $childExists = Designation::where('company_id',$company_id)->where('parent_id',$id)->first();
          if($childExists){
              $data['result']="Can't delete this designation, since it has sub designations under it.";
          }else{
              if($designation->employees->count()>0){
                  $data['result'] = "Can't delete since employee has this designation assigned";
              }else{       
                  $designation->delete();
                  $data['result'] = "Designation Successfully Deleted";
              }            
          }
        }
        $designations = Designation::where('company_id',$company_id)->get();
        $data['designations'] = View::make('company.settings.ajaxdesignation',compact('designations'))->render(); 
        $data['alldesignations'] = $designations;
        return $data;
    }
}
