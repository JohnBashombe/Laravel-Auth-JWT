<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::controller(TodoController::class)->group(function () {
    Route::get('todos', 'index');
    Route::get('todo/{id}', 'show');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::controller(TodoController::class)->group(function () {
        Route::post('todo',  'store');
        Route::put('todo/{id}',  'update');
        Route::delete('todo/{id}',  'destroy');
    });

    Route::middleware(['throttle:sms'])->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('verify', 'sendMessage');
        });
    });
});

Route::fallback(function () {
    return response()->json(['status' => 404, 'message' => 'page not found'], 404);
});
