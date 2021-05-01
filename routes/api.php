<?php

use App\Http\Controllers\API\V1\Auth\OAuthController;
use App\Http\Controllers\API\V1\Auth\RegisterController;
use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Auth\PermissionController;
use App\Http\Controllers\API\V1\Admin\RoleController;
use App\Http\Controllers\API\V1\Admin\UserController;
use App\Http\Controllers\API\V1\Admin\CategoryController;
use App\Http\Controllers\API\V1\Admin\BoxesController;
use App\Http\Controllers\API\V1\Admin\LootController;
use App\Http\Controllers\API\V1\OpenController;
use App\Http\Controllers\API\V1\CartController;
use App\Http\Controllers\API\V1\StatController;
use App\Http\Controllers\API\V1\ProfileController;
use App\Http\Controllers\API\V1\PaymentController;
use App\Http\Controllers\API\V1\AuctionController;
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

Route::prefix('v1')->group(function () {
    /*
     * Profile
     */
    Route::group(['middleware' => 'auth:api'], function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::post('me', [AuthController::class, 'me']);
            Route::get('permissions', PermissionController::class);

            Route::post('changedata', [ProfileController::class, 'changeData']);
            Route::post('changepassword', [ProfileController::class, 'changePassword']);
            Route::post('imageupload', [ProfileController::class, 'imageUpload']);
        });
        Route::prefix('open')->group(function () {
            Route::get('{box}', [OpenController::class, 'real']);
        });
        Route::prefix('cart')->group(function () {
            Route::get('sell/{id}', [CartController::class, 'sell']);
            Route::get('list', [CartController::class, 'list']);
            Route::post('delivery', [CartController::class, 'delivery']);
        });
        Route::get('sell/{id}', [CartController::class, 'sell']);
        Route::post('payment', [PaymentController::class, 'redirect']);
        Route::post('auction/rate', [AuctionController::class, 'rate']);
        Route::get('auction/personal', [AuctionController::class, 'personal']);
    });
    Route::get('auction/top', [AuctionController::class, 'top']);
    Route::get('auction/get/{id}', [AuctionController::class, 'get']);
    Route::get('auction', [AuctionController::class, 'list']);
    Route::get('avatar/{id}', [ProfileController::class, 'avatar']);
    Route::get('open/demo/{box}', [OpenController::class, 'demo']);
    Route::get('open/last_top_gift/{box}', [OpenController::class, 'last_top_gift']);
    Route::get('stats', [StatController::class, 'stat']);
    Route::get('/payment/handler', [PaymentController::class, 'handlePayment']);
    
    /*
     * Auth
     */
    Route::post('auth/register', [RegisterController::class, 'register']);
    /*
         * Social
         */
    Route::prefix('oauth')->group(function () {
        Route::post('{driver}', [OAuthController::class, 'redirect']);
        Route::get('{driver}/callback', [OAuthController::class, 'handleCallback'])->name('oauth.callback');
    });
    Route::post('auth/login', [AuthController::class, 'login']);

    /*
     * Admin Panel
     */
    Route::group(['middleware' => 'auth:api'], function () {
        Route::resource('users', UserController::class);
        Route::resource('loot', LootController::class);
    });
    Route::resource('boxes', BoxesController::class);
    Route::resource('categories', CategoryController::class);
});
