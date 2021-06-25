<?php

namespace App\Http\Controllers\Company\Admin;

use DB;
use Log;
use URL;
use Auth;
use Storage;
use App\User;
use App\Brand;
use App\Color;
use App\Order;
use App\Product;
use App\TaxType;
use App\Category;
use App\UnitTypes;
use App\ClientSetting;
use App\ProductVariant;
use App\UnitConversion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:product-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-view');
        $this->middleware('permission:product-update', ['only' => ['edit','update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
        $this->middleware('permission:product-status', ['only' => ['changeStatus']]);     
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()  
    {
        $company_id = config('settings.company_id');
        $products = Product::where('company_id', $company_id)
                    ->count();
        $brands = Brand::where('company_id', $company_id)->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $categories= Category::where('company_id', $company_id)->whereStatus('Active')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        
        return view('company.products.index', compact('products', 'brands', 'categories'));
    }

    public function ajaxDatatable(Request $request)
    {
      $columns = array( 
        0 => 'id', 
        1 => 'product_name',
        2 => 'product_code',
        3 => 'image',
        4 => 'brand_name',
        5 => 'category_name',
        6 => 'mrp',
        7 => 'status',
        8 => 'action',
      );
      $company_id = config('settings.company_id');
      $start = $request->input('start');
      $limit = $request->input('length');
      $brand = $request->input('brand');
      $category = $request->input('category');
      $order = $columns[$request->input('order.0.column')];
      if($order == "id"){
        $order = 'product_name'; 
      }
      $dir = $request->input('order.0.dir');

      $prepQuery = Product::where('products.company_id', $company_id)->leftJoin('brands', 'brands.id', 'products.brand')->leftJoin('categories', 'categories.id', 'products.category_id')->select('products.id', 'products.star_product', 'products.product_name','product_code', 'products.image','products.image_path', 'products.brand', 'products.category_id', 'products.mrp', 'products.status', 'brands.name as brand_name', 'categories.name as category_name');

      if(isset($brand))  $prepQuery = $prepQuery->where('brands.id', $brand);
      if(isset($category)) $prepQuery = $prepQuery->where('categories.id', $category);

      $totalData =  $prepQuery->count();
      $totalFiltered = $totalData; 
      
      $data = array();
      $selIds = $request->selIds; 
      $selectThisPageCheckBox = true;
      
      if(empty($request->input('search.value'))){
          
        $totalFiltered = $prepQuery->count();
        $products = $prepQuery
                ->offset($start)
                ->limit($limit)
                ->orderBy('products.star_product', 'desc')
                ->orderBy($order,$dir)
                ->get();
      }elseif(!(empty($request->input('search.value')))) {

        $search = $request->input('search.value'); 

        $productsSearchQuery = $prepQuery
                              ->where(function($query) use ($search){
                                $query->orWhere('products.mrp','LIKE',"%{$search}%");
                                $query->orWhere('products.status','LIKE',"%{$search}%");
                                $query->orWhere('products.product_name','LIKE',"%{$search}%");
                                $query->orWhere('products.product_code','LIKE',"%{$search}%");
                                $query->orWhere('brands.name','LIKE',"%{$search}%");
                                $query->orWhere('categories.name','LIKE',"%{$search}%");
                              });
        $totalFiltered = $productsSearchQuery->count();
        $products =  $productsSearchQuery
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.star_product', 'desc')
                        ->orderBy($order,$dir)
                        ->get();
      }

      if(!empty($products)){   
        $i = $start;
        foreach ($products as $product){
          $id = $product->id;
          $status = $product->status;
          $image_path = $product->image_path;
          $show = domain_route('company.admin.product.show',[$id]);
          $edit = domain_route('company.admin.product.edit',[$id]);
          $delete = domain_route('company.admin.product.destroy', [$id]);
          $starredClassName = ($product->star_product==1)?"starredProduct":"notstarredProduct";
          if(Auth::user()->can('product-update')){
            $starIcon = "<i class='fa fa-star star-icon {$starredClassName}' data-product_id='{$id}' data-currentstar='{$product->star_product}' title='Change Starred Status'></i>";
          }else{
            $starIcon = "<i class='fa fa-star unclick-star-icon {$starredClassName}' data-product_id='{$id}' data-currentstar='{$product->star_product}' title='Change Starred Status'></i>";
          }
          $checked = "";
          if(in_array($product->id, explode(',', $selIds))) $checked = "checked";
          else $selectThisPageCheckBox = false;
          if(Auth::user()->can('product-status') || Auth::user()->can('product-delete')) $productIdSpanTag = "<input type='checkbox' class='productStatusCheckBox' name='update_product_status' value='{$product->id}' {$checked}>";
          else $productIdSpanTag = null;
          $nestedData['id'] = $productIdSpanTag.$starIcon.++$i.".";
          $nestedData['product_name'] = $product->product_name;
          $nestedData['product_code'] = $product->product_code;
          
          if(isset($product->image_path)){
            $absPath = URL::asset('cms/'.$image_path);
          }else{
            $absPath = URL::asset('cms/storage/app/public/uploads/defaultprod.jpg');
          }
          $nestedData['image'] = "<img src='".$absPath."' class='direct-chat-gotimg' alt='Product Image'>";
          
          $nestedData['brand_name'] = $product->brand_name;
          $nestedData['category_name'] = $product->category_name;
          $nestedData['mrp'] = ($product->variant_flag==0)?$product->mrp:null;
          
          if($status =='Active'){
            $labelClassName = 'label label-success';
          }else{
            $labelClassName = 'label label-warning';
          }
          if(Auth::user()->can('product-status'))
          $nestedData['status'] = "<a href='#' class='edit-modal' data-id='{$id}' data-status='{$status}'><span class='{$labelClassName}'>{$status}</span></a>";
          else
          $nestedData['status'] = "<a href='#' class='alert-modal' data-id='{$id}' data-status='{$status}'><span class='{$labelClassName}'>{$status}</span></a>";

          
          if(!$product->orderproducts()->first() && Auth::user()->can('product-delete')){
            $deleteBtn = "<a class='btn btn-danger btn-sm delete' data-mid='{$id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>"; 
          }else{
            $deleteBtn = null;
          }
          $nestedData['action'] = "<a href='{$show}' class='btn btn-success btn-sm' style='    padding: 3px 6px;'><i class='fa fa-eye'></i></a>";
          if(Auth::user()->can('product-update'))
          $nestedData['action'] =$nestedData['action'] ."<a href='{$edit}' class='btn btn-warning btn-sm' style='    padding: 3px 6px;'><i class='fa fa-edit'></i></a>";
          if(Auth::user()->can('product-delete'))
          $nestedData['action'] =$nestedData['action'] .$deleteBtn;
          $data[] = $nestedData;
        }
      }else{
        $selectThisPageCheckBox = false;
      }

      $json_data = array(
          "draw"            => intval($request->input('draw')),  
          "recordsTotal"    => intval($totalData),  
          "recordsFiltered" => intval($totalFiltered), 
          "data"            => $data,
          "prevSelVal"      => $selIds,
          "selectThisPageCheckBox" => $selectThisPageCheckBox
      );

      return json_encode($json_data); 
    }

    public function custompdfdexport(Request $request){
      $getExportData = json_decode($request->exportedData)->data;
      $pageTitle = $request->pageTitle;
      $columns = json_decode($request->columns);
      $properties = json_decode($request->properties);
      set_time_limit ( 300 );
      $pdf = PDF::loadView('company.products.exportpdf', compact('getExportData', 'pageTitle', 'properties', 'columns'))->setPaper('a4', 'portrait');
      $download = $pdf->download($pageTitle.'.pdf');
      return $download;
    }

    public function changeStarredStatus(Request $request){
      $company_id = config('settings.company_id');
      $productInstance = Product::findOrFail($request->product_id);
      
      if($request->currentStar==1){
        $productInstance->star_product = 0;
      }elseif($request->currentStar==0){
        $productInstance->star_product = 1;
      }
      
      $productInstance->update();

      $sent = sendPushNotification_(getFBIDs($company_id), 8, null, array("data_type" => "product", "product" => $productInstance, "action" => "update"));

      return response()->json(["statuscode"=>200, "message"=>"Updated Sucessfully."]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $company_id = config('settings.company_id');
      $getClientSetting = getClientSetting();
      
      $product = collect();
      $categories = Category::where('company_id', $company_id)->where('status', '=', 'Active')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
      $brands = Brand::where('company_id', $company_id)->where('status', 'Active')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
      $units = UnitTypes::where('company_id', $company_id)->where('status', 'Active')->orderBy('name', 'asc')->pluck('symbol', 'id')->toArray();
      $colors = Color::pluck('name', 'id')->toArray();
      $conversions = array();

      if($getClientSetting->unit_conversion==1){
        $units_conversions = UnitConversion::where('unit_conversions.company_id', $company_id)
                                  ->leftJoin('unit_types as U', 'U.id', 'unit_conversions.unit_type_id')
                                  ->leftJoin('unit_types as T', 'T.id', 'unit_conversions.converted_unit_type_id')
                                  ->select(\DB::raw("Concat(unit_conversions.quantity, ' ', U.symbol, ' = ', unit_conversions.converted_quantity, ' ', T.symbol) as option_text"), "unit_conversions.id as value", \DB::raw("CONCAT(unit_conversions.unit_type_id, ',',unit_conversions.converted_unit_type_id) as attributes"))
                                  ->orderby('unit_conversions.unit_type_id', 'asc')
                                  ->get()->toArray();
        $select_option = array_column($units_conversions, "option_text", "value");
        $attributes = array_column($units_conversions, "attributes", "value");
        $optionsAttributes = array();
        foreach($attributes as $key=>$attribute){
          $optionsAttributes[$key]['data-ids'] = $attribute; 
        }
        array_push($conversions, $units_conversions, $select_option, $optionsAttributes);

      }

      if($getClientSetting->product_level_tax==1){
        $taxes = TaxType::where('company_id', $company_id)->select('id', DB::raw("CONCAT(name, ' (',percent,'%)') as name"), 'default_flag')->get();
        $tax_types = $taxes->pluck('name','id')->toArray();
        $default_taxes = $taxes->where('default_flag', 1)->pluck('id')->toArray();
        return view('company.products.create', compact('product', 'categories', 'units', 'brands', 'colors', 'tax_types', 'default_taxes', 'getClientSetting', 'conversions'));     
      }else{
        return view('company.products.create', compact('product', 'categories', 'units', 'brands', 'colors', 'getClientSetting', 'conversions'));
      }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $company_id = config('settings.company_id');
      $getClientSetting = getClientSetting();
      $customMessages = [
        'product_name.required' => 'The Product Name is required.',
        'product_name.unique' => 'The Product Name must be unique',
        'product_code.unique' => 'The Product Code must be unique',
        'product_code.alpha_dash' => 'The Product Code must be alpha-numeric characters, dashes or underscores.',
        'tax_type.*.required' => 'Tax Type is required field.',
        'mrp.*.required' => 'Maximum Retail Price is Required.',
        'unit.*.required' => 'Unit is Required.',
        'variant.*.required' => 'Variant cannot be null when flag is ON'
      ];

      $this->validate($request, [
        'product_name' => 'required|unique:products,product_name,NULL,id,company_id,' . $company_id,
        'product_code' => 'alpha_dash|nullable|unique:products,product_code,NULL,id,company_id,' . $company_id,
        'mrp.*' => 'required|numeric',
        'unit.*' => 'required',
        'variant.*' => ($request->varFlag==1)?'required':'nullable',
        'tax_type.*' => ($getClientSetting->product_level_tax==1)?'required':'sometimes|nullable',
      ], $customMessages);

      $company_id = config('settings.company_id');
      $companyName = Auth::user()->companyName($company_id)->domain;
      DB::beginTransaction();
      $product = new Product;
      $product->company_id = $company_id;
      $product->product_name = $request->get('product_name');
      $product->product_code = $request->get('product_code');
      $product->brand = $request->get('brand');
      $product->category_id = $request->get('category_id');
      $variantFlag = $request->get('varFlag');
      $product->variant_flag = $variantFlag;
      $product->star_product = $request->get('star_product');
      $numOfRows = $request->get('numofRows');
      
      if($variantFlag == 0){
        $product->mrp = $request->get('mrp')[$numOfRows[0]];
        $product->unit = $request->get('unit')[$numOfRows[0]];
        $product->short_desc = $request->get('short_desc')[$numOfRows[0]];
      }
      $product->details = $request->get('details');
      if($request->status)
        $product->status = $request->get('status');
      else
        $product->status = 'Active';

      if ($request->file('prodImage')) {
        $this->validate($request, [
          'prodImage' => 'mimes:jpeg,png,jpg|max:500'
        ]);

        $prodImage2 = $request->file('prodImage');
        $realname = pathinfo($request->file('prodImage')->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $prodImage2->getClientOriginalExtension();
        $new_name = $realname . "-" . time() . '.' . $extension;
        $prodImage2->storeAs('public/uploads/' . $companyName . '/products/', $new_name);
        $path = Storage::url('app/public/uploads/' . $companyName . '/products/' . $new_name);
        $product->image = $new_name;
        $product->image_path = $path;
      }
      $saved = $product->save();
      if($saved){
        if($getClientSetting->product_level_tax==1){
          if(!empty($request->get('tax_type'))){
            $product->taxes()->attach($request->get('tax_type'));
          }
        }

        if($getClientSetting->unit_conversion==1){
          $product->conversions()->attach($request->get('unit_conversion'));
        }
      }

      $product_variant_saved = array();
      
      if($variantFlag == 1){
        $variants = $request->get('variant');
        $mrps = $request->get('mrp');
        $units = $request->get('unit');
        $short_descs = $request->get('short_desc');

        if($getClientSetting->var_colors==1){
          $variant_colors = $request->get('var_colors');

          foreach($numOfRows as $index){
            $product_variant = new ProductVariant;
            $product_variant->company_id = $company_id;
            $product_variant->product_id = $product->id;
            $product_variant->variant = $variants[$index];
            $product_variant->mrp = $mrps[$index];
            $product_variant->unit = $units[$index];
            $product_variant->short_desc = $short_descs[$index];
            $saved = $product_variant->save();
            if(is_array($variant_colors)){
              if(array_key_exists($index, $variant_colors)){
                if( !in_array("0", $variant_colors[$index]) ){
                  $product_variant->colors()->attach($variant_colors[$index]);
                }
              }
            }
            array_push($product_variant_saved, $product_variant);
          }
        }else{
          foreach($numOfRows as $index){
            $product_variant = new ProductVariant;
            $product_variant->company_id = $company_id;
            $product_variant->product_id = $product->id;
            $product_variant->variant = $variants[$index];
            $product_variant->mrp = $mrps[$index];
            $product_variant->unit = $units[$index];
            $product_variant->short_desc = $short_descs[$index];
            $saved = $product_variant->save();
            array_push($product_variant_saved, $product_variant);
          }
        }
      }

      if ($saved) {
        DB::commit();
        // $sendingProduct = $product;
        // $unit = DB::table('unit_types')->where('id', $product->unit)->first();
        // $sendingProduct->unit_name = empty($unit) ? "" : $unit->name;
        // $sendingProduct->unit_symbol = empty($unit) ? "" : $unit->symbol;
        // $sendingProduct->product_variants = $request->get('variant');
        
        // if(($request->get('brand')!="")){
        //   $sendingProduct->brand_name = getBrandName($request->get('brand'));
        // }
        
        // if(!empty($product_variant_saved)){
        //   $sendingProduct->product_variants = $product_variant_saved;
        // }

        // $category = getCategory($product->category_id);
        // $sendingProduct->category_name = empty($category) ? "" : $category->name;
        
        // $dataPayload = array("data_type" => "product", "product" => $sendingProduct, "action" => "add");
        // $msgID = sendPushNotification_(getFBIDs($company_id), 8, null, $dataPayload);
        $this->productNotification($company_id, $product->id, "add");
      }else{
        DB::rollback();
      }

      return redirect()->route('company.admin.product', ['domain' => domain()])->with('success', 'Information has been  Added');
    }

    private function productNotification($companyID, $productID, $action) {
      $product = Product::select('products.*', 'categories.name as category_name','brands.name as brand_name', 'unit_types.name as unit_name', 'unit_types.symbol as unit_symbol')
                ->leftJoin('brands', 'brands.id', '=', 'products.brand')
                ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->leftJoin('unit_types', 'unit_types.id', '=', 'products.unit')
                ->where("products.company_id", $companyID)
                ->where("products.id", $productID)
                ->first();
      $pv = DB::table('product_variants')
              ->select('product_variants.*', 'unit_types.name as unit_name', 'unit_types.symbol as unit_symbol')
              ->where("product_variants.company_id", $companyID)
              ->where("product_variants.product_id", $productID)
              ->whereNull("product_variants.deleted_at")
              ->leftJoin('unit_types', 'unit_types.id', '=', 'product_variants.unit')
              ->get()->toArray();

      foreach($pv as $key=>$p){
        $p->variant_colors = $this->getColors($p->id);
      }
      $pvGroupedByProductID = arrayGroupBy($pv,"product_id",true);

      $tempObj = $product;
      $tempObj->product_variants = getArrayValue($pvGroupedByProductID,$product->id);
      $productInstance = $product;
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
      $dataPayload = array("data_type" => "product", "product" => $tempObj, "action" => $action);
      $msgID = sendPushNotification_(getFBIDs($companyID), 8, null, $dataPayload);
      return $msgID;
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

    /**
     * Display the specified resource.
     *
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $company_id = config('settings.company_id');
        $product = Product::findOrFail($request->id);
        if($product->company_id!=Auth::user()->company_id) return redirect()->back();
        // if(getClientSetting()->product_level_tax==1){
          $taxes = $product->taxes;
          if($taxes->first()){
            $taxType = $product->taxes()->get([DB::raw("CONCAT(tax_types.name,' (', tax_types.percent, '%)') as name")])->pluck('name')->toArray();
            // $taxType = $product->taxes()->withTrashed()->select('tax_types.id', DB::raw("CONCAT(tax_types.name, ' (',tax_types.percent,'%)') as name"))->get()->merge(TaxType::where('company_id', $company_id)->select('id', DB::raw("CONCAT(name, ' (',percent,'%)') as name"))->get())->toArray;
          }else{
            $taxType = null;//TaxType::where('company_id', $company_id)->get([DB::raw("CONCAT(tax_types.name,' (', tax_types.percent, '%)') as name")])->pluck('name')->toArray();
          }
        // }else{
        //   $taxType = null;
        // }
        $product_conversions = $product->conversions()->pluck('unit_conversions.id')->toArray();
        $units_conversions = UnitConversion::where('unit_conversions.company_id', $company_id)
                                  ->whereIn('unit_conversions.id', $product_conversions)
                                  ->leftJoin('unit_types as U', 'U.id', 'unit_conversions.unit_type_id')
                                  ->leftJoin('unit_types as T', 'T.id', 'unit_conversions.converted_unit_type_id')
                                  ->select(\DB::raw("Concat(unit_conversions.quantity, ' ', U.symbol, ' = ', unit_conversions.converted_quantity, ' ', T.symbol) as option_text"), "unit_conversions.id as value", \DB::raw("CONCAT(unit_conversions.unit_type_id, ',',unit_conversions.converted_unit_type_id) as attributes"))
                                  ->orderby('unit_conversions.unit_type_id', 'asc')
                                  ->get()->toArray();
        $select_option = implode(',', array_column($units_conversions, "option_text"));
        $productVariants = $product->product_variants;
        
        $action = null;

        if( Auth::user()->can('product-update')){
          $update_url = domain_route('company.admin.product.edit', [$product->id]);
          $action = "<a class='btn btn-warning btn-sm edit' href='{$update_url}' style='padding: 7px 6px;'><i class='fa fa-edit'></i>Edit</a>"; 
        }

        if( Auth::user()->can('product-delete') && $product->orderproducts()->count()==0){
          $delete_url = domain_route('company.admin.product.destroy', [$product->id]);
          $action = $action."<a class='btn btn-danger btn-sm delete' data-pid='{$product->id}' data-vid=''
                      data-url='{$delete_url}' data-toggle='modal' data-target='#deletevar' style='padding: 7px 6px;'><i class='fa fa-trash-o'></i>Delete</a>"; 
        }
        
        if(isset($product))
          return view('company.products.show', compact('product', 'action', 'productVariants', 'taxType', 'select_option'));
        else
          return redirect()->route('company.admin.product', ['domain' => domain()]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
      $company_id = config('settings.company_id');
      $getClientSetting = getClientSetting();
      $categories = Category::where('company_id', $company_id)->where('status', '=', 'Active')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
      $product = Product::findOrFail($request->id);
      if($product->company_id!=Auth::user()->company_id) return redirect()->back();
      $productVariants = $product->product_variants;
      $units = UnitTypes::where('company_id', $company_id)->where('status', 'Active')->orderBy('name', 'asc')->pluck('symbol', 'id')->toArray();
      $brands = Brand::where('company_id', $company_id)->where('status', 'Active')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();

      $colors = Color::pluck('name', 'id')->toArray();
      $conversions = array();
      if ($getClientSetting->unit_conversion==1) {
        $units_conversions = UnitConversion::where('unit_conversions.company_id', $company_id)
                                ->leftJoin('unit_types as U', 'U.id', 'unit_conversions.unit_type_id')
                                ->leftJoin('unit_types as T', 'T.id', 'unit_conversions.converted_unit_type_id')
                                ->select(\DB::raw("Concat(unit_conversions.quantity, ' ', U.symbol, ' = ', unit_conversions.converted_quantity, ' ', T.symbol) as option_text"), "unit_conversions.id as value", \DB::raw("CONCAT(unit_conversions.unit_type_id, ',',unit_conversions.converted_unit_type_id) as attributes"))
                                ->orderby('unit_conversions.unit_type_id', 'asc')
                                ->get()->toArray();
        $select_option = array_column($units_conversions, "option_text", "value");
        $attributes = array_column($units_conversions, "attributes", "value");
        $optionsAttributes = array();
        foreach ($attributes as $key=>$attribute) {
          $optionsAttributes[$key]['data-ids'] = $attribute;
        }
        array_push($conversions, $units_conversions, $select_option, $optionsAttributes);
      }
      if($getClientSetting->product_level_tax==1){
        $taxes = $product->taxes()->select('tax_types.id', DB::raw("CONCAT(tax_types.name, ' (',tax_types.percent,'%)') as name"))->get()->merge(TaxType::where('company_id', $company_id)->select('id', DB::raw("CONCAT(name, ' (',percent,'%)') as name"))->get());
        //TaxType::where('company_id', $company_id)->select('id', 'name', 'default_flag')->get();
        $tax_types = $taxes->pluck('name','id')->toArray();
        // if($product->taxes->count()<=0){
        //   $default_taxes = $taxes->where('default_flag', 1)->pluck('id')->toArray();
        // }else{
          $default_taxes = $product->taxes()->pluck('tax_types.id')->toArray();
        // }
        return view('company.products.edit', compact('product', 'productVariants', 'categories', 'units', 'brands', 'colors', 'tax_types', 'default_taxes', 'getClientSetting', 'conversions'));     
      }else{
        return view('company.products.edit', compact('product', 'productVariants','categories', 'units', 'brands', 'colors', 'getClientSetting', 'conversions'));
      }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
      $company_id = config('settings.company_id');
      $companyName = Auth::user()->companyName($company_id)->domain;
      $getClientSetting = getClientSetting();
      $saved = true;
      $customMessages = [
        'product_name.required' => 'The Product Name field is required.',
        'product_name.unique' => 'The Product Name must be unique',
        'product_code.unique' => 'The Product Code must be unique',
        'product_code.alpha_dash' => 'The Product Code must be alpha-numeric characters, dashes or underscores.',
        'tax_type.*.required' => 'Tax Type is required field.',
        'mrp.*' => 'Maximum Retail Price is Required.',
        'variant.*.required' => 'Variant cannot be null when flag is ON',
        'newrow_variant.*.required' => 'Variant cannot be null when flag is ON'
      ];

      $this->validate($request, [
        'product_name' => 'required|unique:products,product_name,' . $request->id . ',id,company_id,' . $company_id . ',deleted_at,NULL',
        'product_code' => 'alpha_dash|nullable|unique:products,product_code,' . $request->id . ',id,company_id,' . $company_id . ',deleted_at,NULL',
        'mrp.*' => 'required',
        'unit.*' => 'required',
        'variant.*' => ($request->varFlag==1)?'required':'nullable',
        'newrow_variant.*' => ($request->varFlag==1)?'sometimes|required':'nullable',
        'tax_type.*' => ($getClientSetting->product_level_tax==1)?'required':'sometimes|nullable',
      ], $customMessages);

      DB::beginTransaction();
      $product = Product::findOrFail($request->id);
      if($product->company_id!=Auth::user()->company_id) return redirect()->back();
      $storedFlag = $product->variant_flag;
      $product->product_name = $request->get('product_name');
      $product->product_code = $request->get('product_code');
      $product->brand = $request->get('brand');
      $product->category_id = $request->get('category_id');
      $currentVariantFlag = $request->get('varFlag');
      $product->variant_flag = $currentVariantFlag;
      $product->star_product = $request->get('star_product');
      $product->details = $request->get('details');
      if(Auth::user()->can('product-status'))
        $product->status = $request->get('status');
      
      $productVariantIds = $request->get('productVariantIds');
      if($request->has('numofRows')){
        $numOfRows = $request->get('numofRows');
        if($currentVariantFlag==0){
          $product->mrp = $request->get('mrp')[$numOfRows[0]];
          $product->unit = $request->get('unit')[$numOfRows[0]];
          $product->short_desc = $request->get('short_desc')[$numOfRows[0]];
          if($storedFlag == 1){
            $productsVariants = ProductVariant::where('product_id', $product->id)->delete();
            $saved = $productsVariants;
          }
        }elseif($currentVariantFlag==1 && $storedFlag == 1){
          foreach($numOfRows as $index){
            $productsVariants = ProductVariant::findOrFail($productVariantIds[$index]);
            $productsVariants->company_id = $company_id;
            $productsVariants->product_id = $product->id;
            $productsVariants->variant = $request->get('variant')[$index];
            if($getClientSetting->var_colors ==1){
              if($productsVariants->colors->count()>0){
                $productsVariants->colors()->detach();
              }
              $variant_colors = $request->get('var_colors');
              if(is_array($variant_colors)){
                if(array_key_exists($index, $variant_colors)){
                  if(!in_array("0", $variant_colors[$index])){
                    $productsVariants->colors()->attach($variant_colors[$index]);
                  }
                }
              }
            }
            $productsVariants->mrp = $request->get('mrp')[$index];
            $productsVariants->short_desc = $request->get('short_desc')[$index];
            $productsVariants->unit = $request->get('unit')[$index];
            $saved = $productsVariants->update();
          }
        }elseif($currentVariantFlag==1 && $storedFlag == 0){
          foreach($numOfRows as $index){
            $productsVariants = new ProductVariant;
            $productsVariants->company_id = $company_id;
            $productsVariants->product_id = $product->id;
            $productsVariants->variant = $request->get('variant')[$index];
            $productsVariants->mrp = $request->get('mrp')[$index];
            $productsVariants->short_desc = $request->get('short_desc')[$index];
            $productsVariants->unit = $request->get('unit')[$index];
            $product->mrp = null;
            $product->unit = null;
            $product->short_desc = null;
            $saved = $productsVariants->save();
            if($getClientSetting->var_colors ==1){
              $variant_colors = $request->get('var_colors');
              if(is_array($variant_colors)){
                if(array_key_exists($index, $variant_colors)){
                  if(!in_array("0", $variant_colors[$index])){
                    $productsVariants->colors()->attach($variant_colors[$index]);
                  }
                }
              }
            }
          }
        }
      }

      if($request->has('newrow_numofRows')){
        if(!$request->has('numofRows')){
          ProductVariant::where('product_id', $product->id)->delete();
        }
        $numOfRows = $request->get('newrow_numofRows');
        if($currentVariantFlag==0){
          $product->mrp = $request->get('newrow_mrp')[$numOfRows[0]];
          $product->unit = $request->get('newrow_unit')[$numOfRows[0]];
          $product->short_desc = $request->get('newrow_short_desc')[$numOfRows[0]];
          if($storedFlag == 1){
            $productsVariants = ProductVariant::where('product_id', $product->id)->delete();
            $saved = $productsVariants;
          }
        }elseif($currentVariantFlag==1){
          foreach($numOfRows as $index){
            $productsVariants = new ProductVariant;
            $productsVariants->company_id = $company_id;
            $productsVariants->product_id = $product->id;
            $productsVariants->variant = $request->get('newrow_variant')[$index];
            $productsVariants->mrp = $request->get('newrow_mrp')[$index];
            $productsVariants->short_desc = $request->get('newrow_short_desc')[$index];
            $productsVariants->unit = $request->get('newrow_unit')[$index];
            if ($storedFlag == 0) {
              $product->mrp = null;
              $product->unit = null;
              $product->short_desc = null;
            }
            $saved = $productsVariants->save();
            if($getClientSetting->var_colors ==1){
              $variant_colors = $request->get('newrow_var_colors');
              if(is_array($variant_colors)){
                if(array_key_exists($index, $variant_colors)){
                  if(!in_array("0", $variant_colors[$index])){
                    $productsVariants->colors()->attach($variant_colors[$index]);
                  }
                }
              }
            }
          }
        }
      }
        
      if ($request->file('prodImage')) {
        if (!empty($product->image_path) && file_exists(base_path() . $product->image_path)) {
          $oldimg = base_path() . $product->image_path;
          @unlink($oldimg);
        }
        $this->validate($request, [
          'prodImage' => 'mimes:jpeg,png,jpg|max:500'
        ]);

        $prodImage2 = $request->file('prodImage');
        $realname = pathinfo($request->file('prodImage')->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $prodImage2->getClientOriginalExtension();
        $new_name = $realname . "-" . time() . '.' . $extension;
        $prodImage2->storeAs('public/uploads/' . $companyName . '/products/', $new_name);
        $path = Storage::url('app/public/uploads/' . $companyName . '/products/' . $new_name);
        $product->image = $new_name;
        $product->image_path = $path;
      }
      if($saved){
        $saved = $product->save();
      }
      
      if ($saved) {
          if($getClientSetting->product_level_tax==1){
            $product->taxes()->sync($request->get('tax_type'));
          }

          if($getClientSetting->unit_conversion==1){
            $product->conversions()->sync($request->get('unit_conversion'));
          }
          DB::commit();
          // $sendingProduct = $product;
          // $category = getCategory($product->category_id);
          // $sendingProduct->category_name = empty($category) ? "" : $category->name;
          // $sendingProduct->unit_symbol = getUnitName($product->unit);

          // $sendingProduct->product_variants = getProductVariants($company_id,$product->id);

          // $sent = sendPushNotification_(getFBIDs($company_id), 8, null, array("data_type" => "product", "product" => $sendingProduct, "action" => "update"));
          $this->productNotification($company_id, $product->id, "update");
      }else{
        DB::rollback();
      }

      return redirect()->route('company.admin.product', ['domain' => domain()])->with('success', 'Information has been  Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {         
        $deleted_array = array();
        $company_id = config('settings.company_id');
        if(isset($request->productVariantId) ){
          $product_variant = ProductVariant::findOrFail($request->productVariantId);
          if($product_variant->company_id!=Auth::user()->company_id) return redirect()->back();
          $product_id = $product_variant->product_id;
          if($product_variant->orderproducts()->count()>0){
            if($request->has('from_view')){
              \Session::flash('warning', 'Failed Deleting Variant!!!Variant has been used in orders.');
              return redirect()->back();
            }else {
              return response()->json(['statuscode'=>400, 'message'=>'Failed Deleting Variant!!!Variant has been used in orders.']);
            }
          }else{
            $product_variant->delete();
            $variantNotf = $this->productNotification($company_id, $product_id, "update");
            Log::info($variantNotf);
            $product = Product::find($product_id);
            if($product->product_variants()->count()==0){
              $sendingProduct = $product;
              $product->delete();
              $dataPayload = array("data_type" => "product", "product" => $sendingProduct, "action" => "delete");
              $msgID = sendPushNotification_(getFBIDs($company_id), 8, null, $dataPayload);
              $request->session()->flash('success', 'Product Deleted Successfully.');
              $home_page = domain_route('company.admin.product');
              return redirect()->to($home_page);
            }
            if($request->has('from_view')){
              \Session::flash('success', 'Variant Deleted Successfully.');
              return redirect()->back();
            }else {
              return response()->json(['statuscode'=>200, 'message'=>'Variant Deleted Successfully.']);
            }
          }
        }else{
          $company_id = config('settings.company_id');
          $product = Product::findOrFail($request->id);
          if($product->company_id!=Auth::user()->company_id) return redirect()->back();
          if($product->orderproducts()->count()>0){
            \Session::flash('warning', 'Failed Deleting Product!!!Product has been used in orders.');
            return redirect()->back();
          }
          $product->taxes()->detach();
          if($product->variant_flag==1){
            $product_variant = ProductVariant::where('product_id',$request->id)->get();
            array_push($deleted_array,$product_variant);
          }
          if (!empty($product)) {
            $deleted = $product->delete();
            if(!empty($product_variant)){
              ProductVariant::where('product_id',$request->id)->delete();
            }
            if ($deleted) {
              $sendingProduct = $product;
              $deleted_variants = $deleted_array;
              $dataPayload = array("data_type" => "product", "product" => $sendingProduct, "action" => "delete");
              $msgID = sendPushNotification_(getFBIDs($company_id), 8, null, $dataPayload);
              if($request->has('from_view')){
                \Session::flash('success', 'Product Deleted Successfully.');
                $home_page = domain_route('company.admin.product');
                return redirect()->to($home_page);
              }else{
                return redirect()->back()->with('success', 'Product has been deleted.');

              }
              
            }
          }
        }
        return redirect()->back()->with('error', 'Some Error Occured');
    }

    public function massdestroy(Request $request)
    {         
        $deleted_array = array();
        $company_id = config('settings.company_id');
        if(is_array($request->product_id)){
          $productIds = explode(',', $request->product_id[0]);
          if(!empty($productIds)){
            $deleteableProducts = Product::whereCompanyId($company_id)->whereIn('id', $productIds)->whereDoesntHave('orderproducts')->pluck('id')->toArray();
            if(!empty($deleteableProducts)){
              $relatedVariants = ProductVariant::whereIn('product_id',$deleteableProducts)->delete();
              foreach($deleteableProducts as $deleteableProduct){
                $product = Product::find($deleteableProduct);
                $dataPayload = array("data_type" => "product", "product" => $product, "action" => "delete");
                $product->delete();
                $msgID = sendPushNotification_(getFBIDs($company_id), 8, null, $dataPayload);
              }
              DB::table('tax_on_products')->where('product_id', $deleteableProducts)->delete();
              Log::info(print_r(array('mass product delete', $msgID), true));
              if(count($deleteableProducts)>1){
                \Session::flash('success', count($deleteableProducts).' products has been deleted successfully.');
              }else{
                \Session::flash('success', count($deleteableProducts).' product has been deleted successfully.');
              }
            }
          }
        }
        return back();
    }

    public function hasInfo(){
        $company_id = config('settings.company_id');
        $deleteableProducts = Product::whereCompanyId($company_id)->whereDoesntHave('orderproducts')->pluck('id')->toArray();
        return response()->json(['deleteableProducts_id'=>$deleteableProducts]);
    }

    public function changeStatus(Request $request)
    {
        $company_id = config('settings.company_id');
        if(is_array($request->product_id)){
          $productIds = explode(',', $request->product_id[0]);
          if(!empty($productIds)){
            $updatedProducts = Product::where('company_id', $company_id)->whereIn('id', $productIds)->update(['status' => $request->status]);
            if ($updatedProducts) {
              foreach($productIds as $productId){
                $product = Product::find($productId);
                if($product){
                  $sendingProduct = $product;
                  $unit = DB::table('unit_types')->where('id', $product->unit)->first();
                  $sendingProduct->unit_name = empty($unit) ? "" : $unit->name;
                  $sendingProduct->unit_symbol = empty($unit) ? "" : $unit->symbol;
                  if(($request->get('brand')!="")){
                    $sendingProduct->brand_name = getBrandName($request->get('brand'));
                  }
      
                  $sendingProduct->product_variants = getProductVariants($company_id,$product->id);
                  $dataPayload = array("data_type" => "product", "product" => $sendingProduct, "action" => "update_status");
                  $msgID = sendPushNotification_(getFBIDs($company_id), 8, null, $dataPayload);
                }
              }
  
            }
            \Session::flash('success', "Products status has been updated succesfully.");
          }
        }else{
          $product = Product::findOrFail($request->product_id);
          if($product->company_id!=Auth::user()->company_id) return redirect()->back();
          $product->status = $request->status;
          $saved = $product->save();
          
          if ($saved) {
  
              $sendingProduct = $product;
              $unit = DB::table('unit_types')->where('id', $product->unit)->first();
              $sendingProduct->unit_name = empty($unit) ? "" : $unit->name;
              $sendingProduct->unit_symbol = empty($unit) ? "" : $unit->symbol;
              if(($request->get('brand')!="")){
                  $sendingProduct->brand_name = getBrandName($request->get('brand'));
              }
  
              $sendingProduct->product_variants = getProductVariants($company_id,$product->id);
              $dataPayload = array("data_type" => "product", "product" => $sendingProduct, "action" => "update_status");
              $msgID = sendPushNotification_(getFBIDs($company_id), 8, null, $dataPayload);
  
              \Session::flash('success', "Product status has been updated succesfully.");
          }

        }
        return back();
    }

    public function getProduct(Request $request)
    {
        //echo "hi";
        // $company_id=config('settings.company_id');
        // $categories= Category::pluck('name', 'id')->toArray();
        // $product = Product::where('id',$request->id)->where('company_id',$company_id)->first();
        $product = Product::findOrFail($request->id);
        return response()->json($product);
    }

    public function productVariant(Request $request)
    {
        $product = Product::findOrFail($request->id);
        if($product->variant_flag == 1){
            $products_variant = $product->join('product_variants','product_variants.product_id', '=','products.id')
                                        ->where('products.id', $product->id)
                                        ->get();
            foreach($products_variant as $p){
              // if($p->colors->count()>0){
              //   $getColors = $p->colors->pluck('value')->toArray();
                $p->variant_colors = $this->getColors($p->id);
              // }
            }

            foreach($products_variant as $products_var){
                $unit_types = UnitTypes::where('id',$products_var->unit)->value('symbol');
                $products_var['unit_types'] = $unit_types;
            }
            return response()->json($products_variant); 

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

    public function getVariantColors(Request $request){
      $company_id = config('settings.company_id');

      $variantColors = ProductVariant::where('company_id', $company_id)->where('id', $request->id)->select('variant_colors')->first();
      
      return $variantColors->variant_colors;
    }
}
