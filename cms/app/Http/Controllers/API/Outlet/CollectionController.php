<?php

namespace App\Http\Controllers\API\Outlet;

use App\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;

class CollectionController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api-outlets');
  }

  public function index(Client $client)
  {

    try{
      $client_collections = $client->collections;
      if($client_collections)
        $collections = CollectionResource::collection($client_collections);
      else
        $collections = null;
      $data = array("status"=> 200, "msg"=> "Account Status", "data" => $collections);

      return $data;
    }catch(Exception $e){
      $data = array("status"=> 400, "msg"=> $e->getMessage(), "data" => array());

      return $data;
    }
    

    return $data;
  }
}
