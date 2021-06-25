<?php

namespace App\Repository;

use App\Party;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Storage;
use Log;
use DB;

class CustomCheckApi
{
    public $collection;
    public $request;
    public $party_meta;
    public $companyID;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($collection, $request,$party_meta=null,$companyID=null)
    {
        $this->collection = $collection;
        $this->request    = $request;
        $this->party_meta = $party_meta;
        $this->companyID  = $companyID;
    }

    public function check()
    {
        $array = [];
        foreach ($this->collection as $column) {
            if ($this->request[$column->slug]) {
                //switch-case for validation based on $column->type
                switch ($column->type) {
                    case 'Text':
                        # code...
                        $this->request->validate([
                            $column->slug => 'max:255'
                        ]);
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    case 'Numerical':
                        # code...
                        $this->request->validate([
                            $column->slug => 'numeric'
                        ]);
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    case 'Large text':
                        # code...
                        $this->request->validate([
                            $column->slug => 'max:500'
                        ]);
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    // case 'Autocomplete':
                    //     # code...
                    //     // $array[$column->id]= $this->request[$column->slug];
                    //     if (!is_numeric($this->request[$column->slug])) {
                    //         $value = AutoComplete::where('name', $this->request[$column->slug])->where('for', $column->id);
                    //         if ($value->count()) {
                    //             $array[$column->id] = $value->first()->id;
                    //         } else {
                    //             $auto_complete = AutoComplete::create([
                    //                 'name' => $this->request[$column->slug],
                    //                 'for' => $column->id,
                    //             ]);
                    //             $array[$column->id] = $auto_complete->id;
                    //         }
                    //     } else {
                    //         $array[$column->id] = $this->request[$column->slug];
                    //     }
                    //     break;
                    case 'Monetary':
                        # code...
                        // $this->request->validate([
                        //     $column->slug=> 'digits:10'
                        // ]);
                        if (!$this->request[$column->slug]) {
                            $this->request[$column->slug] = 0;
                        }

                        $monetary_value = $this->request[$column->slug . '2'] . ' ' . $this->request[$column->slug];
                        $array[$column->id] = trim($monetary_value, " ");
                        break;
                    case 'User':
                        # code...
                        $this->request->validate([
                            $column->slug => 'numeric'
                        ]);
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    // case 'Person':
                    //     # code...
                    //     // $this->request->validate([
                    //     //     $column->slug=> 'numeric'
                    //     // ]);
                    //     if (!is_numeric($this->request[$column->slug])) {
                    //         $contact = Contact::create([
                    //             'name' => $this->request[$column->slug],
                    //             'user_id' => Auth::id(),
                    //         ]);

                    //         $array_assign = [
                    //             'previous' => 'New contact created by ' . Auth::user()->name,
                    //         ];
                    //         $assignedHistory = new AssignedHistory($array_assign);
                    //         $contact->assignedHistories()->save($assignedHistory);

                    //         $array[$column->id] = $contact->id;
                    //     } else {
                    //         $array[$column->id] = $this->request[$column->slug];
                    //     }

                    //     // $array[$column->id]= $this->request[$column->slug];
                    //     break;
                    // case 'Organization':
                    //     # code...
                    //     // $this->request->validate([
                    //     //     $column->slug=> 'numeric'
                    //     // ]);
                    //     if (!is_numeric($this->request[$column->slug])) {
                    //         $organization = Organization::create([
                    //             'name' => $this->request[$column->slug],
                    //             'user_id' => Auth::id(),
                    //         ]);
                    //         $array_assign = [
                    //             'previous' => 'New organization created by ' . Auth::user()->name,
                    //         ];
                    //         $assignedHistory = new AssignedHistory($array_assign);
                    //         $organization->assignedHistories()->save($assignedHistory);

                    //         $array[$column->id] = $organization->id;
                    //     } else {
                    //         $array[$column->id] = $this->request[$column->slug];
                    //     }
                    //     // $array[$column->id]= $this->request[$column->slug];
                    //     break;
                    case 'Phone':
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    case 'Time':
                        # code...
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    case 'Time range':
                        # code...
                        $array[$column->id] = $this->request[$column->slug] . ' ' . $this->request[$column->slug . '2'];
                        break;
                    case 'Date':
                        # code...
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    case 'Date range':
                        # code...
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    case 'Address':
                        # code...
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    case 'Single option':
                        # code...
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    case 'Multiple options':
                        $array[$column->id] = $this->request[$column->slug];
                        # code...
                        break;
                    case 'Multiple Images':
                        $images = json_decode($this->request[$column->slug]);
                        $tempImageArray = [];
                        //$companyID = $this->companyID;
                        // $this->request->validate([
                        //     $column->slug.'.*' => 'mimes:jpeg,png,jpg,pdf,doc,docx,zip|max:2000',
                        // ]);

                        if($images!="1"){
                            if (!empty($images)) {
                                foreach ($images as $key => $value) {
                                    $company_id = $this->companyID;
                                    $companyName = Auth::user()->companyName($company_id)->domain;
                                    $tempImageName = $this->getImageName();
                                    $tempImageDir  = $this->getImagePath($company_id, 'party');
                                    $tempImagePath = "/storage/app/public/uploads/" . $companyName . "/party/" . $tempImageName;
                                    $decodedData   = base64_decode($value);
                                    $put           = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                                    $tempImageArray[$tempImageName][] = $tempImagePath;
                                }
                            }
                        }

                        // Log::info('info', array("party_meta"=>print_r($this->party_meta,true)));

                        if($this->party_meta){

                            $old_meta_datas =json_decode($this->party_meta->cf_value);

                            Log::info('info', array("old_meta_images"=>print_r($old_meta_datas,true)));


                            $deleted_images = explode(',',$this->request[$column->slug.'-deleted']);

                            foreach($old_meta_datas as $key => $old_meta_data){
                                Log::info('info', array("old_images"=>print_r($old_meta_data,true)));
                                if($key==$column->id){
                                    $old_images = json_decode($old_meta_data);
                                    foreach($old_images as $k => $img){
                                        if(!(in_array($k,$deleted_images))){
                                            $tempImageArray[$k][] = $img[0];
                                        }
                                    }
                                }
                            }
                        }
                        $array[$column->id] = json_encode($tempImageArray);
                        break;
                    case 'File':
                        $files = json_decode($this->request[$column->slug]);
                        $tempImageArray = [];
                        $companyID = $this->companyID;
                        // $this->request->validate([
                        //     $column->slug.'.*' => 'mimes:pdf,doc,docx,zip|max:2000',
                        // ]);

                        Log::info('info', array("customfield keys"=>print_r($this->request[$column->slug.'-ext'],true)));


                        if($files!="1"){

                            $ext = $this->request[$column->slug.'-ext'];
                            if (!empty($files)) {
                                foreach ($files as $key => $value) {
                                    $company_id = $this->companyID;
                                    $companyName = Auth::user()->companyName($company_id)->domain;
                                    $tempImageName = $this->getFileName($ext);
                                    $tempImageDir  = $this->getImagePath($company_id, 'party');
                                    $tempImagePath = "/storage/app/public/uploads/" . $companyName . "/party/" . $tempImageName;
                                    $decodedData   = base64_decode($value);
                                    $put           = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                                    $tempImageArray[$tempImageName][] = $tempImagePath;
                                }
                            }
                        }

                        if($this->party_meta){
                            $old_meta_datas =json_decode($this->party_meta->cf_value);
                            $deleted_images = explode(',',$this->request[$column->slug.'-deleted']);
                            foreach($old_meta_datas as $key => $old_meta_data){
                                if($key==$column->id){
                                    $old_images = json_decode($old_meta_data);
                                    foreach($old_images as $k => $img){
                                        if(!(in_array($k,$deleted_images))){
                                            $tempImageArray[$k][] = $img[0];
                                        }
                                    }
                                }
                            }
                        }
                        $array[$column->id] = json_encode($tempImageArray);
                        break;
                    default:
                        # code...
                        break;
                }
                // abort(404, 'From Custom Check');
                // $array[$column->slug]= $request[$column->slug];
            }
        }
        return $array;
    }


    private function getImageName()
    {
        $imagePrefix = md5(uniqid(mt_rand(), true));
        $imageName = $imagePrefix . ".png";
        return $imageName;
    }

    private function getFileName($ext)
    {
        $imagePrefix = md5(uniqid(mt_rand(), true));
        $imageName = $imagePrefix .'.'.$ext;
        return $imageName;
    }

    private function getImagePath($companyID, $module = "common", $imageName = "")
    {
        if (empty($companyID)) return "";
        $domain = DB::table("companies")->where("id", $companyID)->where("is_active", 2)->pluck("domain")->first();
        if (empty($domain)) return "";

        if (empty($imageName)) {
            $imagePath = "uploads/" . $domain . "/" . $module;
        } else {
            $imagePath = "uploads/" . $domain . "/" . $module . "/" . $imageName;
        }
        return $imagePath;
    }

}