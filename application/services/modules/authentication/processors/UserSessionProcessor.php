<?php

namespace App\services\modules\authentication\processors;

use App\services\generals\constants\LoginType;
use App\services\generals\constants\GeneralStatus;
use App\services\generals\constants\GeneralErrorMessage;
use App\services\generals\traits\QueueTrait;

use App\services\modules\user\users\processors\UsersSearchProcessors;
use App\services\modules\user\users\processors\UsersStoreProcessors;
use App\services\modules\user\usersLoginHistory\processors\UsersLoginHistoryStoreProcessors;

use App\services\modules\master\masterEmailTemplates\processors\MasterEmailTemplatesSearchProcessors;
use App\services\modules\core\systemAccessTokens\processors\SystemAccessTokensStoreProcessors;

class UserSessionProcessor
{
	use QueueTrait;

	public function __construct()
	{
		library('user_agent');
	}

	public function execute($userID, $loginType, $remember = false, $profileID = NULL, $impersonateID = NULL)
	{
		$dataUser = app(new UsersSearchProcessors)->execute([
			'fields' => 'id,name,user_preferred_name,user_nric,email,user_contact_no,user_gender,user_matric_code,program_id,edu_level_id,user_intake,user_dob,user_status,user_marital_status,branch_id,is_deleted',
			'conditions' => [
				'id' => hasData($impersonateID) ? $impersonateID : $userID,
			],
			'with' => [
				'accessToken' => 'id,tokenable_id,name,token',
				'main_profile' => [
					'fields' => 'id,user_id,role_id,college_id,profile_status,is_main,is_special,has_position,branch_id',
					'conditions' => hasData($profileID) ? '`id`=' . $profileID : '`is_main`=1',
					'with' => [
						'roles' => ['fields' => 'role_name,role_code'],
						// 'branch' => ['fields' => 'id,branch_name,branch_code'],
						'branch' => [
							'fields' => 'id,branch_name,branch_code',
							'with' => [
								'address' => [
									'fields' => 'id,address_1,address_2,city_name,state_name,postcode,country_name,entity_address_type',
									'conditions' => '`entity_address_type`=\'BRANCH_ADDRESS\''
								],
								'logo' => [
									'fields' => 'files_compression,files_path,files_path_is_url,files_type,entity_file_type,files_folder',
									'conditions' => '`entity_file_type`=\'BRANCH_LOGO_PHOTO\''
								],
							]
						],
						'avatar' => [
							'fields' => 'files_compression,files_path,files_path_is_url,files_folder,entity_file_type,branch_id',
							'conditions' => '`entity_file_type`=\'PROFILE_PHOTO\'',
						],
						'profileHeader' => [
							'fields' => 'files_compression,files_path,files_path_is_url,files_folder,entity_file_type,branch_id',
							'conditions' => '`entity_file_type`=\'PROFILE_HEADER_PHOTO\'',
						],
					]
				],
			]
		], 'get');

		$userFullName = $dataUser['name'];
		$userNickName = $dataUser['user_preferred_name'];
		$userEmail = $dataUser['email'];
		$userStatus = $dataUser['user_status'];
		$userStaffNo = $dataUser['user_matric_code'];
		$branchID = $dataUser['branch_id'];

		if ($userStatus == GeneralStatus::ACTIVE) {

			// Sent email secure login
			$template = app(new MasterEmailTemplatesSearchProcessors)->execute([
				'fields' => 'id,email_type,email_subject,email_body,email_footer,email_cc,email_bcc,email_status,branch_id',
				'conditions' => [
					'email_status' => 1,
					'email_type' => 'SECURE_LOGIN',
					'branch_id' => $branchID,
				]
			], 'get');

			$browsers = ci()->agent->browser();
			$os = ci()->agent->platform();
			$iplogin = ci()->input->ip_address();
			$agent = ci()->input->user_agent();

			if ($loginType == LoginType::TOKEN) {

				$accessToken = $dataUser['accessToken'];
				$dataUserProfile = $dataUser['main_profile'];

				$generateToken = $this->createToken($userID, ["user_id" => $userID, "branch_id" => $branchID, "profile_id" => $dataUserProfile['id']]);

				if (hasData($generateToken)) {
					$generateToken['id'] = hasData($accessToken, 'id', true);
					app(new SystemAccessTokensStoreProcessors)->execute($generateToken);
				}

				$responseData = ['code' => 200, 'message' => 'Login successfully', 'token' => $generateToken['token']];
				
			} else if (in_array($loginType, [LoginType::CREDENTIAL, LoginType::SOCIALITE])) {

				$dataUserProfile = $dataUser['main_profile'];
				$dataBranch = $dataUserProfile['branch'];
				$userAvatar = fileExist($dataUserProfile['avatar']['files_path']) ? asset($dataUserProfile['avatar']['files_path'], false) : defaultImage('user');
				$userProfileHeader = hasData($dataUserProfile['profileHeader']) ? asset($dataUserProfile['profileHeader']['files_path']) : defaultImage('banner');

				$profileID = $dataUserProfile['id'];
				$roleID = $dataUserProfile['roles']['id'];
				$profileName = $dataUserProfile['roles']['role_name'];

				// default session data
				$defaultSession = [
					'userID'              	=> hasData($impersonateID) ? encodeID($impersonateID) : encodeID($userID),
					'userFullName'      	=> purify($userFullName),
					'userNickName'      	=> purify($userNickName),
					'userStaffNo'          	=> encodeID($userStaffNo),
					'userEmail'          	=> purify($userEmail),
					'userAvatar'          	=> purify($userAvatar),
					'userProfileHeader' 	=> purify($userProfileHeader),
					'profileID'          	=> encodeID($profileID),
					'profileName'         	=> purify($profileName),
					'roleID'             	=> encodeID($roleID),
					'branchID'         		=> encodeID($branchID),
					'impersonatorID'        => hasData($impersonateID) ? $userID : NULL,
					'isLoggedInSession' 	=> TRUE
				];

				$logoPath = hasData($dataBranch, 'logo') ? fileImage($dataBranch['logo'], 'branch_logo') : NULL;

				// branch session data
				$branchSession = [
					'branchName'         => purify(hasData($dataBranch, 'branch_name', true)),
					'branchCode'         => purify(hasData($dataBranch, 'branch_code', true)),
					'branchLogo'         => purify($logoPath),
				];

				$startSession = array_merge($defaultSession, $branchSession);

				setSession($startSession);

				if (hasData($template)) {

					$bodyMessage = replaceTextWithData($template['email_body'], [
						'name' => purify($userFullName),
						'email' => purify($userEmail),
						'browsers' => $browsers,
						'os' => $os,
						'details' => '<table border="1" cellpadding="1" cellspacing="1" width="100%">
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
						'url' => base_url()
					]);

					// Testing Using trait (use phpmailer)
					// $debug = $this->testSentEmail($dataUser, $bodyMessage, $template);

					// add to queue
					$this->addQueue([
						'payload' => json_encode([
							'name' => $userFullName,
							'to' => $userEmail,
							'cc' => $template['email_cc'],
							'bcc' => $template['email_bcc'],
							'subject' => $template['email_subject'],
							'body' => $bodyMessage,
							'attachment' => NULL,
						]),
						'branch_id' => $branchID,
					]);
				}
			}

			// Add to login history
			app(new UsersLoginHistoryStoreProcessors)->execute([
				'user_id' => $userID,
				'ip_address' => $iplogin,
				'login_type' => $loginType,
				'operating_system' => $os,
				'browsers' => $browsers,
				'time' => timestamp(),
				'user_agent' => $agent,
			]);

			// set remember token
			if ($remember) {
				// Refresh the token in the database and cookie
				$new_token = $userID . bin2hex(random_bytes(16)) . timestamp('Ymd');
				app(new UsersStoreProcessors)->execute(['id' => $userID, 'remember_token' => $new_token]);
				set_cookie(env('REMEMBER_COOKIE_NAME'), $new_token, strtotime('+4 week'));
			}

			$responseData = [
				'code' => 200,
				'message' => 'Login',
				'redirectUrl' => 'dashboard',
			];

		} else {
			$responseData = $dataUser['is_deleted'] == 1 ?  GeneralErrorMessage::LIST['AUTH']['DELETED'] : GeneralErrorMessage::LIST['AUTH']['INACTIVE'];
		}

		return $responseData;
	}

	private function createToken($userID, $payload = [], $token_name = "Auth Token")
	{
		// Generate a random token
		$token = bin2hex(random_bytes(32)); // You can adjust the token length as needed

		// Store the token in the database
		$data = [
			'tokenable_type' => 'Users_model',
			'tokenable_id' => $userID,
			'name' => $token_name,
			'token' => $token,
			'abilities' => json_encode($payload),
			'created_at' => timestamp()
		];

		return $data;
	}
}