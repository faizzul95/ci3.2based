<?php

namespace App\services\generals\constants;

final class MasterGroupRoles
{
	public const SUPERADMIN = 1;
	public const DEMO = 2;
	public const SUPPORT_TICKET = 3;

	public const LIST = [
		self::SUPERADMIN => [
			'name' => 'Super Administrator',
			'code' => 'SUPER',
			'badge' => '<span class="badge bg-success"> Super Administrator </span>',
		],
		self::DEMO => [
			'name' => 'Demo',
			'code' => 'DEMO',
			'badge' => '<span class="badge bg-success"> Demo </span>',
		],
		self::SUPPORT_TICKET => [
			'name' => 'Support (Ticketing)',
			'code' => 'SUPPORT',
			'badge' => '<span class="badge bg-success"> Support (Ticketing) </span>',
		],
	];
}
