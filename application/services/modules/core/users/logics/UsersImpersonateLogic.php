<?php

namespace App\services\modules\core\users\logics;

use App\services\modules\authentication\processors\UserSessionProcessor;

class UsersImpersonateLogic
{
	public function __construct()
	{
		// model('User_model', 'userM');
	}

	public function impersonate($request)
	{
		$impersonateID = purify($request['impersonate_id']);
		$userID = currentUserID();

		$remember = isCookieRememberExists(); // check if remember cookie is exist

		return app(new UserSessionProcessor)->execute($userID, NULL, $remember, NULL, $impersonateID);
	}

	public function leaveImpersonation()
	{
		$userID = currentImpersonatorID();
		$remember = isCookieRememberExists(); // check if remember cookie is exist

		return app(new UserSessionProcessor)->execute($userID, NULL, $remember);
	}
}
