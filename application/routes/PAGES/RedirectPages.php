<?php

Route::get('/dashboard', 'DashboardController@index', ['middleware' => 'Sanctum']);
Route::get('/profile', 'UserProfileController@index', ['middleware' => 'Sanctum']);

Route::group('/chat', ['middleware' => ['Sanctum']], function () {
	Route::get('/', function () {
		render('chat/room',  [
			'title' => 'Chat',
			'currentSidebar' => 'Chat',
			'currentSubSidebar' => NULL
		]);
	});

	Route::get('/room/{id?}', function ($id = NULL) {
		render('chat/room',  [
			'title' => 'Chat',
			'currentSidebar' => 'Chat',
			'currentSubSidebar' => NULL
		]);
	});
});
