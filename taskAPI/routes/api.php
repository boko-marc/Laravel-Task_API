<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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

// protected  routes
Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/getUser',[AuthController::class,'show']);
    Route::get('/deleteUser',[AuthController::class,'destroy']);
    Route::put('/updatePicture',[AuthController::class,'update_picture']);
});

// public routes
Route::get('/', function() {
    return "Welcome in my fist API with Laravel but i love Node js";
});
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::get('/allUsers',[AuthController::class,'index']);


