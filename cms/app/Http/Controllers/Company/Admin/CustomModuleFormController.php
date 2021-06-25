<?php

namespace App\Http\Controllers\Company\Admin;

use App\Client;
use App\Currency;
use App\CustomModule;
use App\CustomModuleField;
use App\Employee;
use App\Http\Controllers\Controller;
use App\Repository\CustomCheck;
use App\Repository\CustomModuleUpdate;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use function foo\func;


class CustomModuleFormController extends Controller
{
    public $company_id;

    public function __construct()
    {
        $this->middleware('auth');
        $this->company_id = config('settings.company_id');
    }

    public function index(Request $request)
    {

        $title = [];
        $slug = [];
        $customModule = CustomModule::with('customFields')->find($request->id);

        if (!checkCustomModulePermission($customModule->name, 'view')) {
            Session::flash('alert', 'Sorry! You do not have permission to view this content.');
            return redirect(domain_route('company.admin.home'));
        }

        if (is_null($customModule)) {
            return redirect()->route('company.admin.home', domain())
                ->with('errors', 'Error');
        }

        foreach ($customModule->customFields as $key => $field) {
            if ($key <= 4) {
                $title[] = $field->title;
                $slug[] = $field->slug;
            }
        }

        $customFieldsSlug = array_merge($slug, ['user_id', 'created_at']);
        $customFieldsTitle = array_merge($title, ['Created by', 'Created at']);


        $CustomDatas = Auth::user()->handleBuilderQuery($customModule->table_name)->get(['employee_id']);
        $employeeIds = collect($CustomDatas)->map(function ($ids) {
            return (array)$ids;
        })->toArray();

        $employeesWithDatas = Employee::whereIn('id', $employeeIds)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();


        return view('company.custom-modules-form.index', [
            'custom_fields' => $customModule,
            'form_data' => DB::table($customModule->table_name)->get()->count(),
            'custom_field_title' => $customFieldsTitle,
            'custom_field_slug' => $customFieldsSlug,
            'employeesWithDatas' => $employeesWithDatas,

        ]);
    }

    public function ajaxDatatable(Request $request)
    {
        $slug = [];
        $customModule = CustomModule::with('customFields')->find($request->id);

        foreach ($customModule->customFields as $key => $field) {
            if ($key < 5) {
                $slug[] = $field->slug;
            }
        }
        $customFieldsSlug = array_merge($slug, ['user_id', 'created_at']);

        $empVal = $request->empVal;
        $start_date = $request->startDate;
        $end_date = Carbon::createFromFormat('Y-m-d', $request->endDate)->endOfDay()->toDateTimeString();
        $start = $request->input('start');
        $limit = $request->input('length');
        $order = $customFieldsSlug[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $tableData = Auth::user()->handleBuilderQuery($customModule->table_name)->whereBetween('created_at', [$start_date, $end_date])->orderBy('id', 'desc');

        $dataCount = $tableData->count();


        if (!empty($empVal)) {
            $dataCount = $tableData->count();
            $tableData = $tableData->where('employee_id', $empVal);
        }

        $table_column = Schema::getColumnListing($customModule->table_name);
        if (empty($request->input('search.value'))) {
            $form_data = $tableData->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)->get();

        } elseif (!(empty($request->input('search.value')))) {

            $search = $request->input('search.value');
            $form_data_query = $tableData->where(function ($query) use ($search, $table_column) {
                foreach ($table_column as $column) {
                    $query->orWhere($column, 'LIKE', "%{$search}%");
                }
            });

            $dataCount = $form_data_query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)->count();

            $form_data = $form_data_query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)->get();
        }

        $datas = [];
        $i = $start;
        foreach ($form_data as $key => $data) {
            $main_data = [];

            foreach ($customFieldsSlug as $slug) {
                $employee = Employee::withTrashed()->find($data->employee_id);
                $edit = domain_route('company.admin.custom.modules.form.edit', ['id' => $customModule->id, 'data_id' => $data->id]);
                $view = domain_route('company.admin.custom.modules.form.view', ['id' => $customModule->id, 'data_id' => $data->id]);
                $delete = domain_route('company.admin.custom.modules.form.delete', ['id' => $customModule->id, 'data_id' => $data->id]);
                $main_data[$slug] = $this->makeDataReadable($data, $slug, $customModule);

                $employee_show = domain_route('company.admin.employee.show', [$employee->id]);

                if (checkCustomModulePermission($customModule->name, 'view')) {
                    $view = "<a href='{$view}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
                } else {
                    $view = Null;
                }

                if (checkCustomModulePermission($customModule->name, 'update')) {
                    $edit = "<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                } else {
                    $edit = Null;
                }

                if (checkCustomModulePermission($customModule->name, 'delete')) {
                    $delete = "<a class='btn btn-danger btn-sm delete del-modal' data-mid='$data->id' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash'></i></a>";
                } else {
                    $delete = Null;
                }


                $main_data['id'] = $i + $key + 1;
                $main_data['action'] = "{$view}{$edit}{$delete}";
                $main_data['created_at'] = getDeltaDate(date('Y-m-d', strtotime($data->created_at)));
                if ($employee->deleted_at){
                    $user = $employee->name;
                }else{
                    $user = "<a href='{$employee_show}' datasalesman='{{$employee->name}}'> {$employee->name}</a>";
                }
                $main_data['user_id'] = $user;

            }
            $datas[] = $main_data;
        }


        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($dataCount),
            "recordsFiltered" => intval($dataCount),
            'data' => $datas,
        );
        return json_encode($json_data);
    }

    public function create(Request $request)
    {
        $customModule = CustomModule::with('customFields')->find($request->id);

        if (!checkCustomModulePermission($customModule->name, 'create')) {
            Session::flash('alert', 'Sorry! You do not have permission to view this content.');
            return redirect(domain_route('company.admin.home'));
        }

        return view('company.custom-modules-form.create', [
            'custom_fields' => $customModule,
        ]);
    }

    public function getCommonParties($domain, Request $request, $type = null)
    {
        $company_id = config('settings.company_id');
        //Pluck all clients ids of assignor in case of admin or user
        $isCreatorAdmin = Employee::where('company_id', $company_id)->where('id', $request->created_by)->where('is_admin', 1)->first();
        if ($isCreatorAdmin) {
            $assignerHandles = DB::table('handles')->where('company_id', $company_id)->pluck('client_id')->toArray('client_id');
        } else {
            $assignerHandles = DB::table('handles')->where('company_id', $company_id)->where('employee_id', $request->created_by)->pluck('client_id')->toArray('client_id');
        }

        //New concept implementation for displaying assignor parties
        if ($type == null) {
            $data = Client::select('clients.id', 'clients.company_name')->where('status', 'Active')->whereIn('id', $assignerHandles)->get()->toArray();
        } else {
            $data = Client::select('clients.id')->where('status', 'Active')->whereIn('id', $assignerHandles)->get()->toArray();
        }
        return $data;
    }

    public function store(Request $request)
    {
        $customModule = CustomModule::with('customFields')->find($request->id);

        if (!$customModule) {
            Session::flash('error', 'This Custom Module is deleted');
            return redirect()->route('company.admin.custom.modules.form.index', ['domain' => domain(), 'id' => $request->id]);
        }

        if ($this->checkIfRequestIsEmpty($request->all())) {
            Session::flash('message', 'Save failed ! Nothing to save');
            return redirect()->back();
        }

        if (count($request->all()) < 2) {
            Session::flash('message', 'No Item in form to submit');
            return redirect()->route('company.admin.custom.modules.form.index', ['domain' => domain(), 'id' => $request->id]);
        }

        if ($customModule->customFields->count()) {
            $processedValues = collect((new  CustomCheck($customModule->customFields, $request))->check());
        }

        $extra_data = collect(['created_at' => now(), 'updated_at' => now(), 'user_id' => auth()->id(), 'employee_id' => auth()->user()->EmployeeId(), 'company_id' => $this->company_id]);

        $main_data = $customModule->customFields->map(function ($field) use ($processedValues) {
            return [$field->slug => isset($processedValues[$field->id]) ? $processedValues[$field->id] : ''];
        })->collapse()->merge($extra_data)->toArray();

        $getId = DB::table($customModule->table_name)->insertGetId(
            $main_data
        );

        $data = DB::table($customModule->table_name)->find($getId);

        $superiors = Employee::EmployeeSeniors($data->employee_id, array());

        $dataToArray = json_decode(json_encode($data), true);

        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereIn('id', $superiors)->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "Custom Module Form", 'custom_module_id' => $customModule->id, "custom_module_form" => $dataToArray, "action" => "custom module form added");
        $sent = sendPushNotification_($fbIDs, 45, null, $dataPayload);

        Session::flash('success', 'Created Successfully');
        return redirect()->route('company.admin.custom.modules.form.index', ['domain' => domain(), 'id' => $request->id]);
    }

    public function edit(Request $request)
    {

        $custom_fields = CustomModule::with('customFields')->find($request->id);

        if (!checkCustomModulePermission($custom_fields->name, 'update')) {
            Session::flash('alert', 'Sorry! You do not have permission to view this content.');
            return redirect(domain_route('company.admin.home'));
        }

        $form_data = DB::table($custom_fields->table_name)->find($request->data_id);

        if (is_null($form_data)) {
            Session::flash('error', 'This Custom Module is deleted');
            return redirect()->route('company.admin.custom.modules.form.index', ['domain' => domain(), 'id' => $request->id]);
        }
        return view('company.custom-modules-form.edit', [
            'custom_fields' => $custom_fields,
            'form_data' => $form_data,
        ]);
    }

    public function show(Request $request)
    {

        $customFieldsSlug = [];
        $customModule = CustomModule::with('customFields')->find($request->id);

        if (is_null($customModule)) {
            Session::flash('alert', 'Sorry! You do not have permission to view this content.');
            return redirect()->route('company.admin.custom.modules.form.index', ['domain' => domain(), 'id' => $request->id]);
        }

        if (!checkCustomModulePermission($customModule->name, 'view')) {
            Session::flash('alert', 'Sorry! You do not have permission to view this content.');
            return redirect(domain_route('company.admin.home'));
        }


        $form_data = DB::table($customModule->table_name)->where('id', $request->data_id)->get();

        if (is_null($form_data)) {
            Session::flash('error', 'This Custom Module is deleted');
            return redirect()->route('company.admin.custom.modules.form.index', ['domain' => domain(), 'id' => $request->id]);
        }

        foreach ($customModule->customFields as $key => $field) {
            $customFieldsSlug[] = $field->slug;
            $customFieldsTitle[] = $field->title;
        }


        $action = null;
        if (checkCustomModulePermission($customModule->name, 'update')) {
            $show = domain_route('company.admin.custom.modules.form.edit', ['id' => $customModule->id, 'data_id' => $request->data_id]);
            $action = "<a href='{$show}' class='btn btn-warning btn-sm edit' style='padding: 7px 6px;'><i class='fa fa-edit'></i>Edit</a>";
        }
        if (checkCustomModulePermission($customModule->name, 'delete')) {
            $delete = domain_route('company.admin.custom.modules.form.delete', ['id' => $customModule->id, 'data_id' => $request->data_id]);
            $action = $action . "<a class='btn btn-danger btn-sm delete' data-mid='{{$request->data_id }}' data-backdrop='false' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 7px 6px;'><i class='fa fa-trash-o'></i>Delete</a>";
        }

        $datas = [];
        foreach ($form_data as $key => $data) {
            foreach ($customFieldsSlug as $slug) {
                $datas[$slug] = $this->makeDataReadable($data, $slug, $customModule);
            }
        }

        return view('company.custom-modules-form.view', [
            'customModule' => $customModule,
            'datas' => $datas,
            'customFieldsSlug' => $customFieldsSlug,
            'customFieldsTitle' => $customFieldsTitle,
            'action' => $action
        ]);
    }

    public function update(Request $request)
    {

        $customModule = CustomModule::with('customFields')->find($request->id);
        $data = (array)DB::table($customModule->table_name)->find($request->data_id);


        if (!$customModule) {
            Session::flash('error', 'This Custom Module is deleted');
            return redirect()->route('company.admin.custom.modules.form.index', ['domain' => domain(), 'id' => $request->id]);
        }

        if ($this->checkIfRequestIsEmpty($request->all())) {
            Session::flash('message', 'Save failed ! Nothing to save');
            return redirect()->back();
        }

        if ($customModule->customFields->count()) {
            $processedValues = collect((new  CustomModuleUpdate($customModule->customFields, $request, $data))->check());
        }

        $extra_data = collect(['created_at' => now(), 'updated_at' => now(), 'employee_id' => $data['employee_id'], 'company_id' => $this->company_id]);

        $main_data = $customModule->customFields->map(function ($field) use ($processedValues) {
            return [$field->slug => isset($processedValues[$field->id]) ? $processedValues[$field->id] : ''];
        })->collapse()->merge($extra_data)->filter()->toArray();


        DB::table($customModule->table_name)->whereId($request->data_id)->update($main_data);

        $data = DB::table($customModule->table_name)->find($request->data_id);

        $dataToArray = json_decode(json_encode($data), true);

        $superiors = Employee::EmployeeSeniors($data->employee_id, array());

        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereIn('id', $superiors)->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "Custom Module Form", 'custom_module_id' => $customModule->id, "custom_module_form" => $dataToArray, "action" => "custom module form updated");
        $sent = sendPushNotification_($fbIDs, 45, null, $dataPayload);

        Session::flash('success', 'Updated successfully');
        return redirect()->route('company.admin.custom.modules.form.index', ['domain' => domain(), 'id' => $request->id]);
    }

    public function destroy(Request $request)
    {

        $customModule = CustomModule::findorFail($request->id);

        if (!checkCustomModulePermission($customModule->name, 'delete')) {
            Session::flash('alert', 'Sorry! You do not have permission to view this content.');
            return redirect(domain_route('company.admin.home'));
        }

        DB::table($customModule->table_name)->where('id', $request->data_id)->delete();
        Session::flash('success', 'Deleted successfully');

        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "Custom Module Form", 'custom_module_id' => $customModule->id, "custom_module_form" => $request->data_id, "action" => "custom module form deleted");
        $sent = sendPushNotification_($fbIDs, 45, null, $dataPayload);
        return back();
    }

    public function custompdfdexport(Request $request)
    {
        $getExportData = json_decode($request->exportedData)->data;
        $pageTitle = $request->pageTitle;
        $columns = json_decode($request->columns);
        $properties = json_decode($request->properties);
        set_time_limit(300);
        $pdf = PDF::loadView('company.custom-modules-form.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait');
        $download = $pdf->download($pageTitle . '.pdf');
        return $download;

//        dd($request->all());
//        $custom_module = CustomModule::with('customFields')->where('name', $request->pageTitle)->first();
//        $data_with_images = [];
//        if (isset($request->exportedData)) {
//            $getExportData = json_decode($request->exportedData)->data;
//            foreach ($getExportData as $data) {
//                $data_with_images[] = Arr::except((array)$data, ['action']);
//            }
//        }
//
//
//        if (empty($data_with_images)) {
//            Session::flash('message', 'No Data to print');
//            return back();
//        }
//        //filter get the name of fields having type multiple images
//        $multiple_image_types = array();
//        foreach ($custom_module->customFields as $multiple_image_type) {
//            if ($multiple_image_type->type == 'Multiple Images') {
//                $multiple_image_types[] = $multiple_image_type->slug;
//            }
//        }
//
//        $multiple_image_types[] = 'id';
//
//        // remove all fields having multiple images as it needs lots of workk in pdf to show
//        $main_data = array();
//        foreach ($data_with_images as $data) {
//            $main_data[] = collect($data)->except($multiple_image_types)->toArray();
//        }
//
//
//        $pageTitle = $request->pageTitle;
//        set_time_limit(300);
//        $pdf = PDF::loadView('company.custom-modules-form.exportpdf', compact('main_data', 'pageTitle'))->setPaper('a4', 'portrait');
//        $download = $pdf->download($pageTitle . '.pdf');
//        return $download;
    }

    private function checkIfRequestIsEmpty($requestData)
    {
        $isRequestEmpty = array();
        foreach ($requestData as $key => $data) {
            if ($key != '_token' && $key != '_method') {
                $isRequestEmpty[] = $data;
            }
        }
        return count(array_filter($isRequestEmpty)) == 0;
    }

    public function makeDataReadable($data, $slug, $customModule)
    {
        $columnTypes = CustomModuleField::where('custom_module_id', $customModule->id)->where('slug', $slug)->get();
        foreach ($columnTypes as $columnType) {
            switch ($columnType->type) {
                case 'Text':
                case 'Radio Button':
                case 'Numerical':
                case 'Large text':
                case 'Phone':
                case 'Time':
                case 'Time range':
                case 'Address':
                case 'Single option':
                    return $data->{$slug};
                case 'Date range':

                    return getDeltaRange($data->{$slug});
                case 'Date':
                    if (isset($data->{$slug}) && !empty($data->{$slug})) {
                        return getDeltaDate(date('Y-m-d', strtotime($data->{$slug})));
                    }
                    return '';

                case 'Check Box':
                case 'Multiple options':
                    return json_decode($data->{$slug});
                case 'Monetary':
                    if (!empty($data->{$slug})) {
                        $explode = explode(' ', $data->{$slug});
                        if (isset($explode[1])) {
                            $currencyCode = Currency::find($explode[0]);
                            $salary = $currencyCode->code . ' ' . $explode[1];
                        } else {
                            $salary = '';
                        }
                    } else {
                        $salary = '';
                    }
                    return $salary;

                case 'File':
                    $file = [];
                    if (isset($data->{$slug})) {
                        $arrayMultiple = (array)json_decode($data->{$slug});

                        if (!is_null($arrayMultiple)) {
                            foreach ($arrayMultiple as $key => $file) {
                                $file = '<div class="col-xs-12">
                                        <span><a style="width:100px;" href="' . asset('cms/') . $file[0] . '" target="_blank">' . $key . '</a></span><br><br>
                                        </div>';
                            }
                        }
                    } else {
                        $file = '';
                    }
                    return $file;

                case 'Multiple Images':
                    $photos = [];
                    if (isset($data->{$slug})) {
                        $arrayMultiple = (array)json_decode($data->{$slug});
                        if (!is_null($arrayMultiple)) {
                            foreach ($arrayMultiple as $key => $image) {
                                $absPath = URL::asset('cms/' . $image[0]);
                                $photos[] = "<img src='" . $absPath . "' class='direct-chat-gotimg' alt='custom module Image' ><br>";
                            }
                        } else {
                            $photos[] = '';
                        }
                    }
                    return $photos;

                case 'User':
                    if (!empty($data->{$slug})) {
                        $employee = Employee::withTrashed()->find($data->{$slug});
                        $employee_show = domain_route('company.admin.employee.show', [$employee->id]);
                        $juniors = Employee::EmployeeChilds(Auth::user()->EmployeeId(), array());
                        if ($employee->deleted_at) {
                            $value = $employee->name;
                        } else {
                            if (Auth::user()->isCompanyManager() || in_array($employee->id, $juniors)) {
                                $value = "<a href='{$employee_show}' datasalesman='{$employee->name}'> {$employee->name}</a>";

                            } else {
                                $value = '<a href="#" class="alert-modal" datasalesman="' . $employee->name . '">' . $employee->name . '</a>';
                            }
                        }
                    }else{
                        $value = '';
                    }
                    return $value;
                case 'Party':
                    if (!empty($data->{$slug})) {
                        $client = Client::withTrashed()->find($data->{$slug});
                        $access = Auth::user()->handleQuery('client')->orderBy('company_name', 'asc')->pluck('id')->toArray();

                        if ($client->deleted_at) {
                            $value = $client->company_name;
                        } else {
                            if (in_array($data->{$slug}, $access)) {
                                $client_url = domain_route('company.admin.client.show', [$client->id]);
                                $value = "<a href='{$client_url}' > {$client->company_name}</a>";
                            } else {
                                $value = '<a href="#" class="party-modal" datasalesman="' . $client->company_name . '">' . $client->company_name . '</a>';
                            }
                        }

                    }else{
                        $value = '';
                    }
                    return $value;
                default:
                    break;
            }
        }
    }

    public function validatePhone(Request $request)
    {

        if (!isset($request->phone)) {
            return response()->json([
                'msg' => 'Success',
                'statusCode' => 200
            ]);
        }

        $customMessages = [
            'phone.digits_between' => 'Mobile Number should be between 7 to 20 digits',
            'phone.min' => 'Phone Number should be between 7 to 20 digits',
            'phone.max' => 'Phone Number should be between 7 to 20 digits',
            'phone.regex' => 'Phone Number should not contain non-digit character',

        ];
        $regexphone = "/^([0-9\s\-\+\/\(\)]*)$/|min:7|max:20";

        $this->validate($request, [
            'phone' => 'sometimes|regex:' . $regexphone,
        ], $customMessages);


        return response()->json([
            'msg' => 'Success',
            'statusCode' => 200
        ]);
    }

}
