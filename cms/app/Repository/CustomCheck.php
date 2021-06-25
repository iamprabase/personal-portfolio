<?php

namespace App\Repository;

use App\Party;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;
use Storage;

class CustomCheck
{
    public $collection;
    public $request;
    public $party_meta;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($collection, $request,$party_meta=null)
    {
        $this->collection = $collection;
        $this->request    = $request;
        $this->party_meta = $party_meta;
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

                        if (!empty($this->request[$column->slug . '2'])) {
                            $monetary_value = $this->request[$column->slug . '2'] . ' ' . $this->request[$column->slug];
                            $array[$column->id] = trim($monetary_value, " ");
                        }else{
                            $array[$column->id] = null;
                        }
                        break;
                    case 'User':
                    case 'Party':
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
                        # code...
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    case 'Time':
                        # code...
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    case 'Time range':
                        # code...
                        $array[$column->id] = $this->request[$column->slug] . '-' . $this->request[$column->slug . '2'];
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
                        $array[$column->id] = json_encode($this->request[$column->slug]);
                        # code...
                        break;
                        //added by nishan for custom field
                    case 'Check Box':
                        $array[$column->id] = json_encode($this->request[$column->slug]);
                        break;
                    case 'Radio Button':
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    case 'Multiple Images':
                        $tempImageArray = [];
                        if ($this->request->file($column->slug)) {
                            $company_id = config('settings.company_id');
                            $companyName = Auth::user()->companyName($company_id)->domain;
                            $this->request->validate([
                                $column->slug.'.*' => 'mimes:jpeg,png,jpg,svg|max:2000',
                            ]);

                            foreach ($this->request->file($column->slug) as $receipt) {
                                $receipt2 = $receipt;
                                $realname = pathinfo($receipt->getClientOriginalName(), PATHINFO_FILENAME);
                                $extension = $receipt2->getClientOriginalExtension();
                                $new_name = str_replace(' ','-',$realname) . "-" . time() . '.' . $extension;
                                $receipt2->storeAs('public/uploads/' . $companyName . '/party/', $new_name);
                                $path = Storage::url('app/public/uploads/' . $companyName . '/party/' . $new_name);
                                $tempImageArray[$new_name][] = $path;
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
                    case 'File':
                        $tempImageArray = [];
                        if ($this->request->file($column->slug)) {
                            $this->request->validate([
                                $column->slug.'.*' => 'mimes:pdf,xls,xlsx,doc,docx,csv,txt|max:2000',
                            ]);
                            $company_id = config('settings.company_id');
                            $companyName = Auth::user()->companyName($company_id)->domain;

                            foreach ($this->request->file($column->slug) as $receipt) {
                                $receipt2 = $receipt;
                                $realname = pathinfo($receipt->getClientOriginalName(), PATHINFO_FILENAME);
                                $extension = $receipt2->getClientOriginalExtension();
                                $new_name = str_replace(' ','-',str_limit($realname,30,'')) . "-" . time() . '.' . $extension;
                                $receipt2->storeAs('public/uploads/' . $companyName . '/party/files/', $new_name);
                                $path = Storage::url('app/public/uploads/' . $companyName . '/party/files/' . $new_name);
                                $tempImageArray[$new_name][] = $path;
                            }
                        }else{

                        if($this->party_meta){
                            $old_meta_datas =json_decode($this->party_meta->cf_value);
                            $deleted_images = explode(',',$this->request[$column->slug.'-deleted']);
                            foreach($old_meta_datas as $key => $old_meta_data){
                                if($key==$column->id){
                                    $old_images = json_decode($old_meta_data);
                                    foreach($old_images as $k => $img){
                                        if(!in_array($k,$deleted_images)){
                                            $tempImageArray[$k][] = $img[0];
                                        }
                                    }
                                }
                            }
                        }
                    }
                        $array[$column->id] = json_encode($tempImageArray);
                        break;
                        case 'File2':
                        $tempImageArray = [];
                        if ($this->request->file($column->slug)) {
                            $this->request->validate([
                                $column->slug.'.*' => 'mimes:pdf,doc,docx,csv,txt|max:2000',
                            ]);
                            $company_id = config('settings.company_id');
                            $companyName = Auth::user()->companyName($company_id)->domain;

                            foreach ($this->request->file($column->slug) as $receipt) {
                                $receipt2 = $receipt;
                                $realname = pathinfo($receipt->getClientOriginalName(), PATHINFO_FILENAME);
                                $extension = $receipt2->getClientOriginalExtension();
                                $new_name = str_replace(' ','-',$realname) . "-" . time() . '.' . $extension;
                                $receipt2->storeAs('public/uploads/' . $companyName . '/party/files/', $new_name);
                                $path = Storage::url('app/public/uploads/' . $companyName . '/party/files/' . $new_name);
                                $tempImageArray[$new_name][] = $path;
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

}