<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemHistoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Administrator\MenuController;
use App\Http\Controllers\Administrator\UserController;

Route::get('/', [AuthController::class, 'index'])->middleware('guest');
Route::post('login', [AuthController::class, 'authentication'])->name('login');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('option-ajax', [AjaxController::class, 'optionAjax'])->name('optionAjax');
Route::get('option-ajax-where', [AjaxController::class, 'optionAjaxWhere'])->name('optionAjaxWhere');
Route::get('option-ajaxx', [AjaxController::class, 'optionAjaxx'])->name('optionAjaxx');
Route::get('option-regencie', [AjaxController::class, 'optionRegencie'])->name('optionRegencie');
Route::get('option-district', [AjaxController::class, 'optionDistrict'])->name('optionDistrict');
Route::get('tariff-table', [AjaxController::class, 'tariffTable'])->name('tariffTable');
Route::get('order-handling', [AjaxController::class, 'orderHandling'])->name('orderHandling');
Route::get('data-order-handling', [AjaxController::class, 'dataOrderHandling'])->name('dataOrderHandling');
Route::get('tariff-vendor', [AjaxController::class, 'tariffVendor'])->name('tariffVendor');
Route::get('option-top', [AjaxController::class, 'termOfPayment'])->name('termOfPayment');
Route::get('update-chart-data', [AjaxController::class, 'updateChartData'])->name('updateChartData');
Route::get('update-chart-data', [AjaxController::class, 'updateChartData'])->name('updateChartData');
Route::get('generate-tariff', [AjaxController::class, 'generateTariff'])->name('generate-tariff');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('recap-order-datatable', [HomeController::class, 'datatable'])->name('recaporderdatatable');

    Route::resource('user', UserController::class);
    Route::get('user-datatable', [UserController::class, 'datatable'])->name('userdatatable');
    Route::resource('menu', MenuController::class);
    Route::get('menu-datatable', [MenuController::class, 'datatable'])->name('menudatatable');
    Route::resource('customer', CustomerController::class);
    Route::get('customer-datatable', [CustomerController::class, 'datatable'])->name('customerdatatable');
    Route::resource('type', TypeController::class);
    Route::get('type-datatable', [TypeController::class, 'datatable'])->name('typedatatable');
    Route::resource('category', CategoryController::class);
    Route::get('category-datatable', [CategoryController::class, 'datatable'])->name('categorydatatable');
    Route::resource('item', ItemController::class);
    Route::get('item-datatable', [ItemController::class, 'datatable'])->name('itemdatatable');
    Route::resource('item-history', ItemHistoryController::class);
    Route::get('item-history-datatable', [ItemHistoryController::class, 'datatable'])->name('itemhistorydatatable');
    Route::resource('transaction', TransactionController::class);
    Route::get('transaction-datatable', [TransactionController::class, 'datatable'])->name('transactiondatatable');

    Route::prefix('report')->name('report.')->group(function () {
        Route::get('transaction', [ReportController::class, 'index'])->name('transaction');
    });
});
