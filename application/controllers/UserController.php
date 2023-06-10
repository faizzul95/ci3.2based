<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\users\processors\UserSearchProcessors;
use App\services\modules\core\users\processors\UserStoreProcessors;
use App\services\modules\core\users\processors\UserDeleteProcessors;

class UserController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		model('User_model', 'UserM');
	}

	public function index()
	{
		error('404');
	}

	public function getListUser()
	{
		echo $this->UserM->getUserListDt(input('status'), input('role'));
	}

	public function getUserByID($id)
	{
		$dataUser = app(new UserSearchProcessors)->execute([
			'fields' => 'id,name,user_preferred_name,user_staff_no,user_nric_visa,email,user_contact_no,user_gender,user_dob,username,password,user_marital_status,user_status,company_id',
			'conditions' => [
				'id' => xssClean($id),
			],
			'with' => [
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

		json($dataUser);
	}

	public function save()
	{
		$this->_rules();

		if ($this->form_validation->run() === FALSE) {
			validationErrMessage();
		} else {
			$result = app(new UserStoreProcessors)->execute($_POST);
			json($result);
		}
	}

	public function delete($id)
	{
		$result = app(new UserDeleteProcessors)->execute(xssClean($id));
		json($result);
	}

	public function _rules()
	{
		$this->form_validation->reset_validation(); // reset validation
		$this->form_validation->set_rules('id', 'User ID', 'trim|integer');
	}
}
