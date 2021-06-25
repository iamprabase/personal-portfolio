<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\MarketArea;

class MarkerAreaController extends Controller
{
    public function manageMarketArea()
    {
        $marketareas = Category::where('parent_id', '=', 0)->get();
        $allMarketareas = Category::pluck('title', 'id')->all();
        return view('company.marketarea.index', compact('marketareas', 'allMarketareas'));
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function addMarketarea(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $input = $request->all();
        $input['parent_id'] = empty($input['parent_id']) ? 0 : $input['parent_id'];

        MarketArea::create($input);
        return back()->with('success', 'New Area added successfully.');
    }
}
