<?php

namespace App\services\generals\constants;

final class MasterModule
{
	// MODULE LIST
	public const EMS = 1;
	public const RBAC = 99;

	// MODULE ACTIVE STATUS
	public const DEVELOPMENT = 1;
	public const PRODUCTION = 2;
	public const MAINTENANCE = 3;

	public const LIST = [
		self::EMS => [
			'name' => 'Employees Management',
			'desc' => NULL,
			'code' => 'ems',
			'icon' => NULL,
			'permission' => [['slug' => 'ems-module-enable', 'title' => 'ENABLE EMPLOYEE MANAGEMENT MODULE', 'remark' => '']],
			'status' => [self::DEVELOPMENT],
			'submodule' => [
				[
					'name' => 'Directory',
					'desc' => NULL,
					'icon' => '<i class="ri-user-5-line"></i>',
					'permission' => [
						['slug' => 'ems-directory-view', 'title' => 'VIEW DIRECTORY', 'remark' => '']
					],
					'route' => 'directory',
					'status' => [self::DEVELOPMENT]
				],
			],
			'isSuperadmin' => false,
		],
		self::RBAC => [
			'name' => 'SYSTEM',
			'desc' => 'ONLY FOR SUPERADMIN PACKAGE',
			'code' => 'rbac',
			'icon' => NULL,
			'permission' => [['slug' => 'rbac-module-enable', 'title' => 'ENABLE RBAC MODULE', 'remark' => '']],
			'status' => [self::DEVELOPMENT, self::PRODUCTION],
			'submodule' => [
				[
					'name' => 'Management',
					'desc' => NULL,
					'icon' => '<i class="ri-settings-5-line"></i>',
					'permission' => [
						['slug' => 'rbac-management-view', 'title' => 'VIEW MANAGEMENT (RBAC)', 'remark' => 'user can view menu Management for RBAC'],
					],
					'route' => 'management',
					'status' => [self::DEVELOPMENT, self::PRODUCTION],
				],
				[
					'name' => 'Settings',
					'desc' => NULL,
					'icon' => '<i class="ri-list-settings-line"></i>',
					'permission' => [
						['slug' => 'rbac-settings-view', 'title' => 'VIEW SETTINGS (RBAC)', 'remark' => 'user can view menu Settings for RBAC'],
					],
					'route' => 'rbac',
					'status' => [self::DEVELOPMENT, self::PRODUCTION],
				],
			],
			'isSuperadmin' => true,
		],
	];

	public const FREE = [
		self::GENERAL => [
			'name' => 'General',
			'desc' => NULL,
			'code' => 'gen',
			'icon' => NULL,
			'permission' => [['slug' => 'gen-module-enable', 'title' => 'ENABLE FREE MODULE', 'remark' => '']],
			'status' => [self::DEVELOPMENT, self::PRODUCTION],
			'submodule' => [
				[
					'name' => 'Dashboard',
					'desc' => NULL,
					'icon' => '<i class="ri-dashboard-3-line"></i>',
					'permission' => [
						['slug' => 'gen-dashboard-view', 'title' => 'VIEW HOMEPAGE/DAHSBOARD', 'remark' => '']
					],
					'route' => 'dashboard',
					'status' => [self::DEVELOPMENT, self::PRODUCTION]
				],
			],
			'isSuperadmin' => false,
		],
	];
}
