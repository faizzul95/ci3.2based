<?php

/**
 * Welcome to Luthier-CI!
 *
 * This is your main route file. Put all your HTTP-Based routes here using the static
 * Route class methods
 *
 * Examples:
 *
 *    Route::get('foo', 'bar@baz');
 *      -> $route['foo']['GET'] = 'bar/baz';
 *
 *    Route::post('bar', 'baz@fobie', [ 'namespace' => 'cats' ]);
 *      -> $route['bar']['POST'] = 'cats/baz/foobie';
 *
 *    Route::get('blog/{slug}', 'blog@post');
 *      -> $route['blog/(:any)'] = 'blog/post/$1'
 */

// Route::get('/', function () {
//  luthier_info();
// })->name('homepage');

// GENERAL
Route::set('default_controller', 'AuthenticateController');
// Route::get('/', 'AuthenticateController@index');

// CRON JOB (SERVICES)
Route::group('/cron', function () {
	Route::get('/database/{upload?}', 'BackupController@BackupDrive');
});

// Open login page
Route::get('/', function () {
	// check user session if exist
	if (isLoginCheck()) {
		redirect('dashboard', true);
	} else {

		$remember = app('App\services\modules\authentication\logics\RememberLogic')->logic();

		if (hasData($remember, 'code') && isSuccess($remember['code'])) {
			redirect($remember['redirectUrl'], true);
		}

		// if not redirect to page login
		render('auth/login',  [
			'title' => 'Sign In',
			'currentSidebar' => 'auth',
			'currentSubSidebar' => 'login'
		]);
	}
});

// CRON JOB (SERVICES)
Route::group('/cron', function () {
	Route::get('/backup/{upload?}', 'BackupController@BackupDrive');
});

require __DIR__ . '/PAGES/Auth.php';
require __DIR__ . '/PAGES/Error.php';
require __DIR__ . '/PAGES/Migration.php';

// SYSTEM INFORMATION
Route::get('/sysinfo', function () {
	if (isLoginCheck() && currentUserRoleID() == 1)
		phpinfo();
	else
		error('404', ['title' => '404', 'message' => '', 'image' => asset('custom/images/nodata/404.png')]);
});

Route::set('404_override', function () {
	// show_404();
	view('errors/custom/error_404');
});

// require __DIR__ . '/PAGES/RedirectPages.php';
// require __DIR__ . '/PAGES/SuperadminPages.php';

Route::set('translate_uri_dashes', FALSE);
