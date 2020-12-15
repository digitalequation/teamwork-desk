<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => config('teamwork-desk.authorization')], function () {
    Route::get('tickets', 'TeamworkDeskAPIController@getIndex');
    Route::get('tickets/ticket/{id}', 'TeamworkDeskAPIController@getTicket');
    Route::post('tickets', 'TeamworkDeskAPIController@postIndex');
    Route::post('tickets/reply', 'TeamworkDeskAPIController@postReply');
    Route::post('tickets/upload', 'TeamworkDeskAPIController@postUpload');
});
