<?php

namespace App\Http\Controllers\API;

use App\CustomModule;
use App\Http\Controllers\Controller;
use Auth;

class CustomModuleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');

    }

    public function getAllCustomModule()
    {
        $user = Auth::user();
        $company_id = $user->company_id;

        return response()->json([
            'customModules' => CustomModule::where('company_id', $company_id)
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }
}
