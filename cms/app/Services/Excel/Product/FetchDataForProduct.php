<?php

namespace App\Services\Excel\Product;

use App\Brand;
use App\Category;
use App\UnitTypes;
use Illuminate\Support\Facades\DB;

/**
 * class FetchDataForProduct
 * 
 * @package: App\Services\Excel\Product
 * @author: Shahsank Jha <shashankj677@gmail.com>
 */

class FetchDataForProduct {

  public $companyId;

  public function __construct($companyId) {
    $this->companyId = $companyId;
  }

  public function getCategoryId($category) {
    // => get id and name of categories & convert the name to lowercase letters
    $categories = Category::companyId($this->companyId)->pluck('name', 'id')->toArray();
    $categories = array_map('strtolower', $categories);

    // define categoryId
      $categoryId = 0;

    if (in_array($category, $categories)) {
      // if exists extract the required key and assign it to categoryID
      $categoryId = array_search($category, $categories);
    } else {
      try {
        // begin database transaction
        DB::beginTransaction();

        // create a new category
        Category::create([
          'company_id' => $this->companyId,
          'name' => ucwords($category),
          'status' => "Active"
        ]);

        // commit the DB transaction
        DB::commit();

        // extract the id of the category which was created recently and assign it to categoryID
        $categoryId = Category::companyId($this->companyId)->latest()->first()->id;
      } catch (\Exception $error) {
        // rollback the DB transaction
        DB::rollback();
      }
    }

    return $categoryId;
  }

  public function getBrandId($brand) {
    // => get id and name of brands & convert the name to lowercase letters
    $brands = Brand::companyId($this->companyId)->pluck('name', 'id')->toArray();
    $brands = array_map('strtolower', $brands);

    if (in_array($brand, $brands)) {
      // if exists extract the required key and assign it to brandId
      $brandId = array_search($brand, $brands);
    } else {
      try {
        // begin database transaction
        DB::beginTransaction();

        // create a new brand
        Brand::create([
          'company_id' => $this->companyId,
          'name' => ucwords($brand),
          'status' => "Active"
        ]);

        // commit the DB transaction
        DB::commit();

        // extract the id of the brand which was created recently and assign it to brandId
        $brandId = Brand::companyId($this->companyId)->latest()->first()->id;
      } catch (\Exception $error) {
        // rollback the DB transaction
        DB::rollback();
      }
    }

    return $brandId;
  }

  public function getUnitId($unit) {
    // get id and name of units and convert the name to lowercase letter
    $units = UnitTypes::companyId($this->companyId)->pluck('name', 'id')->toArray();
    $units = array_map('strtolower', $units);

    if (in_array($unit, $units)) {
      // if exists extract the required key and assign it to the unitId
      $unitId = array_search($unit, $units);
    } else {
      try {
        // begin DB transaction
        DB::beginTransaction();

        // create a new unit
        UnitTypes::create([
          'company_id' => $this->companyId,
          'name' => ucwords($unit),
		      'symbol' => $unit,
          'status' => "Active"
        ]);

        // commit the DB transaction
        DB::commit();

        // extract the id of the unit which was created recently and assign it to unitId
        $unitId = UnitTypes::companyId($this->companyId)->latest()->first()->id;
      } catch (\Exception $error) {
        // rollback the DB transaction
        DB::rollback();
      }
    }

    return $unitId;
  }
 
}
