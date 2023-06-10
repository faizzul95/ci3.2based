<?php

defined('BASEPATH') or exit('No direct script access allowed');

class DashboardController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		// if (currentUserRoleID() == 1) {
		$file = 'superadmin';
		// } else if (currentUserRoleID() == 2) {
		// 	$file = 'demo';
		// } 

		render('dashboard/' . $file,  [
			'title' => 'Dashboard',
			'currentSidebar' => 'Dashboard',
			'currentSubSidebar' => NULL,
			'permission' => ''
		]);
	}
}
