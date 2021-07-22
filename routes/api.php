<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FolderController;

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

Route::post('login', [ApiController::class, 'authenticate']);
Route::post('register', [ApiController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']], function(){
    Route::group(['prefix' => '/document-service/'], function(){
        Route::get('/', [FolderController::class, 'index']);
        Route::group(['prefix' => '/folder/'], function(){
            Route::post('/', [FolderController::class, 'store']);
            Route::delete('/{folder_id}', [FolderController::class, 'destroy']);
            Route::get('/{folder_id}', [FolderController::class, 'show']);
        });
        Route::group(['prefix' => '/document/'], function(){
            Route::post('/', [DocumentController::class, 'store']);
            Route::delete('/{document_id}', [DocumentController::class, 'destroy']);
            Route::get('/{document_id}', [DocumentController::class, 'show']);
        });
    });
});
