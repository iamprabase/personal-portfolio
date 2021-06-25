<?php

namespace App\Http\Controllers\Company\Admin;

use App\CustomModule;
use App\CustomModuleField;
use App\Jobs\AddColumnToDatabaseJob;
use App\Jobs\RemoveCustomModuleColumnJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CustomModuleFieldController extends Controller
{
    public $company_id;

    public function __construct()
    {
        $this->middleware('auth');
        $this->company_id = config('settings.company_id');
    }

    public function store(Request $request)
    {

        $customModuleSlug = CustomModuleField::where('custom_module_id', $request->module)->pluck('slug')->toArray();

        if (strlen($this->slug($request->title)) == 0) {
            return response()->json(['error' => 'false', 'message' => 'Title cannot contains special characters only']);
        }

        if (is_numeric($this->checkIfNumericalOnly($request->title))){
            return response()->json(['error' => 'false', 'message' => 'Title cannot contains numerical and special characters only']);

        }

        if ($request->type == 'File') {
            $customModuleFile = CustomModuleField::where('custom_module_id', $request->module)->where('type', 'File')->count();
            if ($customModuleFile > 2) {
                return response()->json(['error' => 'false', 'message' => 'Only three file fields can be created.']);
            }
        }
        if ($request->type == 'Multiple Images') {
            $customModuleType = CustomModuleField::where('custom_module_id', $request->module)->where('type', 'Multiple Images')->count();
            if ($customModuleType > 2) {
                return response()->json(['error' => 'false', 'message' => 'Only three image fields can be created.']);
            }
        }

        if ($request->type == 'Address') {
            $customModuleType = CustomModuleField::where('custom_module_id', $request->module)->where('type', 'Address')->count();
            if ($customModuleType >= 1) {
                return response()->json(['error' => 'false', 'message' => 'Only single Address field can be created.']);
            }
        }

        if (in_array($this->slug($request->title), $customModuleSlug)) {
            return response()->json(['error' => 'false', 'message' => 'Same Field Name Already exists']);
        }

//        if ($this->optionalFieldContainsSpecialCharacters($request)) {
//            return response()->json(['error' => 'false', 'message' => 'custom module field should contain letters and numbers only']);
//        }

        if ($this->optionalFieldContainsSingleOption($request)) {
            return response()->json(['error' => 'false', 'message' => 'Contains Only single Options']);
        }

        if ($this->optionalFieldContainsSameOptions($request)) {
            return response()->json(['error' => 'false', 'message' => 'Contains multiple same options']);
        }

        try {
            DB::BeginTransaction();
            $customModuleField = new CustomModuleField();
            $customModuleField->custom_module_id = $request->module;
            $customModuleField->type = $request->type;
            $customModuleField->required = $request->is_mandatory == 'true' ? 1 : 0;
            $customModuleField->title = Str::ucfirst($request->title);
            $customModuleField->slug = $this->slug($request->title);
            $customModuleField->order = count($customModuleSlug) + 1;
            $this->checkOptions($request, $customModuleField);
            $customModuleField->save();
            AddColumnToDatabaseJob::dispatch($customModuleField);

            $tablename = CustomModule::find($customModuleField->custom_module_id)->table_name;

            if (!Schema::hasColumn($tablename, $customModuleField->slug)){
                $customModuleField->forceDelete();
                Session::flash('success', 'Failed due to internal error, please try again');
            }
            DB::commit();

            $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
            $dataPayload = array("data_type" => "Custom Module Field", "custom_module_field" => $customModuleField, "action" => "custom module field added");
            $sent = sendPushNotification_($fbIDs, 45, null, $dataPayload);

            Session::flash('success', 'Created Successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('success', 'Failed to create field');
        }
    }

    public function update(Request $request)
    {

//        $message = [
//            'title.regex' => 'custom module field should contain letters and numbers only',
//            'title' => 'custom module field name is required'
//        ];
//        $validator = Validator::make($request->all(), [
//            'title' => ['required', 'min:2', 'max:50', 'regex:/^[A-Za-z _]*[A-Za-z][A-Za-z0-9 _]*$/'],
//        ], $message);
//
//        if ($validator->fails()) {
//            return response()->json(['error' => 'false', 'message' => $validator->errors()]);
//        }

        $customModuleTitle = CustomModuleField::where('custom_module_id', $request->module_id)->where('id', '!=', $request->id)->pluck('title')->toArray();

        if (in_array($request->title, $customModuleTitle)) {
            return response()->json(['error' => 'false', 'message' => 'Same Field Name Already exists']);
        }

//        if ($this->optionalFieldContainsSpecialCharacters($request)) {
//            return response()->json(['error' => 'false', 'message' => 'custom module field should contain letters and numbers only']);
//        }

        if ($this->optionalFieldContainsSingleOption($request)) {
            return response()->json(['error' => 'false', 'message' => 'Contains Only single Option']);
        }

        if ($this->optionalFieldContainsSameOptions($request)) {
            return response()->json(['error' => 'false', 'message' => 'Contains multiple same options']);
        }

        $customModuleField = CustomModuleField::find($request->id);
        $customModuleField->title = $request->title;
        $customModuleField->required = $request->is_mandatory == 'true' ? 1 : 0;
        $this->checkOptions($request, $customModuleField);
        $customModuleField->save();

        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "Custom Module Field", "custom_module_field" => $customModuleField->fresh(), "action" => "custom module field update");
        $sent = sendPushNotification_($fbIDs, 45, null, $dataPayload);
        Session::flash('success', 'Updated Successfully');

    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        $customModuleField = CustomModuleField::find($request->id);
        RemoveCustomModuleColumnJob::dispatch($customModuleField);
        $customModuleField->forceDelete();
        DB::commit();
        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "Custom Module Field", "custom_module_field" => $customModuleField->id, "action" => "custom module field deleted");
        $sent = sendPushNotification_($fbIDs, 45, null, $dataPayload);
        Session::flash('success', 'Deleted Successfully');
        return back();
    }

    public function changeStatus(Request $request)
    {
        $custom_field = CustomModuleField::FindOrFail($request->custom_module_id);
        $module = CustomModule::FindOrFail($custom_field->custom_module_id);

        if ($module->status === 'Inactive') {
            Session::flash('success', 'Status Cannot be changed as main module is disabled');
            return back();
        }
        $custom_field->status = $request->status == 'Active' ? 1 : 0;
        $custom_field->save();

        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "Custom Module Field", "custom_module_field" => $custom_field->fresh(), "action" => "custom module field updated");
        $sent = sendPushNotification_($fbIDs, 45, null, $dataPayload);

        Session::flash('success', 'Updated Successfully');
        return redirect()->route('company.admin.custom.modules.edit', ['domain' => domain(), 'id' => $module->id]);

    }

    public function updateOrder(Request $request)
    {
        $fields = CustomModuleField::where('company_id', $this->company_id)->where('custom_module_id', $request->module_id)->get();

        $positionUpdated = array();
        foreach ($fields as $field) {
            $field->timestamps = false; // To disable updated_at field update
            $id = $field->id;

            foreach ($request->position as $pos) {
                if ($pos['id'] == $id) {
                    $field->update(['order' => $pos['order']]);
                    $positionUpdated[] = $field->fresh();
                }
            }
        }

        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $dataPayload = array("data_type" => "Custom Module Field", "custom_module_field" => $positionUpdated, "action" => "custom module field position updated");
        $sent = sendPushNotification_($fbIDs, 45, null, $dataPayload);


        Session::flash('success', 'Updated Successfully');
        return response()->json([
            'message' => 'Updated Successfully',
            'status' => 200
        ]);
    }

//    private function optionalFieldContainsSpecialCharacters($request)
//    {
//        $fields = ['Single option', 'Multiple options', 'Check Box', 'Radio Button'];
//        $response = array();
//        if (in_array($request->type, $fields)) {
//            foreach (array_filter($request->options, 'strlen') as $option) {
//                $response[] = !preg_match('/^[A-Za-z0-9 _]*[A-Za-z0-9][A-Za-z0-9 _]*$/', $option);
//            }
//        }
//        return in_array(true, $response);
//    }

    private function optionalFieldContainsSingleOption($request)
    {
        $fields = ['Single option', 'Multiple options', 'Check Box', 'Radio Button'];

        if (in_array($request->type, $fields)) {
            if (count(array_filter($request->options, 'strlen')) < 2) {
                return true;
            }
        }
    }

    private function optionalFieldContainsSameOptions($request)
    {

        $fields = ['Single option', 'Multiple options', 'Check Box', 'Radio Button'];

        if (in_array($request->type, $fields)) {
            if (count($request->options) !== count(array_unique($request->options))) {
                return true;
            }
        }

    }

    private function checkOptions($request, $customfield)
    {
        $type = ['Single option', 'Multiple options', 'Check Box', 'Radio Button'];

        if (in_array($request->type, $type)) {
            $customfield->options = $this->serializeOptionField($request->options);
        }
    }

    private function serializeOptionField($options)
    {
        return json_encode(array_filter($options, 'strlen'));
    }

    private function slug($title)
    {
        $title = collect(preg_split('/[!@#$%^&*()_+|?":,.~`=-><;\/]/', $title))->filter()->implode('_');
        return Str::slug($title, '_');
    }

    private function checkIfNumericalOnly($title)
    {
        return collect(preg_split('/[!@#$%^&*()_+|?":,.~`=-><;\/]/', $title))->filter()->implode('');

    }

}
