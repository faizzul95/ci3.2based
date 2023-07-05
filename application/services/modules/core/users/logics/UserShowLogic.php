<?php

namespace App\services\modules\core\users\logics;

use App\services\modules\core\users\processors\UserSearchProcessors;

class UserShowLogic
{
	public function __construct()
	{
	}

	public function logic($request)
	{
		return app(new UserSearchProcessors)->execute([
			'fields' => 'id,name,user_preferred_name,user_staff_no,user_nric_visa,email,user_contact_no,user_gender,user_dob,user_marital_status,user_status,company_id',
			'conditions' => [
				'id' => $request['id'],
			],
			'with' => [
				'company' => [
					'fields' => 'id,company_name,company_tel_no,company_email,company_website,company_status'
				],
				'main_profile' => [
					'fields' => 'id,user_id,roles_id,is_main,department_id,profile_status',
					'conditions' => '`is_main`=1',
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
	}
}
