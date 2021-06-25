<?php

namespace App\Http\Controllers\Admin;

use App\Plan;
use App\Module;
use App\Company;
use App\MainModule;
use App\ClientSetting;
use Illuminate\Http\Request;
use App\Events\CutomPlanUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Jobs\AssignFullAccessPermission;
use Illuminate\Validation\ValidationException;

class CustomPlanController extends Controller
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
      $plans = Plan::with(['modules'=>function($q){
        $q->select(['name']);
      }])->whereNULL('users')->get(['name', 'description', 'default_price', 'status', 'id']);
      
      return view( 'admin.custom-plans.index', compact('plans') );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $modules = Module::orderBy('position','ASC')
                      ->get(['id', 'name', 'field']);
      $currency = DB::table('currency')->whereIN('country', ["United States of America", "India", "Nepal"])->select('id', DB::raw("CONCAT(country, ' (', currency,', ', code,')') as currency"))->pluck('currency', 'id')->toArray();
      
      return view('admin.custom-plans.create', compact('modules', 'currency'));
    }

    public function store(Request $request)
    {
      $customMessages = [
        'name' => 'The Plan Name field is required.',
      ];
      $this->validate($request, [
        'name' => 'required|unique:plans',
        'default_price' => 'required|regex:/^[0-9]+\.?[0-9]*?$/',
        'module' => 'required',
        'module.*' => 'required',
        'currency' => 'required',
      ], $customMessages);
      
      $checked_modules = $request->module;
      $allExistingPlansWithSimilarModules = Plan::select(['id', 'name'])->with(['modules' => function($query) use($checked_modules){
        $query->select('modules.id')->whereIn('modules.id', $checked_modules);
      }])->withCount(['modules'])->having('modules_count', '=', 2)->get();
      foreach($allExistingPlansWithSimilarModules as $allExistingPlansWithSimilarModule){
        if($allExistingPlansWithSimilarModule->modules->count() !== count($checked_modules)) continue;

        else throw ValidationException::withMessages(['module' => 'Plan "'. $allExistingPlansWithSimilarModule->name .'" having same modules already exists. Please consider using that plan.']);
      }

      DB::beginTransaction();
      $plan = new Plan;
      $plan->name = $request->get('name');
      $plan->description = $request->get('description');
      $plan->default_price = $request->get('default_price');
      $plan->currency_id = $request->get('currency');
      $plan->status = 'Active';
      $plan->save();
      $plan->modules()->attach($checked_modules, ["enabled" => 1]);
      DB::commit();

      return redirect()->route('app.custom-plan.index')->with('success', 'Information has been  Added');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plan = Plan::find($id);
        return view('admin.custom-plan.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $plan = Plan::with(['modules'=>function($q){
          $q->select(['name']);
        }])->findOrFail($id);
        
        $modules = Module::orderBy('position','ASC')
                      ->get(['id', 'name', 'field']);
        $currency = DB::table('currency')->whereIN('country', ["United States of America", "India", "Nepal"])->select('id', DB::raw("CONCAT(country, ' (', currency,', ', code,')') as currency"))->pluck('currency', 'id')->toArray();
      
        return view('admin.custom-plans.edit', compact('plan', 'modules', 'currency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $customMessages = [
        'name' => 'The Plan Name field is required.',
      ];
      $this->validate($request, [
        'name' => 'required|unique:plans,NULL,'.$id.',id',
        'default_price' => 'required|regex:/^[0-9]+\.?[0-9]*?$/',
        'module' => 'required',
        'module.*' => 'required',
        'currency' => 'required',
      ], $customMessages);

      $checked_modules = $request->module;
      $allExistingPlansWithSimilarModules = Plan::select(['plans.id', 'name'])->where('plans.id', '!=', $id)->with(['modules' => function($query) use($checked_modules){
        $query->select('modules.id')->whereIn('modules.id', $checked_modules);
      }])->withCount(['modules'])->having('modules_count', '=', 2)->get();
      foreach($allExistingPlansWithSimilarModules as $allExistingPlansWithSimilarModule){
        if($allExistingPlansWithSimilarModule->modules->count() !== count($checked_modules)) continue;

        else throw ValidationException::withMessages(['module' => 'Plan "'. $allExistingPlansWithSimilarModule->name .'" having same modules already exists. Please consider using that plan.']);
      }

      DB::beginTransaction();
      $plan = Plan::find($id);
      $plan->name = $request->get('name');
      $plan->description = $request->get('description');
      $plan->default_price = $request->get('default_price');
      $plan->status = 'Active';
      $plan->currency_id = $request->get('currency');
      $plan->update();
      $plan->modules()->sync($checked_modules);
      $plan->modules()->update(["enabled" => 1]);
      DB::commit();

      return redirect()->route('app.custom-plan.index')->with('success', 'Information has been  updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $plan = Plan::find($request->id);
        if($plan){
            if(count($plan->companies)>0){
                return redirect()->back()->with('alert','Plan has companies and can not be deleted');
            }
            $plan->delete();
        }
        flash()->success('plan has been deleted.');
        return back();
    }

}
