<?php

Route::get('/dashboard', 'DashboardController@index', ['middleware' => 'Sanctum']);
Route::get('/profile', 'UserProfileController@index', ['middleware' => 'Sanctum']);

// Route::group('/smm', ['middleware' => ['Sanctum', 'Superadmin']], function () {
// 	Route::get('/', 'PackagePlanController@index');
// });

Route::get('/chat/{id?}', function ($id = NULL) {
	render('dashboard/chat',  [
		'user_id' => $id
	]);
});
