<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;
use App\Task;
use App\Employee;
Use Auth;
use DB;
use Log;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_id = config('settings.company_id');
        $tasks = Task::where('company_id', $company_id)
            ->orderBy('created_at', 'desc') 
            ->get();
        return view('company.tasks.index', compact('tasks'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company_id = config('settings.company_id');
        $clients = Client::select('company_name', 'id')->where('company_id', $company_id)->where('status', 'Active')->orderBy('company_name', 'asc')->get();
        $employees = Employee::select('name', 'id')->where('company_id', $company_id)->where('status', 'Active')->orderBy('name', 'asc')->get();
        return view('company.tasks.create', compact('employees', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'title' => 'required',
            'due_date' => 'required',
            'assigned_from' => 'required',
            'assigned_to' => 'required',
            // 'start_date' =>  'required',
            // 'end_date' =>  'required',
            'description' => 'required',
        ]);

        $company_id = config('settings.company_id');
        $tasks = new \App\Task;
        $tasks->company_id = $company_id;
        $tasks->title = $request->get('title');
        if ($request->assigned_from == 'admin') {
            $tasks->assigned_from = Auth::user()->EmployeeId();
            $tasks->assigned_from_type = "Admin";
        } else {
            $tasks->assigned_from = $request->get('assigned_from');
            $tasks->assigned_from_type = "Employee";
        }
        $tasks->assigned_to = $request->get('assigned_to');
        // $tasks->start_date=$request->get('start_date');
        // $tasks->end_date=$request->get('end_date');
        $tasks->due_date = $request->get('due_date');
        $tasks->description = $request->get('description');
        $tasks->status = $request->get('status');
        $tasks->priority = $request->get('priority');
        $tasks->client_id = $request->get('client_id');

        $saved = $tasks->save();
        if ($saved) {

            //send notification to employee
            $fbID = getFBIDs($company_id, null, $tasks->assigned_to);
            $notificationData = array(
                "company_id" => $company_id,
                "employee_id" => $tasks->assigned_to,
                "data_type" => "task",
                "data" => "",
                "action_id" => $tasks->id,
                "title" => $tasks->title,
                "description" => $tasks->description,
                "created_at" => date('Y-m-d H:i:s'),
                "status" => 1,
                "to" => 1,
                "unix_timestamp" => time()
            );

            $sendingTask = $tasks;
            $client = getClient($tasks->client_id);
            $sendingTask->company_name = empty($client) ? "N/A" : $client->company_name;
            $dataPayload = array("data_type" => "task", "task" => $sendingTask, "action" => "add");
            $sent = sendPushNotification_($fbID, 9, $notificationData, $dataPayload);
        }

        return redirect()->route('company.admin.task', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $company_id = config('settings.company_id');
        $task = Task::where('id', $request->id)->where('company_id', $company_id)->first();
        if ($task)
            return view('company.tasks.show', compact('task'));
        else
            return redirect()->route('company.admin.task', ['domain' => domain()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $company_id = config('settings.company_id');
        $task = Task::where('id', $request->id)->where('company_id', $company_id)->first();
        $clients = Client::select('company_name', 'id')->where('company_id', $company_id)->where('status', 'Active')->orderBy('company_name', 'asc')->get();
        $employees = Employee::select('name', 'id')->where('company_id', $company_id)->where('status', 'Active')->orderBy('name', 'asc')->get();
        if ($task)
            return view('company.tasks.edit', compact('task', 'employees', 'clients'));
        else
            return redirect()->route('company.admin.task', ['domain' => domain()]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $company_id = config('settings.company_id');
        $tasks = Task::findOrFail($request->id);
        $this->validate($request, [
            'title' => 'required',
            'due_date' => 'required',
            'assigned_from' => 'required',
            'assigned_to' => 'required',
            // 'start_date' =>  'required',
            // 'end_date' =>  'required',
            'description' => 'required',
        ]);


        // 'email' => 'required|email|unique:users,email,' . $id,
        //    'roles' => 'required|min:1'
        //$tasks= new \App\Company;
        $tasks->title = $request->get('title');
        $tasks->due_date = $request->get('due_date');
        if ($request->assigned_from == "admin") {
            $tasks->assigned_from = Auth::user()->EmployeeId();
            $tasks->assigned_from_type = "Admin";
        } else {
            $tasks->assigned_from = $request->get('assigned_from');
            $tasks->assigned_from_type = "Employee";
        }
        $tasks->assigned_to = $request->get('assigned_to');
        // $tasks->start_date=$request->get('start_date');
        // $tasks->end_date=$request->get('end_date');
        $tasks->description = $request->get('description');
        $tasks->status = $request->get('status');
        $tasks->priority = $request->get('priority');
        $tasks->client_id = $request->get('client_id');

        $updated = $tasks->save();

        if ($updated) {

            $fbID = getFBIDs($company_id, null, $tasks->assigned_to);
            $notificationData = array(
                "company_id" => $company_id,
                "employee_id" => $tasks->assigned_to,
                "data_type" => "task",
                "data" => "",
                "action_id" => $tasks->id,
                "title" => "Task Updated",
                "description" => $tasks->description,
                "created_at" => date('Y-m-d H:i:s'),
                "status" => 1,
                "to" => 1,
                "unix_timestamp" => time()
            );

            $sendingTask = $tasks;
            $client = getClient($tasks->client_id);
            $sendingTask->company_name = empty($client) ? "N/A" : $client->company_name;
            $dataPayload = array("data_type" => "task", "task" => $sendingTask, "action" => "update");
            $sent = sendPushNotification_($fbID, 9, $notificationData, $dataPayload);

        }


        return redirect()->route('company.admin.task', ['domain' => domain()])->with('success', 'Information has been  Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */

    public function changeStatus(Request $request)
    {
        $company_id = config('settings.company_id');
        $task = Task::findOrFail($request->task_id);
        $task->status = $request->status;
        $saved = $task->save();
        if ($saved) {
            $fbID = getFBIDs($company_id, null, $task->assigned_to);
            $notificationData = array(
                "company_id" => $company_id,
                "employee_id" => $task->assigned_to,
                "data_type" => "task",
                "data" => "",
                "action_id" => $task->id,
                "title" => "Task " . $request->status,
                "description" => "Your Task status has been changed to " . $request->status,
                "created_at" => date('Y-m-d H:i:s'),
                "status" => 1,
                "to" => 1,
                "unix_timestamp" => time()
            );

            $sendingTask = $task;
            $client = getClient($task->client_id);
            $sendingTask->client_name = empty($client) ? "N/A" : $client->company_name;
            $dataPayload = array("data_type" => "task", "task" => $sendingTask, "action" => "update_status");
            $sent = sendPushNotification_($fbID, 9, $notificationData, $dataPayload);
        }
        return redirect()->route('company.admin.task', ['domain' => domain()]);
    }

    public function destroy(Request $request)
    {
        $company_id = config('settings.company_id');
        $tasks = Task::findOrFail($request->id);
        if (!empty($tasks)) {
            $deleted = $tasks->delete();
            if ($deleted) {
                $sendingTask = $tasks;
                $dataPayload = array("data_type" => "task", "task" => $sendingTask, "action" => "delete");
                $msgID = sendPushNotification_(getFBIDs($company_id, null, $tasks->assigned_to), 9, null, $dataPayload);
            }
        }
        flash()->success('Task has been deleted.');
        return redirect()->route('company.admin.task', ['domain' => domain()]);
    }
}
