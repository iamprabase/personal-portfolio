<?php

namespace App\Http\Controllers\Company\Admin;

use Auth;
use Session;
use App\Client;
use App\Outlet;
use App\Product;
use App\ProductVariant;
use App\ClientOutletSetting;
use Illuminate\Http\Request;
// use App\RetailerProductSetup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class RetailerProductController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->company_id = config('settings.company_id');
        $this->currency = config('settings.currency_symbol');
        // $this->middleware('permission:outlet-view');
        // $this->middleware('permission:outlet-create', ['only' => ['store']]);
        // $this->middleware('permission:outlet-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {     
      $currency = $this->currency;
      $outlet_setting = config('client_outlet_settings');
      $min_order_value = "";
      // $order_with_qty_amt = "";

      if(!empty($outlet_setting)){
        $min_order_value = $outlet_setting['min_order_value'];
        // $order_with_qty_amt = $outlet_setting['order_with_qty_amt'];
      }

      $show_in_app_min = Product::whereStatus('Active')->whereCompanyId($this->company_id)->whereAppVisibility(0)->count();
      
      if($show_in_app_min==0){
        $show_all = 1;
      }else{
        $show_all = 0;
      }
      
      return view('company.outlets_ptr_setup.index')->with([
        'company_id'=> $this->company_id,
        'currency' => $currency,
        'min_order_value' => $min_order_value,
        'show_all' => $show_all
        // 'order_with_qty_amt' => $order_with_qty_amt
      ]);
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
        'client_id.required' => 'The Party field is required.',
        'secret_code.required' => 'The secret code is required.',
        'secret_code.min' => 'The secret code must be exactly 16 characters.',
        'secret_code.max' => 'The secret code must be exactly 16 characters.',
      ];
      $this->validate($request, [
        'client_id' => 'required',
        'secret_code' => 'required|min:16|max:16'
      ], $customMessages);

      $client_id = $request->client_id;
      $secret_code = $request->secret_code;
      try{
        $outlet_instance = Outlet::whereUniqueCode($secret_code)->first();
        if($outlet_instance){
          $connected_client = Client::whereOutletId($outlet_instance->id)->pluck('company_id')->toArray();
          if(in_array($this->company_id, $connected_client)){
            return response()->json([
              "message" => "Cannot connect an outlet with multiple clients. Please disconnect the current client first.",
              "status" => 400,
              "append_to" => "secret_code"
            ]);
          }else{
            $client = Client::findOrFail($client_id);
            $client->update([
              'outlet_id' => $outlet_instance->id
            ]);
            DB::table('company_outlet')->insert([
              'company_id' => $this->company_id,
              'outlet_id' => $outlet_instance->id,
            ]);

            return response()->json([
              "message" => "Connection has been established.",
              "status" => 200
            ]);
          }
        }else{

          return response()->json([
            "message" => "Cannot find retailer with given secret key.",
            "status" => 400,
            "append_to" => "secret_code"
          ]);
        }

      } catch(Exception $e){
        Session::flash('error', $e->getMessage());
        return redirect()->back();
      }     
    }

    public function update(Request $request)
    {
      $this->validate($request, [
        'moq' => 'required|integer|min:1',
        'discount' => ($request->discount && $request->discount!='NaN')?'regex:/^\d+(\.\d{1,})?$/':'nullable',
      ]);
      
      $company_id = $this->company_id;
      $product_id = $request->product_id;
      $variant_id = $request->variant_id;
      $moq = $request->moq;
      $discount = $request->discount;
      $visibility = $request->visibility;
      try{
        if(isset($variant_id)){
          $find_instance = ProductVariant::findOrFail($variant_id);
        }else{
          $find_instance = Product::findOrFail($product_id);
        }
        if($find_instance){
          $find_instance->moq = $moq;
          $find_instance->discount = $discount;
          $find_instance->update();
          
        }
        // else{
        //   RetailerProductSetup::create([
        //     'company_id' => $company_id,
        //     'product_id' => $product_id,
        //     'variant_id' => $variant_id,
        //     'app_visibility' => $visibility,
        //     'moq' => $moq,
        //     'discount' => $discount,
        //   ]);
        // }

        return response()->json([
          "message" => "Updated Successfully.",
          "status" => 200
        ]);

      } catch(Exception $e){
        Session::flash('error', $e->getMessage());
        return redirect()->back();
      }  
    }

    public function changeAppVisibility(Request $request){
      try{
        $company_id = $this->company_id;
        $flag_value = $request->visibility;
        if($request->has('show_hide_all')){
          $products = Product::whereCompanyId($company_id)->whereStatus('Active');
          $ids = $products->pluck('id')->toArray();
          $products->update([
            'app_visibility' => $flag_value
          ]);
          $variants = ProductVariant::whereIn('product_id', $ids)->update([
            'app_visibility' => $flag_value
          ]);
          
          $msg = "Products visibility in app has been updated successfully.";
        }else{
          $product_id = $request->product_id;
          $variant_id = isset($request->variant_id)?$request->variant_id:NULL;
          $find_instance = Product::findOrFail($product_id);
          $find_variant_instance = NULL;
          if(isset($variant_id)){
            $find_variant_instance = ProductVariant::findOrFail($variant_id);
          }else{
          }
  
          if($find_instance){
            $find_instance->app_visibility = $flag_value;
  
            if($find_variant_instance){
              $find_variant_instance->app_visibility = $flag_value;
              $find_variant_instance->update();
            }
  
            $find_instance->update();
          }
          // else{
          //   RetailerProductSetup::create([
          //     'company_id' => $company_id,
          //     'product_id' => $product_id,
          //     'variant_id' => $variant_id,
          //     'app_visibility' => $flag_value
          //   ]);
          // }
          $msg = "Product visibility in app has been updated successfully.";
        }

        $show_in_app_min = Product::whereStatus('Active')->whereCompanyId($this->company_id)->whereAppVisibility(0)->count();
      
        if($show_in_app_min==0){
          $show_all = 0;
        }else{
          $show_all = 1;
        }
        
        return response()->json([
          "message" => $msg,
          "status" => 200,
          "show_all" => $show_all
        ]);
      }catch(Exception$e){
        return response()->json([
          "message" => $e->getMessage(),
          "status" => 400
        ]);
      }
    }

    public function updateSettings(Request $request, $domain, $company_id){
      $this->validate($request, [
        'min_order_value' => 'required|regex:/^\d+(\.\d{1,})?$/',
        // 'order_with_amt_qty' => 'required|integer|min:0|max:1',
      ]);

      try{
        $min_order_value = $request->min_order_value;
        // $order_with_qty_amt = $request->order_with_amt_qty;
        
        $instance = ClientOutletSetting::whereCompanyId($company_id)->first();

        if($instance){
          $instance->min_order_value = $min_order_value;
          // $instance->order_with_qty_amt = $order_with_qty_amt;

          $instance->update();          
        }else{
          $instance = ClientOutletSetting::create([
            'company_id' => $company_id,
            'min_order_value' => $min_order_value,
            // 'order_with_qty_amt' => $order_with_qty_amt
          ]);
        }

        return response()->json([
          "message" => "Updated successfully.",
          "status" => 200,
          "min_order_value" => $instance->min_order_value,
          // "order_with_qty_amt" => $instance->order_with_qty_amt,
        ]);
      }catch(Exception $e){
        return response()->json([
          "message" => $e->getMessage(),
          "status" => 200
        ]);
      }
    }

    public function destroy($domain,$id)
    {
      $client = Client::findOrFail($id);

      try{
        $client->outlet_id = NULL;

        $client->update();
        DB::table('company_outlet')->where('company_id', $this->company_id)->delete();
        Session::flash('success', 'Connection removed successfully.');

        return back();
      }catch(Exception $e){
        Session::flash('error', $e->getMessage());

        return back();
      }
    }


    public function fetchData(Request $request)
    { 
      $company_id = $this->company_id;
      $columns = array( 'id', 'product_name', 'variant_name', 'unit', 'mrp', 'moq', 'discount', 'show_in_app');       

      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');
      if($order=='unit'||$order=='mrp'||$order=='discount'||$order=='moq' ){ $order = 'product_variants.'.$order; }
      $search = $request->input('search.value');

      $prepQuery = Product::where('products.company_id', $company_id)
                  ->where('products.status', 'Active')
                  ->leftJoin('product_variants', 'products.id', 'product_variants.product_id')
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
          $query->orWhere('products.moq', 'LIKE', "%{$search}%");
          $query->orWhere('products.discount', 'LIKE', "%{$search}%");
          $query->orWhere('product_variants.variant', 'LIKE', "%{$search}%");
          $query->orWhere('product_variants.mrp', 'LIKE', "%{$search}%");
          $query->orWhere('product_variants.moq', 'LIKE', "%{$search}%");
          $query->orWhere('product_variants.discount', 'LIKE', "%{$search}%");
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
                        ->limit($limit)->select('products.id', 'product_variants.id as variant_id', 'products.variant_flag', 'products.product_name', 'products.unit as product_unit', 'products.mrp as product_mrp', 'product_variants.variant as variant_name', 'product_variants.unit as variant_unit', 'product_variants.mrp as variant_mrp', 'product_variants.mrp', 'unit_types.name as unit_name', DB::raw('(CASE WHEN products.variant_flag = "0" THEN products.app_visibility WHEN products.variant_flag = "1" THEN product_variants.app_visibility END) as visibility'), DB::raw('(CASE WHEN products.variant_flag = "0" THEN products.discount WHEN products.variant_flag = "1" THEN product_variants.discount END) as outlet_discount'), DB::raw('(CASE WHEN products.variant_flag = "0" THEN products.moq WHEN products.variant_flag = "1" THEN product_variants.moq END) as max_order_qty'))->get();

      $data = array();
      if(!empty($products))
      {   
        $i = $start;
        foreach ($products as $product){
          $updateLocation = domain_route('company.admin.outlets.ptr.productUpdate');
          $hasProductConnection = $product->retailer_product;
          
          $moq = $product->max_order_qty;
          $discount = "$product->outlet_discount";
          $checked = $product->visibility==1?"checked":"";

          $nestedData['id'] = ++$i;
          $nestedData['product_name'] = $product->product_name;
          $nestedData['variant_name'] = $product->variant_name;

          if($product->variant_flag==0){
            $mrp = $product->product_mrp;
            $unit = $product->product_unit;

            // if($hasProductConnection){
            //   $checked = ($hasProductConnection->app_visibility==1)?"checked":"";
            //   // $ptr = $hasProductConnection->ptr;
            //   $moq = $hasProductConnection->moq;
            //   $discount = $hasProductConnection->discount;
            // }
          }elseif($product->variant_flag==1){
            $mrp = $product->variant_mrp;
            $unit = $product->variant_unit;
            
            // if($hasProductConnection){
            //   $hasVariantConnection = $hasProductConnection->where('variant_id', $product->variant_id)->first();
            //   if ($hasVariantConnection) {
            //     $checked = ($hasVariantConnection->app_visibility==1)?"checked":"";
            //     // $ptr = $hasProductConnection->ptr;
            //     $moq = $hasVariantConnection->moq;
            //     $discount = $hasVariantConnection->discount;
            //   }
            // }
          }

          $nestedData['mrp'] = $this->currency." ".$mrp;
          $nestedData['ptr'] = $this->currency." ".$mrp;
          $nestedData['moq'] = "<input type='number' name='moq' class='form-control moqInput hidden' value='{$moq}' data-toggle='tooltip'  title='Press Enter to Update'  data-value='{$moq}' data-product_id='{$product->id}' data-variant_id='{$product->variant_id}' data-visibility='{$product->visibility}' data-discount='{$discount}'/>"."<span class='moqtext'>".$moq."</span>";
          $nestedData['discount'] =  "<input type='text' name='discount' class='form-control discountInput hidden' value='{$discount}' data-toggle='tooltip' title='Press Enter to Update'  data-value='{$discount}' data-mrp='{$mrp}' data-product_id='{$product->id}' data-variant_id='{$product->variant_id}' data-visibility='{$product->visibility}' data-moq='{$moq}'/>"."<span class='discountText'>".$discount." "."%"."</span>";
          $nestedData['unit'] = $product->unit_name;

          // if($product->variant_flag==0){
            // $nestedData['mrp'] = $product->mrp;
            // $nestedData['ptr'] = $product->mrp;

          //   if($hasProductConnection){
          //     // $nestedData['ptr'] = $hasProductConnection->ptr;
          //     $nestedData['moq'] = $hasProductConnection->moq;
          //     $nestedData['discount'] = $hasProductConnection->discount;
          //   }
          // }elseif($product->variant_flag==1){
          //   $nestedData['mrp'] = $product->mrp;
          //   $nestedData['ptr'] = $product->mrp;
            
          //   if($hasProductConnection){
          //     $hasVariantConnection = $hasProductConnection->where('variant_id', $product->variant_id)->first();
          //     if ($hasVariantConnection) {
          //       // $nestedData['ptr'] = $hasProductConnection->ptr;
          //       $nestedData['moq'] = $hasProductConnection->moq;
          //       $nestedData['discount'] = $hasProductConnection->discount;
          //     }
          //   }
          // }

          $show_in_app = "<input type='checkbox' class='show_in_app_check_box' name='show_in_app' data-variant_id='{$product->variant_id}' value='{$product->id}' $checked>";

          $nestedData['show_in_app'] = $show_in_app;

          $visibility = !empty($checked)?1:0;

          $action = "<a href='javascript:void(0)' class='btn btn-warning btn-sm' id='product-edit' data-product_id='{$product->id}' data-variant_id='{$product->variant_id}' data-discount='{$discount}' data-moq='{$moq}' data-visibility='{$visibility}' data-href='{$updateLocation}' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>";

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

    // public function customPdfExport(Request $request){
    //   $getExportData = json_decode($request->exportedData)->data;
    //   $pageTitle = $request->pageTitle;
    //   set_time_limit ( 300 );
    //   ini_set("memory_limit", "256M");
    //   $pdf = PDF::loadView('company.orders.exportpdf', compact('getExportData', 'pageTitle'))->setPaper('a4', 'portrait');
    //   unset($getExportData);
    //   return $pdf->download($pageTitle.'.pdf');
    // }
}
