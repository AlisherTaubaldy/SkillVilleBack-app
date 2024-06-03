<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\GroupMessageController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PrivateMessageController;
use App\Http\Controllers\PusherController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'store']);
    Route::post('register', [AuthController::class, 'create']);

    Route::middleware('auth:api')->group(function() {
        Route::post('/pusher/auth', [PusherController::class, 'auth']);

        Route::get('/messages/{chatId}', [MessageController::class, "fetchMessages"]);

        Route::post('/group-messages', [GroupMessageController::class, 'sendMessage']);
        Route::post('/group-messages/create', [GroupMessageController::class, 'createGroupChat']);

        Route::post('/private-messages', [PrivateMessageController::class, 'sendMessage']);

        Route::get("/all-chats", [ChatController::class, "getAllChats"]);

        Route::get("/course/get-all", [CourseController::class, "get"]);
        Route::post("/course/create", [CourseController::class, "store"]);
        Route::post("/course/upload", [CourseController::class, 'uploadImage']);
        Route::post("/module/upload", [CourseController::class, 'uploadVideo']);
        Route::get("/course/get-detail/{courseId}", [CourseController::class, 'getCourseDetail']);

        Route::post("/review/create", [ReviewController::class, 'store']);

    });
});
