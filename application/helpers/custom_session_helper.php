<?php

use App\libraries\AuthToken;

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

if (!function_exists('isLogin')) {
	function isLogin($param = 'isLoggedInSession', $redirect = 'auth/logout')
	{
		$getToken = getTokenAuth();

		if (empty($getToken)) {
			if (!hasSession($param))
				redirect($redirect);
		} else {
			$checkToken = tokenAuthentication($getToken);

			if (!isSuccess($checkToken['code']))
				jsonResponse($checkToken);
		}
	}
}

if (!function_exists('isLoginCheck')) {
	function isLoginCheck($param = 'isLoggedInSession')
	{
		return hasSession($param);
	}
}

if (!function_exists('isSuperadmin')) {
	function isSuperadmin()
	{
		return in_array(currentUserRoleID(), [1]);
	}
}

// CUSTOM FUNCTION (CHANGE ACCORDING YOUR SYSTEM SESSION)

if (!function_exists('currentUserID')) {
	function currentUserID()
	{
		return getSession('userID');
	}
}

if (!function_exists('currentUserFullName')) {
	function currentUserFullName()
	{
		return getSession('userFullName');
	}
}

if (!function_exists('currentUserNickName')) {
	function currentUserNickName()
	{
		return getSession('userNickName') ?? currentUserFullName();
	}
}

if (!function_exists('currentUserRoleID')) {
	function currentUserRoleID()
	{
		return decodeID(getSession('roleID'));
	}
}

if (!function_exists('currentUserRoleName')) {
	function currentUserRoleName()
	{
		return getSession('roleName');
	}
}

if (!function_exists('currentUserProfileID')) {
	function currentUserProfileID()
	{
		return decodeID(getSession('profileID'));
	}
}

if (!function_exists('currentUserAvatar')) {
	function currentUserAvatar()
	{
		return fileExist(getSession('userAvatar')) ? getSession('userAvatar') : defaultImage('user');
	}
}

if (!function_exists('currentUserEmail')) {
	function currentUserEmail()
	{
		return getSession('userEmail');
	}
}

if (!function_exists('currentMatricID')) {
	function currentMatricID()
	{
		return getSession('matricID');
	}
}

if (!function_exists('currentImpersonatorID')) {
	function currentImpersonatorID()
	{
		return getSession('impersonatorID');
	}
}

if (!function_exists('getImageSystemLogo')) {
	function getImageSystemLogo()
	{
		$imageLogoPath = 'public/dist/logo.png';
		return fileExist($imageLogoPath) ? $imageLogoPath : defaultImage('company_logo');
	}
}

// ==============================================================================================

if (!function_exists('getTokenAuth')) {
	function getTokenAuth()
	{
		// Get the Authorization header
		$authorizationHeader = ci()->input->get_request_header('Authorization', TRUE);

		// Remove "Bearer " from the header value
		return !empty($authorizationHeader) ? str_replace('Bearer ', '', $authorizationHeader) : NULL;
	}
}

if (!function_exists('tokenAuthentication')) {
	function tokenAuthentication($tokenData = NULL)
	{
		$token = $tokenData ?? getTokenAuth();

		if (!empty($token)) {
			$verify = AuthToken::verification($token);  // check user data token with database
			$code = $verify['status'] ? 200 : 401;
			$message = $verify['status'] ? 'Token verified' : 'Unauthorized token credentials';
			return ['code' => $code, 'message' => $message, 'data' => $verify['data'], 'token' => $verify['token']];
		}

		return ['code' => 400, 'message' => 'Token not found or provide', 'data' => NULL, 'token' => NULL];
	}
}
