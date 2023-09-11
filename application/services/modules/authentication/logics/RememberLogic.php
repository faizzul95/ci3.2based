<?php

namespace App\services\modules\authentication\logics;

use App\services\generals\constants\LoginType;
use App\services\modules\core\users\processors\UserSearchProcessors;
use App\services\modules\authentication\processors\UserSessionProcessor;

class RememberLogic
{
	public function __construct()
	{
	}

	public function logic()
	{
		$token = get_cookie('remember_me_token_ciarcav5');

		// check if token cookie is exist in browsers
		if (hasData($token)) {

			$dataUser = app(new UserSearchProcessors)->execute([
				'fields' => 'id,name,email,remember_token',
				'conditions' => [
					'remember_token' => xssClean($token),
				],
			], 'get');

			if ($dataUser) {
				return app(new UserSessionProcessor)->execute($dataUser['id'], LoginType::TOKEN, true);
			}
		}
	}
}
