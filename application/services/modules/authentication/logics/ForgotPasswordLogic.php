<?php

namespace App\services\modules\authentication\logics;

use App\services\generals\traits\QueueTrait;
use App\services\generals\constants\GeneralStatus;
use App\services\generals\constants\GeneralErrorMessage;
use  App\services\generals\constants\DefaultEmailTemplate;

use App\services\modules\user\users\processors\UsersSearchProcessors;
use App\services\modules\user\usersPasswordReset\logics\UsersPasswordResetCreateLogic;
use App\services\modules\user\usersPasswordReset\processors\UsersPasswordResetSearchProcessors;
use App\services\modules\master\masterEmailTemplates\processors\MasterEmailTemplatesSearchProcessors;

class ForgotPasswordLogic
{
	use QueueTrait;

	public function __construct()
	{
		library('recaptcha');
		library('user_agent');
	}

	public function sent($request)
	{
		$email  = $request['email'];

		$validateRecaptcha = recaptchav2();

		// Check with recaptcha first
		if ($validateRecaptcha['success']) {

			// query data user by email
			$dataUser = app(new UsersSearchProcessors)->execute([
				'fields' => 'id,email,username,password,user_status,two_factor_status,login_enable',
				'conditions' => [
					'email' => purify($email),
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

			// check if data user is exist
			if (hasData($dataUser)) {
				// Check if this user is able to login into system
				if (hasData($dataUser, 'login_enable', true, 0) == GeneralStatus::ACTIVE) {

					// Check if current user has active profile
					if (hasData($dataUser, 'main_profile')) {
						// check if this user company has active subscription/or active
						if (hasData($dataUser, 'main_profile.company.company_status', true, 0) == GeneralStatus::ACTIVE) {

							$companyID = $dataUser['main_profile']['company_id'];

							$token = $dataUser['id'] . bin2hex(random_bytes(20));
							$resetPassData = app(new UsersPasswordResetCreateLogic)->logic([
								'user_id' => $dataUser['id'],
								'email' => $dataUser['email'],
								'reset_token' => $token,
								'reset_token_expired' => date('Y-m-d H:i:s', strtotime(timestamp() . ' + 30 minutes'))
							]);

							if (isSuccess($resetPassData['code'])) {
								$url = 'auth/reset-password/' . $token;

								$getTemplate = app(new MasterEmailTemplatesSearchProcessors)->execute([
									'fields' => 'id,email_type,email_subject,email_body,email_footer,email_cc,email_bcc,email_status,company_id',
									'conditions' => [
										'email_status' => GeneralStatus::ACTIVE,
										'email_type' => 'FORGOT_PASSWORD',
										'company_id' => $companyID,
									]
								], 'get');

								$template = $getTemplate ? $getTemplate : DefaultEmailTemplate::TEMPLATE['LOGIN']['FORGOT_PASSWORD'];

								if (hasData($template)) {
									$bodyMessage = replaceTextWithData($template['email_body'], [
										'to' => $dataUser['name'],
										'url' => url($url)
									]);

									// Testing Using trait (use phpmailer)
									// $this->testSentEmail($dataUser, $bodyMessage, $template);

									// add to queue
									$saveQueue = $this->addQueue([
										'queue_uuid' => uuid(),
										'type' => 'email',
										'payload' => json_encode([
											'name' => $dataUser['name'],
											'to' => $email,
											'cc' => $template['email_cc'],
											'bcc' => $template['email_bcc'],
											'subject' => $template['email_subject'],
											'body' => $bodyMessage,
											'attachment' => NULL,
										])
									]);

									if (isSuccess($saveQueue['code'])) {
										$responseData = [
											'code' => 200,
											'message' => 'Email has been sent',
											'redirectUrl' => url(''),
										];
									} else {
										$responseData = GeneralErrorMessage::LIST['AUTH']['FORGOT'];
									}
								} else {
									$responseData = GeneralErrorMessage::LIST['AUTH']['FORGOT'];
								}
							} else {
								$responseData = GeneralErrorMessage::LIST['AUTH']['FORGOT'];
							}
						} else {
							$responseData = GeneralErrorMessage::LIST['AUTH']['UNSUBSCRIBE'];
						}
					} else {
						$responseData = GeneralErrorMessage::LIST['AUTH']['PROFILE'];
					}
				} else {
					$responseData = GeneralErrorMessage::LIST['AUTH']['DELETED'];
				}
			} else {
				$responseData = GeneralErrorMessage::LIST['AUTH']['EMAIL_NOT_VALID'];
			}
		} else {
			$responseData = GeneralErrorMessage::LIST['AUTH']['RECAPTCHA'];
			$responseData["message"] = filter_var(env('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ? $validateRecaptcha['error_message'] : "Please verify that you're a human";
		}

		return $responseData;
	}

	public function form($request)
	{
		// query data reset by token
		$dataReset = app(new UsersPasswordResetSearchProcessors)->execute([
			'fields' => 'id,user_id,email,reset_token,reset_token_expired',
			'conditions' => ['reset_token' => purify($request)],
		], 'get');

		// set default response
		$responseData = GeneralErrorMessage::LIST['AUTH']['TOKEN_RESET'];

		// check if data reset is exist
		if (hasData($dataReset)) {
			// check if token is expired
			if ($dataReset['reset_token_expired'] > timestamp())
				$responseData = ['code' => 200, 'message' => "", 'data' => $dataReset];
		}

		return $responseData;
	}
}
