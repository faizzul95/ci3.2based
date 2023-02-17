<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AuthController extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (isLoginCheck() && !in_array(segment(2), ['logout', 'reset', 'forgot'])) {
			redirect('dashboard', true);
		}

		model('User_model', 'userM');
		model('UserProfile_model', 'profileM');
		model('UserAuthAttempt_model', 'attemptM');
		model('MasterEmailTemplates_model', 'templateM');
		model('SystemQueueJob_model', 'queueM');
		model('UserPasswordReset_model', 'resetM');

		library('recaptcha');
	}

	public function index()
	{
		show_404();
	}

	public function forgot()
	{
		if (isMobileDevice())
			redirect('auth');
		else
			view('auth/forgot',  [
				'title' => 'Forgot Password',
				'currentSidebar' => 'auth',
				'currentSubSidebar' => 'login'
			]);
	}

	public function authorize()
	{
		if (isAjax()) {
			if (!checkMaintenance()) {

				$username  = input('username');
				$enteredPassword = input('password');

				$validateRecaptcha = $this->recaptcha->is_valid();

				// Check with recaptcha first
				if ($validateRecaptcha['success']) {

					$dataUser = $this->userM->getSpecificUser($username);

					if (!empty($dataUser)) {

						$userPassword = $dataUser['user_password'];
						$userID = $dataUser['user_id'];
						$attempt = $this->attemptM->login_attempt_exceeded($userID);
						$countAttempt = $attempt['count'];

						if ($attempt['isExceed']) {
							if (password_verify($enteredPassword, $userPassword)) {

								$two_factor_status = $dataUser['two_factor_status'];
								$this->attemptM->clear_login_attempts($userID);

								// if 2FA is disabled
								if ($two_factor_status != 1) {
									$responseData = $this->sessionLoginStart($dataUser['user_id']);
								}
								// if 2FA is enable
								else {
									$responseData = [
										'resCode' => 400,
										'message' => 'Two-factor authentication (2FA) is enable',
										'verify' => true
									];
								}
							} else {

								$this->attemptM::save([
									'user_id' => $userID,
									'ip_address' => $this->input->ip_address(),
									'time' => timestamp(),
									'user_agent' => $this->input->user_agent()
								]);

								$countAttemptRemain = 5 - (int) $countAttempt;

								$responseData = [
									'resCode' => 400,
									'message' => ($countAttempt >= 2) ? 'Invalid username or password. Attempt remaining : ' . $countAttemptRemain : 'Invalid username or password',
									'verify' => false
								];
							}
						} else {
							$responseData = [
								'resCode' => 400,
								'message' => 'You have reached maximum number of login attempt. Your account has been suspended for 15 minutes.',
								'verify' => false
							];
						}
					} else {
						$responseData = [
							'resCode' => 400,
							'message' => 'Invalid username or password',
							'verify' => false
						];
					}
				} else {
					$responseData = array(
						"resCode" => 400,
						"message" => filter_var(env('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ? $validateRecaptcha['error_message'] : "Please verify that you're a human",
						'verify' => false,
						"redirectUrl" => NULL,
					);
				}
			} else {
				$responseData = array(
					"resCode" => 400,
					"message" => 'System under maintenance',
					'verify' => false,
					"redirectUrl" => NULL,
				);
			}
		} else {
			errorpage('404');
		}

		json($responseData);
	}

	// login using google account
	public function socialite()
	{
		if (isAjax()) {
			if (!checkMaintenance()) {
				$email  = input('email');
				$dataUser = $this->userM->getSpecificUser($email);

				if (!empty($dataUser) > 0) {
					$responseData = $this->sessionLoginStart($dataUser['user_id']);
				} else {
					$responseData = array(
						"resCode" => 400,
						"message" => 'Email not found or not registered!',
						"redirectUrl" => NULL,
					);
				}
			} else {
				$responseData = array(
					"resCode" => 400,
					"message" => 'System under maintenance',
					"redirectUrl" => NULL,
				);
			}
			json($responseData);
		} else {
			errorpage('404');
		}
	}

	public function Verify2FA()
	{
		if (isAjax()) {
			$dataUser = $this->userM->getSpecificUser(input('username_2fa'));

			if (!empty($dataUser)) {
				$codeEnter = input('code_2fa');
				$codeSecret = $dataUser['two_factor_secret'];

				if (verifyGA($codeSecret, $codeEnter)) {
					$responseData = $this->sessionLoginStart($dataUser['user_id']);
				} else {
					$responseData = array(
						"resCode" => 400,
						"message" => 'Wrong code or code already expired',
						"redirectUrl" => NULL,
					);
				}
			} else {
				$responseData = [
					'resCode' => 400,
					'message' => 'Invalid username',
					'verify' => false
				];
			}

			json($responseData);
		} else {
			errorpage('404');
		}
	}

	public function switchProfile()
	{
		$profileID = input('profile_id');
		$userID = input('user_id');

		$dataUser = $this->userM->with_main_profile('fields:profile_id,user_id,role_id,is_main,store_id,profile_status', "where:`profile_id`='{$profileID}'", [
			'with' => [
				[
					'relation' => 'roles',
					'fields' => 'role_name,role_code,role_current_no'
				],
				[
					'relation' => 'avatar',
					'fields' => 'file_compression,files_path,file_path_is_url,entity_file_type',
					'where' => '`entity_file_type`=\'PROFILE_PHOTO\'',
				],
			]
		])->where('user_id', $userID)->get();

		$userFullName = $dataUser['name'];
		$userNickName = $dataUser['user_preferred_name'];
		$userEmail = $dataUser['email'];

		$dataUserProfile = $dataUser['main_profile'];
		$userAvatar = $dataUserProfile['avatar']['files_path'];

		$profileID = $dataUserProfile['profile_id'];

		$roleID = $dataUserProfile['roles']['role_id'];
		$profileName = $dataUserProfile['roles']['role_name'];

		setSession([
			'userID'  			=> encodeID($userID),
			'userFullName'  	=> purify($userFullName),
			'userNickName'  	=> purify($userNickName),
			'userEmail'  		=> purify($userEmail),
			'userAvatar'  		=> purify($userAvatar),
			'profileID'  		=> encodeID($profileID),
			'profileName' 		=> purify($profileName),
			'roleID' 			=> encodeID($roleID),
			'isLoggedInSession' => TRUE
		]);

		json([
			'resCode' => 200,
			'message' => NULL,
		]);
	}

	private function sessionLoginStart($userID)
	{
		$dataUser = $this->userM->with_main_profile('fields:profile_id,user_id,role_id,is_main,store_id,profile_status', 'where:`is_main`=1', [
			'with' => [
				[
					'relation' => 'roles',
					'fields' => 'role_name,role_code,role_current_no'
				],
				[
					'relation' => 'avatar',
					'fields' => 'file_compression,files_path,file_path_is_url,entity_file_type',
					'where' => '`entity_file_type`=\'PROFILE_PHOTO\'',
				],
			]
		])->where('user_id', $userID)->get();

		$userID  = $dataUser['user_id'];
		$userFullName = $dataUser['name'];
		$userNickName = $dataUser['user_preferred_name'];
		$userEmail = $dataUser['email'];
		$userStatus = $dataUser['user_status'];

		if ($userStatus == 1) {
			$dataUserProfile = $dataUser['main_profile'];
			$userAvatar = $dataUserProfile['avatar']['files_path'];

			$profileID = $dataUserProfile['profile_id'];
			$roleID = $dataUserProfile['roles']['role_id'];
			$profileName = $dataUserProfile['roles']['role_name'];

			setSession([
				'userID'  			=> encodeID($userID),
				'userFullName'  	=> purify($userFullName),
				'userNickName'  	=> purify($userNickName),
				'userEmail'  		=> purify($userEmail),
				'userAvatar'  		=> purify($userAvatar),
				'profileID'  		=> encodeID($profileID),
				'profileName' 		=> purify($profileName),
				'roleID' 			=> encodeID($roleID),
				'isLoggedInSession' => TRUE
			]);

			$responseData = [
				'resCode' => 200,
				'message' => 'Login',
				'verify' => false,
				'redirectUrl' => url('dashboard'),
			];
		} else {
			$responseData = [
				'resCode' => 400,
				'message' => 'Your ID is inactive, Please contact system support',
				'verify' => false,
				'redirectUrl' => NULL,
			];
		}

		return $responseData;
	}

	public function reset()
	{
		if (isAjax()) {
			if (!checkMaintenance()) {

				$role  = input('role');
				$email  = input('email');

				$validateRecaptcha = $this->recaptcha->is_valid();

				// Check with recaptcha first
				if ($validateRecaptcha['success']) {
					$dataUser = $this->userM->getSpecificUser($email);

					if (!empty($dataUser)) {

						$token = encodeID($dataUser['email'] . '/' . timestamp(), 2); // generate token
						$resetPassData = $this->resetM::save([
							'user_id' => $dataUser['user_id'],
							'email' => $dataUser['email'],
							'reset_token' => $token,
							'reset_token_expired' => date('Y-m-d H:i:s', strtotime(timestamp() . ' + 45 minutes'))
						]);

						if (isSuccess($resetPassData['resCode'])) {
							$url = 'auth/reset-password/' . $token;

							$template = $this->templateM->where('email_type', 'FORGOT_PASSWORD')->get();
							$bodyMessage = replaceTextWithData($template['email_body'], [
								'to' => $dataUser['name'],
								'url' => url($url)
							]);

							// add to queue
							$saveQueue = $this->queueM::save([
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
							], false);

							if (isSuccess($saveQueue['resCode'])) {
								$responseData = [
									'resCode' => 200,
									'message' => 'Email has been sent',
									'redirectUrl' => $role == 2 ? url('store') : url(''),
								];
							} else {
								$responseData = [
									'resCode' => 400,
									'message' => 'Email sent unsuccessfully',
									'redirectUrl' => NULL,
								];
							}
						} else {
							$responseData = [
								'resCode' => 400,
								'message' => 'Email sent unsuccessfully',
								'redirectUrl' => NULL,
							];
						}
					} else {
						$responseData = [
							'resCode' => 400,
							'message' => 'Invalid email or email not register.',
						];
					}
				} else {
					$responseData = array(
						"resCode" => 400,
						"message" => filter_var(env('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ? $validateRecaptcha['error_message'] : "Please verify that you're a human",
						'verify' => false,
						"redirectUrl" => NULL,
					);
				}
			} else {
				$responseData = array(
					"resCode" => 400,
					"message" => 'System under maintenance',
					"redirectUrl" => NULL,
				);
			}

			json($responseData);
		} else {
			errorpage('404');
		}
	}

	public function logout()
	{
		destroySession(true);
	}
}
