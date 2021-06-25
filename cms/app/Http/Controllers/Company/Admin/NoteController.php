<?php

namespace App\Http\Controllers\Company\Admin;

use DB;
use Log;
use Session;
use Storage;
use App\Note;
use App\Client;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:note-view');
        $this->middleware('permission:note-create', ['only' => ['create','store']]);
        $this->middleware('permission:note-update', ['only' => ['edit','update']]);
        $this->middleware('permission:note-delete', ['only' => ['destroy']]);
    }

    public function index(){
        $company_id = config('settings.company_id');
        $clients = Auth::user()->handleQuery('client')->orderBy('company_name', 'asc')->pluck('company_name', 'id')->toArray();
        $notes = Auth::user()->handleQuery('note')
            ->get(['id'])->count();
        $employees = Auth::user()->handleQuery('employee')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        return view('company.notes.index',compact('notes', 'employees', 'clients'));
    }

    public function dataTable(Request $request){
        $getColumns = $request->datacolumns;
        $columns = array();
        $sizeof = sizeof($getColumns);
        for($count = 0; $count<$sizeof; $count++){
            $columns[$count] = $getColumns[$count]["data"];
        }

        $company_id = config('settings.company_id');
        $empVal = $request->empVal;
        $partyVal = $request->partyVal;
        $start_date = $request->startDate;
        $end_date = $request->endDate;
        $search = $request->input('search')['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $order_col_no = $request->input('order.0.column');
        $order = $columns[$order_col_no];
        if($order == "company_name"){
            $order = "clients.".$order;
        }elseif($order == "employee_name"){
            $order = "employees."."name";
        }elseif($order == "date" ){
            $order = "meetings.meetingdate";//.$order;
        }elseif($order == "note"){
            $order = "meetings.remark";
        }
        $dir = $request->input('order.0.dir');

        $prepQuery = Auth::user()->handleQuery('note')->with('images')->select('clients.id as client_id', 'clients.name as contact_person_name', 'clients.company_name', 'employees.name as employee_name', 'meetings.employee_id as employee_id','meetings.id as id', 'meetings.company_id', 'meetings.meetingdate', 'meetings.remark')
            ->leftJoin('clients', 'meetings.client_id', '=', 'clients.id')
            ->leftJoin('employees', 'meetings.employee_id', '=', 'employees.id')
            ->whereBetween('meetings.meetingdate', [$start_date, $end_date])
            ->orderBy('meetings.id','desc');

        if(!empty($empVal)){
            $empFilterQuery =  (clone $prepQuery);
            $prepQuery = $empFilterQuery->where('meetings.employee_id', $empVal);
        }
        if(!empty($partyVal)){
            $partyFilterQuery =  (clone $prepQuery);
            $prepQuery = $partyFilterQuery->where('meetings.client_id', $partyVal);
        }
        if(!empty($search)){
            $searchQuery = (clone $prepQuery);
            $prepQuery = $searchQuery->where(function($query) use ($search){
                $query->orWhere('employees.name' ,'LIKE', "%{$search}%");
                $query->orWhere('clients.company_name' ,'LIKE', "%{$search}%");
                $query->orWhere('meetings.remark' ,'LIKE', "%{$search}%");
            });
        }

        $totalData = $prepQuery->count();
        if($limit==-1){
            $limit = $totalData;
        }
        $totalFiltered = $totalData;

        $data = array();

        $meetings =  $prepQuery->orderBy($order,$dir)->offset($start)
            ->limit($limit)->get();



        if (!empty($meetings)) {
            $i = $start;
            $viewable_clients = Auth::user()->handleQuery('client')->pluck('id')->toArray();
            foreach ($meetings as $key =>  $meeting) {
                $id = $meeting->id;

                $show = domain_route('company.admin.notes.show', [$id]);
                $edit = domain_route('company.admin.notes.edit', [$id]);
                $delete = domain_route('company.admin.notes.destroy', [$id]);

                $client_company_name = $meeting->company_name;
                $client_show = in_array($meeting->client_id, $viewable_clients)?domain_route('company.admin.client.show',[$meeting->client_id]):null;
                $employee_name = $meeting->employee_name;
                $employee_show = domain_route('company.admin.employee.show',[$meeting->employee_id]);
                $date = isset($meeting->meetingdate)?getDeltaDate(date('Y-m-d',strtotime($meeting->meetingdate))):null;
                $remark = $meeting->remark;

                $nestedData['id'] =  $start + ($key+1);

                $nestedData['company_name'] = "<a class='clientLinks' href='{$client_show}' data-viewable='{$client_show}' datasalesman='{$client_company_name}'> {$client_company_name}</a>";
                $action = "<a href='{$show}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";

                if(Auth::user()->can('note-update')){
                    $action = $action."<a href='{$edit}' class='btn btn-warning btn-sm'
              style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
                }
                if(Auth::user()->can('note-delete')){
                    $action = $action."<a class='btn btn-danger btn-sm delete' data-mid='{$id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";
                }
                $nestedData['employee_name'] = "<a href='{$employee_show}' datasalesman='{$employee_name}'> {$employee_name}</a>";
                $nestedData['date'] =$date;
                $nestedData['note'] = $remark;
                $images = "<div class='imgTD'>";
                $i = 0;
                foreach($meeting->images as $image){
                    $i +=1;
                    $image_path = URL::asset('cms/'.$image->image_path);
                    $images .= "<img class='img-responsive display-imglists' id='img{$id}${i}' data-key='{$id}{$i}'
              src='{$image_path}' alt='Picture Displays here' style='width:100px;height:100px;margin-right: 15px;margin-top: 5px;cursor:pointer;' onClick='viewImage(this, {$id}{$i})'/>";
                }
                $nestedData['note'] .= $images."</div>";
                $nestedData['action'] = $action;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        );

        return json_encode($json_data);
    }

    public function show($domain,$id){
        $company_id = config('settings.company_id');

        $note       = Note::select('meetings.*','employees.name as employee_name','clients.company_name as company_name')
            ->leftJoin('employees','meetings.employee_id','employees.id')
            ->leftJoin('clients','meetings.client_id','clients.id')
            ->where('meetings.company_id',$company_id)->where('meetings.id',$id)
            ->whereNull('meetings.deleted_at')
            ->first();

        //$note       = Auth::user()->handleQuery('note')->select('meetings.*','employees.name as employee_name','clients.company_name as company_name')
        // ->leftJoin('employees','meetings.employee_id','employees.id')
        // ->leftJoin('clients','meetings.client_id','clients.id')
        // ->where('meetings.company_id',$company_id)->where('meetings.id',$id)
        // ->whereNull('meetings.deleted_at')
        // ->firstOrFail();
        $images = DB::table('images')->where('company_id',$company_id)->where('type', '=', 'notes')->where('type_id', $id)->get();
        if($note!=null){
            $action = null;
            if(Auth::user()->can('note-update')){
                $show = domain_route('company.admin.notes.edit',[$note->id]);
                $action =  "<a href='{$show}' class='btn btn-warning btn-sm edit' style='padding: 7px 6px;'><i class='fa fa-edit'></i>Edit</a>";
            }
            if (Auth::user()->can('note-delete')) {
                $delete = domain_route('company.admin.notes.destroy', [$note->id]);
                $action = $action."<a class='btn btn-danger btn-sm delete' data-mid='{{ $note->id }}' data-backdrop='false' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 7px 6px;'><i class='fa fa-trash-o'></i>Delete</a>";
            }
            return view('company.notes.show',compact('note','images', 'action'));
        }
    }

    public function create($domain){
        $previous_url = URL::previous();
        $domain = domain_route('company.admin.client');
        $company_id = config('settings.company_id');
        if (strpos($previous_url,$domain) !== false) {
            $client_url = explode('/',$previous_url);
            $client_id = end($client_url);
            $clients = Client::where('company_id',$company_id)->where('id',$client_id)->first();
            // $employees = $clients->employees;
            if(!$clients){
                return redirect()->back();
            }
        }else{
            // return redirect()->back();
            $clients = Auth::user()->handleQuery('client')->orderBy('company_name','ASC')->get();
            // $employees = Employee::where('company_id',$company_id)->orderBy('name','ASC')->get();
        }
        return view('company.notes.create',compact('clients'));
    }

    public function store($domain,Request $request){

        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;

        $customMessages = [
            'note_date.required' => 'Date is required.',
            'note_time.required' => 'Time is required.',
            'description.required' => 'Notes is required',
        ];


        $this->validate($request, [
            'note_date' => 'required',
            'note_time' => 'required',
            'description' => 'required',
        ], $customMessages);

        $note['company_id'] = $company_id;
        $note['employee_id'] = Auth::user()->EmployeeId();
        $note['client_id'] = $request->client_id;
        $note['meetingdate'] = $request->note_date;
        $note['checkintime'] = $request->note_time;
        $note['remark'] = $request->description;
        if ($request->file('audio_note')) {

            $this->validate($request, [
                'audio_note' => 'mimes:mpga,wav|max:5000'
            ]);

            $prodAudio2 = $request->file('audio_note');
            $realname = pathinfo($request->file('audio_note')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $prodAudio2->getClientOriginalExtension();
            $new_name = $realname . "-" . time() . '.' . $extension;
            $prodAudio2->storeAs('public/uploads/' . $companyName . '/audios/', $new_name);
            $path = Storage::url('app/public/uploads/' . $companyName . '/audios/' . $new_name);
            $note['audio_note'] = $path;
        }
        $saved = Note::Create($note);
        if ($saved) {
            $saved->images = "";
            $saved->image_paths = "";
            if ($request->file('receipt')) {
                $tempImageArray = array();
                $tempImagePathArray = array();
                $imageIds = array();

                foreach ($request->file('receipt') as $receipt) {
                    $receipt2 = $receipt;
                    $realname = pathinfo($receipt->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $receipt2->getClientOriginalExtension();
                    $new_name = $realname . "-" . time() . '.' . $extension;
                    $receipt2->storeAs('public/uploads/' . $companyName . '/notes/', $new_name);
                    $path = Storage::url('app/public/uploads/' . $companyName . '/notes/' . $new_name);
                    $imgId = DB::table('images')->insertGetId([
                        'type' => 'notes',
                        'type_id' => $saved->id,
                        'company_id' => $company_id,
                        'client_id' => $saved->client_id,
                        'image' => $new_name,
                        'image_path' => $path,
                    ]);
                    array_push($imageIds,$imgId);
                    array_push($tempImageArray, $new_name);
                    array_push($tempImagePathArray, $path);
                }
                $saved->image_ids = json_encode($imageIds);
                $saved->images = json_encode($tempImageArray);
                $saved->image_paths = json_encode($tempImagePathArray);

            }

            $sendingNote = $saved;
            $sendingNote->company_name = Client::where('company_id',$company_id)->where('id',$note['client_id'])->first()->company_name;
            // Log::info('info', array("data"=>print_r($sendingNote,true)));
            $dataPayload = array("data_type" => "note", "note" => $sendingNote, "action" => "add");
            $msgID = sendPushNotification_(getFBIDs($company_id, null, $sendingNote->employee_id), 19, null, $dataPayload);
        }
        Session::flash('success', 'Note added successfully.');

        $newprevious=explode('/',$request->previous_url);
        if($newprevious[4]=='employee' || $newprevious[4]=='client'){
            $previous = $request->previous_url;
            return redirect($previous);
        }else{

            return redirect()->route('company.admin.notes',['domain'=>domain()]);
        }

        // if($request->previous_url){
        //   $url = $request->previous_url;
        //   return redirect($url);
        // }else{
        //   return redirect()->back();
        // }
    }

    public function edit($domain,$id){
        $previous_url = URL::previous();
        $domain = domain_route('company.admin.client');
        $company_id = config('settings.company_id');
        $note = Note::where('company_id',$company_id)->where('id',$id)->first();
        $images = DB::table('images')->where('company_id',$company_id)->where('type', '=', 'notes')->where('type_id', $id)->get();
        $images_count = $images->count();
        $note->employee_name = Employee::where('id',$note->employee_id)->first()->name;
        if($note!=null){
            // if (strpos($previous_url,$domain) !== false) {
            //   $client_url = explode('/',$previous_url);
            //   $client_id = end($client_url);
            $clients = Client::where('company_id',$company_id)->where('id',$note->client_id)->first();
            if(!$clients){
                return redirect()->back();
            }
            // }else{
            //   return redirect()->back();
            // }
            return view('company.notes.edit',compact('note','clients','images','images_count'));
        }
        return redirect()->route('company.admin.notes',['domain' => domain()]);
    }

    public function update($domain,$id,Request $request){
        $company_id = config('settings.company_id');
        $companyName = Auth::user()->companyName($company_id)->domain;
        $note = Note::where('company_id',$company_id)->where('id',$id)->first();
        if($note!=null){
            $customMessages = [
                'description.required' => 'Notes is required',
            ];
            $this->validate($request, [
                'description' => 'required',
            ], $customMessages);

            $note['company_id'] = $company_id;
            $note['client_id'] = $request->client_id;
            $note['remark'] = $request->description;
            if ($request->file('audio_note')) {

                $this->validate($request, [
                    'audio_note' => 'mimes:mpga,wav|max:5000'
                ]);

                $prodAudio2 = $request->file('audio_note');
                $realname = pathinfo($request->file('audio_note')->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $prodAudio2->getClientOriginalExtension();
                $new_name = $realname . "-" . time() . '.' . $extension;
                $prodAudio2->storeAs('public/uploads/' . $companyName . '/audios/', $new_name);
                $path = Storage::url('app/public/uploads/' . $companyName . '/audios/' . $new_name);
                $note['audio_note'] = $path;
            }
            $saved = $note->save();
            if ($saved) {
                $note->images = "";
                $note->image_paths = "";
                $imageIds = [];
                if($request->get('img_ids') != NULL){
                    DB::table('images')->where('company_id',$company_id)->where('type','notes')->where('type_id',$note->id)->whereNotIn('id',$request->get('img_ids'))->delete();
                    foreach($request->get('img_ids') as $imgid){
                        array_push($imageIds, $imgid);
                    }
                }else{
                    DB::table('images')->where('company_id',$company_id)->where('type','notes')->where('type_id',$note->id)->delete();
                }
                $imageArray = getImageArray("notes", $note->id,$company_id);
                $tempImageArray = getArrayValue($imageArray, "images", array());
                $tempImagePathArray = getArrayValue($imageArray, "image_paths", array());
                // $imageIds = [];
                if ($request->file('receipt')) {
                    foreach ($request->file('receipt') as $receipt) {
                        $receipt2 = $receipt;
                        $realname = pathinfo($receipt->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = $receipt2->getClientOriginalExtension();
                        $new_name = $realname . "-" . time() . '.' . $extension;
                        $receipt2->storeAs('public/uploads/' . $companyName . '/notes/', $new_name);
                        $path = Storage::url('app/public/uploads/' . $companyName . '/notes/' . $new_name);
                        $imgId = DB::table('images')->insertGetId([
                            'type' => 'notes',
                            'type_id' => $note->id,
                            'company_id' => $company_id,
                            'client_id' => $note->client_id,
                            'image' => $new_name,
                            'image_path' => $path,
                        ]);
                        array_push($imageIds,$imgId);
                        array_push($tempImageArray, $new_name);
                        array_push($tempImagePathArray, $path);
                    }

                }

                $note->image_ids = json_encode($imageIds);
                $note->images = json_encode($tempImageArray);
                $note->image_paths = json_encode($tempImagePathArray);
                $note->company_name = Client::where('company_id',$company_id)->where('id',$note->client_id)->first()->company_name;
                //Log::info('info', array("note"=>print_r($note,true)));
                $dataPayload = array("data_type" => "note", "note" => $note, "action" => "update");
                $msgID = sendPushNotification_(getFBIDs($company_id, null, $note->employee_id), 19, null, $dataPayload);
            }

            $url = $request->previous_url;
            Session::flash('success', 'Note Updated successfully.');

            $newprevious=explode('/',$request->previous_url);
            if($newprevious[4]=='employee' || $newprevious[4]=='client'){
                $previous = $request->previous_url;
                return redirect($previous);
            }else{
                return redirect()->route('company.admin.notes',['domain'=>domain()]);
                //  return redirect()->back();
                // return redirect()->route('company.admin.client.show',['domain'=>domain(), 'id'=>$note->client_id]);
            }
            //return redirect($url);
            //  if($saved)
            //  	return redirect()->route('company.admin.notes', ['domain' => domain()])->with('success', 'Note has been Updated Successfully.');
            // else
            // 	return redirect()->route('company.admin.notes',['domain'=>domain()]);
        }
    }

    public function destroy($domain,$id){
        $company_id = config('settings.company_id');
        $note       = Note::where('company_id',$company_id)->where('id',$id)->first();
        $client_id = $note->client_id;
        if($note!=null){
            $deleted = $note->delete();
            if ($deleted) {
                $dataPayload = array("data_type" => "note", "note" => $note, "action" => "delete");
                $msgID       = sendPushNotification_(getFBIDs($note->company_id, null, $note->employee_id), 19, null, $dataPayload);
            }
            Session:: flash('success', 'Note Deleted successfully.');
            if(isset($note->image_path) && $note->image_path!=""){
                unlink(base_path().$note->image_path);
            }
        }else{
            Session:: flash('success','Sorry note was not deleted');
        }
        // return back();
        // return redirect()->route('company.admin.client.show',['domain'=>domain(), 'id'=>$client_id]);

        return redirect()->back();
    }
}
