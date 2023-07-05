<?php

namespace App\services\modules\core\users\logics;

use App\services\modules\authentication\processors\UserSessionProcessor;

class UserProfileSwitchLogic
{
	public function __construct()
	{
		// model('User_model', 'userM');
	}

	public function logic($request)
	{
		$profileID = purify($request['profile_id']);
		$userID = purify($request['user_id']);

		$remember = isCookieRememberExists(); // check if remember cookie is exist

		return app(new UserSessionProcessor)->execute($userID, NULL, $remember, $profileID);
	}
}
