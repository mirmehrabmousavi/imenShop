<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/all-product',  [App\Http\Controllers\Api\ApiController::class, 'allProduct']);
Route::get('/product/{post}',  [App\Http\Controllers\Api\ApiController::class, 'single']);
Route::post('/login/',  [App\Http\Controllers\Api\ApiController::class, 'login']);
Route::post('/user/register/',  [App\Http\Controllers\Api\ApiController::class, 'register']);
Route::get('/single/option/',  [App\Http\Controllers\Api\ApiController::class, 'option']);
Route::post('/single/like/',  [App\Http\Controllers\Api\ApiController::class, 'sendLike']);
Route::post('/single/bookmark/',  [App\Http\Controllers\Api\ApiController::class, 'sendBookmark']);
Route::post('/user/getUser/',  [App\Http\Controllers\Api\ApiController::class, 'getUser']);
Route::post('/user/getUserLike/',  [App\Http\Controllers\Api\ApiController::class, 'getUserLike']);
Route::post('/user/getUserBookmark/',  [App\Http\Controllers\Api\ApiController::class, 'getUserBookmark']);
Route::post('/user/getUserPay/',  [App\Http\Controllers\Api\ApiController::class, 'getUserPay']);
Route::post('/user/getPayMeta/',  [App\Http\Controllers\Api\ApiController::class, 'getPayMeta']);
Route::post('/user/changeUserInfo/',  [App\Http\Controllers\Api\ApiController::class, 'changeUserInfo']);
Route::post('/addCart/',  [App\Http\Controllers\Api\ApiController::class, 'addCart']);
Route::get('/allCategory/',  [App\Http\Controllers\Api\ApiController::class, 'allCategory']);
Route::get('/category/{category}',  [App\Http\Controllers\Api\ApiController::class, 'category']);
Route::get('/brand/{brand}',  [App\Http\Controllers\Api\ApiController::class, 'brand']);
Route::get('/vidget/{vidget}',  [App\Http\Controllers\Api\ApiController::class, 'vidget']);
Route::post('/getCart',  [App\Http\Controllers\Api\ApiController::class, 'getCart']);
Route::post('/increaseCart',  [App\Http\Controllers\Api\ApiController::class, 'increaseCart']);
Route::post('/decreaseCart',  [App\Http\Controllers\Api\ApiController::class, 'decreaseCart']);
Route::delete('/deleteCart',  [App\Http\Controllers\Api\ApiController::class, 'deleteCart']);
Route::get('/order/',  [App\Http\Controllers\Api\ApiController::class, 'order']);
Route::get('/order/nextpay',  [App\Http\Controllers\Api\ApiController::class, 'nextpay']);
Route::post('/shop/successPay',  [App\Http\Controllers\Api\ApiController::class, 'successPay']);
Route::post('/shop/failPay',  [App\Http\Controllers\Api\ApiController::class, 'failPay']);
Route::post('/shop/successPayNextpay',  [App\Http\Controllers\Api\ApiController::class, 'successPayNextpay']);
Route::post('/shop/failPayNextpay',  [App\Http\Controllers\Api\ApiController::class, 'failPayNextpay']);
Route::post('/shop/getPay',  [App\Http\Controllers\Api\ApiController::class, 'getPay']);
Route::post('/search',  [App\Http\Controllers\Api\ApiController::class, 'search']);
Route::get('/get-site-info',  [App\Http\Controllers\Api\ApiController::class, 'getSiteInfo']);
Route::post('/send-code',  [App\Http\Controllers\Api\ApiController::class, 'sendCode']);
Route::post('/register',  [App\Http\Controllers\Api\ApiController::class, 'register']);
Route::post('/check-code',  [App\Http\Controllers\Api\ApiController::class, 'checkCode']);
Route::post('/change-time',  [App\Http\Controllers\Api\ApiController::class, 'changeTimeDelivery']);
Route::post('/change-carrier',  [App\Http\Controllers\Api\ApiController::class, 'changeCarrier']);
