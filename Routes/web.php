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

use Modules\CommissionAgent\Http\Controllers\SalesTargetController;
use Modules\CommissionAgent\Http\Controllers\CommissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'])->group(function () {
    Route::resource('sales-targets', SalesTargetController::class);
    Route::get('view-commissions', 'CommissionController@viewCommissions')->name('viewCommissions');
    Route::get('view-sales-goal-report', 'CommissionController@salesGoalReport')->name('salesGoalReport');
});
