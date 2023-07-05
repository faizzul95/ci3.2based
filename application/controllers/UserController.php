<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\users\processors\UserSearchProcessors;
use App\services\modules\core\users\processors\UserStoreProcessors;
use App\services\modules\core\users\processors\UserDeleteProcessors;
use App\services\modules\core\users\logics\UserShowLogic;

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

	public function show($id)
	{
		json(app(new UserShowLogic)->logic(['id' => xssClean($id)]));
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
