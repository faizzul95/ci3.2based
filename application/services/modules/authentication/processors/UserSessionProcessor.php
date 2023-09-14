<?php

namespace App\services\modules\authentication\processors;

use App\services\generals\constants\LoginType;
use App\services\generals\constants\GeneralStatus;
use App\services\generals\constants\GeneralErrorMessage;
use App\services\generals\traits\QueueTrait;

use App\services\modules\core\users\processors\UserSearchProcessors;
use App\services\modules\core\companies\processors\CompaniesSearchProcessors;

class UserSessionProcessor
{
	use QueueTrait;

	public function __construct()
	{
		model('User_model', 'userM');
		model('Company_model', 'companyM');
		model('CompanyConfigEmailTemplate_model', 'templateM');
		model('UserAuthHistory_model', 'authHistoryM');

		library('user_agent');
	}

	public function execute($userID, $loginType, $remember = false, $profileID = NULL, $impersonateID = NULL)
	{
		// $whereCondition = hasData($profileID) ? 'where:`id`=' . $profileID : 'where:`is_main`=1';
		// $dataUser = ci()->userM->with_main_profile('fields:id,user_id,roles_id,is_main,department_id,profile_status', $whereCondition, [
		// 	'with' => [
		// 		[
		// 			'relation' => 'roles',
		// 			'fields' => 'role_name,role_code,role_group,abilities_json'
		// 		],
		// 		[
		// 			'relation' => 'department',
		// 			'fields' => 'department_name,department_code'
		// 		],
		// 		[
		// 			'relation' => 'avatar',
		// 			'fields' => 'files_compression,files_path,files_path_is_url,files_folder,entity_file_type',
		// 			'where' => '`entity_file_type`=\'PROFILE_PHOTO\'',
		// 		],
		// 		[
		// 			'relation' => 'profileHeader',
		// 			'fields' => 'files_compression,files_path,files_path_is_url,entity_file_type',
		// 			'where' => '`entity_file_type`=\'PROFILE_HEADER_PHOTO\'',
		// 		],
		// 	]
		// ])->where('id', $userID)->get();

		$dataUser = app(new UserSearchProcessors)->execute([
			'fields' => 'id,name,user_preferred_name,user_staff_no,user_nric_visa,email,user_contact_no,user_gender,user_dob,username,password,user_marital_status,user_status,company_id',
			'conditions' => [
				'id' => hasData($impersonateID) ? $impersonateID : $userID,
			],
			'with' => [
				'main_profile' => [
					'fields' => 'id,user_id,roles_id,is_main,department_id,profile_status',
					'conditions' => hasData($profileID) ? '`id`=' . $profileID : '`is_main`=1',
					'with' => [
						'roles' => ['fields' => 'role_name,role_code,role_group,abilities_json'],
						'department' => ['fields' => 'department_name,department_code'],
						'avatar' => [
							'fields' => 'files_compression,files_path,files_path_is_url,files_folder,entity_file_type',
							'conditions' => '`entity_file_type`=\'PROFILE_PHOTO\'',
						],
						'profileHeader' => [
							'fields' => 'files_compression,files_path,files_path_is_url,files_folder,entity_file_type',
							'conditions' => '`entity_file_type`=\'PROFILE_HEADER_PHOTO\'',
						]
					]
				]
			]
		], 'get');

		$userFullName = $dataUser['name'];
		$userNickName = $dataUser['user_preferred_name'];
		$userEmail = $dataUser['email'];
		$userStatus = $dataUser['user_status'];
		$userStaffNo = $dataUser['user_staff_no'];
		$companyID = $dataUser['company_id'];

		if ($userStatus == GeneralStatus::ACTIVE) {
			$dataUserProfile = $dataUser['main_profile'];
			$userAvatar = fileExist($dataUserProfile['avatar']['files_path']) ? asset($dataUserProfile['avatar']['files_path'], false) : defaultImage('user');
			$userProfileHeader = hasData($dataUserProfile['profileHeader']) ? asset($dataUserProfile['profileHeader']['files_path']) : defaultImage('banner');

			$profileID = $dataUserProfile['id'];
			$roleID = $dataUserProfile['roles']['id'];
			$roleGroupID = $dataUserProfile['roles']['role_group'];
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
				'roleGroupID'           => encodeID($roleGroupID),
				'companyID'         	=> encodeID($companyID),
				'impersonatorID'        => hasData($impersonateID) ? $userID : NULL,
				'isLoggedInSession' 	=> TRUE
			];

			// $dataCompany = ci()->companyM
			// 	->fields('id,company_name,company_nickname,company_no,company_tel_no,company_email,company_status')
			// 	->with_active_subcription('fields:id,package_id,company_id,subscription_status|order_inside:subscription_order_no asc', [
			// 		'with' => [
			// 			[
			// 				'relation' => 'package',
			// 				'fields' => 'package_name,package_code,package_module_plan,package_status'
			// 			]
			// 		]
			// 	])
			// 	->with_logo_company('fields:files_compression,files_path,files_path_is_url,files_type,entity_file_type,files_folder', 'where:`entity_file_type`=\'COMPANY_LOGO_PHOTO\'')
			// 	->with_qr_code('fields:files_compression,files_path,files_path_is_url,files_type,entity_file_type,files_folder', 'where:`entity_file_type`=\'COMPANY_QR_CODE\'')
			// 	->with_company_header('fields:files_compression,files_path,files_path_is_url,files_type,entity_file_type,files_folder', 'where:`entity_file_type`=\'COMPANY_HEADER_PHOTO\'')
			// 	->with_address('fields:id,address_1,address_2,city_name,state_name,postcode,country_name', 'where:`entity_address_type`=\'COMPANY_ADDRESS\'')
			// 	->where('id', $companyID)
			// 	->get();

			$dataCompany = app(new CompaniesSearchProcessors)->execute([
				'fields' => 'id,company_name,company_nickname,company_no,company_tel_no,company_email,company_status',
				'conditions' => [
					'id' => $companyID,
				],
				'with' => [
					'active_subcription' => [
						'fields' => 'id,package_id,company_id,subscription_status|order_inside:subscription_order_no asc',
						'with' => [
							'package' => ['fields' => 'package_name,package_code,package_module_plan,package_status']
						]
					],
					'logo_company' => [
						'fields' => 'files_compression,files_path,files_path_is_url,files_type,entity_file_type,files_folder',
						'conditions' => '`entity_file_type`=\'COMPANY_LOGO_PHOTO\''
					],
					'qr_code' => [
						'fields' => 'files_compression,files_path,files_path_is_url,files_type,entity_file_type,files_folder',
						'conditions' => '`entity_file_type`=\'COMPANY_QR_CODE\''
					],
					'company_header' => [
						'fields' => 'files_compression,files_path,files_path_is_url,files_type,entity_file_type,files_folder',
						'conditions' => '`entity_file_type`=\'COMPANY_HEADER_PHOTO\''
					],
					'address' => [
						'fields' => 'id,address_1,address_2,city_name,state_name,postcode,country_name',
						'conditions' => '`entity_address_type`=\'COMPANY_ADDRESS\''
					],
				]
			], 'get');

			$logoPath = hasData($dataCompany, 'logo_company') ? fileImage($dataCompany['logo_company'], 'company_logo') : NULL;

			// company session data
			$companySession = [
				'companyName'         => hasData($dataCompany) ? purify($dataCompany['company_name']) : NULL,
				'companyNickName'     => hasData($dataCompany) ? purify($dataCompany['company_nickname']) : NULL,
				'companyCode'         => hasData($dataCompany) ? purify($dataCompany['company_no']) : NULL,
				'companyLogo'         => purify($logoPath),
			];

			// subscription session data
			$subscriptionSession = [
				'subscriptionID' => hasData($dataCompany, 'active_subcription') ? encodeID($dataCompany['active_subcription']['package_id']) : NULL,
				'subscriptionName' => hasData($dataCompany, 'active_subcription') ? (hasData($dataCompany['active_subcription'], 'package') ? $dataCompany['active_subcription']['package']['package_name'] : NULL) : NULL,
			];

			$startSession = array_merge($defaultSession, $companySession, $subscriptionSession);

			setSession($startSession);

			// Sent email secure login
			$template = ci()->templateM->where('email_type', 'SECURE_LOGIN')->where('email_status', '1')->where('company_id', $companyID)->get();

			$browsers = ci()->agent->browser();
			$os = ci()->agent->platform();
			$iplogin = ci()->input->ip_address();

			if (hasData($loginType)) {
				// if template email is exist and active && login type is not using token
				if (hasData($template) && $loginType != LoginType::TOKEN) {

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
					// $this->testSentEmail($dataUser, $bodyMessage, $template);

					// if ($sentMail['success']) {
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
						'company_id' => $companyID,
					]);
					// }
				}

				// Add to login history
				ci()->authHistoryM::save([
					'ip_address' => $iplogin,
					'user_id' => $userID,
					'time' => timestamp(),
					'operating_system' => $os,
					'browsers' => $browsers,
					'user_agent' => ci()->input->user_agent(),
					'login_type' => $loginType,
				]);
			}

			// set remember token
			if ($remember) {
				// Refresh the token in the database and cookie
				$new_token = $userID . bin2hex(random_bytes(16));
				ci()->userM::save(['id' => $userID, 'remember_token' => $new_token]);
				set_cookie('remember_me_token_ciarcav5', $new_token, strtotime('+4 week'));
			}

			$responseData = [
				'code' => 200,
				'message' => 'Login',
				'redirectUrl' => 'dashboard',
			];
		} else {
			$responseData = GeneralErrorMessage::LIST['AUTH']['INACTIVE'];
		}

		return $responseData;
	}
}
