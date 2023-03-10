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

// CUSTOM FUNCTION (CHANGE ACCORDING YOUR SESSION)

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
		return getSession('userAvatar');
	}
}

if (!function_exists('currentUserEmail')) {
	function currentUserEmail()
	{
		return getSession('userEmail');
	}
}
