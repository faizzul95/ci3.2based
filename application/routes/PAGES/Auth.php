<?php

Route::group('/auth', function () {

	Route::get('/logout', 'AuthenticateController@logout');
	Route::post('/verify-user', 'AuthenticateController@verify2FA', ['middleware' => ['Api']]);
	Route::post('/sign-in', 'AuthenticateController@authorize', ['middleware' => ['Api']]);
	Route::post('/socialite', 'AuthenticateController@socialite', ['middleware' => ['Api']]);

	Route::post('/sent-email', 'AuthenticateController@resetPasswordLink', ['middleware' => ['Api']]);
	Route::post('/change-password', 'AuthenticateController@changePassword', ['middleware' => ['Api']]);
	Route::get('/reset-password/{token?}', 'AuthenticateController@resetPasswordPage');

	// Open forgot page
	Route::get('/forgot-password', function () {
		render('auth/forgot',  [
			'title' => 'Forgot Password',
			'currentSidebar' => 'auth',
			'currentSubSidebar' => 'forgot'
		]);
	});

	// Open verify 2FA page
	Route::get('/verify/{id?}/{timestamp?}/{remember?}', function ($userID = NULL, $timestamp = NULL, $remember = NULL) {
		dd($timestamp);
		// render('auth/login',  [
		// 	'title' => 'Sign In',
		// 	'currentSidebar' => 'auth',
		// 	'currentSubSidebar' => 'login'
		// ]);
	});
});
