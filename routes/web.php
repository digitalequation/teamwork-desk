<?php

use Illuminate\Support\Facades\Route;

Route::post('/tdhook/priority', 'TeamworkWebhookController@postPriority');
Route::post('/tdhook/status', 'TeamworkWebhookController@postStatus');
Route::post('/tdhook/delete', 'TeamworkWebhookController@postDelete');
Route::post('/tdhook/reply', 'TeamworkWebhookController@postReply');
Route::post('/tdhook/note', 'TeamworkWebhookController@postNote');