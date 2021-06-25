<?php

use App\Order;
use App\Client;
use App\Company;
use App\Product;
use App\Location;
use App\PartyType;
use Carbon\Carbon;
use App\ClientVisit;
use App\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

if (!function_exists('company')) {
    /**
     * Get company by subdomain
     *
     * @param null $subdomain
     * @return \App\Company
     */

    function company($subdomain = null)
    {
        $subdomain = $subdomain ?: request()->route('subdomain');
        $company = \App\Company::where('domain', $subdomain)->first();
        if (!$company) {
            return new \App\Company;
        }
        return $company;
    }
}

if (!function_exists('domain')) {
    /**
     * Get current domain
     *
     * @param null $subdomain
     * @return \App\Company
     */

    function domain()
    {
        $subdomain = request()->route('subdomain');
        return $subdomain;
    }
}

if (!function_exists('domain_route')) {
    /**
     * Generate the URL to a named route.
     * This is a modified version of Laravel's route() function
     * Pass subdomain value automatically
     *
     * @param array|string $name
     * @param mixed $parameters
     * @param bool $absolute
     * @return string
     */
    function domain_route($name, $parameters = [], $absolute = true)
    {
        $parameters['subdomain'] = request()->route('subdomain');
        $tempUrl = app('url')->route($name, $parameters, $absolute);
        return $tempUrl;
    }
}

function getCompanyVerification_old($email)
{
    return (new App\Company())->select('is_verified')->where('contact_email', $email)->where('is_verified', 1)->first();

    //  $verify= App\Company::select('is_verified')->where('contact_email', $email)->where('is_verified', 1)->first();
    //  if($verify)
    //  return true;
    //  else
    //  return false;

}

function getCompanyVerification($username,$field)
{
    $company_id = App\Employee::where('email', $email)->value('company_id');
    if($company_id){
        return (new App\Company())->select('is_verified')->where('id', $company_id)->where('is_verified', 1)->first();
    }else{
        return (new App\Company())->select('is_verified')->where('contact_email', $email)->where('is_verified', 1)->first();
    }
}

function getCompanyEmail($id)
{
    return (new App\Company())->select('contact_email')->where('id', $id)->first();
}

function getBeatName($id){
    if($id == 0){
        $name = "Unspecified";
        return $name;
    }else{
        return (new App\Beat())->select('name')->where('id', $id)->value('name');
    }
}

function getColor($title, $cId = null){
    //$company_id = Auth::user()->isCompanyManager()->company_id;
    if(!isset($cId))
        $company_id = config('settings.company_id');
    else
        $company_id = $cId;

    $color = App\ModuleAttribute::where('company_id', $company_id)->where('title', $title)->first();

    return $color;
}

function getEmployee($id)
{
    return (new App\Employee())->select('id','name', 'status','superior')->where('id', $id)->withTrashed()->first();
}

function getActiveEmployee($id)
{
    return (new App\Employee())->select('name', 'status')->where('id', $id)->first();
}

function getEmployeeName($id)
{
    $name = App\Employee::where('id', $id)->value('name');
    return $name;
}

function getEmployeesByGroup($id)
{
    return (new App\Employee())->select('id')->where('employeegroup', $id)->get();
}

function getPartyTypes($id)
{
    return (new App\PartyType())->select('name', 'id')->where('company_id', $id)->orderBy('id', 'ASC')->get();
}


function getPartyTypeName($id)
{
    return (new App\PartyType())->select('name', 'id')->where('id', $id)->orderBy('id', 'ASC')->first();
}

function getPartyTypesCount($id)
{
    return (new App\PartyType())->select('name', 'id')->where('company_id', $id)->count();
}

function getPartyWithoutPartyTypes()
{
    $company_id = $company_id = config('settings.company_id');
    return (new App\Client())->where('company_id', $company_id)->where('client_type', 0)->count();
}

function getAnnouncedEmployees($id)
{
    //$company_id = Auth::user()->isCompanyManager()->company_id;
    $company_id = $company_id = config('settings.company_id');
    $employees = DB::table('announce_employee')->select('employee_id')->where('company_id', $company_id)->where('announcement_id', $id)->get();
    return $employees;
}

function getCompany($id)
{
    return (new App\User())->select('name')->where('id', $id)->first();
}

function getBankName($id)
{
    return (new App\Bank())->select('name')->where('id', $id)->first();
}

function getPartyType($id)
{
    return (new App\PartyType())->select('name')->where('id', $id)->first();
}

function getEmployeePic($id)
{
    $employee = App\Employee::select('*')->where('id', $id)->withTrashed()->first();

    if (!empty($employee->image_path)) {

        $empPicPath = 'cms' . $employee->image_path;
    } else {

        if ($employee['gender'] == 'Male') {
            $empPicPath = 'cms/storage/app/public/uploads/default_m.png';
        } else {
            $empPicPath = 'cms/storage/app/public/uploads/default_f.png';
        }

    }
    return $empPicPath;
}


function getClient($id)
{
    return (new App\Client())->select('*')->where('id', $id)->first();
}

function getOrderNo($id)
{
    $prevorderno = DB::table('orders')->select('order_no')->where('company_id', $id)->orderBy('id', 'desc')->first();


    if (empty($prevorderno)) {

        $defaultorderno = App\ClientSetting::select('order_no_start')->where('company_id', $id)->first();
        $orderno = $defaultorderno['order_no_start'] + 1;
    } else {

        $orderno = $prevorderno->order_no + 1;
    }

    return $orderno;
}

function getEmployeeGroup($id)
{
    return (new App\EmployeeGroup())->select('*')->where('id', $id)->withTrashed()->first();
}

function getEmployeeDesignation($id)
{
    return (new App\Designation())->select('*')->where('id', $id)->first();
}

function getUser($id)
{
    return (new App\User())->select('*')->where('id', $id)->first();
}

function getSetting()
{
    return (new App\Setting())->where('id', '=', 1)->first();
}

function getTaxes($id)
{
    return DB::table('tax_types')->select('name', 'percent')->where('company_id', $id)->get();
}

function getTaxesOnOrders($id)
{
    return DB::table('tax_on_orders')->select('tax_name', 'tax_percent')->where('order_id', $id)->get();
}

function getTaxCount($id)
{
    return DB::table('tax_types')->where('company_id', $id)->count();
}

function getClientSetting()
{
    //return (new App\ClientSetting())->where('id',$id)->first();
    $parsedUrl = $_SERVER['HTTP_HOST'];
    $host = explode('.', $parsedUrl);
    $subdomain = $host[0];
    //$subdomain = $subdomain ?: request()->route('subdomain');
    $company = \App\Company::where('domain', $subdomain)->first();
    if (!$company) {
        return new \App\Company;
        //abort(404);
    }

    $clientsettings = \App\ClientSetting::where('company_id', $company->id)->first();

    if (!$clientsettings) {
        $clientsettings = $company;

    }
    return $clientsettings;
    //return (new App\ClientSetting())->where('company_id',$company->id)->first();
}

function getOrderByproduct($id)
{
    $orders = DB::table('orderproducts')->select('product_id')->where('product_id', $id)->first();

    if (!empty($orders))
        return false;
    else
        return true;
}

function hasChild_old($id)
{
    $client = DB::table('clients')->where('superior', $id)->whereNull('deleted_at')->get();

    if (count($client) > 0) {
        return true;
    } else {
        return false;
    }
}

function checkChildPartyType($id){
    $company_id = config('settings.company_id');
    $client = Client::findOrFail($id);
    $partyType = $client->client_type;

    if(isset($partyType)){
        $findChild = DB::table('partytypes')->where('company_id', $company_id)->where('parent_id', $partyType)->count();
        if($findChild>0) return true;
        else return false;
    }else{
        return false;
    }
}

function hasChild($id)
{
    $company_id = config('settings.company_id');
    $client = DB::table('clients')->where('superior', $id)->whereNull('deleted_at')->pluck('id')->toArray();
    $handles = array();
    if(!empty($client)){
        if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
            $handles = DB::table('handles')
                ->where('company_id',$company_id)
                ->whereIn('client_id',$client)
                ->pluck('client_id')
                ->toArray();
        }else{
            $handles = DB::table('handles')
                ->where('company_id',$company_id)
                ->where('employee_id',Auth::user()->EmployeeId())
                ->whereIn('client_id',$client)
                ->pluck('client_id')
                ->toArray();
        }
    }

    if (!empty($handles) && count($handles) > 0) {
        return true;
    } else {
        return false;
    }
}


function hasClientChild($id)
{
    $company_id = config('settings.company_id');
    $client = DB::table('clients')->where('superior', $id)->whereNull('deleted_at')->get();

    if ($client->first()) {
        return true;
    } else {
        return false;
    }
}



function getEmpByGroup($id)
{
    $empGroup = DB::table('employees')->select('employeegroup')->where('employeegroup', $id)->first();

    if (!empty($empGroup))
        return false;
    else
        return true;
}

function checkUnitToDelete($id)
{

    $defaultUnit = DB::table('unit_types')->select('id')->where('id', $id)->where('company_id', 0)->first();

    if (!empty($defaultUnit)) {

        return false;

    } else {

        $prodUnit = DB::table('products')->select('unit')->where('unit', $id)->first();

        $variantUnit = DB::table('product_variants')->select('unit')->where('unit', $id)->first();

        $prodOrderUnit = DB::table('orderproducts')->select('unit')->where('unit', $id)->first();

        if (!empty($prodUnit) || !empty($prodOrderUnit))
            return false;
        else
            return true;
    }
}

function checkBrandToDelete($id)
{

    $defaultBrand = DB::table('brands')->select('id')->where('id', $id)->where('company_id', 0)->first();

    if (!empty($defaultBrand)) {

        return false;

    } else {

        $prodBrand = DB::table('products')->select('brand')->where('brand', $id)->first();

        $prodOrderBrand = DB::table('orderproducts')->select('brand')->where('brand', $id)->first();

        if (!empty($prodBrand) || !empty($prodOrderBrand))
            return false;
        else
            return true;
    }
}

function getEmpActivity($id)
{

    $empOrder = DB::table('orders')->select('employee_id')->where('employee_id', $id)->first();
    $empCollection = DB::table('collections')->select('employee_id')->where('employee_id', $id)->first();
    $empAttendance = DB::table('attendances')->select('employee_id')->where('employee_id', $id)->first();

    $empExpenses = DB::table('expenses')->select('employee_id')->where('employee_id', $id)->first();
    $empHandles = DB::table('handles')->select('employee_id')->where('employee_id', $id)->first();
    $empLeaves = DB::table('leaves')->select('employee_id')->where('employee_id', $id)->first();

    $empMeetings = DB::table('meetings')->select('employee_id')->where('employee_id', $id)->first();
    // $empTasks = DB::table('tasks')->select('*')->where('assigned_from', $id)->orWhere('assigned_to', $id)->first();
    $empActivity = DB::table('activities')->select('created_by')->where('created_by',$id)->orWhere('assigned_to', $id)->first();

    $empSuperior = DB::table('employees')->select('superior')->where('superior',$id)->first();
    $empVisits = ClientVisit::select('client_id')->where('client_id',$id)->first();

    if (!empty($empOrder) || !empty($empCollection) || !empty($empAttendance) || !empty($empExpenses) || !empty($empLeaves) || !empty($empMeetings) || !empty($empActivity) || !empty($empSuperior) || !empty($empHandles) || !empty($empVisits))
        return false;
    else
        return true;
}

function getPartyActivity($id)
{
    $empOrder = DB::table('orders')->select('client_id', 'order_to')->where(function($query) use($id){
      $query->orWhere('client_id', $id);
      $query->orWhere('order_to', $id);
    })->whereNULL('deleted_at')->first();
    $empCollection = DB::table('collections')->select('client_id')->where('client_id', $id)->whereNULL('deleted_at')->first();
    $empExpense = DB::table('expenses')->select('client_id')->where('client_id', $id)->whereNULL('deleted_at')->first();
    // $empTask = DB::table('tasks')->select('client_id')->where('client_id', $id)->whereNULL('deleted_at')->first();
    $empMeeting = DB::table('meetings')->select('client_id')->where('client_id', $id)->whereNULL('deleted_at')->first();
    $empActivity = DB::table('activities')->select('client_id')->where('client_id',$id)->whereNULL('deleted_at')->first();
    $empVisits = ClientVisit::select('client_id')->where('client_id',$id)->first();
    if($id == 7977){
      Log::info(array("Order", $empOrder));
      Log::info(array("Collection", $empCollection));
      Log::info(array("Expense", $empExpense));
      Log::info(array("Meeting", $empMeeting));
      Log::info(array("Activity", $empActivity));
      Log::info(array("Visit", $empVisits));
    }
    if (!empty($empOrder) || !empty($empCollection) || !empty($empExpense) || !empty($empMeeting) || !empty($empActivity) || !empty($empVisits))
        return false;
    else
        return true;
}

function getCategoryByproduct($id)
{
    $category = DB::table('products')->select('category_id')->where('category_id', $id)->first();

    if (!empty($category))
        return false;
    else
        return true;
}

function getCategoryStatus($id)
{
    $category = DB::table('categories')->select('category_id')->where('category_id', $id)->first();

    if (!empty($category))
        return false;
    else
        return true;
}

function getCategory($id)
{
    return (new App\Category())->select('*')->where('id', $id)->first();
}

function getLeaveType($id)
{
    return (new App\LeaveType())->select('*')->where('id', $id)->withTrashed()->first();
}

function getProductName($id)
{
    return (new App\Product())->select('*')->where('id', $id)->first();
}

function getOrderDetails($id)
{
    return (new App\OrderDetails())->where('product_variant_id', $id)->count();
}

function getproductOrderDetails($id)
{
    return (new App\OrderDetails())->where('product_id', $id)->count();
}

function getShortName($str, $length)
{
    $str = substr($str, 0, $length) . '...';
    return $str;
}

function imageExist($id)
{
    $images = DB::table('images')->where('type_id', $id)->first();
    if (!empty($images))
        return true;
    else
        return false;
}

function getNewOrderCount($id)
{
    $neworders = DB::table('orders')->where('company_id', $id)->where('delivery_status', 'New')->count();
    return $neworders;
}

function getPendingExpenseCount($id)
{
    $pendingexpenses = DB::table('expenses')->where('company_id', $id)->where('status', 'Pending')->count();
    return $pendingexpenses;
}

function getInProgessTaskCount($id)
{
    $inprogresstasks = DB::table('tasks')->where('company_id', $id)->where('status', 'In Progress')->count();
    return $inprogresstasks;
}

function getPendingLeaveCount($id)
{
    $pendingleaves = DB::table('leaves')->where('company_id', $id)->where('status', 'Pending')->count();
    return $pendingleaves;
}

function getDays($startdate, $enddate)
{
    $to = \Carbon\Carbon::createFromFormat('Y-m-d', $enddate);
    $from = \Carbon\Carbon::createFromFormat('Y-m-d', $startdate);
    return $diff_in_days = $to->diffInDays($from) + 1;
}

function getCurrencyInfo($code)
{
    return DB::table('currency')->where('code', $code)->first();
}

function getUnitName($id)
{
    // $unit = DB::table('unit_types')->where('id', $id)->first();
    // if ($unit)
    //     return $unit->symbol;
    //$company_id = Auth::user()->isCompanyManager()->company_id;
    $company_id = $company_id = config('settings.company_id');
    $unit = DB::table('unit_types')->where('company_id', $company_id)->where('id', $id)->first();
    if ($unit)
        return $unit->symbol;
    else
        return NULL;
}

function getBrandName($id)
{
    // $brand = DB::table('brands')->where('id', $id)->first();
    // if ($brand)
    //     return $brand->name;
    // $company_id = Auth::user()->isCompanyManager()->company_id;
    $company_id = $company_id = config('settings.company_id');
    $brand = DB::table('brands')->where('company_id', $company_id)->where('id', $id)->first();
    if ($brand)
        return $brand->name;
    else
        return NULL;
}


function getCurrency()
{
    //$company_id = Auth::user()->isCompanyManager()->company_id;
    $company_id = $company_id = config('settings.company_id');
    $currency = DB::table('client_settings')->select('currency_symbol')->where('company_id', $company_id)->first();
    return $currency->currency_symbol;
}

function getCountryName($id)
{
    $country = DB::table("countries")->where("id", $id)->first();
    return $country;
}

function getStateName($id)
{
    $state = DB::table("states")->where("id", $id)->first();
    return $state;
}

function getCityName($id)
{
    $city = DB::table("cities")->where("id", $id)->first();
    if($city){
        return $city->name;
    }else{
        return null;
    }
}

function getClientAddress($id)
{

    $client = App\Client::select('*')->where('id', $id)->first();

    $country = DB::table("countries")->where("id", $client->country)->first();
    $state = DB::table("states")->where("id", $client->state)->first();
    $city = DB::table("cities")->where("id", $client->city)->first();

    $cname = $country->name;
    $sname = $state->name;
    $ciname = $city->name;
    $address = $ciname . $sname . $cname;
    return $address;

}

function getSource($id)
{
    return (new App\Leadssources())->where('id', $id)->first();
}

function getAmtWord(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            if (!$decimal)
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            else
                $hundred = ($counter == 1 && $str[0]) ? ' ' : null;
            $str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal) ? ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees ' : '') . ' and ' . $paise;
}


function getCollections($date, $eid)
{

    return (new App\Collection())->where('employee_id', $eid)
        ->where('payment_date', '>=', $date)
        ->where('payment_date', '<=', $date)
        ->sum('payment_received');
}

function getCompanyPlan($id)
{

    $companyPlan = DB::table("company_plan")->where("company_id", $id)->first();
    $planDetails = DB::table("plans")->where("id", $companyPlan->plan_id)->first();
    return $planDetails;

}

function getAllcheckin_out($eid, $adate)
{
    return (new App\Attendance())->where('employee_id', $eid)
        ->where('adate', '>=', $adate)
        ->orderBy('atime', 'asc')
        ->get();
}

function getOrders($date, $eid)
{

    return (new App\Order())->where('employee_id', $eid)
        ->where('order_date', '>=', $date)
        ->where('order_date', '<=', $date)
        ->sum('tot_amount');
}


function getWorkedHour($empid, $curr_date)
{
    $attendances = \App\Attendance::where('employee_id', $empid)
        ->where('check_datetime', '>=', $curr_date . ' 00:00:00')
        ->where('check_datetime', '<=', $curr_date . ' 23:59:59')
        ->orderBy('check_datetime', 'asc')
        ->get()->toArray();

    $seconds = $minutes = $hours = 0;
    $checkin = $workedhours = '';

    if (!empty($attendances)) {
        if ($attendances[0]['check_type'] == 2) {
            array_splice($attendances, 0, 1);
        }
        $allrec = count($attendances);
        //     if( $allrec>1){
        //     if($attendances[$allrec-1]['check_type']==1){
        //       array_pop($attendances);
        //     }
        // }
    }

    // echo "<pre>";
    // print_r($attendances);
    $allrec1 = count($attendances);

    //echo $allrec1;

    if (!empty($attendances) && $allrec >= 2) {
        $j = 0;
        for ($i = 0; $i < ($allrec1); $i++) {

            //  echo $i."--".++$i;
            //  echo "</br>";
            //$i++;
            $j = $i + 1;
            $starttimestamp = strtotime($attendances[$i]['check_datetime']);
            // echo "--";
            if (!isset($attendances[$j]['check_datetime'])) {
                $endtimestamp = strtotime($attendances[$i]['check_datetime']);
            } else {
                $endtimestamp = strtotime($attendances[$j]['check_datetime']);
            }
            // echo "</br>";
            $i = $j;
            // die;
            $seconds += abs($endtimestamp - $starttimestamp);
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;
    }
    return $hours . ' hr ' . $minutes . ' min';

}

function getOrdersCount($p_id, $start_date, $end_date){
    $get_count = Auth::user()->handleQuery('order')->join('orderproducts', 'orderproducts.order_id', 'orders.id')
        ->whereBetween('orders.order_date', [$start_date, $end_date])
        ->where('orderproducts.product_id', $p_id)
        ->where('orderproducts.product_variant_id', null)
        ->get();
    return $get_count;
}

function getOriginalUnit($orderdetailProductId, $orderdetailProductVariantId){
    if(isset($orderdetailProductVariantId)){
        $unit = ProductVariant::where('id', $orderdetailProductVariantId)->where('product_id', $orderdetailProductId)->first();
        if($unit)
            return $unit->unit;
        else
            return 0;
    }else{
        $unit = Product::where('id', $orderdetailProductId)->first();
        if($unit)
            return $unit->unit;
        else
            return 0;
    }
}

function getOriginalMrp($orderdetailProductId, $orderdetailProductVariantId){
    if(isset($orderdetailProductVariantId)){
        $mrp = ProductVariant::where('id', $orderdetailProductVariantId)->where('product_id', $orderdetailProductId)->first();
        if($mrp)
            return $mrp->mrp;
        else
            return 0;
    }else{
        $mrp = Product::find($orderdetailProductId);
        if($mrp)
            return $mrp->mrp;
        else
            return 0;
    }
}


function getDistanceTravelled($empid, $curr_date)
{

    // $company_id=Auth::user()->isCompanyManager()->company_id;
    $locations = \App\Location::select('latitude as lat', 'longitude as lng')
        ->where('employee_id', $empid)
        ->where('created_at', '>=', $curr_date . ' 00:00:00')
        ->where('created_at', '<=', $curr_date . ' 23:59:59')
        ->orderBy('unix_timestamp', 'asc')
        ->get();
    $ptcount = count($locations);

    $totdist = 0;

    for ($i = 1; $i < $ptcount; $i++) {

        $distyravelled = distance($locations[$i - 1]->lat, $locations[$i - 1]->lng, $locations[$i]->lat, $locations[$i]->lng, 'K');

        $totdist = $totdist + isValid($distyravelled);
    }

    return number_format($totdist, 2) . ' KM';

}

function isValid($arg = 0)
{
    return (is_nan($arg) || is_infinite($arg)) ? 0 : $arg;
}

function distance($lat1, $lon1, $lat2, $lon2, $unit)
{

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        $miles = $miles * 1.609344;
    }
    if ($unit == "N") {
        $miles = $miles * 0.8684;
    }
    return $miles;

}

// function distance2($lat1, $lon1, $lat2, $lon2, $unit) {
//   //alert('kdfkl');
//   $radlat1 = Math.PI * $lat1/180;
//   $radlat2 = Math.PI * $lat2/180;
//   $theta = $lon1-$lon2;
//   $radtheta = Math.PI * $theta/180;
//   $dist = Math.sin($radlat1) * Math.sin($radlat2) + Math.cos($radlat1) * Math.cos($radlat2) * Math.cos($radtheta);
//   if ($dist > 1) {
//   $dist = 1;
//   }
//   $dist = Math.acos($dist);
//   $dist = $dist * 180/Math.PI;
//   $dist = $dist * 60 * 1.1515;
//   if ($unit=="K") { $dist = $dist * 1.609344; }
//   if ($unit=="N") { $dist = $dist * 0.8684; }
//   return $dist;
// }
function getArrayValue($arraySource, $key, $emptyText = null, $trim = FALSE)
{
    if (is_array($arraySource) && !empty($arraySource[$key])) {
        return $trim == TRUE ? trim($arraySource[$key]) : $arraySource[$key];
    } else {
        return $emptyText;
    }
}

function getObjectValue($source, $key, $emptyText = null)
{
    if (empty($source)) return $emptyText;
    return empty($source->$key) ? $emptyText : $source->$key;
}

function getNotification()
{
    $parsedUrl = $_SERVER['HTTP_HOST'];
    $host = explode('.', $parsedUrl);
    $subdomain = $host[0];
    //$subdomain = $subdomain ?: request()->route('subdomain');
    $company = \App\Company::where('domain', $subdomain)->first();
    return (new App\Notification())->where('company_id', $company->id)
        ->where('to', 0)
        ->where('status', 1)
        ->orderBy('created_at', 'desc')
        ->get();
}


function sendPushNotification($notification = null, $registrationIDs, $updatedData = null, $single = false)
{

    $to = ($single == true) ? 'to' : 'registration_ids';
    $fields = array(
        $to => $registrationIDs, //["id1","id2"]
        'data' => array('message' => $notification, 'data' => $updatedData)
    );

    //$serverKey = "AIzaSyAW3sRmumELYIcrKTW_FxyzfyIvfBieDg8"; // legacy key from https://console.firebase.google.com/project/deltasalesapp/settings/cloudmessaging/android:com.deltatechnepal.salestracking
    $serverKey = env('CLOUD_MESSAGE_LEGACY_KEY');

    $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';

    $headers = array(
        'Authorization:key=' . $serverKey,
        'Content-Type:application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function sendPushNotification_($registrationIDs, $type, $message, $data)
{ //tye 1,2,3,4..... need to  be manage with constant later

    $local = false;
    if ($local) return false;

    //Log::info('info', array("registrationIDs"=>print_r($registrationIDs,true)));
    if (empty($registrationIDs)) return false;

    $to = 'registration_ids';
    $fields = array(
        $to => $registrationIDs, //["id1","id2"]
        'data' => array('message' => $message, 'type' => $type, 'data' => $data)
    );

    // $serverKey = "AIzaSyAW3sRmumELYIcrKTW_FxyzfyIvfBieDg8"; // for live
    // $serverKey = "AIzaSyDY9FgdQKH4nvncjIq8Av891y5mGHiLitY"; // for staging
    //$serverKey = "AIzaSyDiKqIHnLtqffVbNTVQ0CtQmVRyT7jP9DI"; // for local
    $serverKey = env('CLOUD_MESSAGE_LEGACY_KEY');

    //$serverKey = defined('CLOUD_MESSAGE_LEGACY_KEY')?CLOUD_MESSAGE_LEGACY_KEY:"";
    $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';

    $headers = array(
        'Authorization:key=' . $serverKey,
        'Content-Type:application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function sendNotification($companyID, $employeeID, $createdAt, $title, $description = "", $status = 1, $to = 0)
{
    $notificationData = array(
        "company_id" => $companyID,
        "employee_id" => $employeeID,
        "title" => $title,
        "description" => $description,
        "created_at" => $createdAt,
        "status" => $status,
        "to" => $to
    );
    Log::info('info', array("notificationData" => print_r($notificationData, true)));
    $nSaved = DB::table('notifications')->insertGetId($notificationData);
    return $nSaved;
}

function saveAdminNotification($companyID, $employeeID, $createdAt, $title, $dataType, $data, $description = "")
{

    $notificationData = array(

        "action_id"   => getArrayValue($data,"id"),
        "company_id"  => $companyID,
        "employee_id" => $employeeID,
        "created_at"  => $createdAt,
        "title"       => $title,
        "data_type"   => $dataType,
        "data"        => is_array($data)?json_encode($data):$data,
        "description" => $description,
        "status"      => 1,
        "to"          => 0
    );

    //Log::info('info', array("notificationData"=>print_r($notificationData,true)));
    $nSaved = DB::table('notifications')->insertGetId($notificationData);
    return $nSaved;
}


function arrayGroupBy($arrayData = NULL, $key = NULL, $objectElement = NULL)
{
    $returnData = array();
    if (empty($arrayData) || empty($key)) return $returnData;
    if (is_array($arrayData)) {
        foreach ($arrayData as $val) {
            if ($objectElement) {
                $returnData[$val->$key][] = $val;
            } else {
                $returnData[$val[$key]][] = $val;
            }
        }
    }
    return $returnData;
}

function getClientHandlingData($companyID = NULL, $clientID = NULL,$returnArray = false)
{
    $returnData = ($returnArray)?array():"";
    if (empty($companyID) || empty($clientID)) return $returnData;
    //$handles = DB::table('handles')->where("company_id", $companyID)->where("client_id", $clientID)->where("map_type", 2)->get()->toArray();
    $handles = DB::table('handles')->where("company_id", $companyID)->where("client_id", $clientID)->get()->toArray();
    if (!empty($handles)) {
        $tempArray = array();
        foreach ($handles as $k => $v) {
            array_push($tempArray, $v->employee_id);
        }
        $returnData = ($returnArray)?$tempArray:implode(",", $tempArray);

    }
    return $returnData;
}

function getClientAccessibleData($companyID = NULL, $clientID = NULL,$returnArray = false)
{
    $returnData = ($returnArray)?array():"";
    if (empty($companyID) || empty($clientID)) return $returnData;
    //$handles = DB::table('handles')->where("company_id", $companyID)->where("client_id", $clientID)->where("map_type", 2)->get()->toArray();
    $handles = DB::table('accessibility_link')->where("company_id", $companyID)->where("client_id", $clientID)->get()->toArray();
    if (!empty($handles)) {
        $tempArray = array();
        foreach ($handles as $k => $v) {
            array_push($tempArray, $v->employee_id);
        }
        $returnData = ($returnArray)?$tempArray:implode(",", $tempArray);

    }
    return $returnData;
}

function getFBIDs($companyID, $employeeGroupID = null, $employeeID = null)
{
    $fbIDs = array();
    if (empty($companyID)) return false;
    if (empty($employeeGroupID) && empty($employeeID)) {
        $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
    }

    if (!empty($employeeID)) {
        $fbIDs = DB::table('employees')->where(
            array(
                array('company_id', $companyID),
                array('status', 'Active'),
                array('id', $employeeID)
            )
        )->whereNotNull('firebase_token')->pluck('firebase_token');
    }

    if (!empty($employGroupID)) {
        return false;
    }

    return $fbIDs;
}


if (!function_exists('array_group_by')) {
    /**
     * Groups an array by a given key.
     *
     * Groups an array into arrays by a given key, or set of keys, shared between all array members.
     *
     * Based on {@author Jake Zatecky}'s {@link https://github.com/jakezatecky/array_group_by array_group_by()} function.
     * This variant allows $key to be closures.
     *
     * @param array $array The array to have grouping performed on.
     * @param mixed $key,... The key to group or split by. Can be a _string_,
     *                       an _integer_, a _float_, or a _callable_.
     *
     *                       If the key is a callback, it must return
     *                       a valid key from the array.
     *
     *                       If the key is _NULL_, the iterated element is skipped.
     *
     *                       ```
     *                       string|int callback ( mixed $item )
     *                       ```
     *
     * @return array|null Returns a multidimensional array or `null` if `$key` is invalid.
     */
    function array_group_by(array $array, $key)
    {
        if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
            trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
            return null;
        }
        $func = (!is_string($key) && is_callable($key) ? $key : null);
        $_key = $key;
        // Load the new array, splitting by the target key
        $grouped = [];
        foreach ($array as $value) {
            $key = null;
            if (is_callable($func)) {
                $key = call_user_func($func, $value);
            } elseif (is_object($value) && isset($value->{$_key})) {
                $key = $value->{$_key};
            } elseif (isset($value->$_key)) {
                $key = $value->$_key;
            }
            if ($key === null) {
                continue;
            }
            $grouped[$key][] = $value;
        }
        // Recursively build a nested grouping if more parameters are supplied
        // Each grouped array value is grouped according to the next sequential key
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $params = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array('array_group_by', $params);
            }
        }
        return $grouped;
    }
}


function getRecentLocationFromDB($companyID, $employeeID, $start)
{

    $recentEmpLocation = Location::select('*')
        ->where('company_id', $companyID)
        ->where('employee_id', $employeeID)
        ->where('unix_timestamp', '>', $start)
        ->orderBy('unix_timestamp', 'desc')
        ->get()
        ->first();
    return empty($recentEmpLocation) ? $recentEmpLocation : $recentEmpLocation->toArray();
}


function getRecentLocationFromFile($companyID, $employeeID, $currentDate, $time)
{

    if (empty($companyID) || empty($employeeID) || empty($currentDate)) {
        return null;
    }

    $fileName = getFileName($companyID, $employeeID, $currentDate);

    $result = array();
    if (empty($fileName) || empty($time)) return $result;

    $exists = Storage::disk("local")->exists($fileName);
    $fileContent = $exists ? Storage::get($fileName) : "";
    $decodedContent = empty($fileContent) ? array() : json_decode($fileContent, true);

    if (!empty($decodedContent)) {
        //$result = end($decodedContent); most recent location  may be older than check in time location(so in this case need to use checkin location)
        $result = array_filter($decodedContent, function ($location) use ($time) {
            $tempTime = getArrayValue($location, "unix_timestamp");
            return (($tempTime > $time));
        });

        $result = end($result);
    }

    if (empty($result)) {


        $result = getRecentLocationFromDB($companyID, $employeeID, $time);
        //Log::info('info', array("result from db"=>print_r($result,true)));
    } else {

        //Log::info('info', array("result from file"=>print_r($result,true)));

    }

    return $result;
}



function getImageArray($type, $typeID,$companyID=null)
{
    if (empty($type) || empty($typeID)) return array();
    $whereArray = empty($companyID)?[['type_id',$typeID],['type',$type]]:[['type_id',$typeID],['type',$type],['company_id',$companyID]];
    $images = DB::table('images')->where($whereArray)->get()->toArray();
    if (empty($images)) return array();
    $finalArray = array();
    $imageIds = array();
    $imageArray = array();
    $pathArray = array();
    foreach ($images as $key => $value) {
        array_push($imageIds,(string)$value->id);
        array_push($imageArray, $value->image);
        array_push($pathArray, $value->image_path);
    }

    $finalArray["image_ids"] = $imageIds;
    $finalArray["images"] = $imageArray;
    $finalArray["image_paths"] = $pathArray;
    return $finalArray;
}

function getProductVariants($companyID,$productID = null){

    $return = null;

    if (empty($companyID)) return $return;
    $whereArray = empty($productID)?[['company_id',$companyID]]:[['company_id',$companyID],['product_id',$productID]];
    $result = DB::table('product_variants')->where($whereArray)->get()->toArray();
    $return = empty($result)?$return:$result;
    //Log::info('info', array("data "=>print_r($return,true)));
    return $return;
}

function callPythonApi($method, $url, $data = false)
{

    $method = empty($method)?"POST":$method;
    // $url    = empty($url)?"http://35.184.238.11:5000/gps":$url;
    $url    = empty($url)?"http://ektag.pythonanywhere.com/gps":$url;
    
    /*
    *@param  $methond : string
    *@param $url : String
    *@param $data : associative array of field_name and value e.g array("raw_data"=>"json string")
    **/
    //Log::info('info', array("data inside callPythonApi"=>print_r($data,true)));
    //Log::info('info', array("url inside callPythonApi"=>print_r($url,true)));
    $data = json_encode($data);

    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            $headers = array(
                'Content-Type:application/json'
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function getFileName($companyID, $employeeID, $tempDate)
{

    $fileName = "";
    if (!empty($companyID) || !empty($employeeID) || !empty($tempDate)) {
        $fileName = "locations/company_" . $companyID . "/" . $tempDate . "/empyee_" . $employeeID . ".json";
    }
    return $fileName;
}


function getFileLocationWithRange($fileName, $time1, $time2, $accuracy = null)
{

    $result = array();
    if (empty($fileName) || empty($time1) || empty($time2)) return $result;

    $exists = Storage::disk("local")->exists($fileName);
    $fileContent = $exists ? Storage::get($fileName) : "";
    $decodedContent = empty($fileContent) ? array() : json_decode($fileContent, true);
    //Log::info('info', array('$decodedContent' => print_r($decodedContent, true)));

    if (!empty($decodedContent)) {

        foreach ($decodedContent as $location) {

            $datetime = getArrayValue($location,"datetime");
            $tempTime = strtotime($datetime) * 1000;
            $tempAccuracy = getArrayValue($location, "accuracy");
            $distanceFromLastGPS = getArrayValue($location, "distance_from_last_gps");
            $checkTime1 = ($tempTime >= $time1) ? true : false;
            $checkTime2 = ($tempTime <= $time2) ? true : false;
            $checkAccuracy = ($tempAccuracy < $accuracy && $tempAccuracy != 0) ? true : false;
            // $checkTime1 = ($tempTime >= $time1) ? true : false;
            // $checkTime2 = ($tempTime <= $time2) ? true : false;
            // //$checkAccuracy = ($tempAccuracy < $accuracy) ? true : false;
            // $checkDistance = ($distanceFromLastGPS > 10)?true:false;
            //Log::info('info', array('check1/check2' => print_r($checkTime1."/".$checkTime2, true)));

            if (empty($accuracy)) {

                //Log::info('info', array('accuracy' => print_r($accuracy, true)));
                if ($checkTime1 && $checkTime2) {
                    array_push($result, $location);
                }
            } else {

                if ($checkTime1 && $checkTime2 && $checkAccuracy) {
                    array_push($result, $location);
                }
            }
        }
    }
    return $result;
}


/**
 * @param $companyID
 * @param $employeeID
 * @param $date
 * @param $time1
 * @param $time2
 * @param string $unit
 * @param string $fileName
 * @return float|int
 */
function getDistanceWithRange($companyID,$employeeID,$date, $time1, $time2,$unit = "K",$fileName=null)
{

    $totalDistance = 0;
    if($fileName == null){

        $fileName = getFileName($companyID,$employeeID,$date);
        if (empty($fileName) || empty($time1) || empty($time2)) return $totalDistance;
    }

    $result = array();

    $exists = Storage::disk("local")->exists($fileName);
    //Log::info('info', array('fileName' => print_r($fileName, true)));

    $fileContent = $exists ? Storage::get($fileName) : "";
    $decodedContent = empty($fileContent) ? array() : json_decode($fileContent, true);
    //Log::info('info', array('decodedContent' => print_r($decodedContent, true)));




    if (!empty($decodedContent)) {


        $distanceIteration = 0;
        foreach ($decodedContent as $location) {
            if($distanceIteration == 0){

                $lat1 = getArrayValue($location, "latitude");
                $lon1 = getArrayValue($location, "longitude");
            }

            $lat2 = getArrayValue($location,"latitude");
            $lon2 = getArrayValue($location,"longitude");

            $tempTime = getArrayValue($location, "unix_timestamp");
            $tempAccuracy = getArrayValue($location, "accuracy");

            $checkTime1 = ($tempTime >= $time1) ? true : false;
            $checkTime2 = ($tempTime <= $time2) ? true : false;
            $checkAccuracy = ($tempAccuracy < 60) ? true : false;
            if ($checkTime1 && $checkTime2 && $checkAccuracy) {
                array_push($result, $location);
                $totalDistance = $totalDistance + distance($lat1,$lon1,$lat2,$lon2,$unit);
                $lat1 = $lat2;
                $lon1 = $lon2;
                $distanceIteration ++;
            }
        }
    }

    return $totalDistance;
}

function setDateTime($datetime, $time = false)
{
    if ($datetime == '')
        return NULL;
    $format = 'Y-m-d';
    if ($time == true)
        $format = 'Y-m-d H:i:s';
    $appTimeZone = Config::get('app.timezone');
    $customerTimeZone = getSettings('time_zone');
    if ($appTimeZone == $customerTimeZone)
        return $datetime;
    $date = Carbon::createFromFormat($format, $datetime, $customerTimeZone);
    $date->setTimezone($appTimeZone)->format($format);
    return $date->format($format);
}//end setDateTime

function setTime($time = false)
{
    if ($time == '')
        return NULL;

    $format = 'h:i A';
    $appTimeZone = Config::get('app.timezone');
    $customerTimeZone = getSettings('time_zone');
    if ($appTimeZone == $customerTimeZone)
        return $datetime;
    $date = Carbon::createFromFormat($format, $time, $customerTimeZone);
    return $date->format($format);
}

function getSettings($key)
{

    $settings = App\Setting::findOrFail('1');

    return $settings->$key;
}

function notifyAppAboutChange($companyID,$type="leave_types"){
    //need to manag later for other type
    $leaveTypes = DB::table('leave_type')->where('company_id', $companyID)->orderBy('name', 'ASC')->get();
    $leaveTypes = json_encode($leaveTypes);
    $dataPayload = array("data_type" => "leave_type", "leave_types" => $leaveTypes, "action" => "update");
    $msgID = sendPushNotification_(getFBIDs($companyID), 20, null, $dataPayload);
}

function getCompanyAddress($country, $state, $city){
    $countryId = isset($country)?$country:false;
    $stateId = isset($state)?$state:false;
    $cityId = isset($city)?$city:false;

    $countryName = "";
    $stateName = "";
    $cityName = "";

    if($countryId)
        $countryName = DB::table('countries')->where('id', $countryId)->value('name');
    if($stateId)
        $stateName = DB::table('states')->where('id', $stateId)->value('name');
    if($cityId)
        $cityName = DB::table('cities')->where('id', $cityId)->value('name');

    $address = isset($country)?$countryName.','.$stateName.','.$cityName:"Nepal";

    return $address;
}

function getCompanyStatus($company_id){
    $status = Company::find($company_id)->is_active;

    return $status==0? true: false;
}

function getCompanySubscriptionDate($company_id){
    $dateToday = date("Y-m-d");
    $subscriptionEndDate = Company::find($company_id)->end_date;
    $end_date = date("Y-m-d", strtotime($subscriptionEndDate));

    return $dateToday>$subscriptionEndDate;
}

function getCompanyPendingDays($company_id){
    $dateToday = date("Y-m-d");
    $subscriptionEndDate = Company::find($company_id)->end_date;
    $end_date = date("Y-m-d", strtotime($subscriptionEndDate));
    $daysPending = array("end_date" => getDeltaDate($end_date));
    if($dateToday <= $end_date){
        $diff = (strtotime($end_date)-strtotime($dateToday))/60/60/24;
        if($diff<=7) {
            $daysPending["in_range"] = true;
            $daysPending["num_days"] = $diff;
        }else{
            $daysPending["in_range"] = false;
            $daysPending["num_days"] = $diff;
        }
    } else{
        $daysPending["in_range"] = false;
        $daysPending["num_days"] = 0;
    }



    return $daysPending;
}

function checkpartytypepermission($id,$access){
    $company_id = config('settings.company_id');
    $partytypename=PartyType::select('name','id')->where('id', $id)->orderBy('id', 'ASC')->first();
    if($partytypename){
        $ptaccess=str_replace(' ','-',$partytypename->name).'-'.$access;
        $permissionid = DB::table('permissions')->where('company_id',$company_id)->where('name', $ptaccess)->first();

        $hasperm=Auth::user()->hasPermissionTo($permissionid->id);
        return $hasperm;
    }else{
        return true;
    }

}

function checkCustomModulePermission($name, $access){
    $company_id = config('settings.company_id');
    $stringName = str_replace(' ', '-', $name).'-'.$company_id.'-'. $access;
    $permission = Permission::where('company_id', $company_id)->where('name', $stringName)->first();

    if (Auth::user()->hasPermissionTo($permission->id)) {
        return true;
    }
}


function modify_to_format($field_name)
{
    Str::slug(strtolower($field_name));
}

function getCompanyPartyTypeLevel($company_id){
  $levels = PartyType::whereCompanyId($company_id)->where('parent_id', '!=', 0)->pluck( 'parent_id', 'id' )->toArray();
  if(empty($levels)) return 0;
  else return 1;
}

function isFirstLevelParty($client_type){
  $instance = PartyType::whereId($client_type)->first();
  $hasChildren = PartyType::whereParentId($client_type)->first();
  if($instance) {
    if($instance->parent_id == 0 && !$hasChildren) return 0;
    elseif($instance->parent_id != 0 && !$hasChildren) return 0;
    else return 1;
  }

  return 0;
}

function activateAgeingPayment($client_type){
  $company_id = config('settings.company_id');
  $instance = PartyType::whereCompanyId($company_id)->whereId($client_type)->first();
  $hasChildren = PartyType::whereCompanyId($company_id)->whereParentId($client_type)->first();
  if($instance) {
    if($instance->parent_id == 0 && !$hasChildren) return 1;
    else return 0;
  }

  return 0;
}




