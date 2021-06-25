<?php

namespace App\Http\Controllers\Company\Admin;

use App\Brand;
use App\Category;
use App\OrderScheme;
use App\Product;
use App\ProductVariant;
use App\Scheme;
use App\SchemeType;
use App\UnitTypes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Builder;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class SchemesController extends Controller
{

    public $company_id;

    public function __construct()
    {
        $this->middleware('auth');
        $this->company_id = config('settings.company_id');

    }

    public function index()
    {
        if (!(config('settings.product') == 1 && Auth::user()->can('settings-view')) || !(Auth::user()->employee()->first()->role()->first()->name == "Full Access")) {
            return redirect()->back()->with('alert', 'You are not Authorized to view this link.');
        }
        return view('company.schemes.index', [
            'product_schemas' => Scheme::where('status', 1)->where('company_id', $this->company_id)->get()
        ]);
    }

    public function ajaxDatatable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'description',
            3 => 'start_date',
            4 => 'created_at',
            5 => 'status',
            6 => 'action',
        );

        $start = $request->input('start');
        $limit = $request->input('length');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $prepQuery = Scheme::where('company_id', $this->company_id);

        $totalData = $prepQuery->count();
        $totalFiltered = $totalData;


        if (empty($request->input('search.value'))) {
            $totalFiltered = $prepQuery->count();
            $schemes = $prepQuery
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get(['id', 'name', 'description', 'start_date', 'end_date', 'status', 'user_id', 'created_at', 'employee_id', 'scheme_type_id']);
        } elseif (!(empty($request->input('search.value')))) {

            $search = $request->input('search.value');

            $schemesSearchQuery = $prepQuery
                ->where(function ($query) use ($search) {
                    $query->orWhere('name', 'LIKE', "%{$search}%");
                    $query->orWhere('status', 'LIKE', "%{$search}%");
                });
            $totalFiltered = $schemesSearchQuery->count();
            $schemes = $schemesSearchQuery
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get(['id', 'name', 'description', 'start_date', 'end_date', 'status', 'user_id', 'created_at', 'employee_id', 'scheme_type_id']);
        }

        if (!empty($schemes)) {
            $i = $start;
            foreach ($schemes as $scheme) {
                $id = $scheme->id;
                $status = $scheme->status;
                $edit = domain_route('company.admin.scheme.edit', [$id]);
                $delete = domain_route('company.admin.scheme.destroy', [$id]);
                $view = domain_route('company.admin.scheme.view', [$id]);
                $nestedData['id'] = ++$i;
                $nestedData['name'] = $scheme->name;
                $nestedData['start_date'] = getDeltaDate(date('Y-m-d', strtotime($scheme->start_date))) . ' to ' . getDeltaDate(date('Y-m-d', strtotime($scheme->end_date)));
                $nestedData['description'] = Str::limit($scheme->description, 50);
//                $nestedData['user_id'] = Employee::find($scheme->employee_id)->name;
                $nestedData['created_at'] = getDeltaDate(date('Y-m-d', strtotime($scheme->created_at)));


                if ($status == 'Active')
                    $spanTag = "<span class='label label-success'>$status</span>";
                elseif ($status == 'Inactive')
                    $spanTag = "<span class='label label-warning'>$status</span>";

                if (getClientSetting()->order_with_amt == 1 && $scheme->scheme_type_id != 1) {
                    $nestedData['status'] = "<span class='label label-warning'>Inactive</span>";
                } else {
                    $nestedData['status'] = "<a href='#' class='edit-modal' data-id='$id' data-status='$status'>{$spanTag}</a>";
                }


                $viewButton = "<a href='{$view}' class='btn btn-success btn-sm' style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>";


                if ($status == 'Active') {
                    $editButton = "<a href='{$edit}' class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i>";
                } elseif ($status == 'Inactive') {
                    $editButton = '';
                }

                $scheme_orders = OrderScheme::pluck('scheme_id')->all();


                if (!in_array($id, $scheme_orders)) {
                    $deleteBtn = "<a class='btn btn-danger btn-sm delete del-modal' data-mid='$id' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i class='fa fa-trash'></i></a>";
                } else {
                    $deleteBtn = '';
                }

                if (getClientSetting()->order_with_amt == 1 && $scheme->scheme_type_id != 1) {
                    $nestedData['action'] = '';
                } else {
                    $nestedData['action'] = "$viewButton $editButton</a>{$deleteBtn} ";

                }

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        return json_encode($json_data);

    }

    public function create()
    {
        if (!(config('settings.product') == 1 && Auth::user()->can('settings-view')) || !(Auth::user()->employee()->first()->role()->first()->name == "Full Access")) {
            return redirect()->back()->with('alert', 'You are not Authorized to view this link.');
        }

        return view('company.schemes.create', [
            'scheme_types' => SchemeType::where('status', 1)->get(),
            'clients' => Auth::user()->handleQuery('client')->where('status', 'Active')->orderBy('company_name', 'asc')->get(['company_name', 'id'])->toArray(),
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => ['required', 'max:256'],
            'description' => ['required'],
            'image' => ['nullable', 'mimes:jpeg,png,jpg', 'max:2048'],
            'scheme_type_id' => ['required', 'exists:scheme_types,id'],
            'product' => ['required']
        ]);

        foreach ($request->product as $product) {
            $productsWithVariants = explode('-', $product);
            $products[] = intval($productsWithVariants[0]);
            if (isset($productsWithVariants[1])) {
                $variants[][$productsWithVariants[0]] = intval($productsWithVariants[1]);
            }
        }

        if (!is_null($request->offered_product)) {
            $offeredProductIdWithVariantId = explode('-', $request->offered_product);
            $offerProductId = $offeredProductIdWithVariantId[0];
            if (count($offeredProductIdWithVariantId) == 2) {
                $offerProductVariantId = $offeredProductIdWithVariantId[1];
            }
        }

        //old validity date only in english date will be removed when fully tested both dates
//        $dates = explode('to', $request->validity_date);
//        $start_date = Carbon::parse($dates[0])->toDateTimeString();
//        if (isset($dates[1])) {
//            $end_date = Carbon::parse($dates[1])->endOfDay()->toDateTimeString();
//        } else {
//            $end_date = Carbon::parse($dates[0])->endOfDay()->toDateTimeString();
//        }

        $start_date = Carbon::parse($request->start_edate)->toDateTimeString();
        $end_date = Carbon::parse($request->end_edate)->endOfDay()->toDateTimeString();

        $scheme = new Scheme;
        $scheme->name = $request->name;
        $scheme->description = $request->description;
        $scheme->start_date = $start_date;
        $scheme->end_date = $end_date;
        $scheme->image = $this->uploadImage($request->image);
        $scheme->scheme_type_id = $request->scheme_type_id;
        $scheme->product_id = json_encode($products);
        $scheme->product_variant = isset($variants) ? json_encode($variants) : null;
        $scheme->offered_product = isset($offerProductId) ? $offerProductId : null;
        $scheme->offered_product_variant = isset($offerProductVariantId) ? $offerProductVariantId : null;
        $scheme->qty = $request->qty;
        $scheme->offered_qty = $request->offered_qty;
        $scheme->amount = $request->amount;
        $scheme->discount_amount = $request->discount_amount;
        $scheme->percentage_off = $request->percentage_off;
        $scheme->parties = json_encode($request->party);
        $scheme->save();

        $dataPayload = array("data_type" => "scheme", "scheme" => $scheme->refresh(), "action" => 'scheme created');
        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        $sent = sendPushNotification_($fbIDs, 32, null, $dataPayload);

        Log::info($sent);

        Session::flash('success', 'Scheme has been successfully created');
        return redirect()->route('company.admin.scheme', domain());

    }

    public function show(Request $request)
    {
        if (!(config('settings.product') == 1 && Auth::user()->can('settings-view')) || !(Auth::user()->employee()->first()->role()->first()->name == "Full Access")) {
            return redirect()->back()->with('alert', 'You are not Authorized to view this link.');
        }

        $productName = array();
        $scheme = Scheme::with('schemeTypes')->find($request->id);

        if (!isset($scheme->product_variant)) {
            $products = json_decode($scheme->product_id);
            foreach ($products as $product) {
                $productName[] = Product::withTrashed()->find($product)->product_name;
            }
        } else {
            $products = json_decode($scheme->product_id);
            $productWithVariant = json_decode($scheme->product_variant, true);

            foreach ($products as $product) {
                foreach ($productWithVariant as $variant) {
                    if (isset($variant[$product])) {
                        $getProduct = Product::withTrashed()->find($product);
                        $variant = ProductVariant::withTrashed()->find($variant[$product]);
                        $productName[] = $getProduct->product_name . '-' . $variant->variant;
                    } else {
                        $productName[] = Product::withTrashed()->find($product)->product_name;
                    }
                }
            }
        }

        if ($scheme->status === 'Active') {
            $edit_link = domain_route('company.admin.scheme.edit', [$request->id]);
            $action = "<a class='btn btn-warning btn-sm edit' href='{$edit_link}'  style='padding: 7px 6px;'><i class='fa fa-edit'></i>Edit</a>";
        } else {
            $action = '';
        }

        $scheme_orders = OrderScheme::pluck('scheme_id')->all();


        if (!in_array($request->id, $scheme_orders)) {
            $delete = domain_route('company.admin.scheme.destroy', [$request->id]);
            $action = $action . "<a class='btn btn-danger btn-sm delete' data-mid='{$request->id}' data-url='{$delete}' data-toggle='modal' data-target='#delete' style='padding: 7px 6px;'><i class='fa fa-trash-o'></i>Delete</a>";
        }


        return view('company.schemes.view', [
            'scheme' => $scheme,
            'productNames' => array_unique($productName),
            'action' => $action,
            'clients' => Auth::user()->handleQuery('client')->where('status', 'Active')->orderBy('company_name', 'asc')->get(['company_name', 'id'])->toArray(),
            'selected_parties' => collect(\GuzzleHttp\json_decode($scheme->parties)),
        ]);
    }

    public function edit(Request $request)
    {

        if (!(config('settings.product') == 1 && Auth::user()->can('settings-view')) || !(Auth::user()->employee()->first()->role()->first()->name == "Full Access")) {
            return redirect()->back()->with('alert', 'You are not Authorized to view this link.');
        }


        $productName = array();
        $offeredProductName = array();
        $scheme = Scheme::with('schemeTypes')->where('company_id', $this->company_id)->find($request->id);

        if (getClientSetting()->order_with_amt == 1 && $scheme->scheme_type_id != 1) {
            Session::flash('success', 'Cannot edit this scheme');
            return back();
        }
        if (!isset($scheme->product_variant)) {
            $products = json_decode($scheme->product_id);
            foreach ($products as $product) {
                $productDetail = Product::withTrashed()->find($product);
                $brand = Brand::find($productDetail->brand);
                $category = Category::find($productDetail->category_id);
                $productName[$product] = $productDetail->product_name . ($brand ? '-' . $brand->name : '') . ($category ? '-' . $category->name : '');
            }
        } else {
            $products = json_decode($scheme->product_id);
            $productWithVariant = json_decode($scheme->product_variant, true);
            foreach ($products as $product) {
                if ($this->findKey($productWithVariant, $product)) {
                    foreach ($productWithVariant as $variant) {
                        if (!empty($variant[$product])) {
                            $getProduct = Product::withTrashed()->where('company_id', $this->company_id)->find($product);
                            $brand = Brand::find($getProduct->brand);
                            $category = Category::find($getProduct->category_id);
                            $variant = ProductVariant::withTrashed()->where('product_id', $getProduct->id)->find($variant[$product]);
                            $productName[$getProduct->id . '-' . $variant->id] = $getProduct->product_name . ' - ' . $variant->variant . ($brand ? '-' . $brand->name : '') . ($category ? '-' . $category->name : '');
                        }
                    }
                } else {
                    $getProduct = Product::withTrashed()->where('company_id', $this->company_id)->find($product);
                    $brand = Brand::find($getProduct->brand);
                    $category = Category::find($getProduct->category_id);
                    $productName[$getProduct->id] = $getProduct->product_name . ($brand ? '-' . $brand->name : '') . ($category ? '-' . $category->name : '');
                }
            }
        }

        if (isset($scheme->offered_product)) {
            $offeredProduct = Product::withTrashed()->where('company_id', $this->company_id)->find($scheme->offered_product);
            $brand = Brand::find($offeredProduct->brand);
            $category = Category::find($offeredProduct->category_id);
            if (isset($scheme->offered_product_variant)) {
                $offeredVariant = ProductVariant::withTrashed()->where('product_id', $offeredProduct->id)->find($scheme->offered_product_variant);
                $offeredProductName[$offeredProduct->id . ' - ' . $offeredVariant->id] = $offeredProduct->product_name . ' - ' . $offeredVariant->variant . ($brand ? ' - ' . $brand->name : '') . ($category ? ' - ' . $category->name : '');;
            } else {
                $offeredProductName[$offeredProduct->id] = $offeredProduct->product_name . ($brand ? ' - ' . $brand->name : '') . ($category ? ' - ' . $category->name : '');
            }
        }
        $orderedScheme = OrderScheme::get()->pluck('scheme_id')->toArray();
        return view('company.schemes.edit', [
            'scheme' => Scheme::with('schemeRule', 'schemeTypes')->where('company_id', $this->company_id)->find($request->id),
            'scheme_types' => SchemeType::get(),
            'products' => $productName,
            'offered_product' => $offeredProductName,
            'is_scheme_used' => in_array($request->id, $orderedScheme),
            'clients' => Auth::user()->handleQuery('client')->where('status', 'Active')->orderBy('company_name', 'asc')->get(['company_name', 'id'])->toArray(),
        ]);
    }

    public function update(Request $request)
    {
        if ($request->file('image')) {
            $request->validate([
                'name' => ['required', 'max:256'],
                'description' => ['required'],
                'image' => ['nullable', 'mimes:jpeg,png,jpg', 'max:2048'],
                'status' => ['required']
            ]);
        } else {
            $request->validate([
                'name' => ['required', 'max:256'],
                'description' => ['required'],
                'status' => ['required']
            ]);
        }


        $scheme = Scheme::FindOrFail($request->id);

//        $dates = explode('to', $request->validity_date);
//        $start_date = Carbon::parse($dates[0])->toDateTimeString();
//        if (isset($dates[1])) {
//            $end_date = Carbon::parse($dates[1])->endOfDay()->toDateTimeString();
//        } else {
//            $end_date = Carbon::parse($dates[0])->endOfDay()->toDateTimeString();
//        }

        $start_date = Carbon::parse($request->start_edate)->toDateTimeString();
        $end_date = Carbon::parse($request->end_edate)->endOfDay()->toDateTimeString();

        if ($request->is_scheme_used == 1) {
            $scheme->name = $request->name;
            $scheme->description = $request->description;
            $scheme->start_date = $start_date;
            $scheme->end_date = $end_date;
            $scheme->image = $request->file('image') ? $this->uploadImage($request->image) : $request->image;
            $scheme->status = $request->status;
        } else {
            foreach ($request->product as $product) {
                $productsWithVariants = explode('-', $product);
                $products[] = intval($productsWithVariants[0]);
                if (isset($productsWithVariants[1])) {
                    $variants[][$productsWithVariants[0]] = intval($productsWithVariants[1]);
                }
            }

            if (!is_null($request->offered_product)) {
                $offeredProductIdWithVariantId = explode('-', $request->offered_product);
                $offerProductId = $offeredProductIdWithVariantId[0];
                if (count($offeredProductIdWithVariantId) == 2) {
                    $offerProductVariantId = $offeredProductIdWithVariantId[1];
                }
            }

            $scheme->name = $request->name;
            $scheme->description = $request->description;
            $scheme->start_date = $start_date;
            $scheme->end_date = $end_date;
            $scheme->image = $request->file('image') ? $this->uploadImage($request->image) : $request->image;
            $scheme->scheme_type_id = $request->scheme_type_id;
            $scheme->product_id = json_encode($products);
            $scheme->product_variant = isset($variants) ? json_encode($variants) : null;
            $scheme->offered_product = isset($offerProductId) ? $offerProductId : null;
            $scheme->offered_product_variant = isset($offerProductVariantId) ? $offerProductVariantId : null;
            $scheme->qty = $request->qty;
            $scheme->offered_qty = $request->offered_qty;
            $scheme->amount = $request->amount;
            $scheme->discount_amount = $request->discount_amount;
            $scheme->percentage_off = $request->percentage_off;
            $scheme->status = $request->status;
            $scheme->parties = json_encode($request->party);
        }

        $scheme->save();

        $dataPayload = array("data_type" => "scheme", "scheme" => $scheme->refresh(), "action" => 'scheme updated');
        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        sendPushNotification_($fbIDs, 32, null, $dataPayload);

        Session::flash('success', 'Updated Successfully');
        return redirect()->route('company.admin.scheme', domain());
    }

    public function destroy(Request $request)
    {
        $scheme = Scheme::findOrFail($request->id);
        $scheme->schemeRule()->delete();
        $scheme->delete();

        $dataPayload = array("data_type" => "scheme", "scheme" => $scheme, "action" => 'scheme deleted');
        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        sendPushNotification_($fbIDs, 32, null, $dataPayload);

        Session::flash('success', 'Scheme has been deleted.');
        if ($request->has('prev_url')) return redirect()->to($request->prev_url);
        return back();

    }

    /*
     * this function checks all the schemes with product id and variant ids
     * provided in the requested scheme and checks if the same id and
     * variants are already present in other schemes if present in
     * other schemes with active status and valid date-range
     * it will prevent the requested scheme to change
     * status from inactive to active as a product
     * can be active only in single scheme
     */
    public function changeStatus(Request $request)
    {
        $scheme = Scheme::where('company_id', $this->company_id)->find($request->schema_id);

        $productIds = json_decode($scheme->product_id);
        $variants = json_decode($scheme->product_variant, true);

        foreach ($productIds as $id) {
            if (isset($variants)) {
                foreach ($variants as $key => $variant) {
                    if (isset($variant[$id])) {
                        $schemes = Scheme::where('company_id', $this->company_id)
                            ->where('status', 1)
                            ->where('start_date', '<=', Carbon::today())
                            ->where('end_date', '>=', Carbon::today())
                            ->whereJsonContains('product_id', $id)
                            ->whereJsonContains('product_variant', [$id => $variant[$id]])
                            ->get();

                        $schemeCounts = $this->countSchemesBeforeCHangeingStratus($schemes, $scheme);

                    } else {
                        $schemes = Scheme::where('company_id', $this->company_id)
                            ->where('status', 1)
                            ->where('start_date', '<=', Carbon::today())
                            ->where('end_date', '>=', Carbon::today())
                            ->whereJsonContains('product_id', $id)
                            ->get();
                        $schemeCounts = $this->countSchemesBeforeCHangeingStratus($schemes, $scheme);
                    }
                }
            } else {
                $schemes = Scheme::where('company_id', $this->company_id)
                    ->where('status', 1)
                    ->where('start_date', '<=', Carbon::today())
                    ->where('end_date', '>=', Carbon::today())
                    ->whereJsonContains('product_id', $id)
                    ->get();

                $schemeCounts = $this->countSchemesBeforeCHangeingStratus($schemes, $scheme);

            }

        }

        if ($schemeCounts > 0 && $request->status == 'Active') {
            Session::flash('error', 'Status Cannot be Changed, Product in this scheme is already active in another scheme.');
            return redirect()->route('company.admin.scheme', ['domain' => domain()]);
        }
        $scheme->status = $request->status == 'Active' ? 1 : 0;
        $scheme->save();

        $dataPayload = array("data_type" => "scheme", "scheme" => $scheme, "action" => 'scheme updated');
        $fbIDs = DB::table('employees')->where(array(array('company_id', $this->company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
        sendPushNotification_($fbIDs, 32, null, $dataPayload);

        Session::flash('success', 'Status has been changed successfully');
        return redirect()->route('company.admin.scheme', ['domain' => domain()]);

    }

    public function countSchemesBeforeCHangeingStratus($schemas, $scheme)
    {
        $array = [];
        foreach ($schemas as $checkScheme) {
            if (array_intersect(\GuzzleHttp\json_decode($checkScheme->parties), \GuzzleHttp\json_decode($scheme->parties))) {
                $array[] = true;
            }
        }
        return count($array);
    }

    /*
     *  this function will search product based on product name,
     * category_name and brand_name and make products disabled in html
     * which are already available in another active schemes
    */
    public function searchProduct(Request $request)
    {


        if (!isset($request->party)) {
            return response()->json(['message' => 'no party selected']);
        }

        $search = $request->q;
        if (isset($request->scheme_id)) {
            $scheme_id = $request->scheme_id;
        }


        $products = Product::with('product_variants')
            ->where('company_id', $this->company_id)
            ->where('status', 'Active')
            ->where('product_name', 'LIKE', "%{$search}%")
            ->OrwhereHas('brands', function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->where('company_id', $this->company_id)->where('status', 'Active');
            })
            ->OrwhereHas('categories', function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->where('company_id', $this->company_id)->where('status', 'Active');
            })
            ->select('product_name', 'id', 'brand', 'category_id')->get();

        $productNameWithVariants = [];
        foreach ($products as $product) {
            if ($product->product_variants->count()) {
                foreach ($product->product_variants as $variant) {

                    if (isset($scheme_id)) {
                        $scheme = Scheme::where('company_id', $this->company_id)
                            ->where('id', '!=', $scheme_id)
                            ->where('status', 1)
//                            ->where('start_date', '<=', Carbon::today())
                            ->where('end_date', '>=', Carbon::today())
                            ->whereJsonContains('product_id', $product->id)
                            ->whereJsonContains('product_variant', [$product->id => $variant->id])
                            ->latest()
                            ->first();

                        $schemePassed = $this->checkIfOtherPartyHasSameProduct($scheme, $request->party);

                    } else {
                        $scheme = Scheme::where('company_id', $this->company_id)
                            ->where('status', 1)
//                            ->where('start_date', '<=', Carbon::today())
                            ->where('end_date', '>=', Carbon::today())
                            ->whereJsonContains('product_id', $product->id)
                            ->whereJsonContains('product_variant', [$product->id => $variant->id])
                            ->latest()
                            ->first();

                        $schemePassed = $this->checkIfOtherPartyHasSameProduct($scheme, $request->party);

                    }


                    $brand = Brand::find($product->brand);
                    $category = Category::find($product->category_id);


                    //this can be reduced to single line using ternary operator
                    //could also be extracted to a function as it is used in multiple place
                    if (isset($brand) && isset($category)) {
                        $product_name = $product->product_name . ' - ' . $variant->variant . ' - ' . $brand->name . ' - ' . $category->name;
                    } elseif (isset($brand) && !isset($category)) {
                        $product_name = $product->product_name . ' - ' . $variant->variant . ' - ' . $brand->name;
                    } elseif (!isset($brand) && isset($category)) {
                        $product_name = $product->product_name . ' - ' . $variant->variant . ' - ' . $category->name;
                    } else {
                        $product_name = $product->product_name . ' - ' . $variant->variant;
                    }

                    $productNameWithVariants[] = array(
                        'id' => $product->id . '-' . $variant->id,
                        'product_name' => $product_name,
                        'disabled' => $schemePassed
                    );
                }
            } else {

                if (isset($scheme_id)) {
                    $scheme = Scheme::where('company_id', $this->company_id)
                        ->where('id', '!=', $scheme_id)
                        ->where('status', 1)
//                        ->where('start_date', '<=', Carbon::today())
                        ->where('end_date', '>=', Carbon::today())
                        ->whereJsonContains('product_id', $product->id)
                        ->whereNotIn('parties', $request->party)
                        ->latest()
                        ->first();
                    $schemePassed = $this->checkIfOtherPartyHasSameProduct($scheme, $request->party);
                } else {

                    $scheme = Scheme::where('company_id', $this->company_id)
                        ->where('status', 1)
//                        ->where('start_date', '<=', Carbon::today())
                        ->where('end_date', '>=', Carbon::today())
                        ->whereJsonContains('product_id', $product->id)
                        ->latest()
                        ->first();

                    $schemePassed = $this->checkIfOtherPartyHasSameProduct($scheme, $request->party);
                }

                $brand = Brand::find($product->brand);
                $category = Category::find($product->category_id);

                //this can be reduced to single line using ternary operator
                if (isset($brand) && isset($category)) {
                    $product_name = $product->product_name . '-' . $brand->name . '-' . $category->name;
                } elseif (isset($brand) && !isset($category)) {
                    $product_name = $product->product_name . '-' . $brand->name;
                } elseif (!isset($brand) && isset($category)) {
                    $product_name = $product->product_name . '-' . $category->name;
                } else {
                    $product_name = $product->product_name;
                }

                $productNameWithVariants[] = array(
                    'id' => $product->id,
                    'product_name' => $product_name,
                    'disabled' => $schemePassed
                );
            }
        }

        return response()->json($productNameWithVariants);
    }

    public function checkPartySchemes(Request $request)
    {

        $scheme = Scheme::find($request->scheme_id);

        $newParties = array_diff($request->party, \GuzzleHttp\json_decode($scheme->parties));


        $usedProduct = array();
        $usedVariants[] = array();

        foreach ($request->products as $product) {
            $data = explode('-', $product);
            $usedProduct[] = $data[0];
            if (isset($data[1])) {
                $usedVariants[$data[1]] = intVal($data[0]);
            }
        }

//        dd($usedProduct,array_filter($usedVariants));

//        $usedProduct = \GuzzleHttp\json_decode($scheme->product_id);
//        $usedVariants = \GuzzleHttp\json_decode($scheme->product_variant, true);

        $usedVariants = array_filter($usedVariants);

        if (count($newParties)) {
            foreach ($newParties as $party) {
                $schemes = Scheme::wherejsonContains('parties', $party)
                    ->where('end_date', '>=', Carbon::today())
                    ->where('status', 1)
                    ->get();


                foreach ($schemes as $scheme) {

                    $products = \GuzzleHttp\json_decode($scheme->product_id);

                    $checkIfProductMatch = array_intersect($products, $usedProduct);

                    if ($checkIfProductMatch) {
                        if ($scheme->product_variant) {
                            foreach ($checkIfProductMatch as $match) {
                                $product = ProductVariant::where('product_id', $match)->first();
                                if ($product) {
                                    $sameVariants = array_intersect_key($this->flatternMultidimentionalArray(\GuzzleHttp\json_decode($scheme->product_variant, true)), $usedVariants);
                                    if (count($sameVariants)) {
                                        return response([
                                            'status' => 201,
                                        ]);
                                    }
                                }

                            }
                        } else {
                            return response([
                                'status' => 201
                            ]);
                        }
                    }


                }
            }
        }


    }

    private function flatternMultidimentionalArray($array)
    {
        $data = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));

        foreach ($data as $key => $value) {
            $result[$value] = $key;
        }

        return $result;
    }

    private function checkIfOtherPartyHasSameProduct($scheme, $requestParty)
    {
        if (!isset($scheme)) {
            return $scheme;
        }
        if (isset($scheme) && array_intersect(\GuzzleHttp\json_decode($scheme->parties), $requestParty)) {
            return $scheme;
        }
        return null;

    }

    /*
     * search products to be offered, this does not have any restriction,
     * same product can be offered more than one time
     */
    public function offerProducts(Request $request)
    {
        $search = $request->q;

        $products = Product::with('product_variants')
            ->where('company_id', $this->company_id)
            ->where('status', 'Active')
            ->where('product_name', 'LIKE', "%{$search}%")
            ->OrwhereHas('brands', function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->where('company_id', $this->company_id)->where('status', 'Active');
            })
            ->OrwhereHas('categories', function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->where('company_id', $this->company_id)->where('status', 'Active');
            })
            ->select('product_name', 'id', 'brand', 'category_id')->get();

        $productNameWithVariants = array();
        foreach ($products as $product) {

            $brand = Brand::find($product->brand);
            $category = Category::find($product->category_id);

            if ($product->product_variants->count()) {
                foreach ($product->product_variants as $variant) {
                    $productNameWithVariants[$product->id . ' - ' . $variant->id] = $product->product_name . ' - ' . $variant->variant . ($brand ? ' - ' . $brand->name : '') . ($category ? ' - ' . $category->name : '');
                }
            } else {
                $productNameWithVariants[$product->id] = $product->product_name . ($brand ? ' - ' . $brand->name : '') . ($category ? ' - ' . $category->name : '');
            }
        }
        return response()->json($productNameWithVariants);

    }

    public function getAllAvailableSchemes(Request $request)
    {

        $party_id = $request->client_id;
        $products_name = $request->names;
        $product_variants_name = $request->variant_names;
        $quantity = $request->quantity;
        $applied_rate = $request->applied_rate;

        $applied_schemes = isset($request->applied_schemes) ? explode(',', $request->applied_schemes) : $request->applied_schemes;


        $products_information = array();

        foreach ($products_name as $key => $name) {
            if (!is_null($name)) {
                $products_information[] = array(
                    'name' => $name,
                    'variant_name' => $product_variants_name[$key],
                    'quantity' => intval($quantity[$key]),
                    'applied_rate' => $applied_rate[$key]
                );
            }
        }

        //calculating quantity if same items are selected two times
        $count_quantity = collect($products_information)->groupBy(['name', 'variant_name'])->map(function ($items) {
            if (count($items) > 1) {
                return $items->map(function ($data) {
                    $item = collect($data)->first();
                    $item['quantity'] = collect($data)->sum('quantity');
                    $item['applied_rate'] = collect($data)->sum('applied_rate');
                    return $item;
                })->toArray();
            } else {
                $item = collect(array_collapse($items))->first();
                $item['quantity'] = collect(array_collapse($items))->sum('quantity');
                $item['applied_rate'] = collect(array_collapse($items))->sum('applied_rate');
                return $item;
            }
        })->toArray();
        //changing the level of array to same level
        $new_array = array();
        foreach ($count_quantity as $array) {
            foreach ($array as $new) {
                if (is_array($new)) {
                    if (!in_array($new, $new_array)) {
                        $new_array[] = $new;
                    }
                } else {
                    if (!in_array($array, $new_array)) {
                        $new_array[] = $array;
                    }
                }
            }
        }

        $products = array();
        $variants = array();
        $applied_rate = array();
        $quantity = array();
        foreach ($new_array as $key => $values) {
            $products[] = Product::where('product_name', $values['name'])->select('id', 'product_name', 'brand', 'mrp')->where('company_id', $this->company_id)->first();
            $variants[] = !is_null($values['variant_name']) ? ProductVariant::where('variant', $values['variant_name'])->where('product_id', $products[$key]->id)->select('id', 'variant', 'mrp')->first() : null;
            $quantity[] = $values['quantity'];
            $applied_rate[] = $values['applied_rate'];
        }

        $schemes = array();
        foreach (array_filter($products) as $key => $product) {
            if (!is_null($variants[$key])) {
                if (getClientSetting()->order_with_amt == 1) {
                    $scheme = Scheme::where('company_id', $this->company_id)
                        ->where('status', 1)
                        ->where('start_date', '<=', Carbon::today())
                        ->where('end_date', '>=', Carbon::today())
                        ->where('scheme_type_id', 1)
                        ->whereJsonContains('parties', $party_id)
                        ->whereJsonContains('product_id', $product->id)
                        ->whereJsonContains('product_variant', [$product->id => $variants[$key]->id])
                        ->first();
                } else {
                    $scheme = Scheme::where('company_id', $this->company_id)
                        ->where('status', 1)
                        ->where('start_date', '<=', Carbon::today())
                        ->where('end_date', '>=', Carbon::today())
                        ->whereJsonContains('parties', $party_id)
                        ->whereJsonContains('product_id', $product->id)
                        ->whereJsonContains('product_variant', [$product->id => $variants[$key]->id])
                        ->first();
                }

                $schemes[] = array(
                    'scheme' => isset($scheme) ? $scheme->id : null,
                    'product_id' => $product->id,
                    'variant_id' => $variants[$key]->id,
                    'quantity' => $quantity[$key],
                    'amount' => $quantity[$key] * $applied_rate[$key]
                );
            } else {
                if (getClientSetting()->order_with_amt == 1) {
                    $scheme = Scheme::where('company_id', $this->company_id)
                        ->where('start_date', '<=', Carbon::today())
                        ->where('end_date', '>=', Carbon::today())
                        ->where('scheme_type_id', 1)
                        ->whereJsonContains('parties', $party_id)
                        ->whereJsonContains('product_id', $product->id)
                        ->first();
                } else {
                    $scheme = Scheme::where('company_id', $this->company_id)
                        ->where('start_date', '<=', Carbon::today())
                        ->where('end_date', '>=', Carbon::today())
                        ->where('status', 1)
                        ->whereJsonContains('parties', $party_id)
                        ->whereJsonContains('product_id', $product->id)
                        ->first();
                }
                $schemes[] = array(
                    'scheme' => isset($scheme) ? $scheme->id : null,
                    'product_id' => $product->id,
                    'quantity' => $quantity[$key],
                    'amount' => $quantity[$key] * $applied_rate[$key]
                );

            }
        }

        $product_with_same_scheme = collect($schemes)->groupBy('scheme')->map(function ($scheme) {
            if (count($scheme) > 1) {
                $data = collect($scheme)->first();
                $data['quantity'] = collect($scheme)->sum('quantity');
                $data['amount'] = collect($scheme)->sum('amount');
                return $data;
            } else return array_collapse($scheme);
        })->toArray();

        $validated_scheme = array();

        foreach ($product_with_same_scheme as $scheme) {
            $scheme_type = Scheme::find($scheme['scheme']);
            if (isset($scheme_type)) {
                if ($scheme_type->scheme_type_id == 1 && $scheme['quantity'] >= $scheme_type->qty) {
                    $minimun_quantity = $scheme_type->qty;
                    $offered_qty = $scheme_type->offered_qty;
                    $times_offered = intval(floor($scheme['quantity'] / $minimun_quantity));
                    $validated_scheme[] = array(
                        'scheme_type' => $scheme_type,
                        'discount' => null,
                        'free_items' => $offered_qty * $times_offered,
                    );
                } else if ($scheme_type->scheme_type_id == 2 && $scheme['quantity'] >= $scheme_type->qty) {
                    $discount_percentage = $scheme_type->percentage_off;
                    $discount = $scheme['amount'] * ($discount_percentage / 100);
                    $validated_scheme[] = array(
                        'scheme_type' => $scheme_type,
                        'discount' => $discount,
                        'free_items' => null,
                    );
                } else if ($scheme_type->scheme_type_id == 3 && $scheme['quantity'] >= $scheme_type->qty) {
                    $quantity = $scheme['quantity'] / $scheme_type->qty;
                    $validated_scheme[] = array(
                        'scheme_type' => $scheme_type,
                        'discount' => $scheme_type->discount_amount * floor($quantity),
                        'free_items' => null,
                    );
                } else if ($scheme_type->scheme_type_id == 4 && $scheme['amount'] >= $scheme_type->amount) {
                    $discount_percentage = $scheme_type->percentage_off;
                    $discount = $scheme['amount'] * ($discount_percentage / 100);
                    $validated_scheme[] = array(
                        'scheme_type' => $scheme_type,
                        'discount' => $discount,
                        'free_items' => null,
                    );

                } else if ($scheme_type->scheme_type_id == 5 && $scheme['amount'] >= $scheme_type->amount) {
                    $quantity = $scheme['amount'] / $scheme_type->amount;
                    $validated_scheme[] = array(
                        'scheme_type' => $scheme_type,
                        'discount' => $scheme_type->discount_amount * floor($quantity),
                        'free_items' => null,
                    );
                } else if ($scheme_type->scheme_type_id == 6 && $scheme['amount'] >= $scheme_type->amount) {
                    $free_items = intval(floor($scheme['amount'] / $scheme_type->amount));
                    $validated_scheme[] = array(
                        'scheme_type' => $scheme_type,
                        'discount' => null,
                        'free_items' => $scheme_type->offered_qty * $free_items,
                    );
                }
            }
        }

        return response()->json([
            'html' => view('company.schemes.ajaxShowAvailableScheme', compact('validated_scheme', 'applied_schemes'))->render(),
            'count' => count($validated_scheme)
        ]);
    }

    public function applySchemes(Request $request)
    {
        if (is_null($request->scheme)) {
            return 0;
        }
        $schemes = $request->scheme;
        $discounts = $request->discount;
        $freeItems = $request->freeItem;

        $scheme_information = array();

        foreach ($schemes as $key => $scheme) {
            $scheme = Scheme::find($scheme);

            $product = Product::withTrashed()->find($scheme->offered_product);
            $variant = ProductVariant::withTrashed()->where('product_id', $scheme->offered_product)->find($scheme->offered_product_variant);
            $unit = isset($variant) ? UnitTypes::withTrashed()->find($variant->unit) : UnitTypes::withTrashed()->find($product->unit);

            $scheme_information[] = array(
                'id' => $scheme->id,
                'name' => $scheme->name,
                'scheme_type' => $scheme->scheme_type_id,
                'discount' => isset($discounts[$key]) ? $discounts[$key] : ' ',
                'freeItem' => isset($freeItems[$key]) ? $freeItems[$key] : ' ',
                'product_name' => $product ? $product->product_name : '',
                'product_variant' => $variant ? $variant->variant : '',
                'unit' => isset($unit) ? $unit->symbol : ''
            );
        }
        return response()->json([
            'schemes' => $scheme_information
        ]);
    }

    private function uploadImage($image)
    {
        if (isset($image)) {
            $currentdate = Carbon::now()->toDateString();
            $imagename = $currentdate . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('uploads/party/schema')) {
                Storage::disk('public')->makeDirectory('uploads/party/schema');
            }
            $coverimage = Image::make($image)->stream();
            Storage::disk('public')->put('uploads/party/schema/' . $imagename, $coverimage);

            return $imagename;
        }

    }

    //this function checks if a key exists in multi-dimensional array
    private function findKey($array, $keySearch)
    {
        foreach ($array as $key => $item) {
            if ($key == $keySearch) {
                return true;
            } elseif (is_array($item) && $this->findKey($item, $keySearch)) {
                return true;
            }
        }
        return false;
    }


}
