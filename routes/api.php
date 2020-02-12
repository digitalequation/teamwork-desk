<?php

use Illuminate\Support\Facades\Route;

Route::get('tickets', 'TeamworkDeskAPIController@getIndex');
Route::get('tickets/{id}', 'TeamworkDeskAPIController@getTicket');
Route::post('tickets', 'TeamworkDeskAPIController@postIndex');
Route::post('tickets/reply', 'TeamworkDeskAPIController@postReply');
Route::post('tickets/upload', 'TeamworkDeskAPIController@postUpload');