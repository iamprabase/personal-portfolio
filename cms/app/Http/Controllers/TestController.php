<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;


class TestController extends Controller
{

    public function index()
    {

        //Log::info('info', array("mahen" => print_r("mahen", true)));
        return view('test.index');
    }
}

