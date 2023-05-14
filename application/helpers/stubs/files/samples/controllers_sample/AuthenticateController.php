<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\general\constants\GeneralErrorMessage;
use App\services\general\traits\EmailTrait;
use App\middleware\core\traits\SecurityHeadersTrait;

class AuthenticateController extends CI_Controller
{
	use EmailTrait, SecurityHeadersTrait;

	public function __construct()
	{
		parent::__construct();
		$this->set_security_headers();

		model('User_model', 'userM');
		model('UserProfile_model', 'profileM');
		model('UserAuthAttempt_model', 'attemptM');
		model('UserAuthHistory_model', 'authHistoryM');
		model('Company_model', 'companyM');
		model('CompanyConfigEmailTemplate_model', 'templateM');
		model('SystemQueueJob_model', 'queueM');
		model('UserPasswordReset_model', 'resetM');

		library('recaptcha');
		library('user_agent');
	}

	public function index()
	{
		// check user session if exist
		if (isLoginCheck()) {
			redirect('dashboard', true);
		} else {

			// Check cookie remember me if exist relogin user
			$this->checkRememberMeCookie();

			// if not redirect to page login
			render('auth/login',  [
				'title' => 'Sign In',
				'currentSidebar' => 'auth',
				'currentSubSidebar' => 'login'
			]);
		}
	}

	public function authorize()
	{
		$username  = input('username');
		$enteredPassword = input('password');
		$rememberme = input('remember') ? true : false;

		// Check with recaptcha first
		$validateRecaptcha = recaptchav2();

		if ($validateRecaptcha['success']) {
			$dataUser = $this->userM->getSpecificUser($username);

			if (!empty($dataUser)) {
				$dbPassword = $dataUser['password'];
				$userID = $dataUser['id'];

				// get attempt login
				$attempt = $this->attemptM->login_attempt_exceeded($userID);
				$countAttempt = $attempt['count'];

				if ($attempt['isExceed']) {
					if (password_verify($enteredPassword, $dbPassword)) {

						$two_factor_status = $dataUser['two_factor_status'];
						$this->attemptM->clear_login_attempts($userID);

						// if 2FA is disabled
						if ($two_factor_status != 1) {
							// if success, start login session
							$responseData = $this->sessionLoginStart($userID, $rememberme, 1);
						}
						// if 2FA is enabled
						else {
							$responseData =  [
								'resCode' => 200,
								'message' => "",
								'redirectUrl' => url('verify/') . encodeID($userID, 5) . '/' . encodeID(timestamp('YmdHis'), 5) . '/' . encodeID($rememberme, 4),
							];
						}
					} else {
						// if failed to login
						$this->attemptM::save([
							'user_id' => $userID,
							'ip_address' => $this->input->ip_address(),
							'time' => timestamp(),
							'user_agent' => $this->input->user_agent()
						]);

						$countAttemptRemain = 5 - (int) $countAttempt;

						$responseData = GeneralErrorMessage::LIST['DEFAULT'];
						$responseData["message"] = ($countAttempt >= 2) ? 'Invalid username or password. Attempt remaining : ' . $countAttemptRemain : 'Invalid username or password';
					}
				} else {
					$responseData = GeneralErrorMessage::LIST['ATTEMPT'];
				}
			} else {
				$responseData = GeneralErrorMessage::LIST['DEFAULT'];
			}
		} else {
			$responseData = GeneralErrorMessage::LIST['RECAPTCHA'];
			$responseData["message"] = filter_var(env('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ? $validateRecaptcha['error_message'] : "Please verify that you're a human";
		}

		json($responseData);
	}

	public function socialite()
	{
		$email  = input('email');
		$dataUser = $this->userM->getSpecificUser($email);
		$rememberme = input('remember') ? true : false;

		if (!empty($dataUser) > 0) {
			$responseData = $this->sessionLoginStart($dataUser['id'], $rememberme, 2);
		} else {
			$responseData = GeneralErrorMessage::LIST['EMAIL_NOT_VALID'];
		}

		json($responseData);
	}

	public function verify2FA()
	{
		$username  = decodeID(input('username'), 5);
		$rememberme  = decodeID(input('remember'), 4) ? true : false;
		$dataUser = $this->userM->getSpecificUser($username);

		if (!empty($dataUser)) {
			$codeEnter = input('code_2fa');
			$codeSecret = $dataUser['two_factor_secret'];

			if (verifyGA($codeSecret, $codeEnter)) {
				$responseData = $this->sessionLoginStart($dataUser['id'], $rememberme, 1);
			} else {
				$responseData = GeneralErrorMessage::LIST['VERIFY2FA'];
			}
		} else {
			$responseData = GeneralErrorMessage::LIST['DEFAULT'];
		}

		json($responseData);
	}

	public function sessionLoginStart($userID, $remember = false, $loginType = 1, $profileID = NULL, $redirect = false)
	{
		$whereCondition = hasData($profileID) ? 'where:`id`=' . $profileID : 'where:`is_main`=1';
		$dataUser = $this->userM->with_main_profile('fields:id,user_id,roles_id,is_main,department_id,profile_status', $whereCondition, [
			'with' => [
				[
					'relation' => 'roles',
					'fields' => 'role_name,role_code,role_group,abilities_json'
				],
				[
					'relation' => 'department',
					'fields' => 'department_name,department_code'
				],
				[
					'relation' => 'avatar',
					'fields' => 'files_compression,files_path,files_path_is_url,entity_file_type',
					'where' => '`entity_file_type`=\'PROFILE_PHOTO\'',
				],
				[
					'relation' => 'profileHeader',
					'fields' => 'files_compression,files_path,files_path_is_url,entity_file_type',
					'where' => '`entity_file_type`=\'PROFILE_HEADER_PHOTO\'',
				],
			]
		])->where('id', $userID)->get();

		$userFullName = $dataUser['name'];
		$userNickName = $dataUser['user_preferred_name'];
		$userEmail = $dataUser['email'];
		$userStatus = $dataUser['user_status'];
		$userStaffNo = $dataUser['user_staff_no'];
		$companyID = $dataUser['company_id'];

		if ($userStatus == 1) {
			$dataUserProfile = $dataUser['main_profile'];
			$userAvatar = fileExist($dataUserProfile['avatar']['files_path']) ? asset($dataUserProfile['avatar']['files_path'], false) : defaultImage('user');
			$userProfileHeader = hasData($dataUserProfile['profileHeader']) ? asset($dataUserProfile['profileHeader']['files_path']) : defaultImage('banner');

			$profileID = $dataUserProfile['id'];
			$roleID = $dataUserProfile['roles']['id'];
			$profileName = $dataUserProfile['roles']['role_name'];

			// default session data
			$defaultSession = [
				'userID'  			=> encodeID($userID),
				'userFullName'  	=> purify($userFullName),
				'userNickName'  	=> purify($userNickName),
				'userStaffNo'  		=> encodeID($userStaffNo),
				'userEmail'  		=> purify($userEmail),
				'userAvatar'  		=> purify($userAvatar),
				'userProfileHeader' => purify($userProfileHeader),
				'profileID'  		=> encodeID($profileID),
				'profileName' 		=> purify($profileName),
				'roleID' 			=> encodeID($roleID),
				'companyID' 		=> encodeID($companyID),
				'isLoggedInSession' => TRUE
			];

			$dataCompany = $this->companyM->with_logo_company('fields:files_compression,files_path,files_path_is_url,entity_file_type', 'where:`entity_file_type`=\'COMPANY_LOGO_PHOTO\'')
				->with_qr_code('fields:files_compression,files_path,files_path_is_url,entity_file_type', 'where:`entity_file_type`=\'COMPANY_HEADER_PHOTO\'')
				->with_company_header('fields:files_compression,files_path,files_path_is_url,entity_file_type', 'where:`entity_file_type`=\'COMPANY_QR_CODE\'')
				->with_address('fields:id,address_1,address_2,city_name,state_name,postcode,country_name', 'where:`entity_address_type`=\'COMPANY_ADDRESS\'')
				->where('id', $companyID)
				->get();

			$logoPath = hasData($dataCompany, 'logo_company') ? $dataCompany['logo_company']['files_path'] : NULL;
			$companyLogo = fileExist($logoPath) ? asset($logoPath, false) : defaultImage('company_logo');

			// company session data
			$companySession = [
				'companyName' 		=> hasData($dataCompany) ? purify($dataCompany['company_name']) : NULL,
				'companyNickName' 	=> hasData($dataCompany) ? purify($dataCompany['company_nickname']) : NULL,
				'companyCode' 		=> hasData($dataCompany) ? purify($dataCompany['company_no']) : NULL,
				'companyLogo' 		=> purify($companyLogo),
			];

			$startSession = array_merge($defaultSession, $companySession);
			setSession($startSession);

			// Sent email secure login
			$template = $this->templateM->where('email_type', 'SECURE_LOGIN')->where('email_status', '1')->where('company_id', $companyID)->get();

			$browsers = $this->agent->browser();
			$os = $this->agent->platform();
			$iplogin = $this->input->ip_address();

			// if template email is exist and active && login type is not using token
			if (hasData($template) && $loginType != 3) {

				$bodyMessage = replaceTextWithData($template['email_body'], [
					'name' => purify($userFullName),
					'email' => purify($userEmail),
					'browsers' => $browsers,
					'os' => $os,
					'details' => '<table border="1" cellpadding="1" cellspacing="1" width="40%">
								<tr>
									<td style="width:30%">&nbsp; Username </td>
									<td style="width:70%">&nbsp; ' . purify($dataUser['username']) . ' </td>
								</tr>
								<tr>
									<td style="width:30%">&nbsp; Browser </td>
									<td style="width:70%">&nbsp; ' . $browsers . ' </td>
								</tr>
								<tr>
									<td style="width:30%">&nbsp; Operating System </td>
									<td style="width:70%">&nbsp; ' . $os . ' </td>
								</tr>
								<tr>
									<td style="width:30%">&nbsp; IP Address </td>
									<td style="width:70%">&nbsp; ' . $iplogin . ' </td>
								</tr>
								<tr>
									<td style="width:30%">&nbsp; Date </td>
									<td style="width:70%">&nbsp; ' . timestamp('d/m/Y') . ' </td>
								</tr>
								<tr>
									<td style="width:30%">&nbsp; Time </td>
									<td style="width:70%">&nbsp; ' . timestamp('h:i A') . ' </td>
								</tr>
							  </table>',
					'url' => baseURL()
				]);

				// Testing Using trait (use phpmailer)
				// $this->testSentEmail($dataUser, $bodyMessage, $template);

				// if ($sentMail['success']) {
				// add to queue
				$this->queueM::save([
					'queue_uuid' => uuid(),
					'type' => 'email',
					'payload' => json_encode([
						'name' => $userFullName,
						'to' => $userEmail,
						'cc' => $template['email_cc'],
						'bcc' => $template['email_bcc'],
						'subject' => $template['email_subject'],
						'body' => $bodyMessage,
						'attachment' => NULL,
					]),
					'company_id' => $companyID,
				], false);
				// }
			}

			// Add to login history
			$this->authHistoryM::save([
				'ip_address' => $iplogin,
				'user_id' => $userID,
				'time' => timestamp(),
				'operating_system' => $os,
				'browsers' => $browsers,
				'user_agent' => $this->input->user_agent(),
				'login_type' => $loginType,
			]);

			// set remember token
			if ($remember) {
				// Refresh the token in the database and cookie
				$new_token = $userID . bin2hex(random_bytes(16));
				$this->userM::save(['id' => $userID, 'remember_token' => $new_token]);
				set_cookie('remember_me_token_cipmo', $new_token, strtotime('+2 week'));
			}

			$responseData = [
				'resCode' => 200,
				'message' => 'Login',
				'redirectUrl' => url('dashboard'),
			];
		} else {
			$responseData = GeneralErrorMessage::LIST['INACTIVE'];
		}

		if ($redirect)
			redirect('dashboard', true);
		else
			json($responseData);
	}

	public function resetPasswordLink()
	{
		$email  = input('email');

		$validateRecaptcha = recaptchav2();

		// Check with recaptcha first
		if ($validateRecaptcha['success']) {
			// query data user by email
			$dataUser = $this->userM->getSpecificUser($email);

			// check if data user is exist
			if (hasData($dataUser)) {

				$companyID = $dataUser['company_id'];

				$token = $dataUser['id'] . bin2hex(random_bytes(20));
				$resetPassData = $this->resetM::save([
					'user_id' => $dataUser['id'],
					'email' => $dataUser['email'],
					'reset_token' => $token,
					'reset_token_expired' => date('Y-m-d H:i:s', strtotime(timestamp() . ' + 30 minutes'))
				]);

				if (isSuccess($resetPassData['resCode'])) {
					$url = 'auth/reset-password/' . $token;
					$template = $this->templateM->where('email_type', 'FORGOT_PASSWORD')->where('email_status', '1')->where('company_id', $companyID)->get();

					if (hasData($template)) {
						$bodyMessage = replaceTextWithData($template['email_body'], [
							'to' => $dataUser['name'],
							'url' => url($url)
						]);

						// Testing Using trait (use phpmailer)
						// $this->testSentEmail($dataUser, $bodyMessage, $template);

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
								'redirectUrl' => url(''),
							];
						} else {
							$responseData = GeneralErrorMessage::LIST['FORGOT'];
						}
					} else {
						$responseData = GeneralErrorMessage::LIST['FORGOT'];
					}
				} else {
					$responseData = GeneralErrorMessage::LIST['FORGOT'];
				}
			} else {
				$responseData = GeneralErrorMessage::LIST['EMAIL_NOT_VALID'];
			}
		} else {
			$responseData = GeneralErrorMessage::LIST['RECAPTCHA'];
			$responseData["message"] = filter_var(env('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ? $validateRecaptcha['error_message'] : "Please verify that you're a human";
		}

		json($responseData);
	}

	public function resetPasswordPage($token = NULL)
	{
		// check if token has data
		if (hasData($token)) {
			// query data reset by token
			$dataReset = $this->resetM::find($token, 'reset_token');

			// check if data reset is exist
			if (hasData($dataReset)) {
				// check if token is expired
				if ($dataReset['reset_token_expired'] > timestamp()) {
					render('auth/reset',  [
						'title' => 'Reset Password',
						'currentSidebar' => 'auth',
						'currentSubSidebar' => 'reset',
						'data' => $dataReset
					]);
				} else {
					json(GeneralErrorMessage::LIST['TOKEN_RESET']);
				}
			} else {
				json(GeneralErrorMessage::LIST['TOKEN_RESET']);
			}
		} else {
			redirect('auth', true);
		}
	}

	private function checkRememberMeCookie()
	{
		$token = get_cookie('remember_me_token_cipmo');

		if (hasData($token)) {
			$dataUser = $this->userM::find($token, 'remember_token');
			if ($dataUser) {
				$this->sessionLoginStart($dataUser['id'], true, 3, NULL, true); // Log the user in
			}
		}
	}

	public function logout()
	{
		delete_cookie('remember_me_token_cipmo');
		destroySession();
	}
}
