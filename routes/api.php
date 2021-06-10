<?php

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
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('contacts')->group(function () {
        Route::get('/all', 'ContactsController@getAvailableContacts');
        Route::get('/initiations', 'ContactsController@getContactInitiations');
        Route::post('/iniciate', 'ContactsController@iniciateContact');
        Route::post('/iniciation/accept', 'ContactsController@acceptContact');
        Route::post('/iniciation/reject', 'ContactsController@rejectContact');
        Route::post('/remove', 'ContactsController@removeContact');
        Route::get('/', 'ContactsController@getUserContacts');
    });

    Route::prefix('messages')->group(function () {
        Route::get('/{id}', 'MessagesController@getMessagesBetween')->where('id', '[0-9]+');
        Route::post('/send', 'MessagesController@create');
        Route::put('/{message}/read', 'MessagesController@updateMessageReadStatus')->where('message', '[0-9]+');
    });
    
    Route::prefix('profile')->group(function () {
        Route::put('/update', 'UsersController@update');
        Route::put('/security/update', 'UsersController@updatePassword');
        Route::post('/delete', 'UsersController@delete');
    });
    Route::get('/avatar', function(){
        return response()->json(['avatar' => asset(Storage::url('img/avatar.png'))]);
    });
});
Broadcast::routes(['middleware' => ['auth:sanctum']]);
