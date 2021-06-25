<?php

namespace App\Http\Controllers\User;

use App\Plan;
use App\Module;
use App\Company;
use App\MainModule;
use App\Subscription;
use App\ClientSetting;
use Illuminate\Http\Request;
use App\Events\CutomPlanUpdated;
use App\SubscriptionExtraCharge;
use App\SubscriptionFreeFeature;
use App\Mail\SubscriptionCreated;
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

    }




    public function index($subscription_code)
    {
        return view('admin.registration.register',[
            'data' => Subscription::whereSubscriptionCode($subscription_code)->first()
        ]);
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
      $currency = DB::table('currency')->select('id', DB::raw("CONCAT(country, ' (', currency,', ', code,')') as currency"))->pluck('currency', 'id')->toArray();
      $payment_option = array("Bank Transfer" => "Bank Transfer", "Himalayan Bank" => "Himalayan Bank", "To Checkout" => "To Checkout");
      $extension  = DB::table('countries')->select('phonecode as extension', DB::raw("CONCAT(name, ' (', phonecode,')') as phonecode"))->pluck('phonecode', 'extension')->toArray();
      
      return view('admin.subscriptions.create', compact('plans', 'currency', 'payment_option', 'extension'));
    }

    public function store(Request $request)
    {
      $this->validate($request, [
        'name' => 'required|unique:subscriptions',
        'plan_id' => 'required',
        'domain' => 'nullable|unique:subscriptions|regex:/^[a-zA-Z]+$/',
        'currency_id' => 'required|regex:/^[0-9]+$/',
        'extension' => 'required|regex:/^[0-9]+$/',
        'phone' => 'required|unique:subscriptions|regex:/^[0-9]{10,15}$/',
        'email' => 'required|unique:subscriptions',
        'price' => 'required|regex:/^[0-9]+\.?[0-9]*?$/|min:0',
        'price_per_user' => 'required|regex:/^[0-9]+\.?[0-9]*?$/|min:0',
        'setup_fee' => 'required|regex:/^[0-9]+\.?[0-9]*?$/|min:0',
        'auto_renewal_time' => 'required|regex:/^[0-9]+$/|min:0',
        'min_users' => 'required|regex:/^[0-9]+$/|min:0',
        'trial_days' => 'required|regex:/^[0-9]+$/|min:0',
        'payment_option' => 'required',
        'auto_renewal' => 'nullable',
        'free_num_users_months.*' => 'nullable',
        'free_num_months_users.*' => 'nullable',
        'charge_name.*' => 'sometimes|required',
        'charge_price.*' => 'sometimes|required',
        'price_type.*' => 'sometimes|required',
      ]);
      try{
        DB::beginTransaction();
        $subscription = new Subscription;
        $subscription->subscription_code = 'SC-'. uniqid() . '-' . now()->timestamp;
        $subscription->name = $request->name;
        $subscription->plan_id = $request->plan_id;
        $subscription->currency_id = $request->currency_id;
        $subscription->domain = $request->domain;
        $subscription->extension = $request->extension;
        $subscription->phone = $request->phone;
        $subscription->email = $request->email;
        $subscription->price = $request->price;
        $subscription->auto_renewal_time = $request->auto_renewal_time;
        $subscription->expiry_after_current_renewal = $request->has('auto_renewal') ? 1 : 0;
        $subscription->min_users = $request->min_users;
        $subscription->price_per_user = $request->price_per_user;
        $subscription->setup_fee = $request->setup_fee;
        $subscription->trial_days = $request->trial_days;
        $subscription->payment_option = $request->payment_option;

        $subscription->save();
        /** 
         * Free Feature 
        **/
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
        Mail::to($subscription->email)->send(new SubscriptionCreated($link));
        dd(new SubscriptionCreated($link));
        DB::commit();
        return redirect()->route('app.subscription.index')->with('success', 'Information has been Added');
      }catch(\Exception $e){
        Log::error(array("Subscription Save", $e->getMessage()));
        return redirect()->back()->withInput()->withErrors(['error' => "Some Error Occured. Please try again."]);
      }
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
