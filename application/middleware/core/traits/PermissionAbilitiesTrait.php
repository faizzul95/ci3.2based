<?php

namespace App\middleware\core\traits;

defined('BASEPATH') or exit('No direct script access allowed');

trait PermissionAbilitiesTrait
{
	public function hasPermissionAction()
	{
		// $permissionHeader = ci()->input->get_request_header('x-permission', TRUE);
		$permission = true; // default permission is true

		// Access specific Axios header values
		if (isset($_SERVER['HTTP_X_PERMISSION']) && hasData($_SERVER['HTTP_X_PERMISSION'])) {
			// $permission = json_decode($_SERVER['HTTP_X_PERMISSION'], true);
			// $permission = $_SERVER['HTTP_X_PERMISSION'];
			// dd($permission, $_SERVER['HTTP_X_PERMISSION']);
		}

		return $permission;
	}
}
