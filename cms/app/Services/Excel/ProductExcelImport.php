<?php

namespace App\Services\Excel;

use App\Product;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;

/**
 * class ProductExcelImport
 * 
 * @package: App\Services\Excel
 * @author: Shahsank Jha <shashankj677@gmail.com>
 */


class ProductExcelImport implements ToArray {

  public function array(array $row) {
    // extending the maximum execution time
    ini_set('max_execution_time', '0');

    // extending the memory limit
    ini_set('memory_limit', '-1');

    // get company Id
    $companyId = config('settings.company_id');

    // initialize empty products array
    $products = [];

    // => instantiate the FetchDataForProduct class
    $fetchDataForProduct = new FetchDataForProduct($companyId);

    foreach ($row as $key=>$val){
      if ($key != 0) {
        $productName = $val[0];
        $categoryId = $fetchDataForProduct->getCategoryId($val[1]);
        $brandId = $fetchDataForProduct->getBrandId($val[2]);
        $unitId = $fetchDataForProduct->getUnitId($val[3]);
        $mrp = $val[4];

        array_push($products, array('company_id' => $companyId, 'product_name' => $productName, 'category_id' => $categoryId, 'brand' => $brandId, 'unit' => $unitId, 'mrp' => $mrp));
      }
    }
    try {
      // begin DB transaction
      DB::beginTransaction();

      // create multiple products
      Product::insert($products);

      // commit transaction
      DB::commit();
    } catch (\Exception $error) {
      dd($error->getMessage());
    }
  }

}
