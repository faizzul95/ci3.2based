
<?php

use App\services\generals\constants\MasterModule;

if (!function_exists('permission')) {
	function permission($slug = NULL)
	{
		$roleid = currentUserRoleID();

		$hasPermission = NULL;

		if (hasData($roleid)) {

			$ci = ci();
			$tableName = 'users_roles_abilities';

			// if (!empty($slug)) {
			// 	if (!isArray($slug)) {

			// 		$ci->db->where('abilities_slug', $slug);
			// 		$abilitiesData = $ci->db->get($tableName)->row_array();

			// 		if ($abilitiesData) {
			// 			$owned = $abilitiesData['only_owned'];
			// 			if (!empty($owned)) {
			// 				$ids = explode(',', $owned);
			// 				$hasPermission = (in_array($roleid, $ids)) ? TRUE : FALSE;
			// 			}
			// 		}
			// 	} else {
			// 		$ci->db->where_in('abilities_slug', $slug);
			// 		$abilitiesData = $ci->db->get($tableName)->result_array();

			// 		if ($abilitiesData) {
			// 			$checkAbilities = [];
			// 			foreach ($abilitiesData as $data) {
			// 				$newslug = $data['abilities_slug'];
			// 				$owned = $data['only_owned'];

			// 				if (!empty($owned)) {
			// 					$ids = explode(',', $owned);
			// 					$checkAbilities[$newslug] = (in_array($roleid, $ids)) ? TRUE : FALSE;
			// 				} else {
			// 					$checkAbilities[$newslug] = FALSE;
			// 				}
			// 			}

			// 			$hasPermission =  $checkAbilities;
			// 		}
			// 	}
			// } else {
			// 	$abilitiesData = $ci->db->get($tableName)->result_array();

			// 	if ($abilitiesData) {
			// 		$checkAbilities = [];
			// 		foreach ($abilitiesData as $data) {
			// 			$newslug = $data['abilities_slug'];
			// 			$owned = $data['only_owned'];

			// 			if (!empty($owned)) {
			// 				$ids = (!empty($owned)) ? explode(',', $owned) : NULL;
			// 				$checkAbilities[$newslug] = (in_array($roleid, $ids)) ? TRUE : FALSE;
			// 			} else {
			// 				$checkAbilities[$newslug] = FALSE;
			// 			}
			// 		}

			// 		$hasPermission =  $checkAbilities;
			// 	}
			// }
		}

		return $hasPermission;
	}
}

if (!function_exists('abilities')) {
	function abilities($slug = NULL)
	{
		$listModule = MasterModule::LIST;
		$roleid = currentUserRoleID();
		$moduleSubscribe = hasData(currentPackageModule()) ? explode(',', currentPackageModule()) : NULL;

		$hasPermission = [];
		return $hasPermission;
	}
}

if (!function_exists('menuList')) {
	function menuList()
	{
		// get 1st param segment from url
		$menuActive = segment(1);

		// get 2nd param segment from url
		$submenuActive = segment(2);

		// get full url string (without domain)
		$currentMenuUrl = uri_string();

		$moduleSubscribe = getModuleSubscribeList();

		if (hasData($moduleSubscribe['module'])) {

			// REPLACE WITH DATA FROM DATABASE
			$permission = $moduleSubscribe['all_permission'];

			foreach ($moduleSubscribe['module'] as $key => $module) {

				$module_code = $module['code'];
				$permissionModule = $module['permission'];
				$submodules = $module['submodule'];

				if (hasData($submodules && checkPermissionModule($module_code, $permissionModule, $moduleSubscribe['module_permission'], 'enable'))) {
					echo '<li class="menu-title"><span data-key="t-"' . $module_code . '>' . $module['name'] . '</span></li>';

					foreach ($submodules as $submodule) {
						$activeMenu = ($currentMenuUrl == $submodule['route']) ? 'active' : '';
						// $showSubMenu = ($menuActive == $submodule['route']) ? 'show' : '';
						$permissionSubModule = $submodule['permission'];

						// check if submodule is enable in this current environment
						if ($submodule['status']  && checkPermissionModule($module_code, $permissionSubModule, $permission, 'view')) {
							echo '<li class="nav-item">
                                     <a class="nav-link menu-link ' . $activeMenu . '" href="' . url($submodule['route']) . '">
                                         ' . $submodule['icon'] . ' <span data-key="t-' . purify($submodule['name']) . '">' . purify($submodule['name']) . '</span>
                                     </a>
                                 </li>';
						}
					}
				}
			}
		}
	}
}

if (!function_exists('getModuleSubscribeList')) {
	function getModuleSubscribeList()
	{
		model('MasterPackagePlan_model', 'packageM');

		// get current environment status
		$currentEnv = env('ENVIRONMENT') == 'production' ? 2 : 1;

		// $moduleSubscribe = hasData(currentPackageModule()) ? explode(',', currentPackageModule()) : NULL;

		// get subscribe module
		$subscribeData = ci()->packageM::find(currentPackageSubscribeID());
		$moduleSubscribe = hasData($subscribeData) ? explode(',', $subscribeData['package_module_plan']) : NULL;

		$moduleList = [];

		if (hasData($moduleSubscribe)) {

			$all_permission = [];
			$module_permission = [];
			$sub_module_permission = [];
			$permissionSubModule = [];

			foreach ($moduleSubscribe as $key => $moduleID) {
				$module = MasterModule::LIST[$moduleID];

				// check if module is enable for current environment
				if (in_array($currentEnv, $module['status'])) {

					$permissionModule = $module['permission'][0]['slug'];
					$submodules = $module['submodule'];

					if (hasData($submodules)) {
						foreach ($submodules as $submodule) {

							// check if sub module is enable for current environment
							if (in_array($currentEnv, $submodule['status'])) {
								foreach ($submodule['permission'] as $subpermission) {
									$subPermission = $subpermission['slug'];
									array_push($permissionSubModule, $subPermission);
									array_push($sub_module_permission, $subPermission);
								}
							}
						}
					}

					array_push($module_permission, $permissionModule);
					array_push($moduleList, $module);

					$all_permission = array_merge($all_permission, $module_permission, $permissionSubModule);
				}
			}
		}

		return ['module' => $moduleList, 'module_permission' => $module_permission, 'sub_module_permission' => $sub_module_permission, 'all_permission' => $all_permission];
	}
}

if (!function_exists('getModuleList')) {
	function getModuleList($includeSuperadmin = false)
	{
		$modules = MasterModule::LIST;

		// get current environment status
		$currentEnv = env('ENVIRONMENT') == 'production' ? 2 : 1;

		$list = [];
		foreach ($modules as $key => $module) {
			$moduleID = $key++;
			$statusModule = in_array($currentEnv, $module['status']) ? 'active' : 'inactive';
			$listSubModule = [];

			foreach ($module['submodule'] as $submodule) {
				$statusSubModule = in_array($currentEnv, $submodule['status']) ? 'active' : 'inactive';
				array_push($listSubModule, ['name' => $submodule['name'], 'desc' => $submodule['desc'], 'status' => $statusSubModule]);
			}

			if ($includeSuperadmin)
				array_push($list, ['id' => $moduleID, 'name' => $module['name'], 'desc' => $module['desc'], 'status' => $statusModule, 'sub_module' => $listSubModule]);
			else if (!$module['isSuperadmin'])
				array_push($list, ['id' => $moduleID, 'name' => $module['name'], 'desc' => $module['desc'], 'status' => $statusModule, 'sub_module' => $listSubModule]);
		}

		return $list;
	}
}

if (!function_exists('checkPermissionModule')) {
	function checkPermissionModule($prefix, $permissionModule, $listPermission, $suffix = NULL)
	{
		$format = $prefix . '-' . '.*' . '-' . $suffix; // regex pattern for any string between prefix and suffix

		$fullText = null;
		foreach ($permissionModule as $permission) {
			if (preg_match('/^' . $format . '$/', $permission['slug'])) {
				$fullText = $permission['slug'];
				break;
			}
		}

		if ($fullText !== null && in_array($fullText, $listPermission)) {
			// echo "Full text '$fullText' exists in \$listPermission.";
			return true;
		} else {
			// echo "Full text does not exist in \$listPermission.";
			return false;
		}
	}
}
