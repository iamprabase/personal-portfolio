<?php

namespace App\Http\Controllers\Company\Admin;

use Auth;
use Exception;
use App\Product;
use App\RateSetup;
use App\RateDetail;
use App\ProductVariant;
use App\ModuleAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PartyRateController extends Controller
{   
    private $company_id, $currency;

    public function __construct()
    {
        $this->middleware('auth');
        $this->company_id = config('settings.company_id');
        $this->currency = config('settings.currency_symbol');
        $this->middleware('permission:party_wise_rate_setup-create', ['only' => ['rateSetupPage']]);
        $this->middleware('permission:party_wise_rate_setup-view');
        $this->middleware('permission:party_wise_rate_setup-update', ['only' => ['update', 'rateSetupPage']]);
        $this->middleware('permission:party_wise_rate_setup-delete', ['only' => ['destroy']]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
      $createCheck = AUth::user()->can('party_wise_rate_setup-create');
      return view('company.rate_setup.parties_rate_setup', compact('createCheck'));
    }

    /**
     * Add New Rate Page
     */
    public function getRatesData(Request $request)
    { 
      $company_id = $this->company_id;
      $columns = array( 'id', 'rate_name', 'action');       

      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');
      $search = strtoupper( $request->input('search.value'));

      $prepQuery = RateSetup::whereCompanyId($company_id)
                  ->orderBy('rates.name', 'desc');
      $totalData = (clone $prepQuery)->count();
      if(isset($search)){
        $prepQuery = $prepQuery->where(function($query) use ($search){
          $query->orWhere(\DB::raw("UPPER(rates.name)"), 'LIKE', "%{$search}%");
        });
      }

      $totalFiltered = (clone $prepQuery)->count();
      if($limit==-1)$limit = $totalFiltered;  

      $rates =  $prepQuery->offset($start)
                        ->limit($limit)->get();

      $data = array();
      if(!empty($rates))
      {   
        $i = $start;
        foreach ($rates as $rate){
          
          $nestedData['id'] = ++$i;
          $nestedData['rate_name'] = $rate->name;
          $show = domain_route('company.admin.rate_setup_page.show', [$rate->id]);
          // $edit = domain_route('company.admin.rate_setup_page.show', [$rate->id]);
          $delete = domain_route('company.admin.add_new_rate.delete', [$rate->id]);

          $action = "<a href='{$show}' class='btn btn-success btn-sm rate_show_details' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";

          // if(Auth::user()->can('party_wise_rate_setup-update'))  $action = $action."<a href='{$edit}' class='btn btn-warning btn-sm rate_show_details' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";

          if(Auth::user()->can('party_wise_rate_setup-delete'))  $action = $action."<a data-url='{$delete}' data-mid='{$rate->id}' data-toggle='modal' data-target='#delete' data-rate_id='{$rate->id}' data-title='{$rate->name}' class='btn btn-danger btn-sm' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>";

          $nestedData['action'] = $action;

          $data[] = $nestedData;
        }
      }

      $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data,
      );

      return json_encode($json_data); 
    }

    public function store(Request $request)
    {
      $company_id = $this->company_id;
      $this->validate($request, [
        'rate_name' => 'required|unique:rates,name,NULL,id,company_id,'.$company_id
      ]);

      try{
        $saved = RateSetup::create([
          'company_id' => $company_id,
          'name' => $request->rate_name,
        ]);

        $rate_data = $this->getPartyRates($company_id, $saved->id);
        
        $dataPayload = array("data_type" => "partyrate", "action" => "add");
        $msgID = sendPushNotification_(getFBIDs($company_id), 37, null, $dataPayload);

        if($saved) return response()->json([
          'status' => true,
          'msg' => 'Rate added successfully.',
          'next_page_url' => domain_route('company.admin.rate_setup_page', [$saved->id]),
        ]);
      }catch(Exception $e){
        return response()->json([
          'status' => false,
          'msg' => $e->getMessage()
        ]);
      }
    }

    private function getPartyRates($company_id, $id) {

      $party_rates = RateSetup::where('rates.id', $id)->where('rates.company_id', $company_id)->leftJoin('rate_details', 'rate_details.rate_id', 'rates.id')
                ->get(['rates.name as rate_name', 'rates.id as rate_id', 'rate_details.product_id', 'rate_details.variant_id', 'rate_details.mrp']);
      $rates = array();
      if($party_rates->first()){
        if($party_rates->first()->product_id){
          foreach($party_rates as $rate){
            $object = array('product_id'=> $rate->product_id, 'variant_id'=> $rate->variant_id, 'mrp'=> round($rate->mrp, 2));
            $rates[$rate->rate_id]['id'] = $rate->rate_id;
            $rates[$rate->rate_id]['name'] = $rate->rate_name;
            $rates[$rate->rate_id]['object_details'][] = $object;
          }
        }else{
          $rates[$party_rates->first()->rate_id]['id'] = $party_rates->first()->rate_id;
          $rates[$party_rates->first()->rate_id]['name'] = $party_rates->first()->rate_name;
          $rates[$party_rates->first()->rate_id]['object_details'][] = "";
        }
      }

      $tempArray = array();
      foreach($rates as $key=>$value){
        $value['details'] = json_encode($value['object_details'],true);
        unset($value['object_details']);
        array_push($tempArray, $value);
      }
      
      return $tempArray;
    }

    public function update(Request $request)
    {
      $company_id = $this->company_id;
      $id = $request->rate_id;
      
      $this->validate($request, [
        'rate_name' => 'required|unique:rates,name,'.$id.',id,company_id,'.$company_id
      ]);
      try{
        $instance = RateSetup::find($id);
  
        $instance->name = $request->rate_name;
  
        $updated = $instance->update();

        $rate_data = $this->getPartyRates($company_id, $id);
        
        $dataPayload = array("data_type" => "partyrate", "action" => "update");
        $msgID = sendPushNotification_(getFBIDs($company_id), 37, null, $dataPayload);
        
        if($updated) return response()->json([
                                          'status' => true,
                                          'msg' => 'Rate Name updated successfully.',
                                          'rate_name' => $request->rate_name
                                        ]);

      }catch(Exception $e){
        return response()->json([
          'status' => false,
          'msg' => $e->getMessage()
        ]);
      }
    }



    public function delete(Request $request)
    {
      $id = $request->id;
      $company_id =  $this->company_id;
      $instance = RateSetup::find($id);

      if($instance->clients->count()>0){
        \Session::flash('error', 'Cannot delete rates that are assigned to parties.');

        return back();
      }

      $instance->delete();

      RateDetail::where('rate_id', $id)->delete();

      $rate_data = $this->getPartyRates($company_id, $id);

      $dataPayload = array("data_type" => "partyrate", "action" => "delete");
      $msgID = sendPushNotification_(getFBIDs($company_id), 37, null, $dataPayload);

      \Session::flash('success', 'Rate has been deleted successfully.');
      return back();
    }

    /**
     * Rate Setup
     * Details of individual rates
     */

    public function fetchProductsData(Request $request)
    { 
      $company_id = $this->company_id;
      $columns = array( 'id', 'product_name', 'variant_name', 'unit', 'original_mrp', 'custom_mrp');       

      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');
      if($order=='unit'||$order=='mrp' ){ $order = 'product_variants.'.$order; }
      $search = $request->input('search.value');

      $prepQuery = Product::where('products.company_id', $company_id)
                  ->where('products.status', 'Active')
                  ->leftJoin('product_variants', function ($join) {
                      $join->on('products.id',  '=', 'product_variants.product_id')
                          ->where('products.variant_flag', '=', 1);
                  })
                  // ->leftJoin('product_variants', 'products.id', 'product_variants.product_id')
                  ->where('product_variants.deleted_at', NULL)
                  ->leftJoin('unit_types', function($query){
                              $query->on('unit_types.id', '=', DB::raw('case when products.variant_flag = 0 then products.unit else product_variants.unit end'));
                            })
                  ->orderBy('products.star_product', 'desc');
      $totalData = (clone $prepQuery)->count();
      if(isset($search)){
        $prepQuery = $prepQuery->where(function($query) use ($search){
          $query->orWhere('products.product_name', 'LIKE', "%{$search}%");
          $query->orWhere('unit_types.name', 'LIKE', "%{$search}%");
          $query->orWhere('products.mrp', 'LIKE', "%{$search}%");
          $query->orWhere('product_variants.variant', 'LIKE', "%{$search}%");
        });
      }

      $totalFiltered = (clone $prepQuery)->count();
      if($limit==-1)$limit = $totalFiltered;  

      if($order=="product_name"){
        $prepQuery = $prepQuery->orderBy($order,$dir)->orderBy('product_variants.variant',$dir);
      }else{
        $prepQuery = $prepQuery->orderBy($order,$dir);
      }

      $products =  $prepQuery->offset($start)
                        ->limit($limit)->select('products.id', 'product_variants.id as variant_id', 'products.variant_flag', 'products.product_name', 'products.unit as product_unit', 'products.mrp as product_mrp', 'product_variants.variant as variant_name', 'product_variants.unit as variant_unit', 'product_variants.mrp as variant_mrp', 'unit_types.name as unit_name')->get();

      $data = array();
      if(!empty($products))
      {   
        $i = $start;
        foreach ($products as $product){
          
          $nestedData['id'] = ++$i;
          $nestedData['product_name'] = $product->product_name;
          $nestedData['variant_name'] = $product->variant_name;

          if($product->variant_flag==0){
            $mrp = $product->product_mrp;
            $unit = $product->product_unit;

            $rate_relation = $product->product_rates->where('rate_id', $request->rate_id);

            if($rate_relation->first()){
              $custom_mrp = $rate_relation->first()->mrp;
            }else{
              $custom_mrp = $mrp;
            }
          }elseif($product->variant_flag==1){
            $mrp = $product->variant_mrp;
            $unit = $product->variant_unit;

            $rate_relation = $product->product_variant_rates->where('variant_id', $product->variant_id)->where('rate_id', $request->rate_id);
            
            if($rate_relation->first()){
              $custom_mrp = $rate_relation->first()->mrp;
            }else{
              $custom_mrp = $mrp;
            }
          }

          $nestedData['original_mrp'] = $this->currency." ".$mrp;
          $nestedData['custom_mrp'] = "<input type='text' name='mrp' class='form-control mrpInput hidden' value='{$custom_mrp}' data-toggle='tooltip' title='Press Enter to Update'  data-value='{$custom_mrp}' data-original_mrp='{$mrp}' data-product_id='{$product->id}' data-variant_id='{$product->variant_id}' />"."<span class='mrpText'>".$this->currency." ".$custom_mrp."</span>";
          $nestedData['unit'] = $product->unit_name;

          $data[] = $nestedData;
        }
      }

      $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data,
      );

      return json_encode($json_data); 
    }

    public function rateSetupPage(Request $request){
      $instance = RateSetup::find($request->rate_id);

      $rate_name = $instance->name;
      $rate_id = $instance->id;
      $form_url = domain_route('company.admin.rate_setup_page.update_name', [$instance->id]);
      $method = 'patch';

      return view('company.parties_rate_setup.index', compact('rate_name', 'rate_id', 'form_url', 'method'));
    }

    public function show(Request $request){
      $instance = RateSetup::find($request->rate_id);

      $rate_name = $instance->name;
      $rate_id = $instance->id;
      $form_url = domain_route('company.admin.rate_setup_page.update_name', [$instance->id]);
      $method = 'patch';

      $createCheck = AUth::user()->can('party_wise_rate_setup-create');

      return view('company.parties_rate_setup.show', compact('rate_name', 'rate_id', 'form_url', 'method', 'createCheck'));
    }

    public function updateMRP(Request $request)
    {
      $company_id = $this->company_id;
      $id = $request->rate_id;
      $product_id = $request->product_id;
      $variant_id = $request->variant_id;
      $mrp = $request->mrp;
      
      try{
        $createOrUpdate = RateDetail::updateOrCreate(['rate_id'=>$id, 'product_id'=>$product_id, 'variant_id'=>$variant_id ],
        ['rate_id'=>$id, 'product_id'=>$product_id, 'variant_id'=>$variant_id, 'mrp'=>$mrp ]);


        $rate_data = $this->getPartyRates($company_id, $id);
      
        $dataPayload = array("data_type" => "partyrate", "action" => "update");
        $msgID = sendPushNotification_(getFBIDs($company_id), 37, null, $dataPayload);
        
        if($createOrUpdate) return response()->json([
                                          'status' => true,
                                          'msg' => 'Rate updated successfully.'
                                        ]);

      }catch(Exception $e){
        return response()->json([
          'status' => false,
          'msg' => $e->getMessage()
        ]);
      }
    }

    public function quickCustomRateSetup(Request $request){
      try{
        $id = $request->rate_id;
        $discount_percent = floatval($request->discount_percent);
        $company_id = $this->company_id;
        if($discount_percent>=0){
          $results = Product::where('products.company_id', $company_id)
                  ->where('products.status', 'Active')
                  // ->leftJoin('product_variants', 'products.id', 'product_variants.product_id')
                  ->leftJoin('product_variants', function ($join) {
                      $join->on('products.id',  '=', 'product_variants.product_id')
                          ->where('products.variant_flag', '=', 1);
                  })
                  ->where('product_variants.deleted_at', NULL)
                  ->leftJoin('unit_types', function($query){
                              $query->on('unit_types.id', '=', DB::raw('case when products.variant_flag = 0 then products.unit else product_variants.unit end'));
                            })
                  ->orderBy('products.star_product', 'desc')->select('products.id', 'product_variants.id as variant_id', 'products.variant_flag', 'products.mrp as product_mrp', 'product_variants.mrp as variant_mrp')->get();
          foreach($results as $result){
            if($result->variant_flag==0){
              $custom_mrp = round($discount_percent*floatval($result->product_mrp)/100, 2);
            }else{
              $custom_mrp = round($discount_percent*floatval($result->variant_mrp)/100, 2);
            }
            
            $createOrUpdate = RateDetail::updateOrCreate(['rate_id'=>$id, 'product_id'=>$result->id, 'variant_id'=>$result->variant_id ], ['rate_id'=>$id, 'product_id'=>$result->id, 'variant_id'=>$result->variant_id, 'mrp'=>$custom_mrp ]);
            unset($custom_mrp);
          }
          $rate_data = $this->getPartyRates($company_id, $id);
      
          $dataPayload = array("data_type" => "partyrate", "action" => "update");
          $msgID = sendPushNotification_(getFBIDs($company_id), 37, null, $dataPayload);

        }
        return response()->json([
          'status' => true,
          'msg' => "Rate Updated Successfully."
        ]);
      }catch(Exception $e){
        return response()->json([
          'status' => false,
          'msg' => $e->getMessage()
        ]);
      }
    }
}
