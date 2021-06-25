<?php

namespace App\Http\Controllers\API;

use App\OrderScheme;
use App\Product;
use App\ProductVariant;
use App\Scheme;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SchemeController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {

        $user = Auth::user();
        $company_id = $user->company_id;

        $schemes = Scheme::where('company_id', $company_id)->get();
        $schemes = $schemes->each(function ($scheme) {
            if ($scheme->start_date > Carbon::now() || $scheme->end_date < Carbon::now()->endOfDay()) {
                $scheme->status = 0;
            }
        });

        return response()->json([
            'schemes' => $schemes
        ]);
    }




    public function getAppliedSchemes()
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        return response()->json([
           'applied_schemes' => OrderScheme::where('company_id', $company_id)->get()
        ]);
    }

//these are not needed as schemes are also needed to calculated for offline
//    public function getAllAvailableSchemes(Request $request)
//    {
//        $products_name = $request->names;
//        $product_variants_name = $request->variant_names;
//        $quantity = $request->quantity;
//        $applied_rate = $request->applied_rate;
//
//        $products_information = array();
//
//        foreach ($products_name as $key => $name) {
//            if (!is_null($name)) {
//                $products_information[] = array(
//                    'name' => $name,
//                    'variant_name' => $product_variants_name[$key],
//                    'quantity' => intval($quantity[$key]),
//                    'applied_rate' => intval($applied_rate[$key])
//                );
//            }
//        }
//        //calculating quantity if same items are selected two times
//        $count_quantity = collect($products_information)->groupBy(['name', 'variant_name'])->map(function ($items) {
//            if (count($items) > 1) {
//                return $items->map(function ($data) {
//                    $item = collect($data)->first();
//                    $item['quantity'] = collect($data)->sum('quantity');
//                    $item['applied_rate'] = collect($data)->sum('applied_rate');
//                    return $item;
//                })->toArray();
//            } else {
//                $item = collect(array_collapse($items))->first();
//                $item['quantity'] = collect(array_collapse($items))->sum('quantity');
//                $item['applied_rate'] = collect(array_collapse($items))->sum('applied_rate');
//                return $item;
//            }
//        })->toArray();
//
//        //changing the level of array to same level
//        $new_array = array();
//        foreach ($count_quantity as $array) {
//            foreach ($array as $new) {
//                if (is_array($new)) {
//                    if (!in_array($new, $new_array)) {
//                        $new_array[] = $new;
//                    }
//                } else {
//                    if (!in_array($array, $new_array)) {
//                        $new_array[] = $array;
//                    }
//                }
//            }
//        }
//
//        $products = array();
//        $variants = array();
//        $applied_rate = array();
//        $quantity = array();
//        foreach ($new_array as $key => $values) {
//            $products[] = Product::where('product_name', $values['name'])->select('id', 'product_name', 'brand', 'mrp')->where('company_id', $this->company_id)->first();
//            $variants[] = !is_null($values['variant_name']) ? ProductVariant::where('variant', $values['variant_name'])->where('product_id', $products[$key]->id)->select('id', 'variant', 'mrp')->first() : null;
//            $quantity[] = $values['quantity'];
//            $applied_rate[] = $values['applied_rate'];
//        }
//
//        $schemes = array();
//        foreach ($products as $key => $product) {
//            if (!is_null($variants[$key])) {
//                $scheme = Scheme::where('company_id', $this->company_id)
//                    ->where('status', 1)
//                    ->where('start_date', '<=', Carbon::today())
//                    ->where('end_date', '>=', Carbon::today())
//                    ->whereJsonContains('product_id', $product->id)
//                    ->whereJsonContains('product_variant', [$product->id => $variants[$key]->id])
//                    ->first();
//
//
//                $schemes[] = array(
//                    'scheme' => isset($scheme) ? $scheme->id : null,
//                    'product_id' => $product->id,
//                    'variant_id' => $variants[$key]->id,
//                    'quantity' => $quantity[$key],
//                    'amount' => $quantity[$key] * $variants[$key]->mrp
//                );
//            } else {
//
//                $scheme = Scheme::where('company_id', $this->company_id)
//                    ->where('start_date', '<=', Carbon::today())
//                    ->where('end_date', '>=', Carbon::today())
//                    ->where('status', 1)
//                    ->whereJsonContains('product_id', $product->id)
//                    ->first();
//                $schemes[] = array(
//                    'scheme' => isset($scheme) ? $scheme->id : null,
//                    'product_id' => $product->id,
//                    'quantity' => $quantity[$key],
//                    'amount' => $quantity[$key] * $product->mrp
//                );
//
//            }
//        }
//
//        $product_with_same_scheme = collect($schemes)->groupBy('scheme')->map(function ($scheme) {
//            if (count($scheme) > 1) {
//                $data = collect($scheme)->first();
//                $data['quantity'] = collect($scheme)->sum('quantity');
//                $data['amount'] = collect($scheme)->sum('amount');
//                return $data;
//            } else return array_collapse($scheme);
//        })->toArray();
//
//        $validated_scheme = array();
//
//        foreach ($product_with_same_scheme as $scheme) {
//            $scheme_type = Scheme::find($scheme['scheme']);
//            if (isset($scheme_type)) {
//                if ($scheme_type->scheme_type_id == 1 && $scheme['quantity'] >= $scheme_type->qty) {
//                    $minimun_quantity = $scheme_type->qty;
//                    $offered_qty = $scheme_type->offered_qty;
//                    $times_offered = intval(floor($scheme['quantity'] / $minimun_quantity));
//                    $validated_scheme[] = array(
//                        'scheme_type' => $scheme_type,
//                        'discount' => null,
//                        'free_items' => $offered_qty * $times_offered,
//                    );
//                } else if ($scheme_type->scheme_type_id == 2 && $scheme['quantity'] >= $scheme_type->qty) {
//
//                    $discount_percentage = $scheme_type->percentage_off;
//                    $discount = $scheme['amount'] * ($discount_percentage / 100);
//                    $validated_scheme[] = array(
//                        'scheme_type' => $scheme_type,
//                        'discount' => intval($discount),
//                        'free_items' => null,
//                    );
//                } else if ($scheme_type->scheme_type_id == 3 && $scheme['quantity'] >= $scheme_type->qty) {
//                    $quantity = $scheme['quantity'] / $scheme_type->qty;
//                    $validated_scheme[] = array(
//                        'scheme_type' => $scheme_type,
//                        'discount' => intval($scheme_type->discount_amount) * floor($quantity),
//                        'free_items' => null,
//                    );
//                } else if ($scheme_type->scheme_type_id == 4 && $scheme['amount'] >= $scheme_type->amount) {
//                    $discount_percentage = $scheme_type->percentage_off;
//                    $discount = $scheme['amount'] * ($discount_percentage / 100);
//                    $validated_scheme[] = array(
//                        'scheme_type' => $scheme_type,
//                        'discount' => intval($discount),
//                        'free_items' => null,
//                    );
//
//                } else if ($scheme_type->scheme_type_id == 5 && $scheme['amount'] >= $scheme_type->amount) {
//                    $validated_scheme[] = array(
//                        'scheme_type' => $scheme_type,
//                        'discount' => $scheme_type->discount_amount,
//                        'free_items' => null,
//                    );
//                } else if ($scheme_type->scheme_type_id == 6 && $scheme['amount'] >= $scheme_type->amount) {
//                    $free_items = intval(floor($scheme['amount'] / $scheme_type->amount));
//                    $validated_scheme[] = array(
//                        'scheme_type' => $scheme_type,
//                        'discount' => null,
//                        'free_items' => $scheme_type->offered_qty * $free_items,
//                    );
//                }
//            }
//        }
//
//        return response()->json([
//            'schemes' => $validated_scheme,
//            'count' => count($validated_scheme)
//        ]);
//    }
//
//
//    public function applySchemes(Request $request)
//    {
//        $schemes = $request->scheme;
//        $discounts = $request->discount;
//        $freeItems = $request->freeItem;
//
//        $scheme_information = array();
//
//        foreach ($schemes as $key => $scheme) {
//            $scheme = Scheme::find($scheme);
//            $scheme_information[] = array(
//                'id' => $scheme->id,
//                'name' => $scheme->name,
//                'scheme_type' => $scheme->scheme_type_id,
//                'discount' => isset($discounts[$key]) ? $discounts[$key] : ' ',
//                'freeItem' => isset($freeItems[$key]) ? $freeItems[$key] : ' ',
//                'product_name' => isset($scheme->offered_product) ? Product::find($scheme->offered_product)->product_name : ' ',
//                'product_variant' => isset($scheme->offered_product_variant) ? Product::where('product_id', $scheme->offered_product)->find($scheme->offered_product_variant)->variant : ' ',
//            );
//        }
//        return response()->json([
//            'schemes' => $scheme_information
//        ]);
//    }


}
