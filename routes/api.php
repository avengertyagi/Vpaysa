<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ForgotController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\SellerController;
use App\Http\Controllers\Api\SellerTransaction;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\UserprofileController;
use App\Http\Controllers\Api\ChangepasswordController;

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
Route::post('/userRegister', [RegisterController::class, 'storeRegister'])->name('userRegister');
Route::post('/userLogin', [LoginController::class, 'userLogin'])->name('userLogin');
Route::post('/userchangepassword', [ChangepasswordController::class, 'changePassword'])->name('userchangepassword');
Route::post('/userForgotpassword',[ForgotController::class,'forgotpassword'])->name('forgotpassword');

Route::controller(SellerController::class)->group(function(){
    Route::post('/userSeller', 'storeSeller');
    Route::get('/usermytransaction','myTransaction');
});

Route::controller(UserprofileController::class)->group(function () {
    Route::post('/userDebit', 'userCard');
    Route::post('/userDebitupdate/{id}', 'updateCard');
    Route::post('/userAddress', 'saveAddress');
    Route::post('/userAddressupdate/{id}', 'updateAddress');
    Route::post('/userProfileupdate/{id}','updateProfile');
});
Route::get('/userLogout',[LogoutController::class,'userLogout'])->name('userLogout');
