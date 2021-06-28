<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\LogsController;

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

Route::post('carga', [CajaController::class, 'store']);

Route::get('estado', [CajaController::class, 'show']);

Route::get('logs', [LogsController::class, 'show']);

Route::put('pagar', [CajaController::class, 'pago']);

Route::delete('vaciar', [CajaController::class, 'truncate']);