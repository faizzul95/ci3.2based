<?php

Route::group('/error', function () {

	Route::get('/404', function () {
		view('errors/custom/error_general', [
			'title' => '404',
			'message' => 'Page Not Found',
			'image' => asset('custom/images/maintenance.png')
		], false);
	});

	Route::get('/403', function () {
		view('errors/custom/error_general', [
			'title' => '403',
			'message' => 'Unauthorize Access',
			'image' => asset('custom/images/maintenance.png')
		], false);
	});

	Route::get('/maintenance', 'Errorpage@maintenance');
});
