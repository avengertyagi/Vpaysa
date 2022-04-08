<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\LoginController;


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

//website routes
Route::get('/', [Controller::class, 'home'])->name('home');
Route::controller(HomeController::class)->group(function () {
    Route::get('/admin', 'index');
    Route::get('/post', 'searchseo');
});
//admin routes
Auth::routes();
// Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
//     Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
//     Route::post('/', [LoginController::class, 'Createlogin'])->name('showlogin');
//     Route::controller(UserController::class)->group(function () {
//         Route::get('/addUsers', 'create');
//         Route::post('/storeUser', 'store');
//     });
// });
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
});
