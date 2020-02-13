<?php

use Illuminate\Support\Facades\Route;

Route::post('/tdhook/priority', 'TeamworkDeskWebhookController@postPriority');
Route::post('/tdhook/status', 'TeamworkDeskWebhookController@postStatus');
Route::post('/tdhook/delete', 'TeamworkDeskWebhookController@postDelete');
Route::post('/tdhook/reply', 'TeamworkDeskWebhookController@postReply');
Route::post('/tdhook/note', 'TeamworkDeskWebhookController@postNote');