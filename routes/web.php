<?php

Route::get('/', 'MicropostsController@index');
    
//------------------------------------------------------------------------------ユーザ登録
Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('signup.get');
Route::post('signup', 'Auth\RegisterController@register')->name('signup.post');

//------------------------------------------------------------------------------ログイン認証
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login.post');
Route::get('logout', 'Auth\LoginController@logout')->name('logout.get');

//------------------------------------------------------------------------------ルート制限
Route::group(['middleware' => 'auth'], function () {
    //middlewareによってログインしているユーザー(auth)にのみ、以下のルートを作る。
    //Route::groupで<div>のようにくくることができる。
        Route::resource('users', 'UsersController', ['only' => ['index', 'show']]);
        //URIーコントローラー名ー指定：アクション名
        
//-------------------------------------------authグループ内のusers/{id}グループ 
        Route::group(['prefix' => 'users/{id}'], function () {
            //pefixによってURIはfollow/users/{id}のようになる。
            
            Route::post('follow', 'UserFollowController@store')->name('user.follow');
            Route::delete('unfollow', 'UserFollowController@destroy')->name('user.unfollow');
            Route::get('followings', 'UsersController@followings')->name('users.followings');
            Route::get('followers', 'UsersController@followers')->name('users.followers');
        });
        
        Route::resource('microposts', 'MicropostsController', ['only' => ['store', 'destroy']]);
});

//------------------------------------------------------------------------------
