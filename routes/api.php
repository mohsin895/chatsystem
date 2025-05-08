<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
// If using Sanctum or auth
Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::middleware('auth:sanctum')->post('/pusher/auth', function () {
    return Broadcast::auth(request());
});

Route::post('registration', [\App\Http\Controllers\Api\AuthController::class, 'signup']);
Route::post('login', [\App\Http\Controllers\Api\AuthController::class,'login']);

Route::middleware( Admin::class)->group(function () {

    Route::get('/info',[\App\Http\Controllers\Api\User\UserController::class,'info']);
    Route::group(['prefix'=>'admin'], function(){

        Route::get('/chat', [App\Http\Controllers\Api\ChatController::class, 'adminChat']);
        Route::get('/chat/users', [App\Http\Controllers\Api\ChatController::class, 'getUsers']);
        Route::get('/chat/messages/{user}', [App\Http\Controllers\Api\ChatController::class, 'getMessages']);
        Route::post('/chat/send', [App\Http\Controllers\Api\ChatController::class, 'sendMessage']);
     

      Route::group(['prefix'=>'ticket'], function(){
        Route::get('/get/list', [App\Http\Controllers\Api\Admin\TicketController::class,'index']);
        Route::post('/comments', [App\Http\Controllers\Api\Admin\TicketController::class, 'comments']);
        Route::post('/comments/reply', [App\Http\Controllers\Api\Admin\TicketController::class, 'storeReply']);
        Route::post('/status', [App\Http\Controllers\Api\Admin\TicketController::class, 'updateStatus']);
        Route::get('/info', [App\Http\Controllers\Api\Customer\TicketController::class,'info']);
        
    });
});

});

Route::middleware( User::class)->group(function () {
    Route::get('/info',[\App\Http\Controllers\Api\User\UserController::class,'info']);
    
    Route::group(['prefix'=>'ticket'], function(){
        Route::get('/get/list', [App\Http\Controllers\Api\Customer\TicketController::class,'index']);
        Route::get('/info', [App\Http\Controllers\Api\Customer\TicketController::class,'info']);
        Route::post('/insert/update', [App\Http\Controllers\Api\Customer\TicketController::class, 'dataInfoAddOrUpdate']);
        Route::post('/comments', [App\Http\Controllers\Api\Customer\TicketController::class, 'comments']);
        Route::post('/comments/reply', [App\Http\Controllers\Api\Customer\TicketController::class, 'storeReply']);
        Route::post('/delete', [App\Http\Controllers\Api\Customer\TicketController::class, 'dataInfoDelete']);
     

        
    });

    Route::get('/chat', [App\Http\Controllers\Api\ChatController::class, 'userChat']);
    Route::get('/chat/messages', [App\Http\Controllers\Api\ChatController::class, 'getUserMessages']);
    Route::post('/chat/send', [App\Http\Controllers\Api\ChatController::class, 'sendUserMessage']);
});