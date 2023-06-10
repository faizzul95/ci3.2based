
<?php

use App\services\generals\constants\MasterModule;

// COOKIE REMEMBER ME

if (!function_exists('isCookieRememberExists')) {
	function isCookieRememberExists($cookieName = 'remember_me_token_cipmo')
	{
		$token = get_cookie($cookieName); 	// get cookie remember
		return hasData($token) ? true : false; // check if remember cookie is exist
	}
}

// PERMISSION CONTROL

if (!function_exists('permission')) {
	function permission($slug = NULL)
	{
		$roleid = currentUserRoleID();
		$hasPermission = NULL;

		return $hasPermission;
	}
}

if (!function_exists('abilities')) {
	function abilities($slug = NULL)
	{
		$roleid = currentUserRoleID();
		// $moduleSubscribe = hasData(currentPackageModule()) ? explode(',', currentPackageModule()) : NULL;

		$hasPermission = [];
		return $hasPermission;
	}
}
