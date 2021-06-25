<?php

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
Route::post('/api/fetchClientSetting','ApiController@fetchClientSetting');

//API routes for Beats, Beatplans
Route::post('/api/fetchBeatplans', 'ApiController@fetchBeatplans');
Route::post('/api/fetchBeats', 'ApiController@fetchBeats');
Route::post('/api/fetchBeatsParties', 'ApiController@fetchBeatsParties');
Route::post('/api/saveBeatplan', 'ApiController@saveBeatplan');
Route::post('/api/syncBeatplan','ApiController@syncBeatplan');

Route::post('/api/fetchTourPlans', 'ApiController@fetchTourPlans');
Route::post('/api/saveTourPlans', 'ApiController@saveTourPlans');
Route::post('/api/syncTourPlans', 'ApiController@syncTourPlans');

//API routes for stock,stock_details,returns,order_fulfillments
Route::post('/api/saveStock','ApiController@saveStock');
Route::post('/api/syncStock','ApiController@syncStock');
Route::post('/api/fetchStock','ApiController@fetchStock');
Route::post('/api/fetchStockDetails','ApiController@fetchStockDetails');
Route::post('/api/fetchreturns','ApiController@fetchReturns');
Route::post('/api/fetchreturnreasons', 'ApiController@fetchReturnReason');
Route::post('/api/savereturns','ApiController@saveReturns');
Route::post('/api/syncreturns','ApiController@syncReturns');
Route::post('/api/fetchdayremarks','ApiController@fetchDayRemark');
Route::post('/api/savedayremarks','ApiController@saveDayRemark');
Route::post('/api/syncdayremarks','ApiController@syncDayRemark');
Route::post('/api/fetchOrderFulFillments','ApiController@fetchOrderFulFillments');


//API routes activites, activities type and activity priority
Route::post('/api/fetchActivities', 'ApiController@fetchActivities');
Route::post('/api/saveActivity', 'ApiController@saveActivity');
Route::post('/api/deleteActivity','ApiController@deleteActivity');
Route::post('/api/fetchActivityTypes', 'ApiController@fetchActivityTypes');
Route::post('/api/fetchActivityPriorities', 'ApiController@fetchActivityPriorities');
Route::post('/api/syncActivities','ApiController@syncActivities');

//API Employees
Route::post('/api/fetchEmployees', 'ApiController@fetchEmployees');

Route::post('/api/fetchPartyFormDependentData', 'ApiController@fetchPartyFormDependentData');
Route::post('/api/fetchCollectionFormDependentData', 'ApiController@fetchCollectionFormDependentData');
Route::post('/api/fetchOrderFormDependentData', 'ApiController@fetchOrderFormDependentData');


Route::post('/api/fetchCollateralFiles', 'ApiController@fetchCollateralFiles');

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

        Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
        Route::post('/login', 'Auth\LoginController@handleLogin')->name('login.post');
        Route::get('/registration', 'Auth\RegistrationController@showRegistrationForm')->name('registration');
        Route::post('/registration', 'Auth\RegistrationController@handleRegistration')->name('registration.post');
        Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
        Route::get('/home', 'DashboardController@home')->name('home');
        
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

                //Company

                Route::get('/company', 'Admin\CompanyController@index')->name('company');
                Route::get('/company/create', 'Admin\CompanyController@create')->name('company.create');
                Route::post('/company/create', 'Admin\CompanyController@store')->name('company.store');
                Route::get('/company/{id}', 'Admin\CompanyController@show')->name('company.show');
                Route::get('company/{id}/edit', 'Admin\CompanyController@edit')->name('company.edit');
                Route::patch('company/{id}', 'Admin\CompanyController@update')->name('company.update');
                Route::get('company/setting/{id}', 'Admin\CompanyController@settings')->name('company.setting');
                Route::patch('company/setting/profile/{id}', 'Admin\CompanyController@updateProfile')->name('company.setting.updateProfile');
                Route::patch('company/setting/layout/{id}', 'Admin\CompanyController@updateLayout')->name('company.setting.updateLayout');
                Route::patch('company/setting/email/{id}', 'Admin\CompanyController@updateEmail')->name('company.setting.updateEmail');
                Route::patch('company/setting/other/{id}', 'Admin\CompanyController@updateOther')->name('company.setting.updateOther');
                Route::post('company/setting/removeTax', 'Admin\CompanyController@removeTax')->name('company.setting.removeTax');
                Route::get('company/settings/editMarketArea/{id}','Admin\CompanyController@editMarketArea')->name('company.setting.editMarketArea');
                Route::get('company/settings/removeMarketArea/{id}','Admin\CompanyController@removeMarketArea')->name('company.setting.removeMarketArea');

                Route::post('company/setting/addpartytype', 'Admin\CompanyController@addPartyType')->name('company.setting.addpartytype');
                Route::get('company/settings/editPartyType/{id}','Admin\CompanyController@editPartyType')->name('company.setting.editPartyType');
                Route::get('company/settings/getPartyTypeList','Admin\CompanyController@getPartyTypeList')->name('company.setting.getPartyTypeList');
                Route::get('company/settings/removePartyType/{id}','Admin\CompanyController@removePartyType')->name('company.setting.removePartyType');

                Route::post('company/setting/addmarketarea', 'Admin\CompanyController@addMarketArea')->name('company.setting.addmarketarea');

                Route::delete('/company/{id}', 'Admin\CompanyController@destroy')->name('company.destroy');


                //Route::get('/ddd', 'Company\ApiController@index');


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


/* SHOP ROUTES */
Route::group(
    [
        'domain' => '{subdomain}.' . config('app.domain'),
        'middleware' => ['company.domain','isActive'],
        'as' => 'company.'
    ], function () {

    Route::get('/home', 'HomeController@home')->name('home');

    Route::get('/', 'Company\Auth\LoginController@showLoginForm')->name('login');
    // Authentication and registration
    Route::get('/login', 'Company\Auth\LoginController@showLoginForm')->name('login');
    Route::get('/showforgotPassword', 'Company\Auth\LoginController@showForgotPassword')->name('showForgotPassword');
    Route::post('/forgotPassword', 'Company\Auth\LoginController@forgotPassword')->name('forgotPassword');
    
    Route::get('password/reset/{token}','Company\Auth\ResetPasswordController@showResetForm')->name('password.reset');
    
    Route::post('password/reset','Company\Auth\ResetPasswordController@reset')->name('password.update');
    Route::post('/login', 'Company\Auth\LoginController@handleLogin')->name('login.post');
    Route::get('/logout', 'Company\Auth\LoginController@logout')->name('logout');
    Route::post('/logout', 'Company\Auth\LoginController@logout')->name('logout');


    // SHOP MANAGEMENT
    Route::group(
        [
            'prefix' => 'admin',
            'middleware' => ['auth'],
            'as' => 'admin.'
        ],
        function () {
        
            Route::get('/setting', 'Company\Admin\ClientSettingController@index')->name('setting');
            // Route::get('/setting/reset', 'Company\Admin\ClientSettingController@reset');
            Route::get('setting/edit', 'Company\Admin\ClientSettingController@edit')->name('setting.edit');
            Route::patch('setting/{id}', 'Company\Admin\ClientSettingController@update')->name('setting.update');
            Route::patch('setting/profile/{id}', 'Company\Admin\ClientSettingController@updateProfile')->name('setting.updateProfile');
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
            
            // Route::post('/setting/removeOTP/{id}', 'Company\Admin\ClientSettingController@removeOTP')->name('setting.removeOTP');

            //Route::get('/', 'company\PageController@home')->name('home');

            Route::get('/', 'Company\Admin\DashboardController@home')->name('home');
            // master search
            Route::post('/', 'Company\Admin\DashboardController@homeSearch')->name('home.p');

            //end of master search
            Route::get('/home/livetracking', 'Company\Admin\DashboardController@home')->name('home.livetracking');

            // @todo: orders, customers, staff members, etc
            //Employee
            Route::get('/employee', 'Company\Admin\EmployeeController@index')->name('employee');
            Route::get('/employee/getsuperiorlist', 'Company\Admin\EmployeeController@getSuperiorList')->name('employee.get-superior-list');
            Route::post('/employee/getEmployeeSuperiors','Company\Admin\EmployeeController@getEmployeeSuperiors')->name('employee.getEmployeeSuperiors');
            Route::post('/employee/ajaxdatatable', 'Company\Admin\EmployeeController@ajaxDatatable')->name('employee.ajaxDatatable');
            Route::get('/employee/create', 'Company\Admin\EmployeeController@create')->name('employee.create');
            Route::get('/employee/getsuperiorparties', 'Company\Admin\EmployeeController@getsuperiorparties')->name('employee.getsuperiorparties');
            Route::post('/employee/create', 'Company\Admin\EmployeeController@store')->name('employee.store');
            Route::get('/employee/{id}', 'Company\Admin\EmployeeController@show')->name('employee.show');
            Route::get('employee/{id}/edit', 'Company\Admin\EmployeeController@edit')->name('employee.edit');
            Route::post('employee/ajaxvalidate','Company\Admin\EmployeeController@ajaxValidate')->name('employee.ajaxValidate');
            Route::post('employee/ajaxupdatevalidate','Company\Admin\EmployeeController@ajaxUpdateValidate')->name('employee.ajaxUpdateValidate');
            Route::post('employee/ajaxbasicupdate','Company\Admin\EmployeeController@ajaxUpdate')->name('employee.ajaxBasicUpdate');
            Route::post('employee/ajaxcontactupdate','Company\Admin\EmployeeController@ajaxContactUpdate')->name('employee.ajaxContactUpdate');
            Route::post('employee/ajaxcompanyupdate','Company\Admin\EmployeeController@ajaxCompanyUpdate')->name('employee.ajaxCompanyUpdate');
            Route::post('employee/ajaxbankupdate','Company\Admin\EmployeeController@ajaxBankUpdate')->name('employee.ajaxBankUpdate');
            Route::post('employee/ajaxdocumentupdate','Company\Admin\EmployeeController@ajaxDocumentUpdate')->name('employee.ajaxDocumentUpdate');
            Route::post('employee/ajaxaccountupdate','Company\Admin\EmployeeController@ajaxAccountUpdate')->name('employee.ajaxAccountUpdate');
            Route::post('employee/update/{id}', 'Company\Admin\EmployeeController@update')->name('employee.update');
            Route::delete('/employee/{id}', 'Company\Admin\EmployeeController@destroy')->name('employee.destroy');

            Route::post('/employee/addClient', 'Company\Admin\EmployeeController@addClient')->name('setting.addClient');
            Route::post('/employee/removeClient', 'Company\Admin\EmployeeController@removeClient')->name('setting.removeClient');
            Route::post('/employee/removeDoc', 'Company\Admin\EmployeeController@removeDoc')->name('employee.removeDoc');
            Route::post('/employee/addParties/{id}', 'Company\Admin\EmployeeController@addParties')->name('employee.addParties');
            Route::post('/employee/getEmployeeHandles','Company\Admin\EmployeeController@getEmployeeHandles')->name('employee.getEmployeeHandles');
            Route::post('/employee/getAttendances','Company\Admin\EmployeeController@getAttendances')->name('employee.getAttendances');
            Route::post('/employee/getAttendances/count','Company\Admin\EmployeeController@getAttendancesCount')->name('employee.getAttendancesCount');

            //unit routes
            Route::get('/unit', 'Company\Admin\UnitController@index')->name('unit');
            Route::get('/unit/create', 'Company\Admin\UnitController@create')->name('unit.create');
            Route::post('/unit/create', 'Company\Admin\UnitController@store')->name('unit.store');
            Route::get('/unit/{id}', 'Company\Admin\UnitController@show')->name('unit.show');
            Route::get('unit/{id}/edit', 'Company\Admin\UnitController@edit')->name('unit.edit');
            Route::patch('unit/update/{id}', 'Company\Admin\UnitController@update')->name('unit.update');
            Route::delete('/unit/{id}', 'Company\Admin\UnitController@destroy')->name('unit.destroy');
            Route::post('unit/changeStatus', 'Company\Admin\UnitController@changeStatus')->name('unit.changeStatus');

            Route::get('/brand', 'Company\Admin\BrandController@index')->name('brand');
            Route::get('/brand/create', 'Company\Admin\BrandController@create')->name('brand.create');
            Route::post('/brand/create', 'Company\Admin\BrandController@store')->name('brand.store');
            Route::get('/brand/{id}', 'Company\Admin\BrandController@show')->name('brand.show');
            Route::get('brand/{id}/edit', 'Company\Admin\BrandController@edit')->name('brand.edit');
            Route::patch('brand/update/{id}', 'Company\Admin\BrandController@update')->name('brand.update');
            Route::delete('/brand/{id}', 'Company\Admin\BrandController@destroy')->name('brand.destroy');
            Route::post('brand/changeStatus', 'Company\Admin\BrandController@changeStatus')->name('brand.changeStatus');

            /// Employee Group

            Route::get('/employeegroup', 'Company\Admin\EmployeeGroupController@index')->name('employeegroup');
            Route::get('/employeegroup/create', 'Company\Admin\EmployeeGroupController@create')->name('employeegroup.create');
            Route::post('/employeegroup/create', 'Company\Admin\EmployeeGroupController@store')->name('employeegroup.store');
            Route::get('/employeegroup/{id}', 'Company\Admin\EmployeeGroupController@show')->name('employeegroup.show');
            Route::get('employeegroup/{id}/edit', 'Company\Admin\EmployeeGroupController@edit')->name('employeegroup.edit');
            Route::post('employee/changeStatus', 'Company\Admin\EmployeeController@changeStatus')->name('employee.changeStatus');
            Route::patch('employeegroup/update/{id}', 'Company\Admin\EmployeeGroupController@update')->name('employeegroup.update');
            Route::delete('/employeegroup/{id}', 'Company\Admin\EmployeeGroupController@destroy')->name('employeegroup.destroy');
            Route::post('employeegroup/changeStatus', 'Company\Admin\EmployeeGroupController@changeStatus')->name('employeegroup.changeStatus');

            //Product
            Route::get('/product', 'Company\Admin\ProductController@index')->name('product');
            Route::get('/product/create', 'Company\Admin\ProductController@create')->name('product.create');
            Route::post('/product/create', 'Company\Admin\ProductController@store')->name('product.store');
            Route::get('/product/{id}', 'Company\Admin\ProductController@show')->name('product.show');
            Route::get('product/{id}/edit', 'Company\Admin\ProductController@edit')->name('product.edit');
            Route::post('product/changeStatus', 'Company\Admin\ProductController@changeStatus')->name('employee.changeStatus');
            Route::patch('product/update/{id}', 'Company\Admin\ProductController@update')->name('product.update');
            Route::delete('/product/{id}', 'Company\Admin\ProductController@destroy')->name('product.destroy');
            Route::post('/product/get', 'Company\Admin\ProductController@getProduct')->name('product.get');
            Route::post('/product/productVariant', 'Company\Admin\ProductController@productVariant')->name('product.productVariant');

            //Category
            Route::get('/category', 'Company\Admin\CategoryController@index')->name('category');
            Route::get('/category/create', 'Company\Admin\CategoryController@create')->name('category.create');
            Route::post('/category/create', 'Company\Admin\CategoryController@store')->name('category.store');
            Route::get('/category/{id}', 'Company\Admin\CategoryController@show')->name('category.show');
            Route::get('category/{id}/edit', 'Company\Admin\CategoryController@edit')->name('category.edit');
            Route::patch('category/update/{id}', 'Company\Admin\CategoryController@update')->name('category.update');
            Route::delete('/category/{id}', 'Company\Admin\CategoryController@destroy')->name('category.destroy');
            Route::post('category/changeStatus', 'Company\Admin\CategoryController@changeStatus')->name('category.changeStatus');

            //Client

            Route::get('/client', 'Company\Admin\ClientController@index')->name('client');
            Route::get('/client/create', 'Company\Admin\ClientController@create')->name('client.create');
            Route::post('/client/create', 'Company\Admin\ClientController@store')->name('client.store');
            Route::get('/client/getsuperiorlist', 'Company\Admin\ClientController@getSuperiorList')->name('client.get-superior-list');
            Route::get('/client/subclients/{id}', 'Company\Admin\ClientController@subclients')->name('client.subclients');
            Route::post('/client/ajaxupdate', 'Company\Admin\ClientController@ajaxUpdate')->name('client.ajaxUpdate');

            Route::get('/client/retailerslist/{id}', 'Company\Admin\ClientController@retailerslist')->name('client.retailerslist');
            // Route::get('/client/showchild/{id}', 'Company\Admin\ClientController@getChildParty')->name('client.showchild');
            Route::get('/client/retailers', 'Company\Admin\ClientController@retailers')->name('client.retailers');
            Route::get('/client/{id}', 'Company\Admin\ClientController@show')->name('client.show');
            Route::post('/client/getEmployeeSuperior', 'Company\Admin\ClientController@getEmployeeSuperiors')->name('client.getEmployeeSuperior');
            Route::post('/client/getEmployeeJunior', 'Company\Admin\ClientController@getEmployeeJuniors')->name('client.getEmployeeJuniors');
            Route::get('client/{id}/edit', 'Company\Admin\ClientController@edit')->name('client.edit');
            Route::post('client/changeStatus', 'Company\Admin\ClientController@changeStatus')->name('client.changeStatus');
            Route::patch('client/update/{id}', 'Company\Admin\ClientController@update')->name('client.update');
            Route::delete('/client/{id}', 'Company\Admin\ClientController@destroy')->name('client.destroy');
            Route::post('/client/getajaxpartytypeclients','Company\Admin\ClientController@getPartyTypeClients')->name('client.getPartyTypeClients');


            Route::get('/client/order/{id}', 'Company\Admin\OrderController@show')->name('order.showorder');
            Route::get('/client/collection/{id}', 'Company\Admin\ClientController@showcollection')->name('order.showcollection');

            Route::post('/client/addEmployee', 'Company\Admin\ClientController@addEmployee')->name('setting.addEmployee');
            Route::post('/client/addLinkEmployee', 'Company\Admin\ClientController@addLinkEmployee')->name('setting.addLinkEmployee');
            Route::post('/client/removeEmployee', 'Company\Admin\ClientController@removeEmployee')->name('setting.removeEmployee');


            //Announcement
            Route::get('/announcement', 'Company\Admin\AnnouncementController@index')->name('announcement');
            // Route::post('/announcement', 'Company\Admin\AnnouncementController@ajaxIndex')->name('announcement.ajaxIndex');
            Route::get('/announcement/create', 'Company\Admin\AnnouncementController@create')->name('announcement.create');
            Route::post('/announcement/create', 'Company\Admin\AnnouncementController@store')->name('announcement.store');
            Route::post('/announcement/detail', 'Company\Admin\AnnouncementController@detail')->name('announcement.detail');
            Route::get('/announcement/{id}', 'Company\Admin\AnnouncementController@show')->name('announcement.show');
            Route::get('announcement/{id}/edit', 'Company\Admin\AnnouncementController@edit')->name('announcement.edit');
            Route::patch('announcement/{id}', 'Company\Admin\AnnouncementController@update')->name('announcement.update');
            Route::delete('/announcement/{id}', 'Company\Admin\AnnouncementController@destroy')->name('announcement.destroy');

            //Expenses
            Route::get('/expense', 'Company\Admin\ExpenseController@index')->name('expense');
            // Route::post('/expense/ajaxIndex', 'Company\Admin\ExpenseController@ajaxIndex')->name('expense.ajaxIndex');
            Route::get('/expense/create', 'Company\Admin\ExpenseController@create')->name('expense.create');
            Route::post('/expense/create', 'Company\Admin\ExpenseController@store')->name('expense.store');
            Route::get('/expense/{id}', 'Company\Admin\ExpenseController@show')->name('expense.show');
            Route::get('expense/{id}/edit', 'Company\Admin\ExpenseController@edit')->name('expense.edit');
            Route::post('expense/changeStatus', 'Company\Admin\ExpenseController@changeStatus')->name('expense.changeStatus');
            Route::patch('expense/update/{id}', 'Company\Admin\ExpenseController@update')->name('expense.update');
            Route::delete('/expense/{id}', 'Company\Admin\ExpenseController@destroy')->name('expense.destroy');

            //Meetings
            // Route::get('/meeting', 'Company\Admin\MeetingController@index')->name('meeting');
            // Route::get('/meeting/create', 'Company\Admin\MeetingController@create')->name('meeting.create');
            // Route::post('/meeting/create', 'Company\Admin\MeetingController@store')->name('meeting.store');
            // Route::get('/meeting/{id}', 'Company\Admin\MeetingController@show')->name('meeting.show');
            // Route::get('meeting/{id}/edit', 'Company\Admin\MeetingController@edit')->name('meeting.edit');
            // Route::post('meeting/changeStatus', 'Company\Admin\MeetingController@changeStatus')->name('meeting.changeStatus');
            // Route::patch('meeting/update/{id}', 'Company\Admin\MeetingController@update')->name('meeting.update');
            // Route::delete('/meeting/{id}', 'Company\Admin\MeetingController@destroy')->name('meeting.destroy');

            //Notes
            Route::get('/notes', 'Company\Admin\NoteController@index')->name('notes');
            Route::get('/notes/create','Company\Admin\NoteController@create')->name('notes.create');
            Route::get('/notes/{id}','Company\Admin\NoteController@show')->name('notes.show');
            Route::post('/notes/store','Company\Admin\NoteController@store')->name('notes.store');
            Route::get('/notes/{id}/edit','Company\Admin\NoteController@edit')->name('notes.edit');
            Route::patch('/notes/update/{id}','Company\Admin\NoteController@update')->name('notes.update');
            Route::delete('/notes/{id}','Company\Admin\NoteController@destroy')->name('notes.destroy');            

            //Collections

            Route::get('/collection', 'Company\Admin\CollectionController@index')->name('collection');
            // Route::post('/collection', 'Company\Admin\CollectionController@ajaxIndex')->name('collection.ajaxIndex');
            Route::get('/collection/create', 'Company\Admin\CollectionController@create')->name('collection.create');
            Route::post('/collection/create', 'Company\Admin\CollectionController@store')->name('collection.store');
            Route::get('/collection/{id}', 'Company\Admin\CollectionController@show')->name('collection.show');
            Route::get('collection/{id}/edit', 'Company\Admin\CollectionController@edit')->name('collection.edit');
            Route::patch('collection/update/{id}', 'Company\Admin\CollectionController@update')->name('collection.update');
            Route::get('collection/{id}/download', 'Company\Admin\CollectionController@download')->name('collection.download');
            Route::delete('/collection/{id}', 'Company\Admin\CollectionController@destroy')->name('collection.destroy');

            //Task
            Route::get('/task', 'Company\Admin\TaskController@index')->name('task');
            Route::get('/task/create', 'Company\Admin\TaskController@create')->name('task.create');
            Route::post('/task/create', 'Company\Admin\TaskController@store')->name('task.store');
            Route::get('/task/{id}', 'Company\Admin\TaskController@show')->name('task.show');
            Route::get('task/{id}/edit', 'Company\Admin\TaskController@edit')->name('task.edit');
            Route::post('task/changeStatus', 'Company\Admin\TaskController@changeStatus')->name('task.changeStatus');
            Route::patch('task/{id}', 'Company\Admin\TaskController@update')->name('task.update');
            Route::delete('/task/{id}', 'Company\Admin\TaskController@destroy')->name('task.destroy');

            //Orders
            Route::get('/order', 'Company\Admin\OrderController@index')->name('order');
            Route::post('/order/ajaxdatatable', 'Company\Admin\OrderController@ajaxDatatable')->name('order.ajaxDatatable');
            // Route::post('/order/ajaxindex', 'Company\Admin\OrderController@ajaxIndex')->name('order.ajaxIndex');
            Route::get('/orderdist', 'Company\Admin\OrderController@orderdist')->name('orderdist');
            Route::get('/order/create', 'Company\Admin\OrderController@create')->name('order.create');
            Route::post('/order/create', 'Company\Admin\OrderController@store')->name('order.store');
            Route::get('/order/{id}', 'Company\Admin\OrderController@show')->name('order.show');
            Route::get('order/{id}/edit', 'Company\Admin\OrderController@edit')->name('order.edit');
            Route::patch('order/{id}', 'Company\Admin\OrderController@update')->name('order.update');
            Route::delete('/order/{id}', 'Company\Admin\OrderController@destroy')->name('order.destroy');
            Route::post('order/changeDeliveryStatus', 'Company\Admin\OrderController@changeDeliveryStatus')->name('order.changeDeliveryStatus');
            Route::get('/order/{id}/download', 'Company\Admin\OrderController@download')->name('order.download');
            Route::post('/order/{id}', 'Company\Admin\OrderController@mail')->name('order.mail');
            Route::get('/orderstatus', 'Company\Admin\OrderStatusController@index')->name('orderstatus');
            Route::post('/orderstatus/store', 'Company\Admin\OrderStatusController@store')->name('orderstatus.store');
            Route::post('/orderstatus/update', 'Company\Admin\OrderStatusController@update')->name('orderstatus.update');
            Route::post('/orderstatus/delete', 'Company\Admin\OrderStatusController@delete')->name('orderstatus.delete');
            //Attendance
            Route::get('/attendance', 'Company\Admin\AttendanceController@index')->name('attendance');
            Route::get('/attendance/create', 'Company\Admin\AttendanceController@create')->name('attendance.create');
            Route::post('/attendance/create', 'Company\Admin\AttendanceController@store')->name('attendance.store');
            //Route::get('/attendance/{id}', 'Company\Admin\AttendanceController@show')->name('attendance.show');
            Route::get('/attendance/{id}/{date}', 'Company\Admin\AttendanceController@show')->name('attendance.show');
            Route::get('attendance/{id}/edit', 'Company\Admin\AttendanceController@edit')->name('attendance.edit');
            Route::patch('attendance/update/{id}', 'Company\Admin\AttendanceController@update')->name('attendance.update');
            Route::delete('/attendance/{id}', 'Company\Admin\AttendanceController@destroy')->name('attendance.destroy');

            //Routes for Activities Section
            Route::resource('/activities', 'Company\Admin\ActivityController');
            Route::post('activities/mark-completed', 'Company\Admin\ActivityController@ajaxResponse')->name('activities.mark-completed');
            Route::post('activities/update/mark', 'Company\Admin\ActivityController@updateMark')->name('activities.updateMark');
            Route::post('/activities/filter', 'Company\Admin\ActivityController@indexSearch')->name('activities.search');

            Route::resource('/activities-type', 'Company\Admin\ActivityTypeController');
            Route::post('/activities-type/update', 'Company\Admin\ActivityTypeController@updateActivityType')->name('activityType.update');
            Route::post('/activities-type/delete', 'Company\Admin\ActivityTypeController@deleteActivityType')->name('activityType.delete');

            Route::resource('/activities-priority', 'Company\Admin\ActivityPriorityController');
            Route::post('/activities-priority/update', 'Company\Admin\ActivityPriorityController@updateActivityPriority')->name('activityPriority.update');
            Route::post('/activities-priority/delete', 'Company\Admin\ActivityPriorityController@deleteActivityPriority')->name('activityPriority.delete');

            //Holidays
            Route::resource('/holidays', 'Company\Admin\HolidayController');
            Route::post('/holidays/getCalendar','Company\Admin\HolidayController@getCalendar')->name('holidays.getCalendar');
            Route::post('/holidays/edit', 'Company\Admin\HolidayController@edit')->name('holidays.edit');
            Route::post('/holidays/delete', 'Company\Admin\HolidayController@delete')->name('holidays.delete');
            Route::post('/holidays/populate', 'Company\Admin\HolidayController@populate')->name('holidays.populate');

            //Cheque
            Route::resource('/cheque', 'Company\Admin\ChequeController');
            Route::post('/cheque/ajaxIndex', 'Company\Admin\ChequeController@ajaxIndex')->name('pdc.ajaxIndex');
            Route::post('/cheque/updatestatus', 'Company\Admin\ChequeController@updateStatus')->name('chequeUpdateStatus');

            //Leave
            Route::get('/leave', 'Company\Admin\LeaveController@index')->name('leave');
            // Route::post('/leave', 'Company\Admin\LeaveController@ajaxIndex')->name('leave.ajaxIndex');
            Route::get('/leave/create', 'Company\Admin\LeaveController@create')->name('leave.create');
            Route::post('/leave/create', 'Company\Admin\LeaveController@store')->name('leave.store');
            Route::get('/leave/{id}', 'Company\Admin\LeaveController@show')->name('leave.show');
            Route::get('leave/{id}/edit', 'Company\Admin\LeaveController@edit')->name('leave.edit');
            Route::post('leave/changeStatus', 'Company\Admin\LeaveController@changeStatus')->name('leave.changeStatus');
            Route::patch('leave/{id}', 'Company\Admin\LeaveController@update')->name('leave.update');
            Route::delete('/leave/{id}', 'Company\Admin\LeaveController@destroy')->name('leave.destroy');
            Route::post('/leave/approve', 'Company\Admin\LeaveController@approve')->name('leave.approve');

            Route::resource('/returnreason', 'Company\Admin\ReturnReasonController');
            Route::post('/returnreason/update', 'Company\Admin\ReturnReasonController@updateReturnReason')->name('returnreason.update');
            Route::post('/returnreason/delete', 'Company\Admin\ReturnReasonController@deleteReturnReason')->name('returnreason.delete');
            //leaveType
            Route::post('/leaveType/store','Company\Admin\LeaveTypeController@store')->name('leavetype.store');
            Route::post('/leaveType/update/{id}','Company\Admin\LeaveTypeController@update')->name('leavetype.update');
            Route::post('/leaveType/destroy/{id}','Company\Admin\LeaveTypeController@destroy')->name('leavetype.destroy');

            Route::group(
                [
                    'middleware' => ['is_enabled:tourplans'],
                ],
            function(){
            //Tour Plan
                Route::get('/tourplans', 'Company\Admin\TourPlanController@index')->name('tours');
                // Route::post('/tourplans', 'Company\Admin\TourPlanController@ajaxIndex')->name('tourplans.ajaxIndex');
                Route::get('/tourplans/{id}', 'Company\Admin\TourPlanController@show')->name('tours.show');
                Route::get('/tourplans/{id}/download', 'Company\Admin\TourPlanController@download')->name('tours.download');
                Route::post('tourplans/changeStatus', 'Company\Admin\TourPlanController@changeStatus')->name('leave.changeStatus');
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

            Route::get('/beat', 'Company\Admin\BeatController@index')->name('beat');
            Route::get('beat/assignedParties', 'Company\Admin\BeatController@assignedParties')->name('beat.assignedParties');
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
                function(){
                Route::get('/beatplan', 'Company\Admin\BeatPlanController@index')->name('beatplan');

                Route::post('/beatplan/getCalendar','Company\Admin\BeatPlanController@getCalendar')->name('beatplans.getCalendar');

                Route::post('/beatplan/fetcheachplan','Company\Admin\BeatPlanController@fetcheachplan')->name('beatplan.fetcheachplan');
                Route::get('/beatplan/appendElement', 'Company\Admin\BeatPlanController@appendElement')->name('beatplan.appendElement.index');
                Route::post('/beatplan/fetchpartylist', 'Company\Admin\BeatPlanController@fetchpartylist')->name('beatplan.fetchpartylist');
                Route::get('/beatplan/monthplans', 'Company\Admin\BeatPlanController@monthplans')->name('beatplan.monthplans');
                
                Route::post('/beatplan/create', 'Company\Admin\BeatPlanController@store')->name('beatplan.store');

                Route::get('/beatplan/{id}', 'Company\Admin\BeatPlanController@detail')->name('beatplan.detail');
                Route::get('/beatplan/show/{id}', 'Company\Admin\BeatPlanController@show')->name('beatplan.show');
            
                Route::post('/beatplan/delete','Company\Admin\BeatPlanController@delete')->name('beatplan.delete');
                
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


            //Reports
            Route::get('/reports/salesmangpspath', 'Company\Admin\ReportController@employeerattendancegps')->name('employeerattendancegps');
            Route::get('/reports/dailyemployeereport', 'Company\Admin\ReportController@dailyemployeereport')->name('dailyemployeereport');
            Route::get('/reports/monthlyemployeereport', 'Company\Admin\ReportController@monthlyemployeereport')->name('monthlyemployeereport');

            Route::get('/reports/getlocation', 'Company\Admin\ReportController@getlocation')->name('reports.location');
            Route::get('/reports/getFileLocation', 'Company\Admin\ReportController@getFileLocation')->name('reports.filelocation');
            Route::get('/reports/getPartyLocation', 'Company\Admin\ReportController@getPartyLocation')->name('reports.partylocation');
            Route::get('/reports/getAllRecentlocation', 'Company\Admin\ReportController@getAllRecentlocation')->name('reports.recentlocation');

             Route::get('/reports/updatelocation', 'Company\Admin\ReportController@updatelocation')->name('reports.updatelocation');


            Route::get('/reports/getdistance', 'Company\Admin\ReportController@getdistance')->name('reports.distance');
            Route::get('/reports/getdistanceold', 'Company\Admin\ReportController@getdistanceold')->name('reports.distanceold');

            Route::get('/reports/dailypartyreport', 'Company\Admin\ReportController@dailyordercollectionreport')->name('dailyordercollectionreport');

            Route::get('/reports/dailyattendancereport', 'Company\Admin\ReportController@attendancereport')->name('dailyattendancereport');
            Route::get('/reports/checkin-checkoutlocationreport', 'Company\Admin\ReportController@viewattendancereport')->name('viewattendancereport');
            Route::get('/reports/monthlyattendancereport', 'Company\Admin\ReportController@monthlyattendancereport')->name('monthlyattendancereport');
            Route::post('/reports/monthlyattendancereport', 'Company\Admin\ReportController@getmonthlyattendancereport')->name('getmonthlyattendancereport');

            Route::get('/reports/monthlyordercollectionreport', 'Company\Admin\ReportController@monthlyordercollectionreport')->name('monthlyordercollectionreport');

            Route::get('/reports/getreportdata', 'Company\Admin\ReportController@getreportdata')->name('reports.getreportdata');//ajax
            Route::get('/reports/reportgenerator', 'Company\Admin\ReportController@reportgenerator')->name('reportgenerator');
            Route::get('/reports/dailysalesorderreport', 'Company\Admin\ReportController@dailysalesreport')->name('dailysalesreport');

            Route::get('/reports/dailysalesmanorderreport', 'Company\Admin\ReportController@dailysalesreportbysalesman')->name('dailysalesreportbysalesman');
            Route::get('/reports/searchdailysalesreportbysalesman', 'Company\Admin\ReportController@searchdailysalesreportbysalesman')->name('searchdailysalesreportbysalesman');

            Route::get('/reports/zeroorderlist', 'Company\Admin\ReportController@noorders')->name('noorders');
            Route::get('/reports/generateorderreport', 'Company\Admin\ReportController@generateorderreport')->name('generateorderreport');
            Route::get('/reports/generatecustomreport', 'Company\Admin\ReportController@generatecustomreport')->name('generatecustomreport');
            Route::post('/reports/generatnewreport', 'Company\Admin\ReportController@generatnewreport')->name('generatnewreport');

            Route::post('/reports/collectionreport', 'Company\Admin\ReportController@collectionreport')->name('collectionreport.filter');

            Route::get('/reports/employeereport', 'Company\Admin\ReportController@employeereport')->name('employeereport');
            Route::post('/reports/employeereport', 'Company\Admin\ReportController@employeereport')->name('employeereport.filter');


            Route::get('/reports/getlocationbycompany', 'Company\Admin\ReportController@getlocationbycompany')->name('reports.locationbycompany');
            Route::get('/reports/gettotalhour', 'Company\Admin\ReportController@gettotalhour')->name('reports.totalhours');
            Route::get('/reports/getWorkedHourDetails', 'Company\Admin\ReportController@getWorkedHourDetails')->name('reports.gethoursdetails');
            Route::get('/reports/getdistancetravelled', 'Company\Admin\ReportController@getDistanceTravelled')->name('reports.getdistancetravelled');
            Route::get('/reports/getdistancetravelled2', 'Company\Admin\ReportController@getDistanceTravelled2')->name('reports.getdistancetravelled2');


            Route::get('/reports/meetingreport', 'Company\Admin\ReportController@meetingreport')->name('meetingreport');
            Route::post('/reports/meetingreport', 'Company\Admin\ReportController@meetingreport')->name('meetingreport.filter');

            Route::get('/reports/companyreach', 'Company\Admin\ReportController@companyreach')->name('companyreach');
            Route::get('/reports/employeetracking', 'Company\Admin\ReportController@employeetracking')->name('employeetracking');

            Route::get('/reports/getsalesreportdaily', 'Company\Admin\ReportController@getsalesreportdaily')->name('getsalesreportdaily');

            Route::get('/reports/todayattendancereport', 'Company\Admin\ReportNewController@todayattendancereport')->name('todayattendancereport');

            Route::get('/reports/orderreports', 'Company\Admin\ReportNewController@orderreports')->name('variantreports');
            Route::post('/reports/orderreports', 'Company\Admin\ReportNewController@generateorderreports')->name('getvariantreports');
            Route::get('/reports/orderreports/salesmanandpartylist', 'Company\Admin\ReportNewController@salesmanandpartylist')->name('fetchedrecords');
            
            Route::get('/reports/productsalesorderreport', 'Company\Admin\ReportNewController@productsalesreports')->name('viewproductsalesreports');
            Route::get('/reports/fetchpartylist', 'Company\Admin\ReportNewController@fetchpartylist')->name('fetchpartylist');
            Route::post('/reports/productsalesorderreport', 'Company\Admin\ReportNewController@generateproductsalesreports')->name('productsalesreports');

            Route::get('/reports/salesmanparty-wisereports', 'Company\Admin\ReportNewController@dailysalesmanreports')->name('salesreports');
            Route::post('/reports/salesmanparty-wisereports', 'Company\Admin\ReportNewController@generatedailysalesmanreports')->name('downloadsalesreports');

            Route::group(
                [
                    'middleware' => ['is_enabled:beatplans'],
                ],
                function(){
                Route::get('/reports/beatreport', 'Company\Admin\ReportNewController@beatroutereports')->name('beatroutereports');
                Route::post('/reports/beatreport', 'Company\Admin\ReportNewController@datebeatroutereport')->name('beatroutereport');
                Route::post('/reports/custombeatroutereport', 'Company\Admin\ReportNewController@salesmanbeatroutereport')->name('custombeatroutereport');
                Route::get('/reports/beatgpscomparison', 'Company\Admin\ReportController@beatgpscomparison')->name('gpscomparison');
            });

            // Route::get('/reports/getvarientreportnew', 'Company\Admin\ReportNewController@getvarientreportnew')->name('getvarientreportnew');

            Route::group(
                [
                    'middleware' => ['is_enabled:stockreport'],
                ],
            function(){
                Route::get('/reports/stockreport', 'Company\Admin\ReportNewController@stockreport')->name('stockreport');
                Route::get('/reports/fetchparties', 'Company\Admin\ReportNewController@fetchstockreportparties')->name('stockreport.fetchparties');
                Route::post('/reports/stockreport', 'Company\Admin\ReportNewController@partylateststockreports')->name('stockreports');
                Route::post('/reports/singlepartystockreports', 'Company\Admin\ReportNewController@singlepartystockreports')->name('stockreports2');
                Route::post('/reports/partieslateststockreports', 'Company\Admin\ReportNewController@partieslateststockreports')->name('stockreports3');
            });

            Route::get('/reports/fetchhandledparty', 'Company\Admin\ReportNewController@fetchhandledparty')->name('fetchhandledparty');

           Route::get('/notification', 'Company\Admin\NotificationController@index')->name('notification');
        Route::get('/notification/{id}', 'Company\Admin\NotificationController@notificationDetail')->name('notification.id');
        Route::post('/notifications/get-notification', 'Company\Admin\NotificationController@getUserNotification')->name('notification.get');
        Route::post('/notifications/get-more-notification', 'Company\Admin\NotificationController@getMoreUserNotification')->name('notification.getmore');
        Route::post('/notifications/new-notification', 'Company\Admin\NotificationController@getUserLatestNotification')->name('notification.new');

        }
    );


}
);
Route::get('/clients/phonecode/get/{id}', 'Company\Admin\ClientController@getPhoneCode');
Route::get('/clients/states/get/{id}', 'Company\Admin\ClientController@getStates');
Route::get('/clients/cities/get/{id}', 'Company\Admin\ClientController@getCities');

/*FrontEnd*/

Route::get('/verify/{id}/{token}', 'FrontController@verify')->name('verify');
Route::post('/checksubdomain', 'FrontController@checksubdomain')->name('checksubdomain');

Route::get('/', 'FrontController@index')->name('index');
Route::get('/feature', 'FrontController@feature')->name('feature');
Route::get('/pricing', 'FrontController@pricing')->name('pricing');
Route::get('/request-demo', 'FrontController@requestdemo')->name('request-demo');
Route::post('/requestquote', 'FrontController@requestquote')->name('requestquote');
Route::get('/contact-us', 'FrontController@contactus')->name('contact-us');
Route::get('/privacy-policy', 'FrontController@privacy')->name('privacy-policy');
Route::post('/contact_us', 'FrontController@contact_us')->name('contact_us');
Route::get('/refresh_captcha', 'FrontController@refreshCaptcha')->name('refresh_captcha');
Route::get('/login', 'FrontController@login')->name('login');

Route::get('/blog', 'FrontController@blog')->name('blog');
Route::get('/sales-tracking-app-in-nepal', 'FrontController@salestrackingappinnepal')->name('sales-tracking-app-in-nepal');
Route::get('/lead-management-software-in-nepal', 'FrontController@leadmanagementsoftwareinnepal')->name('lead-management-software-in-nepal');
Route::get('/performance-measuring-software-in-nepal', 'FrontController@performancemeasuringsoftwareinnepal')->name('performance-measuring-software-in-nepal');
Route::get('/field-sales-management-software-in-nepal', 'FrontController@fieldsalesmanagementsoftwareinnepal')->name('field-sales-management-software-in-nepal');
Route::get('/sales-management-software-in-nepal', 'FrontController@salesmanagementsoftwareinnepal')->name('sales-management-software-in-nepal');
Route::get('/manage-order-in-nepal', 'FrontController@manageorderinnepal')->name('manage-order-in-nepal');


Route::get('/real-time-gps-tracking', 'FrontController@realtimegpstracking')->name('real-time-gps-tracking');
Route::get('/maintain-attendance', 'FrontController@maintainattendance')->name('maintain-attendance');
Route::get('/manage-sales-expense', 'FrontController@managesalesexpense')->name('manage-sales-expense');
Route::get('/measure-sales-performance', 'FrontController@measuresalesperformance')->name('measure-sales-performance');
Route::get('/leave-application', 'FrontController@leaveapplication')->name('leave-application');
Route::get('/tasks-assignment', 'FrontController@tasksassignment')->name('tasks-assignment');
Route::get('/manage-clients', 'FrontController@manageclients')->name('manage-clients');
Route::get('/mark-client-location', 'FrontController@markclientlocation')->name('mark-client-location');
Route::get('/manage-enquiries', 'FrontController@manageenquiries')->name('manage-enquiries');
Route::get('/manage-collections', 'FrontController@managecollections')->name('manage-collections');
Route::get('/manage-orders', 'FrontController@manageorders')->name('manage-orders');
Route::get('/works-offline', 'FrontController@worksoffline')->name('works-offline');

Route::get('/best-sales-tracking-application', 'FrontController@bestsalestrackingapplication')->name('best-sales-tracking-application');


Route::get('/travel-distance-calculator', 'FrontController@traveldistancecalculator')->name('travel-distance-calculator');
Route::get('/monthly-sales-target', 'FrontController@monthlysalestarget')->name('monthly-sales-target');
Route::get('/announcement', 'FrontController@announcement')->name('announcement');
Route::get('/manage-products', 'FrontController@manageproducts')->name('manage-products');
Route::get('/meeting-records', 'FrontController@meetingrecords')->name('meeting-records');
Route::get('/sales-employee-reports', 'FrontController@salesemployeereports')->name('sales-employee-reports');

Route::get('/add-daily-remarks', 'FrontController@adddailyremarks')->name('add-daily-remarks');



/*End FrontEnd*/
