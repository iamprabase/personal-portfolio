<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee;
use App\Note;
use Auth;
use DB;
use Log;

class NoteController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:api');
		$this->middleware('permission:note-view');
		$this->middleware('permission:note-create', ['only' => ['create','store']]);
		$this->middleware('permission:note-edit', ['only' => ['edit','update']]);
		$this->middleware('permission:note-delete', ['only' => ['destroy']]);
	}

	public function index($return = false, $tempPostData = null)
	{
			$postData = $return ? $tempPostData : $this->getJsonRequest();

			$user = Auth::user();
			$companyID = $user->company_id;
			$employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
			$employeeID = $employee->id;

			/*Check if unsynced data is available . if available first update to tha database */
			$syncStatus = $this->manageUnsyncedMeeting($postData);

			$meetings = DB::table('meetings')
					->select('meetings.*', 'clients.company_name', 'employees.name as employee_name')
					->leftJoin('employees', 'meetings.employee_id', 'employees.id')
					->leftJoin('clients', 'meetings.client_id', '=', 'clients.id')
					->where('meetings.company_id', $companyID)
					->where('meetings.employee_id', $employeeID)
					->whereNull('meetings.deleted_at')
					->orderBy('created_at', 'desc')
					->get()->toArray();

			if (empty($meetings)) {
					if ($return) {
							return array();
					} else {
							$this->sendEmptyResponse();
					}
			}

			$finalArray = array();
			foreach ($meetings as $key => $value) {
					$imageArray = getImageArray("notes", $value->id, $companyID, $employeeID);
					$value->image_ids   = json_encode($this->getArrayValue($imageArray, "image_ids"));
					if ($value->image_ids=="null") {
							$value->image_ids = null;
					}
					$value->images = json_encode($this->getArrayValue($imageArray, "images"));
					if ($value->images=="null") {
							$value->images = null;
					}
					$value->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
					if ($value->image_paths=="null") {
							$value->image_paths = null;
					}
					array_push($finalArray, $value);
			}

			$response = array("status" => true, "message" => "Success", "data" => $finalArray);

			if ($return) {
					return $meetings;
			} else {
					$this->sendResponse($response);
			}
	}

	public function store($postData = null)
	{
			$postData = $this->getJsonRequest();
			$user = Auth::user();
			$companyID = $user->company_id;
			$employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
			$employeeID = $employee->id;

			$note_id = $this->getarrayValue($postData, "id");
			$unique_id = (string)$this->getarrayValue($postData, "unique_id");
			$note = Note::where('company_id', $companyID)->where('id', $note_id)->first();
			
			if (!$note) {
					$note = new Note;
					$note->company_id  = $companyID;
					$note->employee_id = $employeeID;

			}

			$audio = $this->getArrayValue($postData, "audio");
			$tempAudioPath = null;
			if (!empty($audio)) {
					$tempAudioName = md5(uniqid(mt_rand(), true)).'.mp3';
					$tempAudioDir = $this->getImagePath($companyID, "notes");
					$tempAudioPath = $tempAudioDir . "/" . $tempAudioName;
					$put = \Storage::disk('public')->put($tempAudioDir . '/' . $tempAudioName, base64_decode($audio));
			}

			$note->unique_id   = $unique_id;
      $clientID          = $this->getarrayValue($postData, "client_id");      
      $clientUniqueID    = $this->getarrayValue($postData, "client_unique_id");
      if(isset($clientUniqueID) && empty($clientID)) throw new \Exception('Client Id cannot be null');
			$note->client_id   = $clientID;
			$note->checkintime = $this->getArrayValue($postData, "checkintime");
			$note->meetingdate = $this->getArrayValue($postData, "meetingdate");
			$note->remark      = $this->getArrayValue($postData, "remark", "N/A");
			$note->latitude    = $this->getArrayValue($postData, "latitude");
			$note->longitude   = $this->getArrayValue($postData, "longitude");
			$note->audio_note  = $tempAudioPath;
			$note->created_at  = $this->getArrayValue($postData, "created_at");
			$note->updated_at  = $this->getArrayValue($postData, "updated_at");
			$note->save();

			$createdAt = $this->getArrayValue($postData, "created_at");

			$images = $this->getArrayValue($postData, "images");

			$tempImageNames = array();
			$tempImagePaths = array();
			$imageArray     = array();
			$images_ids     = [];
			$images_names   = [];
			$images_paths   = [];

			if (!empty($images)) {
					foreach ($images as $key => $value) {

							$tempImageName = $this->getImageName();
							$tempImageDir  = $this->getImagePath($companyID, 'notes');
							$tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
							$put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
							array_push($tempImageNames, $tempImageName);
							array_push($tempImagePaths, $tempImagePath);
							$imageArray[$tempImageName] = $tempImagePath;
					}
			}

			//Check For Updated Images
			if ($note_id) {
					$deleted_images_id = $this->getArrayValue($postData, "deleted_images_id");
					if (!empty($deleted_images_id)) {
							foreach ($deleted_images_id as $deleted_image) {
									$instance = DB::table('images')->whereId($deleted_image)->first();
									if ($instance) {
											$image_path = $instance->image_path;
											DB::table('images')->whereId($deleted_image)->delete();
											try{
												\Storage::delete('/public'.str_replace('/storage', '', $image_path));
											}catch(\Exception $e){
												Log::info("Unlinking Note image error");
												Log::info($e->getMessage());
												Log::info($image_path);
											}
									}
							}
					}

					$updated_images = $this->getArrayValue($postData, "edited_images");

					if (!empty($updated_images)) {
							foreach ($updated_images as $key => $value) {
									$tempImageName = $this->getImageName();
									$tempImageDir  = $this->getImagePath($companyID, 'notes');
									$tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
									$put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
									// DB::table('images')->insert([
									// 		"type"        => "notes",
									// 		"type_id"     => $note_id,
									// 		"company_id"  => $note->company_id,
									// 		"employee_id" => $note->employee_id,
									// 		"image"       => $tempImageName,
									// 		"image_path"  => $tempImagePath,
									// 		"created_at"  => $createdAt
									// ]);
									array_push($tempImageNames, $tempImageName);
									array_push($tempImagePaths, $tempImagePath);
									$imageArray[$tempImageName] = $tempImagePath;
									unset($tempImageName);
									unset($tempImagePath);
							}
					}
			}

			// if(!empty($audio)){
			//   $tempAudioName = md5(uniqid(mt_rand(), true)).'.mp3';
			//   $tempAudioDir  = $this->getImagePath($companyID, "notes");
			//   $tempAudioPath = $tempAudioDir . "/" . $tempAudioName;
			//   $put = \Storage::disk('public')->put($tempAudioDir . '/' . $tempAudioName, base64_decode($audio));
			// }

			if ($note) {
					if (!empty($imageArray)) {
							$imageData = array();
							foreach ($imageArray as $imageName => $imagePath) {
									$tempArray                = array();
									$tempArray["type"]        = "notes";
									$tempArray["type_id"]     = $note->id;
									$tempArray["company_id"]  = $note->company_id;
									$tempArray["employee_id"] = $note->employee_id;
									$tempArray["image"]       = $imageName;
									$tempArray["image_path"]  = $imagePath;
									$tempArray["created_at"]  = $this->getArrayValue($postData, "created_at");
									array_push($imageData, $tempArray);
							}
							DB::table('images')->insert($imageData);
					}

					$finalImages = DB::table('images')->where('type', 'notes')->where('type_id', $note->id)->whereNull('deleted_at')->get();
					foreach ($finalImages as $finalImage) {
							array_push($images_ids, $finalImage->id);
							array_push($images_names, $finalImage->image);
							array_push($images_paths, $finalImage->image_path);
					}

					$note->image_ids   = json_encode($images_ids);
					if ($note->image_ids=='[]') {
							$note->image_ids = null;
					}
					$note->images      = json_encode($images_names);
					if ($note->images=='[]') {
							$note->images = null;
					}
					$note->image_paths = json_encode($images_paths);
					if ($note->image_paths=='[]') {
							$note->image_paths = null;
					}

					// if(!empty($tempImageNames) && !empty($tempImagePaths)){
					// 	$note->images = json_encode($tempImageNames);
					// 	$note->image_paths = json_encode($tempImagePaths);
					// }else{
					// 	$note->images = null;
					// 	$note->image_paths = null;
					// }

					if ($note_id) {
              $msg = "Updated Note";
              
              if($employeeID != $note->employee_id){
                $dataPayload = array("data_type" => "note", "note" => $note, "action" => "update");
                $msgID = sendPushNotification_(getFBIDs($companyID, null, $note->employee_id), 19, null, $dataPayload);
              }

					} else {
							$msg = "Added Note";
					}

					$nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "notes", $note);
			}
			$response = array("status" => true, "message" => "successfully saved", "data" => $note);
			$this->sendResponse($response);
	}

	public function destroy(Request $request)
	{
			$user = Auth::user();
			$company_id = $user->company_id;
			$employee = Employee::where('company_id', $company_id)->where('user_id', $user->id)->first();
			$employeeID = $employee->id;
			// if (Auth::user()->isCompanyManager()) {
          $note = Note::where('company_id', $company_id)->where('id', $request->id)->first();
          Log::info($note);
			// } else {
			// 		$juniors = Employee::EmployeeChilds($employeeID, array());
			// 		$note = Note::where('company_id', $company_id)->where('id', $request->id)->whereIn('employee_id', $juniors)->first();
			// }
			if (!$note) {
					return response(['status'=>false,'error'=>'No note found or no permission access to delete this note.']);
			}
			$note->delete();
      $nSaved = saveAdminNotification($company_id, $employeeID, date("Y-m-d H:i:s"), 'Note Deleted', "note", $note);
      
      if ($note->employee_id != $employeeID) {
        $dataPayload = array("data_type" => "note", "note" => $note, "action" => "delete");
        $msgID       = sendPushNotification_(getFBIDs($note->company_id, null, $note->employee_id), 19, null, $dataPayload);
      }

			return response(['status'=>true,'message'=>'Note Deleted']);
	}

	public function syncMeeting()
	{
			$postData = $this->getJsonRequest();
			$arraySyncedData = $this->manageUnsyncedMeeting($postData, true);
			$response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
			$this->sendResponse($response);
	}

	private function manageUnsyncedMeeting($postData, $returnItems = false, $client = null)
	{
			$rawData = $this->getArrayValue($postData, "nonsynced_meeting");
			// Log::info('info', array('postData'=>print_r($rawData,true)));
			$user = Auth::user();
			$companyID = $user->company_id;
			$employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
			$employeeID = $employee->id;

			if (empty($rawData)) {
					return $returnItems ? array() : false;
			}
			$data = json_decode($rawData, true);
			$arraySyncedData = array();
			foreach ($data as $key => $meeting) {
					$audio     = $this->getArrayValue($meeting, "audio");
          $note_id   = $this->getarrayValue($meeting, "id");
					$unique_id = $this->getarrayValue($meeting, "unique_id");
          $note      = Note::where('company_id', $companyID)->where('id', $note_id)->orWhere('unique_id', $unique_id)->first();
          $created = false;
					if (!$note) {
							$note = new Note;
							$note->company_id  = $companyID;
              $note->employee_id = $employeeID;
              $created = true;
					}
					$tempAudioPath = null;
					if (!empty($audio)) {
							$tempAudioName = md5(uniqid(mt_rand(), true)).'.mp3';
							$tempAudioDir = $this->getImagePath($companyID, "notes");
							$tempAudioPath = $tempAudioDir . "/" . $tempAudioName;
							$put = \Storage::disk('public')->put($tempAudioDir . '/' . $tempAudioName, base64_decode($audio));
					}
					$clientID = $this->getarrayValue($meeting, "client_id");
					$note->unique_id   = $unique_id;
					$note->client_id   = $clientID;
					$note->checkintime = $this->getArrayValue($meeting, "checkintime");
					$note->meetingdate = $this->getArrayValue($meeting, "meetingdate");
					$note->remark      = $this->getArrayValue($meeting, "remark", "N/A");
					$note->latitude    = $this->getArrayValue($meeting, "latitude");
					$note->longitude   = $this->getArrayValue($meeting, "longitude");
					$note->audio_note  = $tempAudioPath;
					$note->created_at  = $this->getArrayValue($meeting, "created_at");
					$note->updated_at  = $this->getArrayValue($meeting, "updated_at");
					$note->save();

					$images     = $this->getArrayValue($meeting, "images");
					$imagePaths = $this->getArrayValue($meeting, "image_paths");

					$images_ids     = [];
					$images_names   = [];
					$images_paths   = [];

					if ($note) {
							$imageArray = array();
							$tempImageNames = array();
							$tempImagePaths = array();

							//saving images
							if (!empty($imagePaths) && $created) {
									$jsonDecoded = json_decode($images, true);

									foreach ($jsonDecoded as $key => $value) {
											$tempImageName = $this->getImageName();
											$tempImageDir = $this->getImagePath($companyID, "notes");
											$tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
											$decodedData = base64_decode($value);
											$put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
											array_push($tempImageNames, $tempImageName);
											array_push($tempImagePaths, $tempImagePath);
											$imageArray[$tempImageName] = $tempImagePath;
									}

									if (!empty($imageArray)) {
											$imageData = array();
											foreach ($imageArray as $imageName => $imagePath) {
													$tempImageArray = array();
													$tempImageArray["type"] = "notes";
													$tempImageArray["type_id"] = $note->id;
													$tempImageArray["company_id"] = $note->company_id;
													$tempImageArray["employee_id"] = $note->employee_id;
													$tempImageArray["image"] = $imageName;
													$tempImageArray["image_path"] = $imagePath;
													$tempImageArray["created_at"] = $note->created_at;
													array_push($imageData, $tempImageArray);
                          DB::table('images')->insert($tempImageArray);
											}
											// $finalImages = DB::table('images')->where('type', 'notes')->where('type_id', $note->id)->whereNull('deleted_at')->get();
											// foreach ($finalImages as $finalImage) {
											// 		array_push($images_ids, $finalImage->id);
											// 		array_push($images_names, $finalImage->image);
											// 		array_push($images_paths, $finalImage->image_path);
											// }
									}
              }
              $finalImages = DB::table('images')->where('type', 'notes')->where('type_id', $note->id)->whereNull('deleted_at')->get();
              if($finalImages->first()){
                foreach ($finalImages as $finalImage) {
                  array_push($images_ids, $finalImage->id);
                  array_push($images_names, $finalImage->image);
                  array_push($images_paths, $finalImage->image_path);
                }
              }
							// $note->images = $tempImageNames;
							// $note->image_paths = $tempImagePaths;
							$note->image_ids   = json_encode($images_ids);
							if ($note->image_ids=='[]') {
									$note->image_ids = null;
							}
							$note->images      = json_encode($images_names);
							if ($note->images=='[]') {
									$note->images = null;
							}
							$note->image_paths = json_encode($images_paths);
							if ($note->image_paths=='[]') {
									$note->image_paths = null;
							}
							array_push($arraySyncedData, $note);

							if ($note_id) {
									$msg = "Updated Note";
							} else {
									$msg = "Added Note";
							}

							saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "notes", $note);
					}
			}
			return $returnItems ? $arraySyncedData : false;
	}

	//common methods
	private function sendEmptyResponse()
	{
			$response = array("status" => true, "message" => "No Record Found", "data" => array());
			echo json_encode($response);
			exit;
	}

	private function getArrayValue($arraySource, $key, $emptyText = null, $trim = false)
	{
			if (is_array($arraySource) && !empty($arraySource[$key])) {
					return $trim == true ? trim($arraySource[$key]) : $arraySource[$key];
			} else {
					return $emptyText;
			}
	}

	private function sendResponse($response)
	{
			echo json_encode($response);
			exit;
	}

	private function getJsonRequest($isJson = true)
	{
			if ($isJson) {
					return json_decode($this->getFileContent(), true);
			} else {
					return $_POST;
			}
	}

	private function getFileContent()
	{
			return file_get_contents('php://input');
	}

	private function getImageName()
	{
			$imagePrefix = md5(uniqid(mt_rand()*time(), true));
			$imageName = $imagePrefix . ".png";
			return $imageName;
	}

	private function getImagePath($companyID, $module = "common", $imageName = "")
	{
			if (empty($companyID)) {
					return "";
			}
			$domain = DB::table("companies")->where("id", $companyID)->where("is_active", 2)->pluck("domain")->first();
			if (empty($domain)) {
					return "";
			}

			if (empty($imageName)) {
					$imagePath = "uploads/" . $domain . "/" . $module;
			} else {
					$imagePath = "uploads/" . $domain . "/" . $module . "/" . $imageName;
			}
			return $imagePath;
	}
}
