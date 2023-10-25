
<?php

use App\services\generals\constants\MasterModule;

// COOKIE REMEMBER ME

if (!function_exists('isCookieRememberExists')) {
	function isCookieRememberExists()
	{
		$cookieName = env('REMEMBER_COOKIE_NAME');
		$token = get_cookie($cookieName); 	// get cookie remember
		return hasData($token) ? true : false; // check if remember cookie is exist
	}
}

// LEAVE ACTION

// if (!function_exists('leaveAction')) {
// 	function leaveAction()
// 	{
// 		// check if current user is impersonating others user
// 		if (hasData(currentImpersonatorID())) {
// 			echo '<a class="dropdown-item" href="' . url('auth/leave-user') . '"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout"> Leave Impersonation </span></a>';
// 		} else {
// 			echo '<a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout"> Logout </span></a>';
// 		}
// 	}
// }

// PERMISSION CONTROLL