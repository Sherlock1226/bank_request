<?php

use Illuminate\Support\Facades\Route;
use Vtiful\Kernel\Excel;

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
    return view('welcome');
});

Route::get('users', function()
{
    return 'Users!';
});

Route::get('/bank', 'App\Http\Controllers\BankRequestController@getInfo');
Route::get('/callbank', 'App\Http\Controllers\BankRequestController@callBank');
Route::get('/sap', 'App\Http\Controllers\SapController@loginSap');
Route::post('/export', 'App\Http\Controllers\BankRequestController@export');
Route::get('/getDetail', 'App\Http\Controllers\BankRequestController@getDetail');
Route::any('/Test', 'App\Http\Controllers\EasyTestController@Test');
Route::any('/manualcallBank', 'App\Http\Controllers\BankRequestController@manualcallBank');
Route::any('/megaBank', 'App\Http\Controllers\MegaBankController@callsFtp');


Route::get('bankdetailexport', function()
{
    return view('bankdetailexport');
});
