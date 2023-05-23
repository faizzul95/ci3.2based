<?php

Route::group('/migrate', function () {
	Route::get('/', 'MigrateController@list');
	Route::get('/all', 'MigrateController@all');
	Route::post('/specific-migration', 'MigrateController@specificMigration');
});
