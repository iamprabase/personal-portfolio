<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Plan;

class PlanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        $plans = Plan::all()->sortByDesc("created_at");;
        return view('plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('plans.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customMessages = [
            'name' => 'The Plan Name field is required.',
            'users' => 'No. of Users is required.',
            'users.numeric' => 'Users field should be in number.',
            'duration.required' => 'Duration field is required.',
            'duration.numeric' => 'Duration field should be in number.',
        ];


        $this->validate($request, [
            'name' => 'required|unique:plans',
            'users' => 'required|numeric',
            'duration' => 'required|numeric',
        ], $customMessages);

        $plan = new \App\Plan;
        $plan->name = $request->get('name');
        $plan->users = $request->get('users');
        $plan->duration = $request->get('duration');
        $plan->duration_in = $request->get('duration_in');
        $plan->description = $request->get('description');
        $plan->status = $request->get('status');

        $plan->save();
        return redirect()->route('app.plan')->with('success', 'Information has been  Added');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plan = plan::find($id);
        return view('plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        return view('plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $customMessages = [
            'name' => 'The Plan Name field is required.',
            'users' => 'No. of Users is required.',
            'users.numeric' => 'Users field should be in number.',
            'duration.required' => 'Duration field is required.',
            'duration.numeric' => 'Duration field should be in number.',
        ];


        $this->validate($request, [
            'name' => 'required|unique:plans,name,' . $id,
            'users' => 'required|numeric',
            'duration' => 'required|numeric',
        ], $customMessages);

        $plan = plan::findOrFail($id);
        $plan->name = $request->get('name');
        $plan->users = $request->get('users');
        $plan->duration = $request->get('duration');
        $plan->duration_in = $request->get('duration_in');
        $plan->description = $request->get('description');
        $plan->status = $request->get('status');
        $plan->save();

        return redirect()->route('app.plan')->with('success', 'Information has been  Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $plan = Plan::findOrFail($request->id);
        $plan->delete();
        flash()->success('plan has been deleted.');
        return back();
    }

}
