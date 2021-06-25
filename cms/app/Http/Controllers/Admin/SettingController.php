<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use Storage;
use DB;

//use App\role;

class SettingController extends Controller
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
        $setting = Setting::find(1);
        $countries = DB::table('countries')->get();
        $states = DB::table('states')->get()->where('country_id', $setting->country);
        $cities = DB::table('cities')->get()->where('state_id', $setting->state);

        // var_dump($countries);
        // die;

        $zones_array = array();
        $timestamp = time();
        foreach (timezone_identifiers_list() as $key => $zone) {
           // date_default_timezone_set($zone);
            // $timezonelist[$key]['zone'] = $zone;
            // $timezonelist[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
            $timezonelist[$zone] = '(UTC/GMT ' . date('P', $timestamp) . ') ' . $zone;
        }
        // $timezonelist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        // $settings = Setting::all()->sortByDesc("created_at");;
        return view('admin.settings.edit', compact('setting', 'timezonelist', 'countries', 'states', 'cities'));
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
            'title.required' => 'The Title field is required.',
            'currency_format.required' => 'Currency Format Field is required',
            'time_zone.required' => 'Time Zone field is required.',
            'date_format' => 'Date Format Field is required.',
            'location' => 'Location field is required',
            'city' => 'City field is required',
            'state' => 'State field is required',
            'country' => 'Country field is required',
            'logo.mimes' => 'Upload correct file type.',
            'logo.max' => 'Your file is too large to upload.',
            'favicon.mimes' => 'Upload correct file type.',
            'favicon.max' => 'Your file is too large to upload.',
        ];


        $this->validate($request, [
            'title' => 'required',
            'email' => 'required|email',
            'phone' => 'required|digits_between:7,14',
            'mobile' => 'required|digits_between:7,14',
            //'currency' => 'required',
            //'currency_format' => 'required',
            'time_zone' => 'required',
            // 'date_format' => 'required',
            'location' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ], $customMessages);

        $siteinfo = Setting::findOrFail($id);

        $siteinfo->title = $request->get('title');
        $siteinfo->email = $request->get('email');

        $siteinfo->phone = $request->get('phone');
        $siteinfo->ext_no = $request->get('ext_no');
        $siteinfo->mobile = $request->get('mobile');

        $siteinfo->default_currency = $request->get('default_currency');
        $siteinfo->currency_format = $request->get('currency_format');
        $siteinfo->currency_symbol = $request->get('currency_symbol');

        $siteinfo->time_zone = $request->get('time_zone');
        $siteinfo->date_format = $request->get('date_format');
        $siteinfo->date_type = $request->get('date_type');


        $siteinfo->invoice_prefix = $request->get('invoice_prefix');
        $siteinfo->order_prefix = $request->get('order_prefix');
        // $siteinfo->invoice_text=$request->get('invoice_text'); 
        // $siteinfo->invoice_logo=$request->get('invoice_logo');

        $siteinfo->smtp_host = $request->get('smtp_host');
        $siteinfo->smtp_username = $request->get('smtp_username');
        $siteinfo->smtp_password = $request->get('smtp_password');
        $siteinfo->smtp_port = $request->get('smtp_port');

        $siteinfo->invoice_mail_from = $request->get('invoice_mail_from');
        $siteinfo->recovery_mail_from = $request->get('recovery_mail_from');
        $siteinfo->other_mails_from = $request->get('other_mails_from');


        $siteinfo->login_title = $request->get('login_title');
        $siteinfo->login_description = $request->get('login_description');
        $siteinfo->copyright_text = $request->get('copyright_text');

        $siteinfo->location = $request->get('location');
        $siteinfo->city = $request->get('city');
        $siteinfo->state = $request->get('state');
        $siteinfo->country = $request->get('country');
        $siteinfo->zip = $request->get('zip');

        if ($request->file('logo')) {

            $this->validate($request, [
                'logo' => 'mimes:jpeg,png,jpg|max:500'
            ], $customMessages);

            $image = $request->file('logo');
            $realname = pathinfo($request->file('logo')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $image->storeAs('public/uploads/', $new_name);
            $path = Storage::url('uploads/' . $new_name);
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
            $image->storeAs('public/uploads/', $new_name);
            $path = Storage::url('uploads/' . $new_name);
            $siteinfo->small_logo = $new_name;
            $siteinfo->small_logo_path = $path;
        }


        if ($request->file('favicon')) {
            $this->validate($request, [
                'favicon' => 'mimes:jpeg,png,jpg,ico|max:50'
            ], $customMessages);
            $image2 = $request->file('favicon');
            $realname2 = pathinfo($request->file('favicon')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension2 = $image2->getClientOriginalExtension();
            $new_name2 = $realname2 . "-" . time() . '.' . $extension2;
            $image2->storeAs('public/uploads/', $new_name2);
            $path2 = Storage::url('uploads/' . $new_name2);
            $siteinfo->favicon = $new_name2;
            $siteinfo->favicon_path = $path2;
        }


        $siteinfo->save();

        return redirect()->route('app.setting')->with('success', 'Information has been  Updated');
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

}
