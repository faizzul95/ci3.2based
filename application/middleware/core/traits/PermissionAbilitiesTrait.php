<?php

namespace App\middleware\core\traits;

defined('BASEPATH') or exit('No direct script access allowed');

trait PermissionAbilitiesTrait
{
	public function hasPermissionAction()
	{
		$permissionHeader = ci()->input->get_request_header('x-permission', TRUE);

		// Access specific Axios header values
		if (hasData($permissionHeader)) {

			// initialize table
			model('MODEL_NAME', 'model_alias');

			$dataProfiles = ci()->model_alias::find(currentUserProfileID());
			$permissionArray = ''; // create own logic

			if (hasData($permissionArray))
				$permission = in_array($permissionHeader, $permissionArray) ? true : false;
			else
				$permission = false; // set false

		} else {
			$permission = true; // set true if no header x-permission to validate
		}

		return $permission;
	}
}
