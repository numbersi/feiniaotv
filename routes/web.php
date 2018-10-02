<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

#前台路由

Route::get('/test', 'Index\IndexController@test');//test

Route::get('/', 'Index\IndexController@index');//首页
Route::get('', 'Index\IndexController@index');//首页
Route::get('movielist/{cat}/{page}.html', 'Index\IndexController@dy');//电影列表
Route::get('tvlist/{cat}/{page}.html', 'Index\IndexController@tv');//电视剧列表
Route::get('zylist/{cat}/{page}.html', 'Index\IndexController@zy');//综艺列表
Route::get('dmlist/{cat}/{page}.html', 'Index\IndexController@dm');//动漫列表列表
Route::get('viplist.html', 'Index\IndexController@cX');//尝鲜视频列表
Route::get('autocxlist/{type}/{pg}.html', 'Index\IndexController@autoCx');//自动尝鲜视频列表
Route::get('zhibo.html', 'Index\IndexController@zbQx');//直播

Route::get('play/{play}.html', 'Index\IndexController@play');//播放
Route::group([
    'prefix'=>'play'
],function (){
    Route::get('/tv/{play}.html','Index\IndexController@tvPlay');
});

Route::get('search/{key}.html', 'Index\IndexController@Search');//搜索
Route::get('search/{key}', 'Index\IndexController@Search');//搜索
Route::get('history','Index\IndexController@history');//历史记录
Route::get('jzad','Index\IndexController@jzAd');//加载广告
Route::get('app.html','Index\IndexController@appInfo');//加载广告
Route::get('302{url}.html','Index\IndexController@cdx');//重定向页面
/*
        后台
*/

Route::group([
    'prefix' => empty(config('webset.webdir')) ? 'admin' : config('webset.webdir'),
    'namespace' => 'Admin'
], function($router){
    Route::any('/login','IndexController@login');
    Route::post('/getToken','IndexController@getToken');
    Route::group([
        'middleware'=>"auth:admin"
    ], function ($router1) {
        Route::get('/', 'IndexController@index');
        Route::get('/webset', 'IndexController@webSet');
        Route::get('/jkset', 'IndexController@jkSet');
        Route::get( '/newmovielist', 'IndexController@newMovieList');//尝鲜列表
        Route::get( '/autocx', 'IndexController@autoCx');//自动尝鲜
        Route::get( '/addnewmovie', 'IndexController@addNewMovie');//增加
        Route::get( '/editmovie/{id}', 'IndexController@editMovie');//执行电影编辑操作
        Route::get('/userlist','UCenterController@userList');//会员列表
        Route::get('/userset','UCenterController@userSet');//会员设置
        Route::get('/edituser/{id}','UCenterController@editUser');//编辑会员
        Route::post('action/deluser','UCenterController@delUser');//删除会员
        Route::post('action/userset','actionController@userSet');//会员设置
        Route::post('action/edituser','actionController@editUser');//执行会员编辑

#首页轮播
        Route::get('/addbanner','IndexController@addBanner');//添加轮播
        Route::get('/bannerlist','IndexController@bannerList');//轮播列表
        Route::get('/editbanner/{id}','IndexController@editBanner');//编辑轮播
#导航设置
        Route::get( '/addnav','IndexController@addNav');//添加导航
        Route::get( '/navlist','IndexController@navList');//导航列表
        Route::get( '/editnav/{id}','IndexController@editNav');//编辑导航


        #播放器设置
        Route::get('/playerset','IndexController@playerSet');//播放器设置
        Route::post('/action/playerset', 'actionController@playerSet');//执行播放器设置

/*
 *
 */
        Route::get('/addzb', 'IndexController@addZb');//添加直播页面
        Route::get('/addzb2', 'IndexController@addZb2');//添加直播页面
        Route::get('/zblist', 'IndexController@zbList');//直播列表
        Route::get('/editzb/{id}', 'IndexController@editZb');//直播编辑页面


#缓存相关
        Route::get('/cacheset','IndexController@cacheSet');//缓存设置


        Route::get( '/setad','IndexController@setAd');//广告设置

        #CC防御
        Route::get( '/ccdefense','IndexController@ccDefense');//编辑CC
    });



});

Route::group([
    'prefix'=>'action',
    'middleware' => 'auth:admin_api',
    'namespace' => 'Admin'
], function () {
    Route::post('/webset', 'actionController@webSet');//执行后台设置操作
    Route::post('/jkset', 'actionController@jkSet');//执行Jx设置设置操作
    Route::post('/autocx', 'actionController@autoCx');//执行电影增加操作
    Route::post('/addnewmovie', 'actionController@addNewMovie');//执行电影增加操作
    Route::post('/editmovie', 'actionController@editMovie');//执行电影编辑操作
    Route::post('/delmovie', 'actionController@deleteMovie');//执行电影删除操作

    Route::post('/playerset', 'actionController@playerSet');//执行播放器设置

    #获取尝鲜数据
    Route::post('/getcx','actionController@getCx');//获取尝鲜数据
    Route::post('/getcxlist','actionController@getCxList');//获取尝鲜列表

#首页轮播
    Route::post('/addbanner', 'actionController@addBanner');//执行添加轮播
    Route::post('/editbanner', 'actionController@editBanner');//执行编辑轮播
    Route::post('/delbanner', 'actionController@delBanner');//执行删除轮播


//    导航
    Route::post('/addnav', 'actionController@addNav');//执行添加轮播
    Route::post('/editnav', 'actionController@editNav');//执行编辑轮播
    Route::post('/delnav', 'actionController@delNav');//执行删除轮播
    //直播
    Route::post('/editzb', 'actionController@editZb');//执行直播编辑操作

    Route::post('/setad', 'actionController@setAd');//执行广告设置

    Route::post('/ccdefense', 'actionController@ccDefense');//执行cc编辑

    Route::post('/cacheset', 'actionController@cacheSet');//执行缓存编辑

});
