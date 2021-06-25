<?php

namespace App\Repository;

use App\Party;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;
use Storage;

class CustomModuleUpdate
{
    public $collection;
    public $request;
    public $party_meta;
    public $data;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($collection, $request, $data = null)
    {
        $this->collection = $collection;
        $this->request = $request;
        $this->data = $data;
    }

    public function check()
    {
        $array = [];
        foreach ($this->collection as $column) {
//            if ($this->request[$column->slug]) {
            //switch-case for validation based on $column->type
            switch ($column->type) {
                case 'Text':
                    $this->request->validate([
                        $column->slug => 'max:255'
                    ]);
                    $array[$column->id] = $this->request[$column->slug];
                    break;
                case 'Numerical':
                case 'User':
                case 'Party':
                    # code...
//                    $this->request->validate([
//                        $column->slug => 'numeric'
//                    ]);
                    $array[$column->id] = $this->request[$column->slug];
                    break;
                case 'Large text':
                    # code...
                    $this->request->validate([
                        $column->slug => 'max:500'
                    ]);
                    $array[$column->id] = $this->request[$column->slug];
                    break;
                case 'Monetary':
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
                case 'Phone':
                case 'Address':
                case 'Time':
                case 'Date':
                case 'Date range':
                case 'Single option':
                case 'Radio Button':
                    # code...
                    $array[$column->id] = $this->request[$column->slug];
                    break;
                case 'Time range':
                    # code...
                    $array[$column->id] = $this->request[$column->slug] . '-' . $this->request[$column->slug . '2'];
                    break;
                case 'Multiple options':
                    $array[$column->id] = json_encode($this->request[$column->slug]);
                    # code...
                    break;
                case 'Check Box':
                    $array[$column->id] = json_encode($this->request[$column->slug]);
                    break;
                case 'Multiple Images':
                    $tempImageArray = [];
                    if ($this->request->file($column->slug)) {
                        $company_id = config('settings.company_id');
                        $companyName = Auth::user()->companyName($company_id)->domain;
                        $this->request->validate([
                            $column->slug . '.*' => 'mimes:jpeg,png,jpg,svg|max:2000',
                        ]);
                        foreach ($this->request->file($column->slug) as $receipt) {
                            $receipt2 = $receipt;
                            $realname = pathinfo($receipt->getClientOriginalName(), PATHINFO_FILENAME);
                            $extension = $receipt2->getClientOriginalExtension();
                            $new_name = str_replace(' ', '-', $realname) . "-" . time() . '.' . $extension;
                            $receipt2->storeAs('public/uploads/' . $companyName . '/party/', $new_name);
                            $path = Storage::url('app/public/uploads/' . $companyName . '/party/' . $new_name);
                            $tempImageArray[$new_name][] = $path;
                        }

                        $imageAfterDeleted = [];
                        if (($this->request[$column->slug . '-deleted'])) {
                            $old_images = json_decode($this->data[$column->slug]);
                            $deleted_image = explode(',', $this->request[$column->slug . '-deleted']);
                            foreach ($old_images as $key => $image) {
                                if (!in_array($key, $deleted_image)) {
                                    $imageAfterDeleted[$key][] = $image[0];
                                }
                            }
                            $merged_images = array_merge($imageAfterDeleted, $tempImageArray);
                        } else {
                            $old_images = $this->data ? (array)json_decode($this->data[$column->slug]) : null;
                            if ($old_images) {
                                $merged_images = array_merge($old_images, $tempImageArray);
                            } else {
                                $merged_images = $tempImageArray;
                            }

                        }
                        $array[$column->id] = json_encode($merged_images);
                        break;
                    }
                    else if (!is_null($this->request[$column->slug . '-deleted'])) {
                        $old_images = json_decode($this->data[$column->slug]);
                        $deleted_image = explode(',', $this->request[$column->slug . '-deleted']);
                        foreach ($old_images as $key => $image) {
                            if (!in_array($key, $deleted_image)) {
                                $tempImageArray[$key][] = $image[0];
                            }
                        }
                        $array[$column->id] = json_encode($tempImageArray);
                        break;
                    } else {
                        $array[$column->id] = $this->data[$column->slug];
                        break;
                    }
                case 'File':
                    if ($this->request[$column->slug]) {
                        $tempImageArray = [];
                        if ($this->request->file($column->slug)) {
                            $this->request->validate([
                                $column->slug . '.*' => 'mimes:pdf,xls,xlsx,csv,doc,docx,txt|max:2000',
                            ]);
                            $company_id = config('settings.company_id');
                            $companyName = Auth::user()->companyName($company_id)->domain;

                            foreach ($this->request->file($column->slug) as $receipt) {
                                $receipt2 = $receipt;
                                $realname = pathinfo($receipt->getClientOriginalName(), PATHINFO_FILENAME);
                                $extension = $receipt2->getClientOriginalExtension();
                                $new_name = str_replace(' ', '-', str_limit($realname,30,'')) . "-" . time() . '.' . $extension;
                                $receipt2->storeAs('public/uploads/' . $companyName . '/party/files/', $new_name);
                                $path = Storage::url('app/public/uploads/' . $companyName . '/party/files/' . $new_name);
                                $tempImageArray[$new_name][] = $path;
                            }
                        }
                        $array[$column->id] = json_encode($tempImageArray);
                        break;
                    } else {
                        $array[$column->id] = $this->data[$column->slug];
                        break;
                    }
                case 'File2':
                    $tempImageArray = [];
                    if ($this->request->file($column->slug)) {
                        $this->request->validate([
                            $column->slug . '.*' => 'mimes:pdf,doc,docx,csv,txt|max:2000',
                        ]);
                        $company_id = config('settings.company_id');
                        $companyName = Auth::user()->companyName($company_id)->domain;

                        foreach ($this->request->file($column->slug) as $receipt) {
                            $receipt2 = $receipt;
                            $realname = pathinfo($receipt->getClientOriginalName(), PATHINFO_FILENAME);
                            $extension = $receipt2->getClientOriginalExtension();
                            $new_name = str_replace(' ', '-', $realname) . "-" . time() . '.' . $extension;
                            $receipt2->storeAs('public/uploads/' . $companyName . '/party/files/', $new_name);
                            $path = Storage::url('app/public/uploads/' . $companyName . '/party/files/' . $new_name);
                            $tempImageArray[$new_name][] = $path;
                        }
                    }

                    if ($this->party_meta) {
                        $old_meta_datas = json_decode($this->party_meta->cf_value);
                        $deleted_images = explode(',', $this->request[$column->slug . '-deleted']);
                        foreach ($old_meta_datas as $key => $old_meta_data) {
                            if ($key == $column->id) {
                                $old_images = json_decode($old_meta_data);
                                foreach ($old_images as $k => $img) {
                                    if (!(in_array($k, $deleted_images))) {
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
        }
//        }
        return $array;
    }

}