<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Beat;
use App\BeatVPlan;
use App\BeatPlansDetails;
use App\Client;
use DB;
use View;
use Log;

class BeatController extends Controller
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
        $beats = Beat::whereCompanyId($company_id)->orderBy('id', 'desc')->get();
        return view('company.beats.index', compact('beats'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('company.beats.create');
    }

    public function assignedParties(){
        $company_id = config('settings.company_id');  
        $beats =  Beat::whereCompanyId($company_id)->pluck('id')->toArray();
        $assignedParties = DB::table("beat_client")->whereIn('beat_id', $beats)->pluck('client_id')->toArray();
        
        return $assignedParties;
    }

    public function fetchCityParties(Request $request){
        $company_id = config('settings.company_id');
        if($request->has("beatId") && isset($request->beatId)){
          $getPartyIds = DB::table('beat_client')->where('beat_id', $request->beatId)->pluck('client_id')->toArray();
        }else{
          $getPartyIds = array();
        }

        if(isset($request->city) && $request->city!="null")
          $parties["parties"] = Client::where('company_id',$company_id)->where('city', $request->city)->orWhereIn('id', $getPartyIds)->orderBy('company_name','ASC')->get(['company_name', 'id']);
        else
          $parties["parties"] = Client::where('company_id', $company_id)->orderBy('company_name', 'ASC')->get(['company_name', 'id']);

        if(isset($request->beatId)){
          // $parties["beatParties"] = DB::table("beat_client")->where("beat_id", $request->beatId)->orWhereIn('client_id', $getPartyIds)->pluck('client_id')->toArray();
          $parties["beatParties"] = DB::table("beat_client")->where("beat_id", $request->beatId)->pluck('client_id')->toArray();
          if(!empty($getPartyIds)){
            foreach($getPartyIds as $getPartyId){
              array_push($parties["beatParties"], $getPartyId);
            }
          }
          $companyBeats = Beat::whereCompanyId($company_id)->where("id", "<>",$request->beatId)->pluck('id')->toArray();
          $parties["assignedParties"] = DB::table("beat_client")->whereIn('beat_id', $companyBeats)->pluck('client_id')->toArray();
        }else{
          $parties["beatParties"] = array();
          $companyClients = Client::where('company_id', $company_id)->pluck('id')->toArray();
          $assignedParties = DB::table("beat_client")->whereIn('client_id', $companyClients)->pluck('client_id')->toArray();
          if(!empty($assignedParties)) $parties["assignedParties"] = array_map('intval', $assignedParties);
          else $parties["assignedParties"] = array();
        }
        
        return $parties;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store($domain,Request $request)
    {
        $box = $request->all();        
        $requests=  array();
        parse_str($box['data'], $requests);
        $company_id = config('settings.company_id');
        $beatExists = Beat::where('company_id',$company_id)->where('name',$requests['name'])->first();
        if($beatExists!=null){
            $data['result']=false;
        }else{

            $beat = new Beat();
            $beat->company_id = $company_id;
            $beat->name = $requests['name'];
            $beat->city_id = ($requests['beatcity']==0)?null:$requests['beatcity'];
            if (!empty($requests['partyId']) && $requests['beatcity']==0) {
              $clientCities = Client::where('company_id', $company_id)->whereIn('id', $requests['partyId'])->whereNotNull('city')->distinct('city')->pluck('city')->toArray();
              if(!empty($clientCities) && sizeof($clientCities)==1){
                $beat->city_id = $clientCities[0];
              }
            }
            $beat->save();
            if( !empty($requests['partyId'])){
              $beat->clients()->attach($requests['partyId']);
            }
            
            $beats = Beat::where('company_id',$company_id)->orderBy('name','ASC')->get();
            $beatsPlanDetails = DB::table('beatplansdetails')->pluck('beat_id');
            $beatsArray[] = [];
            foreach($beatsPlanDetails as $beatPlan){
                $beatPlan = explode(",",$beatPlan);
                $beatsArray = array_merge($beatsArray,$beatPlan); 
            }            
            $data['result']=true;
            $data['beats'] = View::make('company.settings.ajaxbeatlists',compact('beats','beatsArray'))->render();

            $beatObject = $beat;
            if( !empty($requests['partyId'])){
              $tempClientArray = $requests['partyId'];
              $beatObject->client_id = array_map('intval', $tempClientArray);
            }else{
              $beatObject->client_id = array();
            }
            $dataPayload = array("data_type" => "beat", "beat" => $beatObject, "action" => "add");
            $msgSent = sendPushNotification_(getFBIDs($company_id), 13, null, $dataPayload);
        }
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\beat $beat
     * @return \Illuminate\Http\Response
     */
    public function show($domain,$id)
    {
        $beat = Beat::findOrFail($id);
        $count = $beat->parties()->count();
        if($count>0){
          $parties = $beat->parties;
          $data['beatname'] = $beat->name;
          foreach($parties as $party){
              $data['name'][] = $party->company_name;
          }          
        }else{
          $data['result'] = "No parties available";
        }
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\beat $beat
     * @return \Illuminate\Http\Response
     */
    public function edit($domain, Request $request, $id)
    {

        $company_id = config('settings.company_id');
        $beat = Beat::findOrFail($id);
        $getPartyIds = DB::table('beat_client')->where('beat_id', $id)->pluck('client_id')->toArray();

        if(isset($beat->city_id))
          $selectedBeatParties=$beat->parties->where('city', $beat->city_id);
        else
          $selectedBeatParties=$beat->parties;
        $data['selected_beat_party']=[];
        foreach($selectedBeatParties as $party){
            array_push($data['selected_beat_party'], $party->id);
        }
        if(!empty($getPartyIds)){
          foreach($getPartyIds as $getPartyId){
            array_push($data['selected_beat_party'], (int)$getPartyId);
          }
        }

        
        if(isset($request->cityVal) && $request->cityVal!="0"){
          $parties = Client::where('company_id',$company_id)->where('city', $request->cityVal)->orWhereIn('id', $getPartyIds)->orderBy('company_name','ASC')->get();
        }else{
          $parties = Client::where('company_id', $company_id)->orderBy('company_name', 'ASC')->get();
        }

        $data['all_beat_party']=[];
        foreach($parties as $party){
            array_push($data['all_beat_party'], ['id'=>$party->id,'company_name'=>$party->company_name]); 
        }

        $data['other_beat_party']=[];
        $companyBeats = Beat::whereCompanyId($company_id)->where("id", "<>",$id)->pluck('id')->toArray();
          
        $other_beat_parties = DB::table("beat_client")->whereIn('beat_id', $companyBeats)
                                                    ->get();
        foreach($other_beat_parties as $other){
            array_push($data['other_beat_party'], $other->client_id); 
        }

        return $data;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\beat $beat
     * @return \Illuminate\Http\Response
     */
    public function update($domain,Request $request, $id)
    {
        $box = $request->all();        
        $myValue=  array();
        parse_str($box['data'], $myValue);
        $company_id = config('settings.company_id');
        $beat = Beat::findOrFail($id);
        $beatExists = Beat::where('company_id',$company_id)->where('id','<>',$id)->where('name',$myValue['name'])->first();
        if(!(empty($myValue['partyId']))){
            array_walk($myValue['partyId'], function(&$x){$x = intval($x);});
            $prevClients = DB::table('beat_client')->where('beat_id', $id)->pluck('client_id')->toArray();
            if(count($myValue['partyId'])>count($prevClients)){
                $diffWithPrev = array_diff($myValue['partyId'], $prevClients);
            }else{
                $diffWithPrev = array_diff($prevClients,$myValue['partyId']);
            }
        }else{
            $diffWithPrev = array();
            $myValue['partyId'] = array();
        }
        if(!$beatExists || count($diffWithPrev)>0){
            // if($beat->name == $myValue['name']){
                if($beat->name != $myValue['name']){
                    $beat['old_name'] = $beat->name;
                }
                $beat['name'] = $myValue['name'];
                if(array_key_exists('edit_beatcity', $myValue)){
                  $beat['city_id'] = ($myValue['edit_beatcity']!="null")?$myValue['edit_beatcity']:null;
                }elseif (!array_key_exists('edit_beatcity', $myValue)) {
                  $clientCities = Client::where('company_id', $company_id)->whereIn('id', $myValue['partyId'])->whereNotNull('city')->distinct('city')->pluck('city')->toArray();
                  if(!empty($clientCities) && sizeof($clientCities)==1){
                    $beat['city_id'] = $clientCities[0];
                  }
                }
                $beat->save();
                DB::table('beat_client')->where('beat_id', $id)->delete();
                $beat->clients()->attach($myValue['partyId']);
                $today = date('Y-m-d');
                $existingFutureBeatPlans = BeatVPlan::where('company_id', $company_id)->join('beatplansdetails', 'beatplansdetails.beatvplan_id','beatvplans.id')->where('beatplansdetails.plandate', '>=',$today)->get();
                // if($existingFutureBeatPlans->count()>0){
                //     foreach($existingFutureBeatPlans as $existingFutureBeatPlan){
                //         if(isset($existingFutureBeatPlan->beat_id)){
                //             $beatIDS = explode(',',$existingFutureBeatPlan->beat_id);
                //             $beatClientIDS = json_decode($existingFutureBeatPlan->beat_clients, true);
                //             foreach($beatClientIDS as $key=>$beatClientID){
                //                 if((int)$key==$id){
                //                     foreach($beatClientID as $beatClientId){
                //                         if(!in_array($beatClientId, $myValue['partyId'])){
                //                             $_ind = array_search($beatClientId,$beatClientID);
                //                             unset($beatClientIDS[$key][$_ind]);
                //                             $beatClientIDS[0][] = $beatClientId;
                //                         }elseif(in_array($beatClientId, $myValue['partyId']) && (int)$key!=$id){
                //                             $_ind = array_search($beatClientId,$beatClientID);
                //                             unset($beatClientIDS[$key][$_ind]);
                //                             $beatClientIDS[$id][] = $beatClientId;
                //                         }
                //                     }
                //                 }
                //             }
                //             if(!(array_key_exists($id, $beatClientIDS))){
                //                 $clIDS = explode(',',$existingFutureBeatPlan->client_id);
                //                 $arrayIntersect = array_intersect($clIDS, $myValue['partyId']);
                //                 if(count($arrayIntersect)>0){
                //                     foreach($arrayIntersect as $comValue){
                //                         $_ind = array_search($comValue,$beatClientID);
                //                         unset($beatClientIDS[$key][$_ind]);
                //                         $beatClientIDS[$id][] = $comValue;
                //                     }
                //                 } 
                //             }
                //             $beatPlansUpdate = BeatPlansDetails::where('id', $existingFutureBeatPlan->id)->first();
                //             $newClientIds = array();
                //             foreach($beatClientIDS as $key=>$beatClientid){
                //                 if(empty($beatClientid)){
                //                     unset($beatClientIDS[$key]);
                //                 }
                //                 foreach($beatClientid as $clId){
                //                     array_push($newClientIds, $clId);
                //                 }
                //             }
                //             $newBeat_Ids = DB::table('beat_client')->whereIn('client_id', $newClientIds)->groupBy('beat_id')->pluck('beat_id')->toArray();
                //             if(array_key_exists(0, $beatClientIDS)){
                //                 array_push($newBeat_Ids, 0);
                //             }
                //             $beatPlansUpdate->client_id = implode(',',$newClientIds);
                //             $beatPlansUpdate->beat_id = implode(',',$newBeat_Ids);
                //             $beatPlansUpdate->beat_clients = json_encode($beatClientIDS);
                //             if($beatPlansUpdate->client_id==""){
                //                 $beatPlansUpdate->delete();
                //                 $beatVplan = BeatPlansDetails::where('beatvplan_id',$existingFutureBeatPlan->beatvplan_id)->get();
                //                 if($beatVplan->count() ==0){
                //                     BeatVPlan::where('id', $existingFutureBeatPlan->beatvplan_id)->delete();
                //                 }
                //             }else{
                //                 $beatPlansUpdate->save();
                //             }
                //         }
                //     }
                // }
                $beats = Beat::where('company_id',$company_id)->orderBy('name','ASC')->get();
                $beatsPlanDetails = DB::table('beatplansdetails')->pluck('beat_id');
                $beatsArray[] = [];
                foreach($beatsPlanDetails as $beatPlan){
                    $beatPlan = explode(",",$beatPlan);
                    $beatsArray = array_merge($beatsArray,$beatPlan); 
                }
                $data['beats'] = View::make('company.settings.ajaxbeatlists',compact('beats','beatsArray'))->render();
                $data['result'] = true;
            // }
        }else{
            $data['result'] = false;
        }
        $beatObject = $beat;
        $tempClientArray = $myValue['partyId'];
        $beatObject->client_id = array_map("intval", $tempClientArray);
        if(array_key_exists('edit_beatcity', $myValue)){
          $beatObject->city_id = $myValue['edit_beatcity'];
        }
        $dataPayload = array("data_type" => "beat", "beat" => $beatObject, "action" => "update");
        $msgSent = sendPushNotification_(getFBIDs($company_id), 13, null, $dataPayload);
        return $data;
        // return redirect()->route('beat', ['domain' => domain()])->with('success', 'Information has been  Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\beat $beat
     * @return \Illuminate\Http\Response
     */
    public function destroy($domain,$id,Request $request)
    {
        $beat = Beat::findOrFail($id);
        $beat->delete();
        $company_id = config('settings.company_id');
        DB::table('beat_client')->where('beat_id', $request->id)->delete();
        
        
        $beats = Beat::where('company_id',$company_id)->orderBy('name','ASC')->get();
        $beatsPlanDetails = DB::table('beatplansdetails')->pluck('beat_id');
        $beatsArray[] = [];
        foreach($beatsPlanDetails as $beatPlan){
            $beatPlan = explode(",",$beatPlan);
            $beatsArray = array_merge($beatsArray,$beatPlan); 
        }
        $data['beats'] = View::make('company.settings.ajaxbeatlists',compact('beats','beatsArray'))->render();
        $data['result']="Successfully Deleted";
        $dataPayload = array("data_type" => "beat", "beat" => $beat, "action" => "delete");
        $msgSent = sendPushNotification_(getFBIDs($company_id), 13, null, $dataPayload);
        return $data;
    }
}
