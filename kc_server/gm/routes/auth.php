<?php

use Illuminate\Support\Facades\Route;

Route::prefix("admin")->group(function () {
    Route::any('auth/login', 'AdminAuthController@login')->name("login");
    Route::get('auth/wechat/qrcode', 'AdminAuthController@wechatQrcode');
    Route::get('auth/wechat/qrcode/status', 'AdminAuthController@wechatQrcodeStatus');
    Route::get('auth/wechat/oauth', 'AdminAuthController@wechatOauth');
    Route::get('auth/wechat/oauth/callback', 'AdminAuthController@wechatOauthCallback')->name('wechat.admin.oauth.callback');
    Route::get('auth/refresh', 'AdminAuthController@refresh');
    #需要验证登录
    Route::middleware("auth:admin")->group(function () {
        Route::get('auth/me', 'AdminAuthController@me');
        Route::get('auth/logout', 'AdminAuthController@logout');
        Route::get('auth/menu', 'AdminAuthController@menu');
        Route::get('auth/role', 'AdminAuthController@role');
        Route::get('auth/test', 'AdminAuthController@test');
    });
});

Route::prefix("api")->group(function () {
    Route::get('auth/wechat/oauth', 'UserAuthController@wechatOauth');
    Route::get('auth/wechat/oauth/callback', 'UserAuthController@wechatOauthCallback')->name('wechat.api.oauth.callback');
    Route::get('auth/refresh', 'UserAuthController@refresh');
    #需要验证登录
    Route::middleware("auth:api")->group(function () {
        Route::get('auth/me', 'UserAuthController@me');
        Route::get('auth/logout', 'UserAuthController@logout');
    });
});
