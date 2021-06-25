<?php


namespace App\Repository;

use App\Party;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\File\File;
use Validator;
use Storage;
use Log;
use DB;



class CustomModuleUpdateApi
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
    public function __construct($collection, $request, $party_meta = null, $companyID = null)
    {
        $this->collection = $collection;
        $this->request = $request;
        $this->party_meta = $party_meta;
        $this->companyID = $companyID;
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
                    case 'User':
                    case 'Party':
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
                    case 'Time':
                    case 'Date':
                    case 'Date range':
                    case 'Address':
                    case 'Single option':
                        # code...
                        $array[$column->id] = $this->request[$column->slug];
                        break;
                    case 'Time range':
                        # code...
                        $array[$column->id] = $this->request[$column->slug] . ' ' . $this->request[$column->slug . '2'];
                        break;
                    case 'Phone':
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
                        $images = explode(',', $this->request[$column->slug]);
                        $tempImageArray = [];
                        if ($images != "1") {
                            if (!empty($images)) {
                                foreach ($images as $key => $value) {
                                    $company_id = $this->companyID;
                                    $companyName = Auth::user()->companyName($company_id)->domain;
                                    $tempImageName = $this->getImageName();
                                    $tempImageDir = $this->getImagePath($company_id, 'party');
                                    $tempImagePath = "/storage/app/public/uploads/" . $companyName . "/party/" . $tempImageName;
                                    $decodedData = base64_decode($value);
                                    $put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                                    $tempImageArray[$tempImageName][] = $tempImagePath;
                                }
                            }
                        }
                        $array[$column->id] = json_encode($tempImageArray);
                        break;
                    case 'File':
                        if (!empty($this->data[$column->slug]))
                        {
                            break;
                        }else{
                            $files = $this->request[$column->slug];
                            $tempImageArray = [];
                            $ext = $this->request[$column->slug . '-ext'];
                            if (!empty($files)) {
                                $company_id = $this->companyID;
                                $companyName = Auth::user()->companyName($company_id)->domain;
                                $tempImageName = $this->getFileName($ext);
                                $tempImageDir = $this->getImagePath($company_id, 'party');

                                $extension = $this->getFileExtension($files, $tempImageName);

                                $tempImagePath = "/storage/app/public/uploads/" . $companyName . "/party/" . $tempImageName . $extension;

                                \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName . $extension, base64_decode($files));

                                $tempImageArray[$tempImageName . $extension][] = $tempImagePath;
                            }
                            $array[$column->id] = json_encode($tempImageArray);
                            break;

                        }
                    default:
                        # code...
                        break;
                }
            }
        }
        return $array;
    }

    private function getFileExtension($files, $tempImageName)
    {
        $fileData = base64_decode($files);
        // save it to temporary dir first.
        $tmpFilePath = sys_get_temp_dir() . '/' . $tempImageName;
        file_put_contents($tmpFilePath, $fileData);
        // this just to help us get file info.
        $tmpFile = new File($tmpFilePath);
        $_file = new UploadedFile(
            $tmpFile->getPathname(),
            $tmpFile->getFilename(),
            $tmpFile->getMimeType(),
            0,
            true
        );
        return $_file->extension();
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
        $imageName = $imagePrefix . '.' . $ext;
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