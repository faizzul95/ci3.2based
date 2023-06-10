<?php

namespace App\services\modules\authentication\logics;

use App\services\generals\constants\LoginType;
use App\services\generals\constants\GeneralErrorMessage;

use App\services\modules\core\users\processors\UserSearchProcessors;
use App\services\modules\authentication\processors\UserSessionProcessor;

class SocialliteLogic
{
	public function __construct()
	{
	}

	public function logic($request, $loginType = LoginType::SOCIALITE)
	{
		// default response
		$responseData = GeneralErrorMessage::LIST['AUTH']['DEFAULT'];

		$dataUser = app(new UserSearchProcessors)->execute([
			'fields' => 'id,name,email',
			'conditions' => [
				'email' => purify($request['email']),
			],
		], 'get');

		if (hasData($dataUser)) {
			$userID = hasData($dataUser) ? $dataUser['id'] : NULL;
			$rememberme = purify($request['rememberme']);

			$responseData = app(new UserSessionProcessor)->execute($userID, $loginType, $rememberme);
		}

		return $responseData;
	}
}
