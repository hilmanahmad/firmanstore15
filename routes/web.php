<?php

use App\Livewire\Dashboard;
use App\Livewire\BuybackCalculator;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GoldRateController; // Tambahkan ini
use App\Http\Controllers\ItemHistoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ItemPurchaseController;
use App\Http\Controllers\RoleMenuAccessController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecordingController;

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

    // Role Management
    Route::resource('role', RoleController::class);
    Route::get('role-datatable', [RoleController::class, 'datatable'])->name('roledatatable');
    Route::get('role-all', [RoleController::class, 'getAll'])->name('role.all');

    // Role Menu Access
    Route::get('role-menu-access', [RoleMenuAccessController::class, 'index'])->name('role-menu-access.index');
    Route::get('role-menu-access/get-by-role', [RoleMenuAccessController::class, 'getByRole'])->name('role-menu-access.get-by-role');
    Route::post('role-menu-access', [RoleMenuAccessController::class, 'store'])->name('role-menu-access.store');
    Route::post('role-menu-access/update-single', [RoleMenuAccessController::class, 'updateSingle'])->name('role-menu-access.update-single');
    Route::post('role-menu-access/copy', [RoleMenuAccessController::class, 'copyAccess'])->name('role-menu-access.copy');

    Route::resource('user', UserController::class);
    Route::get('user-datatable', [UserController::class, 'datatable'])->name('userdatatable');
    Route::resource('menu', MenuController::class);
    Route::get('menu-datatable', [MenuController::class, 'datatable'])->name('menudatatable');
});

// Routes with menu access middleware
Route::middleware(['auth', 'menu.access'])->group(function () {
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
    Route::resource('item-purchase', ItemPurchaseController::class);
    Route::get('item-purchase-datatable', [ItemPurchaseController::class, 'datatable'])->name('itempurchasedatatable');
    Route::post('item-purchase/confirm-received', [ItemPurchaseController::class, 'confirmReceived'])->name('item-purchase.confirm');

    Route::get('transaction-datatable', [TransactionController::class, 'datatable'])->name('transactiondatatable');
    Route::get('transaction-detail/{id}', [TransactionController::class, 'getDetail'])->name('transaction.detail');
    Route::delete('transaction-delete-group/{noTrans}', [TransactionController::class, 'deleteGroup'])->name('transaction.deleteGroup');
    Route::prefix('report')->name('report.')->group(function () {
        Route::get('transaction', [ReportController::class, 'index'])->name('transaction');
    });
});

// Gold Rate
Route::get('gold-rate', [GoldRateController::class, 'index'])->name('gold-rate');
Route::get('gold-rate/fetch', [GoldRateController::class, 'getRate'])->name('gold-rate.fetch');

// Buyback Calculator
Route::get('buyback-calculator', BuybackCalculator::class)->name('buyback-calculator');


// OBS Recording Management
Route::resource('recording', RecordingController::class);
Route::get('recording-datatable', [RecordingController::class, 'datatable'])->name('recordingdatatable');
Route::post('recording/start', [RecordingController::class, 'startRecording'])->name('recording.start');
Route::post('recording/stop', [RecordingController::class, 'stopRecording'])->name('recording.stop');
Route::post('recording/complete', [RecordingController::class, 'completeRecording'])->name('recording.complete');
// OBS WebSocket Proxy (API endpoints - only auth required, not menu.access)
Route::post('recording/obs/connect', [RecordingController::class, 'obsConnect'])->name('recording.obs.connect');
Route::post('recording/obs/start-record', [RecordingController::class, 'obsStartRecord'])->name('recording.obs.start');
Route::post('recording/obs/stop-record', [RecordingController::class, 'obsStopRecord'])->name('recording.obs.stop');
Route::post('recording/obs/disconnect', [RecordingController::class, 'obsDisconnect'])->name('recording.obs.disconnect');

// OBS Settings per-user
Route::post('recording/obs-settings', [RecordingController::class, 'obsSettingsStore'])->name('recording.obs-settings.store');
Route::put('recording/obs-settings/{id}', [RecordingController::class, 'obsSettingsUpdate'])->name('recording.obs-settings.update');
Route::delete('recording/obs-settings/{id}', [RecordingController::class, 'obsSettingsDestroy'])->name('recording.obs-settings.destroy');
Route::post('recording/obs-settings/test', [RecordingController::class, 'obsSettingsTest'])->name('recording.obs-settings.test');
