<?php

Route::get('/dashboard', 'DashboardController@index', ['middleware' => 'Sanctum']);
Route::get('/profile', 'UserProfileController@index', ['middleware' => 'Sanctum']);

Route::group('/smm', ['middleware' => ['Sanctum', 'Superadmin']], function () {
	Route::get('/', 'PackagePlanController@index');
	Route::get('/plan-list', 'PackagePlanController@listPackage');
	Route::get('/upgrade-downgrade', 'PackagePlanController@listPackage');
	Route::get('/cancel-refund', 'PackagePlanController@listPackage');
	Route::get('/renewal', 'PackagePlanController@listPackage');
	Route::get('/history-logs', 'PackagePlanController@listPackageHistory');
});
