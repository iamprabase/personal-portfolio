<?php

namespace App\Http\Controllers\Company\Admin;

use DB;
use Log;
use Auth;
use Hash;
use Mail;
use View;
use Excel;
use Config;
use Storage;
use App\Bank;
use App\Beat;
use App\User;
use App\Client;
use App\Company;
use App\Holiday;
use App\TaxType;
use App\Employee;
use App\LeaveType;
use App\PartyType;
use Carbon\Carbon;
use App\CustomField;
use App\Designation;
use App\ExpenseType;
use App\BusinessType;
use App\ReturnReason;
use App\VisitPurpose;
use App\ClientSetting;
use App\ProductReturn;
use App\CollateralsFile;
use App\ModuleAttribute;
use App\CollateralsFolder;
use App\PermissionCategory;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\Query\Builder;
use Spatie\Permission\Models\Permission;

class ClientSettingController extends Controller
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
        if(!Auth::user()->can('settings-view') && !(Auth::user()->isCompanyOwner())){
            return redirect()->back();
        }

        $company_id = config('settings.company_id');
        $companyPlan = DB::table('company_plan')->where('company_id', $company_id)->first();
        $plan = DB::table('plans')->where('id', $companyPlan->plan_id)->first();
        $active_users = Employee::where('company_id', $company_id)->where("status", "=", "Active")->count();
        $inactive_users = Employee::where('company_id', $company_id)->where("status", "=", "Inactive")->count();
        // $archived_users = DB::table('employees')->where('company_id', $company_id)->where('status', "Archived" )->count();
        $leavetypes = LeaveType::where('company_id',$company_id)->orderBy('name','ASC')->get();
        $taxes = TaxType::where('company_id', $company_id)->get();

        $setting = ClientSetting::where('company_id', $company_id)->first();
        if ($setting && $setting->country) {
            $states = DB::table('states')->where('country_id', $setting->country)->pluck('id')->toArray();
            $cities = DB::table('cities')->whereIn('state_id', $states)->pluck('name', 'id')->toArray();
        } else {
            $cities = [];
            // $states = DB::table('states')->get();
            $states = array();
        }

        $zones_array = array();
        $timestamp = time();
        foreach (timezone_identifiers_list() as $key => $zone) {
            //date_default_timezone_set($zone);
            $timezonelist[$zone] = '(UTC/GMT ' . date('P', $timestamp) . ') ' . $zone;
        }

        $currencies = DB::table('currency')->get();
        $banks = Bank::where('company_id', $company_id)->orderBy('name','ASC')->get();
        $beats = Beat::where('company_id', $company_id)->get();
        $beatsPlanDetails = DB::table('beatplansdetails')->pluck('beat_id');
        $beatsArray[] = [];
        foreach($beatsPlanDetails as $beatPlan){
            $beatPlan = explode(",",$beatPlan);
            $beatsArray = array_merge($beatsArray,$beatPlan); 
        }
        $designations = Designation::where('company_id', $company_id)->orderBy('parent_id','ASC')->get();
        $partylist = Client::where('company_id', $company_id)->orderBy('company_name','ASC')->get();
        if($partylist->first()){
          if(!empty($cities)){
            $nonCountryCities = array_keys($cities);
          }else{
            $nonCountryCities = array();
          }
          $cityNotINCurrentCompany = Client::where('company_id', $company_id)->whereNotIn('city', $nonCountryCities)->whereNotNull('city')->pluck('city')->toArray();
          if(!empty($cityNotINCurrentCompany)){
            $getNonCitiesData = DB::table('cities')->whereIn('id', $cityNotINCurrentCompany)->pluck('name', 'id')->toArray();
            if(!empty($getNonCitiesData)){
              foreach($getNonCitiesData as $key=>$cityData){
                $cities[$key] = $cityData;
              } 
            }
          }
        }
        $employeelist = DB::table('employees')->where('company_id', $company_id)->where('status','Active')->whereNull('deleted_at')->get();

        // $moduleAttributes_added = ModuleAttribute::where('company_id', $company_id)
        // ->where('module_id',1)->where('default','0')->get();

        // $moduleAttributes_default = ModuleAttribute::where('default','1')->get();
        // $moduleAttributes = $moduleAttributes_added->merge($moduleAttributes_default)->sortBy('title');
        $moduleAttributes = ModuleAttribute::where('company_id', $company_id)
        ->where('module_id',1)->get();
        //Holdiay Section
        $holidays = Holiday::where('company_id', $company_id)->get();
        $data[] = "";
        foreach ($holidays as $holiday) {
            $end_date = Carbon::parse($holiday->end_date);
            $data['nextday_end'][$holiday->id] = $end_date->addDays(1);
        }
        //End of Holiday
        //
        //Client Settings New
        $companies = Company::orderBy('company_name', 'ASC')->get();
        $company = $companies->where('id', $company_id)->first();

        $companyCollateralFolders = CollateralsFolder::where('company_id', $company_id)->get();

        $company_id = config('settings.company_id');
        $returnreasons = ReturnReason::where('company_id', $company_id )->orderBy('id', 'ASC')->get();

        $existingReturnReasons = ProductReturn::join('return_details', 'return_details.return_id', 'returns.id')
                                ->where('returns.company_id',  $company_id)
                                ->distinct('return_details.reason')
                                ->pluck('return_details.reason')->toArray();
        $roles = Role::where('company_id',$company_id)->orderBy('name','ASC')->get();
        $permission_categories = PermissionCategory::where('company_id',$company_id)->orWhere('permission_category_type','GLobal')->orderBy('indexing_priority','ASC')->get();
        $business_types = BusinessType::where('company_id',$company_id)->get();
        $expense_types = ExpenseType::where('company_id',$company_id)->get();

        $expiry_text = ($company->end_date<date('Y-m-d'))?"Expired On": "Expiring On";
        $allpartypes = PartyType::where('company_id',$company_id)->get(); 
        $partytypes = $allpartypes->where('parent_id',0);
        $customFields = CustomField::where('company_id',$company_id)->orderBy('id','desc')->get();

        $visit_purposes = VisitPurpose::whereCompanyId($company_id)->with('client_visit')->orderBy('title','ASC')->get(['title', 'id'])->map(function($visit_purpose) {
          $visit_purpose->deleteable = $visit_purpose->client_visit->count()==0;
          return $visit_purpose;
        })->toArray();


        return view('company.settings.edit', compact('setting', 'timezonelist', 
        'states', 'cities', 'plan', 'active_users', 'inactive_users', 
        'taxes', 'currencies', 'banks', 'partylist', 'employeelist', 'data', 
        'moduleAttributes','holidays', 'companies', 'beats','designations','beatsArray','leavetypes', 'companyCollateralFolders','returnreasons', 'existingReturnReasons','roles','permission_categories','business_types','expense_types', 'company', 'expiry_text','partytypes','customFields', 'allpartypes', 'visit_purposes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
          

        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $customMessages = [
            'title.required' => 'The Title field is required.',
            'default_currency.required' => 'Currency Field is required',
            'time_zone.required' => 'Time Zone field is required.',
            'date_format' => 'Date Format Field is required.',
            'phone.required' => 'Phone number is required',
            'mobile.required' => 'Mobile number is required',
            'phone.digits_between' => 'Phone must be between 7 to 14 digits',
            'mobile.digits_between' => 'Mobile must be between 7 to 14 digits',
            'city' => 'City field is required',
            'state' => 'State field is required',
            'country' => 'Country field is required',
            'logo.mimes' => 'Upload correct file type.',
            'logo.max' => 'Your file is too large to upload.',
            'favicon.mimes' => 'Upload correct file type.',
            'favicon.max' => 'Your file is too large to upload.',
        ];


        $this->validate($request, [
            'phone' => 'required|digits_between:7,14',
            'mobile' => 'required|digits_between:7,14',
            // 'default_currency' => 'required',
            // 'time_zone' => 'required',
            // 'date_format' => 'required',
            // 'city' => 'required',
            // 'state' => 'required',
            // 'country' => 'required',
        ], $customMessages);

        $siteinfo = ClientSetting::findOrFail($request->id);

        if (!empty($request->get('title'))) {
            $siteinfo->title = $request->get('title');
        }
        // $siteinfo->email=$request->get('email');
        $siteinfo->phonecode = $request->get('phonecode');
        $siteinfo->phone = $request->get('phone');
        $siteinfo->ext_no = $request->get('ext_no');
        $siteinfo->mobile = $request->get('mobile');

        $siteinfo->default_currency = $request->get('default_currency');
        $siteinfo->currency_format = $request->get('currency_format');
        $siteinfo->currency_symbol = $request->get('currency_symbol');

        $siteinfo->time_zone = $request->get('time_zone');
        $siteinfo->date_format = $request->get('date_format');
        $siteinfo->date_type = $request->get('date_type');

        $siteinfo->opening_time = $request->get('opening_time');
        $siteinfo->closing_time = $request->get('closing_time');

        $siteinfo->invoice_prefix = $request->get('invoice_prefix');
        $siteinfo->order_prefix = $request->get('order_prefix');
        //$siteinfo->order_with_amt = $request->get('order_with_amt');

        // echo "<pre>";
        // print_r($siteinfo);
        // die;


        if (!empty($request->get('tax_name')) && !empty($request->get('tax_percent'))) {
            $tax_type = [];
            $names = $request->get('tax_name');
            $percents = $request->get('tax_percent');
            $i = 0;
            foreach ($names as $name) {
                if (!empty($name) && !empty($percents[$i])) {
                    $tax_type[] = [
                        'company_id' => $company_id,
                        'name' => $name,
                        'percent' => $percents[$i],
                    ];
                    $i++;
                }
            }
            DB::table('tax_types')->insert($tax_type);
        }


        // $siteinfo->invoice_text=$request->get('invoice_text');
        // $siteinfo->invoice_logo=$request->get('invoice_logo');

        $siteinfo->smtp_host = $request->get('smtp_host');
        $siteinfo->smtp_username = $request->get('smtp_username');

        if (!empty($request->get('smtp_password')))
            $siteinfo->smtp_password = $request->get('smtp_password');

        $siteinfo->smtp_port = $request->get('smtp_port');

        $siteinfo->invoice_mail_from = $request->get('invoice_mail_from');
        $siteinfo->recovery_mail_from = $request->get('recovery_mail_from');
        $siteinfo->other_mails_from = $request->get('other_mails_from');


        $siteinfo->login_title = $request->get('login_title');
        $siteinfo->login_description = $request->get('login_description');
        // $siteinfo->copyright_text=$request->get('copyright_text');

        // $siteinfo->location=$request->get('location');
        $siteinfo->address_1 = $request->get('address_1');
        $siteinfo->address_2 = $request->get('address_2');
        $siteinfo->city = $request->get('city');
        $siteinfo->state = $request->get('state');
        $siteinfo->country = $request->get('country');
        $siteinfo->zip = $request->get('zip');

        if ($request->file('logo')) {

            if (!empty($siteinfo->logo_path) && file_exists(base_path() . $siteinfo->logo_path)) {
            }

            $this->validate($request, [
                'logo' => 'mimes:jpeg,png,jpg|max:500'
            ], $customMessages);

            $image = $request->file('logo');
            $realname = pathinfo($request->file('logo')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $image->storeAs('public/uploads/' . $companyName . '/logo/', $new_name);
            $path = Storage::url('app/public/uploads/' . $companyName . '/logo/' . $new_name);
            $siteinfo->logo = $new_name;
            $siteinfo->logo_path = $path;
        }


        if ($request->file('small_logo')) {

            $this->validate($request, [
                'small_logo' => 'mimes:jpeg,png,jpg|max:300'
            ], $customMessages);

            $image = $request->file('small_logo');
            $realname = pathinfo($request->file('small_logo')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $image->storeAs('public/uploads/' . $companyName . '/logo/', $new_name);
            $path = Storage::url('app/public/uploads/' . $companyName . '/logo/' . $new_name);
            $siteinfo->small_logo = $new_name;
            $siteinfo->small_logo_path = $path;
        }


        if ($request->file('favicon')) {
            $this->validate($request, [
                'favicon' => 'mimes:jpeg,png,jpg,ico|max:100'
            ], $customMessages);
            $image2 = $request->file('favicon');
            $realname2 = pathinfo($request->file('favicon')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension2 = $image2->getClientOriginalExtension();
            $new_name2 = $realname2 . "-" . time() . '.' . $extension2;
            $image2->storeAs('public/uploads/' . $companyName . '/logo/', $new_name2);
            $path2 = Storage::url('app/public/uploads/' . $companyName . '/logo/' . $new_name2);
            $siteinfo->favicon = $new_name2;
            $siteinfo->favicon_path = $path2;
        }


        $siteinfo->save();
        return redirect()->route('company.admin.setting', ['domain' => domain()])->with('success', 'Information has been  Updates');
    }else{
    return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
       }
    }


    public function updateProfile(Request $request)
    {
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $customMessages = [
            'title.required' => 'The Title field is required.',
            'phone.required' => 'Phone number is required',
            'mobile.required' => 'Mobile number is required',
            'phone.digits_between' => 'Phone must be between 7 to 14 digits',
            'mobile.digits_between' => 'Mobile must be between 7 to 14 digits',
            'city' => 'City field is required',
            'state' => 'State field is required',
            'country' => 'Country field is required',
        ];

        $this->validate($request, [
            'phone' => 'required|digits_between:7,14',
            'mobile' => 'required|digits_between:7,14',
            // 'default_currency' => 'required',
            // 'time_zone' => 'required',
            // 'date_format' => 'required',
            // 'city' => 'required',
            // 'state' => 'required',
            // 'country' => 'required',
        ], $customMessages);

        $siteinfo = ClientSetting::findOrFail($request->id);

        if (!empty($request->get('title'))) {
            $siteinfo->title = $request->get('title');
        }
        // $siteinfo->email=$request->get('email');
        $siteinfo->phonecode = $request->get('phonecode');
        $siteinfo->phone = $request->get('phone');
        $siteinfo->ext_no = $request->get('ext_no');
        $siteinfo->mobile = $request->get('mobile');
        $siteinfo->opening_time = $request->get('opening_time');
        $siteinfo->closing_time = $request->get('closing_time');
        $siteinfo->address_1 = $request->get('address_1');
        $siteinfo->address_2 = $request->get('address_2');
        $siteinfo->city = $request->get('city');
        $siteinfo->state = $request->get('state');
        $siteinfo->country = $request->get('country');
        $siteinfo->zip = $request->get('zip');

        $siteinfo->save();
        $request->session()->flash('active', 'profile');
        return redirect()->route('company.admin.setting', ['domain' => domain()]);
    }

    public function updateLayout(Request $request)
    {
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;

        $siteinfo = ClientSetting::findOrFail($request->id);

        $siteinfo->login_title = $request->get('login_title');
        $siteinfo->login_description = $request->get('login_description');

        if ($request->file('logo')) {

            if (!empty($siteinfo->logo_path) && file_exists(base_path() . $siteinfo->logo_path)) {
            }

            $this->validate($request, [
                'logo' => 'mimes:jpeg,png,jpg|max:500'
            ], $customMessages);

            $image = $request->file('logo');
            $realname = pathinfo($request->file('logo')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $image->storeAs('public/uploads/' . $companyName . '/logo/', $new_name);
            $path = Storage::url('app/public/uploads/' . $companyName . '/logo/' . $new_name);
            $siteinfo->logo = $new_name;
            $siteinfo->logo_path = $path;
        }


        if ($request->file('small_logo')) {

            $this->validate($request, [
                'small_logo' => 'mimes:jpeg,png,jpg|max:300'
            ], $customMessages);

            $image = $request->file('small_logo');
            $realname = pathinfo($request->file('small_logo')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $image->storeAs('public/uploads/' . $companyName . '/logo/', $new_name);
            $path = Storage::url('app/public/uploads/' . $companyName . '/logo/' . $new_name);
            $siteinfo->small_logo = $new_name;
            $siteinfo->small_logo_path = $path;
        }


        if ($request->file('favicon')) {
            $this->validate($request, [
                'favicon' => 'mimes:jpeg,png,jpg,ico|max:100'
            ], $customMessages);
            $image2 = $request->file('favicon');
            $realname2 = pathinfo($request->file('favicon')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension2 = $image2->getClientOriginalExtension();
            $new_name2 = $realname2 . "-" . time() . '.' . $extension2;
            $image2->storeAs('public/uploads/' . $companyName . '/logo/', $new_name2);
            $path2 = Storage::url('app/public/uploads/' . $companyName . '/logo/' . $new_name2);
            $siteinfo->favicon = $new_name2;
            $siteinfo->favicon_path = $path2;
        }


        $siteinfo->save();
        $request->session()->flash('active', 'layout');
        return redirect()->route('company.admin.setting', ['domain' => domain()]);
    }

    public function updateEmail(Request $request)
    {
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;

        $siteinfo = ClientSetting::findOrFail($request->id);

        $siteinfo->smtp_host = $request->get('smtp_host');
        $siteinfo->smtp_username = $request->get('smtp_username');

        if (!empty($request->get('smtp_password')))
            $siteinfo->smtp_password = $request->get('smtp_password');

        $siteinfo->smtp_port = $request->get('smtp_port');

        $siteinfo->invoice_mail_from = $request->get('invoice_mail_from');
        $siteinfo->recovery_mail_from = $request->get('recovery_mail_from');
        $siteinfo->other_mails_from = $request->get('other_mails_from');

        $siteinfo->save();
        $request->session()->flash('active', 'email');
        return redirect()->route('company.admin.setting', ['domain' => domain()]);
    }

    public function updateOther(Request $request)
    {
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;

        $siteinfo = ClientSetting::findOrFail($request->id);

        $siteinfo->default_currency = $request->get('default_currency');
        $siteinfo->currency_format = $request->get('currency_format');
        $siteinfo->currency_symbol = $request->get('currency_symbol');

        $siteinfo->time_zone = $request->get('time_zone');
        $siteinfo->date_format = $request->get('date_format');
        $siteinfo->date_type = $request->get('date_type');

        $siteinfo->invoice_prefix = $request->get('invoice_prefix');
        $siteinfo->order_prefix = $request->get('order_prefix');

        // echo "<pre>";
        // print_r($siteinfo);
        // die;


        if (!empty($request->get('tax_name')) && !empty($request->get('tax_percent'))) {
            $tax_type = [];
            $names = $request->get('tax_name');
            $percents = $request->get('tax_percent');
            $i = 0;
            foreach ($names as $name) {
                if (!empty($name) && !empty($percents[$i])) {
                    $tax_type[] = [
                        'company_id' => $company_id,
                        'name' => $name,
                        'percent' => $percents[$i],
                    ];
                    $i++;
                }
            }
            DB::table('tax_types')->insert($tax_type);
        }

        $siteinfo->save();
        $request->session()->flash('active', 'other');
        return redirect()->route('company.admin.setting', ['domain' => domain()]);
    }

    public function getStates($id)
    {
        $states = DB::table("states")->where("country_id", $id)->pluck("name", "id");
        return json_encode($states);
    }

    public function getcities($id)
    {
        $cities = DB::table("cities")->where("state_id", $id)->pluck("name", "id");
        return json_encode($cities);
    }

    public function removeTax(Request $request)
    {
        DB::table('tax_types')->where('id', $request->tax_id)->delete();
        return "One Tax removed";
    }


    public function updatePassword()
    {
        $user = Auth::user();
        return view('company.settings.editProfile', compact('user'));
    }

    public function updatedPassword(Request $request)
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
                    'success' => 0,
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        "password" => "Current password doesn't match!"
                    ]
                ], 422);
        }
        $userRow->password = Hash::make($request->password);
        $userRow->save();
        $token = $request->password;
        $email = "support@deltatechnepal.com";
        $subject = "Password Reset";
        $company = Company::where('id',$userRow->company_id)->first();
        try{
            Mail::send('company.sendtocompany', ['user'=>$userRow,'company' => $company,'token'=>$token, 'domain' => domain(),], function ($message) use ($email, $subject) 
            {
                $message->from('support@deltatechnepal.com', 'Deltatech Nepal');
                $message->to($email);
                $message->bcc('dev@deltatechnepal.com');
                $message->subject($subject);
            });
        }catch(Exception $e){
            $mailMessage = $e->getMessage();
        }
        if(isset($mailmessage)){
            return response()->json(
            ['success' => 1,
                'message' => 'Password has been changed successfully but could not send mail currently due to'.$mailMessage,
                'url' => domain_route('company.admin.setting.updateuserprofile')
            ], 200);
        }else{
            return response()->json(
                ['success' => 1,
                    'message' => 'Password has been changed successfully.',
                    'url' => domain_route('company.admin.setting.updateuserprofile')
                ], 200);
        }
    }

    public function updatedProfile(Request $request)
    {
        $user = User::findOrFail($request->id);
        $request->validate([
            'name' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagenameWithExt = $request->file('image')->getClientOriginalName();
            $imagename = pathinfo($imagenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $imagenameToStore = $imagename . '_' . time() . '.' . $extension;
            $imagepath = $request->file('image')->storeAs('public/uploads/gsipl/profile_images', $imagenameToStore);
            $user->profile_image = $imagenameToStore;
            $user->profile_imagePath = $imagepath;
        }
        $user->name = $request->name;
        $user->save();
        // return redirect()->back();
        // if (!Hash::check($request->current_password, $userRow->password)) {
        //  return response()->json(
        //      [
        //      'success'=>0,
        //      'message' => 'The given data was invalid.',
        //      'errors' => [
        //          "password" =>"Current password doesn't match!"
        //       ]
        //      ],422);
        // }
        // $userRow->password = Hash::make($request->password);
        // $userRow->save();
        return response()->json(
            ['success' => 1,
                'message' => 'Profile has been updated successfully.',
                'url' => domain_route('company.admin.setting.updateuserprofile')
            ], 200);
    }

    public function generate_string($input, $strength = 16)
    {
        $input_length = strlen($input);
        $random_string = '';
        for ($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    public function generateotp(Request $request)
    {
        // echo"i am here";

        $type = 'csv';
        $empotps = array();
        $otp_name = $request->name;
        $email = $request->email;
        $no_character = 6;
        $company_id = config('settings.company_id');

        // $employees = Employee::where('company_id', $company_id)->get();
        $employees = $request->employeeId;

        // echo "<pre>";
        // print_r($employees);
        // die;
        // print_r($request->employeeId);
        //die;
        //$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $permitted_chars = '0123456789';
        foreach ($employees as $emp) {
            $otp = "";
            $otp = $this->generate_string($permitted_chars, $no_character);
            $employee = Employee::where('company_id', $company_id)->where('id', $emp)->first();
            if ($employee) {
                $employee->password = $otp;
                $empotps[] = array('name' => $employee->name, 'otp' => $otp);


                $saved = $employee->save();
            }
            // echo $emp;
            // echo "</br>";
        }
        // die;

        $data2 = json_decode(json_encode($empotps), true);
        $file = strtolower(str_replace(' ', '_', $otp_name));
        $filename = $file . '_' . time();
        Excel::create($filename, function ($excel) use ($data2) {
            $excel->sheet('mySheet', function ($sheet) use ($data2) {
                $sheet->fromArray($data2);
            });
        })->store($type, storage_path('otp/exports'));

        $filepath = 'http://' . $_SERVER['HTTP_HOST'] . '/cms/storage/otp/exports/' . $filename . '.csv';
        //die;
        $subject = 'Employees OTP';
        Mail::send('mails.otp_mailcontent', ['url' => '', 'company_name' => ''], function ($message) use ($email, $subject, $filepath) {
            $message->from('support@deltasalesapp.com', 'Deltatech Nepal');
            $message->to($email);
            //$message->bcc('bikash.deltatech@gmail.com');
            $message->subject($subject);
            $message->attach($filepath);
        });

        return redirect()->route('company.admin.setting', ['domain' => domain()]);

    }

    public function removeOTP(Request $request)
    {
        $company_id = config('settings.company_id');
        $employee = Employee::findOrFail($request->id);
        if (!empty($employee)) {
            $employee->password = NULL;
            $saved = $employee->save();
        }
        flash()->success('OTP has been removed.');
        return redirect()->route('company.admin.setting', ['domain' => domain()]);
    }

    public function addNewSettings(Request $request)
    {
        // dd($request->all());
        $customMessages = [
            'company_id' => "Select Valid CLient",
            'option' => "It is required.",
            'value' => "It is required.",
        ];

        $this->validate($request, [
            'company_id' => 'required',
            'option' => 'required',
            'value' => 'required',
        ], $customMessages);

        $ClientSettingsNew = ClientSettingsNew::Create($request->all());
        $data['result'] = "Successfully Added";
        return $data;

    }

    public function editNewSettings(Request $request)
    {
        // dd($request->all());
        $customMessages = [
            'settingId' => "Select Valid settingId",
            'company_id' => "Select Valid CLient",
            'option' => "It is required.",
            'value' => "It is required.",
        ];

        $this->validate($request, [
            'settingId' => 'required',
            'company_id' => 'required',
            'option' => 'required',
            'value' => 'required',
        ], $customMessages);
        $ClientSettingsNew = ClientSettingsNew::findOrFail($request->settingId);
        $row['company_id'] = $request->company_id;
        $row['option'] = $request->option;
        $row['value'] = $request->value;
        $ClientSettingsNew->Update($row);
        $data['result'] = "Successfully Updated";
        return $data;
    }

    public function deleteNewSettings(Request $request)
    {
        // dd($request->all());
        $customMessages = [
            'settingId' => "Select Valid settingId",
        ];

        $this->validate($request, [
            'settingId' => 'required',
        ], $customMessages);

        $ClientSettingsNew = ClientSettingsNew::findOrFail($request->settingId);
        $ClientSettingsNew->delete();
        $data['result'] = "Successfully Deleted";
        return $data;
    }

    public function getcounts(Request $request){
        
      $company_id=config('settings.company_id');
      $pendingId = ModuleAttribute::where('company_id', $company_id)->where('title', 'Pending')->first()->id; 
      if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
        $result = DB::select("SELECT (SELECT COUNT(*) FROM orders where company_id = $company_id and delivery_status_id = $pendingId and deleted_at IS NULL) as orderCount,
            (SELECT COUNT(*) FROM expenses where company_id =$company_id and status = 'Pending') as expenseCount, (SELECT COUNT(*) FROM leaves where company_id =$company_id and status = 'Pending') as leaveCount");
       return $result;
        }else{
            $empdetails=Auth::user()->isCompanyEmployee();
                $emp_id=$empdetails->id;
                $company_id=$empdetails->company_id;
                $chainusers=Auth::user()->getChainUsers($emp_id);
                array_push($chainusers, $emp_id);
                $visibleusers=implode(', ',$chainusers);
               //print_r($visibleusers);
               // die;
            $result = DB::select("SELECT (SELECT COUNT(*) FROM orders where company_id = $company_id AND employee_id IN ($visibleusers) AND delivery_status_id = $pendingId And deleted_at IS NULL) as orderCount ,
                (SELECT COUNT(*) FROM expenses where company_id =$company_id AND employee_id IN ($visibleusers) AND  status = 'Pending') as expenseCount , (SELECT COUNT(*) FROM leaves where company_id =$company_id AND employee_id IN ($visibleusers) AND status = 'Pending') as leaveCount ");
           return $result;
        }
        
    }

    public function getpartytype(Request $request){
        $company_id=config('settings.company_id');
        $partytypes = PartyType::select('id', 'name')->where('company_id', $company_id)->where('id', '!=', $request->myId)->orderBy('id', 'ASC')->get();
        foreach($partytypes as $partytype){
            $stringName = str_replace(' ','-',$partytype->name).'-view';
            $permission = Permission::where('company_id',$company_id)->where('name',$stringName)->first();
            if($permission){
                if(Auth::user()->hasPermissionTo($permission->id)){
                    $partytype->can = true;
                }else{
                    $partytype->can = false;
                }
            }else{
                $partytype->can = false;
            }
        }
        return $partytypes;
        // return PartyType::select('id', 'name')->where('company_id', $company_id)->orderBy('id', 'ASC')->get();
    }

    public function getpartytypenew($parentId = 0) { 
        $company_id=config('settings.company_id');
        $partytypes = PartyType::where('company_id', $company_id)->orderBy('id', 'ASC')->get()->toArray();
        $tree = $this->buildTree($partytypes);

        return $tree;
    } 

    protected function buildTree(array $elements, $parentId = 0) { 
        $branch = array(); 
        foreach ($elements as $element) { 
            if ($element['parent_id'] == $parentId) { 
                $children = $this->buildTree($elements, $element['id']); 
                if ($children) { 
                    $element['children'] = $children; 
                } $branch[] = $element; 
            } 
        } return $branch; 
    } 
         //

    public function updateLocAccuracy(Request $request){

        $ClientSetting = ClientSetting::find(config('settings.id'));
        
        if($ClientSetting != null){
          $ClientSetting->loc_fetch_interval = $request->accuracy_val;
          $ClientSetting->save();
          $fbIDs = DB::table('employees')->where(array(array('company_id', $ClientSetting->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
          $dataPayload = array("data_type" => "company_setting", "company_setting" => json_encode($ClientSetting), "action" => "update");
          $sent = sendPushNotification_($fbIDs, 12, null, $dataPayload);

          $data['result'] = true;            
        }else{
            $data['result'] = false;
        }
        return $data;
    }

    /*
        Collaterals Related Controllers
    */

    public function createCollateralsFolder(Request $request){
      $companyID = config('settings.company_id');
      $createdBy = Auth::user()->id;

      $customMessages = [
        'folderName.required' => 'Please specify name for folder.',
        'folderName.unique' => 'Folder name already exists.',
      ];

      $this->validate($request, [
          "folderName" => "required|unique:collateral_folders,folder_name,NULL,deleted_at,company_id,$companyID",
      ],$customMessages);

      $folderName = $request->folderName;
      $collateralInstance = new CollateralsFolder;
      $collateralInstance->company_id = $companyID;
      $collateralInstance->folder_name = $folderName;
      $collateralInstance->created_by = $createdBy;
      $savedCollaterals = $collateralInstance->save();
      if($savedCollaterals){
        $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "collateralFolder", "collateralFolder" => $collateralInstance, "action" => "add");
        $sent = sendPushNotification_($fbIDs, 23, null, $dataPayload);
      }
      $companyCollateralFolders = CollateralsFolder::where('company_id', $companyID)->get();
      return view('company.settings.collateralsPartial', compact('companyCollateralFolders'));
    }

    public function editCollateralsFolder(Request $request){
      $companyID = config('settings.company_id');
      $createdBy = Auth::user()->id;

      $customMessages = [
        'editFolderName.required' => 'Please specify name for folder.',
        'editFolderName.unique' => 'Cannot Rename folder. Folder name already exists.',
      ];

      $this->validate($request, [
        "editFolderName" => "required|unique:collateral_folders,folder_name,'$request->editFolderId',id,deleted_at,NULL,company_id,$companyID",
      ],$customMessages);

      $folderName = $request->editFolderName;
      $folderId = $request->editFolderId;
      $collateralInstance = CollateralsFolder::findOrFail($folderId);
      $collateralInstance->company_id = $companyID;
      $collateralInstance->folder_name = $folderName;
      $collateralInstance->created_by = $createdBy;
      $updatedCollaterals = $collateralInstance->update();
      if($updatedCollaterals){
        $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "collateralFolder", "collateralFolder" => $collateralInstance, "action" => "update");
        $sent = sendPushNotification_($fbIDs, 23, null, $dataPayload);
      }
      $companyCollateralFolders = CollateralsFolder::where('company_id', $companyID)->get();
      return view('company.settings.collateralsPartial', compact('companyCollateralFolders'));
    }

    public function deleteCollateralsFolder(Request $request){
      $companyID = config('settings.company_id');
      $createdBy = Auth::user()->id;
      $companyName = Auth::user()->companyName($companyID)->domain;

      $folderId = (int)$request->folderId;
      $collateralInstance = CollateralsFolder::findOrFail($folderId);
      $folderName = $collateralInstance->folder_name;
      $fileExistsFolder = CollateralsFile::where('collateral_folder_id', $folderId)->get();
      if($fileExistsFolder){
          $fileExistsFolderCount = $fileExistsFolder->count();
          if($fileExistsFolderCount>0){
              foreach($fileExistsFolder as $file){
                $file->delete();
              }
              $path = "public/uploads/sonatech/collaterals/".$folderName;
              Storage::deleteDirectory($path);
          }
      }
      $deletedCollaterals = $collateralInstance->delete();
      if($deletedCollaterals){
        $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "collateralFolder", "collateralFolder" => $collateralInstance, "action" => "delete");
        $sent = sendPushNotification_($fbIDs, 23, null, $dataPayload);
      }
      $companyCollateralFolders = CollateralsFolder::where('company_id', $companyID)->get();
      return view('company.settings.collateralsPartial', compact('companyCollateralFolders'));
    }

    public function viewCollateralsFolder(Request $request){
      $folder_id = $request->folder_id;
      $folder_name = "$request->folder_name";

      $companyCollateralFiles = CollateralsFile::where('collateral_folder_id', $request->folder_id)->get();
      
      return view('company.settings.collateralsFilePartial', compact('companyCollateralFiles','folder_name','folder_id'));
    }

    public function viewCollateralsFolderHome(){
      $company_id = config('settings.company_id');

      $companyCollateralFolders = CollateralsFolder::where('company_id', $company_id)->get();

      return view('company.settings.collaterals', compact('companyCollateralFolders'));
    } 

    public function uploadCollateralsFiles(Request $request){
      $uploadedFiles = $request->only('files');
      $folder_name = $request->folderName;
      $company_id = config('settings.company_id');
      $companyName = Auth::user()->companyName($company_id)->domain;
      $uploadedBy = Auth::user()->id;
      $folder_id = $request->folderId;
      $customMessages = [
        'files.required' => 'Please upload a file. ',
        'files.*.mimes' => 'File format not supported. The file :attribute must be a file of type: '.config('settings.uploadtypes').'. ',
        !'files.*' => "The file :attribute must be less than or equal to ". config('settings.file_upload_size')*1000 ." bytes.",
      ];
      $attributes = [];
      $lenofUploadedFiles = sizeof($uploadedFiles["files"]);
      $ind = 0;
      for($ind; $ind<$lenofUploadedFiles; $ind++){
        $attributes["files.".$ind] = "<strong>{$uploadedFiles['files'][$ind]->getClientOriginalName()}</strong>";
      }
      $this->validate($request, [
        'files'=>'required',
        'files.*' => 'mimes:'.config('settings.uploadtypes').'|max:'.config('settings.file_upload_size'),
      ], $customMessages, $attributes);

      $storedFiles = array();

      foreach($uploadedFiles as $files){
        foreach ($files as $file) {
          $fileSize = $file->getSize();
          $realname = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
          $extension = $file->getClientOriginalExtension();
          if($extension != "txt"){
            $new_name = $realname . "-" . time() . '.' . $extension;
          }else{
            $file_name_without_ext = explode('.', $file->getClientOriginalName())[0];
            $new_name = $file_name_without_ext . "-" . time();
          }
          $stored = $file->storeAs('public/uploads/' . $companyName . '//collaterals/'.$folder_name.'/', $new_name);
          $path = Storage::url('app/public/uploads/' . $companyName . '/collaterals/'.$folder_name.'/'.$new_name);
          if($stored){
            $collateralsFileInstance = new CollateralsFile;
            $collateralsFileInstance->collateral_folder_id = $folder_id;
            $collateralsFileInstance->uploaded_by = $uploadedBy;
            $collateralsFileInstance->server_path =  'http://' . $_SERVER['HTTP_HOST'] . '/cms';
            $collateralsFileInstance->file_path = $path;
            $collateralsFileInstance->file_name = $realname;
            $collateralsFileInstance->file_extension = $extension;
            $collateralsFileInstance->file_size = $fileSize;
            $saved = $collateralsFileInstance->save();
            if($saved)
              array_push($storedFiles, $collateralsFileInstance);
          }
        }
      }
      $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
      $dataPayload = array("data_type" => "collateralFile", "collateralFile" => $storedFiles, "action" => "add");
      $sent = sendPushNotification_($fbIDs, 24, null, $dataPayload);

      $companyCollateralFiles = CollateralsFile::where('collateral_folder_id', '=', (int)$folder_id)->get();

      $data = View::make('company.settings.collateralsFileView', compact('companyCollateralFiles'))->render();
      return response()->json(['message'   => 'File Uploaded Successfully', 'view'=>$data]);
    }  

    public function editCollateralsFiles(Request $request){
      $uploadedFiles = $request->only('files');
      $folder_id = (int)$request->folderId;
      $file_id = (int)$request->fileId;
      $folder_name = $request->folderName;
      $file_name = $request->fileName;
      $company_id = config('settings.company_id');
      $companyName = Auth::user()->companyName($company_id)->domain;
      $uploadedBy = Auth::user()->id;
      $storedFiles = array();
      $updatedName = false; $storedFile = false;
      $customMessages = [
        'files.*.mimes' => 'File format not supported. The file :attribute must be a file of type: '.config('settings.uploadtypes').'. ',
        !'files.*' => "The file :attribute must be less than or equal to ". config('settings.file_upload_size')*1000 ." bytes.",
      ];
      $this->validate($request, [
        'files' => 'mimes:'.config('settings.uploadtypes').'|max:'.config('settings.file_upload_size'),
      ], $customMessages);
      $collateralsFileInstance = CollateralsFile::findOrFail($file_id);
      if(isset($file_name) && $collateralsFileInstance->file_name != $file_name){
        $collateralsFileInstance->file_name = $file_name;
        $updatedName = $collateralsFileInstance->update();
      }
      
      
      if(!empty($uploadedFiles)){
        $deletePath = unlink('cms/'.$collateralsFileInstance->file_path);
        foreach($uploadedFiles as $file){
          $fileSize = $file->getSize();
          $realname = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
          $extension = $file->getClientOriginalExtension();
          if($extension != "txt"){
            $new_name = $realname . "-" . time() . '.' . $extension;
          }else{
            $file_name_without_ext = explode('.', $file->getClientOriginalName())[0];
            $new_name = $file_name_without_ext . "-" . time();
          }
          $storedFile = $file->storeAs('public/uploads/' . $companyName . '//collaterals/'.$folder_name.'/', $new_name);
          $path = Storage::url('app/public/uploads/' . $companyName . '/collaterals/'.$folder_name.'/'.$new_name);

          if($storedFile){
            $collateralsFileInstance->collateral_folder_id = $folder_id;
            $collateralsFileInstance->uploaded_by = $uploadedBy;
            $collateralsFileInstance->server_path =  'http://' . $_SERVER['HTTP_HOST'] . '/cms';
            $collateralsFileInstance->file_path = $path;
            if(!(isset($file_name))){
                $collateralsFileInstance->file_name = $realname;
            }
            $collateralsFileInstance->file_extension = $extension;
            $collateralsFileInstance->file_size = $fileSize;
            $updated = $collateralsFileInstance->update();
          }
        }
      }

      if($storedFile || $updatedName){
        array_push($storedFiles, $collateralsFileInstance);
        $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "collateralFile", "collateralFile" => $storedFiles, "action" => "update");
        $sent = sendPushNotification_($fbIDs, 24, null, $dataPayload);
      }
      $companyCollateralFiles = CollateralsFile::where('collateral_folder_id', '=', (int)$folder_id)->get();

      $data = View::make('company.settings.collateralsFileView', compact('companyCollateralFiles'))->render();
      return response()->json(['message'   => 'Updated Successfully', 'view'=>$data]);
    }

    public function deleteCollateralsFiles(Request $request){
      $company_id = config('settings.company_id');
      $folder_id = (int)$request->folderId;
      $file_id = (int)$request->fileId;
      $collateralsFileInstance = CollateralsFile::findOrFail($file_id);
      $collateralsFileInstance->delete(); 
      $companyCollateralFiles = CollateralsFile::where('collateral_folder_id', '=', (int)$folder_id)->get();
      $deletePath = unlink('cms/'.$collateralsFileInstance->file_path);
      if($deletePath){
        $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "collateralFile", "collateralFile" => $collateralsFileInstance, "action" => "delete");
        $sent = sendPushNotification_($fbIDs, 24, null, $dataPayload);
        $data = View::make('company.settings.collateralsFileView', compact('companyCollateralFiles'))->render();
        return response()->json(['message'   => 'Deleted Successfully.', 'view'=>$data]);
      }else{
        return response()->json(['errors'   => 'Cannot Delete.']);
      }
    }

    public function updateDateFormat(Request $request){
        $company_id = config('settings.company_id');
        $clientSetting = ClientSetting::where('company_id',$company_id)->first();
        if($clientSetting){
            $clientSetting->date_format = $request->dateFormat;
            $clientSetting->save();
            return response(['status'=>true,'message'=>'Successfully Updated']);
        }
        return response(['status'=>true,'message'=>'No ClientSetting Found']);
    }
}