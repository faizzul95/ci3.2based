<?php

namespace App\services\modules\generals\users\logics;

use App\services\modules\authentication\processors\UserSessionProcessor;

class ImpersonateLogic
{
	public function __construct()
	{
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