<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
Use Auth;
use App\Employee;
use App\Beat;
use App\BeatVPlan;
use App\BeatPlansDetails;
use App\BeatClient; 
use App\Client;
use App\Holiday;
use DB;
use \Datetime;
use DatePeriod;
use DateInterval;
use Log;

class BeatPlanController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	*/
	public function __construct()
	{
			$this->middleware('auth');
			$this->middleware('permission:beat-plan-create', ['only' => ['detail','store']]);
			$this->middleware('permission:beat-plan-view');
			$this->middleware('permission:beat-plan-update', ['only' => ['edit','update']]);
			$this->middleware('permission:beat-plan-delete', ['only' => ['destroy']]);
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	*/

	public function index()
	{
		
		$data1 = array();

		$company_id = config('settings.company_id');
		// $employee_with_assigned_beats =	DB::table('handles')
		// ->where('company_id', $company_id)
		// 			->pluck('employee_id')->toArray();
		
		// $employees = Employee::where('company_id', $company_id)
		// 			->where('status','active')
		// 			->whereIn('id', $employee_with_assigned_beats)
		// 			->orderBy('name','asc')
		// 			->get();
		// if(!(empty($employee_with_assigned_beats))){
		// 	$employees = Auth::user()->handleQuery('employee')
		// 				->where('status','active')
		// 				->whereIn('id', $employee_with_assigned_beats)
		// 				->orderBy('name','asc')
		// 				->get();
		// }else{
			$employees = Auth::user()->handleQuery('employee')
						->where('status','active')
						->orderBy('name','asc')
						->get();
		// }

		$holidays = Holiday::where('company_id', $company_id)
							->get();

		foreach($holidays as $holiday){
			$end_date = Carbon::parse($holiday->end_date);
			$data1['nextday_end'][$holiday->id]=$end_date->addDays(1);
		}

		$beats_list = Beat::where('status','Active')->orderby('name','asc')->get();

		return view('company.beatplans.index',compact('employees','data1','beats_list','holidays'));
	}

	public function fetcheachplan(Request $request)
	{
		$company_id = config('settings.company_id');
		$get_employee_id = $request->id;

		// $beats_planned = BeatVPlan::where('company_id', $company_id)
		// 						->where('employee_id',$get_employee_id)
		// 						->count();
		$beats_planned = BeatVPlan::where('company_id', $company_id)
								->where('employee_id',$get_employee_id)
								->count();
		
		return $beats_planned;
	}

	public function store(Request $request){
		$messages = [
			'title.*.required' => 'Required.',
			'beat_list.*.required' => 'Required.',
			// 'party_list.*.required' => 'Required.',
			'start_date*.required' => 'Required.',
		];

		$this->validate($request, [
			'title.*' => 'required',
			'start_date*.' => 'required',
			'end_date' => 'nullable',
			'remark' => 'nullable',
			'starttime' => 'nullable',
			'endtime' => 'nullable',
			'status' => 'nullable',
			'employee_list'=>'nullable',
		], $messages);

		foreach($request->getCount as $index=>$value){
			$messages = [
				'beat_list.'.$index.'.required' => 'Required.',
				// 'party_list.'.$index.'.required' => 'Required.',
			];
			$this->validate($request, [
				'beat_list.'.$index.'' => 'required',
				// 'party_list.'.$index.'' => 'required',
			], $messages);
		}

		$company_id = config('settings.company_id');

		$request_dates = $request->start_date;
		if(config('settings.ncal')==1){
			$len = count($request_dates);
			for($j=0 ; $j<$len; $j++){
				$request_dates[$j] = getEngDeltaDate($request->start_date[$j]);
			}
		}

		$counter = 0;

		$existing_plans = DB::table('beatvplans')
							->where('employee_id','=',$request->employee_list)	
							->join('beatplansdetails', 'beatvplans.id', '=', 'beatplansdetails.beatvplan_id')
							->whereIn('beatplansdetails.plandate', $request_dates)
							->count();
		
		if($existing_plans>0){
			$counter = $counter +1;
		}

		if($counter<=0){
			$beatvplan = new BeatVPlan();
			$beatvplan->company_id = $company_id;
			$beatvplan->employee_id = $request->employee_list;	
			$saved = $beatvplan->save();
			if($saved){
				$beatPlanArray = array();
				foreach($request->getCount as $index=>$value){
	
					$beatplan = new BeatPlansDetails();
					$beatplan->title = $request->title[$index];
					$beatid_clientid_values = $request->beat_list[$index];
					$beat_ids = array();
					$client_ids = array();
					$beat_clients_array = array();
					foreach($beatid_clientid_values as $beatid_clientid_value){
						$beat_id = explode(',',$beatid_clientid_value);
						$beat_clients_array[$beat_id[0]][] =  $beat_id[1];
						if(!in_array($beat_id[0],$beat_ids)){
							array_push($beat_ids,$beat_id[0]);
						}
						array_push($client_ids,$beat_id[1]);
					} 
					$beatplan->beat_id = implode(',',$beat_ids);
					$beatplan->client_id = implode(',',$client_ids);
					$beatplan->beat_clients = json_encode($beat_clients_array);
					unset($beat_clients_array);
					unset($beat_ids);
					unset($client_ids);  	 
					if(config('settings.ncal')==1){
						$bplandate = getEngDeltaDate($request->start_date[$index]);
					}else{
						$bplandate = $request->start_date[$index];
          }
          $beatplan->plandate = date("Y-m-d", strtotime($bplandate));
					$beatplan->remark = $request->remark[$index];
					$beatplan->beatvplan_id = $beatvplan->id;
					$beatplan_details_saved = $beatplan->save();
					if($beatplan_details_saved){
						$beatplan['company_id'] = $beatvplan->company_id;
						$beatplan['employee_id'] = $beatvplan->employee_id;
		
						array_push($beatPlanArray,$beatplan->toArray());
					}else{
						$saved->delete();
					}
					
				}
			}

			$tempEmployee = Auth::user()->handleQuery('employee',$beatvplan->employee_id)->first();
			//  Employee::where('id', $beatvplan->employee_id)->first();
			if (!empty($tempEmployee->firebase_token)) {

				$notificationData = array(
	                "company_id" => $company_id,
	                "employee_id" => $beatvplan->employee_id,
	                "data_type" => "beatplan",
	                "data" => "",
	                "action_id" => $beatvplan->id,
	                "title" => "A new beatplan has been assigned to you",
	                "description" => $beatvplan->remark,
	                "created_at" => date('Y-m-d H:i:s'),
	                "status" => 1,
	                "to" => 1,
	                "unix_timestamp" => time()
	            );

                $dataPayload = array("data_type" => "beatplan", "beatplan" => $beatPlanArray, "action" => "add");
                $msgID = sendPushNotification_([$tempEmployee->firebase_token], 14, null, $dataPayload);
            }

			return $beatvplan;
		}else{
			// $data['result'] = "Cannot assign same party for same date.";
			$data['result'] = "Cannot create multiple plans for same date.";
			return $data;
		}
	}

	public function show(Request $request){ 
    $company_id = config('settings.company_id');
    $detailInstance = BeatPlansDetails::findOrFail($request->id);
    $beatVPlanInstance = BeatVPlan::findOrFail($detailInstance->beatvplan_id);
        $employee_id = $beatVPlanInstance->employee_id;

        $data1=array();

        // $employee_with_assigned_beats = DB::table('handles')
        // 	->where('company_id', $company_id)
        //                                 ->pluck('employee_id')->toArray();


        // if ($employee_with_assigned_beats) {
        //     $employees = Auth::user()->handleQuery('employee')
        //     //   Employee::where('company_id', $company_id)
        //                 ->where('status', 'active')
        //                 ->whereIn('id', $employee_with_assigned_beats)
        //                 ->orderBy('name', 'asc')
        //                 ->get();
        // } else {
            $employees = Auth::user()->handleQuery('employee')
            //   Employee::where('company_id', $company_id)
                        ->where('status', 'active')
                        ->orderBy('name', 'asc')
                        ->get();
        // }
                            
        $beats_planned = BeatVPlan::join('beatplansdetails', 'beatplansdetails.beatvplan_id', '=', 'beatvplans.id')
                        ->where('beatvplans.company_id', $company_id)
                        ->where('beatvplans.employee_id', '=', $employee_id)
                        ->get();

        $beats_only = BeatVPlan::where('company_id', $company_id)
                        ->where('employee_id', $employee_id)
                        ->orderby('created_at', 'desc')
                        ->get();

        $handles =	DB::table('handles')
                    ->where('employee_id', $employee_id)
                    ->pluck('client_id')->toArray();

        
        $clients = Auth::user()->handleQuery('client')
        // Client::where('company_id', $company_id)
                    ->where('status', 'Active')
                    ->orderBy('company_name', 'asc')
                    ->get();
        $beat_clients = Client::select('clients.company_name', 'clients.id', 'beats.name as beat_name', 'beats.id as beatid')
                        ->leftJoin('beat_client', 'clients.id', 'beat_client.client_id')
                        ->leftJoin('beats', 'beats.id', 'beat_client.beat_id')
                        ->where('clients.company_id', $company_id)
                        ->where('clients.status', 'Active')
                        ->select('clients.company_name', 'clients.id', 'beats.name as beat_name', 'beats.id as beatid')
                        ->whereIn('clients.id', $handles)
                        ->orderBy('beats.name', 'desc')
                        ->orderBy('company_name', 'asc')
                        ->get();
        $beats_list = array();
        foreach ($beat_clients as $beat_client) {
            if ($beat_client->beatid!=0) {
                $beats_list[$beat_client->beatid]['name']=$beat_client->beat_name;
                $beats_list[$beat_client->beatid]['id']=$beat_client->beatid;
                $beats_list[$beat_client->beatid]['clients'][$beat_client->id]=$beat_client->company_name;
            } else {
                $beats_list[0]['name']='Unspecified';
                $beats_list[0]['id']='0';
                $beats_list[0]['clients'][$beat_client->id]=$beat_client->company_name;
            }
        }
        //
        
        $dateTime = Carbon::now();
        $startOfYear = $dateTime->copy()->startOfYear();
        $endOfYear = $dateTime->copy()->endOfYear();
        $startDate = Carbon::parse($startOfYear)->next(Carbon::SATURDAY); // Get the first friday.
        $endDate = Carbon::parse($endOfYear);
        $data[] = "";
        foreach ($beats_planned as $beat) {
            $end_date = Carbon::parse($beat->planenddate);
            $data['nextday_end'][$beat->id]=$end_date->addDays(0);
        }

        $holidays = Holiday::where('company_id', $company_id)
                            ->get();
        
        foreach ($holidays as $holiday) {
            $end_date = Carbon::parse($holiday->end_date);
            $data1['nextday_end'][$holiday->id]=$end_date->addDays(1);
        }


		
		return view('company.beatplans.show',compact('beats_only','employees','beats_planned',
														'beats_list','data','data1','beats_planned','holidays', 'employee_id'));
	}

	public function detail(Request $request){ 
		$company_id = config('settings.company_id');
		$employee_id = $request->id;

		$data1=array();

		// $employee_with_assigned_beats = DB::table('handles')
		// 	->where('company_id', $company_id)
		// 								->pluck('employee_id')->toArray();


		// if($employee_with_assigned_beats){
		// 	$employees = Auth::user()->handleQuery('employee')
		// 	//   Employee::where('company_id', $company_id)
		// 				->where('status','active')
		// 				->whereIn('id', $employee_with_assigned_beats)
		// 				->orderBy('name','asc')
		// 				->get();
		// }else{
			$employees = Auth::user()->handleQuery('employee')
			//   Employee::where('company_id', $company_id)
						->where('status','active')
						->orderBy('name','asc')
						->get();
		// }
							
		$beats_planned = DB::table('beatvplans')
						->where('beatvplans.company_id',$company_id)
						->where('beatvplans.employee_id','=',$request->id)
						->join('beatplansdetails','beatplansdetails.beatvplan_id','=','beatvplans.id')
						->get();

		$beats_only = BeatVPlan::where('company_id',$company_id)
						->where('employee_id',$request->id)
						->orderby('created_at', 'desc')
						->get();

		$handles =	DB::table('handles')
					->where('employee_id', $request->id)
					->pluck('client_id')->toArray();

		
		$clients = Auth::user()->handleQuery('client') 
		// Client::where('company_id', $company_id)
					->where('status', 'Active')
					->orderBy('company_name', 'asc')
					->get();
		$beat_clients = Client::select('clients.company_name', 'clients.id','beats.name as beat_name','beats.id as beatid')
						->leftJoin('beat_client','clients.id','beat_client.client_id')
						->leftJoin('beats','beats.id','beat_client.beat_id')
						->where('clients.company_id', $company_id)
						->where('clients.status', 'Active')
						->select('clients.company_name', 'clients.id','beats.name as beat_name','beats.id as beatid')
						->whereIn('clients.id', $handles)
						->orderBy('beats.name', 'desc')
						->orderBy('company_name', 'asc')
						->get();
		$beats_list = array();	
		foreach($beat_clients as $beat_client){
			if($beat_client->beatid!=0){
				$beats_list[$beat_client->beatid]['name']=$beat_client->beat_name;
				$beats_list[$beat_client->beatid]['id']=$beat_client->beatid;
				$beats_list[$beat_client->beatid]['clients'][$beat_client->id]=$beat_client->company_name;
			}else{
				$beats_list[0]['name']='Unspecified';
				$beats_list[0]['id']='0';
				$beats_list[0]['clients'][$beat_client->id]=$beat_client->company_name;
            }
		}
		//
		
		$dateTime = Carbon::now();
		$startOfYear = $dateTime->copy()->startOfYear();
		$endOfYear = $dateTime->copy()->endOfYear();
		$startDate = Carbon::parse($startOfYear)->next(Carbon::SATURDAY); // Get the first friday.
		$endDate = Carbon::parse($endOfYear);
		$data[] = "";
		foreach($beats_planned as $beat){
			$end_date = Carbon::parse($beat->planenddate);
			$data['nextday_end'][$beat->id]=$end_date->addDays(0);
		}

		$holidays = Holiday::where('company_id', $company_id)
							->get();
		
		foreach($holidays as $holiday){
			$end_date = Carbon::parse($holiday->end_date);
			$data1['nextday_end'][$holiday->id]=$end_date->addDays(1);
		}
		
		return view('company.beatplans.detail',compact('beats_only','employees','beats_planned',
														'beats_list','data','data1','beats_planned','holidays', 'employee_id'));
	}

	public function monthplans(Request $request){
		$company_id = config('settings.company_id');
		$today = date('Y-m-d');
		$employee_id = $request->empId;
		$year = $request->year;
		$month = $request->month;
		$view = 'monthlyEdit';
		// $employee_with_assigned_beats = DB::table('handles')
		// 	->where('company_id', $company_id)
		// 								->pluck('employee_id')->toArray();
		$employees = Auth::user()->handleQuery('employee')
		// Employee::where('company_id', $company_id)
								->where('status','active')
								// ->whereIn('id', $employee_with_assigned_beats)
								->orderBy('name','asc')
								->get();
		$handles =	DB::table('handles')
					->where('employee_id', $employee_id)
					->pluck('client_id')->toArray();

		$clients = Auth::user()->handleQuery('client') 
		// Client::where('company_id', $company_id)
					->where('status', 'Active')
					->orderBy('company_name', 'asc')
					->get();
		$beat_clients = Client::select('clients.company_name', 'clients.id','beats.name as beat_name','beats.id as beatid')
						->leftJoin('beat_client','clients.id','beat_client.client_id')
						->leftJoin('beats','beats.id','beat_client.beat_id')
						->whereIn('clients.id', $handles)
						->where('clients.company_id', $company_id)
						->where('clients.status', 'Active')
						->orderBy('beats.name', 'desc')
						->orderBy('company_name', 'asc')
						->get();
		$beats_list = array();	
		foreach($beat_clients as $beat_client){
			if($beat_client->beatid!=0){
				$beats_list[$beat_client->beatid]['name']=$beat_client->beat_name;
				$beats_list[$beat_client->beatid]['id']=$beat_client->beatid;
				$beats_list[$beat_client->beatid]['clients'][$beat_client->id]=$beat_client->company_name;
			}else{
				$beats_list[0]['name']='Unspecified';
				$beats_list[0]['id']='0';
				$beats_list[0]['clients'][$beat_client->id]=$beat_client->company_name;
			}
		}
		
		$fetchBeatPlans = BeatVPlan::join('beatplansdetails', 'beatvplans.id', 'beatplansdetails.beatvplan_id')
							->where('beatvplans.company_id', $company_id)
							->where('beatvplans.employee_id', $employee_id)
							->whereYear('beatplansdetails.plandate', $year)
							->whereMonth('beatplansdetails.plandate', $month)
							->whereDate('beatplansdetails.plandate', '>=', $today)
							->orderby('beatplansdetails.plandate','desc')
							->get();
		if($fetchBeatPlans->count()>0){
			return view('company.beatplans.singleEditForm',compact('employees','beats_list','fetchBeatPlans','view'));
		}else{
			return 1;
		}
	}

	public function delete(Request $request){

		$employee_id = $request->empl_id;
		$tempEmployee = Auth::user()->handleQuery('employee', $employee_id)->first();//Employee::where('id', $employee_id)->first();
		$fbToken = getObjectValue($tempEmployee,"firebase_token");

		if($request->del_id!="" && $request->beat_del_id==""){
			$data = BeatPlansDetails::findOrFail($request->del_id);
			$data->delete();

			/*inform app*/
			if (!empty($fbToken)) {

                $dataPayload = array("data_type" => "beatplan", "beatplan" => $data, "action" => "delete");
                $sent = sendPushNotification_([$fbToken], 14, null, $dataPayload);
            }



			$fetchData = BeatPlansDetails::where('beatvplan_id',$request->get_id)->count();
			if($fetchData === (int)0){
				$data2 = BeatVPlan::findOrFail($request->get_id);
				$data2->delete();
			}
		}
		return $data;
	}

	public function edit(Request $request)
	{
		$company_id = config('settings.company_id');
		
		// $employee_with_assigned_beats = DB::table('handles')
		// 	->where('company_id', $company_id)
		// 								->pluck('employee_id')->toArray();

		// if($employee_with_assigned_beats){
		// 	$employees =  Auth::user()->handleQuery('employee')
		// 	// Employee::where('company_id', $company_id)
		// 							->where('status','active')
		// 							->whereIn('id', $employee_with_assigned_beats)
		// 							->orderBy('name','asc')
		// 							->get();
		// }else{
			$employees =  Auth::user()->handleQuery('employee')
			// Employee::where('company_id', $company_id)
									->where('status','active')
									->orderBy('name','asc')
									->get();
		// }

		$handles =	DB::table('handles')
					->where('employee_id', $request->id)
					->pluck('client_id')->toArray();

		$clients = Auth::user()->handleQuery('client') 
		// Client::where('company_id', $company_id)
					->where('status', 'Active')
					->orderBy('company_name', 'asc')
					->get();
		$beat_clients = Client::select('clients.company_name', 'clients.id','beats.name as beat_name','beats.id as beatid')
						->leftJoin('beat_client','clients.id','beat_client.client_id')
						->leftJoin('beats','beats.id','beat_client.beat_id')
						->whereIn('clients.id', $handles)
						->where('clients.company_id', $company_id)
						->where('clients.status', 'Active')
						->orderBy('beats.name', 'desc')
						->orderBy('company_name', 'asc')
						->get();
		$beats_list = array();	
		foreach($beat_clients as $beat_client){
			if($beat_client->beatid!=0){
				$beats_list[$beat_client->beatid]['name']=$beat_client->beat_name;
				$beats_list[$beat_client->beatid]['id']=$beat_client->beatid;
				$beats_list[$beat_client->beatid]['clients'][$beat_client->id]=$beat_client->company_name;
			}else{
				$beats_list[0]['name']='Unspecified';
				$beats_list[0]['id']='0';
				$beats_list[0]['clients'][$beat_client->id]=$beat_client->company_name;
            }
		}

		if(isset($request->fetch_id)){
			$fetchBeatPlans = BeatVPlan::where('beatvplans.company_id',$company_id)
								->where('beatvplans.employee_id','=',$request->id)
								->join('beatplansdetails','beatplansdetails.beatvplan_id','=','beatvplans.id')
								->where('beatplansdetails.id',$request->fetch_id)
								->get();
		}else{
			$fetchBeatPlans = BeatVPlan::where('beatvplans.company_id',$company_id)
								->where('beatvplans.employee_id','=',$request->id)
								->join('beatplansdetails','beatplansdetails.beatvplan_id','=','beatvplans.id')
								->where('beatplansdetails.plandate',$request->date)
								->get();
		}

							

		return view('company.beatplans.singleEditForm',compact('employees','beats_list','fetchBeatPlans'));
	}

	public function update(Request $request){

		foreach($request->getCount as $index=>$value){

			$messages = [
				'edit_beat_list.'.$index.'.required' => 'Required.',
				// 'edit_party_list.'.$index.'.required' => 'Required.',
			];

			$this->validate($request, [
				'edit_beat_list.'.$index.'' => 'required',
				// 'edit_party_list.'.$index.'' => 'required',
			], $messages);
		}

		$request_dates = $request->edit_start_date;
		if(config('settings.ncal')==1){
			$len = count($request_dates);
			for($j=0 ; $j<$len; $j++){
				$request_dates[$j] = getEngDeltaDate($request->edit_start_date[$j]);
			}
		}


		$counter = 0;

		$existing_plans = BeatVPlan::where('employee_id','=',$request->employee_list)	
							->join('beatplansdetails', 'beatvplans.id', '=', 'beatplansdetails.beatvplan_id')
							->whereIn('beatplansdetails.plandate', $request_dates)
							->whereNotIn('beatplansdetails.id',	$request->edit_id)	
							->count();

		if($existing_plans>0){
			$counter = $counter +1;
		}
		if($counter == 0){

			$beatvplan = BeatVPlan::findOrFail($request->beatvplan_id);
			$beatvplan->employee_id = $request->employee_list;
			$saved = $beatvplan->Update();
			
			if($saved){
				$beatPlanArray = array();

				foreach($request->getCount as $index=>$value){

					$beatplan = BeatPlansDetails::findOrFail($request->edit_id[$index]);
					$beatplan->title = $request->title[$index];
					if(config('settings.ncal')==1){
						$bplandate = getEngDeltaDate($request->edit_start_date[$index]);
					}else{
						$bplandate = $request->edit_start_date[$index];
          }
          $beatplan->plandate = date("Y-m-d", strtotime($bplandate));
					$beatid_clientid_values = $request->edit_beat_list[$index];
					$beat_ids = array();
					$client_ids = array();
					$beat_clients_array = array();
					foreach($beatid_clientid_values as $beatid_clientid_value){
						$beat_id = explode(',',$beatid_clientid_value);
						$beat_clients_array[$beat_id[0]][] =  $beat_id[1];
						if(!in_array($beat_id[0],$beat_ids)){
							array_push($beat_ids,$beat_id[0]);
						}
						array_push($client_ids,$beat_id[1]);
					} 
					$beatplan->beat_id = implode(',',$beat_ids);
					$beatplan->client_id = implode(',',$client_ids);
					$beatplan->beat_clients = json_encode($beat_clients_array);
					unset($beat_ids);
					unset($client_ids); 
					unset($beat_clients_array);
	
					$beatplan->remark = $request->remark[$index];
					$beatplan->beatvplan_id = $beatvplan->id;
					$beatplan_details_saved = $beatplan->Update();
					if($beatplan_details_saved){
						$beatplan['company_id'] = $beatvplan->company_id;
						$beatplan['employee_id'] = $beatvplan->employee_id;
		
						array_push($beatPlanArray,$beatplan->toArray());
					}
				}
				$tempEmployee = Auth::user()->handleQuery('employee', $beatvplan->employee_id)->first();//Employee::where('id', $beatvplan->employee_id)->first();
				if (!empty($tempEmployee->firebase_token)) {

					$dataPayload = array("data_type" => "beatplan", "beatplan" => $beatPlanArray, "action" => "add");
					$msgID = sendPushNotification_([$tempEmployee->firebase_token], 14, null, $dataPayload);
				}
				$data['result'] ="Successfully Updated";
				return $data;
			}
		} else{
			$data['msg'] ="Cannot create multiple plans for same date.";
			return $data;
		}
	}

	public function getCalendar(Request $request){
        $company_id = config('settings.company_id');
		$employee_id = $request->employee_id;
		if(isset($employee_id)){
			$queryBeatPlans = BeatVPlan::join('beatplansdetails', 'beatvplans.id','beatplansdetails.beatvplan_id')->where('beatvplans.company_id',$company_id)->where('beatvplans.employee_id', $employee_id)->where('beatplansdetails.plandate','<=',$request->engLastDate)->where('beatplansdetails.plandate','>=',$request->engFirstDate)->orderBy('beatplansdetails.plandate','ASC')->select('beatvplans.id as id', 'beatvplans.company_id', 'beatvplans.employee_id', 'beatplansdetails.id as beatDetailID','beatplansdetails.title as name', 'beatplansdetails.plandate as start_date', 'beatplansdetails.remark as description');
			$beatplans = $beatplans = $queryBeatPlans->get();
			if(!empty($beatplans)){
				$beatPlanCount = $queryBeatPlans->count();
			}
		}else{
			$beatplans = NULL;
			$beatPlanCount = 0;
		}
		$queryHolidays = Holiday::where('company_id',$company_id)->where('start_date','<=',$request->engLastDate)->where('end_date','>=',$request->engFirstDate)->orderBy('start_date','ASC');
		$holidays = $queryHolidays->get();
		if(empty($holidays)){
			$holidays = NULL;
		}
        $data=$this->ajaxEvents($request->engFirstDate,$beatplans, $holidays);
		$holidaysCount = $queryHolidays->count();
		if($beatPlanCount > $holidaysCount){
			$data['maxCount'] = $beatPlanCount;
		}else{
			$data['maxCount'] = $holidaysCount;
		}
        $data['year'] = $request->getYear;
		$data['month'] = $request->getMonth;
        return $data;
	}
	
	private function ajaxEvents($engFirstDate,$beatplans, $holidays){
        $FirstRowFirstDate = $engFirstDate;
        $FirstRowLastDate = Carbon::parse($engFirstDate)->addDays(6)->format('Y-m-d');
        $SecondRowFirstDate = Carbon::parse($engFirstDate)->addDays(7)->format('Y-m-d');
        $SecondRowLastDate = Carbon::parse($engFirstDate)->addDays(13)->format('Y-m-d');
        $ThirdRowFirstDate = Carbon::parse($engFirstDate)->addDays(14)->format('Y-m-d');
        $ThirdRowLastDate = Carbon::parse($engFirstDate)->addDays(20)->format('Y-m-d');
        $FourthRowFirstDate = Carbon::parse($engFirstDate)->addDays(21)->format('Y-m-d');
        $FourthRowLastDate = Carbon::parse($engFirstDate)->addDays(27)->format('Y-m-d');
        $FifthRowFirstDate = Carbon::parse($engFirstDate)->addDays(28)->format('Y-m-d');
        $FifthRowLastDate = Carbon::parse($engFirstDate)->addDays(34)->format('Y-m-d');
        $SixthRowFirstDate = Carbon::parse($engFirstDate)->addDays(35)->format('Y-m-d');
        $SixthRowLastDate = Carbon::parse($engFirstDate)->addDays(41)->format('Y-m-d');
        $i=0;
		$data = [];

		if(!empty($holidays)){
			foreach($holidays as $holiday){

				if($holiday->start_date<=$FirstRowFirstDate && $holiday->end_date>=$FirstRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $FirstRowFirstDate;
					$data['holidays'][$i]['end_date'] = $FirstRowLastDate;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}elseif($holiday->start_date<=$FirstRowFirstDate && $holiday->end_date>=$FirstRowFirstDate && $holiday->end_date<=$FirstRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $FirstRowFirstDate;
					$data['holidays'][$i]['end_date'] = $holiday->end_date;            
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;

				}elseif($holiday->start_date>=$FirstRowFirstDate && $holiday->start_date<=$FirstRowLastDate && $holiday->end_date>=$FirstRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $holiday->start_date;
					$data['holidays'][$i]['end_date'] = $FirstRowLastDate;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}elseif($holiday->start_date>=$FirstRowFirstDate && $holiday->end_date<=$FirstRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $holiday->start_date;
					$data['holidays'][$i]['end_date'] = $holiday->end_date;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}

				// Second row
				if($holiday->start_date<=$SecondRowFirstDate && $holiday->end_date>=$SecondRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $SecondRowFirstDate;
					$data['holidays'][$i]['end_date'] = $SecondRowLastDate;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}elseif($holiday->start_date<=$SecondRowFirstDate && $holiday->end_date>=$SecondRowFirstDate && $holiday->end_date<=$SecondRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $SecondRowFirstDate;
					$data['holidays'][$i]['end_date'] = $holiday->end_date;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;

				}elseif($holiday->start_date>=$SecondRowFirstDate && $holiday->start_date<=$SecondRowLastDate && $holiday->end_date>=$SecondRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $holiday->start_date;
					$data['holidays'][$i]['end_date'] = $SecondRowLastDate;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}elseif($holiday->start_date>=$SecondRowFirstDate && $holiday->end_date<=$SecondRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $holiday->start_date;
					$data['holidays'][$i]['end_date'] = $holiday->end_date;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}

				// Third Row
				if($holiday->start_date<=$ThirdRowFirstDate && $holiday->end_date>=$ThirdRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $ThirdRowFirstDate;
					$data['holidays'][$i]['end_date'] = $ThirdRowLastDate;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}elseif($holiday->start_date<=$ThirdRowFirstDate && $holiday->end_date>=$ThirdRowFirstDate && $holiday->end_date<=$ThirdRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $ThirdRowFirstDate;
					$data['holidays'][$i]['end_date'] = $holiday->end_date;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;

				}elseif($holiday->start_date>=$ThirdRowFirstDate && $holiday->start_date<=$ThirdRowLastDate && $holiday->end_date>=$ThirdRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $holiday->start_date;
					$data['holidays'][$i]['end_date'] = $ThirdRowLastDate;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}elseif($holiday->start_date>=$ThirdRowFirstDate && $holiday->end_date<=$ThirdRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $holiday->start_date;
					$data['holidays'][$i]['end_date'] = $holiday->end_date;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}

				//Fourth Row
				if($holiday->start_date<=$FourthRowFirstDate && $holiday->end_date>=$FourthRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $FourthRowFirstDate;
					$data['holidays'][$i]['end_date'] = $FourthRowLastDate;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}elseif($holiday->start_date<=$FourthRowFirstDate && $holiday->end_date>=$FourthRowFirstDate && $holiday->end_date<=$FourthRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $FourthRowFirstDate;
					$data['holidays'][$i]['end_date'] = $holiday->end_date;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;

				}elseif($holiday->start_date>=$FourthRowFirstDate && $holiday->start_date<=$FourthRowLastDate && $holiday->end_date>=$FourthRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $holiday->start_date;
					$data['holidays'][$i]['end_date'] = $FourthRowLastDate;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}elseif($holiday->start_date>=$FourthRowFirstDate && $holiday->end_date<=$FourthRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $holiday->start_date;
					$data['holidays'][$i]['end_date'] = $holiday->end_date;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}
				//Fifth Row
				if($holiday->start_date<=$FifthRowFirstDate && $holiday->end_date>=$FifthRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $FifthRowFirstDate;
					$data['holidays'][$i]['end_date'] = $FifthRowLastDate;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}elseif($holiday->start_date<=$FifthRowFirstDate && $holiday->end_date>=$FifthRowFirstDate && $holiday->end_date<=$FifthRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $FifthRowFirstDate;
					$data['holidays'][$i]['end_date'] = $holiday->end_date;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;

				}elseif($holiday->start_date>=$FifthRowFirstDate && $holiday->start_date<=$FifthRowLastDate && $holiday->end_date>=$FifthRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $holiday->start_date;
					$data['holidays'][$i]['end_date'] = $FifthRowLastDate;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}elseif($holiday->start_date>=$FifthRowFirstDate && $holiday->end_date<=$FifthRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $holiday->start_date;
					$data['holidays'][$i]['end_date'] = $holiday->end_date;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}

				// Sixth row
				if($holiday->start_date<=$SixthRowFirstDate && $holiday->end_date>=$SixthRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $SixthRowFirstDate;
					$data['holidays'][$i]['end_date'] = $SixthRowLastDate;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}elseif($holiday->start_date<=$SixthRowFirstDate && $holiday->end_date>=$SixthRowFirstDate && $holiday->end_date<=$SixthRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $SixthRowFirstDate;
					$data['holidays'][$i]['end_date'] = $holiday->end_date;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;

				}elseif($holiday->start_date>=$SixthRowFirstDate && $holiday->start_date<=$SixthRowLastDate && $holiday->end_date>=$SixthRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $holiday->start_date;
					$data['holidays'][$i]['end_date'] = $SixthRowLastDate;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}elseif($holiday->start_date>=$SixthRowFirstDate && $holiday->end_date<=$SixthRowLastDate){
					$data['holidays'][$i]['id'] = $holiday->id;
					$data['holidays'][$i]['name'] = $holiday->name;
					$data['holidays'][$i]['description'] = $holiday->description;
					$data['holidays'][$i]['start_date'] = $holiday->start_date;
					$data['holidays'][$i]['end_date'] = $holiday->end_date;
					$data['holidays'][$i]['ostart_date'] = $holiday->start_date;
					$data['holidays'][$i]['oend_date'] = $holiday->end_date;
					$data['holidays'][$i]['color'] = '#f58641';
					$i++;
				}
			// End Of rows data

			}
		}
		$i=0;
		if(!empty($beatplans)){
			foreach($beatplans as $beatplan){
	
			  if($beatplan->start_date>=$FirstRowFirstDate && $beatplan->start_date<=$FirstRowLastDate){
				$data['beatplans'][$i]['id'] = $beatplan->id;
				$data['beatplans'][$i]['beatdetailid'] = $beatplan->beatDetailID;
				$data['beatplans'][$i]['name'] = $beatplan->name;
				$data['beatplans'][$i]['description'] = $beatplan->description;
				$data['beatplans'][$i]['start_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['end_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['ostart_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['oend_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['color'] = '#dc1212';
				$i++;
			  }elseif($beatplan->start_date>=$SecondRowFirstDate && $beatplan->start_date<=$SecondRowLastDate){
				$data['beatplans'][$i]['id'] = $beatplan->id;
				$data['beatplans'][$i]['beatdetailid'] = $beatplan->beatDetailID;
				$data['beatplans'][$i]['name'] = $beatplan->name;
				$data['beatplans'][$i]['description'] = $beatplan->description;
				$data['beatplans'][$i]['start_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['end_date'] =  $beatplan->start_date;
				$data['beatplans'][$i]['ostart_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['oend_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['color'] = '#dc1212';
				$i++;
			  }elseif($beatplan->start_date>=$ThirdRowFirstDate && $beatplan->start_date<=$ThirdRowLastDate){
				$data['beatplans'][$i]['id'] = $beatplan->id;
				$data['beatplans'][$i]['beatdetailid'] = $beatplan->beatDetailID;
				$data['beatplans'][$i]['name'] = $beatplan->name;
				$data['beatplans'][$i]['description'] = $beatplan->description;
				$data['beatplans'][$i]['start_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['end_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['ostart_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['oend_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['color'] = '#dc1212';
				$i++;
			  }elseif($beatplan->start_date>=$FourthRowFirstDate && $beatplan->start_date<=$FourthRowLastDate){
				$data['beatplans'][$i]['id'] = $beatplan->id;
				$data['beatplans'][$i]['beatdetailid'] = $beatplan->beatDetailID;
				$data['beatplans'][$i]['name'] = $beatplan->name;
				$data['beatplans'][$i]['description'] = $beatplan->description;
				$data['beatplans'][$i]['start_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['end_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['ostart_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['oend_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['color'] = '#dc1212';
				$i++;
			  }elseif($beatplan->start_date>=$FifthRowFirstDate && $beatplan->start_date<=$FifthRowLastDate){
				$data['beatplans'][$i]['id'] = $beatplan->id;
				$data['beatplans'][$i]['beatdetailid'] = $beatplan->beatDetailID;
				$data['beatplans'][$i]['name'] = $beatplan->name;
				$data['beatplans'][$i]['description'] = $beatplan->description;
				$data['beatplans'][$i]['start_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['end_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['ostart_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['oend_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['color'] = '#dc1212';
				$i++;
			  }elseif($beatplan->start_date>=$SixthRowFirstDate && $beatplan->start_date<=$SixthRowLastDate){
				$data['beatplans'][$i]['id'] = $beatplan->id;
				$data['beatplans'][$i]['beatdetailid'] = $beatplan->beatDetailID;
				$data['beatplans'][$i]['name'] = $beatplan->name;
				$data['beatplans'][$i]['description'] = $beatplan->description;
				$data['beatplans'][$i]['start_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['end_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['ostart_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['oend_date'] = $beatplan->start_date;
				$data['beatplans'][$i]['color'] = '#dc1212';
				$i++;
			  }
			  // End Of rows data
	
			}
		}

        return $data;

    }
}
