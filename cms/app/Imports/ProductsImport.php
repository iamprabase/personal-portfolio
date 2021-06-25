<?php

namespace App\Imports;

use App\Product;
use App\RateSetup;
use App\RateDetail;
use App\Brand;
use App\Category;
use App\UnitTypes;
use App\ProductVariant;
use Maatwebsite\Excel\Concerns\ToModel;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;


class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
  use Importable;
  /**
  * @param array $row
  *
  * @return \Illuminate\Database\Eloquent\Model|null
  */
  public function model(array $row)
  {
    $company_id = config('settings.company_id');
    $is_customrate = config('settings.party_wise_rate_setup');
    $categoryid='';
    $brandid=''; 
    $details='';
    $variantname='';
    $shortdesc=''; 
    $variantattr='';
    $productcode='';

    //dd($row);
    if($is_customrate==1){
    foreach($row as $key=>$value){
      if(strpos($key, 'customrate_') !== false){
        $ratename=trim(str_replace('customrate_','',$key));
        $checkratename = RateSetup::where('company_id',$company_id)->where('name',$ratename)->first();
        if($checkratename){
          $customrateid=$checkratename->id;
        }else{
          $newcustomrate = new RateSetup;
          $newcustomrate->name = $ratename;
          $newcustomrate->company_id = $company_id;
          $newcustomrate->save();
          $customrateid=$newcustomrate->id;
        }
         $customratenames[$key]=$customrateid;
       } 
    }

   // dd($customratenames);
    }
    if(isset($row['category'])){
      if($row['category']!=''){
        $category = Category::where('company_id',$company_id)->where('name',$row['category'])->first();
        if($category){
          $categoryid=$category->id;
        }else{
          $categoryadd = new Category;
          $categoryadd->name = $row['category'];
          $categoryadd->company_id = $company_id;
          $categoryadd->status = 'Active';
          $categoryadd->save();
          $categoryid=$categoryadd->id;
        }
      }
    }



    if(isset($row['brand'])){
      if($row['brand']!=''){
        $brand = Brand::where('company_id',$company_id)->where('name',$row['brand'])->first();
        if($brand){
          $brandid=$brand->id;
        }else{
          $brandadd = new Brand;
          $brandadd->name = $row['brand'];
          $brandadd->company_id = $company_id;
          $brandadd->status = 'Active';
          $brandadd->save();
          $brandid=$brandadd->id;
        }
      }
      }

    if(isset($row['unit'])){
      if($row['unit']!=''){
        $unit = UnitTypes::where('company_id',$company_id)->where('name',$row['unit'])->first();
        if($unit){
          $unitid=$unit->id;
        }else{
          $unitadd = new UnitTypes;
          $unitadd->name = $row['unit'];
          $unitadd->symbol = $row['unit'];
          $unitadd->company_id = $company_id;
          $unitadd->status = 'Active';
          $unitadd->save();
          $unitid=$unitadd->id;
        }
      }
    }

    if(isset($row['productcode'])){
        // if(!preg_match("/^[\w\s-]+$/", $row['productcode'])){
        //     $row['productcode'] = preg_replace("/[\/\&%#@.\$]/", "-", $row['productcode']);
        //     $row['productcode'] = preg_replace ("/[\"\']/", "-", $row['productcode']);
        //   }
    $productcode=trim(preg_replace('/[^a-zA-Z0-9_.]/', '-', str_replace(')','',$row['productcode'])));
    }

    if(isset($row['details'])){
    $details=$row['details'];
    }
    if(isset($row['variantname'])){
      $variantname=$row['variantname'];
    }
    if(isset($row['shortdesc'])){
      $shortdesc=$row['shortdesc'];
    }
    if(isset($row['rate'])){
      $rate=$row['rate'];
    }

    //dd($row);

    if(isset($row['productname'])){
      $product = Product::where('company_id',$company_id)->where('product_name',$row['productname'])->first();
      if($product)
      {
        //dd(1);
        $productUpdate = Product::find($product->id);
        $productUpdate->category_id = $categoryid;
        $productUpdate->brand = $brandid;
        $productUpdate->mrp = $rate;
        $productUpdate->unit = $unitid;
        $productUpdate->details = $details;
        $productUpdate->short_desc = $shortdesc;
        $productUpdate->save();


        if(isset($row['variantname']) && $row['variantname']!='')
        {
          $productid=$product->id;
          if(isset($row['variantattribute']) && $row['variantattribute']!=''){
            $variantattr=explode(',',$row['variantattribute']);
          }

          $productvarientcheck = ProductVariant::where('company_id',$company_id)->where('product_id',$productid)->where('variant',$variantname)->first();
          
          if($productvarientcheck){
            $product_varient_id=$productvarientcheck->id;
            $ProductVariantUpdate = ProductVariant::find($product_varient_id);
            $ProductVariantUpdate->mrp = $rate;
            $ProductVariantUpdate->unit = $unitid;
            $ProductVariantUpdate->short_desc = $shortdesc;
            $ProductVariantUpdate->save();
            if($is_customrate==1){
            foreach($customratenames as $cname=>$cid){
            if(isset($row[$cname]) && $row[$cname]!=''){
              // dd('i am here');
              $customrateexist = DB::table('rate_details')
                          ->where('rate_id', $cid)
                          ->where('product_id', $productid)
                          ->where('variant_id', $product_varient_id)
                          ->first();
              if($customrateexist){
                $customratesupdated=DB::table('rate_details')
                      ->where('rate_id', $cid)
                      ->where('product_id', $productid)
                      ->where('variant_id', $product_varient_id)
                      ->update(['mrp' => $row[$cname]]); 
              }else{
                $customratesupdated=DB::table('rate_details')->insert([
                  'rate_id' => $cid,
                  'product_id' => $productid,
                  'variant_id' => $product_varient_id,
                  'mrp' => $row[$cname]
                ]);
              }
            }
          }
        }
           // $this->addCustomRates($row,$productid,$product_varient_id);
          
            return $ProductVariantUpdate;

          }else{

          $newprodvarient = new ProductVariant;
          $newprodvarient->company_id = $company_id;
          $newprodvarient->product_id = $productid;
          $newprodvarient->variant = $variantname;
          $newprodvarient->variant_colors = json_encode($variantattr);
          $newprodvarient->mrp = $rate;
          $newprodvarient->unit = $unitid;
          $newprodvarient->short_desc = $shortdesc;
          $newprodvarient->moq = 1;
          $newprodvarient->app_visibility = 1;
          $newprodvarient->save();
          $product_varient_id=$newprodvarient->id;

          //return new ProductVariant(['company_id' =>$company_id, 'product_id' => $productid, 'variant' => $variantname, 'variant_colors' => json_encode($variantattr),'mrp' => $rate, 'unit' => $unitid, 'short_desc' => $shortdesc, 'moq' => 1, 'app_visibility' => 1]);
          if($is_customrate==1){
          foreach($customratenames as $cname=>$cid){
            if(isset($row[$cname]) && $row[$cname]!=''){
              // dd('i am here');
              $customrateexist = DB::table('rate_details')
                          ->where('rate_id', $cid)
                          ->where('product_id', $productid)
                          ->where('variant_id', $product_varient_id)
                          ->first();
              if($customrateexist){
                $customratesupdated=DB::table('rate_details')
                      ->where('rate_id', $cid)
                      ->where('product_id', $productid)
                      ->where('variant_id', $product_varient_id)
                      ->update(['mrp' => $row[$cname]]); 
              }else{
                $customratesupdated=DB::table('rate_details')->insert([
                  'rate_id' => $cid,
                  'product_id' => $productid,
                  'variant_id' => $product_varient_id,
                  'mrp' => $row[$cname]
                ]);
              }
            }
          }
        }
          
          //$this->addCustomRates($row,$productid,$product_varient_id);
          
            return $newprodvarient;
          }

        } else{
           // dd('i am here');
          if($is_customrate==1){
          foreach($customratenames as $cname=>$cid){
            $productid=$product->id;
            if(isset($row[$cname]) && $row[$cname]!=''){
             
              $customrateexist = DB::table('rate_details')
                          ->where('rate_id', $cid)
                          ->where('product_id', $productid)
                          ->where('variant_id', Null)
                          ->first();
              if($customrateexist){
                $customratesupdated=DB::table('rate_details')
                      ->where('rate_id', $cid)
                      ->where('product_id', $productid)
                      ->where('variant_id', Null)
                      ->update(['mrp' => $row[$cname]]); 
              }else{
                $customratesupdated=DB::table('rate_details')->insert([
                  'rate_id' => $cid,
                  'product_id' => $productid,
                  'variant_id' => Null,
                  'mrp' => $row[$cname]
                ]);
              }
            }
          }
        }
          return $productUpdate;
          //return $this->addCustomRates($row,$productid);
        }
        
      }else{ 
       // dd(2);
        if(isset($row['variantname']) && $row['variantname']!=''){       
          $productadd = new Product;
          $productadd->product_name = $row['productname'];
          $productadd->company_id = $company_id;
          $productadd->category_id = $categoryid;
          $productadd->brand = $brandid;
          $productadd->product_code=$productcode;
          $productadd->mrp = $rate;
          $productadd->unit = $unitid;
          $productadd->details = $details;
          $productadd->short_desc = $shortdesc;
          $productadd->variant_flag = 1;
          $productadd->star_product = 0;
          $productadd->moq = 1;
          $productadd->app_visibility = 1;
          $productadd->status = 'Active';
          $productadd->save();
          $productid=$productadd->id;
          if(isset($row['variantattribute']) && $row['variantattribute']!='')
          {
            $variantattr=explode(',',$row['variantattribute']);
          }
          $newprodvarient = new ProductVariant;
          $newprodvarient->company_id = $company_id;
          $newprodvarient->product_id = $productid;
          $newprodvarient->variant = $variantname;
          $newprodvarient->variant_colors = json_encode($variantattr);
          $newprodvarient->mrp = $rate;
          $newprodvarient->unit = $unitid;
          $newprodvarient->short_desc = $shortdesc;
          $newprodvarient->moq = 1;
          $newprodvarient->app_visibility = 1;
          $newprodvarient->save();
          $product_varient_id=$newprodvarient->id;

         // dd($product_varient_id);

          // return new ProductVariant(['company_id' =>$company_id, 'product_id' => $productid, 'variant' => $variantname, 'variant_colors' => json_encode($variantattr),'mrp' => $rate, 'unit' => $unitid, 'short_desc' => $shortdesc, 'moq' => 1, 'app_visibility' => 1]);
          if($is_customrate==1){
          foreach($customratenames as $cname=>$cid){
            if(isset($row[$cname]) && $row[$cname]!=''){
              // dd('i am here');
              $customrateexist = DB::table('rate_details')
                          ->where('rate_id', $cid)
                          ->where('product_id', $productid)
                          ->where('variant_id', $product_varient_id)
                          ->first();
              if($customrateexist){
                $customratesupdated=DB::table('rate_details')
                      ->where('rate_id', $cid)
                      ->where('product_id', $productid)
                      ->where('variant_id', $product_varient_id)
                      ->update(['mrp' => $row[$cname]]); 
              }else{
                $customratesupdated=DB::table('rate_details')->insert([
                  'rate_id' => $cid,
                  'product_id' => $productid,
                  'variant_id' => $product_varient_id,
                  'mrp' => $row[$cname]
                ]);
              }
            }
          }
        }

          return $newprodvarient;
        }else{
          $newproduct = new Product;
          $newproduct->company_id = $company_id;
          $newproduct->product_name = $row['productname'];
          $newproduct->product_code = $productcode;
          $newproduct->category_id = $categoryid;
          $newproduct->brand = $brandid;
          $newproduct->mrp = $rate;
          $newproduct->unit = $unitid;
          $newproduct->details = $details;
          $newproduct->short_desc = $shortdesc;
          $newproduct->variant_flag = 0;
          $newproduct->star_product = 0;
          $newproduct->moq = 1;
          $newproduct->app_visibility = 1;
          $newproduct->save();
          $productid=$newproduct->id;
          if($is_customrate==1){
          foreach($customratenames as $cname=>$cid){
            if(isset($row[$cname]) && $row[$cname]!=''){
              // dd('i am here');
              $customrateexist = DB::table('rate_details')
                          ->where('rate_id', $cid)
                          ->where('product_id', $productid)
                          ->where('variant_id', Null)
                          ->first();
              if($customrateexist){
                $customratesupdated=DB::table('rate_details')
                      ->where('rate_id', $cid)
                      ->where('product_id', $productid)
                      ->where('variant_id', Null)
                      ->update(['mrp' => $row[$cname]]); 
              }else{
                $customratesupdated=DB::table('rate_details')->insert([
                  'rate_id' => $cid,
                  'product_id' => $productid,
                  'variant_id' => Null,
                  'mrp' => $row[$cname]
                ]);
              }
            }
          }
        }
          
         // $this->addCustomRates($row,$productid);
          return $newproduct;
        } 
      }
    }
  }

  // public function addCustomRates($row,$productid,$product_varient_id=Null){

  //   dd($row);
    
  //   foreach($row as $key=>$value){
  //     if(strpos($key, 'customrate_') !== false){
  //       $ratename=trim(str_replace('customrate_','',$key));
  //       $checkratename = RateSetup::where('company_id',$company_id)->where('name',$ratename)->first();
  //       if($checkratename){
  //         $customrateid=$checkratename->id;
  //       }else{
  //         $newcustomrate = new RateSetup;
  //         $newcustomrate->name = $row['category'];
  //         $newcustomrate->company_id = $company_id;
  //         $newcustomrate->save();
  //         $customrateid=$newcustomrate->id;
  //       }
  //        $customratenames[$key]=$customrateid;
  //      } 
  //   }

  //   foreach($customratenames as $ckey=>$cname){
  //     if(isset($row[$cname]) && $row[$cname]!=''){
  //       $customrateexist = DB::table('rate_details')
  //                   ->where('rate_id', $ckey)
  //                   ->where('product_id', $productid)
  //                   ->where('variant_id', $product_varient_id)
  //                   ->first();
  //       if($customrateexist){
  //         $customratesupdated=DB::table('rate_details')
  //               ->where('rate_id', $ckey)
  //               ->where('product_id', $productid)
  //               ->where('variant_id', $product_varient_id)
  //               ->update(['mrp' => $row[$cname]]); 
  //       }else{
  //         $customratesupdated=DB::table('handles')->insert([
  //           'company_id' => $company_id,
  //           'rate_id' => $ckey,
  //           'product_id' => $productid,
  //           'variant_id' => $product_varient_id,
  //           'mrp' => $row[$cname]
  //         ]);
  //       }
  //     }
  //   }
  //   return $customratesupdated;
  // }


  public function rules(): array
    {
        return [
            'productname' => 'required',
            'rate' => 'required',
            'unit' => 'required',
        ];
    }

    
}
