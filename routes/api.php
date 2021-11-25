<?php

use App\Http\Controllers\Api\ApiProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TelegramBotController;
use App\Http\Controllers\ProductController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group([
    'middleware' => 'api_basic',
], function ($router) {
    Route::group([
        'middleware' => 'api',
    ], function ($router) {
        Route::prefix('auth/customer')->group(function(){
            Route::post('/login', [AuthController::class, 'login'])->name('login');
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh-token', [AuthController::class, 'refresh']);
            Route::get('/user-profile', [AuthController::class, 'userProfile']);
            Route::post('/change-password', [AuthController::class, 'changePassWord']);
        });
    });
    Route::prefix('product')->group(function(){
        Route::get('/search',[ApiProductController::class , 'search']);
        Route::get('/detail',[ApiProductController::class , 'getDetailProduct']);
        Route::post('/add', [ApiProductController::class , 'postAdd']);
        Route::delete('/delete', [ApiProductController::class , 'delete']);
        Route::put('/edit', [ApiProductController::class , 'postEdit']);
    });
    Route::get('/updated-activity', [TelegramBotController::class,'updatedActivity']);
    Route::post('/send-message', [TelegramBotController::class,'sendMessage']);
});

