<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Color;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ProductController extends Controller
{
    public function __construct()
    {
    	$this->middleware('auth:api');
    }

    public function index(Request $request)
    {
    	$user = Auth::user();
        $companyID = $user->company_id;
        $offset = $this->getArrayValue($request->all(), "offset",0);
        $limit = $this->getArrayValue($request->all(), "limit",200);
        //todo need to be managed properly with other parameters like limit
        $finalArray = array();

        $products = DB::table('products')
            ->select('products.*', 'categories.name as category_name','brands.name as brand_name', 'unit_types.name as unit_name', 'unit_types.symbol as unit_symbol')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('unit_types', 'unit_types.id', '=', 'products.unit')
            ->where("products.company_id", $companyID)
            ->where("products.status", "Active")
            ->get()->toArray();

        $pv = DB::table('product_variants')->whereNull("product_variants.deleted_at")
            ->select('product_variants.*', 'unit_types.name as unit_name', 'unit_types.symbol as unit_symbol')
            ->leftJoin('unit_types', 'unit_types.id', '=', 'product_variants.unit')
            ->where("product_variants.company_id", $companyID)
            ->get()->toArray();

        foreach($pv as $key=>$p){
          $p->variant_colors = $this->getColors($p->id);
        }

        // $pv2 = ProductVariant::leftJoin('unit_types', 'unit_types.id', '=', 'product_variants.unit')
        //     ->select('product_variants.*', 'unit_types.name as unit_name', 'unit_types.symbol as unit_symbol')
        //     ->where("product_variants.company_id", $companyID)
        //     ->get();
        // foreach($pv2 as $v){
        //   if($v->colors->count()>0)
        //     $v->variant_colors = json_encode($v->colors->pluck('value')->toArray());
        //   else
        //     $v->variant_colors = null;
        // }

        // $pvA = (object)$pv2->toArray();
        // $pvA = json_decode(json_encode($pv2), FALSE);
        // $pvAGroupedByProductID = arrayGroupBy($pvA,"product_id",true); 
        // Log::info(print_r($pvA, true));
        // Log::info(print_r($pvAGroupedByProductID, true));
        $pvGroupedByProductID = arrayGroupBy($pv,"product_id",true); 
        // Log::info(print_r($pv, true));
        // Log::info(print_r($pvGroupedByProductID, true));

        foreach ($products as $key => $value) {
            $tempObj = $value;
            $tempPVProductID = getObjectValue($value,"id");
            $tempObj->product_variants = getArrayValue($pvGroupedByProductID,$tempPVProductID);
            $productInstance = Product::findOrFail($tempPVProductID);
            $instance = $productInstance->taxes;
            if($instance->count()>0){
              $tempObj->product_tax = json_encode($instance->toArray(), true);
            }else{
              $tempObj->product_tax = null;
            }
            $conversions = $productInstance->conversions;
            if($conversions->count()>0){
              $converted = $conversions->toArray();
              $conversion_relations = array();
              foreach($converted as $conversion){
                $conversion_units = $this->getUnitName($conversion['unit_type_id'], $companyID);
                $conversion['unit_name'] = !empty($conversion_units)?$conversion_units['unit_name']:null;
                $conversion['unit_symbol'] = !empty($conversion_units)?$conversion_units['unit_symbol']:null;
                $converted_units = $this->getUnitName($conversion['converted_unit_type_id'], $companyID);

                $conversion['converted_unit_name'] = !empty($converted_units)?$converted_units['unit_name']:null;
                $conversion['converted_unit_symbol'] = !empty($converted_units)?$converted_units['unit_symbol']:null;
                array_push($conversion_relations, $conversion);
              }
              $tempObj->conversion = json_encode($conversion_relations, true);
            }else{
              $tempObj->conversion = null;
            }
            array_push($finalArray,$tempObj);
        }   

    	$response = array('status'=>true,'message'=>'products','data'=>$finalArray);
    	return response($response);
    }

    private function getUnitName($id, $company_id)
    {
        $unit = DB::table('unit_types')->where('company_id', $company_id)->where('id', $id)->first();
        if ($unit){
          $units= array();
          $units["unit_name"] = $unit->name;
          $units["unit_symbol"] = $unit->symbol;

          return $units;
        }
        else{
          return NULL;
        }
    }

    private function getColors($id){
      $colorIds = DB::table('color_product_variant')->where('product_variant_id', $id)->pluck('color_id')->toArray();
      if(empty($colorIds)){
        return null;
      }else{
        $colors = Color::whereIn('id', $colorIds)->pluck('value')->toArray();
        return json_encode($colors);
      }
    }

    private function getArrayValue($arraySource, $key, $emptyText = null, $trim = FALSE)
    {
        if (is_array($arraySource) && !empty($arraySource[$key])) {
            return $trim == TRUE ? trim($arraySource[$key]) : $arraySource[$key];
        } else {
            return $emptyText;
        }
    }

    private function sendResponse($response)
    {
        echo json_encode($response);
        exit;
    }

}
