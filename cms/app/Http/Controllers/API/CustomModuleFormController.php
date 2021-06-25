<?php

namespace App\Http\Controllers\API;

use App\Client;
use App\Currency;
use App\Employee;
use App\CustomModule;
use App\CustomModuleField;
use App\Repository\CustomModuleUpdate;
use App\Repository\CustomModuleUpdateApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use App\Repository\CustomModuleCheckApi;

class CustomModuleFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');

    }

    public function index(Request $request)
    {
        $id = $request->id;
        $employee_id = Employee::where('user_id', Auth::user()->id)->value('id');
        $custom_module = CustomModule::find($id);
        $juniors = Employee::employeeChilds($employee_id, array());
        $custom_modules = DB::table($custom_module->table_name)->whereIn('employee_id', $juniors)->get()->map(function ($module) use ($id) {
            return $this->formatCustomModule($module, $id);
        });

        return response()->json([
            'msg' => $custom_modules,

        ]);
    }

    public function formatCustomModule($object, $id)
    {
        foreach ($object as $slug => $data) {
            $module_type = CustomModuleField::where('custom_module_id', $id)->where('slug', $slug)->first();
            if ($module_type) {
                $type = $module_type->type;
                if (empty($data)) continue;
                if ($type != "Multiple Images" && $type != "File") continue;

                $decodedData = json_decode($data);
                $keys = array();
                foreach ($decodedData as $key => $value) {
                    $temp['image'] = $key;
                    array_push($keys, $temp);
                }
                $object->$slug = json_encode($keys);
            }
        }
        return $object;
    }

    public function create(Request $request)
    {
        return response()->json([
            'custom_fields' => CustomModuleField::where('custom_module_id', $request->id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        if ($this->checkIfRequestIsEmpty($request->all())) {
            return response([
                'message' => 'Save failed ! Nothing to save'
            ]);
        }


        $user = Auth::user();
        $employee = Employee::where('company_id', $user->company_id)->where('user_id', $user->id)->first();

        $customModule = CustomModule::with('customFields')->find($request->id);

        if (!$customModule) {
            return response()->json(['message' => 'No Custom Module Found']);
        }

        if ($customModule->customFields->count()) {
            $processedValues = collect((new  CustomModuleCheckApi($customModule->customFields, $request, null, $user->company_id))->check());
        }
        $extra_data = collect(['created_at' => now(), 'updated_at' => now(), 'user_id' => $user->id, 'employee_id' => $employee->id, 'company_id' => $user->company_id]);

        $main_data = $customModule->customFields->map(function ($field) use ($processedValues) {
            return [$field->slug => isset($processedValues[$field->id]) ? $processedValues[$field->id] : ''];
        })->collapse()->merge($extra_data)->toArray();

        DB::table($customModule->table_name)->insert([
            $main_data
        ]);

        return response()->json(['message' => 'Created Successfully']);
    }

    public function edit(Request $request)
    {
        $custom_module = CustomModule::find($request->id);

        $custom_fields = CustomModuleField::where('custom_module_id', $custom_module->id)->get();

        $form_data = DB::table($custom_fields->table_name)->find($request->data_id);

        if (is_null($form_data)) {
            return response()->json([
                'message' => 'Deleted Successfully'
            ]);
        }

        return response()->json([
            'custom_fields' => $custom_fields,
            'form_data' => $form_data,
        ]);
    }

    public function show(Request $request)
    {
        $customModule = CustomModule::with('customFields')->find($request->id);

        $form_data = DB::table($customModule->table_name)->where('id', $request->data_id)->get();

        if (is_null($form_data)) {
            return response()->json([
                'message' => 'data does not exists'
            ]);
        }

        return view('company.custom-modules-form.view', [
            'customModule' => $customModule,
            'datas' => $form_data
        ]);


    }

    public function update(Request $request)
    {
        if ($this->checkIfRequestIsEmpty($request->all())) {
            return response([
                'message' => 'Save failed ! Nothing to save'
            ]);
        }
        $user = Auth::user();
        $employee = Employee::where('company_id', $user->company_id)->where('user_id', $user->id)->first();


        $customModule = CustomModule::with('customFields')->find($request->id);
        $data = (array)DB::table($customModule->table_name)->find($request->data_id);

        if (!$customModule) {
            return response([
                'message' => 'this custom module doesnt exist'
            ]);
        }

        if ($customModule->customFields->count()) {
            $processedValues = collect((new  CustomModuleUpdateApi($customModule->customFields, $request, $data, $user->company_id))->check());
        }

        $extra_data = collect(['created_at' => now(), 'updated_at' => now(), 'employee_id' => $employee->id, 'company_id' => $user->company_id]);

        $main_data = $customModule->customFields->map(function ($field) use ($processedValues) {
            return [$field->slug => isset($processedValues[$field->id]) ? $processedValues[$field->id] : ''];
        })->collapse()->merge($extra_data)->filter()->toArray();


        DB::table($customModule->table_name)->where('id', $request->data_id)->update($main_data);

        return response([
            'message' => 'Updated Successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        $customModule = CustomModule::findorFail($request->id);
        DB::table($customModule->table_name)->where('id', $request->data_id)->delete();
        return response([
            'message' => 'Deleted Successfully'
        ]);
    }

    private function checkIfRequestIsEmpty($requestData)
    {
        $isRequestEmpty = array();
        foreach ($requestData as $key => $data) {
            if ($key != 'id') {
                $isRequestEmpty[] = $data;
            }
        }
        return count(array_filter($isRequestEmpty)) == 0;
    }

}
