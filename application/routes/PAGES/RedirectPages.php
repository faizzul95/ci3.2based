<?php

Route::get('/dashboard', 'DashboardController@index', ['middleware' => 'Sanctum']);
Route::get('/profile', 'UserProfileController@index', ['middleware' => 'Sanctum']);

// SUPERADMIN ACCESS
Route::get('/directory', 'UserController@index', ['middleware' => ['Sanctum', 'Superadmin']]);
Route::get('/management', 'ManagementController@index', ['middleware' => ['Sanctum', 'Superadmin']]);
Route::get('/rbac', 'SystemController@index', ['middleware' => ['Sanctum', 'Superadmin']]);
Route::get('/email', 'EmailTemplateController@index', ['middleware' => ['Sanctum', 'Superadmin']]);
Route::get('/logger', 'SystemController@LogPage', ['middleware' => ['Sanctum', 'Superadmin']]);
Route::get('/backup', 'SystemController@DatabaseBackupList', ['middleware' => ['Sanctum', 'Superadmin']]);

Route::group('/smm', ['middleware' => ['Sanctum', 'Superadmin']], function () {
	Route::get('/', 'PackagePlanController@index');
	Route::get('/plan-list', 'PackagePlanController@listPackage');
	Route::get('/upgrade-downgrade', 'PackagePlanController@listPackage');
	Route::get('/cancel-refund', 'PackagePlanController@listPackage');
	Route::get('/renewal', 'PackagePlanController@listPackage');
	Route::get('/history-logs', 'PackagePlanController@listPackageHistory');
});
