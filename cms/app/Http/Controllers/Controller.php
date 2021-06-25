<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private function logErrorException($exception){
      Log::error(
        array(
          "File : - ", $exception->getFile(), 
          "Line : - ", $exception->getLine(),
          "Message : - ", $exception->getMessage(),
        )
      );
    }
}
