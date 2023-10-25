<?php

namespace App\services\modules\authentication\logics;

use App\services\generals\constants\LoginType;
use App\services\generals\constants\GeneralStatus;
use App\services\generals\constants\GeneralErrorMessage;

use App\services\modules\user\users\processors\UsersSearchProcessors;
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

		$dataUser = app(new UsersSearchProcessors)->execute([
			'fields' => 'id,email,user_status,two_factor_status,login_enable',
			'conditions' => [
				'email' => purify($request['email']),
			],
			'with' => [
				'main_profile' => [
					'fields' => 'id,user_id,role_id,profile_status,is_main,company_id',
					'conditions' => ['is_main' => 1, 'profile_status' => 1],
					'with' => [
						'company' => ['fields' => 'id,company_name,company_code,company_status']
					]
				],
			]
		], 'get');

		// Check if this user is able to login into system
		if (hasData($dataUser, 'login_enable', true, 0) == GeneralStatus::ACTIVE) {
			// Check if current user has active profile
			if (hasData($dataUser, 'main_profile')) {
				// check if this user company has active subscription/or active
				if (hasData($dataUser, 'main_profile.company.company_status', true, 0) == GeneralStatus::ACTIVE) {
					$userID = $dataUser['id'];
					$rememberme = purify($request['rememberme']);
					$responseData = app(new UserSessionProcessor)->execute($userID, $loginType, $rememberme);
				} else {
					$responseData = GeneralErrorMessage::LIST['AUTH']['UNSUBSCRIBE'];
				}
			} else {
				$responseData = GeneralErrorMessage::LIST['AUTH']['PROFILE'];
			}
		}

		return $responseData;
	}
}
