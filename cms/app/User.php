<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 'name', 'phone', 'email', 'password', 'outlet_id', 'is_logged_in', 'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function name()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isCompanyManager()
    {
        $owner = company()->managers()->where('user_id', $this->id)->first();
        if (!($owner))
            $owner = Employee::where('is_admin', 1)->where('user_id', $this->id)->first();

        return $owner;
    }

    public function isCompanyOwner()
    {
        $owner = company()->managers()->where('user_id', $this->id)->first();
        if (!($owner))
            $owner = Employee::where('is_owner', 1)->where('user_id', $this->id)->first();

        return $owner;
    }

    public function managerName($company_id)
    {
        $result = company()->managers()->where('company_id', $company_id)->first();

        return User::where('id', $result['user_id'])->first();
    }

    public function companyName($company_id)
    {
        return company()->where('id', $company_id)->first();
    }

    public function initials()
    {
        return mb_substr($this->first_name, 0, 1) . mb_substr($this->last_name, 0, 1);
    }

    public function customers()
    {
        return $this->hasMany('App\Customer');
    }

    public function managers()
    {
        return $this->hasMany('App\Manager');
    }

    public function isAppAdmin()
    {
        return $this->is_appadmin;
    }

    public function employees()
    {
        return $this->hasMany('App\Employee');
    }

    public function employee()
    {
        return $this->hasOne('App\Employee');
    }

    public function isCompanyMember()
    {
        return company()->employees()->where('user_id', $this->id)->first();
    }

    public function isCompanyEmployee()
    {
        return company()->employees()->where('is_admin', '!=', 1)->where('user_id', $this->id)->first();
    }

    public function isCompanyEmployee_old()
    {
        if (!($this->isCompanyManager())) {
            $userid = Auth()->user()->id;
            $employee = company()->employees()->where('user_id', $userid)->first();
            if (isset($employee->designation)) {
                return Designation::where('id', $employee->designation)->where('name', '<>', 'Admin')->first();
            }
        }
        return false;
    }

    public static function EmployeeId()
    {
        $userid = Auth()->user()->id;
        $result = company()->employees()->where('user_id', $userid)->first();
        return $result->id;
    }

    public static function EmployeeName()
    {
        $userid = Auth()->user()->id;
        $result = company()->employees()->where('user_id', $userid)->first();
        return $result->name;
    }

    public function getChainUsers($id, array $finalresult = [])
    {

        $results = Employee::where('superior', $id)->get();
        foreach ($results as $result) {
            $finalresult[] = $result->id;
            $finalresult = $this->getChainUsers($result->id, $finalresult);

        }
        return $finalresult;

    }

    public function getUpChainUsers($id, array $finalresult = [])
    {

        $results = Employee::where('id', $id)->get();
        foreach ($results as $result) {
            $finalresult[] = $result->superior;
            $finalresult = $this->getUpChainUsers($result->superior, $finalresult);
        }
        return $finalresult;
    }

    public function getAllChainUsers($id, array $finalresult = [])
    {
        $finalresult[] = $id;
        $finalresult = $this->getChainUsers($id, $finalresult);
        $finalresult = $this->getUpChainUsers($id, $finalresult);
        return $finalresult;

    }

    public function getsuperior($id)
    {
        $results = Employee::where('id', $id)->get();
        $immsuperior = $results->superior;
        return $immsuperior;

    }

    public function getAllSuperior($id, array $allsuperior = [])
    {
        $empdetails = $this->isCompanyEmployee();
        $emp_superior = $empdetails->superior;
        $results = Employee::where('id', $emp_superior)->get();
        $allsuperior[] = $results->id;
        $allsuperior = $this->getAllSuperior($results->superior, $allsuperior);
        return $allsuperior;

    }

    public function isCompanyAdmin()
    {
        if (!($this->isCompanyManager())) {
            $userid = Auth()->user()->id;
            $employee = company()->employees()->where('user_id', $userid)->first();
            if (isset($employee->designation)) {
                $desig = Designation::where('id', $employee->designation)->where('name', 'Admin')->first();
                if ($desig)
                    return $employee;
            }
        }
        return false;

    }

    public function isLowestDesignation()
    {
        if (!($this->isCompanyManager())) {
            $userid = Auth()->user()->id;
            $company_id = config('settings.company_id');
            $employee = company()->employees()->where('user_id', $userid)->first();
            $subDesignationExists = Designation::where('company_id', $company_id)->where('parent_id', $employee->designation)->first();
            if (isset($subDesignationExists)) {
                return true;
            }

        }
        return false;
    }

    //handle query gives the user/employee id from the particular table
    //performing the hierarchy only present in the table
    public function handleQuery($tableraw, $id = NULL)
    {

        $ts = str_plural(strtolower(str_replace('_', '', $tableraw)));
        $t = str_replace(' ', '', ucwords(str_replace("_", " ", $tableraw)));
        if ($t == 'Partytype') {
            $table = 'App\PartyType';
        } else {
            $table = 'App\\' . $t;
        }
        if ($ts == "noorders") {
            $ts = "no_orders";

        }

        if ($ts == "dayremarks") {
            $ts = "day_remarks";
        }

        if ($ts == "notes") {
            $ts = "meetings";
        }

        if ($ts == "clientvisits") {
            $ts = "client_visits";
        }

        if ($ts == "odometerreports") {
            $ts = "odometer_reports";
        }

        if ($ts == "targetsalesmen") {
            $ts = "salesmantarget";
        }


        if ($this->isCompanyEmployee() && !($this->isCompanyAdmin())) {
            $empdetails = $this->isCompanyEmployee();
            $emp_id = $empdetails->id;
            $company_id = $empdetails->company_id;
            $chainusers = $this->getChainUsers($emp_id);
            array_push($chainusers, $emp_id);

            if (isset($id)) {
                if ($tableraw == 'employee') {
                    $result = $table::where($ts . '.id', $id)
                        ->where($ts . '.company_id', $company_id);
                } elseif ($tableraw == 'client') {
                    $visibleusers = $chainusers;
                    //array_push($visibleusers, $emp_id);
                    // $handles = DB::table('handles')
                    //       ->where('employee_id', $chainusers)
                    //     ->pluck('client_id')
                    //   ->toArray();
                    $result = $table::where($ts . '.company_id', $company_id)
                        ->where($ts . '.id', $id);
                } elseif ($tableraw == 'activity') {
                    $result = $table::where($ts . '.company_id', $company_id)
                        ->where('assigned_to', $emp_id)
                        ->orWhere('created_by', $emp_id);

                } else {
                    $result = $table::where($ts . '.id', $id)
                        ->where($ts . '.company_id', $company_id)
                        ->whereIn($ts . '.employee_id', $chainusers);
                }
            } else {
                $visibleusers = $chainusers;
                if ($tableraw == 'employee') {
                    $result = $table::where($ts . '.company_id', $company_id)
                        ->whereIn($ts . '.id', $visibleusers);
                } elseif ($tableraw == 'client') {
                    $handles = DB::table('handles')
                        ->where('employee_id', $emp_id)
                        ->where('handles.company_id', $company_id)
                        ->pluck('client_id')->toArray();
                    // echo "<pre>";
                    // print_r($handles);
                    // die;

                    $result = $table::where($ts . '.company_id', $company_id)
                        ->whereIn($ts . '.id', $handles);
                } elseif ($tableraw == 'activity') {
                    $result = $table::where($ts . '.company_id', $company_id)
                        ->where('assigned_to', $emp_id)
                        ->orWhere('created_by', $emp_id);

                } else {
                    $result = $table::where($ts . '.company_id', $company_id)
                        ->whereIn($ts . '.employee_id', $visibleusers);
                }
            }

        } else {
            if ($this->isCompanyManager()) {
                $admindetails = $this->isCompanyManager();
                $company_id = $admindetails->company_id;
                $emp_id = 0;

            } else {
                $admindetails = $this->isCompanyAdmin();
                $company_id = $admindetails->company_id;
                $emp_id = $admindetails->id;
            }
            if (isset($id)) {
                if ($tableraw == 'activity') {
                    $result = $table::where($ts . '.company_id', $company_id)
                        ->where('assigned_to', $emp_id)
                        ->orWhere('created_by', $emp_id);

                } else {
                    $result = $table::where($ts . '.id', $id)
                        ->where($ts . '.company_id', $company_id);
                }
            } else {
                $company_id = $this->company_id;
                if ($tableraw == 'activity') {
                    $result = $table::where($ts . '.company_id', $company_id)
                        ->where('assigned_to', $emp_id)
                        ->orWhere('created_by', $emp_id);

                } else {
                    $result = $table::where($ts . '.company_id', $company_id);
                }
            }
        }

        return $result;
    }

    //this does the same thing as handle-query
    //this is used to manage the hierarchy in custom module only
    public function handleBuilderQuery($tableName)
    {

        if ($this->isCompanyEmployee() && !($this->isCompanyAdmin())) {
            $empdetails = $this->isCompanyEmployee();
            $emp_id = $empdetails->id;
            $company_id = $empdetails->company_id;
            $chainusers = $this->getChainUsers($emp_id);
            array_push($chainusers, $emp_id);
            $visibleusers = $chainusers;
            $result = DB::table($tableName)->where('company_id', $company_id)->whereIn('employee_id', $visibleusers);
        } else {
            $company_id = $this->company_id;
            $result = DB::table($tableName)->where('company_id', $company_id);
        }
        return $result;
    }

    public function handleClients($id = NULL)
    {
        if ($this->isCompanyEmployee()) {
            $empdetails = $this->isCompanyEmployee();
            $emp_id = $empdetails->id;
            $company_id = $empdetails->company_id;
            $handles = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $emp_id)->pluck('client_id')->toArray();
            $clients = Client::where('company_id', $company_id)
                ->where(function ($query) use ($handles, $emp_id, $id) {
                    if (isset($id)) {
                        $query->where('client_type', $id);
                    }
                    $query->where('created_by', $emp_id);
                    $query->orWhereIn('id', $handles);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $admindetails = $this->isCompanyManager();
            $company_id = $admindetails->company_id;
            $clients = Client::where('company_id', $company_id)
                ->where('client_type', $id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return $clients;
    }

    public function handleCollections()
    {
        if ($this->isCompanyEmployee()) {
            $empdetails = $this->isCompanyEmployee();
            $emp_id = $empdetails->id;
            $company_id = $empdetails->company_id;
            $collections = Collection::where('company_id', $company_id)->where('employee_id', $emp_id)->orderBy('payment_date', 'desc')->orderBy('id', 'desc')->get();
        } else {
            $admindetails = $this->isCompanyManager();
            $company_id = $admindetails->company_id;
            $collections = Collection::where('company_id', $company_id)->orderBy('payment_date', 'desc')->orderBy('id', 'desc')->get();
        }

        return $collections;
    }

    public function handleCollectionById($id)
    {
        if ($this->isCompanyEmployee()) {
            $empdetails = $this->isCompanyEmployee();
            $emp_id = $empdetails->id;
            $company_id = $empdetails->company_id;
            $collection = Collection::where('id', $id)->where('employee_id', $emp_id)->first();
        } else {
            $admindetails = $this->isCompanyManager();
            $company_id = $admindetails->company_id;
            $collection = Collection::where('id', $id)->first();
        }

        return $collection;
    }

    public function handleEmployees()
    {
        if ($this->isCompanyEmployee()) {
            $empdetails = $this->isCompanyEmployee();
            $emp_id = $empdetails->id;
            $company_id = $empdetails->company_id;
            $employees = Employee::select('name', 'id')->where('company_id', $company_id)->where('id', $emp_id)->where('status', 'Active')->orderBy('name', 'asc')->get();
        } else {
            $admindetails = $this->isCompanyManager();
            $company_id = $admindetails->company_id;
            $employees = Employee::select('name', 'id')->where('company_id', $company_id)->where('status', 'Active')->orderBy('name', 'asc')->get();
        }
        return $employees;
    }

    public function handleEmployeeById()
    {
        return "Not Defined yet";
    }

    public function handleOrders()
    {
        if ($this->isCompanyEmployee()) {
            $empdetails = $this->isCompanyEmployee();
            $emp_id = $empdetails->id;
            $company_id = $empdetails->company_id;
            $orders = Order::where('company_id', $company_id)->where('employee_id', $emp_id);
        } else {
            $admindetails = $this->isCompanyManager();
            $company_id = $admindetails->company_id;
            $orders = Order::where('company_id', $company_id);
        }

        return $orders;
    }

    public function handleEmployeeswithOrders($empids)
    {
        if ($this->isCompanyEmployee()) {
            $empdetails = $this->isCompanyEmployee();
            $emp_id = $empdetails->id;
            $company_id = $empdetails->company_id;
            $employees = Employee::where('company_id', $company_id)->where('id', $emp_id)->orderBy('name', 'asc')
                ->pluck('name', 'id')
                ->toArray();
        } else {
            $admindetails = $this->isCompanyManager();
            $company_id = $admindetails->company_id;
            $employees = Employee::where('company_id', $company_id)
                ->whereIn('id', $empids)
                ->orderBy('name', 'asc')
                ->pluck('name', 'id')
                ->toArray();
        }

        return $employees;
    }

    public function handlePartieswithOrders($partyids)
    {
        if ($this->isCompanyEmployee()) {
            $empdetails = $this->isCompanyEmployee();
            $emp_id = $empdetails->id;
            $company_id = $empdetails->company_id;
            $clients = Client::where('company_id', $company_id)
                ->where('id', $partyids)
                ->orderBY('company_name', 'asc')
                ->pluck('company_name', 'id')
                ->toArray();
        } else {
            $admindetails = $this->isCompanyManager();
            $company_id = $admindetails->company_id;
            $clients = Client::where('company_id', $company_id)
                ->whereIn('id', $partyids)
                ->orderBY('company_name', 'asc')
                ->pluck('company_name', 'id')
                ->toArray();
        }

        return $clients;
    }

    public function handleOrderById($id)
    {
        if ($this->isCompanyEmployee()) {
            $empdetails = $this->isCompanyEmployee();
            $emp_id = $empdetails->id;
            $company_id = $empdetails->company_id;
            $collection = Order::where('id', $id)->where('employee_id', $emp_id)->first();
        } else {
            $admindetails = $this->isCompanyManager();
            $company_id = $admindetails->company_id;
            $collection = Order::where('id', $id)->first();
        }

        return $collection;
    }

    public function customClause()
    {
        if ($this->isCompanyEmployee()) {
            $empdetails = $this->isCompanyEmployee();
            $emp_id = $empdetails->id;
            $company_id = $empdetails->company_id;

            $clause = "where('employee_id'," . $emp_id . ")->where('company_id'," . $company_id . ")";
        } else {
            $admindetails = $this->isCompanyManager();
            $company_id = $admindetails->company_id;

            $clause = "where('company_id'," . $company_id . ")";
        }
        return $clause;
    }

    public function outlets()
    {
        return $this->belongsTo('App\Outlet');
    }

    public function companies()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }
}
