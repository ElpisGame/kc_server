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

#微信
Route::get('game/items', 'GameController@items');
Route::post('game/sendMail', 'GameController@sendMail');
Route::post('game/go', 'GameController@go');
Route::post('game/charge', 'GameController@charge');
Route::get('game/shell', 'GameController@shell');
Route::get('game/giftPacks', 'GameController@giftPacks');
Route::post('game/sendGiftPack', 'GameController@sendGiftPack');
Route::post('game/silent', 'GameController@silent');
Route::post('game/unsilent', 'GameController@unsilent');
Route::post('game/sealed', 'GameController@sealed');
Route::post('game/unsealed', 'GameController@unsealed');
Route::post('game/broadCast', 'GameController@broadCast');
Route::post('game/delivergoods', 'GameController@delivergoods');
Route::get('game/checkRoleInfo', 'GameController@checkRoleInfo');
Route::get('game/statisticalGet', 'GameController@statisticalGet');
Route::get('game/OnlineCount', 'GameController@OnlineCount');
Route::get('game/dayOrdersGet', 'GameController@dayOrdersGet');
Route::post('game/dayOrders', 'GameController@dayOrders');
Route::post('game/SendMs', 'GameController@SendMs');
Route::get('game/setActivityTaskTime', 'GameController@setActivityTaskTime');
Route::post('game/getCodeMS', 'GameController@getCodeMS');
Route::post('game/verifyMS', 'GameController@verifyMS');
Route::post('game/getTotalActivePlayerNum', 'GameController@getTotalActivePlayerNum');
Route::post('game/queryActivePlayerNumByServer', 'GameController@queryActivePlayerNumByServer');
//大厅
Route::get('game/announce', 'GameHallController@announce');
Route::post('game/verifyRespondSign', 'GameHallController@verifyRespondSign');
Route::get('game/verifyLogin', 'GameHallController@verifyLogin');
Route::get('game/startPay', 'GameHallController@startPay');
Route::get('game/verifyLoginAndroid', 'GameHallController@verifyLoginAndroid');
Route::get('game/verifyWhiteListByIp', 'GameHallController@verifyWhiteListByIp');
Route::get('game/getPlayerServerInfo', 'GameHallController@getPlayerServerInfo');
Route::get('game/getUserauthenInfo', 'GameHallController@getUserauthenInfo');
Route::get('game/getServerInfo', 'GameHallController@getServerInfo');

//QK--胜良
Route::get('game/checkUserInfoForSL', 'GameHallController@checkUserInfoForSL'); //QUICK SDK
Route::post('game/verifyRespondSignForSL', 'GameHallController@verifyRespondSignForSL'); //QUICK SDK

//QK--绘梦
Route::get('game/checkUserInfoForHM', 'GameHallController@checkUserInfoForHM'); //QUICK SDK
Route::post('game/verifyRespondSignForHM', 'GameHallController@verifyRespondSignForHM'); //QUICK SDK


//QK--水娱
Route::get('game/checkUserInfoForSY', 'GameHallController@checkUserInfoForSY'); //QUICK SDK
Route::post('game/verifyRespondSignForSY', 'GameHallController@verifyRespondSignForSY'); //QUICK SDK


//多游
Route::post('game/verifyRespondSignForDY', 'GameHallController@verifyRespondSignForDY');


//佳优
Route::get('game/checkUserInfoForJY', 'GameHallController@checkUserInfoForJY');
Route::get('game/verifyRespondSignForJY', 'GameHallController@verifyRespondSignForJY');


Route::get('game/verifyMD5', 'GameHallController@verifyMD5');


//WX
Route::get('game/checkUserInfoForWX', 'GameHallController@checkUserInfoForWX');
Route::get('game/verifyRespondSignForWX', 'GameHallController@verifyRespondSignForWX');


//QQ
Route::get('game/checkUserInfoForQQ', 'GameHallController@checkUserInfoForQQ');
Route::get('game/gamePrePayForQQ', 'GameHallController@gamePrePayForQQ');
Route::get('game/checkGamePayForQQ', 'GameHallController@checkGamePayForQQ');
Route::post('game/verifyRespondSignForQQ', 'GameHallController@verifyRespondSignForQQ');


Route::post('game/qureyBalanceFromChain', 'GameController@qureyBalanceFromChain');
Route::post('game/qureyBalanceFromDB', 'GameController@qureyBalanceFromDB');
Route::post('game/checkNFTFormChain', 'GameController@checkNFTFormChain');
Route::post('game/checkNFTFormDB', 'GameController@checkNFTFormDB');
Route::post('game/requestNFT', 'GameController@requestNFT');
Route::get('game/recieveNFT', 'GameController@recieveNFT');


Route::post('game/requestPkey', 'GameController@requestPkey');
Route::post('game/uploadNFTRankScore', 'GameController@uploadNFTRankScore');
Route::post('game/queryWorldRank', 'GameController@queryWorldRank');
Route::get('game/uploadNFTRankScore', 'GameController@uploadNFTRankScore');

Route::get('game/sync', 'GameController@syncNFTRankPlayerInfo');

Route::get('game/cv', 'GameController@checkVersion');

Route::post('game/requestPkeyTest', 'GameHallController@requestPkey');
Route::get('game/syncTest', 'GameHallController@syncNFTRankPlayerInfo');
Route::get('game/uploadNFTRankScoreTest', 'GameHallController@uploadNFTRankScore');




Route::middleware('auth:api')->group(function () {
    Route::get('price/startCities', 'GameController@startCities');
});

