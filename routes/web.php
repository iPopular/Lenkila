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
        return Redirect::to('/'. Auth::user()->stadium->name .'/report_problems');
    else
        return Redirect::to('/login');
});

Auth::routes();

Route::get('/{stadium}/dashboard', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'DashBoardController@show',
    'roles' => ['owner']
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
    'roles' => ['owner']
]);

Route::post('/{stadium}/add-account', [
    'middleware' => ['auth', 'roles', 'stadium'], 
    'uses' => 'AccountController@addAccount',
    'roles' => ['owner']
]);

Route::post('/{stadium}/update-account/{username}', [
    'middleware' => ['auth', 'roles', 'stadium'], 
    'uses' => 'AccountController@updateAccount',
    'roles' => ['owner']
]);

Route::post('/{stadium}/delete-account', [
    'middleware' => ['auth', 'roles', 'stadium'], 
    'uses' => 'AccountController@deleteAccount',
    'roles' => ['owner']
]);

Route::get('/{stadium}/report_problems', [
    'middleware' => ['auth', 'roles', 'stadium'],
    'uses' => 'ReportProblemsController@show',
    'roles' => ['owner', 'administrator', 'staff']
]);

