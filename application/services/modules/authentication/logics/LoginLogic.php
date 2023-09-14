<?php

namespace App\services\modules\authentication\logics;

use App\services\generals\constants\LoginType;
use App\services\generals\constants\GeneralErrorMessage;

use App\services\modules\generals\users\processors\UsersSearchProcessors;
// use App\services\modules\authentication\processors\UserSessionProcessor;

class LoginLogic
{
	public function __construct()
	{
		model('UsersLoginAttempt_model', 'attemptM');

		library('recaptcha');
		library('user_agent');
	}

	public function logic($request, $loginType = LoginType::CREDENTIAL)
	{
		// default response
		$responseData = GeneralErrorMessage::LIST['AUTH']['DEFAULT'];


		$dataUser = app(new UsersSearchProcessors)->execute([
			'fields' => 'id,email,username,password,user_status,two_factor_status',
			'whereQuery' => purify($request['username'])
		], 'get');

		$validateRecaptcha = recaptchav2();

		if ($validateRecaptcha['success']) {
			if (hasData($dataUser)) {

				$dbPassword = hasData($dataUser, 'password', true);
				$enteredPassword = purify($request['password']);
				$rememberme = purify($request['rememberme']);

				// get attempt login
				$attempt = ci()->attemptM->login_attempt_exceeded($userID);
				$countAttempt = $attempt['count'];

				// if ($attempt['isExceed']) {
				// 	if (password_verify($enteredPassword, $dbPassword)) {

				// 		$two_factor_status = $dataUser['two_factor_status'];
				// 		ci()->attemptM->clear_login_attempts($userID);

				// 		// if 2FA is disabled
				// 		if ($two_factor_status != 1) {
				// 			// if success, start login session
				// 			$responseData = app(new UserSessionProcessor)->execute($userID, $loginType, $rememberme);
				// 		}
				// 		// if 2FA is enabled
				// 		else {
				// 			$responseData =  [
				// 				'code' => 200,
				// 				'message' => "",
				// 				'redirectUrl' => url('auth/verify/') . $userID . '/' . timestamp('YmdHis') . '/' . $rememberme,
				// 			];
				// 		}
				// 	} else {
				// 		// if failed to login
				// 		ci()->attemptM::save([
				// 			'user_id' => $userID,
				// 			'ip_address' => ci()->input->ip_address(),
				// 			'time' => timestamp(),
				// 			'user_agent' => ci()->input->user_agent()
				// 		]);

				// 		$countAttemptRemain = 5 - (int) $countAttempt;

				// 		$responseData = GeneralErrorMessage::LIST['AUTH']['DEFAULT'];
				// 		$responseData["message"] = ($countAttempt >= 2) ? 'Invalid username or password. Attempt remaining : ' . $countAttemptRemain : 'Invalid username or password';
				// 	}
				// } else {
				// 	$responseData = GeneralErrorMessage::LIST['AUTH']['ATTEMPT'];
				// }
			}
		} else {
			$responseData = GeneralErrorMessage::LIST['AUTH']['RECAPTCHA'];
			$responseData["message"] = filter_var(env('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ? $validateRecaptcha['error_message'] : "Please verify that you're a human";
		}

		return $responseData;
	}
}
