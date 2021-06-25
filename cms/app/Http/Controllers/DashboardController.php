<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

    public function home()
    {
      $user = auth()->user();
        //return view('app.dashboard', ['user' => auth()->user()]);
        if (auth()->check() && auth()->user()->isAppAdmin()) {
            $companiesInstances = Company::get([ DB::raw('CAST(end_date AS DATE) as subs_end_date'), 'companies.*' ]);
            $active_count = $companiesInstances->where('is_active', 2)->where('subs_end_date', '>=', date('Y-m-d'))->count();
            $disabled_count = $companiesInstances->where('is_active', 1)->count();
            $expired_count = $companiesInstances->where('is_active', 0)->count();
            $extension_count = Company::where('companies.is_active', 2)->where(DB::raw('CAST(companies.end_date AS DATE)'), '<', date('Y-m-d'))->get()->count();
            $companies = array();
            $companies_count = $companiesInstances->count();
            $companies['Active'] = $active_count;
            $companies['Disabled'] = $disabled_count;
            $companies['Expired'] = $expired_count;
            $companies['Extension'] = $extension_count;

            
            $customer_count = $companiesInstances->where('customer_status', 'customer')->count();
            $customer_active_count = $companiesInstances->where('customer_status', 'customer')->where('is_active', 2)->where('subs_end_date', '>=', date('Y-m-d'))->count();
            $customer_disabled_count = $companiesInstances->where('customer_status', 'customer')->where('is_active', 1)->count();
            $customer_expired_count = $companiesInstances->where('customer_status', 'customer')->where('is_active', 0)->count();
            $customer_extension_count = Company::where('companies.is_active', 2)->where(DB::raw('CAST(companies.end_date AS DATE)'), '<', date('Y-m-d'))->where('customer_status', 'customer')->get()->count();
            
            $customers = array();
            $customers['Active'] = $customer_active_count;
            $customers['Disabled'] = $customer_disabled_count;
            $customers['Expired'] = $customer_expired_count;
            $customers['Extension'] = $customer_extension_count;

            $customer_trial_count = $companiesInstances->where('customer_status', 'trial')->count();
            $trial_active_count = $companiesInstances->where('customer_status', 'trial')->where('is_active', 2)->where('subs_end_date', '>=', date('Y-m-d'))->count();
            $trial_disabled_count = $companiesInstances->where('customer_status', 'trial')->where('is_active', 1)->count();
            $trial_expired_count = $companiesInstances->where('customer_status', 'trial')->where('is_active', 0)->count();
            $trial_extension_count = Company::where('companies.is_active', 2)->where(DB::raw('CAST(companies.end_date AS DATE)'), '<', date('Y-m-d'))->where('customer_status', 'trial')->get()->count();
            $trials = array();
            $trials['Active'] = $trial_active_count;
            $trials['Disabled'] = $trial_disabled_count;
            $trials['Expired'] = $trial_expired_count;
            $trials['Extension'] = $trial_extension_count;

            $label_type = ["warning", "primary", "default", "danger", "success"];

            return view('app.dashboard', compact('user', 'trials', 'customers', 'companies', 'label_type', 'customer_trial_count', 'customer_count', 'companies_count'));
        } else {
            auth()->logout();
            return redirect()->route('app.login');
        }
    }

    public function changePassword()
    {
        return view('admin.settings.password');
    }

    public function updatePassword(Request $request)
    {
        $userRow = Auth::user();
        $customMessages = [
            'current_password.required' => 'Current password is required!',
            'current_password.different' => 'Current password should not be same as new password!',
            'password.required' => 'New password is required!',
            'password.string' => 'New password must be string!',
            'password.min' => 'New password must be of minimum 8 characters!',
            'password.max' => 'New password must be less than 26 characters!',
            'password.confirmed' => "Confirm password doesn't match!",
            'password_confirmation.required' => 'Password Confirmation is required!'
        ];

        $this->validate($request, [
            'current_password' => 'required|different:password',
            'password' => 'required|string|min:8|max:25|confirmed',
            'password_confirmation' => 'required',
        ], $customMessages);
        if (!Hash::check($request->current_password, $userRow->password)) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Current password doesn't match!",
                ], 200);
        }
        $userRow->password = Hash::make($request->password);
        $userRow->save();
        return response()->json(['status'=>true,'message'=>'Password Updated',$userRow]);
    }
    
}
