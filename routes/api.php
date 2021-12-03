<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\VoucherController;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'api'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});
Route::group(['middleware' => ['jwt.verify']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => '/user'], function () {
        Route::post('/byID', [PenggunaController::class, 'byID']);
        Route::post('/', [PenggunaController::class, 'create']);
        Route::patch('/', [PenggunaController::class, 'update']);
    });
    Route::group(['prefix' => '/voucher'], function () {
        Route::get('/', [VoucherController::class, 'index']);
        Route::get('/all', [VoucherController::class, 'allData']);
        Route::post('/byID', [VoucherController::class, 'byID']);
    });
    Route::group(['prefix' => '/menu'], function () {
        Route::get('/', [MenuController::class, 'index']);
        Route::get('/all', [MenuController::class, 'allData']);
        Route::post('/byID', [MenuController::class, 'byID']);
    });
    Route::group(['prefix' => '/order'], function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'create']);
        Route::post('/ubah-status', [OrderController::class, 'changeStatus']);
        Route::get('/find/{id}', [OrderController::class, 'byID']);
    });

    Route::group(['prefix' => '/user'], function () {
        Route::get('/', [PenggunaController::class, 'index']);
        Route::delete('/', [PenggunaController::class, 'delete']);
        Route::get('/all', [PenggunaController::class, 'allData']);
    });
    Route::group(['prefix' => '/satuan'], function () {
        Route::get('/', [SatuanController::class, 'index']);
        Route::post('/', [SatuanController::class, 'create']);
        Route::patch('/', [SatuanController::class, 'update']);
        Route::delete('/', [SatuanController::class, 'delete']);
        Route::get('/all', [SatuanController::class, 'allData']);
        Route::post('/byID', [SatuanController::class, 'byID']);
    });
    Route::group(['prefix' => '/konfigurasi'], function () {
        Route::get('/', [KonfigurasiController::class, 'index']);
        Route::get('/find/{id}', [KonfigurasiController::class, 'show']);
        Route::post('/', [KonfigurasiController::class, 'create']);
        Route::patch('/', [KonfigurasiController::class, 'update']);
        Route::delete('/', [KonfigurasiController::class, 'delete']);
        Route::get('/all', [KonfigurasiController::class, 'allData']);
    });
    Route::group(['prefix' => '/stok'], function () {
        Route::get('/', [StokController::class, 'index']);
        Route::post('/', [StokController::class, 'create']);
        Route::patch('/', [StokController::class, 'update']);
        Route::delete('/', [StokController::class, 'delete']);
        Route::get('/all', [StokController::class, 'allData']);
        Route::post('/byID', [StokController::class, 'byID']);
    });
    Route::group(['prefix' => '/voucher'], function () {
        Route::post('/', [VoucherController::class, 'create']);
        Route::patch('/', [VoucherController::class, 'update']);
        Route::delete('/', [VoucherController::class, 'delete']);
    });
    Route::group(['prefix' => '/menu'], function () {
        Route::post('/', [MenuController::class, 'create']);
        Route::patch('/', [MenuController::class, 'update']);
        Route::delete('/', [MenuController::class, 'delete']);
    });
});
