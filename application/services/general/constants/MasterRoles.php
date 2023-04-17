<?php

namespace App\services\general\constants;

final class MasterRoles
{
	public const SUPERADMIN = 1;
	public const ADMIN = 2;

	public const LIST = [
		self::SUPERADMIN => 'SUPER ADMINISTRATOR',
		self::ADMIN => 'ADMINISTRATOR',
	];

	public const DISPLAY = [
		self::SUPERADMIN => '<span class="badge bg-success"> SUPER ADMINISTRATOR </span>',
		self::ADMIN => '<span class="badge bg-info"> ADMINISTRATOR </span>',
	];

	public const DIRECTORY = [
		self::SUPERADMIN => 'directory/superadmin',
		self::ADMIN => 'directory/admin',
	];
}
