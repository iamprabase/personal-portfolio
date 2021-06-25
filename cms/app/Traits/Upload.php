<?php 

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;

trait Upload
{
    public function upload(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $filename = null, $compression = true)
    {
        $name = !is_null($filename) ? $filename : Str::random(25).time();
        try{
          $extension = $uploadedFile->getClientOriginalExtension();
          if(!$extension){
            $extension = $uploadedFile->extension();
            if(!$extension) $extension = explode('/', $uploadedFile->getMimeType())[1];
          }
          if(in_array($extension, ['png', 'jpg', 'jpeg', 'gif'])){
            $img = InterventionImage::make($uploadedFile->getRealPath());
            $height = $img->height();
            $width = $img->width();
            if($compression) {
              $encoded = $img->resize($height, $width, function ($constraint) {
                $constraint->aspectRatio();
              })->stream('', 60);
              $file = Storage::disk($disk)->put(
                $folder.'/'.$name.'.'.$extension,
                $encoded
              );
            } else {
              $file = $uploadedFile->storeAs($folder, $name.'.'.$extension, $disk);
            }
          }else{
            $file = $uploadedFile->storeAs($folder, $name.'.'.$extension, $disk);
          }
          $data = [
            'file_name' => $name,
            'path' => $folder.'/'.$name.'.'.$extension
          ];
          return $data;
        }catch(\Exception $e){
          Log::info($e->getMessage());
          return null;
        }
    }
}