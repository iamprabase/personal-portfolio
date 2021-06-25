<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Retailer Route File
Route::prefix('outlet')
    ->group(base_path('routes/outlet.php')); 

/*TARGET API*/
Route::post('/v2/testargetRep', 'API\TargetAchieveController@APItest');
Route::post('/v2/targetReport', 'API\TargetAchieveController@index');
Route::post('/v2/targetReport2', 'API\TargetAchieveController@reportFiltered2');
Route::post('/v2/nptargetReport', 'API\TargetAchieveController@npIndex');
Route::post('/v2/nptargetReport2', 'API\TargetAchieveController@npReportFiltered2');

// Login and get token
Route::post('/v2/mobilelogin','API\AuthController@login');
Route::post('/v2/logoutPreviousDevice', 'API\LogoutController@logoutPreviousDevice');
Route::post('/v2/mobilelogout', 'API\LogoutController@logout');
Route::post('/v2/getMobileAppVersionCode', 'API\AuthController@getMobileAppVersionCode');
Route::post('/v2/companystatus','API\CompanyStatusController@login');

Route::post('/v2/demoLoginMail','API\AuthController@demoLoginMail');

// FetchCommanData
Route::post('/v2/fetchCommonData', 'API\CommonController@fetchCommonData');
Route::post('/v2/fetchCommonDataClients', 'API\CommonController@fetchCommonDataClients');
Route::post('v2/getPartyRate/{company_id}', 'API\CommonController@fetchPartyRates')->name('api.company.getpartyrates');
Route::post('/v2/fetchModuleAttributes', 'API\ModuleAttributeController@index');
Route::post('/v2/getPermissions','API\PermissionController@index');

// Category 
Route::post('/v2/fetchCategory', 'API\CategoryController@index');

// Category Rate
Route::post('/v2/fetchCategoryRates', 'API\CategoryRateController@index');
Route::post('/v2/fetchCategoryRate', 'API\CategoryRateController@show');
Route::post('/v2/fetchCategoryRateType', 'API\CategoryRateController@edit');

// Dependent Data
Route::post('/v2/fetchPartyFormDependentData', 'API\DependentController@fetchPartyFormDependentData');
Route::post('/v2/fetchCollectionFormDependentData', 'API\DependentController@fetchCollectionFormDependentData');
Route::post('/v2/fetchOrderFormDependentData', 'API\DependentController@fetchOrderFormDependentData');

// Fetch ClientSettings
Route::post('/v2/fetchClientSetting','API\CommonController@fetchClientSetting');

// Attendance
Route::post('/v2/fetchAttendance', 'API\AttendanceController@index');
Route::post('/v2/saveAttendance', 'API\AttendanceController@store');
Route::post('/v2/syncAttendances', 'API\AttendanceController@syncAttendances');

// Activities
Route::post('/v2/fetchActivities', 'API\ActivityController@index');
Route::post('/v2/fetchActivitiesChanges', 'API\ActivityController@fetchActivitiesChanges');
Route::post('/v2/showActivity/{id}', 'API\ActivityController@show');
Route::post('/v2/saveActivity', 'API\ActivityController@store');
Route::post('/v2/updateActivity/{id}', 'API\ActivityController@update');
Route::post('/v2/removeActivity/{id}', 'API\ActivityController@destroy');
Route::post('/v2/syncActivities', 'API\ActivityController@syncActivities');
Route::post('/v2/fetchActivityTypes', 'API\ActivityTypeController@index');
Route::post('/v2/fetchActivityPriorities', 'API\ActivityPriorityController@index');

// Custom Field
Route::post('/v2/fetchCustomFields','API\CustomFieldController@index');

// Employees
Route::post('/v2/fetchEmployees', 'API\EmployeeController@index');

// Products
Route::post('/v2/fetchProducts', 'API\ProductController@index');

// Clients
Route::post('/v2/fetchClients', 'API\ClientController@index');
Route::post('/v2/saveClient', 'API\ClientController@saveClient');
Route::post('/v2/syncClients', 'API\ClientController@syncClients');
Route::post('/v2/fetchClientCustomField', 'API\ClientController@fetchClientCustomField');
Route::post('/v2/fetchClientOrders', 'API\ClientController@fetchClientOrders');
Route::post('/v2/fetchClientCollections', 'API\ClientController@fetchClientCollections');
Route::post('/v2/fetchClientExpenses', 'API\ClientController@fetchClientExpenses');
Route::post('/v2/fetchClientZeroOrders', 'API\ClientController@fetchClientZeroOrders');
Route::post('/v2/fetchClientNotes', 'API\ClientController@fetchClientNotes');
Route::post('/v2/fetchClientActivities', 'API\ClientController@fetchClientActivities');
Route::post('/v2/fetchClientVisits', 'API\ClientController@fetchClientVisits');

Route::post('/v2/getFiles', 'API\ClientController@getFiles');
Route::post('/v2/createUploadFolder', 'API\ClientController@createUploadFolder');
Route::post('/v2/updateUploadFolder', 'API\ClientController@updateUploadFolder');
Route::post('/v2/deleteUploadFolder', 'API\ClientController@deleteUploadFolder');

Route::post('/v2/uploadFolderItems', 'API\ClientController@uploadFolderItems');
Route::post('/v2/updateFolderItems', 'API\ClientController@updateFolderItems');
Route::post('/v2/deleteFolderItems', 'API\ClientController@deleteFolderItems');


// Visit Module
Route::post('/v2/fetchVisitModule', 'API\ClientVisitController@index');
Route::post('/v2/syncVisitModule', 'API\ClientVisitController@syncVisitModule'); 
Route::post('/v2/saveVisitModule', 'API\ClientVisitController@store'); 
Route::post('/v2/updateVisitModule', 'API\ClientVisitController@update'); 
Route::post('/v2/deleteVisitModule', 'API\ClientVisitController@destroy'); 

// Collections
Route::post('/v2/fetchCollection', 'API\CollectionController@index');
Route::post('/v2/fetchChainCollection', 'API\CollectionController@fetchChainCollection');
Route::post('/v2/fetchCollectionChanges', 'API\CollectionController@fetchCollectionChanges');
Route::post('/v2/saveCollection', 'API\CollectionController@store');
Route::post('/v2/syncCollection', 'API\CollectionController@syncCollection');
Route::post('/v2/fetchCollectionFormDependentData', 'API\CollectionController@fetchCollectionFormDependentData');
Route::post('/v2/removeCollection', 'API\CollectionController@destroy');

// Order
Route::post('/v2/fetchOrders', 'API\OrderController@index');
Route::post('/v2/fetchAdminOrders', 'API\OrderController@fetchAdminOrders');
Route::post('/v2/fetchOrderChanges', 'API\OrderController@fetchOrderChanges');
Route::post('/v2/updateOrderStatus', 'API\OrderController@updateOrderStatus');
Route::post('/v2/saveOrder', 'API\OrderController@store');
Route::post('/v2/updateOrder', 'API\OrderController@update');
Route::post('/v2/syncOrders', 'API\OrderController@syncOrders');
Route::post('/v2/deleteOrder', 'API\OrderController@destroy');
Route::post('/v2/getOrderById', 'API\OrderController@getOrderById');

// noOrder
Route::post('/v2/fetchNoOrder', 'API\NoOrderController@index');
Route::post('/v2/fetchAdminNoOrder', 'API\NoOrderController@fetchChainNoOrder');
Route::post('/v2/fetchNoOrderChanges', 'API\NoOrderController@fetchNoOrderChanges');
Route::post('/v2/saveNoOrder', 'API\NoOrderController@store');
Route::post('/v2/syncNoOrder', 'API\NoOrderController@syncNoOrder');
Route::post('/v2/deleteNoOrder', 'API\NoOrderController@destroy');

// Leave
Route::post('/v2/fetchLeave', 'API\LeaveController@index');
Route::post('/v2/fetchPendingLeave', 'API\LeaveController@fetchPendingLeave');
Route::post('/v2/changeLeaveStatus', 'API\LeaveController@changeLeaveStatus');
Route::post('/v2/saveLeave', 'API\LeaveController@store');
Route::post('/v2/deleteLeave', 'API\LeaveController@destroy');
Route::post('/v2/syncLeave', 'API\LeaveController@syncLeave');

// Expense
Route::post('/v2/fetchExpense', 'API\ExpenseController@index');
Route::post('/v2/fetchPendingExpense', 'API\ExpenseController@fetchPendingExpense');
Route::post('/v2/changeExpenseStatus', 'API\ExpenseController@changeExpenseStatus');
Route::post('/v2/saveExpense', 'API\ExpenseController@store');
Route::post('/v2/syncExpense', 'API\ExpenseController@syncExpense');
Route::post('/v2/removeExpense', 'API\ExpenseController@destroy');

// Holiday
Route::post('/v2/fetchHolidays', 'API\HolidayController@index');

// API routes for Beats, Beatplans
Route::post('/v2/fetchBeatplans', 'API\BeatPlanController@fetchBeatplans');
Route::post('/v2/fetchBeats', 'API\CommonController@fetchBeats');
Route::post('/v2/fetchBeatsParties', 'API\BeatPlanController@fetchBeatsParties');
Route::post('/v2/saveBeatplan', 'API\BeatPlanController@store');
Route::post('/v2/syncBeatplan','API\BeatPlanController@syncBeatplan');

// Tourplan
Route::post('/v2/fetchTourPlans', 'API\TourPlanController@index');
Route::post('/v2/fetchPendingTourPlan', 'API\TourPlanController@fetchPendingTourPlan');
Route::post('/v2/changeTourPlanStatus', 'API\TourPlanController@changeTourPlanStatus');
Route::post('/v2/saveTourPlans', 'API\TourPlanController@store');
Route::post('/v2/deleteTourPlans', 'API\TourPlanController@destroy');
Route::post('/v2/syncTourPlans', 'API\TourPlanController@syncTourPlans');

// Stocks
Route::post('/v2/fetchStock','API\StockController@index');
Route::post('/v2/saveStock','API\StockController@store');
Route::post('/v2/syncStock','API\StockController@syncStock');

// Returns
Route::post('/v2/fetchreturns','API\ReturnController@index');
Route::post('/v2/fetchreturnreasons', 'API\ReturnController@fetchReturnReason');
Route::post('/v2/savereturns','API\ReturnController@saveReturns');
Route::post('/v2/syncreturns','API\ReturnController@syncReturns');

// DayRemark
Route::post('/v2/fetchdayremarks','API\DayRemarkController@index');
Route::post('/v2/savedayremarks','API\DayRemarkController@store');
Route::post('/v2/syncdayremarks','API\DayRemarkController@syncDayRemark');
Route::post('/v2/removedayremarks','API\DayRemarkController@destroy');

// Location
Route::post('/v2/fetchLocation', 'API\LocationController@index');
Route::post('/v2/saveLocation', 'API\LocationController@store');
Route::post('/v2/syncLocations', 'API\LocationController@syncLocations');

//GPS
Route::post('/v2/syncGPSTrigger', 'API\GPSTriggerController@sync');

// colaterals
Route::post('/v2/fetchCollateralFiles', 'API\CollateralController@fetchCollateralFiles');

// Meetings or Notes
Route::post('/v2/fetchMeeting', 'API\NoteController@index');
Route::post('/v2/saveMeeting', 'API\NoteController@store');
Route::post('/v2/syncMeeting', 'API\NoteController@syncMeeting');
Route::post('/v2/removeNote', 'API\NoteController@destroy');

/**
 * Api Routes for analytics
 */

// ** APIS for employees tab

Route::post('/v2/count-parameters', 'API\AnalyticController@countParameters');
Route::post('/v2/get-no-of-orders', 'API\AnalyticController@getNoOfOrders');
Route::post('/v2/get-order-value', 'API\AnalyticController@getOrderValue');
Route::post('/v2/get-collection-amount', 'API\AnalyticController@getCollectionAmount');
Route::post('/v2/count-beat-parameters', 'API\AnalyticController@countBeatParameters');
Route::post('/v2/top-parties', 'API\AnalyticController@topParties');

// ** APIS for parties tab
Route::post('/v2/count-parties-parameters', 'API\AnalyticController@countPartiesParameters');
Route::post('/v2/get-no-of-orders-for-parties', 'API\AnalyticController@getNoOfOrdersForParties');
Route::post('/v2/get-order-value-for-parties', 'API\AnalyticController@getOrderValueForParties');
Route::post('/v2/get-collection-amount-for-parties', 'API\AnalyticController@getCollectionAmountForParties');


// APIs for schemes

Route::get('/v2/get-schemes', 'API\SchemeController@index');
Route::post('/v2/get-applied-schemes','API\SchemeController@getAppliedSchemes');

//Route::post('/v2/check-available-schemes', 'API\SchemeController@getAllAvailableSchemes');
//Route::post('/v2/apply-schemes', 'API\SchemeController@applySchemes');


Route::post('/v2/get-custom-modules', 'API\CustomModuleController@getAllCustomModule');
Route::post('/v2/create-custom-modules-form','API\CustomModuleFormController@create');
Route::post('/v2/get-custom-modules-data','API\CustomModuleFormController@index');
Route::post('/v2/store-custom-modules-data','API\CustomModuleFormController@store');
Route::post('/v2/edit-custom-modules-data','API\CustomModuleFormController@edit');
Route::post('/v2/update-custom-modules-data','API\CustomModuleFormController@update');
Route::post('/v2/delete-custom-modules-data','API\CustomModuleFormController@destroy');

//API for odometer

Route::post('/v2/get-odometer-data','API\OdometerController@getOdometerData');
Route::post('/v2/odometer-store','API\OdometerController@store');
Route::post('/v2/odometer-sync','API\OdometerController@syncOdometerRecord');

/*API END*/
