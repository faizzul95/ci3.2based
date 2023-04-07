
<?php

if (!function_exists('permission')) {
	function permission($slug = NULL)
	{
		$roleid = currentUserRoleID();

		$hasPermission = NULL;

		if (hasData($roleid)) {

			$ci = ci();
			$tableName = 'users_roles_abilities';

			if (!empty($slug)) {
				if (!isArray($slug)) {

					$ci->db->where('abilities_slug', $slug);
					$abilitiesData = $ci->db->get($tableName)->row_array();

					if ($abilitiesData) {
						$owned = $abilitiesData['only_owned'];
						if (!empty($owned)) {
							$ids = explode(',', $owned);
							$hasPermission = (in_array($roleid, $ids)) ? TRUE : FALSE;
						}
					}
				} else {
					$ci->db->where_in('abilities_slug', $slug);
					$abilitiesData = $ci->db->get($tableName)->result_array();

					if ($abilitiesData) {
						$checkAbilities = [];
						foreach ($abilitiesData as $data) {
							$newslug = $data['abilities_slug'];
							$owned = $data['only_owned'];

							if (!empty($owned)) {
								$ids = explode(',', $owned);
								$checkAbilities[$newslug] = (in_array($roleid, $ids)) ? TRUE : FALSE;
							} else {
								$checkAbilities[$newslug] = FALSE;
							}
						}

						$hasPermission =  $checkAbilities;
					}
				}
			} else {
				$abilitiesData = $ci->db->get($tableName)->result_array();

				if ($abilitiesData) {
					$checkAbilities = [];
					foreach ($abilitiesData as $data) {
						$newslug = $data['abilities_slug'];
						$owned = $data['only_owned'];

						if (!empty($owned)) {
							$ids = (!empty($owned)) ? explode(',', $owned) : NULL;
							$checkAbilities[$newslug] = (in_array($roleid, $ids)) ? TRUE : FALSE;
						} else {
							$checkAbilities[$newslug] = FALSE;
						}
					}

					$hasPermission =  $checkAbilities;
				}
			}
		}

		return $hasPermission;
	}
}
