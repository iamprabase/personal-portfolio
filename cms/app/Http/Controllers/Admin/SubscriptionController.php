<?php

namespace App\Http\Controllers\Admin;

use App\Plan;
use App\Module;
use App\Company;
use App\MainModule;
use App\Subscription;
use App\ClientSetting;
use App\PaymentOption;
use Illuminate\Http\Request;
use App\Events\CutomPlanUpdated;
use App\SubscriptionExtraCharge;
use App\SubscriptionFreeFeature;
use App\Mail\SubscriptionCreated;
use App\Jobs\MailRegistrationLink;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Jobs\AssignFullAccessPermission;

class SubscriptionController extends Controller
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
        return view( 'admin.subscriptions.index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $plans = Plan::orderBy('id','DESC')->whereNull('users')
                      ->pluck('name', 'id')->toArray();
      $currency = DB::table('currency')->whereIN('country', ["United States of America", "India", "Nepal"])->select('id', DB::raw("CONCAT(country, ' (', currency,', ', code,')') as currency"))->pluck('currency', 'id')->toArray();
      $payment_option = PaymentOption::get()->pluck('name', 'id')->toArray();
      $extension  = DB::table('countries')->select('phonecode as extension', DB::raw("CONCAT(name, ' (', phonecode,')') as phonecode"))->pluck('phonecode', 'extension')->toArray();
      
      return view('admin.subscriptions.create', compact('plans', 'currency', 'payment_option', 'extension'));
    }

    public function store(Request $request)
    {

      $this->validate($request, [
        'plan_id' => 'required',
        'name' => 'required|unique:subscriptions',
        'domain' => 'nullable|unique:subscriptions|regex:/^[a-zA-Z]+$/',
        'email' => 'required|unique:subscriptions',
        'extension' => 'required|regex:/^[0-9]+$/',
        'phone' => 'required|unique:subscriptions|regex:/^[0-9]{10,15}$/',
        'min_users' => 'required|regex:/^[0-9]+$/|min:0',
        'currency_id' => 'required|regex:/^[0-9]+$/',
        'price_per_user' => 'required|regex:/^[0-9]+\.?[0-9]*?$/|min:0',
        'setup_fee' => 'required|regex:/^[0-9]+\.?[0-9]*?$/|min:0',
        'expiry_after_current_billing' => 'required',
        'auto_renewal_time' => 'nullable',
        'payment_option' => 'required',
        'trial_days' => 'required|regex:/^[0-9]+$/|min:0',
        'free_num_users_months.*' => 'nullable',
        'free_num_months_users.*' => 'nullable',
        'charge_name.*' => 'sometimes|required',
        'charge_price.*' => 'sometimes|required',
        'price_type.*' => 'sometimes|required',
        'chargeIndexes.*' => 'nullable'
      ]);
      try{

        DB::beginTransaction();
        $subscription = new Subscription;
        $subscription->subscription_code = 'DSA-'. uniqid() . '-' . now()->timestamp;
        $subscription->plan_id = $request->plan_id;
        $subscription->name = $request->name;
        $subscription->domain = $request->domain;
        $subscription->email = $request->email;
        $subscription->extension = $request->extension;
        $subscription->phone = $request->phone;
        $subscription->min_users = $request->min_users;
        $subscription->currency_id = $request->currency_id;
        $subscription->price_per_user = $request->price_per_user;
        $subscription->setup_fee = $request->setup_fee;
        $subscription->expiry_after_current_billing = $request->expiry_after_current_billing;
        $subscription->auto_renewal_days = $request->auto_renewal_time;
        $subscription->trial_days = $request->trial_days;

        $subscription->save();
        $payment_options = $request->payment_option;
        $subscription->paymentoptions()->attach($payment_options);
        $free_features = $request->free_feature;
        foreach($free_features as $key => $features){
          $breakLoop = false;
          foreach($features as $index => $value){
            if(is_null($value)){
              $breakLoop = true;
              continue;
            }
          }
          if($breakLoop) continue;
          $insertFreeFeature = [
            'key' => $key,
            'subscription_id' => $subscription->id,
            'value' => json_encode($features)
          ];
          SubscriptionFreeFeature::create($insertFreeFeature);
        }

        if($request->has('chargeIndexes')){
          $appliedCharges = $request->chargeIndexes;
          foreach($appliedCharges as $index=>$appliedChargeIndex){
            $chargeFor = $request->charge_name[$index];
            $charge = $request->charge_price[$index];
            $chargeType = $request->price_type[$index];
            SubscriptionExtraCharge::create([
              'subscription_id' => $subscription->id,
              'name' => $chargeFor,
              'price' => $charge,
              'pricing_type' => $chargeType
            ]);
          }
        }
        
        /**
         * Mail Subscription To User
         */
        $link = route('registercompany.subscription.register', $subscription->subscription_code);
        DB::commit();
        MailRegistrationLink::dispatch($subscription->email, $link);
        return redirect()->route('app.subscription.index')->with('success', 'Information has been Added');
      }catch(\Exception $e){
        Log::error(array("Subscription Save", $e->getMessage()));
        return redirect()->back()->withInput()->withErrors(['error' => "Some Error Occured. Please try again."]);
      }
    }

    /**
     * Display the specified resource.
     *php
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plan = Plan::find($id);
        // return view('admin.custom-plan.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->load('modules');
        $planModules = $plan->modules->pluck('field')->toArray();
        $modules = Module::orderBy('position','ASC')
                      ->get(['id', 'name', 'field']);

        // return view('admin.custom-plans.edit', compact('plan', 'modules', 'planModules'));
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
        $customMessages = [
          'name' => 'The Plan Name field is required.',
        ];
        $this->validate($request, [
          'name' => 'required|unique:plans',
          'default_price' => 'required|regex:/^[0-9]+\.?[0-9]*?$/',
          'module' => 'required',
          'module.*' => 'required'
        ], $customMessages);

        DB::beginTransaction();
        $plan = Plan::findOrFail($id);
      try{
        $plan->name = $request->get('name');
        $plan->description = $request->get('description');
        $plan->default_price = $request->get('default_price');
        $plan->status = 'Active';
        $plan->update();

        $modules = Module::orderBy('position','ASC')->pluck('field', 'id')->toArray();
        $checked_modules = $request->module;
        $insertData = array();
        DB::table('plan_has_modules')->where('plan_id', $id)->delete();
        foreach($modules as $id => $module){
          if(!array_key_exists($module, $checked_modules)) continue;
          $data = array(
            'plan_id' => $plan->id,
            'module_id' => $id,
            'enabled' => 1
          ); 
          array_push($insertData, $data);
        }
        if(!empty($insertData)) DB::table('plan_has_modules')->insert($insertData);
        DB::commit();

        event(new CutomPlanUpdated($id));

        // return redirect()->route('app.custom-plan.index')->with('success', 'Information has been  Updated.');
      }catch(\Exception $e){
        DB::rollback();
        Log::error(array("Plan Update Error:", $e->getMessage()));
        // return redirect()->route('app.custom-plan.index')->with('warning', 'Some Error Occured.');
      }
    }

    // private function enableForFullAccess($company_id, $module, $permission_category_name){
    //   $role = Role::whereCompanyId($company_id)->whereName('Full Access')->whereGuardName('web')->first();
    //   $permission_category_party_uploads_modules = DB::table('permission_categories')->where('name', 'LIKE', $permission_category_name)->get();
    //   if($permission_category_party_uploads_modules->first()){
    //     try{
    //       foreach($permission_category_party_uploads_modules as  $permission_category_party_uploads_module){
    //         $permission_category_party_uploads_module_id = $permission_category_party_uploads_module->id;
    //         $permissions_partyuploads = DB::table('permissions')->wherePermissionCategoryId($permission_category_party_uploads_module_id)->pluck('name')->toArray();
    //         foreach($permissions_partyuploads as $permission_name){
    //           $role->givePermissionTo($permission_name);
    //         }
    //       }
    //     }catch(\Exception $e){
    //       Log::info($e->getCode());
    //     }
    //     return true;
    //   }
    // }

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
