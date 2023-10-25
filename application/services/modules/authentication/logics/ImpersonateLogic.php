<?php

namespace App\services\modules\generals\users\logics;

use App\services\generals\constants\GeneralErrorMessage;

use App\services\modules\user\users\processors\UsersSearchProcessors;
use App\services\modules\authentication\processors\UserSessionProcessor;

class ImpersonateLogic
{
	public function __construct()
	{
	}

	public function impersonate($request)
	{
		$impersonateID = purify($request['impersonate_id']);

		$dataUser = app(new UsersSearchProcessors)->execute([
			'fields' => 'id,name,email',
			'conditions' => [
				'id' => purify($impersonateID),
			],
			'with' => [
				'main_profile' => [
					'fields' => 'id,user_id,role_id,profile_status,is_main,company_id',
					'conditions' => ['is_main' => 1, 'profile_status' => 1],
				],
			]
		], 'get');

		// Check if current user has active profile
		if (hasData($dataUser, 'main_profile')) {
			$userID = currentUserID();
			$remember = isCookieRememberExists(); // check if remember cookie is exist
			return app(new UserSessionProcessor)->execute($userID, NULL, $remember, NULL, $impersonateID);
		} else {
			return GeneralErrorMessage::LIST['AUTH']['PROFILE'];
		}
	}

	public function leaveImpersonation()
	{
		$userID = currentImpersonatorID();
		$remember = isCookieRememberExists(); // check if remember cookie is exist

		return app(new UserSessionProcessor)->execute($userID, NULL, $remember);
	}
}
