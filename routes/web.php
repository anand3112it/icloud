<?php

use Illuminate\Support\Facades\Route;

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
    return redirect('login');
});

Auth::routes();

Route::prefix('task')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/home', 'HomeController@index')->name('home');
        Route::get('/import', 'ImportController@index')->name('import');
        Route::post('/import/store', 'ImportController@store')->name('storeImportData');
        Route::any('/transaction/list', 'TransactionController@index');
        Route::any('/transaction/sublist', 'TransactionController@details');
    });
});
