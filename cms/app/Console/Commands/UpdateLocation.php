<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Attendance;

class UpdateLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:location';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $nolocs = Attendance::select('id','latitude', 'longitude', 'address','company_id','employee_id','adate','check_datetime','atime')
            ->whereNull('address')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude','!=',0)
            ->where('longitude','!=',0)
            ->where('latitude','!=',0.000000)
            ->where('longitude','!=',0.000000)
            ->where('from_file',0)
            //->where('check_type',1)
            // ->where('adate','2020-12-15')
            // ->where('company_id',193)
            // ->where('employee_id',2679)
            ->orderBy('id', 'asc')
            ->limit(1000)
            ->get();


           // dd($nolocs);
            
            $i=1;
            foreach($nolocs as $noloc){

              //date_default_timezone_set($this->getTimeZone($noloc->company_id));

               $newtimestamp1=strtotime($noloc->atime.' - 1 minute');
               $time1=date('H:i:s', $newtimestamp1);
              $newtimestamp2 = strtotime($noloc->atime.' + 1 minute');
              //echo "<br>";
              $time2=date('H:i:s', $newtimestamp2);
              // echo "<br>";
              $fileName = getFileName($noloc->company_id, $noloc->employee_id, $noloc->adate);

              //echo $fileName;
              $locations = getFileLocationWithRange($fileName,$time1,$time2);

              $exists = Storage::disk("local")->exists($fileName);
              $fileContent = $exists ? Storage::get($fileName) : "";
              $decodedContent = empty($fileContent) ? array() : json_decode($fileContent, true);

              //dd($decodedContent);

              //dd($locations);
              $accuracy=60;
               $loc_lat=$noloc->latitude;
              $loc_lang=$noloc->longitude;
              foreach ($decodedContent as $location) {
                if($location['accuracy']>0 && $location['accuracy']<60 and $location['accuracy']<$accuracy){
                  $accuracy =$location['accuracy'];

                  $loc_lat=$location['latitude'];
                  $loc_lang=$location['longitude'];
                }
             }

             $address= $this->getaddress($loc_lat,$loc_lang);

             //echo $address;

             if($address)
                {
                  $updateAttendance = Attendance::findOrFail($noloc->id);
                  $updateAttendance->address = $address;
                  $updateAttendance->from_file = 1;
                  $updateAttendance->save();
                }

          // echo $noloc->latitude; echo "--";
          // echo $noloc->longitude;
           // if($noloc->latitude || $noloc->longitude){
           // $address= $this->getaddress($noloc->latitude,$noloc->longitude);
              // if($address)
              // {
              //   $updateAttendance = Attendance::findOrFail($noloc->id);
              //   $updateAttendance->address = $address;
              //   $updateAttendance->save();
              // }
              
          //}

             echo " ".$i++." ";

        }

       //echo "Attendance Location Updated from Location File";
      
        $this->info('Attendance Location Updated from Location File');
    }
    
    public function getaddress($lat,$lng)
      {
         $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&key=AIzaSyDR6v2elDctrDptLyvTjpTBEs6z7CLSfW8';
    
         $json = @file_get_contents($url);
         $data=json_decode($json);
         $status = $data->status;
         if($status=="OK")
         {
           return $data->results[0]->formatted_address;
         }
         else
         {
           return false;
         }
      }
}
