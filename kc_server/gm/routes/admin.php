<?php

use Illuminate\Support\Facades\Route;

#补充路由必须在resource之前添加
#账号相关
Route::resource('players', 'PlayerController');
Route::resource('mails', 'MailController');
Route::resource('gmcmds', 'GmcmdController');
