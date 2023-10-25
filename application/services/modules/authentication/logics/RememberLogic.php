<?php

namespace App\services\modules\authentication\logics;

use App\services\generals\constants\LoginType;
use App\services\modules\user\users\processors\UsersSearchProcessors;
use App\services\modules\authentication\processors\UserSessionProcessor;

class RememberLogic
{
	public function __construct()
	{
	}

	public function logic()
	{
		$token = get_cookie(env('REMEMBER_COOKIE_NAME', 'remember_me_token_cihrm'));

		// check if token cookie is exist in browsers
		if (hasData($token)) {

			$dataUser = app(new UsersSearchProcessors)->execute([
				'fields' => 'id,name,email,remember_token',
				'conditions' => [
					'remember_token' => xssClean($token),
				],
			], 'get');

			if (hasData($dataUser)) {
				return app(new UserSessionProcessor)->execute($dataUser['id'], LoginType::REMEMBER_ME, true);
			}
		}
	}
}
