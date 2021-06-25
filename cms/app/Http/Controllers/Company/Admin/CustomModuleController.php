<?php

namespace App\Http\Controllers\Company\Admin;

use App\CustomModule;
use App\CustomModuleField;
use App\Employee;
use App\Jobs\CreateDatabaseTableFromCustomModuleJob;
use App\Jobs\DropCustomModuleTableJob;
use App\PartyType;
use App\PermissionCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CustomModuleController extends Controller
{

    public $company_id;
    public $indexingPriority;

    public function __construct()
    {

        $this->middleware('auth');
        $this->middleware('permission:Custom-Module-view')->except('getCustomModules');
        $this->company_id = config('settings.company_id');
        $this->indexingPriority = PermissionCategory::whereName('Custom_Module')->value('indexing_priority');
    }

    public function index()
    {
        return view('company.custom-modules.index', [
            'custom_modules' => CustomModule::where('company_id', $this->company_id)
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }

    public function ajaxDatatable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'user_id',
            3 => 'created_at',
            4 => 'status',
            5 => 'action',
        );

        $start = $request->input('start');
        $limit = $request->input('length');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');


        $prepQuery = CustomModule::where('company_id', $this->company_id);

        $totalData = $prepQuery->count();
        $totalFiltered = $totalData;

        if (empty($request->input('search.value'))) {
            $totalFiltered = $prepQuery->count();
            $customModules = $prepQuery
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get(['id', 'name', 'status', 'user_id', 'created_at', 'employee_id']);
        } elseif (!(empty($request->input('search.value')))) {

            $search = $request->input('search.value');

            $customModulesSearchQuery = $prepQuery
                ->where(function ($query) use ($search) {
                    $query->orWhere('name', 'LIKE', "%{$search}%");
                    $query->orWhere('status', 'LIKE', "%{$search}%");
                })->orWherehas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            $totalFiltered = $customModulesSearchQuery->count();
            $customModules = $customModulesSearchQuery
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get(['id', 'name', 'status', 'user_id', 'created_at', 'employee_id']);
        }
        $data = array();
        if (!empty($customModules)) {
            $i = $start;
            $getCustomModules = CustomModule::where('company_id', $this->company_id)->pluck('id')->toArray();
            foreach ($customModules as $module) {

                $employee = Employee::find($module->employee_id);
                $id = $module->id;
                $status = $module->status;
                $edit = domain_route('company.admin.custom.modules.edit', [$id]);
                $delete = domain_route('company.admin.custom.modules.destroy', [$id]);
                $editName = domain_route('company.admin.custom.modules.changeName', [$id]);
                $employee_show = domain_route('company.admin.employee.show', [$employee->id]);

                $module_view = domain_route('company.admin.custom.modules.form.index', [$module->id]);

                $nestedData['id'] = ++$i;
                $nestedData['name'] = ($module->status == 'Inactive' ? "<a href='{$module_view}'>{$module->name}</a>" : $module->name);
                $nestedData['user_id'] = "<a href='{$employee_show}' datasalesman='{{$employee->name}}'> {$employee->name}</a>";
                $nestedData['created_at'] = getDeltaDate(date('Y-m-d', strtotime($module->created_at)));
                $nestedData['created_at'] = getDeltaDate(date('Y-m-d', strtotime($module->created_at)));
                if ($status == 'Active')
                    $spanTag = "<span class='label label-success'>$status</span>";
                elseif ($status == 'Inactive')
                    $spanTag = "<span class='label label-warning'>$status</span>";

                $nestedData['status'] = "<a href='#' class='edit-modal' data-id='$id' data-status='$status'>{$spanTag}</a>";

                if (in_array($id, $getCustomModules)) {
                    $deleteBtn = "<a class='btn btn-danger btn-sm delete del-modal' data-mid='$id' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash'></i></a>";
                } else {
                    $deleteBtn = null;
                }
                $nestedData['action'] = "<a href='#' class='btn btn-warning btn-sm edit-name-modal '  data-id='$id' data-url='$editName' data-name='$module->name'><i class='fa fa-edit'></i></a><a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-cog'></i></a>{$deleteBtn} ";

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        return json_encode($json_data);
    }

    public function create()
    {
        return redirect()->route('company.admin.custom.modules', domain());
    }


    public function store(Request $request)
    {
        $customModules = CustomModule::whereCompanyId($this->company_id)->pluck('name')->toArray();

        if (strlen($this->name($request->name)) == 0) {
            Session::flash('error', 'Failed, Module name cannot be created with special characters only');
            return back();
        }


        if (is_numeric($this->checkIfNumericalOnly($request->name))){
            Session::flash('error', 'Failed, Module name cannot be created with numerical  and special charactes only');
            return back();
        }

        if (count($customModules) && in_array($request->name, $customModules)) {
            Session::flash('error', 'Failed, Module name cannot be created, try again with new name');
            return back();
        }

        if (Schema::hasTable($this->tableName($request->name))) {
            Session::flash('error', 'Failed, Module name cannot be created, try again with new name');
            return back();
        }

        try {
            DB::BeginTransaction();

            $custom_module = CustomModule::create([
                'name' => $request->name,
                'table_name' => $this->tableName($request->name),
            ]);

            CreateDatabaseTableFromCustomModuleJob::dispatchNow($custom_module);

            $permissionCategory = new PermissionCategory;
            $permissionCategory->company_id = $this->company_id;
            $permissionCategory->permission_model_id = $custom_module->id;
            $permissionCategory->permission_category_type = 'Company';
            $permissionCategory->name = $this->tableName($request->name);
            $permissionCategory->display_name = 'Custom Module: ' . $custom_module->name;
            $permissionCategory->indexing_priority = ($this->indexingPriority + 5);
            $permissionCategory->save();

            $create_id = $this->addPermission($custom_module, $permissionCategory, 'create');
            $view_id = $this->addPermission($custom_module, $permissionCategory, 'view');
            $update_id = $this->addPermission($custom_module, $permissionCategory, 'update');
            $delete_id = $this->addPermission($custom_module, $permissionCategory, 'delete');
            $status_id = $this->addPermission($custom_module, $permissionCategory, 'status');


            DB::Commit();

            $module_type_permissions = array($create_id, $view_id, $update_id, $delete_id, $status_id);
            $module_type_permissions_for_other_roles = array($create_id, $view_id);

            $roles = Role::whereCompanyId($this->company_id)->whereGuardName('web')->get();
            foreach ($roles as $role) {
                if ($role->name == 'Full Access') {
                    $exist_role_permissions = DB::table('role_has_permissions')->whereRoleId($role->id)->pluck('permission_id')->toArray();
                    $non_exist_role_permission = array_merge($module_type_permissions, $exist_role_permissions);
                    if (!empty($non_exist_role_permission)) {
                        $role->syncPermissions($non_exist_role_permission);
                    }
                } else {
                    $exist_role_permissions = DB::table('role_has_permissions')->whereRoleId($role->id)->pluck('permission_id')->toArray();
                    $non_exist_role_permission = array_merge($module_type_permissions_for_other_roles, $exist_role_permissions);
                    $role->syncPermissions($non_exist_role_permission);
                }
                $data = array();

                $permissions = Permission::where('is_mobile', 1)->where('permission_category_id', $permissionCategory->id)->where('permission_type', 'Company')->where('company_id', $this->company_id)->get();

                foreach ($permissions as $permission) {
                    $data['pt-' . $permission->name] = ($role->hasPermissionTo($permission->id)) ? '1' : '0';
                }

                $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('role', $role->id)->pluck('firebase_token');
                $dataPayload = array("data_type" => "Custom Module", "custom_module" => $custom_module->fresh(), "permissions" => $data, "action" => "custom module added", 'role_id' => $role->id);
                $sent = sendPushNotification_($fbIDs, 45, null, $dataPayload);

            }
            Session::flash('success', 'Created Successfully');
            return redirect()->route('company.admin.custom.modules', domain());

        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Error : ' . $e->getMessage()], 500);
        }
    }


    public function edit(Request $request)
    {
        $module = CustomModule::find($request->id);

        if ($module == null) {
            return redirect()->route('company.admin.home', domain())
                ->with('errors', 'Error');
        }

        return view('company.custom-modules.edit', [
            'module' => $module,
            'custom_modules_field' => CustomModuleField::where('company_id', $this->company_id)
                ->where('custom_module_id', $request->id)
                ->orderBy('order', 'asc')
                ->get(),
        ]);
    }

    public function destroy(Request $request)
    {
        $module = CustomModule::with('customFields')->findOrFail($request->id);

        if ($module->company_id == $this->company_id) {
            $permissionCategory = PermissionCategory::where('permission_model', NULL)
                ->where('permission_model_id', $module->id)->first();
            $this->destroyPermission($permissionCategory, 'create');
            $this->destroyPermission($permissionCategory, 'view');
            $this->destroyPermission($permissionCategory, 'update');
            $this->destroyPermission($permissionCategory, 'delete');
            $this->destroyPermission($permissionCategory, 'status');

            $permissionCategory->delete();
            DropCustomModuleTableJob::dispatch($module->table_name);
            $module->customFields()->each(function ($field) {
                $field->forceDelete();
            });
            $module->forceDelete();

            $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "Custom Module", "custom_module" => $module->id, "action" => "custom module deleted");
            sendPushNotification_($fbIDs, 45, null, $dataPayload);

            Session::flash('success', 'Deleted Successfully');
            return back();
        }
    }

    public function changeStatus(Request $request)
    {
        $module = CustomModule::findOrFail($request->custom_module_id);
        if ($request->status === $module->status) {
            return back();
        }
        $module = CustomModule::findOrFail($request->custom_module_id);
        $module->status = $request->status == 'Active' ? 1 : 0;
        $module->save();

        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "Custom Module", "custom_module" => $module->id, "action" => "custom module updated");
        sendPushNotification_($fbIDs, 45, null, $dataPayload);

        Session::flash('success', 'Updated Successfully');
        return redirect()->route('company.admin.custom.modules', ['domain' => domain()]);
    }

    public function changeName(Request $request)
    {
        $customModules = CustomModule::whereCompanyId($this->company_id)->where('id', '!=', $request->module_id)->pluck('name')->toArray();

//        $message = [
//            'name.regex' => 'custom module should contain letters and numbers only'
//        ];
//        $request->validate([
//            'name' => ['required', 'min:3', 'max:50', 'regex:/^[A-Za-z _]*[A-Za-z][A-Za-z0-9 _]*$/']
//        ], $message);

        if (count($customModules) && in_array($request->name, $customModules)) {
            Session::flash('error', 'Failed, Name Cannot be changed, try again with new name');
            return back();
        }

        try {
            DB::BeginTransaction();

            $customModule = CustomModule::find($request->module_id);

            if ($request->name == $customModule->name) {
                Session::flash('success', 'Updated Successfully');
                return redirect()->route('company.admin.custom.modules', domain());
            }

            $oldcustomModule = $customModule->replicate();

            $customModule->update(['name' => $request->name]);

            $permissionCategory = PermissionCategory::where('permission_model', Null)
                ->where('permission_model_id', $customModule->id)->first();

            if (!$permissionCategory) {
                $permissionCategory = new PermissionCategory;
                $permissionCategory->company_id = $this->company_id;
                $permissionCategory->permission_model_id = $customModule->id;
                $permissionCategory->permission_category_type = 'Company';
                $permissionCategory->name = $this->tableName($request->name);
                $permissionCategory->display_name = 'Custom Module: ' . $customModule->name;
                $permissionCategory->indexing_priority = ($this->indexingPriority + 5);
                $permissionCategory->save();
            }
            $oldPermissionCategory = $permissionCategory;

            $permissionCategory->name = $this->tableName($request->name);

            $permissionCategory->display_name = 'Custom Module: ' . $request->name;

            $permissionCategory->indexing_priority = ($this->indexingPriority + 5);

            $permissionCategory->save();
            $this->updatePermission($customModule, $oldcustomModule, $oldPermissionCategory, 'create');
            $this->updatePermission($customModule, $oldcustomModule, $oldPermissionCategory, 'view');
            $this->updatePermission($customModule, $oldcustomModule, $oldPermissionCategory, 'update');
            $this->updatePermission($customModule, $oldcustomModule, $oldPermissionCategory, 'delete');
            $this->updatePermission($customModule, $oldcustomModule, $oldPermissionCategory, 'status');

            DB::Commit();

//            $name = str_replace(' ', '-', $permissionCategory->name);
//            $permissions = Permission::where('is_mobile', 1)->where('permission_category_id', $oldPermissionCategory->id)->where('permission_type', 'Custom Module')->where('name', 'LIKE', $name . '-%')->where('company_id', $this->company_id)->get();
//
//            $pdata = array();
//
//            $allroles_with_permission = DB::table('role_has_permissions')->whereIn('permission_id', $permissions->pluck('id')->toArray())->pluck('role_id')->toArray();
//
//            $roles_with_permission = Role::whereIn('id', $allroles_with_permission)->get();
//
//
//            if ($roles_with_permission->first()) {
//                $permissions_data = array();
//                if ($permissions->first()) {
//                    foreach ($roles_with_permission as $role) {
//                        foreach ($permissions as $permission) {
//                            $pdata['pt -' . $permission->name] = ($role->hasPermissionTo($permission->id)) ? '1' : '0';
//                        }
//                        $permissions_data[$role->id] = $pdata;
//
//                        $permissions = Permission::where('is_mobile', 1)->where('permission_category_id', $permissionCategory->id)->where('permission_type', 'Custom Module')->where('company_id', $this->company_id)->get();
//
//                        foreach ($permissions as $permission) {
//                            $data['pt-' . $permission->name] = ($role->hasPermissionTo($permission->id)) ? '1' : '0';
//                        }
//
//                        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->where('role', $role->id)->pluck('firebase_token');
//                        $dataPayload = array("data_type" => "Custom Module", "custom_module" => $customModule->fresh(), "permissions" => $data, "action" => "custom module updated", 'role_id' => $role->id);
//                        $sent = sendPushNotification_($fbIDs, 45, null, $dataPayload);
//                    }
//                }
//            }

            $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "Custom Module", "custom_module" => $customModule, "action" => "custom module updated");
            sendPushNotification_($fbIDs, 45, null, $dataPayload);
            Session::flash('success', 'Updated Successfully');
            return redirect()->route('company.admin.custom.modules', domain());

        } catch (\Exception $e) {
            DB::rollback();

            Log::info($e);
            Session::flash('success', 'Custom module updated Failed');
            return redirect()->route('company.admin.custom.modules', domain());
        }

    }

    public function getCustomModules(Request $request)
    {

        $customModules = CustomModule::select('id', 'name')->where('company_id', $this->company_id)->where('status', 1)->orderBy('id', 'DESC')->get();
        foreach ($customModules as $modules) {
            $stringName = str_replace(' ', '-', $modules->name) . '-' . $this->company_id . '-view';
            $permission = Permission::where('company_id', $this->company_id)->where('name', $stringName)->first();
            if ($permission) {
                if (Auth::user()->hasPermissionTo($permission->id)) {
                    $modules->can = true;
                } else {
                    $modules->can = false;
                }
            } else {
                $modules->can = false;
            }
        }
        return $customModules;
    }

    private function tableName($name)
    {
        $name = collect(preg_split('/[!@#$%^&*()_+|?":,.~`=-><;\/]/', $name))->filter()->implode('_');
        return Str::slug($name, '_') . '_' . $this->company_id;
    }

    private function checkIfNumericalOnly($name){
        return collect(preg_split('/[!@#$%^&*()_+|?":,.~`=-><;\/]/', $name))->filter()->implode('');

    }

    private function name($name)
    {
        return collect(preg_split('/[!@#$%^&*()_+|?":,.~`=-><;\/]/', $name))->filter()->implode('_');
    }

    private function addPermission($partytype, $permissionCategory, $permissionTag)
    {
        $stringName = str_replace(' ', '-', $partytype->name);
        $permission = new Permission;
        $permission->permission_category_id = $permissionCategory->id;
        $permission->company_id = config('settings.company_id');
        $permission->name = $stringName . '-' . $partytype->company_id . '-' . $permissionTag;
        $permission->guard_name = 'web';
        $permission->permission_type = 'Company';
        if ($permissionTag != 'status') {
            $permission->enabled = 1;
            $permission->is_mobile = 1;
        } else {
            $permission->enabled = 0;
            $permission->is_mobile = 0;
        }
        $permission->save();

        return $permission->id;
    }

    private function updatePermission($customModule, $oldcustomModule, $permissionCategory, $permissionTag)
    {
        $stringName = str_replace(' ', '-', $oldcustomModule->name) . '-' . $customModule->company_id . '-' . $permissionTag;
        $permission = Permission::where('company_id', $this->company_id)->where('permission_category_id', $permissionCategory->id)
            ->where('name', $stringName)->first();
        if (!$permission) {
            $this->addPermission($customModule, $permissionCategory, $permissionTag);
        } else {
            $permission->name = str_replace(' ', '-', $customModule->name) . '-' . $customModule->company_id . '-' . $permissionTag;
            $permission->save();
        }
    }

    private function destroyPermission($permissionCategory, $permissionTag)
    {
        $company_id = config('settings.company_id');
        $permission = Permission::where('company_id', $company_id)->where('permission_category_id', $permissionCategory->id)
            ->first();

        if ($permission) {
            DB::table('role_has_permissions')->where('permission_id', $permission->id)->delete();
            $permission->delete();
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

}
