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

Route::get('/', function () {
    if(count(Auth::user()) > 0)
    {
        if(Auth::user()->role_id != 4)
            return Redirect::to('/'. Auth::user()->stadium->name .'/today');
        else
            return Redirect::to('/'. Auth::user()->stadium->name .'/owner_management');
    }        
    else
    {
        return Redirect::to('/login');
    }
});

Auth::routes();

Route::get('/{stadium}/owner_management', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'OwnerController@show',
    'roles' => ['root']
]);

Route::post('/{stadium}/owner_management/add-owner', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'OwnerController@addOwner',
    'roles' => ['root']
]);

Route::post('/{stadium}/owner_management/update-account/{username}', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'OwnerController@updateOwner',
    'roles' => ['root']
]);

Route::post('/{stadium}/owner_management/delete-owner', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'OwnerController@deleteOwner',
    'roles' => ['root']
]);

Route::get('/{stadium}/stadium_management', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'StadiumController@show',
    'roles' => ['root']
]);

Route::post('/{stadium}/stadium_management/add-stadium', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'StadiumController@addStadium',
    'roles' => ['root']
]);

Route::post('/{stadium}/stadium_management/update-stadium', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'StadiumController@updateStadium',
    'roles' => ['root']
]);

Route::post('/{stadium}/stadium_management/delete-stadium', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'StadiumController@deleteStadium',
    'roles' => ['root']
]);

Route::get('/{stadium}/today', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReserveTodayController@show',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::post('/{stadium}/today-paid-reserve', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReserveTodayController@paidReserve',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::get('/{stadium}/dashboard', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'DashBoardController@show',
    'roles' => ['owner', 'administrator']
]);

Route::post('/{stadium}/add-field-price', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'DashBoardController@addFieldPrice',
    'roles' => ['owner']
]);

Route::post('/{stadium}/edit-field-price', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'DashBoardController@editFieldPrice',
    'roles' => ['owner']
]);

Route::post('/{stadium}/delete-field-price', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'DashBoardController@deleteFieldPrice',
    'roles' => ['owner']
]);

Route::post('/{stadium}/add-promotion', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'DashBoardController@addPromotion',
    'roles' => ['owner']
]);

Route::post('/{stadium}/edit-promotion', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'DashBoardController@editPromotion',
    'roles' => ['owner']
]);

Route::post('/{stadium}/delete-promotion', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'DashBoardController@deletePromotion',
    'roles' => ['owner']
]);

Route::post('/{stadium}/add-holiday', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'DashBoardController@addHoliday',
    'roles' => ['owner']
]);

Route::post('/{stadium}/edit-holiday', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'DashBoardController@editHoliday',
    'roles' => ['owner']
]);

Route::post('/{stadium}/delete-holiday', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'DashBoardController@deleteHoliday',
    'roles' => ['owner']
]);

Route::post('/{stadium}/edit-stadium', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'DashBoardController@editStadium',
    'roles' => ['owner']
]);

Route::get('/{stadium}/reservation', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReservationController@show',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::post('/{stadium}/add-field', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReservationController@addField',
    'roles' => ['owner']
]);

Route::post('/{stadium}/edit-field', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReservationController@editField',
    'roles' => ['owner']
]);

Route::post('/{stadium}/delete-field', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReservationController@deleteField',
    'roles' => ['owner']
]);

Route::post('/{stadium}/add-reserve', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReservationController@addReserve',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::post('/{stadium}/edit-reserve', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReservationController@editReserve',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::post('/{stadium}/paid-reserve', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReservationController@paidReserve',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::post('/{stadium}/delete-reserve', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReservationController@deleteReserve',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::post('/{stadium}/getCustomer', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReservationController@getCustomer',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::post('/{stadium}/getHoliday', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReservationController@getHoliday',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::get('/{stadium}/customer_info', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'CustomerInfoController@show',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::post('/{stadium}/add-customer', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'CustomerInfoController@addCustomer',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::post('/{stadium}/edit-customer', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'CustomerInfoController@editCustomer',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::post('/{stadium}/delete-customer', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'CustomerInfoController@deleteCustomer',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::get('/{stadium}/analysis', [
    'middleware' => ['auth', 'roles', 'stadium'], 
    'uses' => 'AnalysisController@show',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::post('/{stadium}/edit-best-customer', [
    'middleware' => ['auth', 'roles', 'stadium'], 
    'uses' => 'AnalysisController@editBestCustomer',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::post('/{stadium}/analysis-getStat', [
    'middleware' => ['auth', 'roles', 'stadium'], 
    'uses' => 'AnalysisController@getStat',
    'roles' => ['owner', 'administrator', 'staff']
]);

Route::get('/{stadium}/account_management', [
    'middleware' => ['auth', 'roles', 'stadium'], 
    'uses' => 'AccountController@show',
    'roles' => ['owner', 'administrator']
]);

Route::post('/{stadium}/add-account', [
    'middleware' => ['auth', 'roles', 'stadium'], 
    'uses' => 'AccountController@addAccount',
    'roles' => ['owner', 'administrator']
]);

Route::post('/{stadium}/update-account/{username}', [
    'middleware' => ['auth', 'roles', 'stadium'], 
    'uses' => 'AccountController@updateAccount',
    'roles' => ['owner', 'administrator']
]);

Route::post('/{stadium}/delete-account', [
    'middleware' => ['auth', 'roles', 'stadium'], 
    'uses' => 'AccountController@deleteAccount',
    'roles' => ['owner', 'administrator']
]);

Route::get('/{stadium}/report_problems', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReportProblemsController@show',
    'roles' => ['owner', 'administrator', 'staff']
]);

