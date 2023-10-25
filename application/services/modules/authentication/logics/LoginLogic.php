<?php

namespace App\services\modules\authentication\logics;

use App\services\generals\constants\LoginType;
use App\services\generals\constants\GeneralStatus;
use App\services\generals\constants\GeneralErrorMessage;

use App\services\modules\user\users\processors\UsersSearchProcessors;
use App\services\modules\user\usersLoginAttempt\processors\UsersLoginAttemptSearchProcessors;
use App\services\modules\user\usersLoginAttempt\processors\UsersLoginAttemptDeleteProcessors;
use App\services\modules\user\usersLoginAttempt\processors\UsersLoginAttemptStoreProcessors;
use App\services\modules\authentication\processors\UserSessionProcessor;

class LoginLogic
{
	public function __construct()
	{
		library('recaptcha');
		library('user_agent');
	}

	public function logic($request, $loginType = LoginType::CREDENTIAL)
	{
		// default response
		$responseData = GeneralErrorMessage::LIST['AUTH']['DEFAULT'];

		$dataUser = app(new UsersSearchProcessors)->execute([
			'fields' => 'id,email,username,password,user_status,two_factor_status,login_enable',
			'whereQuery' => purify($request['username']),
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
					$validateRecaptcha = recaptchav2();

					if ($validateRecaptcha['success']) {
						if (hasData($dataUser)) {

							$userID = $dataUser['id'];
							$dbPassword = hasData($dataUser, 'password', true);
							$enteredPassword = purify($request['password']);
							$rememberme = purify($request['rememberme']);

							// get attempt login
							$countAttempt = app(new UsersLoginAttemptSearchProcessors)->execute(
								['conditions' => "user_id = " . $userID . " AND ip_address = '" . ci()->input->ip_address() . "' AND time > NOW() - INTERVAL 10 MINUTE"],
								'count_rows'
							);

							$attemptEnable = 5;
							$isAttemptExceed = $countAttempt >= $attemptEnable ? true : false;

							if (!$isAttemptExceed) {
								if (password_verify($enteredPassword, $dbPassword)) {

									$two_factor_status = $dataUser['two_factor_status'];
									$clearAttempt = app(new UsersLoginAttemptDeleteProcessors)->execute(['user_id' => $userID]);

									// if 2FA is disabled
									if ($two_factor_status != 1) {
										// if success, start login session
										$responseData = app(new UserSessionProcessor)->execute($userID, $loginType, $rememberme);
									}
									// if 2FA is enabled
									else {
										$responseData =  [
											'code' => 200,
											'message' => "",
											'redirectUrl' => url('auth/verify/') . $userID . '/' . timestamp('YmdHis') . '/' . $rememberme,
										];
									}
								} else {
									// if failed to login
									$increaseAttempt = app(new UsersLoginAttemptStoreProcessors)->execute([
										'user_id' => $userID,
										'ip_address' => ci()->input->ip_address(),
										'time' => timestamp(),
										'user_agent' => ci()->input->user_agent()
									]);

									$countAttemptRemain = $attemptEnable - (int) $countAttempt;

									$responseData = GeneralErrorMessage::LIST['AUTH']['DEFAULT'];
									$responseData["message"] = ($countAttempt >= ($attemptEnable - 3)) ? 'Invalid username or password. Attempt remaining : ' . $countAttemptRemain : 'Invalid username or password';
								}
							} else {
								$responseData = GeneralErrorMessage::LIST['AUTH']['ATTEMPT'];
							}
						}
					} else {
						$responseData = GeneralErrorMessage::LIST['AUTH']['RECAPTCHA'];
						$responseData["message"] = filter_var(env('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ? $validateRecaptcha['error_message'] : "Please verify that you're a human";
					}
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
