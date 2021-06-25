<?php

namespace App\Http\Controllers\Company\Admin;

use App\Client;
use App\Collection;
use App\Company;
use App\DayRemark;
use App\DayRemarkDetail;
use App\Employee;
use App\Expense;
use App\Http\Controllers\Controller;
use App\Leave;
use App\Manager;
use App\ModuleAttribute;
use App\Order;
use App\PartyType;
use App\PermissionCategory;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use session;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         // $this->middleware('permission:role-list');
         // $this->middleware('permission:role-create', ['only' => ['create','store']]);
         // $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         // $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

// To clear the permission cache
    public function index()
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        dd('done');
        die();
    }

// View the logged in company permission
    public function indexAllPermissions(){   
        // $dayremarkdetail = DayRemarkDetail::where('id',2339)->first();
        $company_id = config('settings.company_id');
        $permissions = DB::table('permissions')->where('permission_type','Company')->where('company_id',$company_id)->get();
        echo '<table>';
        echo '<thead><tr><th>id</th><th>name</th><th>Category Id</th></tr></thead><tbody>';
        foreach($permissions as $permission){
            echo '<tr>';
            echo '<td>'.$permission->id.'</td>';
            echo '<td>'.$permission->name.'</td>';
            echo '<td>'.$permission->permission_category_id.'</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        // app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        die();
    }

    // Populating Permissions for existing party types
    public function indexPopulatePermisiions()
    {
        $partyTypes = PartyType::all();
        foreach($partyTypes as $PartyType)
        {
            $permissionCategory                           = new PermissionCategory;
            $permissionCategory->company_id               = $PartyType->company_id;
            $permissionCategory->permission_model_id      = $PartyType->id;
            $permissionCategory->permission_model         = 'PartyType';
            $permissionCategory->permission_category_type = 'Company';
            $permissionCategory->name                     = str_replace(' ','_',$PartyType->name);
            $permissionCategory->display_name             = 'Party Type: '.$PartyType->name;
            $permissionCategory->indexing_priority        = 10;
            $permissionCategory->save();

            $this->addPermission($PartyType,$permissionCategory,'create');
            $this->addPermission($PartyType,$permissionCategory,'view');
            $this->addPermission($PartyType,$permissionCategory,'update');
            $this->addPermission($PartyType,$permissionCategory,'delete');
            $this->addPermission($PartyType,$permissionCategory,'status');

        }
        dd('partytype populated');
    }

    private function addPermission($partytype,$permissionCategory,$permissionTag)
    {
        $stringName                        = str_replace(' ','-',$partytype->name);
        $permission                        = new Permission;
        $permission->permission_category_id= $permissionCategory->id;
        $permission->company_id            = $partytype->company_id;
        $permission->name                  = $stringName.'-'.$permissionTag;
        $permission->guard_name            = 'web';
        $permission->permission_type       = 'Company';
        $permission->enabled               = 1;
        $permission->is_mobile             = 1;
        $permission->save();
    }

// Conver dayremark of old client to new dayremark
    public function indexPopulateDayremarkOldtoNew()
    {
        $dayremarks = DayRemark::all();
        foreach($dayremarks as $dayremark)
        {
            $subdayremarks = DayRemark::where('employee_id',$dayremark->employee_id)->where('remark_date',$dayremark->remark_date)->where('id','!=',$dayremark->id)->get();

            $countSubdayRemarks = count($subdayremarks); 

            $dayremarkdetail = new DayRemarkDetail;

            $dayremarkdetail->remark_id = $dayremark->id;
            $dayremarkdetail->company_id = $dayremark->company_id;
            $dayremarkdetail->remark_details = $dayremark->remarks;
            $dayremarkdetail->created_at = $dayremark->created_at;
            $dayremarkdetail->updated_at = $dayremark->updated_at;
            $dayremarkdetail->save();
            if($countSubdayRemarks>0){
                foreach($subdayremarks as $subdayremark){
                    $subdetail = new DayRemarkDetail;
                    $subdetail->remark_id = $dayremark->id;
                    $subdetail->company_id = $dayremark->company_id;
                    $subdetail->remark_details = $subdayremark->remarks;
                    $subdetail->created_at = $subdayremark->created_at;
                    $subdetail->updated_at = $subdayremark->updated_at;
                    $subdetail->save();
                    $subdayremark->delete();
                }
                $dayremark->remarks = $subdayremark->remarks;
                $dayremark->created_at = $subdayremark->created_at;
                $dayremark->updated_at = $subdayremark->updated_at;
                $dayremark->save();
            }
        }
        dd('restructed dayremarks completed');
    }

// Add expense date to old client
    public function indexPopulateExpenseDate(){
        $expenses = Expense::all();
        foreach($expenses as $expense){
            if($expense->created_at)
                $expense->expense_date =Carbon::parse($expense->created_at)->format('Y-m-d');
                $expense->save();
        }
        dd('expense date populated');
    }

    // public function indexPopulatedOwner()
    // {
    //     $managers = Manager::all();
    //     foreach($managers as $manager){
    //         $employee = Employee::where('user_id',$manager->user_id)->first();
    //         if($employee){
    //             $employee->is_owner=1;
    //             $employee->save();
    //         }
    //     }
    //     dd('populated is_owner');
    // }


    // public function indexCheckDiffOrderAndCollectionTotal($id){
    //     $today = Carbon::now();
    //     $ordersAmount = Order::where('client_id',1978)->orderBy('due_date','ASC')->where('due_date','<',$today->format('Y-m-d'))->sum('grand_total');
    //     $collectionAmount = Collection::where('client_id',1978)->sum('payment_received');
    //     $diffAmount = $ordersAmount-$collectionAmount;
    //     dd('OrderAmt '.$ordersAmount.'CollectionAmt '.$collectionAmount.' diffAmount='.$diffAmount);
    // }

    public function show($domain,$clientID){
        $orderIDs = [];
        $company_id = config('settings.company_id');
        $today = Carbon::now();
        $ClientCollectionsPaymentSum = Collection::where('company_id',$company_id)->where('client_id',$clientID)->where('payment_status','Cleared')->sum('payment_received');
        // $ordersTotalAmount = Order::where('company_id',$company_id)->where('due_date','<',$today->format('Y-m-d'))->where('client_id',$clientID)->orderBy('due_date','ASC')->sum('grand_total');

        $ordersTotalAmount = Order::select('orders.id','orders.company_id','due_date','orders.delivery_status_id','grand_total','module_attributes.id as module_attributes_id','module_attributes.order_amt_flag')
                    ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
                    ->where('orders.company_id',$company_id)
                    ->where('client_id',$clientID)
                    ->where('due_date','<',$today->format('Y-m-d'))
                    ->where('order_amt_flag',1)
                    ->orderBy('due_date','ASC')->sum('grand_total');

        // $orders = Order::select('id','order_no','due_date','grand_total')->where('company_id',$company_id)->where('due_date','<',$today->format('Y-m-d'))->where('client_id',$clientID)->orderBy('due_date','ASC')->get();
        // 
        $orders = Order::select('orders.id','orders.company_id','due_date','order_no','orders.delivery_status_id','grand_total','module_attributes.id as module_attributes_id','module_attributes.order_amt_flag')
                    ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
                    ->where('orders.company_id',$company_id)
                    ->where('due_date','<',$today->format('Y-m-d'))
                    ->where('client_id',$clientID)
                    ->where('order_amt_flag',1)
                    ->orderBy('due_date','ASC')->get();

        echo 'Orders total Amount= '.$ordersTotalAmount.'<br>'; 
        echo 'Collection total amount= '.$ClientCollectionsPaymentSum.'<br>';
        if($ordersTotalAmount>=$ClientCollectionsPaymentSum){
            $diff = $ordersTotalAmount-$ClientCollectionsPaymentSum;
            echo 'Order Amount is greater with amount = '.$diff.'<br>';
        }else{
            $diff = $ClientCollectionsPaymentSum-$ordersTotalAmount;
            echo 'Collection Amount is greater with amount = '.$diff.'<br>';
        }
        foreach($orders as $order){
            if($order->grand_total<=$ClientCollectionsPaymentSum){
                $ClientCollectionsPaymentSum = $ClientCollectionsPaymentSum - $order->grand_total;
                echo '<br> Order no = '.config('settings.order_prefix').$order->order_no.' Amount Paid= '.$order->grand_total.' Amount Remaining='.$ClientCollectionsPaymentSum;
            }elseif($order->grand_total>$ClientCollectionsPaymentSum && $ClientCollectionsPaymentSum!=0){
                echo '<br> Order no = '.config('settings.order_prefix').$order->order_no.' Order amount= '.$order->grand_total.' Amount Partially Paid= '.$ClientCollectionsPaymentSum.' Amount Remaining=0';
                $ClientCollectionsPaymentSum = 0;
            }else{
                echo '<br> Order no ='.config('settings.order_prefix').$order->order_no.' Not paid.. Amount Remaining='.$ClientCollectionsPaymentSum;
            }
        }
        die();
    }



    // public function indexPopulateDueDate()
    // {
    //     $orders = Order::whereNull('due_date')->get();
    //     foreach($orders as $order){
    //         $client = Client::where('id',$order->client_id)->first();
    //         if($client){
    //             $order->due_date = Carbon::parse($order->order_date)->addDays($client->credit_days)->format('Y-m-d');
    //             $order->save();
    //         }else{
    //             $order->delete();
    //         }
    //     }
    //     dd('populated due date done');
    // }

    // public function autoSetPaidUnpaid(){
    //     $clients = Client::all();
    //     foreach($clients as $client){
    //         $ClientOrders = Order::where('client_id',$client->id)->get();
    //         $ClientCollectionsPaymentSum = Collection::where('client_id',$client->id)->sum('payment_received');
    //         foreach($ClientOrders as $order){
    //             if($order->grand_total<=$ClientCollectionsPaymentSum){
    //                 $ClientCollectionsPaymentSum = $ClientCollectionsPaymentSum - $order->grand_total;
    //                 $order->status = "Paid";
    //                 $order->save();
    //             }elseif($order->grand_total>$ClientCollectionsPaymentSum && $ClientCollectionsPaymentSum!=0){
    //                 $ClientCollectionsPaymentSum = 0;
    //                 $order->status = "Partially Paid";
    //                 $order->save();
    //             }else{
    //                 $order->status = "Not Paid";
    //                 $order->save();
    //             }
    //         }
    //     }
    // }
    
// Add new permission to old client. Add new permission id to the list
    public function indexNewPermissions(Request $request)
    {
        // $managers = Manager::all();
        // foreach($managers as $manager){
        //     $employee = Employee::where('user_id',$manager->user_id)->first();
        //     if($employee){
        //         $employee->is_owner=1;
        //         $employee->save();
        //     }
        // }

        // dd('process ended');        
        // Begining of script for full access default setting set as 1 roles
        $permissions = [
            0=>"172",
            1=>"117",
            2=>"127",
            3=>"132",
            4=>"137",
            5=>"142",
            6=>"147",
            7=>"152",
            8=>"157",
            9=>"162",
            10=>"167",
            11=>"177",
            12=>"182",
            13=>"122",
            14=>"112",
            15=>"187"
        ];
        $companies = Company::all();
        foreach($companies as $company){
            $role = DB::table('roles')->where('company_id',$company->id)->where('name','Full Access')->first();
            if($role){
                foreach($permissions as $permission){
                    $roleHasPermission = DB::table('role_has_permissions')->where('role_id',$role->id)->where('permission_id',$permission)->first();
                    // Log::info('info', array("message"=>print_r($roleHasPermission,true)));
                    if($roleHasPermission){
                    }else{
                        // Log::info('info', array("role_id"=>print_r($role->id,true)));
                        DB::table('role_has_permissions')->insert(['permission_id'=>$permission,'role_id'=>$role->id]);
                    }
                }
            }
        }
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        // return redirect()->back();
        // $company_id = 162;
        // $employees = Employee::where('company_id',$company_id)->where('designation',90)->get();
        // foreach($employees as $employee){
        //     $employee->role = 234;
        //     $employee->save();
        //     $user = User::where('company_id',$company_id)->where('id',$employee->user_id)->first();
        //     $user->assignRole(234);
        // }
        // $employees = Employee::where('company_id',$company_id)->where('designation',89)->get();
        // foreach($employees as $employee){
        //     $employee->role = 233;
        //     $employee->save();
        //     $user = User::where('company_id',$company_id)->where('id',$employee->user_id)->first();
        //     $user->assignRole(233);
        // }
        dd('default permissions for company admin populated');
            // }
    }

    public function indexold(Request $request)
    {
        // app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        // dd('breaked the sections');
        //Starting the script for companies populate
        // $companies = Company::all();
        // $adminPermissions=[
        //     0 => "21",
        //     1 => "22",
        //     2 => "23",
        //     3 => "24",
        //     4 => "25",
        //     5 => "41",
        //     6 => "42",
        //     7 => "43",
        //     8 => "44",
        //     9 => "45",
        //     10 => "51",
        //     11 => "52",
        //     12 => "53",
        //     13 => "54",
        //     14 => "55",
        //     15 => "36",
        //     16 => "37",
        //     17 => "38",
        //     18 => "39",
        //     19 => "40",
        //     20 => "16",
        //     21 => "17",
        //     22 => "18",
        //     23 => "19",
        //     24 => "66",
        //     25 => "67",
        //     26 => "68",
        //     27 => "69",
        //     28 => "1",
        //     29 => "2",
        //     30 => "3",
        //     31 => "4",
        //     32 => "5",
        //     33 => "26",
        //     34 => "27",
        //     35 => "28",
        //     36 => "29",
        //     37 => "30",
        //     38 => "31",
        //     39 => "32",
        //     40 => "33",
        //     41 => "34",
        //     42 => "35",
        //     43 => "77",
        //     44 => "47",
        //     45 => "48",
        //     46 => "50",
        //     47 => "6",
        //     48 => "7",
        //     49 => "11",
        //     50 => "12",
        //     51 => "13",
        //     52 => "14",
        //     53 => "56",
        //     54 => "57",
        //     55 => "58",
        //     56 => "59",
        //     57 => "60",
        //     58 => "81",
        //     59 => "82",
        //     60 => "88",
        //     61 => "91",
        //     62 => "92",
        //     63 => "96",
        //     64 => "97",
        //     65 => "102",
        //     66 => "107",
        // ];

        // $salesmanPermission = [
        //     0 => "22",
        //     1 => "41",
        //     2 => "42",
        //     3 => "43",
        //     4 => "36",
        //     5 => "37",
        //     6 => "16",
        //     7 => "17",
        //     8 => "66",
        //     9 => "67",
        //     10 => "1",
        //     11 => "2",
        //     12 => "3",
        //     13 => "4",
        //     14 => "26",
        //     15 => "27",
        //     16 => "31",
        //     17 => "32",
        //     18 => "33",
        //     19 => "34",
        //     20 => "47",
        //     21 => "48",
        //     22 => "50",
        //     23 => "11",
        //     24 => "12",
        //     25 => "13",
        //     26 => "56",
        //     27 => "57",
        //     28 => "81",
        //     29 => "82",
        //     30 => "91",
        //     31 => "92",
        //     32 => "96",
        //     33 => "97",
        //     34 => "102",
        //     35 => "107"
        // ];

        // foreach($companies as $company)
        // {
        //     $roleAdmin = Role::where('company_id',$company->id)->where('name','Full Access')->first();
        //     if($roleAdmin){
        //         $adminEmployees = Employee::where('company_id',$company->id)->where('is_admin',1)->whereNull('role')->get(); 
        //         foreach ($adminEmployees as $employee) {
        //             $user = User::where('id',$employee->user_id)->first();
        //             if($user){

        //                 $employee->role = $roleAdmin->id;
        //                 $employee->save();
        //                 $user->assignRole($roleAdmin->id);    
        //             }
        //         }
        //         $roleAdmin->syncPermissions($adminPermissions);
        //     }else{
        //         $role = new Role();
        //         $role->company_id = $company->id;
        //         $role->name = "Full Access";
        //         $role->save();
        //         $role->syncPermissions($adminPermissions);

        //         $adminEmployees = Employee::where('company_id',$company->id)->where('is_admin',1)->whereNull('role')->get(); 
        //         foreach ($adminEmployees as $employee) {
        //             $user = User::where('id',$employee->user_id)->first();
        //             if($user){
        //                 $employee->role = $role->id;
        //                 $employee->save();
        //                 $user->assignRole($role->id);
        //             }
        //         }
        //     }

        //     $roleSalesman = Role::where('company_id',$company->id)->where('name','Limited Access')->first();
        //     if($roleSalesman){
        //         $salesmanEmployees = Employee::where('company_id',$company->id)->where('is_admin',0)->whereNull('role')->get(); 
        //         foreach ($salesmanEmployees as $employee) {
        //             $user = User::where('id',$employee->user_id)->first();
        //             if($user){
        //                 $employee->role = $roleSalesman->id;
        //                 $employee->save();
        //                 $user->assignRole($roleSalesman->id);
        //             }
        //         }
        //         $roleSalesman->syncPermissions($salesmanPermission);
        //     }else{
        //         $role = new Role();
        //         $role->company_id = $company->id;
        //         $role->name = "Limited Access";
        //         $role->save();
        //         $role->syncPermissions($salesmanPermission);

        //         $salesmanEmployees = Employee::where('company_id',$company->id)->where('is_admin',0)->whereNull('role')->get(); 
        //         foreach ($salesmanEmployees as $employee) {
        //             $user = User::where('id',$employee->user_id)->first();
        //             if($user){
        //                 $employee->role = $role->id;
        //                 $employee->save();
        //                 $user->assignRole($role->id);
        //             }
        //         }
        //     }
        // }

        //Populating delivery_status_id
        // app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        // $company_id = config('settings.company_id');
        // foreach($companies as $company){
        //     $orders = Order::where('company_id',$company->id)->get();
        //     foreach($orders as $order){
        //         $moduleAttribute = ModuleAttribute::where('company_id',$company->id)->where('title',$order->delivery_status)->first();
        //         if($moduleAttribute){
        //             $order->delivery_status_id = $moduleAttribute->id;
        //             $order->save();
        //         }else{
        //             $moduleAttribute = ModuleAttribute::where('company_id',$company->id)->where('title','Pending')->first();
        //             if($moduleAttribute){
        //                 $order->delivery_status_id = $moduleAttribute->id;
        //                 $order->save();
        //             }else{
        //                 $moduleAttribute = new ModuleAttribute;
        //                 $moduleAttribute->title = "Pending";
        //                 $moduleAttribute->color = "#29e025";
        //                 $moduleAttribute->order_amt_flag = 1;
        //                 $moduleAttribute->order_edit_flag = 1;
        //                 $moduleAttribute->order_delete_flag = 1;
        //                 $moduleAttribute->default = 1;
        //                 $moduleAttribute->module_id = 1;
        //                 $moduleAttribute->save();
        //                 $order->delivery_status_id = $moduleAttribute->id;
        //                 $order->save();                    
        //             }
        //         }
        //     }            
        // }

        //Assigning roles
        // $company_id = config('settings.company_id');
        // $users = User::select('users.id')->leftJoin('employees','users.id','employees.user_id')->where('users.company_id',$company_id)->where('employees.is_admin',0)->whereNull('employees.deleted_at')->get();
        // foreach($users as $user){
        //     $user->assignRole(2);
        // }
        // dd('Role Assigned');
        // $user = User::find(4);
        // $user->assignRole(1);
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $company_id = config('settings.company_id');
        $this->validate($request, [
            'name' => 'required',
        ]);
        $company_id = config('settings.company_id');
        $allowed_user_roles = config('settings.user_roles');
        $roleCount = Role::where('company_id', $company_id)->count();

        if($allowed_user_roles < $roleCount){
          return redirect()->route('company.admin.settingnew.userroles', ['domain' => domain()])->with('error','Only allowed to have '. $allowed_user_roles .' roles.');
        }
        $roleExists = Role::where('company_id',$company_id)->where('name',$request->name)->first();
        if($roleExists || $request->name=="Full Access"){
            return redirect()->back();
        }
        $role = new Role;
        $role->name = $request->name;
        $role->company_id = $company_id;
        $role->save();
        session()->flash('role',$role->id);
        // $role = Role::create(['name' => $request->name,'company_id'=>$company_id]);
        $role->syncPermissions($request->input('permission'));
        session()->flash('active', 'roles');
        return redirect()->route('company.admin.settingnew.userroles', ['domain' => domain()])->with('success','Role created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        return redirect()->back();
    }


    public function update($domain,Request $request, $id)
    {  
        // dd($request->all());
        $this->validate($request, [
            'name' => 'required',
        ]);
        $company_id = config('settings.company_id');
        session()->flash('active', 'roles');
        $role = Role::where('company_id',$company_id)->where('id',$id)->where('name','!=','Full Access')->first();
        if($role){
            $roleExists = Role::where('company_id',$company_id)->where('name',$request->name)->where('id','!=',$id)->first();
            if($roleExists){
                return redirect()->back();
            }
            $role->name = $request->name;
            $role->company_id = $company_id;
            $role->save();
        }else{
            return redirect()->back();
        }
        return redirect()->route('company.admin.settingnew', ['domain' => domain()])->with('success','Role updated successfully');
    }

    public function updatePermission($domain,Request $request){
        $company_id = config('settings.company_id');
        $role = Role::where('id',$request->role_id)->where('company_id',$company_id)->first();
        if($role){
            $role->syncPermissions($request->input('permission'));
            $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->where('role', $role->id)->whereNotNull('firebase_token')->pluck('firebase_token', 'id');
            // $permissions = Permission::where('is_mobile',1)->where(function($q)use($company_id){
            //     $q = $q->where('permission_type','Global')->orWhere('company_id',$company_id);
            // })->get();
            // foreach($permissions as $permission){
            //     if($permission->permission_type=='Company')
            //         $data['pt-'.$permission->name] = ($role->hasPermissionTo($permission->id))?'1':'0'; 
            //     else
            //         $data[$permission->name] = ($role->hasPermissionTo($permission->id))?'1':'0'; 
            // }

            if (!empty($fbIDs)) {
                $notificationData = array(
                    'company_id' => $company_id,
                    'title' => $role->name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'permissions' => null,
                );
                $sendingNotificationData = $notificationData;
                $sendingNotificationData['unix_timestamp'] = time(); //need to manage server/client 
                foreach($fbIDs as $eId=>$fbID){
                  $fbIDArray = array($fbID);
                  $sent = sendPushNotification_($fbIDArray, 27,'Roles Updated',$sendingNotificationData);
                }
            }
        }else{
            dd('no roles found');
            return redirect()->back();
        }
        session()->flash('success','Roles Updated Successfully');
        session()->put('active', 'roles');   
        session()->flash('role',$role->id);
        return redirect()->route('company.admin.settingnew.userroles', ['domain' => domain()])->with('success','Role updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($domain,$id)
    {
        $company_id = config('settings.company_id');
        $role = Role::where('company_id',$company_id)->where('id',$id)->first();
        if($role){
            $exists_users=DB::table('model_has_roles')->where('role_id',$id)->first(); 
            if($exists_users){
                session()->flash('active', 'roles');
                return redirect()->route('company.admin.settingnew.userroles', ['domain' => domain()])->with('alert','Role has users and can not be deleted');
            }else{
                $role->delete();                
                DB::table('role_has_permissions')->where('role_id',$id)->delete();
            }
        }
        session()->flash('active', 'roles');
        return redirect()->route('company.admin.settingnew.userroles', ['domain' => domain()])->with('success','Role deleted successfully');
    }
}