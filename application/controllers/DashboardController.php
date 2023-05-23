<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\general\constants\MasterGroupRoles;

class DashboardController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		// dd(getAllSession(), currentUserRoleID());

		// if (currentUserRoleID() == 1) {
		$file = 'superadmin';
		// } else if (currentUserRoleID() == 2) {
		// 	$file = 'demo';
		// } 

		render('dashboard/' . $file,  [
			'title' => 'Dashboard',
			'currentSidebar' => 'Dashboard',
			'currentSubSidebar' => NULL,
			'permission' => permission(['dashboard-view'])
		]);
	}
}
