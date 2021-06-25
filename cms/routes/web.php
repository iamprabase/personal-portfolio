<?php

if (App::environment('production')) {

    URL::forceScheme('https');

}

 

 
 
 





// Route::group(

//   [

//       'domain' => '{subdomain}.' . config('app.domain'),

//       'as' => 'company.'

//   ], function () {

//     Route::group(

//       [

//           'prefix' => 'admin',

//           'as' => 'admin.'

//       ],

//       function () {

//         Route::get('/reports/partydetailsreport','Company\Admin\ReportNewController@partyDetailsReport')->name('partydetailsreport');

//         Route::post('/reports/generatepartydetailsreport','Company\Admin\ReportNewController@genPartyDetailsReport')->name('generatepartydetailsreport');

//         Route::post('/reports/generatepartydetailsreportdt','Company\Admin\ReportNewController@genPartyDetailsReportDT')->name('generatepartydetailsreportdt');



//         Route::get('/reports/employeedetailsreport','Company\Admin\ReportNewController@employeeDetailsReport')->name('employeedetailsreport');

//         Route::post('/reports/generateemployeedetailsreport','Company\Admin\ReportNewController@genEmployeeDetailsReport')->name('generateemployeedetailsreport');

//         Route::post('/reports/generateemployeedetailsreportdt','Company\Admin\ReportNewController@genEmployeeDetailsReportDT')->name('generateemployeedetailsreportdt');











//     });

// });























/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/

/*API Start*/

Route::post('/api/testPath', 'ApiController@testPath');

Route::post('/api/test', 'ApiController@test');

Route::get('/api/test', 'ApiController@test');

Route::get('/test', 'TestController@index');





Route::post('/api/login', 'ApiController@login');

Route::post('/api/mobileLogin', 'ApiController@mobileLogin');

Route::post('/api/logoutPreviousDevice', 'ApiController@logoutPreviousDevice');

Route::post('/api/getMobileAppVersionCode', 'ApiController@getMobileAppVersionCode');



Route::post('/api/fetchCommonData', 'ApiController@fetchCommonData');

Route::post('/api/sync', 'ApiController@sync');



Route::post('/api/fetchProducts', 'ApiController@fetchProducts');



Route::post('/api/fetchLocation', 'ApiController@fetchLocation');

Route::post('/api/saveLocation', 'ApiController@saveLocation');

Route::post('/api/syncLocations', 'ApiController@syncLocations');



Route::post('/api/fetchAttendance', 'ApiController@fetchAttendance');

Route::post('/api/saveAttendance', 'ApiController@saveAttendance');

Route::post('/api/syncAttendances', 'ApiController@syncAttendances');



Route::post('/api/fetchLeave', 'ApiController@fetchLeave');

Route::post('/api/saveLeave', 'ApiController@saveLeave');

Route::post('/api/deleteLeave', 'ApiController@deleteLeave');

Route::post('/api/syncLeave', 'ApiController@syncLeave');





Route::post('/api/fetchClients', 'ApiController@fetchClients');

Route::post('/api/saveClient', 'ApiController@saveClient');

Route::post('/api/syncClients', 'ApiController@syncClients');



Route::post('/api/saveUpload', 'ApiController@saveUpload');



Route::post('/api/fetchCollection', 'ApiController@fetchCollection');

Route::post('/api/saveCollection', 'ApiController@saveCollection');

Route::post('/api/syncCollection', 'ApiController@syncCollection');



Route::post('/api/fetchMeeting', 'ApiController@fetchMeeting');

Route::post('/api/saveMeeting', 'ApiController@saveMeeting');

Route::post('/api/syncMeeting', 'ApiController@syncMeeting');



Route::post('/api/fetchExpense', 'ApiController@fetchExpense');

Route::post('/api/saveExpense', 'ApiController@saveExpense');

Route::post('/api/syncExpense', 'ApiController@syncExpense');



Route::post('/api/fetchOrders', 'ApiController@fetchOrders');

Route::post('/api/saveOrder', 'ApiController@saveOrder');

Route::post('/api/syncOrders', 'ApiController@syncOrders');



Route::post('/api/fetchTask', 'ApiController@fetchTask');

Route::post('/api/saveTask', 'ApiController@saveTask');

Route::post('/api/syncTask', 'ApiController@syncTask');



Route::post('/api/fetchNoOrder', 'ApiController@fetchNoOrder');

Route::post('/api/saveNoOrder', 'ApiController@saveNoOrder');

Route::post('/api/syncNoOrder', 'ApiController@syncNoOrder');





Route::post('/api/fetchHolidays', 'ApiController@fetchHolidays');



//API for client settings

Route::post('/api/fetchClientSetting', 'ApiController@fetchClientSetting');



//API routes for Beats, Beatplans

Route::post('/api/fetchBeatplans', 'ApiController@fetchBeatplans');

Route::post('/api/fetchBeats', 'ApiController@fetchBeats');

Route::post('/api/fetchBeatsParties', 'ApiController@fetchBeatsParties');

Route::post('/api/saveBeatplan', 'ApiController@saveBeatplan');

Route::post('/api/syncBeatplan', 'ApiController@syncBeatplan');



Route::post('/api/fetchTourPlans', 'ApiController@fetchTourPlans');

Route::post('/api/saveTourPlans', 'ApiController@saveTourPlans');

Route::post('/api/syncTourPlans', 'ApiController@syncTourPlans');



//API routes for stock,stock_details,returns,order_fulfillments

Route::post('/api/saveStock', 'ApiController@saveStock');

Route::post('/api/syncStock', 'ApiController@syncStock');

Route::post('/api/fetchStock', 'ApiController@fetchStock');

Route::post('/api/fetchStockDetails', 'ApiController@fetchStockDetails');

Route::post('/api/fetchreturns', 'ApiController@fetchReturns');

Route::post('/api/fetchreturnreasons', 'ApiController@fetchReturnReason');

Route::post('/api/savereturns', 'ApiController@saveReturns');

Route::post('/api/syncreturns', 'ApiController@syncReturns');

Route::post('/api/fetchdayremarks', 'ApiController@fetchDayRemark');

Route::post('/api/savedayremarks', 'ApiController@saveDayRemark');

Route::post('/api/syncdayremarks', 'ApiController@syncDayRemark');

Route::post('/api/fetchOrderFulFillments', 'ApiController@fetchOrderFulFillments');





//API routes activites, activities type and activity priority

Route::post('/api/fetchActivities', 'ApiController@fetchActivities');

Route::post('/api/saveActivity', 'ApiController@saveActivity');

Route::post('/api/deleteActivity', 'ApiController@deleteActivity');

Route::post('/api/fetchActivityTypes', 'ApiController@fetchActivityTypes');

Route::post('/api/fetchActivityPriorities', 'ApiController@fetchActivityPriorities');

Route::post('/api/syncActivities', 'ApiController@syncActivities');



//API Employees

// Route::post('/api/fetchEmployees', 'ApiController@fetchEmployees');



Route::post('/api/fetchPartyFormDependentData', 'ApiController@fetchPartyFormDependentData');

Route::post('/api/fetchCollectionFormDependentData', 'ApiController@fetchCollectionFormDependentData');

Route::post('/api/fetchOrderFormDependentData', 'ApiController@fetchOrderFormDependentData');





Route::post('/api/fetchCollateralFiles', 'ApiController@fetchCollateralFiles');



/*API END*/



/**

 * Api Routes for analytics

 */



// ** APIS for employees tab



Route::get('/api/count-parameters', 'Company\Admin\AnalyticController@countParameters')->name('count-parameters');

Route::get('/api/get-no-of-orders', 'Company\Admin\AnalyticController@getNoOfOrders')->name('get-no-of-orders');

Route::get('/api/get-order-value', 'Company\Admin\AnalyticController@getOrderValue')->name('get-order-value');

Route::get('/api/get-collection-amount', 'Company\Admin\AnalyticController@getCollectionAmount')->name('get-collection-amount');

Route::get('/api/count-beat-parameters', 'Company\Admin\AnalyticController@countBeatParameters')->name('count-beat-parameters');

Route::get('/api/top-parties', 'Company\Admin\AnalyticController@topParties')->name('top-parties');



// ** APIS for parties tab

Route::get('/api/count-parties-parameters', 'Company\Admin\AnalyticController@countPartiesParameters')->name('count-parties-parameters');

Route::get('/api/get-no-of-orders-for-parties', 'Company\Admin\AnalyticController@getNoOfOrdersForParties')->name('get-no-of-orders-for-parties');

Route::get('/api/get-order-value-for-parties', 'Company\Admin\AnalyticController@getOrderValueForParties')->name('get-order-value-for-parties');

Route::get('/api/get-collection-amount-for-parties', 'Company\Admin\AnalyticController@getCollectionAmountForParties')->name('get-collection-amount-for-parties');



Route::get('/api/get-employee-first-date', 'Company\Admin\AnalyticController@getEmployeeFirstDate')->name('get-employee-first-date');

Route::get('/api/get-client-first-date', 'Company\Admin\AnalyticController@getClientFirstDate')->name('get-client-first-date');



/*API END*/



Route::get('/dependent-dropdown', 'CommonController@index');

Route::get('/get-state-list', 'CommonController@getStateList');

Route::get('/get-city-list', 'CommonController@getCityList');





















/* GENERAL APP : LOGIN, REGISTRATION AND SIMPLE DASHBOARD*/

Route::group(

    [

        'domain' => 'app.' . config('app.domain'),

        'as' => 'app.'

    ],

    function () {

        Route::get('/', 'Auth\LoginController@showLoginForm')->name('login');



        Route::get('/create-visit-purpose', 'Admin\CompanyController@createVisitPurpose');



        Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');

        Route::post('/login', 'Auth\LoginController@handleLogin')->name('login.post');

        Route::get('/registration', 'Auth\RegistrationController@showRegistrationForm')->name('registration');

        Route::post('/registration', 'Auth\RegistrationController@handleRegistration')->name('registration.post');

        Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

        Route::get('/home', 'DashboardController@home')->name('home');

        Route::get('/changePassword', 'DashboardController@changePassword')->name('password.change');

        Route::post('/updatePassword', 'DashboardController@updatePassword')->name('update.password.change');



        Route::group(

            [

                'domain' => 'app.' . config('app.domain'),

                'middleware' => ['auth', 'app.is_admin'],

            ],

            function () {



                //  Route::get('/', 'DashboardController@home')->name('home');



                Route::get('/employeetouser', 'Admin\DashboardController@emptouser')->name('employeetouser');

                Route::get('/managertouser', 'Admin\DashboardController@mgrtouser')->name('managertouser');

                // Settings



                Route::get('/setting', 'Admin\SettingController@index')->name('setting');

                Route::get('setting/edit', 'Admin\SettingController@edit')->name('setting.edit');

                Route::patch('setting/{id}', 'Admin\SettingController@update')->name('setting.update');

                Route::delete('/setting/{id}', 'Admin\SettingController@destroy')->name('setting.destroy');



                Route::get('/setting/states/get/{id}', 'Admin\SettingController@getStates');

                Route::get('/setting/cities/get/{id}', 'Admin\SettingController@getCities');





                //Plans

                Route::get('/plan', 'Admin\PlanController@index')->name('plan');

                Route::get('/plan/create', 'Admin\PlanController@create')->name('plan.create');

                Route::post('/plan/create', 'Admin\PlanController@store')->name('plan.store');

                Route::get('/plan/{id}', 'Admin\PlanController@show')->name('plan.show');

                Route::get('plan/{id}/edit', 'Admin\PlanController@edit')->name('plan.edit');

                Route::patch('plan/{id}', 'Admin\PlanController@update')->name('plan.update');

                Route::delete('/plan/{id}', 'Admin\PlanController@destroy')->name('plan.destroy');

                

                // Custom Company Plan

                Route::get('/custom-plans', 'Admin\CustomPlanController@index')->name('custom-plan.index');

                Route::get('/custom-plans/create', 'Admin\CustomPlanController@create')->name('custom-plan.create');

                Route::post('/custom-plans/store', 'Admin\CustomPlanController@store')->name('custom-plan.store');

                Route::get('/custom-plans/{id}', 'Admin\CustomPlanController@show')->name('custom-plan.show');

                Route::get('/custom-plans/{id}/edit', 'Admin\CustomPlanController@edit')->name('custom-plan.edit');

                Route::patch('/custom-plans/{id}', 'Admin\CustomPlanController@update')->name('custom-plan.update');

                Route::delete('/custom-plans/{id}', 'Admin\CustomPlanController@destroy')->name('custom-plan.destroy');



                // Subscription

                Route::get('/subscriptions', 'Admin\SubscriptionController@index')->name('subscription.index');

                Route::get('/subscriptions/create', 'Admin\SubscriptionController@create')->name('subscription.create');

                Route::post('/subscriptions/store', 'Admin\SubscriptionController@store')->name('subscription.store');

                Route::get('/subscriptions/{id}', 'Admin\SubscriptionController@show')->name('subscription.show');

                Route::get('/subscriptions/{id}/edit', 'Admin\SubscriptionController@edit')->name('subscription.edit');

                Route::patch('/subscriptions/{id}', 'Admin\SubscriptionController@update')->name('subscription.update');

                Route::delete('/subscriptions/{id}', 'Admin\SubscriptionController@destroy')->name('subscription.destroy');

                

                //Company

                Route::get('/company', 'Admin\CompanyController@index')->name('company');

                Route::get('/company/fetchrecords', 'Admin\CompanyController@dataTable')->name('company.fetchrecords');

                Route::post('/companyupdatestatus/generate-mail-otp', 'Admin\CompanyController@generateAndMailOTP')->name('company.generateAndMailOTP');

                Route::post('/companyupdatestatus/{id}', 'Admin\CompanyController@updateStatus')->name('company.updatestatus');

                Route::post('/setting/updateModule/{id}', 'Admin\CompanyController@updateModule')->name('setting.updateModule');

                Route::post('/setting/getPlanModules/{id}', 'Admin\CompanyController@getPlanModules')->name('setting.getPlanModules');

                Route::post('/setting/changePlan/{id}', 'Admin\CompanyController@changePlan')->name('setting.changePlan');

                Route::get('/company/empdownload/{id}', 'Admin\CompanyController@empdownload')->name('company.empdownload');

                Route::post('/company/notes/{id}', 'Admin\CompanyController@notes')->name('company.notes');

                Route::get('/company/create', 'Admin\CompanyController@create')->name('company.create');

                Route::post('/company/create', 'Admin\CompanyController@store')->name('company.store');

                Route::get('/company/{id}', 'Admin\CompanyController@show')->name('company.show');

                Route::get('company/{id}/edit', 'Admin\CompanyController@edit')->name('company.edit');

                Route::patch('company/{id}', 'Admin\CompanyController@update')->name('company.update');

                Route::get('company/setting/{id}', 'Admin\CompanyController@settings')->name('company.setting');

                Route::post('company/setting/{id}/updatetax', 'Admin\CompanyController@updateTax')->name('company.updateTax');

                Route::post('company/setting/{id}/updateflag', 'Admin\CompanyController@updateDefaultFlag')->name('company.updateDefaultFlag');

                Route::patch('company/setting/profile/{id}', 'Admin\CompanyController@updateProfile')->name('company.setting.updateProfile');

                Route::patch('company/setting/layout/{id}', 'Admin\CompanyController@updateLayout')->name('company.setting.updateLayout');

                Route::patch('company/setting/email/{id}', 'Admin\CompanyController@updateEmail')->name('company.setting.updateEmail');

                Route::patch('company/setting/other/{id}', 'Admin\CompanyController@updateOther')->name('company.setting.updateOther');

                Route::post('company/setting/removeTax', 'Admin\CompanyController@removeTax')->name('company.setting.removeTax');

                Route::get('company/settings/editMarketArea/{id}', 'Admin\CompanyController@editMarketArea')->name('company.setting.editMarketArea');

                Route::get('company/settings/removeMarketArea/{id}', 'Admin\CompanyController@removeMarketArea')->name('company.setting.removeMarketArea');



                Route::post('company/setting/addpartytype', 'Admin\CompanyController@addPartyType')->name('company.setting.addpartytype');

                Route::get('company/settings/editPartyType/{id}', 'Admin\CompanyController@editPartyType')->name('company.setting.editPartyType');

                Route::get('company/settings/getPartyTypeList', 'Admin\CompanyController@getPartyTypeList')->name('company.setting.getPartyTypeList');

                Route::get('company/settings/removePartyType/{id}', 'Admin\CompanyController@removePartyType')->name('company.setting.removePartyType');



                Route::post('company/setting/addmarketarea', 'Admin\CompanyController@addMarketArea')->name('company.setting.addmarketarea');



                Route::delete('/company/{id}', 'Admin\CompanyController@destroy')->name('company.destroy');



                Route::get('/outlets', 'Admin\OutletController@index')->name('outlets');

                Route::get('/outlets/fetchData', 'Admin\OutletController@fetchData')->name('outlets.fetchData');

                Route::get('/outlets/create', 'Admin\OutletController@create')->name('outlets.create');

                Route::post('/outlets/create', 'Admin\OutletController@store')->name('outlets.store');

                Route::get('/outlets/edit/{id}', 'Admin\OutletController@edit')->name('outlets.edit');



                Route::post('/outlets/edit/{id}', 'Admin\OutletController@update')->name('outlets.update');

                Route::get('/outlets/{id}', 'Admin\OutletController@show')->name('outlets.show');

                Route::delete('/outlets/{id}', 'Admin\OutletController@destroy')->name('outlets.delete');

                Route::patch('/outlets/{id}', 'Admin\OutletController@updateStatus')->name('oulets.updatestatus');



                Route::get('/companyusage', 'Admin\CompanyController@companyusage')->name('companyusage');

                Route::get('/companyusagenew', 'Admin\CompanyController@companyusagenew')->name('companyusagenew');

                Route::get('/fetchUsageCompany', 'Admin\CompanyController@fetchUsageCompany')->name('companyusage.fetchUsageCompany');



                Route::get('/fetch-phonecode-state', 'Admin\OutletController@fetchPhoneState')->name('outlets.fetchPhoneState');

                Route::get('/fetch-city', 'Admin\OutletController@fetchCity')->name('outlets.fetchCity');



            }

        );



    }

);





/* SUPER ADMIN : SERVICE MANAGEMENT */

// Route::group(

//     [

//         'domain' => 'admin.' . config('app.domain'),

//         'middleware' => ['auth', 'app.is_admin'],

//         'as' => 'admin.'

//     ],

//     function () {

//          Route::get('/', 'DashboardController@home')->name('home');



//         // @todo: Metrics, Billing/Subscription Management, etc

//     }

// );



Route::group([

  'domain' => 'register.' . config('app.domain'),

  'as' => 'registercompany.'

], function () {

  Route::get('/subscriptions/{subscription_code}/register', 'User\SubscriptionController@index')->name('subscription.register');

});





/* SHOP ROUTES */

Route::group(

    [

        'domain' => '{subdomain}.' . config('app.domain'),

        'middleware' => ['company.domain', 'isActive', 'isEmployeeActive'],

        'as' => 'company.'

    ], function () {



    Route::get('/home', 'HomeController@home')->name('home');



    Route::get('/', 'Company\Auth\LoginController@showLoginForm')->name('login');

    // Authentication and registration

    Route::get('/login', 'Company\Auth\LoginController@showLoginForm')->name('login');

    Route::get('/showforgotPassword', 'Company\Auth\LoginController@showForgotPassword')->name('showForgotPassword');

    Route::post('/forgotPassword', 'Company\Auth\LoginController@forgotPassword')->name('forgotPassword');





    Route::get('password/reset/{token}', 'Company\Auth\ResetPasswordController@showResetForm')->name('password.reset');



    Route::post('password/reset', 'Company\Auth\ResetPasswordController@reset')->name('password.update');

    Route::post('/login', 'Company\Auth\LoginController@handleLogin')->name('login.post');

    Route::get('/logout', 'Company\Auth\LoginController@logout')->name('logout');

    Route::post('/logout', 'Company\Auth\LoginController@logout')->name('logout');



    // SHOP MANAGEMENT (subdomain management)

    Route::group(

        [

            'prefix' => 'admin',

            'middleware' => ['auth'],

            'as' => 'admin.'

        ],

        function () {

            Route::get('/', 'Company\Admin\DashboardController@home')->name('home');

            Route::get('/home/{startdate?}/{enddate?}', 'Company\Admin\DashboardController@homemod')->name('home.mod');





            Route::get('/assignRoleToUser/{user_id}/{company_id}/{role_id}', 'Company\Admin\DashboardController@assignRoleToUser');

            Route::get('permission-cache-reset', function () {

                \Artisan::call('cache:forget', ['key' => 'spatie.permission.cache']);

                dd("Permission Cache is cleared");

            });



            Route::get('artisan-migrate', function () {

                //     \Artisan::call('migrate', array('--path' => '/database/migrations/custom_modules',  '--force' => true ));

                //     \Artisan::call('migrate', array('--path' => '/database/migrations/partyfilesimages',  '--force' => true ));

                // dump("Migrated");

                // \Artisan::call('migrate', array('--path' => '/database/migrations/addcolumns',  '--force' => true ));

                // dump("Migrated");

                //     $main_modules = DB::statement("INSERT INTO `main_modules` (`id`, `name`, `position`, `field`, `created_at`, `updated_at`) VALUES (NULL, 'Custom Module', NULL, 'custom_module', NULL, NULL)");

                //     $client_settings = DB::statement("ALTER TABLE `client_settings` ADD `no_of_custom_module` INT(0) NOT NULL AFTER `party_wise_rate_setup`, ADD `custom_module` BOOLEAN NOT NULL AFTER `no_of_custom_module`");

                //     $main_module_id = DB::select("SELECT `id` FROM `main_modules` WHERE `name` LIKE 'Custom Module' AND `field` LIKE 'custom_module'");

                //     $permission_categories = DB::statement("INSERT INTO `permission_categories` (`id`, `company_id`, `permission_model_id`, `permission_model`, `permission_category_type`, `name`, `display_name`, `indexing_priority`, `created_at`, `updated_at`) VALUES (NULL, NULL, {$main_module_id[0]->id}, '', 'Global', 'Custom_Module', 'Custom Module', NULL, NULL, NULL)");

                //     $permission_category_id = DB::select("SELECT `id` FROM `permission_categories` WHERE `name` LIKE 'Custom_Module' AND `display_name` LIKE 'Custom Module'");

                //     $permissions = DB::statement("INSERT INTO `permissions` (`id`, `company_id`, `permission_type`, `permission_category_id`, `name`, `guard_name`, `enabled`, `is_mobile`, `created_at`, `updated_at`) VALUES (NULL, NULL, NULL, {$permission_category_id[0]->id}, 'Custom-Module-create', 'web', '0', '0', NULL, NULL), (NULL, NULL, NULL, {$permission_category_id[0]->id}, 'Custom-Module-view', 'web', '1', '1', NULL, NULL), (NULL, NULL, NULL, {$permission_category_id[0]->id}, 'Custom-Module-update', 'web', '0', '0', NULL, NULL), (NULL, NULL, NULL, {$permission_category_id[0]->id}, 'Custom-Module-delete', 'web', '0', '0', NULL, NULL), (NULL, NULL, NULL, {$permission_category_id[0]->id}, 'Custom-Module-status', 'web', '0', '0', NULL, NULL)");

                // $sharing_permission_category = DB::statement("INSERT INTO `permission_categories` (`id`, `company_id`, `permission_model_id`, `permission_model`, `permission_category_type`, `name`, `display_name`, `indexing_priority`, `created_at`, `updated_at`) VALUES (NULL, NULL, NULL, NULL, 'Global', 'file_uploads', 'File Uploads', NULL, NULL, NULL), (NULL, NULL, NULL, NULL, 'Global', 'image_uploads', 'Image Uploads', NULL, NULL, NULL)");

                // $file_sharing_permission_category_id = DB::select("SELECT `id` FROM `permission_categories` WHERE `name` LIKE 'file_uploads' AND `display_name` LIKE 'File Uploads'");

                // $image_sharing_permission_category_id = DB::select("SELECT `id` FROM `permission_categories` WHERE `name` LIKE 'image_uploads' AND `display_name` LIKE 'Image Uploads'");

                // $add_permissions = DB::statement("INSERT INTO `permissions`

                // (`id`, `company_id`, `permission_type`, `permission_category_id`, `name`, `guard_name`, `enabled`, `is_mobile`, `created_at`, `updated_at`) VALUES

                // (NULL, NULL, NULL, '{$file_sharing_permission_category_id[0]->id}', 'fileuploads-create', '', '1', '1', NULL, NULL),

                // (NULL, NULL, NULL, '{$file_sharing_permission_category_id[0]->id}', 'fileuploads-view', '', '1', '1', NULL, NULL),

                // (NULL, NULL, NULL, '{$file_sharing_permission_category_id[0]->id}', 'fileuploads-update', '', '1', '1', NULL, NULL),

                // (NULL, NULL, NULL, '{$file_sharing_permission_category_id[0]->id}', 'fileuploads-delete', '', '1', '1', NULL, NULL),

                // (NULL, NULL, NULL, '{$file_sharing_permission_category_id[0]->id}', 'fileuploads-status', '', '0', '0', NULL, NULL),

                // (NULL, NULL, NULL, '{$image_sharing_permission_category_id[0]->id}', 'imageuploads-create', '', '1', '1', NULL, NULL),

                // (NULL, NULL, NULL, '{$image_sharing_permission_category_id[0]->id}', 'imageuploads-view', '', '1', '1', NULL, NULL),

                // (NULL, NULL, NULL, '{$image_sharing_permission_category_id[0]->id}', 'imageuploads-update', '', '1', '1', NULL, NULL),

                // (NULL, NULL, NULL, '{$image_sharing_permission_category_id[0]->id}', 'imageuploads-delete', '', '1', '1', NULL, NULL),

                // (NULL, NULL, NULL, '{$image_sharing_permission_category_id[0]->id}', 'imageuploads-status', '', '0', '0', NULL, NULL)");

                // DB::statement("INSERT INTO `main_modules` (`id`, `name`, `position`, `field`, `created_at`, `updated_at`) VALUES (NULL, 'Party Files', NULL, 'party_files', NULL, NULL), (NULL, 'Party Images', NULL, 'party_images', NULL, NULL)");

                // DB::statement("ALTER TABLE `client_settings` CHANGE `party_files_images` `party_files` TINYINT(4) NOT NULL DEFAULT '0'");

                // DB::statement("ALTER TABLE `client_settings` ADD `party_images` TINYINT NOT NULL DEFAULT '0' AFTER `party_files`");

                // DB::statement("ALTER TABLE `client_settings` CHANGE `party_file_upload_types` `party_file_upload_types` VARCHAR(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'jpeg,jpg,png,gif,doc,docx,odt,txt,pdf,csv,xlsx,xls', CHANGE `party_file_upload_size` `party_file_upload_size` INT(10) UNSIGNED NULL DEFAULT '2048', CHANGE `party_image_upload_types` `party_image_upload_types` VARCHAR(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'jpeg,jpg,png,gif,doc,docx,odt,pdf,csv,xlsx,xls,zip,txt', CHANGE `party_image_upload_size` `party_image_upload_size` INT(10) UNSIGNED NULL DEFAULT '2048'");

                // DB::statement("UPDATE `client_settings` SET `party_file_upload_types` = 'doc,docx,odt,txt,pdf,csv,xlsx,xls',`party_image_upload_types` = 'jpeg,jpg,png,gif,svg', `party_file_upload_size` = '2048', `party_image_upload_size` = '2048' WHERE `party_image_upload_size` OR `party_file_upload_size` IS NULL ");

            });





            Route::group(['middleware' => ['is_expired']], function () {

                Route::get('/setting', 'Company\Admin\ClientSettingController@index')->name('setting');

                // Route::get('/setting/reset', 'Company\Admin\ClientSettingController@reset');

                Route::get('setting/edit', 'Company\Admin\ClientSettingController@edit')->name('setting.edit');

                Route::patch('setting/{id}', 'Company\Admin\ClientSettingController@update')->name('setting.update');

                

                Route::patch('setting/layout/{id}', 'Company\Admin\ClientSettingController@updateLayout')->name('setting.updateLayout');

                Route::patch('setting/email/{id}', 'Company\Admin\ClientSettingController@updateEmail')->name('setting.updateEmail');

                Route::patch('setting/other/{id}', 'Company\Admin\ClientSettingController@updateOther')->name('setting.updateOther');

                Route::delete('/setting/{id}', 'Company\Admin\ClientSettingController@destroy')->name('setting.destroy');

                Route::post('/setting/removeTax', 'Company\Admin\ClientSettingController@removeTax')->name('setting.removeTax');

                Route::post('/setting/addSettings/new', 'Company\Admin\ClientSettingController@addNewSettings')->name('setting.addNewSettings');

                Route::post('/setting/addSettings/edit', 'Company\Admin\ClientSettingController@editNewSettings')->name('setting.editNewSettings');

                Route::post('/setting/addSettings/delete', 'Company\Admin\ClientSettingController@deleteNewSettings')->name('setting.deleteNewSettings');

                Route::get('/clientsettings/getcounts', 'Company\Admin\ClientSettingController@getcounts')->name('clientsettings.getcounts');

                Route::get('/clientsettings/getpartytypes', 'Company\Admin\ClientSettingController@getpartytype')->name('clientsettings.getpartytypes');



                Route::get('/setting/states/get/{id}', 'Company\Admin\ClientSettingController@getStates');

                Route::get('/setting/cities/get/{id}', 'Company\Admin\ClientSettingController@getCities');





                // New Settings



                Route::get('/settingnew/setup', 'Company\Admin\ClientSettingNewController@index')->name('settingnew.setup');

                Route::get('/settingnew/customization', 'Company\Admin\ClientSettingNewController@customization')->name('settingnew.customization');

                Route::get('/settingnew/userroles', 'Company\Admin\ClientSettingNewController@userroles')->name('settingnew.userroles');

                

                Route::get('/settingnew/collaterals', 'Company\Admin\ClientSettingNewController@collaterals')->name('settingnew.collaterals');

                Route::get('/settingnew/integration', 'Company\Admin\ClientSettingNewController@integration')->name('settingnew.integration');

                Route::post('/settingnew/storetally', 'Company\Admin\ClientSettingNewController@storeTally')->name('settingnew.storetally');



                Route::post('/settingnew/update/admin-layout', 'Company\Admin\ClientSettingNewController@updateAdminLayout')->name('update.adminLayout');

                Route::patch('settingnew/profile/{id}', 'Company\Admin\ClientSettingNewController@updateProfile')->name('setting.updateProfile');



                // Collaterals

                Route::get('settingnew/createCollateralsFolderHome', 'Company\Admin\ClientSettingNewController@viewCollateralsFolderHome')->name('settingnew.viewCollateralsFolderHome');



                Route::post('settingnew/createCollateralsFolder', 'Company\Admin\ClientSettingNewController@createCollateralsFolder')->name('settingnew.createCollateralsFolder');



                Route::post('settingnew/editCollateralsFolder', 'Company\Admin\ClientSettingNewController@editCollateralsFolder')->name('settingnew.editCollateralsFolder');

                Route::post('settingnew/deleteCollateralsFolder', 'Company\Admin\ClientSettingNewController@deleteCollateralsFolder')->name('settingnew.deleteCollateralsFolder');





                Route::post('settingnew/uploadCollateralsFiles', 'Company\Admin\ClientSettingNewController@uploadCollateralsFiles')->name('settingnew.uploadCollateralsFiles');



                Route::post('settingnew/editCollateralsFiles', 'Company\Admin\ClientSettingNewController@editCollateralsFiles')->name('settingnew.editCollateralsFiles');

                Route::post('settingnew/deleteCollateralsFiles', 'Company\Admin\ClientSettingNewController@deleteCollateralsFiles')->name('settingnew.deleteCollateralsFiles');

                Route::post('settingnew/createCollateralsFolder/{id}', 'Company\Admin\ClientSettingNewController@viewCollateralsFolder')->name('settingnew.viewCollateralsFolder');





                //Profile and password update codes

                Route::get('setting/updateUserProfile', 'Company\Admin\ClientSettingController@updatePassword')->name('setting.updateuserprofile');

                Route::post('setting/updateUserProfile', 'Company\Admin\ClientSettingController@updatedPassword')->name('setting.updatedpassword');

                Route::patch('setting/updateUserProfile/{id}', 'Company\Admin\ClientSettingController@updatedProfile')->name('setting.updatedProfile');



                Route::post('setting/generateotp', 'Company\Admin\ClientSettingController@generateotp')->name('setting.generateotp');

                Route::post('setting/update/locationAccuracy', 'Company\Admin\ClientSettingController@updateLocAccuracy')->name('setting.updateLocAccuracy');



                Route::get('setting/createCollateralsFolderHome', 'Company\Admin\ClientSettingController@viewCollateralsFolderHome')->name('setting.viewCollateralsFolderHome');



                Route::post('setting/createCollateralsFolder', 'Company\Admin\ClientSettingController@createCollateralsFolder')->name('setting.createCollateralsFolder');



                Route::post('setting/editCollateralsFolder', 'Company\Admin\ClientSettingController@editCollateralsFolder')->name('setting.editCollateralsFolder');

                Route::post('setting/deleteCollateralsFolder', 'Company\Admin\ClientSettingController@deleteCollateralsFolder')->name('setting.deleteCollateralsFolder');





                Route::post('setting/uploadCollateralsFiles', 'Company\Admin\ClientSettingController@uploadCollateralsFiles')->name('setting.uploadCollateralsFiles');



                Route::post('setting/editCollateralsFiles', 'Company\Admin\ClientSettingController@editCollateralsFiles')->name('setting.editCollateralsFiles');

                Route::post('setting/deleteCollateralsFiles', 'Company\Admin\ClientSettingController@deleteCollateralsFiles')->name('setting.deleteCollateralsFiles');

                Route::post('setting/createCollateralsFolder/{id}', 'Company\Admin\ClientSettingController@viewCollateralsFolder')->name('setting.viewCollateralsFolder');

                // Route::post('/client/updatedateformat','Company\Admin\ClientSettingController@updateDateFormat')->name('setting.updateDateFormat');



                Route::post('/client/updatedateformat', 'Company\Admin\ClientSettingNewController@updateDateFormat')->name('settingnew.updateDateFormat');

                Route::post('/client/updateodometerrate', 'Company\Admin\ClientSettingNewController@updateOdometerRate')->name('settingnew.updateOdometerRate');

                Route::post('/client/update-client-setting-field', 'Company\Admin\ClientSettingNewController@updateClientSettingField')->name('settingnew.updateClientSettingTable');

                Route::post('/client/delete-tax', 'Company\Admin\ClientSettingNewController@deleteTax')->name('settingnew.deleteTax');

                Route::post('/client/update-tax', 'Company\Admin\ClientSettingNewController@updateTax')->name('settingnew.updateTax');

                Route::post('/client/update-default-tax-flag', 'Company\Admin\ClientSettingNewController@updateDefaultFlag')->name('settingnew.updateDefaultFlag');



                // Route::post('/setting/removeOTP/{id}', 'Company\Admin\ClientSettingController@removeOTP')->name('setting.removeOTP');

                Route::group(

                    [

                        'middleware' => ['is_enabled:party_wise_rate_setup'],

                    ],

                    function () {

                        Route::get('custom-rate-setup', 'Company\Admin\PartyRateController@index')->name('setup_rates');

                        Route::get('get-rates-data', 'Company\Admin\PartyRateController@getRatesData')->name('get_rates_data');

                        Route::get('custom-rate-setup/fetch-products-data', 'Company\Admin\PartyRateController@fetchProductsData')->name('add_new_rate.fetch_products_data');

                        Route::post('custom-rate-setup/quick-custom-rate-setup', 'Company\Admin\PartyRateController@quickCustomRateSetup')->name('add_new_rate.quick_custom_rate_setup');

                        Route::post('custom-rate-setup', 'Company\Admin\PartyRateController@store')->name('add_new_rate.store');

                        Route::delete('/custom-rate-setup', 'Company\Admin\PartyRateController@delete')->name('add_new_rate.delete');

                        Route::get('custom-rate-setup/{rate_id}/show', 'Company\Admin\PartyRateController@show')->name('rate_setup_page.show');

                        Route::get('custom-rate-setup/{rate_id}', 'Company\Admin\PartyRateController@rateSetupPage')->name('rate_setup_page');

                        Route::patch('rates-update-name/{rate_id}', 'Company\Admin\PartyRateController@update')->name('rate_setup_page.update_name');

                        Route::patch('custom-rate-setup/{rate_id}', 'Company\Admin\PartyRateController@updateMRP')->name('rate_setup_page.update_mrp');

                    });





                Route::group([

                    'middleware' => ['is_enabled:custom_module']

                ], function () {

                    Route::get('/custom-modules', 'Company\Admin\CustomModuleController@index')->name('custom.modules');

                    Route::get('/custom-modules/create', 'Company\Admin\CustomModuleController@create')->name('custom.modules.create');

                    Route::post('/custom-module/create', 'Company\Admin\CustomModuleController@store')->name('custom.modules.store');

                    Route::get('custom-modules/{id}/manage', 'Company\Admin\CustomModuleController@edit')->name('custom.modules.edit');

                    Route::patch('custom-modules/update/{id}', 'Company\Admin\CustomModuleController@update')->name('custom.modules.update');

                    Route::delete('/custom-modules/{id}', 'Company\Admin\CustomModuleController@destroy')->name('custom.modules.destroy');

                    Route::post('custom-modules/changeStatus', 'Company\Admin\CustomModuleController@changeStatus')->name('custom.modules.changeStatus');

                    Route::post('custom-modules/changeName', 'Company\Admin\CustomModuleController@changeName')->name('custom.modules.changeName');

                    Route::post('/custom-module', 'Company\Admin\CustomModuleController@ajaxDatatable')->name('custom.modules.ajaxDatatable');

                    Route::get('/custom-modules/getNames', 'Company\Admin\CustomModuleController@getCustomModules')->name('custom.modules.getCustomModules');





                    Route::get('/custom-modules/{id}/form/index', 'Company\Admin\CustomModuleFormController@index')->name('custom.modules.form.index');

                    Route::get('/custom-modules/{id}/form/create', 'Company\Admin\CustomModuleFormController@create')->name('custom.modules.form.create');

                    Route::post('/custom-modules/{id}/form/store', 'Company\Admin\CustomModuleFormController@store')->name('custom.modules.dynamic.form.store');

                    Route::get('/custom-modules/{id}/form/view/{data_id}', 'Company\Admin\CustomModuleFormController@show')->name('custom.modules.form.view');

                    Route::get('/custom-modules/{id}/form/{data_id}/edit', 'Company\Admin\CustomModuleFormController@edit')->name('custom.modules.form.edit');

                    Route::patch('/custom-modules/{id}/form/update/{data_id}', 'Company\Admin\CustomModuleFormController@update')->name('custom.modules.form.update');

                    Route::delete('/custom-modules/{id}/form/{data_id}', 'Company\Admin\CustomModuleFormController@destroy')->name('custom.modules.form.delete');

                    Route::post('/custom-modules/form', 'Company\Admin\CustomModuleFormController@ajaxDatatable')->name('custom.modules.form.ajaxDatatable');

                    Route::post('custom-modules/custompdfdexport', 'Company\Admin\CustomModuleFormController@custompdfdexport')->name('custom.modules.form.custompdfdexport');



                    Route::post('/custom-module-form/create', 'Company\Admin\CustomModuleFieldController@store')->name('custom.modules.form.store');

                    Route::post('/custom-module-form/update', 'Company\Admin\CustomModuleFieldController@update')->name('custom.modules.form.field.update');

                    Route::post('custom-module-form/change-status', 'Company\Admin\CustomModuleFieldController@changeStatus')->name('custom.modules.form.changeStatus');

                    Route::delete('/custom-modules-form/{id}', 'Company\Admin\CustomModuleFieldController@destroy')->name('custom.modules.field.destroy');

                    Route::post('/custom-modules-form/order/update', 'Company\Admin\CustomModuleFieldController@updateOrder')->name('custom.modules.field.order');

                    Route::post('/custom-modules-form/validate-phone', 'Company\Admin\CustomModuleFormController@validatePhone')->name('custom.modules.form.validatePhone');



                });





                Route::group([

                    'middleware' => ['is_enabled:odometer']

                ], function () {

                    Route::get('/reports/odometer', 'Company\Admin\OdometerReportsController@index')->name('odometer.report.index');

                    Route::post('/odometer-reports', 'Company\Admin\OdometerReportsController@ajaxDatatable')->name('odometer.report.ajaxDatatable');

                    Route::post('/odometer-report/update', 'Company\Admin\OdometerReportsController@update')->name('odometer.report.update');

                    Route::post('/odometer-report/collection/download-pdf', 'Company\Admin\OdometerReportsController@custompdf')->name('downloadPdf');

                    Route::get('/reports/odometer/{employee_id}/{from}/{to}', 'Company\Admin\OdometerReportsController@show')->name('odometer.report.show');

                    Route::get('/reports/odometer/single-date/{employee_id}/{from}/{to}', 'Company\Admin\OdometerReportsController@generateSingleDatePdf')->name('odometer.single.date.downloadPdf');

                });

                // //Target routes 

                // Route::get('/salesmantarget', 'Company\Admin\TargetSalesmanController@index')->name('salesmantarget');

                // Route::get('/salesmantargetreport', 'Company\Admin\TargetSalesmanController@genReport')->name('salesmantargetreport');





                //Route::get('/', 'company\PageController@home')->name('home');



                // Route::get('/', 'Company\Admin\DashboardController@home')->name('home');

                // master search

                Route::post('/', 'Company\Admin\DashboardController@homeSearch')->name('home.p');

                // Route::get('/earn-rewards', function () {

                //     return view('layouts.partials.company.earn-rewards');

                // })->name('earn-rewards');

                //end of master search

                Route::get('/home/livetracking', 'Company\Admin\DashboardController@home')->name('home.livetracking');

                Route::get('/home/getpath', 'Company\Admin\DashboardController@getpath')->name('getpath');

                Route::post('/home/getclientspath', 'Company\Admin\DashboardController@getclientspath')->name('getclientspath');

                Route::get('/home/getPartiesFromBeat', 'Company\Admin\DashboardController@getPartiesFromBeat')->name('getpartiesfrombeat');

                Route::post('/home/mail', 'Company\Admin\DashboardController@mail')->name('mail');



                // @todo: orders, customers, staff members, etc

                //Employee

                Route::get('/employee', 'Company\Admin\EmployeeController@index')->name('employee');

                Route::get('/employees-designation/{filtered}', 'Company\Admin\EmployeeController@filteredDesignation')->name('employee.filtered');

                Route::get('/employee/getsuperiorlist', 'Company\Admin\EmployeeController@getSuperiorList')->name('employee.get-superior-list');

                Route::post('/employee/getEmployeeSuperiors', 'Company\Admin\EmployeeController@getEmployeeSuperiors')->name('employee.getEmployeeSuperiors');

                Route::post('/employee/getChainDesignationEmployees', 'Company\Admin\EmployeeController@getChainDesignationEmployees')->name('employee.getChainDesignationEmployees');

                Route::post('/employee/ajaxdatatable', 'Company\Admin\EmployeeController@ajaxDatatable')->name('employee.ajaxDatatable');

                Route::post('/employee/custompdfdexport', 'Company\Admin\EmployeeController@customPdfExport')->name('employee.customPdfExport');

                Route::post('/employee/{id}/pdfexports', 'Company\Admin\EmployeeController@pdfexports')->name('employee.show.pdfexports');

                Route::post('/employee/empOrderTable', 'Company\Admin\EmployeeController@empOrderTable')->name('employee.empOrderTable');

                Route::post('/employee/empClientVisitTable', 'Company\Admin\EmployeeController@empClientVisitTable')->name('employee.empClientVisitTable');

                Route::post('/employeeZeroOrdersTable', 'Company\Admin\EmployeeController@employeeZeroOrdersTable')->name('employee.employeeZeroOrdersTable');



                // Route::get('/employee/{id}/{date}', 'Company\Admin\EmployeeController@empClientVisitDetail')->name('employee.empClientVisitDetail');

                // Route::get('/employee-party-visit-download/{id}/{date}', 'Company\Admin\EmployeeController@empClientVisitDetailDownload')->name('employee.empClientVisitDetailDownload');

                // Route::get('/employee-party-visit-print-frame/{id}/{date}', 'Company\Admin\EmployeeController@empClientVisitDetailPrint')->name('employee.empClientVisitDetailPrint');



                Route::post('/employee/empDayremarksTable', 'Company\Admin\EmployeeController@empDayremarksTable')->name('employee.empDayremarksTable');

                Route::post('/employee/empCollectionTable', 'Company\Admin\EmployeeController@empCollectionTable')->name('employee.empCollectionTable');

                Route::post('/employee/empActivityTable', 'Company\Admin\EmployeeController@empActivityTable')->name('employee.empActivityTable');

                Route::get('/employee/create', 'Company\Admin\EmployeeController@create')->name('employee.create');

                Route::get('/employee/getsuperiorparties', 'Company\Admin\EmployeeController@getsuperiorparties')->name('employee.getsuperiorparties');

                Route::post('/employee/create', 'Company\Admin\EmployeeController@store')->name('employee.store');

                Route::get('/employee/{id}', 'Company\Admin\EmployeeController@show')->name('employee.show');

                Route::post('/employee/{id}/logout', 'Company\Admin\EmployeeController@logout')->name('employee.logout');

                Route::get('employee/{id}/edit', 'Company\Admin\EmployeeController@edit')->name('employee.edit');

                Route::post('employee/ajaxvalidate', 'Company\Admin\EmployeeController@ajaxValidate')->name('employee.ajaxValidate');

                Route::post('employee/ajaxupdatevalidate', 'Company\Admin\EmployeeController@ajaxUpdateValidate')->name('employee.ajaxUpdateValidate');

                Route::post('employee/ajaxbasicupdate', 'Company\Admin\EmployeeController@ajaxUpdate')->name('employee.ajaxBasicUpdate');

                Route::post('employee/ajaxcontactupdate', 'Company\Admin\EmployeeController@ajaxContactUpdate')->name('employee.ajaxContactUpdate');

                Route::post('employee/ajaxcompanyupdate', 'Company\Admin\EmployeeController@ajaxCompanyUpdate')->name('employee.ajaxCompanyUpdate');

                Route::post('employee/ajaxbankupdate', 'Company\Admin\EmployeeController@ajaxBankUpdate')->name('employee.ajaxBankUpdate');

                Route::post('employee/ajaxdocumentupdate', 'Company\Admin\EmployeeController@ajaxDocumentUpdate')->name('employee.ajaxDocumentUpdate');

                Route::post('employee/ajaxaccountupdate', 'Company\Admin\EmployeeController@ajaxAccountUpdate')->name('employee.ajaxAccountUpdate');

                Route::post('employee/transferuser', 'Company\Admin\EmployeeController@transferUser')->name('employee.transferUser');

                Route::post('employee/update/{id}', 'Company\Admin\EmployeeController@update')->name('employee.update');

                Route::delete('/employee/{id}', 'Company\Admin\EmployeeController@destroy')->name('employee.destroy');



                Route::post('/employee/addClient', 'Company\Admin\EmployeeController@addClient')->name('setting.addClient');

                Route::post('/employee/removeClient', 'Company\Admin\EmployeeController@removeClient')->name('setting.removeClient');

                Route::post('/employee/removeDoc', 'Company\Admin\EmployeeController@removeDoc')->name('employee.removeDoc');

                Route::post('/employee/addParties/{id}', 'Company\Admin\EmployeeController@addParties')->name('employee.addParties');

                Route::post('/employee/getEmployeeHandles', 'Company\Admin\EmployeeController@getEmployeeHandles')->name('employee.getEmployeeHandles');

                Route::post('/employee/getAttendances', 'Company\Admin\EmployeeController@getAttendances')->name('employee.getAttendances');

                Route::post('/employee/getAttendances/count', 'Company\Admin\EmployeeController@getAttendancesCount')->name('employee.getAttendancesCount');



                // Employee Group

                Route::get('/employeegroup', 'Company\Admin\EmployeeGroupController@index')->name('employeegroup');

                Route::post('/employeegroups', 'Company\Admin\EmployeeGroupController@ajaxDatatable')->name('employeegroups.ajaxDatatable');

                Route::post('/employeegroups/custompdfdexport', 'Company\Admin\EmployeeGroupController@custompdfdexport')->name('employeegroups.custompdfdexport');

                Route::get('/employeegroup/create', 'Company\Admin\EmployeeGroupController@create')->name('employeegroup.create');

                Route::post('/employeegroup/create', 'Company\Admin\EmployeeGroupController@store')->name('employeegroup.store');

                Route::get('/employeegroup/{id}', 'Company\Admin\EmployeeGroupController@show')->name('employeegroup.show');

                Route::get('employeegroup/{id}/edit', 'Company\Admin\EmployeeGroupController@edit')->name('employeegroup.edit');

                Route::post('employee/changeStatus', 'Company\Admin\EmployeeController@changeStatus')->name('employee.changeStatus');

                Route::patch('employeegroup/update/{id}', 'Company\Admin\EmployeeGroupController@update')->name('employeegroup.update');

                Route::delete('/employeegroup/{id}', 'Company\Admin\EmployeeGroupController@destroy')->name('employeegroup.destroy');

                Route::post('employeegroup/changeStatus', 'Company\Admin\EmployeeGroupController@changeStatus')->name('employeegroup.changeStatus');





                Route::group(

                    [

                        'middleware' => ['is_enabled:products'],

                    ],

                    function () {

                        //Product

                        Route::group(['middleware' => ['is_enabled:zero_orders']], function () {

                            Route::get('/zeroorders', 'Company\Admin\ZeroOrderController@index')->name('zeroorders');

                            Route::post('/zeroorders', 'Company\Admin\ZeroOrderController@zeroorderlistDataTable')->name('zeroorders.zeroordersDataTable');

                            Route::post('/zeroorders/custompdfreport', 'Company\Admin\ZeroOrderController@custompdfreport')->name('zeroorders.custompdfreport');

                            Route::get('/zeroorder/create', 'Company\Admin\ZeroOrderController@create')->name('zeroorder.create');

                            Route::post('/zeroorder/create', 'Company\Admin\ZeroOrderController@store')->name('zeroorder.store');

                            Route::get('/zeroorder/{id}', 'Company\Admin\ZeroOrderController@show')->name('zeroorder.show');

                            Route::get('/zeroorder/{id}/edit', 'Company\Admin\ZeroOrderController@edit')->name('zeroorder.edit');

                            Route::patch('/zeroorder/update/{id}', 'Company\Admin\ZeroOrderController@update')->name('zeroorder.update');

                            Route::delete('/zeroorder/delete/{id}', 'Company\Admin\ZeroOrderController@destroy')->name('zeroorder.destroy');

                        });



                        Route::get('/product', 'Company\Admin\ProductController@index')->name('product');

                        Route::post('/products', 'Company\Admin\ProductController@ajaxDatatable')->name('products.ajaxDatatable');

                        Route::delete('/products/massdestroy', 'Company\Admin\ProductController@massdestroy')->name('product.massdestroy');

                        Route::post('/products/hasInfo', 'Company\Admin\ProductController@hasInfo')->name('product.hasInfo');

                        Route::post('/products/changeStarredStatus', 'Company\Admin\ProductController@changeStarredStatus')->name('products.changeStarredStatus');



                        Route::post('/products/custompdfdexport', 'Company\Admin\ProductController@custompdfdexport')->name('products.custompdfdexport');

                        Route::get('/product/create', 'Company\Admin\ProductController@create')->name('product.create');

                        Route::post('/product/create', 'Company\Admin\ProductController@store')->name('product.store');

                        Route::get('/product/getVariantColors', 'Company\Admin\ProductController@getVariantColors')->name('product.getVariantColors');

                        Route::get('/product/{id}', 'Company\Admin\ProductController@show')->name('product.show');

                        Route::get('product/{id}/edit', 'Company\Admin\ProductController@edit')->name('product.edit');

                        Route::post('product/changeStatus', 'Company\Admin\ProductController@changeStatus')->name('product.changeStatus');

                        Route::patch('product/update/{id}', 'Company\Admin\ProductController@update')->name('product.update');

                        Route::delete('/product/{id}', 'Company\Admin\ProductController@destroy')->name('product.destroy');

                        Route::post('/product/get', 'Company\Admin\ProductController@getProduct')->name('product.get');



                        Route::post('/order/productVariant', 'Company\Admin\OrderController@productVariant')->name('get.productVariant');

                        Route::post('/order/productPartiesMrp', 'Company\Admin\OrderController@productPartiesMrp')->name('get.productPartiesMrp');

                        Route::post('/order/variantPartiesMrp', 'Company\Admin\OrderController@variantPartiesMrp')->name('get.variantPartiesMrp');



                        //Category

                        Route::get('/category', 'Company\Admin\CategoryController@index')->name('category');

                        Route::post('/category/custompdfdexport', 'Company\Admin\CategoryController@custompdfdexport')->name('category.customPdfExport');

                        Route::get('/category/create', 'Company\Admin\CategoryController@create')->name('category.create');

                        Route::post('/category/create', 'Company\Admin\CategoryController@store')->name('category.store');

                        Route::get('/category/{id}', 'Company\Admin\CategoryController@show')->name('category.show');

                        Route::get('category/{id}/edit', 'Company\Admin\CategoryController@edit')->name('category.edit');

                        Route::patch('category/update/{id}', 'Company\Admin\CategoryController@update')->name('category.update');

                        Route::delete('/category/{id}', 'Company\Admin\CategoryController@destroy')->name('category.destroy');

                        Route::post('category/changeStatus', 'Company\Admin\CategoryController@changeStatus')->name('category.changeStatus');



                        Route::group(

                          [

                              'middleware' => ['is_enabled:category_wise_rate_setup'],

                          ],

                          function () {

                        /** Category wise rate setup */

                          Route::get('category-rate-setup', 'Company\Admin\CategoryRateController@index')->name('category.rates.index');

                          Route::post('category-rate-setup/create', 'Company\Admin\CategoryRateController@store')->name('category.rates.store');

                          Route::get('category-rate-setup/{id}', 'Company\Admin\CategoryRateController@show')->name('category.rates.show');

                          Route::post('category-rate-setup/{id}', 'Company\Admin\CategoryRateController@quickRateSetup')->name('category.rates.quickRateSetup');

                          Route::get('category-rate-setup/{id}/rates', 'Company\Admin\CategoryRateController@ratesShow')->name('category.rates.ratesShow');

                          Route::patch('category-rate-setup/{id}/rates', 'Company\Admin\CategoryRateController@update')->name('category.rates.update');

                          Route::delete('category-rate-setup/{id}/rates', 'Company\Admin\CategoryRateController@destroy')->name('category.rates.delete');

                          Route::post('category-rate-setup/{id}/fetch', 'Company\Admin\CategoryRateController@fetch')->name('category.rates.fetch');

                          Route::patch('category-rate-setup/{id}/{category_rate_type_id}', 'Company\Admin\CategoryRateController@updateOrCreateProductRate')->name('category.rates.updateOrCreate');

                        });



                        //unit routes

                        Route::get('/unit', 'Company\Admin\UnitController@index')->name('unit');





                        Route::group(

                            [

                                'middleware' => ['is_enabled:unit_conversion'],

                            ],

                            function () {

                                Route::get('/unitsconversion', 'Company\Admin\UnitController@unitconversion')->name('unit.conversion');

                                Route::post('/unitsconversion', 'Company\Admin\UnitController@storeunitconversion')->name('unit.storeunitconversion');

                                Route::patch('/unitsconversion/{id}', 'Company\Admin\UnitController@updateunitconversion')->name('unit.updateunitconversion');

                                Route::delete('/unitsconversion/{id}', 'Company\Admin\UnitController@deleteunitconversion')->name('unit.deleteunitconversion');

                                Route::get('/order/unitsconversion/getrelatedunits', 'Company\Admin\OrderController@getRelatedUnits')->name('unit.getrelatedunits');

                                Route::get('/order/unitsconversion/getunitsmrp', 'Company\Admin\OrderController@getUnitMrp')->name('unit.getunitsmrp');

                            });

                        Route::post('/unit/custompdfdexport', 'Company\Admin\UnitController@custompdfdexport')->name('unit.customPdfExport');

                        Route::get('/unit/create', 'Company\Admin\UnitController@create')->name('unit.create');

                        Route::post('/unit/create', 'Company\Admin\UnitController@store')->name('unit.store');

                        Route::get('/unit/{id}', 'Company\Admin\UnitController@show')->name('unit.show');

                        Route::get('unit/{id}/edit', 'Company\Admin\UnitController@edit')->name('unit.edit');

                        Route::patch('unit/update/{id}', 'Company\Admin\UnitController@update')->name('unit.update');

                        Route::delete('/unit/{id}', 'Company\Admin\UnitController@destroy')->name('unit.destroy');

                        Route::post('unit/changeStatus', 'Company\Admin\UnitController@changeStatus')->name('unit.changeStatus');



                        //brands

                        Route::get('/brand', 'Company\Admin\BrandController@index')->name('brand');

                        Route::post('/brand/custompdfdexport', 'Company\Admin\BrandController@custompdfdexport')->name('brand.customPdfExport');

                        Route::get('/brand/create', 'Company\Admin\BrandController@create')->name('brand.create');

                        Route::post('/brand/create', 'Company\Admin\BrandController@store')->name('brand.store');

                        Route::get('/brand/{id}', 'Company\Admin\BrandController@show')->name('brand.show');

                        Route::get('brand/{id}/edit', 'Company\Admin\BrandController@edit')->name('brand.edit');

                        Route::patch('brand/update/{id}', 'Company\Admin\BrandController@update')->name('brand.update');

                        Route::delete('/brand/{id}', 'Company\Admin\BrandController@destroy')->name('brand.destroy');

                        Route::post('brand/changeStatus', 'Company\Admin\BrandController@changeStatus')->name('brand.changeStatus');

                    });



                Route::group(

                    [

                        'middleware' => ['is_enabled:schemes'],

                    ],

                    function () {





                        // schemes route

                        Route::get('/schemes', 'Company\Admin\SchemesController@index')->name('scheme');

                        Route::get('/scheme/create', 'Company\Admin\SchemesController@create')->name('scheme.create');

                        Route::post('/scheme', 'Company\Admin\SchemesController@ajaxDatatable')->name('scheme.ajaxDatatable');

                        Route::get('/scheme/{id}', 'Company\Admin\SchemesController@show')->name('scheme.view');

                        Route::get('/scheme/{id}/edit', 'Company\Admin\SchemesController@edit')->name('scheme.edit');

                        Route::delete('/scheme/{id}', 'Company\Admin\SchemesController@destroy')->name('scheme.destroy');



                        Route::post('/scheme/create', 'Company\Admin\SchemesController@store')->name('scheme.store');

                        Route::patch('/scheme/update/{id}', 'Company\Admin\SchemesController@update')->name('scheme.update');

                        Route::post('/scheme/changeStatus', 'Company\Admin\SchemesController@changeStatus')->name('scheme.changeStatus');



                        Route::post('/filter/brand/category', 'Company\Admin\SchemesController@filterBrandCategory')->name('filter.brand.category');

                        Route::post('/filter/category/brand', 'Company\Admin\SchemesController@filterCategoryBrand')->name('filter.category.brand');

                        Route::post('/filter/products/search', 'Company\Admin\SchemesController@searchProduct')->name('filter.products.search');

                        Route::post('/filter/offered/products', 'Company\Admin\SchemesController@offerProducts')->name('filter.offered.products');



                        Route::post('/apply/selected/schemes', 'Company\Admin\SchemesController@applySchemes')->name('apply.selected.scheme');

                        Route::post('get/all/available/schemes', 'Company\Admin\SchemesController@getAllAvailableSchemes')->name('get.all.available.schemes');

                        Route::post('check/party/schemes', 'Company\Admin\SchemesController@checkPartySchemes')->name('check.party.schemes');

                    });









                Route::group(

                    [

                        'middleware' => ['is_enabled:clients'],

                    ],

                    function () {

                        //Client

                        Route::get('/client', 'Company\Admin\ClientController@index')->name('client');

                        Route::post('/client', 'Company\Admin\ClientController@ajaxDatatable')->name('client.ajaxDatatable');

                        Route::get('/get-total-party-created', 'Company\Admin\ClientController@getTotalPartyCreated')->name('client.getTotalPartyCreated');

                        Route::get('/get-never-ordered-party', 'Company\Admin\ClientController@getNeverOrderedParty')->name('client.getNeverOrderedParty');

                        Route::get('/get-unvisited-party', 'Company\Admin\ClientController@getPartyUnVisited')->name('client.getPartyUnVisited');

                        Route::get('/get-party-no-action', 'Company\Admin\ClientController@getPartyNoAction')->name('client.getPartyNoAction');

                        

                        Route::post('/client/custompdfdexport', 'Company\Admin\ClientController@custompdfdexport')->name('client.customPdfExport');

                        Route::get('/client/fetchcitywisebeats', 'Company\Admin\ClientController@fetchcitywisebeats')->name('client.fetchcitywisebeats');

                        Route::post('/partyOrdersTable', 'Company\Admin\ClientController@partyOrdersTable')->name('client.partyOrdersTable');

                        Route::post('/partyOrdersReceivedTable', 'Company\Admin\ClientController@partyOrdersReceivedTable')->name('client.partyOrdersReceivedTable');

                        Route::post('/partyZeroOrdersTable', 'Company\Admin\ClientController@partyZeroOrdersTable')->name('client.partyZeroOrdersTable');

                        Route::post('/partyCollectionTable', 'Company\Admin\ClientController@partyCollectionTable')->name('client.partyCollectionTable');

                        Route::get('/client/create', 'Company\Admin\ClientController@create')->name('client.create');

                        Route::post('/client/create', 'Company\Admin\ClientController@store')->name('client.store');

                        Route::post('/client/validateCompanyName', 'Company\Admin\ClientController@validateCompanyName')->name('client.validateCompanyName');

                        Route::get('/client/getsuperiorlist', 'Company\Admin\ClientController@getSuperiorList')->name('client.get-superior-list');



                        Route::delete('/client/massdestroy', 'Company\Admin\ClientController@massdestroy')->name('party.massdestroy');



                        Route::get('/client/subclients/{id}', 'Company\Admin\ClientController@index')->name('client.subclients');

                        Route::post('/client/subclients/{id}', 'Company\Admin\ClientController@ajaxDatatable')->name('client.subclients.ajaxDatatable');



                        Route::get('/client/subclients/{id}/{type}/{range?}', 'Company\Admin\ClientController@fileredResults')->name('client.fileredResults');



                        Route::post('/client/ajaxbasicupdate', 'Company\Admin\ClientController@ajaxBasicUpdate')->name('client.ajaxBasicUpdate');

                        Route::post('/client/ajaxbusinessupdate', 'Company\Admin\ClientController@ajaxBusinessUpdate')->name('client.ajaxBusinessUpdate');

                        Route::post('/client/ajaxcontactupdate', 'Company\Admin\ClientController@ajaxContactUpdate')->name('client.ajaxContactUpdate');

                        Route::post('/client/ajaxlocationupdate', 'Company\Admin\ClientController@ajaxLocationUpdate')->name('client.ajaxLocationUpdate');

                        Route::post('/client/ajaxaccountingupdate', 'Company\Admin\ClientController@ajaxAccountingUpdate')->name('client.ajaxAccountingUpdate');

                        Route::post('/client/ajaxmiscupdate', 'Company\Admin\ClientController@ajaxMiscellaneousUpdate')->name('client.ajaxMiscellaneousUpdate');

                        Route::post('/client/ajaxcustomfieldupdate', 'Company\Admin\ClientController@ajaxCustomFieldUpdate')->name('client.ajaxCustomFieldUpdate');



                        Route::get('/client/retailerslist/{id}', 'Company\Admin\ClientController@retailerslist')->name('client.retailerslist');

                        Route::get('/client/retailers', 'Company\Admin\ClientController@retailers')->name('client.retailers');

                        Route::get('/client/{id}', 'Company\Admin\ClientController@show')->name('client.show');

                        Route::put('client/{id}', 'Company\Admin\ClientController@changeStatus')->name('client.show');

                        Route::post('/client/{id}/pdfexports', 'Company\Admin\ClientController@pdfexports')->name('client.show.pdfexports');

                        // Route::put('client/{id}', 'Company\Admin\ClientController@changeStatusFromPage')->name('client.changeStatusFromPage');

                        Route::delete('/client/{id}', 'Company\Admin\ClientController@destroy')->name('client.destroy');

                        Route::post('/client/getEmployeeSuperior', 'Company\Admin\ClientController@getEmployeeSuperiors')->name('client.getEmployeeSuperior');

                        Route::post('/client/getEmployeeJunior', 'Company\Admin\ClientController@getEmployeeJuniors')->name('client.getEmployeeJuniors');

                        Route::get('client/{id}/edit', 'Company\Admin\ClientController@edit')->name('client.edit');

                        Route::post('client/changeStatus', 'Company\Admin\ClientController@changeStatus')->name('client.changeStatus');



                        Route::patch('client/update/{id}', 'Company\Admin\ClientController@update')->name('client.update');

                        Route::delete('/client/subclients/{id}', 'Company\Admin\ClientController@destroy')->name('subclient.destroy');

                        Route::post('/client/getajaxpartytypeclients', 'Company\Admin\ClientController@getPartyTypeClients')->name('client.getPartyTypeClients');





                        Route::get('/client/order/{id}', 'Company\Admin\OrderController@show')->name('order.showorder');

                        Route::get('/client/collection/{id}', 'Company\Admin\ClientController@showcollection')->name('order.showcollection');



                        Route::post('/client/addEmployee', 'Company\Admin\ClientController@addEmployee')->name('setting.addEmployee');

                        Route::post('/client/addLinkEmployee', 'Company\Admin\ClientController@addLinkEmployee')->name('setting.addLinkEmployee');

                        Route::post('/client/removeEmployee', 'Company\Admin\ClientController@removeEmployee')->name('setting.removeEmployee');

                        // Route::post('/client/updatecreditdays','Company\Admin\ClientController@updateCreditDays')->name('setting.updateCreditDays');



                        Route::post('/client/updatecreditdays', 'Company\Admin\ClientController@updateCreditDays')->name('settingnew.updateCreditDays');



                        //client credit routes

                        Route::post('/client/get-client-order-credit-details', 'Company\Admin\ClientController@getClientOrderCreditDetails')->name('get-client-order-credit-details');

                        Route::post('/client/get-client-payment-credit-details', 'Company\Admin\ClientController@getClientPaymentCreditDetails')->name('get-client-payment-credit-details');

                        Route::post('/client/get-order-on-credit-days', 'Company\Admin\ClientController@getOrderOnCreditDays')->name('get-order-on-credit-days');

                        Route::post('/client/get-upcoming-payment-details', 'Company\Admin\ClientController@getUpcomingPaymentDetails')->name('get-upcoming-payment-details');



                        //Business Type

                        Route::post('/businessType/store', 'Company\Admin\BusinessTypeController@store')->name('business_type.store');

                        Route::post('/businessType/update/{id}', 'Company\Admin\BusinessTypeController@update')->name('business_type.update');

                        Route::post('/businessType/destroy/{id}', 'Company\Admin\BusinessTypeController@destroy')->name('business_type.destroy');



                        //   Party Types

                        Route::get('/partytypes', 'Company\Admin\PartyTypeController@index')->name('partytype.list');

                        Route::post('/partytypes/store', 'Company\Admin\PartyTypeController@store')->name('partytype.store');

                        Route::post('/partytypes/update/{id}', 'Company\Admin\PartyTypeController@update')->name('partytype.update');

                        Route::post('/partyType/delete/{id}', 'Company\Admin\PartyTypeController@destroy')->name('partytype.destroy');

                        Route::get('/partytype/getPartyTypeList', 'Company\Admin\PartyTypeController@getPartyTypeList')->name('partytype.getPartyTypeList');



                        // Custom Field

                        Route::post('/customfields/addNew', 'Company\Admin\CustomFieldController@store')->name('customfield.addNew');

                        Route::post('/customfields/updateVisibility', 'Company\Admin\CustomFieldController@updateVisibility')->name('customfield.updateVisibility');

                        Route::post('/customfields/updateTitle', 'Company\Admin\CustomFieldController@updateTitle')->name('customfield.updateTitle');

                        Route::post('/customfields/destroy', 'Company\Admin\CustomFieldController@destroy')->name('customfield.destroy');

                        Route::post('/customfields/custom_field', 'Company\Admin\CustomFieldController@custom_edit')->name('customfields.custom_field');

                        Route::post('/customfields/updateStatus', 'Company\Admin\CustomFieldController@updateStatus')->name('customfield.updateStatus');



                        Route::post('/clients/clientVisitTable', 'Company\Admin\ClientController@clientVisitTable')->name('clients.clientVisitTable');

                        Route::post('/clients/party-visit-datatable-export', 'Company\Admin\ClientController@customPdfExport')->name('clientsVisits.customPdfExport');



                        Route::get('/party-visit/{id}/{visit_id}', 'Company\Admin\ClientController@clientVisitDetail')->name('clients.clientVisitDetail');





                        /**

                         * Party

                         * Uploads

                         * Files/Images

                         */

                        Route::post('/client/create-upload-folders/{id}', 'Company\Admin\ClientController@createUploadFolders')->name('client.createUploadFolders');

                        Route::post('/client/update-upload-folders/{id}', 'Company\Admin\ClientController@updateUploadFolders')->name('client.updateUploadFolders');

                        Route::delete('/client/delete-upload-folders/{id}', 'Company\Admin\ClientController@deleteUploadFolders')->name('client.deleteUploadFolders');



                        Route::get('/client/get-folder-view/{id}', 'Company\Admin\ClientController@getFolderView')->name('client.getFolderView');

                        Route::get('/client/get-folder-details/{id}/{folder_id}', 'Company\Admin\ClientController@getFolderDetails')->name('client.getFolderDetails');

                        Route::get('/client/get-image-folder-details/{id}/{folder_id}', 'Company\Admin\ClientController@getImagesDetails')->name('client.getImagesDetails');

                        Route::post('/client/get-folder-details-serverside/{id}/{folder_id}', 'Company\Admin\ClientController@getFolderDetailsDT')->name('client.getFolderDetailsDT');



                        Route::post('/client/upload-files-folder/{id}/{folder_id}', 'Company\Admin\ClientController@uploadObject')->name('client.uploadFileFolder');



                        Route::patch('/client/update-file/{id}/{file_id}', 'Company\Admin\ClientController@updateFile')->name('client.updateFile');

                        Route::delete('/client/delete-file/{id}/{file_id}', 'Company\Admin\ClientController@deleteFile')->name('client.deleteFile');



                        Route::get('/client/download-files-folder/{item_id}', 'Company\Admin\ClientController@downloadUploadedItems')->name('client.downloadUploadedItems');

                        Route::group(

                            [

                                'middleware' => ['is_enabled:visit_module'],

                            ],

                            function () {



                                Route::get('/party-visit', 'Company\Admin\ClientVisitController@index')->name('clientvisit.index');

                                Route::get('/party-visit-datatable', 'Company\Admin\ClientVisitController@ajaxDatatable')->name('clientvisit.ajaxDatatable');

                                Route::post('/party-visit-datatable-export', 'Company\Admin\ClientVisitController@customPdfExport')->name('clientvisit.customPdfExport');



                                Route::get('/employee-periodic-location', 'Company\Admin\ClientVisitController@employeePeriodicLocation')->name('employee.employeePeriodicLocation');

                                Route::get('/party-employee-visit/{id}/{date}', 'Company\Admin\ClientVisitController@empClientVisitDetail')->name('employee.empClientVisitDetail');

                                Route::get('/party-employee-visit-download/{id}/{date}', 'Company\Admin\ClientVisitController@empClientVisitDetailDownload')->name('employee.empClientVisitDetailDownload');

                                Route::get('/party-employee-visit-print-frame/{id}/{date}', 'Company\Admin\ClientVisitController@empClientVisitDetailPrint')->name('employee.empClientVisitDetailPrint');





                                //visit purpose

                                Route::post('/visitpurpose/store', 'Company\Admin\VisitPurposeController@store')->name('visitpurpose.store');

                                Route::post('/visitpurpose/update/{id}', 'Company\Admin\VisitPurposeController@update')->name('visitpurpose.update');

                                Route::post('/visitpurpose/destroy/{id}', 'Company\Admin\VisitPurposeController@destroy')->name('visitpurpose.destroy');

                            }

                        );

                        Route::get('/settingnew/customfields', 'Company\Admin\ClientSettingNewController@customfields')->name('settingnew.customfields');

                    });





                Route::group(

                    [

                        'middleware' => ['is_enabled:announcement'],

                    ],

                    function () {

                        //Announcement

                        Route::get('/announcement', 'Company\Admin\AnnouncementController@index')->name('announcement');

                        Route::post('/announcement/ajaxtable', 'Company\Admin\AnnouncementController@ajaxTable')->name('announcement.ajaxTable');

                        Route::post('/announcement/custompdfdexport', 'Company\Admin\AnnouncementController@custompdfdexport')->name('announcement.customPdfExport');

                        Route::get('/announcement/create', 'Company\Admin\AnnouncementController@create')->name('announcement.create');

                        Route::post('/announcement/create', 'Company\Admin\AnnouncementController@store')->name('announcement.store');

                        Route::post('/announcement/detail', 'Company\Admin\AnnouncementController@detail')->name('announcement.detail');

                        Route::get('/announcement/{id}', 'Company\Admin\AnnouncementController@show')->name('announcement.show');

                        Route::get('announcement/{id}/edit', 'Company\Admin\AnnouncementController@edit')->name('announcement.edit');

                        Route::patch('announcement/{id}', 'Company\Admin\AnnouncementController@update')->name('announcement.update');

                        Route::delete('/announcement/{id}', 'Company\Admin\AnnouncementController@destroy')->name('announcement.destroy');

                    });



                Route::group(

                    [

                        'middleware' => ['is_enabled:expenses'],

                    ],

                    function () {

                        //Expenses

                        Route::get('/expense', 'Company\Admin\ExpenseController@index')->name('expense');

                        Route::post('/expense/ajaxtable', 'Company\Admin\ExpenseController@ajaxTable')->name('expense.ajaxTable');

                        Route::post('/expense/custompdfdexport', 'Company\Admin\ExpenseController@custompdfdexport')->name('expense.customPdfExport');

                        Route::get('/expense/create', 'Company\Admin\ExpenseController@create')->name('expense.create');

                        Route::post('/expense/create', 'Company\Admin\ExpenseController@store')->name('expense.store');

                        Route::get('/expense/{id}', 'Company\Admin\ExpenseController@show')->name('expense.show');

                        Route::get('expense/{id}/edit', 'Company\Admin\ExpenseController@edit')->name('expense.edit');

                        Route::post('expense/changeStatus', 'Company\Admin\ExpenseController@changeStatus')->name('expense.changeStatus');

                        Route::patch('expense/update/{id}', 'Company\Admin\ExpenseController@update')->name('expense.update');

                        Route::delete('/expense/{id}', 'Company\Admin\ExpenseController@destroy')->name('expense.destroy');



                        //Expense Type

                        Route::post('/expenseType/store', 'Company\Admin\ExpenseTypeController@store')->name('expense_type.store');

                        Route::post('/expenseType/update/{id}', 'Company\Admin\ExpenseTypeController@update')->name('expense_type.update');

                        Route::post('/expenseType/destroy/{id}', 'Company\Admin\ExpenseTypeController@destroy')->name('expense_type.destroy');

                    });



                //Meetings

                // Route::get('/meeting', 'Company\Admin\MeetingController@index')->name('meeting');

                // Route::get('/meeting/create', 'Company\Admin\MeetingController@create')->name('meeting.create');

                // Route::post('/meeting/create', 'Company\Admin\MeetingController@store')->name('meeting.store');

                // Route::get('/meeting/{id}', 'Company\Admin\MeetingController@show')->name('meeting.show');

                // Route::get('meeting/{id}/edit', 'Company\Admin\MeetingController@edit')->name('meeting.edit');

                // Route::post('meeting/changeStatus', 'Company\Admin\MeetingController@changeStatus')->name('meeting.changeStatus');

                // Route::patch('meeting/update/{id}', 'Company\Admin\MeetingController@update')->name('meeting.update');

                // Route::delete('/meeting/{id}', 'Company\Admin\MeetingController@destroy')->name('meeting.destroy');

                //



                Route::group(

                    [

                        'middleware' => ['is_enabled:notes'],

                    ],

                    function () {

                        //Notes

                        Route::get('/notes', 'Company\Admin\NoteController@index')->name('notes');

                        Route::post('/notes/fetch-data', 'Company\Admin\NoteController@dataTable')->name('notes.dataTable');

                        Route::get('/notes/create', 'Company\Admin\NoteController@create')->name('notes.create');

                        Route::get('/notes/{id}', 'Company\Admin\NoteController@show')->name('notes.show');

                        Route::post('/notes/store', 'Company\Admin\NoteController@store')->name('notes.store');

                        Route::get('/notes/{id}/edit', 'Company\Admin\NoteController@edit')->name('notes.edit');

                        Route::patch('/notes/update/{id}', 'Company\Admin\NoteController@update')->name('notes.update');

                        Route::delete('/notes/{id}', 'Company\Admin\NoteController@destroy')->name('notes.destroy');

                    });



                Route::group(

                    [

                        'middleware' => ['is_enabled:collections'],

                    ],

                    function () {

                        //Collections

                        Route::get('/collection', 'Company\Admin\CollectionController@index')->name('collection');

                        Route::post('/collection', 'Company\Admin\CollectionController@ajaxDatatable')->name('collection.ajaxDatatable');

                        Route::post('/collection/custompdfdexport', 'Company\Admin\CollectionController@custompdfdexport')->name('collection.customPdfExport');

                        Route::get('/collection/create', 'Company\Admin\CollectionController@create')->name('collection.create');

                        Route::post('/collection/create', 'Company\Admin\CollectionController@store')->name('collection.store');

                        Route::get('/collection/{id}', 'Company\Admin\CollectionController@show')->name('collection.show');

                        Route::get('collection/{id}/edit', 'Company\Admin\CollectionController@edit')->name('collection.edit');

                        Route::patch('collection/update/{id}', 'Company\Admin\CollectionController@update')->name('collection.update');

                        Route::get('collection/{id}/download', 'Company\Admin\CollectionController@download')->name('collection.download');

                        Route::delete('/collection/{id}', 'Company\Admin\CollectionController@destroy')->name('collection.destroy');



                        //Cheque

                        Route::resource('/cheque', 'Company\Admin\ChequeController');

                        Route::post('/cheque', 'Company\Admin\ChequeController@ajaxDatatable')->name('cheque.ajaxDatatable');

                        Route::post('/cheque/custompdfdexport', 'Company\Admin\ChequeController@custompdfdexport')->name('cheque.customPdfExport');

                        Route::post('/cheque/updatestatus', 'Company\Admin\ChequeController@updateStatus')->name('chequeUpdateStatus');

                    });



                //Task

                Route::get('/task', 'Company\Admin\TaskController@index')->name('task');

                Route::get('/task/create', 'Company\Admin\TaskController@create')->name('task.create');

                Route::post('/task/create', 'Company\Admin\TaskController@store')->name('task.store');

                Route::get('/task/{id}', 'Company\Admin\TaskController@show')->name('task.show');

                Route::get('task/{id}/edit', 'Company\Admin\TaskController@edit')->name('task.edit');

                Route::post('task/changeStatus', 'Company\Admin\TaskController@changeStatus')->name('task.changeStatus');

                Route::patch('task/{id}', 'Company\Admin\TaskController@update')->name('task.update');

                Route::delete('/task/{id}', 'Company\Admin\TaskController@destroy')->name('task.destroy');



                Route::group(

                    [

                        'middleware' => ['is_enabled:orders'],

                    ],

                    function () {



                        Route::get('/updatetaxonorders', 'Company\Admin\OrderController@updatetaxonorders');

                        // Remove above route after going live

                        Route::get('/order', 'Company\Admin\OrderController@index')->name('order');

                        Route::post('/order/ajaxdatatable', 'Company\Admin\OrderController@ajaxDatatable')->name('order.ajaxDatatable');

                        Route::post('/order/custompdfdexport', 'Company\Admin\OrderController@customPdfExport')->name('order.customPdfExport');

                        Route::get('/order/create', 'Company\Admin\OrderController@create')->name('order.create');

                        Route::post('/order/create', 'Company\Admin\OrderController@store')->name('order.store');

                        Route::get('/order/create/orderTo', 'Company\Admin\OrderController@orderTo')->name('orderTo');

                        Route::post('/order/suffCreditLimit', 'Company\Admin\OrderController@suffCreditLimit')->name('order.suffCreditLimit');

                        Route::get('/order/{id}', 'Company\Admin\OrderController@show')->name('order.show');

                        Route::get('/order/{id}/edit', 'Company\Admin\OrderController@editOrder')->name('order.edit');

                        Route::patch('/order/{id}', 'Company\Admin\OrderController@updateOrder')->name('order.update');

                        Route::delete('/order/{id}', 'Company\Admin\OrderController@remove')->name('order.destroy');

                        Route::post('order/changeDeliveryStatus', 'Company\Admin\OrderController@changeDeliveryStatus')->name('order.changeDeliveryStatus');

                        Route::get('/order/{id}/download', 'Company\Admin\OrderController@download')->name('order.download');



                        Route::get('/order/{id}/new-invoice', 'Company\Admin\OrderController@newInvoice')->name('order.download.newInvoice');

                        Route::post('/order/{id}', 'Company\Admin\OrderController@mail')->name('order.mail');

                        Route::delete('/editOrder/{id}', 'Company\Admin\OrderController@deleteOrderProducts')->name('order.deleteOrderProducts');

                        Route::delete('/updateorderamount/{id}', 'Company\Admin\OrderController@updateOrderAmount')->name('order.updateorderamount');

                        Route::get('/orderstatus', 'Company\Admin\OrderStatusController@index')->name('orderstatus');

                        Route::post('/orderstatus/store', 'Company\Admin\OrderStatusController@store')->name('orderstatus.store');

                        Route::post('/orderstatus/update', 'Company\Admin\OrderStatusController@update')->name('orderstatus.update');

                        Route::post('/orderstatus/delete', 'Company\Admin\OrderStatusController@delete')->name('orderstatus.delete');

                        Route::post('/orders/massorderactions', 'Company\Admin\OrderController@massActions')->name('order.massActions');





                    });



                //Attendance

                Route::get('/attendance', 'Company\Admin\AttendanceController@index')->name('attendance');

                Route::get('/attendance/create', 'Company\Admin\AttendanceController@create')->name('attendance.create');

                Route::post('/attendance/create', 'Company\Admin\AttendanceController@store')->name('attendance.store');

                //Route::get('/attendance/{id}', 'Company\Admin\AttendanceController@show')->name('attendance.show');

                Route::get('/attendance/{id}/{date}', 'Company\Admin\AttendanceController@show')->name('attendance.show');

                Route::get('attendance/{id}/edit', 'Company\Admin\AttendanceController@edit')->name('attendance.edit');

                Route::patch('attendance/update/{id}', 'Company\Admin\AttendanceController@update')->name('attendance.update');

                Route::delete('/attendance/{id}', 'Company\Admin\AttendanceController@destroy')->name('attendance.destroy');

















              Route::group(

                [

                  'middleware' => ['is_enabled:targets']

                ], function(){ 

              

                Route::get('/salesmantarget', 'Company\Admin\TargetSalesmanController@salesmanTargetList')->name('salesmantarget');

                Route::post('/salesmantargetdt', 'Company\Admin\TargetSalesmanController@salesmanTargetListDT')->name('salesmantargetdt');





                Route::post('/salesmantarget', 'Company\Admin\TargetSalesmanController@targetsForDatatable')->name('salesmantarget.fetchdata');

                Route::get('/salesmantargetlist/{id}/edit', 'Company\Admin\TargetSalesmanController@targetSalesmanListsEdit')->name('salesmanindivtargetlist.edit');

                Route::get('/salesmantargetlist/{id}/edit', 'Company\Admin\TargetSalesmanController@targetSalesmanListsEdit2')->name('salesmanindivtargetlist.modify');

                Route::post('/salesmantargetlistdata', 'Company\Admin\TargetSalesmanController@targetSalesmanDats')->name('salesmantargetsdata');

              

                Route::patch('/salesmantargetlistdata/{id}', 'Company\Admin\TargetSalesmanController@targetSalesmanChange')->name('salesmantargetsdata.changeconf');

                Route::post('/salesmantargetsingleemp/{id}', 'Company\Admin\TargetSalesmanController@targetCreateAssignSingleEmp')->name('salesmantargetsdata.crtsngemp');

              

                Route::patch('/salesmantargetlist/update/{id}', 'Company\Admin\TargetSalesmanController@targetSalesmanListsUpdate')->name('salesmanindivtargetlist.update');

                

                Route::get('/salesmantargetshow', 'Company\Admin\TargetSalesmanController@targetlistShow')->name('salesmantargetlist.show');

                Route::get('/salesmantargetedit/{id}/edit', 'Company\Admin\TargetSalesmanController@targetsListsEdit')->name('salesmantargetlist.edit');

                Route::patch('/salesmantarget/update/{id}', 'Company\Admin\TargetSalesmanController@update')->name('salesmantargetlist.update');

                Route::post('/salesmantarget/delete', 'Company\Admin\TargetSalesmanController@targetDelete')->name('salesmanindivtargetlist.delete');

              

                Route::post('/salesmantargetcrtasgn/', 'Company\Admin\TargetSalesmanController@targetCreateAssign')->name('salesmantargetcrtasgn');

                

                Route::get('/salesmantargetnew/create', 'Company\Admin\TargetSalesmanController@create')->name('salesmantarget.create');

                Route::post('/salesmantargetnew/create', 'Company\Admin\TargetSalesmanController@store')->name('salesmantarget.store');

              

                Route::get('/salesmantargetassign', 'Company\Admin\TargetSalesmanController@targetAssign')->name('salesmantarget.set');

                Route::post('/salesmantargetassign', 'Company\Admin\TargetSalesmanController@targetAssignSave')->name('salesmantarget.setconfirm');

 

                // Route::get('/salesmantargetreport/{yearmonth?}/{id?}/{lctype?}', 'Company\Admin\TargetSalesmanController@genReport')->middleware('is_enabled:targets_rep')->name('salesmantargetreport');



                Route::get('/salesmantargetreport', 'Company\Admin\TargetSalesmanController@genReport')->middleware('is_enabled:targets_rep')->name('salesmantargetreport');

                Route::post('/salesmantargetreportdt', 'Company\Admin\TargetSalesmanController@genReportDT')->name('salesmantargetreportdt');

                Route::post('/salesmantargetreporpdf', 'Company\Admin\TargetSalesmanController@custompdfdexport')->name('salesmantargetreporpdf');

                Route::post('/salesmantargetreporpdf_tg', 'Company\Admin\TargetSalesmanController@custompdfdexport_target')->name('salesmantargetreporpdf_tg');



              

                Route::post('/salesmantargethistory/{id?}', 'Company\Admin\TargetSalesmanController@salesmanTargetHistory')->name('salesmantargethistory');

                Route::post('/salesmanindivtargethistory/{id?}', 'Company\Admin\TargetSalesmanController@targetUpdHistory')->name('salesmanindivtargethistory');  

                

                

                





              }

            );





            





            //Dashboard routes

            Route::group(['prefix' => 'dashboard'], function(){ 

              

              Route::post('/tickerCountDatas', 'Company\Admin\DashboardController@tickerCountDatas')->name('fetchtickercountdata');

              Route::post('/infoBarsDatas', 'Company\Admin\DashboardController@infoBarsDatas')->name('fetchinfobarsdatas');

              

              Route::post('/getparty', 'Company\Admin\DashboardController@newPartiesAdded')->name('fetchnewparties');

              

              Route::post('/topproducts_order', 'Company\Admin\DashboardController@topProductsOrder')->name('fetchtopproductsorder');

              Route::post('/topbrands_order', 'Company\Admin\DashboardController@topBrandsOrder')->name('fetchtopbrandsorder');

              Route::post('/topcategories_order', 'Company\Admin\DashboardController@topCategoriesOrder')->name('fetchtopcategoriesorder');

              Route::post('/collection_qty', 'Company\Admin\DashboardController@collectionQty')->name('fetchcollectionqty');

              Route::post('/topperformbeats_order', 'Company\Admin\DashboardController@topPerformBeatsOrder')->name('fetchtopperformbeatsorder');

              

              Route::post('/topproducts_amount', 'Company\Admin\DashboardController@topProductsAmount')->name('fetchtopproductsamount');

              Route::post('/topbrands_amount', 'Company\Admin\DashboardController@topBrandsAmount')->name('fetchtopbrandsamount');

              Route::post('/topcat_amount', 'Company\Admin\DashboardController@topCategoriesAmount')->name('fetchtopcategoriesamount');

              Route::post('/collection_amount', 'Company\Admin\DashboardController@collectionAmount')->name('fetchcollectionamt');

              Route::post('/topperformbeats_amount', 'Company\Admin\DashboardController@topPerformBeatsAmt')->name('fetchtopperformbeatsamt');

              Route::post('/topperformbeats_collamount', 'Company\Admin\DashboardController@topPerformBeatsCollAmt')->name('fetchtopperformbeatscollamt');

              

              Route::post('/timespentvisit', 'Company\Admin\DashboardController@timeSpentVisit')->name('fetchtimespentvisit');

              Route::post('/totalorder', 'Company\Admin\DashboardController@totalOrder')->name('fetchtotal_order');

              Route::post('/zeroorder', 'Company\Admin\DashboardController@zeroOrder')->name('fetchzero_order');

              Route::post('/productivecalls', 'Company\Admin\DashboardController@prodCalls')->name('fetchproductivecalls');

              Route::post('/totalcalls', 'Company\Admin\DashboardController@totalCalls')->name('fetchtotalcalls');

              Route::post('/totalvisits', 'Company\Admin\DashboardController@totalVisits')->name('fetchtotalvisits');

              Route::post('/totprodsold', 'Company\Admin\DashboardController@totProdSold')->name('fetchtotprodsold');

              Route::post('/totpartiesadded', 'Company\Admin\DashboardController@totalPartiesAdded')->name('fetchtotpartiesadded');

              Route::post('/totalcollections', 'Company\Admin\DashboardController@totalCollections')->name('fetchtotcollections');

              Route::post('/totalorderamount', 'Company\Admin\DashboardController@totalOrderAmount')->name('fetchtotalorderamt');

              Route::post('/totalcollectionamount', 'Company\Admin\DashboardController@totalCollAmt')->name('fetchcollectionamt');

              

              Route::post('/toppartiesbycollamt', 'Company\Admin\DashboardController@topPartiesByCollAmt')->name('fetchtoppartiescollamt');

              Route::post('/toppartiesbyordervalue', 'Company\Admin\DashboardController@topPartiesByOrderValue')->name('fetchtoppartiesordvalue');

              Route::post('/topexpenses', 'Company\Admin\DashboardController@topExpenses')->name('fetchtopexpenses');

              

              Route::post('/topoutlets', 'Company\Admin\DashboardController@topOutlets')->name('fetchtopoutlets');

              Route::post('/totalzeroorder', 'Company\Admin\DashboardController@totalZeroOrderPie')->name('fetchtotalzeroorder');

              Route::post('/targetsnooforder', 'Company\Admin\DashboardController@targetsGaugeData')->name('fetchtargetsnoorder');

              Route::post('/visitlocations', 'Company\Admin\DashboardController@clientVisitLocations')->name('fetchtvisitlocations');



              

              

                              

            });





























                Route::group(

                    [

                        'middleware' => ['is_enabled:activities'],

                    ],

                    function () {

                        //Routes for Activities Section

                        Route::resource('/activities', 'Company\Admin\ActivityController');

                        // Route::post('activities/mark-completed', 'Company\Admin\ActivityController@ajaxResponse')->name('activities.mark-completed');

                        Route::post('activities/update/mark', 'Company\Admin\ActivityController@updateMark')->name('activities.updateMark');

                        Route::post('/activities/ajaxTable', 'Company\Admin\ActivityController@ajaxTable')->name('activities.ajaxTable');

                        Route::post('/activities/custompdfdexport', 'Company\Admin\ActivityController@custompdfdexport')->name('activities.customPdfExport');

                        Route::resource('/activities-type', 'Company\Admin\ActivityTypeController');

                        Route::post('/activities-type/custompdfdexport', 'Company\Admin\ActivityTypeController@custompdfdexport')->name('activityType.customPdfExport');

                        Route::post('/activities-type/update', 'Company\Admin\ActivityTypeController@updateActivityType')->name('activityType.update');

                        Route::post('/activities-type/delete', 'Company\Admin\ActivityTypeController@deleteActivityType')->name('activityType.delete');



                        Route::resource('/activities-priority', 'Company\Admin\ActivityPriorityController');

                        Route::post('/activities-priority/custompdfdexport', 'Company\Admin\ActivityTypeController@custompdfdexport')->name('activityType.customPdfExport');



                        Route::post('/activities-priority/update', 'Company\Admin\ActivityPriorityController@updateActivityPriority')->name('activityPriority.update');

                        Route::post('/activities-priority/delete', 'Company\Admin\ActivityPriorityController@deleteActivityPriority')->name('activityPriority.delete');

                    });



                //Holidays

                Route::resource('/holidays', 'Company\Admin\HolidayController');

                Route::post('/holidays/getCalendar', 'Company\Admin\HolidayController@getCalendar')->name('holidays.getCalendar');

                Route::post('/holidays/edit', 'Company\Admin\HolidayController@edit')->name('holidays.edit');

                Route::post('/holidays/delete', 'Company\Admin\HolidayController@delete')->name('holidays.delete');

                Route::post('/holidays/populate', 'Company\Admin\HolidayController@populate')->name('holidays.populate');



                Route::group(

                    [

                        'middleware' => ['is_enabled:leaves'],

                    ],

                    function () {

                        //Leave

                        Route::get('/leave', 'Company\Admin\LeaveController@index')->name('leave');

                        Route::post('/leave', 'Company\Admin\LeaveController@ajaxTable')->name('leave.ajaxTable');

                        Route::post('/leave/custompdfdexport', 'Company\Admin\LeaveController@custompdfdexport')->name('leaves.customPdfExport');

                        Route::get('/leave/create', 'Company\Admin\LeaveController@create')->name('leave.create');

                        Route::post('/leave/create', 'Company\Admin\LeaveController@store')->name('leave.store');

                        Route::get('/leave/{id}', 'Company\Admin\LeaveController@show')->name('leave.show');

                        Route::get('leave/{id}/edit', 'Company\Admin\LeaveController@edit')->name('leave.edit');

                        Route::post('leave/changeStatus', 'Company\Admin\LeaveController@changeStatus')->name('leave.changeStatus');

                        Route::patch('leave/{id}', 'Company\Admin\LeaveController@update')->name('leave.update');

                        Route::delete('/leave/{id}', 'Company\Admin\LeaveController@destroy')->name('leave.destroy');

                        Route::post('/leave/approve', 'Company\Admin\LeaveController@approve')->name('leave.approve');



                        //leaveType

                        Route::post('/leaveType/store', 'Company\Admin\LeaveTypeController@store')->name('leavetype.store');

                        Route::post('/leaveType/update/{id}', 'Company\Admin\LeaveTypeController@update')->name('leavetype.update');

                        Route::post('/leaveType/destroy/{id}', 'Company\Admin\LeaveTypeController@destroy')->name('leavetype.destroy');





                    });



                Route::resource('/returnreason', 'Company\Admin\ReturnReasonController');

                Route::post('/returnreason/update', 'Company\Admin\ReturnReasonController@updateReturnReason')->name('returnreason.update');

                Route::post('/returnreason/delete', 'Company\Admin\ReturnReasonController@deleteReturnReason')->name('returnreason.delete');



                Route::group(

                    [

                        'middleware' => ['is_enabled:tourplans'],

                    ],

                    function () {

                        //Tourplan

                        Route::get('/tourplans', 'Company\Admin\TourPlanController@index')->name('tours');

                        Route::post('/tourplans', 'Company\Admin\TourPlanController@ajaxDatatable')->name('tourplan.ajaxDatatable');

                        Route::post('/tourplans/custompdfdexport', 'Company\Admin\TourPlanController@custompdfdexport')->name('tourplan.customPdfExport');

                        Route::post('/tourplans/store', 'Company\Admin\TourPlanController@store')->name('tourplan.store');

                        Route::get('/tourplans/{id}', 'Company\Admin\TourPlanController@show')->name('tours.show');

                        Route::patch('/tourplans/{id}', 'Company\Admin\TourPlanController@update')->name('tourplan.update');

                        Route::get('/tourplans/{id}/download', 'Company\Admin\TourPlanController@download')->name('tours.download');

                        Route::post('tourplans/changeStatus', 'Company\Admin\TourPlanController@changeStatus')->name('leave.changeStatus');

                        Route::delete('/tourplans/{id}', 'Company\Admin\TourPlanController@destroy')->name('tourplan.destroy');

                    });



                Route::group(

                    [

                        'middleware' => ['is_enabled:remarks'],

                    ],

                    function () {

                        //Dayremark

                        Route::get('/dayremarks', 'Company\Admin\DayRemarkController@index')->name('dayremarks');

                        Route::get('/dayremarks/{id}', 'Company\Admin\DayRemarkController@show')->name('remark.show');

                        Route::post('/dayremarks', 'Company\Admin\DayRemarkController@ajaxDatatable')->name('dayremarks.ajaxDatatable');

                        Route::post('/dayremarks/custompdfdexport', 'Company\Admin\DayRemarkController@custompdfdexport')->name('dayremarks.customPdfExport');

                        Route::post('/dayremarks/store', 'Company\Admin\DayRemarkController@store')->name('dayremarks.store');

                        Route::post('/dayremarks/ajaxupdate', 'Company\Admin\DayRemarkController@update')->name('dayremarks.ajaxupdate');

                        Route::post('/dayremarks/ajaxdelete', 'Company\Admin\DayRemarkController@destroy')->name('dayremarks.ajaxdelete');

                    });





                // Route::get('/tourplans/create', 'Company\Admin\TourPlanController@create')->name('tours.create');

                // Route::post('/leave/create', 'Company\Admin\LeaveController@store')->name('leave.store');

                // Route::get('leave/{id}/edit', 'Company\Admin\LeaveController@edit')->name('leave.edit');

                // Route::patch('leave/{id}', 'Company\Admin\LeaveController@update')->name('leave.update');

                // Route::delete('/leave/{id}', 'Company\Admin\LeaveController@destroy')->name('leave.destroy');

                // Route::post('/leave/approve', 'Company\Admin\LeaveController@approve')->name('leave.approve');



                //Follow-up

                Route::get('/followup', 'Company\Admin\FollowupController@index')->name('followup');

                Route::get('/followup/create', 'Company\Admin\FollowupController@create')->name('followup.create');

                Route::post('/followup/create', 'Company\Admin\FollowupController@store')->name('followup.store');

                Route::get('/followup/{id}', 'Company\Admin\FollowupController@show')->name('followup.show');

                Route::get('followup/{id}/edit', 'Company\Admin\FollowupController@edit')->name('followup.edit');

                Route::patch('followup/{id}', 'Company\Admin\FollowupController@update')->name('followup.update');

                Route::delete('/followup/{id}', 'Company\Admin\FollowupController@destroy')->name('followup.destroy');

                //Bank

                Route::get('/bank', 'Company\Admin\BankController@index')->name('bank');

                Route::get('/bank/create', 'Company\Admin\BankController@create')->name('bank.create');

                Route::post('/bank/create', 'Company\Admin\BankController@store')->name('bank.store');

                Route::get('/bank/{id}', 'Company\Admin\BankController@show')->name('bank.show');

                Route::get('bank/{id}/edit', 'Company\Admin\BankController@edit')->name('bank.edit');

                Route::post('bank/update/{id}', 'Company\Admin\BankController@update')->name('bank.update');

                Route::post('/bank/delete/{id}', 'Company\Admin\BankController@destroy')->name('bank.destroy');



                //Designation

                Route::get('/designation', 'Company\Admin\DesignationController@index')->name('designation');

                Route::get('/designation/create', 'Company\Admin\DesignationController@create')->name('designation.create');

                Route::post('/designation/create', 'Company\Admin\DesignationController@store')->name('designation.store');

                Route::get('/designation/{id}', 'Company\Admin\DesignationController@show')->name('designation.show');

                Route::get('designation/{id}/edit', 'Company\Admin\DesignationController@edit')->name('designation.edit');

                Route::post('designation/{id}/update', 'Company\Admin\DesignationController@update')->name('designation.update');

                Route::post('/designation/delete/{id}', 'Company\Admin\DesignationController@destroy')->name('designation.destroy');



                //Roles

                Route::resource('roles', 'Company\Admin\RoleController');

                Route::post('roles/update/{id}', 'Company\Admin\RoleController@update')->name('role.update');

                Route::post('roles/delete/{id}', 'Company\Admin\RoleController@destroy')->name('role.destroy');

                Route::post('roles/permission/update', 'Company\Admin\RoleController@updatePermission')->name('role.permission.update');



                Route::get('/beat', 'Company\Admin\BeatController@index')->name('beat');

                Route::get('beat/assignedParties', 'Company\Admin\BeatController@assignedParties')->name('beat.assignedParties');

                Route::get('beat/fetchCityParties', 'Company\Admin\BeatController@fetchCityParties')->name('beat.fetchCityParties');

                Route::get('/beat/create', 'Company\Admin\BeatController@create')->name('beat.create');

                Route::post('/beat/create', 'Company\Admin\BeatController@store')->name('beat.store');

                Route::get('/beat/{id}', 'Company\Admin\BeatController@show')->name('beat.show');

                Route::get('beat/{id}/edit', 'Company\Admin\BeatController@edit')->name('beat.edit');

                Route::post('beat/{id}/update', 'Company\Admin\BeatController@update')->name('beat.update');

                Route::post('/beat/delete/{id}', 'Company\Admin\BeatController@destroy')->name('beat.destroy');

                Route::group(

                    [

                        'middleware' => ['is_enabled:beatplans'],

                    ],

                    function () { 

                        //Beatplans

                        Route::get('/beatplan', 'Company\Admin\BeatPlanController@index')->name('beatplan');



                        Route::post('/beatplan/getCalendar', 'Company\Admin\BeatPlanController@getCalendar')->name('beatplans.getCalendar');



                        Route::post('/beatplan/fetcheachplan', 'Company\Admin\BeatPlanController@fetcheachplan')->name('beatplan.fetcheachplan');

                        Route::get('/beatplan/appendElement', 'Company\Admin\BeatPlanController@appendElement')->name('beatplan.appendElement.index');

                        Route::post('/beatplan/fetchpartylist', 'Company\Admin\BeatPlanController@fetchpartylist')->name('beatplan.fetchpartylist');

                        Route::get('/beatplan/monthplans', 'Company\Admin\BeatPlanController@monthplans')->name('beatplan.monthplans');



                        Route::post('/beatplan/create', 'Company\Admin\BeatPlanController@store')->name('beatplan.store');



                        Route::get('/beatplan/{id}', 'Company\Admin\BeatPlanController@detail')->name('beatplan.detail');

                        Route::get('/beatplan/show/{id}', 'Company\Admin\BeatPlanController@show')->name('beatplan.show');



                        Route::post('/beatplan/delete', 'Company\Admin\BeatPlanController@delete')->name('beatplan.delete');



                        Route::get('/beatplan/{id}/{beat_id}', 'Company\Admin\BeatPlanController@edit')->name('beatplan.editSingle');



                        Route::post('/beatplan/{id}', 'Company\Admin\BeatPlanController@update')->name('beatplan.updateSingle');

                    });



                //Leads

                Route::get('/lead', 'Company\Admin\LeadController@index')->name('lead');

                Route::get('/lead/create', 'Company\Admin\LeadController@create')->name('lead.create');

                Route::post('/lead/create', 'Company\Admin\LeadController@store')->name('lead.store');

                Route::get('/lead/{id}', 'Company\Admin\LeadController@show')->name('lead.show');

                Route::get('lead/{id}/edit', 'Company\Admin\LeadController@edit')->name('lead.edit');

                Route::patch('lead/{id}', 'Company\Admin\LeadController@update')->name('lead.update');

                Route::delete('/lead/{id}', 'Company\Admin\LeadController@destroy')->name('lead.destroy');

                Route::post('/lead/approve', 'Company\Admin\LeadController@approve')->name('lead.approve');

                  
                Route::group(

                  [

                      'middleware' => ['is_enabled:import'],

                  ],

                  function () { 


                Route::get('import/employees', 'Company\Admin\ImportController@importEmployees')->name('import.employees');
                Route::post('import/addEmployees', 'Company\Admin\ImportController@addEmployees')->name('import.addemployees');


                Route::get('import/products', 'Company\Admin\ImportController@importProducts')->name('import.products')->middleware('is_enabled:products');

                Route::post('import/addProducts', 'Company\Admin\ImportController@addProducts')->name('import.addproducts')->middleware('is_enabled:products');



                Route::get('import/parties', 'Company\Admin\ImportController@importClients')->name('import.parties')->middleware('is_enabled:clients');

                Route::post('import/addClients', 'Company\Admin\ImportController@addClients')->name('import.addclients')->middleware('is_enabled:clients');



                 Route::get('import/partiesoutstanding', 'Company\Admin\ImportController@importOutstanding')->name('import.partiesoutstanding')->middleware('is_enabled:clients');

                Route::post('import/addClientsOutstanding', 'Company\Admin\ImportController@addOutstanding')->name('import.addclientsoutstanding')->middleware('is_enabled:clients');



                Route::get('import/index', 'Company\Admin\ImportController@index')->name('import.index');

              });



                //import as excel for Product

                // Route::get('/import/product', 'Company\Admin\ExcelController@product')->name('import.product');

                // Route::post('/import/product/excel', 'Company\Admin\ExcelController@importProduct')->name('import.product.excel');



                // // for employees import

                // Route::get('/import/employee', 'Company\Admin\ExcelController@employee')->name('import.employee');

                // Route::post('/import/employee/excel', 'Company\Admin\ExcelController@importEmpoyee')->name('import.employee.excel');



                // // for party import

                // Route::get('/import/client', 'Company\Admin\ExcelController@client')->name('import.client');

                // Route::post('/import/client/excel', 'Company\Admin\ExcelController@importClient')->name('import.client.excel');



                //Reports

                Route::get('/reports/salesmangpspath', 'Company\Admin\ReportController@employeerattendancegps')->name('employeerattendancegps');

                Route::post('/reports/salesmangpspath', 'Company\Admin\ReportController@salesmanGPSPathDatatable')->name('salesmangpspath.ajaxDatatable');

                Route::get('/reports/monthlyattendancereport', 'Company\Admin\ReportController@monthlyattendancereport')->name('monthlyattendancereport');

                Route::post('/reports/monthlyattendancereport', 'Company\Admin\ReportController@getmonthlyattendancereport')->name('getmonthlyattendancereport');

                Route::get('/reports/checkin-checkoutlocationreport', 'Company\Admin\ReportController@checkin_checkoutlocationreport')->name('viewattendancereport');

                Route::post('/reports/checkin-checkoutlocationreport', 'Company\Admin\ReportController@checkin_checkoutReportDataTable')->name('checkin_checkoutReportDataTable');

                Route::get('/reports/dailysalesorderreport', 'Company\Admin\ReportController@dailysalesorderreport')->name('dailysalesreport');

                Route::post('/reports/dailysalesorderreport', 'Company\Admin\ReportController@dailysalesorderreportDataTable')->name('dailysalesorderreportDataTable');

                Route::get('/reports/zeroorderlist', 'Company\Admin\ReportController@zeroorderlist')->name('noorders');

                Route::post('/reports/zeroorderlist', 'Company\Admin\ReportController@zeroorderlistDataTable')->name('noorders.zeroorderlistDataTable');

                Route::get('/reports/dailysalesmanorderreport', 'Company\Admin\ReportController@dailysalesmanorderreport')->name('dailysalesreportbysalesman');

                Route::get('/reports/dailyemployeereport', 'Company\Admin\ReportController@dailyemployeereport')->name('dailyemployeereport');

                Route::post('/reports/dailyemployeereport', 'Company\Admin\ReportController@dailyemployeereportDataTable')->name('dailyemployeereportDataTable');

                Route::get('/reports/dailypartyreport', 'Company\Admin\ReportController@dailypartyreport')->name('dailyordercollectionreport');

                Route::post('/reports/dailypartyreport', 'Company\Admin\ReportController@dailypartyreportDataTable')->name('dailypartyreportDataTable');



                Route::post('/reports/custompdfexports', 'Company\Admin\ReportController@custompdfdexport')->name('reports.customPdfExport');



                // Route::get('/reports/monthlyemployeereport', 'Company\Admin\ReportController@monthlyemployeereport')->name('monthlyemployeereport');



                Route::get('/reports/getlocation', 'Company\Admin\ReportController@getlocation')->name('reports.location');

                Route::get('/reports/getFileLocation', 'Company\Admin\ReportController@getFileLocation')->name('reports.filelocation');

                Route::get('/reports/getrawlocation', 'Company\Admin\ReportController@getRawLocation')->name('reports.getrawlocation');

                Route::get('/reports/getPartyLocation', 'Company\Admin\ReportController@getPartyLocation')->name('reports.partylocation');

                Route::get('/reports/getAllRecentlocation', 'Company\Admin\ReportController@getAllRecentlocation')->name('reports.recentlocation');

                Route::get('/reports/updatelocation', 'Company\Admin\ReportController@updatelocation')->name('reports.updatelocation');

                Route::get('/reports/getdistance', 'Company\Admin\ReportController@getdistance')->name('reports.distance');

                Route::get('/reports/getdistanceold', 'Company\Admin\ReportController@getdistanceold')->name('reports.distanceold');

                // Route::get('/reports/dailyattendancereport', 'Company\Admin\ReportController@attendancereport')->name('dailyattendancereport');

                // Route::get('/reports/monthlyordercollectionreport', 'Company\Admin\ReportController@monthlyordercollectionreport')->name('monthlyordercollectionreport');

                // Route::get('/reports/getreportdata', 'Company\Admin\ReportController@getreportdata')->name('reports.getreportdata');//ajax

                // Route::get('/reports/reportgenerator', 'Company\Admin\ReportController@reportgenerator')->name('reportgenerator');

                Route::get('/reports/searchdailysalesreportbysalesman', 'Company\Admin\ReportController@searchdailysalesreportbysalesman')->name('searchdailysalesreportbysalesman');



                Route::get('/reports/generateorderreport', 'Company\Admin\ReportController@generateorderreport')->name('generateorderreport');

                // Route::get('/reports/generatecustomreport', 'Company\Admin\ReportController@generatecustomreport')->name('generatecustomreport');

                // Route::post('/reports/generatnewreport', 'Company\Admin\ReportController@generatnewreport')->name('generatnewreport');

                // Route::post('/reports/collectionreport', 'Company\Admin\ReportController@collectionreport')->name('collectionreport.filter');



                // Route::get('/reports/employeereport', 'Company\Admin\ReportController@employeereport')->name('employeereport');

                // Route::post('/reports/employeereport', 'Company\Admin\ReportController@employeereport')->name('employeereport.filter');



                Route::get('/reports/getlocationbycompany', 'Company\Admin\ReportController@getlocationbycompany')->name('reports.locationbycompany');

                Route::get('/reports/gettotalhour', 'Company\Admin\ReportController@gettotalhour')->name('reports.totalhours');

                Route::get('/reports/getWorkedHourDetails', 'Company\Admin\ReportController@getWorkedHourDetails')->name('reports.gethoursdetails');

                Route::get('/reports/getdistancetravelled', 'Company\Admin\ReportController@getDistanceTravelled')->name('reports.getdistancetravelled');

                Route::get('/reports/getdistancetravelled2', 'Company\Admin\ReportController@getDistanceTravelled2')->name('reports.getdistancetravelled2');



                // Route::get('/reports/meetingreport', 'Company\Admin\ReportController@meetingreport')->name('meetingreport');

                // Route::post('/reports/meetingreport', 'Company\Admin\ReportController@meetingreport')->name('meetingreport.filter');



                // Route::get('/reports/companyreach', 'Company\Admin\ReportController@companyreach')->name('companyreach');

                // Route::get('/reports/employeetracking', 'Company\Admin\ReportController@employeetracking')->name('employeetracking');





                // Ageing Reports

                Route::get('/reports/ageingreport', 'Company\Admin\AgeingController@index')->name('ageingReport');

                Route::post('/reports/ageingreport/custompdfdexport', 'Company\Admin\AgeingController@custompdfdexport')->name('ageing.custompdfexports');

                Route::post('/reports/ajaxdatatableageingreport', 'Company\Admin\AgeingController@ajaxDatatable')->name('ageing.ajaxDatatable');

                Route::get('/reports/ageingbreakdownreport', 'Company\Admin\AgeingController@indexBreakdown')->name('ageingBreakdownReport');

                Route::post('/reports/ageingbreakdownreport/custompdfdexport', 'Company\Admin\AgeingController@custombreakdownpdfdexport')->name('ageing.custombreakdownpdfexports');

                Route::post('/reports/ajaxdatatableageingbreakdownreport', 'Company\Admin\AgeingController@ajaxBreakdownDatatable')->name('ageing.ajaxBreakdownDatatable');



                



                /***

                 * ReportNewController Reports

                 */

                Route::get('/reports/assignadminreports', 'Company\Admin\ReportNewController@updateReportGeneratedId')->name('assignadminreports');

                Route::get('/reports/getsalesreportdaily', 'Company\Admin\ReportController@getsalesreportdaily')->name('getsalesreportdaily');

                Route::get('/todayattendancereport', 'Company\Admin\ReportNewController@todayattendancereport')->name('todayattendancereport');

                Route::get('/reports/orderreports', 'Company\Admin\ReportNewController@orderreports')->name('variantreports');

                Route::post('/reports/orderreports', 'Company\Admin\ReportNewController@generateorderreports')->name('getvariantreports');

                Route::post('/reports/orderreportsdt', 'Company\Admin\ReportNewController@orderreportsDT')->name('orderreportsdt');



                Route::get('/reports/orderreports/salesmanandpartylist', 'Company\Admin\ReportNewController@salesmanandpartylist')->name('fetchedrecords');



                Route::get('/reports/productsalesorderreport', 'Company\Admin\ReportNewController@productsalesreports')->name('viewproductsalesreports');

                Route::get('/reports/fetchpartylist', 'Company\Admin\ReportNewController@fetchpartylist')->name('fetchpartylist');

                Route::post('/reports/productsalesorderreport', 'Company\Admin\ReportNewController@generateproductsalesreports')->name('productsalesreports');

                Route::post('/reports/productsalesreportsdt', 'Company\Admin\ReportNewController@productsalesreportsDT')->name('productsalesreportsdt');



                Route::get('/reports/salesmanparty-wisereports', 'Company\Admin\ReportNewController@dailysalesmanreports')->name('salesreports');

                Route::post('/reports/salesmanparty-wisereports', 'Company\Admin\ReportNewController@generatedailysalesmanreports')->name('downloadsalesreports');

                Route::post('/reports/salesmanpartywisereportsdt', 'Company\Admin\ReportNewController@salesmanpartywisereportsDT')->name('salesmanpartywisereportsdt');



                Route::get('/reports/partydetailsreport','Company\Admin\ReportNewController@partyDetailsReport')->name('partydetailsreport');

                Route::post('/reports/generatepartydetailsreport','Company\Admin\ReportNewController@genPartyDetailsReport')->name('generatepartydetailsreport');

                Route::post('/reports/generatepartydetailsreportdt','Company\Admin\ReportNewController@genPartyDetailsReportDT')->name('generatepartydetailsreportdt');









                Route::group(

                    [

                        'middleware' => ['is_enabled:beatplans'],

                    ],

                    function () {

                        Route::get('/reports/beatreport', 'Company\Admin\ReportNewController@beatroutereports')->name('beatroutereports');

                        Route::post('/reports/beatreport', 'Company\Admin\ReportNewController@datebeatroutereport')->name('beatroutereport');

                        Route::post('/reports/custompdfdexport', 'Company\Admin\ReportNewController@custompdfdexport')->name('beatroutereports.customPdfExport');

                        Route::post('/reports/custombeatroutereport', 'Company\Admin\ReportNewController@salesmanbeatroutereport')->name('custombeatroutereport');

                        Route::get('/reports/beatgpscomparison', 'Company\Admin\ReportController@beatgpscomparison')->name('gpscomparison');

                    });



                // Route::get('/reports/getvarientreportnew', 'Company\Admin\ReportNewController@getvarientreportnew')->name('getvarientreportnew');



                Route::get('/reports/stockreport', 'Company\Admin\ReportNewController@stockreport')->name('stockreport');

                Route::post('/reports/stockreportsdt', 'Company\Admin\ReportNewController@stockreportsdtDT')->name('stockreportsdt');

                Route::get('/reports/loadparties', 'Company\Admin\ReportNewController@loadParties')->name('stockreport.loadparties');

                Route::post('/reports/stockreport', 'Company\Admin\ReportNewController@partylateststockreports')->name('stockreports');

                Route::post('/reports/singlepartystockreports', 'Company\Admin\ReportNewController@singlepartystockreports')->name('stockreports2');

                Route::post('/reports/partieslateststockreports', 'Company\Admin\ReportNewController@partieslateststockreports')->name('stockreports3');

                Route::post('/reports/stockreport/custompdfdexport', 'Company\Admin\ReportNewController@custompdfdexport')->name('stockreport.customPdfExport');



                Route::group(

                    [

                        'middleware' => ['is_enabled:returns'],

                    ],

                    function () {

                        Route::get('/reports/returnsreport', 'Company\Admin\ReportNewController@returnsReport')->name('returnsReport');

                        Route::post('/reports/returnsreport', 'Company\Admin\ReportNewController@generateReturnsReport')->name('postReturnsReport');

                        Route::post('/reports/returnreportsdt', 'Company\Admin\ReportNewController@returnreportsDT')->name('returnreportsdt');



                        Route::post('/reports/getDetailView', 'Company\Admin\ReportNewController@getDetailView')->name('returnsreport.getDetailView');

                        Route::post('/reports/productpartywisereturnsreport', 'Company\Admin\ReportNewController@productpartywisereturnsreport')->name('productpartywisereturnsreport');



                        Route::post('/reports/barplotreturnsreport', 'Company\Admin\ReportNewController@barplotreturnsreport')->name('barplotreturnsReport');

                        Route::post('/reports/piplotreturnsreport', 'Company\Admin\ReportNewController@piplotreturnsreport')->name('piplotreturnsReport');



                        Route::post('/reports/returnsreport/custompdfdexport', 'Company\Admin\ReportNewController@custompdfdexport')->name('returnsReport.customPdfExport');



                    });







                    

                    Route::get('/reports/employeedetailsreport','Company\Admin\ReportNewController@employeeDetailsReport')->name('employeedetailsreport');

                    Route::post('/reports/generateemployeedetailsreport','Company\Admin\ReportNewController@genEmployeeDetailsReport')->name('generateemployeedetailsreport');

                    Route::post('/reports/generateemployeedetailsreportdt','Company\Admin\ReportNewController@genEmployeeDetailsReportDT')->name('generateemployeedetailsreportdt');



                    Route::get('/analytics', 'Company\Admin\DashboardController@dashboardAnalytics')->name('analytics');

                    Route::post('/analyticssave', 'Company\Admin\DashboardController@fetchAnalyticsSave')->name('analyticssave');

                    Route::post('/analyticssavedtfilter', 'Company\Admin\DashboardController@saveDateFilter')->name('analyticssavedtfilter');



                    Route::get('/reports/order-breakdown-report', 'Company\Admin\ReportNewController@productOrderDetailsReport')->name('report.product-order-details');

                    Route::post('/reports/product-order-details-report', 'Company\Admin\ReportNewController@generateOrderDetailsReport')->name('report.productsalesorderdetailsreports');

                    Route::post('/reports/datable-product-order-details-report', 'Company\Admin\ReportNewController@orderDetailsReportDT')->name('report.orderDetailsReportDT');

                    



                Route::get('/outlet-connection', 'Company\Admin\RetailerOutletController@index')->name('outlets.connection');

                Route::get('/outlet-connection/fetch-data', 'Company\Admin\RetailerOutletController@fetchData')->name('outlets.fechData');

                Route::post('/outlet-connection', 'Company\Admin\RetailerOutletController@store')->name('outlets.connection.store');

                Route::patch('/outlet-connection/{id}', 'Company\Admin\RetailerOutletController@update')->name('outlets.connection.update');

                Route::delete('/outlet-connection/{id}', 'Company\Admin\RetailerOutletController@destroy')->name('outlets.connection.delete');



                Route::get('/outlet-order-setup', 'Company\Admin\RetailerProductController@index')->name('outlets.ptr.setup');

                Route::get('/outlet-order-setup/fetch-data', 'Company\Admin\RetailerProductController@fetchData')->name('outlets.ptr.fechData');

                Route::post('/change-app-visibility', 'Company\Admin\RetailerProductController@changeAppVisibility')->name('outlets.ptr.changeAppVisibility');

                Route::patch('/outlet-order-setup', 'Company\Admin\RetailerProductController@update')->name('outlets.ptr.productUpdate');



                Route::patch('/outlet-settings/{company_id}', 'Company\Admin\RetailerProductController@updateSettings')->name('outlets.settings.update');



                Route::get('/reports/fetchhandledparty', 'Company\Admin\ReportNewController@fetchhandledparty')->name('fetchhandledparty');

                Route::get('/notification', 'Company\Admin\NotificationController@index')->name('notification');

                Route::get('/notification/{id}', 'Company\Admin\NotificationController@notificationDetail')->name('notification.id');

                Route::post('/notifications/get-notification', 'Company\Admin\NotificationController@getUserNotification')->name('notification.get');

                Route::post('/notifications/get-more-notification', 'Company\Admin\NotificationController@getMoreUserNotification')->name('notification.getmore');

                Route::post('/notifications/new-notification', 'Company\Admin\NotificationController@getUserLatestNotification')->name('notification.new');



            });

        });

});

Route::get('/clients/phonecode/get/{id}', 'Company\Admin\ClientController@getPhoneCode');

Route::get('/clients/states/get/{id}', 'Company\Admin\ClientController@getStates');

Route::get('/clients/cities/get/{id}', 'Company\Admin\ClientController@getCities');



/*FrontEnd*/



Route::get('/verify/{id}/{token}', 'FrontController@verify')->name('verify');

Route::post('/checksubdomain', 'FrontController@checksubdomain')->name('checksubdomain');



// Route::get('/', 'FrontController@index')->name('index');

// Route::get('/feature', 'FrontController@feature')->name('feature');

// Route::get('/pricing', 'FrontController@pricing')->name('pricing');

// Route::get('/request-demo', 'FrontController@requestdemo')->name('request-demo');

// Route::post('/requestquote', 'FrontController@requestquote')->name('requestquote');

// Route::get('/contact-us', 'FrontController@contactus')->name('contact-us');

// Route::get('/privacy-policy', 'FrontController@privacy')->name('privacy-policy');

// Route::post('/contact_us', 'FrontController@contact_us')->name('contact_us');

// Route::get('/refresh_captcha', 'FrontController@refreshCaptcha')->name('refresh_captcha');

// Route::get('/login', 'FrontController@login')->name('login');



// Route::get('/blog', 'FrontController@blog')->name('blog');

// Route::get('/sales-tracking-app-in-nepal', 'FrontController@salestrackingappinnepal')->name('sales-tracking-app-in-nepal');

// Route::get('/lead-management-software-in-nepal', 'FrontController@leadmanagementsoftwareinnepal')->name('lead-management-software-in-nepal');

// Route::get('/performance-measuring-software-in-nepal', 'FrontController@performancemeasuringsoftwareinnepal')->name('performance-measuring-software-in-nepal');

// Route::get('/field-sales-management-software-in-nepal', 'FrontController@fieldsalesmanagementsoftwareinnepal')->name('field-sales-management-software-in-nepal');

// Route::get('/sales-management-software-in-nepal', 'FrontController@salesmanagementsoftwareinnepal')->name('sales-management-software-in-nepal');

// Route::get('/manage-order-in-nepal', 'FrontController@manageorderinnepal')->name('manage-order-in-nepal');





// Route::get('/real-time-gps-tracking', 'FrontController@realtimegpstracking')->name('real-time-gps-tracking');

// Route::get('/maintain-attendance', 'FrontController@maintainattendance')->name('maintain-attendance');

// Route::get('/manage-sales-expense', 'FrontController@managesalesexpense')->name('manage-sales-expense');

// Route::get('/measure-sales-performance', 'FrontController@measuresalesperformance')->name('measure-sales-performance');

// Route::get('/leave-application', 'FrontController@leaveapplication')->name('leave-application');

// Route::get('/tasks-assignment', 'FrontController@tasksassignment')->name('tasks-assignment');

// Route::get('/manage-clients', 'FrontController@manageclients')->name('manage-clients');

// Route::get('/mark-client-location', 'FrontController@markclientlocation')->name('mark-client-location');

// Route::get('/manage-enquiries', 'FrontController@manageenquiries')->name('manage-enquiries');

// Route::get('/manage-collections', 'FrontController@managecollections')->name('manage-collections');

// Route::get('/manage-orders', 'FrontController@manageorders')->name('manage-orders');

// Route::get('/works-offline', 'FrontController@worksoffline')->name('works-offline');



// Route::get('/best-sales-tracking-application', 'FrontController@bestsalestrackingapplication')->name('best-sales-tracking-application');





// Route::get('/travel-distance-calculator', 'FrontController@traveldistancecalculator')->name('travel-distance-calculator');

// Route::get('/monthly-sales-target', 'FrontController@monthlysalestarget')->name('monthly-sales-target');

// Route::get('/announcement', 'FrontController@announcement')->name('announcement');

// Route::get('/manage-products', 'FrontController@manageproducts')->name('manage-products');

// Route::get('/meeting-records', 'FrontController@meetingrecords')->name('meeting-records');

// Route::get('/sales-employee-reports', 'FrontController@salesemployeereports')->name('sales-employee-reports');



// Route::get('/add-daily-remarks', 'FrontController@adddailyremarks')->name('add-daily-remarks');





/*End FrontEnd*/

