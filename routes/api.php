<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
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
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});
Route::middleware('jwt.verify')->group(function (){

    ################# GROUP ROUTES ##############
    Route::post('/create_group',[GroupController::class,'create_group']);
    Route::post('/joinOrder',[GroupController::class,'joinOrder']);
    Route::get('/getPendingOrder/{groupID}',[GroupController::class,'getPendingOrder']);
    Route::post('/approvePendingOrder',[GroupController::class,'approvePendingOrder']);
    Route::get('/get_all_groups/{id}',[GroupController::class,'getAllGroups']);
    Route::get('/get_AllGroups',[GroupController::class,'get_AllGroups']);
    Route::delete('/delete_group/{userid}',[GroupController::class,'deleteGroup']);
    Route::post('/addUserToGroup/{groupID}/{userID}',[GroupController::class,'addUserToGroup']);
    #################  END    ################

    ####################   FILES ROUTES   ##########
    Route::post('/addFile',[FileController::class,'addFile']);
    Route::get('/cancelReservation/{id}',[FileController::class,'cancelReservation']);
    Route::post('check_in/{id}',[FileController::class,'check_in']);
    Route::post('check_in_m',[FileController::class,'check_in_m']);
    Route::post('check_out/',[FileController::class,'check_out']);
    Route::get('get_groupFile/{id}',[FileController::class,'get_groupFile']);
    Route::get('getFileRecord/{id}/{userID}',[FileController::class,'getFileRecord']);


    ###################   END    ###############

    Route::get('getUsers/{group_id}',[UserController::class,'getUsers']);
    Route::get('test/{id}',[FileController::class,'test']);




});

Route::get('get',[FileController::class,'getfile']);

