<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
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
    Route::post('/user/logout',[AuthController::class,'logout']);
    Route::get('/user/get/user',[AuthController::class,'show']);
    Route::delete('/user/delete/user',[AuthController::class,'destroy']);
    Route::put('/user/update/picture',[AuthController::class,'updatePicture']);
    Route::put('/user/update/profil',[AuthController::class,'updateProfil']);
    Route::put('/user/reset/password',[AuthController::class,'resetPassword']);
    Route::post('/user/create/task',[TaskController::class,'createTask']);
    Route::put('/user/update/task/{id}',[TaskController::class,'updateTask']);
    Route::get('/user/tasks',[TaskController::class,'getAllTasksCreateByUser']);
    Route::delete('/user/delete/task/{id}',[TaskController::class,'deleteTask']);
    Route::put('/user/update/task/status/{id}',[TaskController::class,'updateTaskStatus']);
    Route::get('/user/tasks/todo',[TaskController::class,'getAllTasksToDoByUser']);
    Route::get('/user/tasks/doing',[TaskController::class,'getAllTasksDoingByUser']);
    Route::get('/user/tasks/end',[TaskController::class,'getAllTasksEndByUser']);
});

// public routes
Route::get('/', function() {
    return "Welcome in my fist API with Laravel but i love Node js";
});
Route::post('/auth/register',[AuthController::class,'register']);
Route::post('/auth/login',[AuthController::class,'login']);
Route::get('/user/all/users',[AuthController::class,'index']);
Route::get('/task/all/tasks',[TaskController::class,'index']);
Route::put('/auth/activation/account/{id}',[AuthController::class,'activationCompte']);
Route::post('/user/receive/mail/forgot/password',[AuthController::class,'receiveEmailToForgotPassword']);
Route::put('/user/change/password/{token}',[AuthController::class,'changePassword']);

