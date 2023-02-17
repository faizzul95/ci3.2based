<?php

Route::group('/auth', function () {
	Route::get('/', 'Auth@index');
	Route::get('/logout', 'Auth@logout');
	Route::get('/forgot-password', 'Auth@forgot');
	Route::post('/sign-in', 'AuthController@authorize', ['middleware' => ['Api']]);
	Route::post('/socialite', 'AuthController@socialite', ['middleware' => ['Api']]);
	Route::post('/sent-email', 'AuthController@reset', ['middleware' => ['Api']]);
	Route::post('/verify-user', 'AuthController@Verify2FA', ['middleware' => ['Api']]);
});
