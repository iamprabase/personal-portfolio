<?php

use App\Module;
use Illuminate\Database\Seeder;
use App\ModuleFunctionalitySetup;

class ModuleFunctionalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $modules = array();
      Module::insert($modules);

      $module_sub_functionality = array(
                                  'product' => array(
                                    'party_wise_rate_setup' => array(
                                      'type' => 'Radio',
                                      'values' => array(
                                        0 => 'No',
                                        1 => 'Yes',
                                      )
                                    ),
                                    'unit_conversion' => array(
                                      'type' => 'Radio',
                                      'values' => array(
                                        0 => 'No',
                                        1 => 'Yes',
                                      )
                                    ),
                                    'variant_attributes' => array(
                                      'type' => 'Radio',
                                      'values' => array(
                                        0 => 'No',
                                        1 => 'Yes',
                                      )
                                    ),
                                  ),
                                  'orders' => array(
                                    'order_prefix' => array(
                                      'type' => 'TextBox',
                                      'values' => "Placeholder"
                                    ),
                                    'order_with_authsign' => array(
                                      'type' => 'Radio',
                                      'values' => array(
                                        0 => 'No',
                                        1 => 'Yes',
                                      )
                                    ),
                                    'order_approval' => array(
                                      'type' => 'Radio',
                                      'values' => array(
                                        0 => 'No',
                                        1 => 'Yes',
                                      )
                                    ),
                                    'order_with_amt' => array(
                                      'type' => 'Radio',
                                      'values' => array(
                                        0 => 'No',
                                        1 => 'Yes',
                                      )
                                    ),
                                    'non_zero_discount' => array(
                                      'type' => 'Radio',
                                      'values' => array(
                                        0 => 'No',
                                        1 => 'Yes',
                                      )
                                    ),
                                    'product_level_discount' => array(
                                      'type' => 'Radio',
                                      'values' => array(
                                        0 => 'No',
                                        1 => 'Yes',
                                      )
                                    ),
                                    'product_level_tax' => array(
                                      'type' => 'Radio',
                                      'values' => array(
                                        0 => 'No',
                                        1 => 'Yes',
                                      )
                                    ),
                                  )
                                );
      foreach($module_sub_functionality as $module_key => $functionality){
        $module = Module::whereField($module_key)->first();
        if($module){
          foreach($functionality as $key => $value){
            ModuleFunctionalitySetup::create([
              'module_id' => $module->id,
              'key' => $key,
              'value' => json_encode($value['values'], JSON_FORCE_OBJECT),
              'type' => $value['type']
            ]);
          }
        }
      }
    }
}
