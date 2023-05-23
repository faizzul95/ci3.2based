<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

if (!function_exists('isLogin')) {
	function isLogin($param = 'isLoggedInSession', $redirect = 'auth/logout')
	{
		if (!hasSession($param)) {
			redirect($redirect);
		}
	}
}

if (!function_exists('isLoginCheck')) {
	function isLoginCheck($param = 'isLoggedInSession')
	{
		return hasSession($param);
	}
}

// CUSTOM FUNCTION (CHANGE ACCORDING YOUR SYSTEM SESSION)

if (!function_exists('currentUserID')) {
	function currentUserID()
	{
		return decodeID(getSession('userID'));
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
		return getSession('userNickName');
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
		return getSession('profileName');
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

if (!function_exists('currentUserStaffID')) {
	function currentUserStaffID()
	{
		return getSession('userStaffNo');
	}
}

if (!function_exists('getImageSystemLogo')) {
	function getImageSystemLogo()
	{
		$imageLogoPath = 'public/dist/logo.png';
		return fileExist($imageLogoPath) ? asset($imageLogoPath) : defaultImage('company_logo');
	}
}
