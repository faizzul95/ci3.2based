<?php

namespace App\services\modules\authentication\logics;

use App\services\generals\constants\LoginType;
use App\services\generals\constants\GeneralErrorMessage;

use App\services\modules\core\users\processors\UserSearchProcessors;
use App\services\modules\authentication\processors\UserSessionProcessor;

class TwoFactorAuthenticateLogic
{
	public function __construct()
	{
	}

	public function logic($request, $loginType = LoginType::CREDENTIAL)
	{
		$username  = purify($request['username']);
		$codeEnter  = purify($request['code']);
		$rememberme  = purify($request['rememberme']);

		$dataUser = app(new UserSearchProcessors)->execute([
			'fields' => 'id,name,email,two_factor_secret',
			'whereQuery' => $username,
		], 'get');

		if (!empty($dataUser)) {
			$userID = hasData($dataUser) ? $dataUser['id'] : NULL;
			$codeSecret = $dataUser['two_factor_secret'];

			if (verifyGA($codeSecret, $codeEnter)) {
				$responseData = app(new UserSessionProcessor)->execute($userID, $loginType, $rememberme);
			} else {
				$responseData = GeneralErrorMessage::LIST['AUTH']['VERIFY2FA'];
			}
		} else {
			$responseData = GeneralErrorMessage::LIST['AUTH']['DEFAULT'];
		}

		return $responseData;
	}
}
