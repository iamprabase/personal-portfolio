<?php
namespace App\Http\Controllers\Company\Admin;
use DB;
use Session;
use App\User;
Use Auth;
use App\UnitTypes;
use App\UnitRelation;
use App\UnitConversion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;

class UnitController extends Controller
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
		if(config('settings.product')==1 && Auth::user()->can('settings-view') || Auth::user()->employee()->first()->role()->first()->name=="Full Access"){
			$company_id = config('settings.company_id');
			$units = UnitTypes::where('company_id', $company_id)
	// ->orWhere('company_id', 0)
			->orderBy('created_at', 'desc')
			->get();
			return view('company.units.index', compact('units'));
		}
		return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
  }
  
  public function unitconversion(){
    // if(config('settings.product')==0 || !Auth::user()->can('settings-view')){
    //   return redirect()->back();
    // }
    if(config('settings.product')==1 && Auth::user()->can('settings-view') || Auth::user()->employee()->first()->role()->first()->name=="Full Access"){
    $company_id = config('settings.company_id');
    $defined_converted_units = UnitConversion::where('company_id', $company_id)->get(['id', 'quantity', 'unit_type_id', 'converted_quantity', 'converted_unit_type_id']);
    $conversionUnits = $defined_converted_units->pluck('unit_type_id')->toArray();
    $convertedUnits = $defined_converted_units->pluck('converted_unit_type_id')->toArray();
    $unitsCount = $defined_converted_units->count();

    $units = UnitTypes::where('company_id', $company_id)
              ->orderBy('name', 'asc')
              ->get(['id', 'name', 'symbol'])->toArray();
    return view('company.units.conversion', compact('units', 'defined_converted_units', 'conversionUnits', 'convertedUnits', 'unitsCount'));
    }
    	return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
  }

  public function storeunitconversion(Request $request){
    $company_id = config('settings.company_id');
    
    $this->validate($request, [
      'conversion_unit.*' => 'required|integer|min:1',
      'conversion-unit-symbol.*' => 'required',
      'converted_unit.*' => 'required|integer|min:1',
      'converted-unit-symbol.*' => 'required'
    ]);
    $indexes = $request->get('index');
    $conversion_unit = $request->get('conversion_unit');
    $conversion_unit_symbol = $request->get('conversion-unit-symbol');
    $converted_unit = $request->get('converted_unit');
    $converted_unit_symbol = $request->get('converted-unit-symbol');
    try{
      DB::beginTransaction();
      $sendingProduct = array();
      foreach($indexes as $index){
        $instance = UnitConversion::create([
          'company_id' => $company_id,
          'quantity' => $conversion_unit[$index],
          'unit_type_id' => $conversion_unit_symbol[$index],
          'converted_quantity' => $converted_unit[$index],
          'converted_unit_type_id' => $converted_unit_symbol[$index],          
        ]);
        array_push($sendingProduct, $instance);
        $inserted = UnitRelation::create([
          'unit_type_id' => $conversion_unit_symbol[$index],
          'related_unit_type_id' => $converted_unit_symbol[$index],
          'unit_conversion_id' => $instance->id
        ]); 
        $inserted = UnitRelation::create([
          'unit_type_id' => $converted_unit_symbol[$index],
          'related_unit_type_id' => $conversion_unit_symbol[$index],
          'unit_conversion_id' => $instance->id
        ]);
      }
      if($inserted){
        DB::commit();
        $dataPayload = array("data_type" => "unit_conversion", "unit_conversion" => $sendingProduct, "action" => "add");
        $msgID = sendPushNotification_(getFBIDs($company_id), 40, null, $dataPayload);
        $request->session()->flash('success', 'Successfully Added.');    
      }else{
        $request->session()->flash('error', 'Something went wrong.');    
        DB::rollback();
      }
      return back();
    }catch(Exception $e){
      DB::rollback();
      return back()->with(['error'=>$e->getMessage()]);
    }
  }


  public function updateunitconversion(Request $request){
    $company_id = config('settings.company_id');
    $id = $request->get('unit_id');
    $instance = UnitConversion::findOrFail($id);
    $conversion_unit = $request->get('conversion_unit');
    $conversion_unit_symbol = $request->get('conversion-unit-symbol');
    $converted_unit = $request->get('converted_unit');
    $converted_unit_symbol = $request->get('converted-unit-symbol');
    
    $check_duplicate = UnitConversion::where('company_id', $company_id)->where('id', '<>',$id)
    ->where('unit_type_id', $conversion_unit_symbol)->where('quantity', $conversion_unit)->where('converted_unit_type_id', $converted_unit_symbol)->where('converted_quantity', $converted_unit)->count();
    $check_duplicate_opp = UnitConversion::where('company_id', $company_id)->where('id', '<>',$id)
    ->where('converted_unit_type_id', $conversion_unit_symbol)->where('converted_quantity', $conversion_unit)->where('unit_type_id', $converted_unit_symbol)->where('quantity', $converted_unit)->count();
    if($check_duplicate>0 || $check_duplicate_opp>0){
      $request->session()->flash('error', 'Duplicate Conversion for similar units cannot be added.');
    }else{
      $relationInstanceFirst = UnitRelation::where('unit_conversion_id', $id)->where('unit_type_id', $instance->unit_type_id)->where('related_unit_type_id', $instance->converted_unit_type_id)->update([
        'unit_type_id' => $conversion_unit_symbol,
        'related_unit_type_id' => $converted_unit_symbol,
        'unit_conversion_id'=>$id
      ]);
      if(!$relationInstanceFirst){
        UnitRelation::create([
          'unit_type_id' => $conversion_unit_symbol,
          'related_unit_type_id' => $converted_unit_symbol,
          'unit_conversion_id'=>$id
        ]);
      }
      $relationInstanceSecond = UnitRelation::where('unit_conversion_id', $id)->where('unit_type_id', $instance->converted_unit_type_id)->where('related_unit_type_id', $instance->unit_type_id)->update([
        'unit_type_id' => $converted_unit_symbol,
        'related_unit_type_id' => $conversion_unit_symbol,
        'unit_conversion_id'=>$id
      ]); 
      if(!$relationInstanceSecond){
        UnitRelation::create([
          'unit_type_id' => $converted_unit_symbol,
          'related_unit_type_id' => $conversion_unit_symbol,
          'unit_conversion_id'=>$id
        ]); 
      }
      
      $instance->quantity = $conversion_unit;
      $instance->unit_type_id = $conversion_unit_symbol;
      $instance->converted_quantity = $converted_unit;
      $instance->converted_unit_type_id = $converted_unit_symbol;
      $instance->update();
      $dataPayload = array("data_type" => "unit_conversion", "unit_conversion" => $instance, "action" => "update");
      $msgID = sendPushNotification_(getFBIDs($company_id), 40, null, $dataPayload);
      $request->session()->flash('success', 'Successfully Updated.');
    }

    return back();
  }

  public function deleteunitconversion(Request $request){
    $company_id = config('settings.company_id');
    $id = $request->get('unit_id');
    $instance = UnitConversion::findOrFail($id);
    $relationInstanceFirst = UnitRelation::where('unit_type_id', $instance->unit_type_id)->where('related_unit_type_id', $instance->converted_unit_type_id)->delete();
    $relationInstanceSecond = UnitRelation::where('unit_type_id', $instance->converted_unit_type_id)->where('related_unit_type_id', $instance->unit_type_id)->delete(); 
    $deleted = $instance->delete();
    if($deleted){
      $dataPayload = array("data_type" => "unit_conversion", "unit_conversion" => $instance, "action" => "delete");
      $msgID = sendPushNotification_(getFBIDs($company_id), 40, null, $dataPayload);
      $request->session()->flash('success', 'Deleted Successfully.');
    } 
    else{
      $request->session()->flash('error', 'Failed deleting');
    }
    return back();
  }
	/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create()
	{
		if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
			return view('company.units.create');
		}
		return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$customMessages = [
			'name.required' => 'The Unit Name is required',
			'symbol.required' => 'The Unit Symbol is required'
		];
		$company_id = config('settings.company_id');
		$this->validate($request, [
			'name' => 'required|unique:unit_types,name,NULL,id,company_id,' . $company_id,
			'symbol' => 'required'
		], $customMessages);
		$unit = new \App\UnitTypes;
		$unit->company_id = $company_id;
		$unit->name = $request->get('name');
		$unit->symbol = $request->get('symbol');
		$unit->status = $request->get('status');
		$unit->save();
		return redirect()->route('company.admin.unit', ['domain' => domain()])->with('success', 'Information has been  Added');
	}
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request)
	{
		if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin()){
			$company_id = config('settings.company_id');
			$unit = UnitTypes::where('id', $request->id)->where('company_id', $company_id)->first();
			if ($unit)
				return view('company.units.edit', compact('unit'));
			else
				return redirect()->route('company.admin.unit', ['domain' => domain()]);
		}
		return redirect()->back()->withErrors(['msg', 'You are not Authorized to view this link.']);
	}
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request)
	{
		$company_id = config('settings.company_id');
		$unit = UnitTypes::findOrFail($request->id);
		$customMessages = [
			'name.required' => 'The Unit Name is required',
			'symbol.required' => 'The Unit Symbol is required'
		];
		$this->validate($request, [
			'name' => 'required|unique:unit_types,name,' . $request->id . ',id,company_id,' . $company_id,
			'symbol' => 'required'
		], $customMessages);
		$unit->name = $request->get('name');
		$unit->symbol = $request->get('symbol');
		$unit->status = $request->get('status');
		$saved = $unit->save();
		return redirect()->route('company.admin.unit', ['domain' => domain()])->with('success', 'Information has been  Updated');
	}
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request)
	{
		$company_id = config('settings.company_id');
		$unit = UnitTypes::where('company_id', $company_id)->findOrFail($request->id);
		$unit->delete();
		flash()->success('Unit has been deleted.');
		return back();
	}
	public function changeStatus(Request $request)
	{
		$company_id = config('settings.company_id');
		$unit = UnitTypes::findOrFail($request->unit_id);
		$unit->status = $request->status;
		$saved = $unit->save();
		return back();
  }
  
  public function custompdfdexport(Request $request){
    $getExportData = json_decode($request->exportedData);
    $pageTitle = $request->pageTitle;
    set_time_limit ( 300 );
    $columns = json_decode($request->columns);
    $properties = json_decode($request->properties);
    $pdf = PDF::loadView('company.units.exportpdf', compact('getExportData', 'pageTitle', 'columns',
    'properties'))->setPaper('a4', 'portrait');
    $download = $pdf->download($pageTitle.'.pdf');
    return $download;
  }
}