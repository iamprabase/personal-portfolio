<?php

namespace App\Http\Controllers\Company\Admin;

use Session;
use App\Product;
use App\Category;
use App\Employee;
use App\CategoryRateType;
use Illuminate\Http\Request;
use App\CategoryRateTypeRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CategoryRateController extends Controller
{

    public function __construct()
    {
      $this->middleware('auth');

    }

    private function sendNotification ($category_rate_id, $category_id, $action) {
      
      $fbID = Employee::where('company_id', config('settings.company_id'))->where('status', 'Active')->whereNotNull('firebase_token')->pluck('firebase_token');
      $dataPayload = array("data_type" => "category_rate", "category_rate" => array('category_rate_id' => json_encode($category_rate_id), 'category_id' => $category_id), "action" => $action);
      sendPushNotification_($fbID, config('firebaseNotification.NOTIFICATION_CASE_CATEGORY_PRICING'), null, $dataPayload);

    }

    private function updatePivotData($category_id, $category_rate_type_id, $product_id, $product_variant_id, $mrp){
      CategoryRateTypeRate::updateOrCreate([
        'category_id' => $category_id, 
        'category_rate_type_id' => $category_rate_type_id,
        'product_id' => $product_id,
        'product_variant_id' => $product_variant_id,
      ], [
        'category_id' => $category_id, 
        'category_rate_type_id' => $category_rate_type_id,
        'product_id' => $product_id,
        'product_variant_id' => $product_variant_id,
        'mrp' => $mrp,
      ]);
    }

    public function index()
    {
      $categories = Category::CompanyCategories(array('name', 'id'))
                    ->pluck('name', 'id')->toArray();
      
      return view ('company.categories_rate_setup.index', compact('categories'));
    }

    public function store(Request $request)
    {
      $customMessage = array(
        'name.unique' => 'Rate Type with given name already exists in this category.'
      );

      $this->validate($request, [
        'name' => 'required|unique:category_rate_types,name,NULL,id,deleted_at,NULL,category_id,' . $request->category_id,
        'category_id' => 'required',
        'discount_percent' => 'nullable|regex:/^\d+\.?+\d*/',
      ], $customMessage);
      $addRecord = CategoryRateType::create($request->only('name', 'category_id'));
      $discount_percent = $request->discount_percent;

      // if(!empty($discount_percent)){
        $getColumns = array('products.id', 'product_variants.id as variant_id', 'products.variant_flag', 'products.mrp as product_mrp', 'product_variants.mrp as variant_mrp');
        $products = Product::AllProductsAtVariantLevel($getColumns, 'Active', array(), $request->category_id);
        DB::beginTransaction();
        if($products->first()){
          foreach($products as $product){
            $mrp = !empty($discount_percent) ? round($discount_percent*floatval($product->product_mrp)/100, 2) : $product->product_mrp;
            if($product->variant_flag==1){
              $mrp = !empty($discount_percent) ? round($discount_percent*floatval($product->variant_mrp)/100, 2) : $product->variant_mrp;
            }
            $this->updatePivotData($request->category_id, $addRecord->id, $product->id, $product->variant_id, $mrp);
          }
        }
        DB::commit();
      // }

      $this->sendNotification($addRecord->id, $request->category_id, "add");

      return response()->json([
        'status' => true,
        'msg' => 'Rate Added Successfully.'
      ], 200);
    }

    public function show($domain, $id)
    {
      $category = Category::findOrFailById($id, array('id', 'name'));
      
      return view ('company.categories_rate_setup.show', compact('category'));
    }

    public function fetch(Request $request)
    { 
      $category_id = $request->id;
      $columns = $request->columns;
      
      $start = $request->input('start');
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')]['data'];
      $dir = $request->input('order.0.dir');
      if($order=='unit'||$order=='mrp' ){ 
        $order = 'product_variants.'.$order; 
      }
      $search = $request->input('search.value');
      $getColumns = array('products.id', 'product_variants.id as variant_id', 'products.variant_flag', 'products.product_name', 'products.unit as product_unit', 'products.mrp as product_mrp', 'product_variants.variant as variant_name', 'product_variants.unit as variant_unit', 'product_variants.mrp as variant_mrp', 'unit_types.name as unit_name');

      $product_query = Product::AllProductsAtVariantLevel($getColumns, 'Active', array('search' => $search, 'offset' => $start, 'limit' => $limit, 'order' => $order, 'dir' => $dir), $category_id);

      $products = $product_query['data'];
      $category_rates = json_decode($request->category_rates);
      if(!empty($category_rates)){
        $products->load(['categoryrates' => function($query) use($category_id){
          $query->where('category_id', $category_id)->select('mrp', 'id', 'product_id', 'product_variant_id', 'category_rate_type_id');
        }]);

        $products->load(['variantcategoryrates' => function($query) use($category_id){
          $query->where('category_id', $category_id)->select('mrp', 'id', 'product_id', 'product_variant_id', 'category_rate_type_id');
        }]);
      }

      $totalFiltered = $product_query['currentFilter'];
      $totalData = $product_query['totalData'];
      $data = array();
      if($products->first())
      {   
        $i = $start;
        foreach ($products as $product){
          
          $nestedData['id'] = ++$i;
          $nestedData['product_name'] = $product->product_name;
          $nestedData['variant_name'] = $product->variant_name;

          if($product->variant_flag==0){
            $mrp = $product->product_mrp;
            $unit = $product->product_unit;
          }elseif($product->variant_flag==1){
            $mrp = $product->variant_mrp;
            $unit = $product->variant_unit;
          }

          $nestedData['unit'] = $product->unit_name;
          $nestedData['original_mrp'] = config('settings.currency_symbol')." ".$mrp;
          $cat_rate = $mrp;

          if(!empty($category_rates)){
            foreach($category_rates as $category_rate){
              $category_rate_id = explode('___', $category_rate)[0];
              $relatedRates = $product->variant_flag == 1 ? $product->variantcategoryrates : $product->categoryrates;
              
              if($relatedRates->first()){
                $has_this_product_rate = $relatedRates
                                          ->filter(function($query) use($product, $category_rate_id){
                                            if($product->variant_flag==1){
                                              return $query->category_rate_type_id == $category_rate_id && $query->product_id == $product->id && $query->product_variant_id;
                                            } else {
                                              return $query->category_rate_type_id == $category_rate_id && $query->product_id == $product->id;
                                            }
                                          });
                if($has_this_product_rate->first()){
                  $cat_rate = $has_this_product_rate->first()->mrp;
                } 
              }
              $nestedData[$category_rate] = "<input type='text' name='mrp' class='form-control mrpInput hidden' value='{$cat_rate}' data-toggle='tooltip' title='Press Enter to Update'  data-value='{$cat_rate}' data-original_mrp='{$mrp}' data-product_id='{$product->id}' data-variant_id='{$product->variant_id}' data-category_rate_type_id='{$category_rate_id}' />"."<span class='mrpText'>".config('settings.currency_symbol')." ".$cat_rate."</span>";
              $cat_rate = $mrp;
            }
          }
          

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
    
    public function quickRateSetup(Request $request){
      $company_id = config('settings.company_id');
      $this->validate($request, [
        'discount_percent' => 'required|regex:/^\d+\.?+\d*/',
        'category_rates' => 'required',
      ]);
      
      try{
        $category_id = $request->id;
        $category_rate_types = json_decode($request->category_rates);
        $discount_percent = floatval($request->discount_percent);
        
        if($discount_percent>=0){
          $getColumns = array('products.id', 'product_variants.id as variant_id', 'products.variant_flag', 'products.mrp as product_mrp', 'product_variants.mrp as variant_mrp');
          $products = Product::AllProductsAtVariantLevel($getColumns, 'Active', array(), $category_id);
          if(!$products->first()) {
            return response()->json([
              'status' => true,
              'msg' => "No Products to apply rates."
            ]);
          }
          DB::beginTransaction();
          foreach($products as $product){
            $mrp = round($discount_percent*floatval($product->product_mrp)/100, 2);
            if($product->variant_flag==1){
              $mrp = round($discount_percent*floatval($product->variant_mrp)/100, 2);
            }
            foreach($category_rate_types as $category_rate_type_id){
              $this->updatePivotData($category_id, $category_rate_type_id, $product->id, $product->variant_id, $mrp);
            }
          }
          DB::commit();

          $this->sendNotification($category_rate_types, $category_id, "update");
         
        }
        return response()->json([
          'status' => true,
          'msg' => "Rate Updated Successfully."
        ]);
      }catch(Exception $e){
        DB::rollback();
        $this->logErrorException($e);
        return response()->json([
          'status' => false,
          'msg' => "Some Error Occured"
        ]);
      }
    }

    public function updateOrCreateProductRate(Request $request){
      $this->validate($request, [
        'product_id' => 'required',
        'mrp' => 'required|regex:/^\d+\.?+\d*/'
      ]);
      $category_id = $request->id; 
      $category_rate_type_id = $request->category_rate_type_id;
      $product_id = $request->product_id;
      $product_variant_id = $request->variant_id;
      $mrp = $request->mrp;
      
      try{
        $this->updatePivotData($category_id, $category_rate_type_id, $product_id, $product_variant_id, $mrp);
        
        $this->sendNotification($category_rate_type_id, $category_id, "update");
         
        return response()->json([
          'msg' => "Updated Successfully",
        ], 200);
      }catch(\Exception $e){
        $this->logErrorException($e);
        
        return response()->json([
          'msg' => "Some error occured while processing your request.",
        ], 400);
      }

    }

    public function ratesShow($domain, $id)
    {
      // $category = Category::with('categoryrates')->CompanyCategories(array('id', 'name'))->where('id', $id)->first();
      $category = Category::findOrFailById($id, array('id', 'name'));
      
      return view ('company.categories_rate_setup.rate_show', compact('category'));
    }

    public function update(Request $request){
      $instance = CategoryRateType::find($request->id);
      $customMessage = array(
        'name.unique' => 'Rate Type with given name already exists in this category.'
      );

      $this->validate($request, [
        'name' => 'required|unique:category_rate_types,name,'.$instance->id.',id,deleted_at,NULL,category_id,' . $instance->category_id,
      ], $customMessage);
      $instance->update([
        'name' => $request->name
      ]);

      $fbID = Employee::where('company_id', config('settings.company_id'))->where('status', 'Active')->whereNotNull('firebase_token')->pluck('firebase_token');
      $dataPayload = array("data_type" => "category_rate", "category_rate" => array('category_rate_id' => $instance->id, 'category_id' => $instance->category_id, 'name' => $instance->name), "action" => "update_name");
      sendPushNotification_($fbID, config('firebaseNotification.NOTIFICATION_CASE_CATEGORY_PRICING'), null, $dataPayload);

      return response()->json([
        'status' => true,
        'msg' => 'Updated Successfully.',
        
      ], 200);
      
    }

    public function destroy(Request $request)
    {
      DB::beginTransaction();
      $instance = CategoryRateType::find($request->id);
      if($instance->appliedcategoryrates->count() > 0){
        Session::flash('warning', 'Cannot delete this rate type as it has been applied to party.');
        return redirect()->back();
      }
      // $instance->forceDelete();
      CategoryRateTypeRate::where('category_rate_type_id', $instance->id)->forceDelete();
      $instance->delete();
      DB::commit();

      $this->sendNotification($instance->id, $instance->category_id, "delete");
      
      Session::flash('success', 'Deleted Successfully.');
      return redirect()->back();
    }
}
