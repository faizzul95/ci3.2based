
<?php

// if (!function_exists('hasAccess')) {
// 	function hasAccess()
// 	{
// 		if (!isAjax()) {
// 			$menu = ci()->uri->segment(1);
// 			$submenu = ci()->uri->segment(2);

// 			if (!empty($submenu)) {
// 				$menu = $menu . '/' . $submenu;
// 			}

// 			// check if current role is not superadmin
// 			if (currentUserRoleID() != 1) {

// 				$specialAccess = [
// 					'menu',
// 					'menu/permission',
// 					'menu/abilities',
// 					'management',
// 				];

// 				if (in_array($menu, $specialAccess)) {
// 					if (currentUserRoleID() == 2) {
// 						return true;
// 						exit;
// 					} else {
// 						errorpage('403');
// 						exit;
// 					}
// 				}

// 				// get menu
// 				$ci = ci();
// 				$ci->db->where('menu_url', $menu);
// 				$menuData = $ci->db->get('menu')->row_array();

// 				if ($menuData) {
// 					$menu_id = $menuData['menu_id'];

// 					$deviceID = isMobileDevice() ? 2 : 1;

// 					// get access
// 					$ci->db->where('menu_id', $menu_id);
// 					$ci->db->where('role_id', currentUserRoleID());
// 					$ci->db->where('access_device_type', $deviceID);
// 					$roleAccess = $ci->db->get('menu_permission')->result_array();

// 					if (count($roleAccess) > 0) {
// 						return true;
// 					} else {
// 						errorpage('403');
// 						exit;
// 					}
// 				} else {
// 					errorpage('403');
// 					exit;
// 				}
// 			} else {
// 				return true; // superadmin has all access
// 			}
// 		}
// 	}
// }

if (!function_exists('getMenu')) {
	function getMenu($menuLocation = 0)
	{
		$roleID = currentUserRoleID();
		$menuData = getMenuByRoleID($roleID, $menuLocation);
		$arrayMenu = array();

		if ($menuData) {

			foreach ($menuData as $main) {
				if ($main['menu_location'] == $menuLocation) {
					array_push($arrayMenu, [
						'menu_id' => $main['menu_id'],
						'menu_title' => $main['menu_title'],
						'menu_url' => $main['menu_url'],
						'menu_order' => $main['menu_order'],
						'menu_icon' => $main['menu_icon'],
						'submenu' => getSubMenuByMenuID($roleID, $main['menu_id']),
					]);
				}
			}
		}

		return $arrayMenu;
	}
}

if (!function_exists('getMenuByRoleID')) {
	function getMenuByRoleID($roleID = 1, $menuloc = 1, $main_menu = 0)
	{
		// $deviceID = isMobileDevice() ? 2 : 1;
		$deviceID = 1;

		$ci = ci();
		$ci->db->select('*');
		$ci->db->from('menu_permission mp');
		$ci->db->join('menu m', 'm.menu_id=mp.menu_id', 'left');
		$ci->db->where('m.is_active', '1');
		$ci->db->where('m.menu_location', $menuloc);
		$ci->db->where('m.is_main_menu', $main_menu);
		$ci->db->where('mp.role_id', $roleID);
		$ci->db->where('mp.access_device_type', $deviceID);
		$ci->db->order_by('m.menu_order', 'asc');
		return $ci->db->get()->result_array();
	}
}

if (!function_exists('getSubMenuByMenuID')) {
	function getSubMenuByMenuID($roleID = 1, $menuID = NULL)
	{
		// $deviceID = isMobileDevice() ? 2 : 1;
		$deviceID = 1;

		$ci = ci();
		$ci->db->select('*');
		$ci->db->from('menu_permission mp');
		$ci->db->join('menu m', 'm.menu_id=mp.menu_id', 'left');
		$ci->db->where('m.is_active', '1');
		$ci->db->where('m.is_main_menu', $menuID);
		$ci->db->where('mp.role_id', $roleID);
		$ci->db->where('mp.access_device_type', $deviceID);
		$ci->db->order_by('m.menu_order', 'asc');
		return $ci->db->get()->result_array();
	}
}

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
