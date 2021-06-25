<?php

namespace App\Http\Controllers\Admin;

use DB;
use Log;
use Mail;
use View;
use Excel;
use Storage;
use App\Plan;
use App\User;
use App\Client;
use App\Outlet;
use App\Company;
use App\Manager;
use App\TaxType;
use App\Employee;
use App\PartyType;
use App\MainModule;
use App\MarketArea;
use App\Designation;
use App\ReturnReason;
use App\ClientSetting;
use App\ModuleAttribute;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class OutletController extends Controller
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
        $outlets = Outlet::orderBy('created_at','DESC')
                    ->get();
        return view('admin.outlets.index', compact('outlets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unique_code =  time().substr(uniqid(),3,6);
        $countries = DB::table("countries")->pluck("name", "id")->toArray();
        return view('admin.outlets.create', compact('unique_code', 'countries'));
    }

    public function fetchData(Request $request){ 
      $columns = array(  'id',  'outlet_name', 'contact_person', 'phone', 'email', 'status', 'num_of_suppliers', 'company_plan', 'action');

      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');
      
      $search = $request->input('search.value');
      
      $totalData =  Outlet::orderBy('created_at','DESC')
                    ->get()->count();

      // $getOutlets= Outlet;
      
      $totalFiltered = $totalData; 
      $outlets =  Outlet::where(function($query) use ($search){
        if(isset($search)){
          $query->orWhere('outlets.outlet_name', 'LIKE', "%{$search}%");
          $query->orWhere('outlets.contact_person', 'LIKE', "%{$search}%");
          $query->orWhere('outlets.phone', 'LIKE', "%{$search}%");
          $query->orWhere('outlets.email', 'LIKE', "%{$search}%");
          $query->orWhere('outlets.status', 'LIKE', "%{$search}%");
        }
      })->orderBy($order,$dir)->offset($start)
                        ->limit($limit)->get();
      $data = array();
      if(!empty($outlets))
      {   
        $i = $start;
        foreach ($outlets as $outlet){
          $id = $outlet->id;

          $show = route('app.outlets.show',$outlet->id);
          $edit =  route('app.outlets.edit',$outlet->id);
          $delete = route('app.outlets.delete', $outlet->id);
          $upstsLink = route('app.oulets.updatestatus', [$outlet->id]);

          $show_action = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
          $edit_action = "<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
          $delete_action = "<a class='btn btn-danger btn-sm' data-mid='{$outlet->id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";


          $stsBox = "<a href='#' class='update-status-modal' data-value='{$outlet->status}' data-outlet_id='{$outlet->id}' data-status-type='status' data-action='{$upstsLink}'>";
          if($outlet->status=="Incomplete") $spanLabel = "<span class='label label-warning'>{$outlet->status}</span>";
          elseif($outlet->status=="Disabled") $spanLabel = "<span class='label label-danger'>{$outlet->status}</span>";
          elseif($outlet->status=="New" || $outlet->status=="Active") $spanLabel = "<span class='label label-success'>{$outlet->status}</span>";
       
          $nestedData['id'] = ++$i;
          $nestedData['outlet_name'] = $outlet->outlet_name;
          $nestedData['contact_person'] = $outlet->contact_person;
          $nestedData['phone'] = $outlet->phone;
          $nestedData['email'] = $outlet->email;
          $nestedData['status'] = $stsBox.$spanLabel."</a>";
          $nestedData['num_of_suppliers'] = $outlet->suppliers->count();
          $nestedData['action'] = $show_action.$edit_action.$delete_action;
            
          $data[] = $nestedData;
        }
      }

      $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data
        );

      return json_encode($json_data); 
    }

    /**
     * Store a newly created resource in storage.ssss
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customMessages = [
            'outlet_name.required' => 'Th0 Outlet Name field is required.',
            'unique_code.required' => 'The Secret Code field is required.',
            'c_password.same' => 'Password do not match.',
            'c_password.required' => 'Confirm Password field is required.',
        ];


        $this->validate($request, [
            'outlet_name' => 'required',
            'unique_code' => 'required',
            'contact_name' => 'required',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|digits_between:7,14',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
        ], $customMessages);


        $outlet = new Outlet();
        $outlet->outlet_name = $request->get('outlet_name');
        $outlet->unique_code = $request->get('unique_code');
        $outlet->phone = $request->get('contact_phone');
        $outlet->phone_ext = $request->get('extNo');
        $outlet->email = $request->get('contact_email');
        $outlet->password = bcrypt($request->get('password'));
        $outlet->contact_person = $request->get('contact_name');
        $outlet->address = $request->get('address');
        $outlet->latitude = $request->get('lat');
        $outlet->longitude = $request->get('long');
        $outlet->status = $request->get('status');
        $outlet->save();
        
        $request->session()->flash('success', 'Information has been  Added.');
        return redirect()->route('app.outlets');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $outlet = Outlet::find($id);
      $city = DB::table("cities")->whereId($outlet->city)->first();
      if($city){
        $city_name = $city->name;
        $city = $city->state_id; 
        $state = DB::table("states")->whereId($city)->first();
        if($state) $state = $state->name;
      }else{
        $state = 'NA';
        $city_name = 'NA';
      }

      $country = getCountryName($outlet->country);
      if($country){
        $country_name = $country->name;
      }else{
        $country_name = 'NA';
      }
      $connections = $outlet->suppliers->pluck('company_name')->toArray();
      $connected_suppliers = implode(',', $connections); 
      $count_connected_suppliers = sizeof($connections);
      if(isset($outlet->image)){
        $tempImageDir = $outlet->email;
        $tempImagePath = "/storage/app/public/outlets/" . $tempImageDir . "/" . $outlet->image;
        $image_path = isset($outlet->image)?'https://'.$_SERVER['HTTP_HOST'].'/cms'.$tempImagePath:"";
      } else{
        $image_path = "";
      }

      return view('admin.outlets.show', compact('outlet', 'state', 'country_name','city_name', 'connected_suppliers', 'count_connected_suppliers', "image_path"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $outlet = Outlet::find($id);
        $unique_code = $outlet->unique_code;
        $countries = DB::table("countries")->pluck("name", "id")->toArray();
        $states = DB::table("states")->whereCountryId($outlet->country)->pluck("name", "id")->toArray();
        $state_instance = DB::table("cities")->where('id', $outlet->city)->first();
        if($state_instance){
          $state = $state_instance->state_id;
        }else{
          $state = null;
        }
        $cities = DB::table("cities")->whereStateId($state)->pluck("name", "id")->toArray();
        return view('admin.outlets.edit', compact('outlet', 'unique_code', 'countries', 'states', 'state', 'cities'));
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
        if($request->has('update_secret_code')) {
          $unique_code =  time().substr(uniqid(),3,6);
          $request['unique_code'] = $unique_code;
        }
        $customMessages = [
            'outlet_name.required' => 'Th0 Outlet Name field is required.',
            'unique_code.required' => 'The Secret Code field is required.',
        ];


        $this->validate($request, [
            'outlet_name' => 'required',
            'contact_name' => 'required',
            'unique_code' => 'required',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|digits_between:7,14',
            'address' => 'required',
            // 'c_password' => 'same:password',
            'country' => 'required',
            'state' => 'required',
            'extNo' => 'required|integer',
            'city' => 'required'
        ], $customMessages);


        $outlet = Outlet::find($id);
        $outlet->outlet_name = $request->get('outlet_name');
        $outlet->contact_person = $request->get('contact_name');
        // $outlet->unique_code = $request->get('unique_code');
        $outlet->email = $request->get('contact_email');
        $outlet->country = $request->get('country');
        $outlet->city = $request->get('city');
        // $outlet->state = $request->get('state');
        $outlet->phone_ext = $request->get('extNo');
        $outlet->phone = $request->get('contact_phone');
        $outlet->address = $request->get('address');
        if($outlet->suppliers->count()==0) $outlet->status = "New";
        else $outlet->status = $request->get('status');
        if($request->has('update_secret_code')) {
          $outlet->unique_code = $unique_code;
        }
        // if(!empty($request->get('password'))) $outlet->password = bcrypt($request->get('password'));
        $outlet->latitude = $request->get('lat');
        $outlet->longitude = $request->get('lng');
        $outlet->save();

        $user_instance = Outlet::whereEmail($outlet->email)->first()->users;
        if($user_instance){
          $updated = $user_instance->update([
            'name'=> $outlet->outlet_name,
            'phone'=> $outlet->phone,
            'password'=> bcrypt($outlet->phone),
          ]);
        }
        
        $request->session()->flash('success', 'Information has been  Updated.');
        return redirect()->route('app.outlets');
    }

    public function updateStatus(Request $request){
      $outlet_id = $request->id;
      $outlet_instance = Outlet::findOrFail($outlet_id);
      $field_type = $request->field_type;
      $outlet_instance->$field_type = $request->chosen_status;

      $updated = $outlet_instance->update();

      if($updated){
       \Session::flash('success', 'Updated.');
      }else{
       \Session::flash('error', 'Failed.');
      }

       return redirect()->route('app.outlets');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    { 
        $outlet = Outlet::findOrFail($request->outlet_id);
        DB::beginTransaction();
        if($outlet->clients){
          foreach($outlet->clients as $client){
            $client->update([
              'outlet_id' => NULL
            ]);
            DB::table('company_outlet')->whereOutletId($request->outlet_id)->delete();
          }

          if($outlet->users){
            $outlet->users->delete();
          }
        }
        $outlet->update([
          'email'=> time().$outlet->email
        ]);
        $outlet->delete();
        DB::commit();
        $request->session()->flash('success', 'Outlet has been deleted.');
        return back();
    }

    private function getLatLng($address){
      $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&key=AIzaSyDR6v2elDctrDptLyvTjpTBEs6z7CLSfW8';
      $geocode = @file_get_contents($url);
      $json = json_decode($geocode);
      $status = $json->status;
      if($status=="OK")
      {
        return $json->results[0]->geometry->location;
      }
      else
      {
        return null;
      }
    }

    public function fetchPhoneState(Request $request){
      $country_instance = DB::table('countries')->whereId($request->countryId)->first();
      if($country_instance){
        $ext = $country_instance->phonecode;
        $states = DB::table('states')->whereCountryId($request->countryId)->pluck('name', 'id')->toArray();

        return response()->json([
          'phone_extension' => $ext,
          'states' => $states,
          'status' => 200
        ]);
      }
    }

    public function fetchCity(Request $request){
      $state_instance = DB::table('states')->whereId($request->stateId)->first();
      if($state_instance){
        $cities = DB::table('cities')->whereCountryId($request->stateId)->pluck('name', 'id')->toArray();

        return response()->json([
          'cities' => $cities,
          'status' => 200
        ]);
      }
    }
}
