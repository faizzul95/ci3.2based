<?php

namespace App\services\generals\constants;

final class GeneralStatus
{
	public const INACTIVE = 0;
	public const ACTIVE = 1;
	public const SUSPENDED = 2;
	public const DELETED = 3;
	public const UNVERIFIED = 4;
	public const ENDED = 5;

	public const LIST = [
		self::ACTIVE => 'Active',
		self::INACTIVE => 'Inactive',
		self::SUSPENDED => 'Suspended',
		self::DELETED => 'Deleted',
		self::UNVERIFIED => 'Unverified',
		self::ENDED => 'Ended',
	];

	public const BADGE = [
		self::ACTIVE => '<span class="badge bg-label-success"> Active </span>',
		self::INACTIVE => '<span class="badge bg-label-warning"> Inactive </span>',
		self::SUSPENDED => '<span class="badge bg-label-dark"> Suspended </span>',
		self::DELETED => '<span class="badge bg-label-danger"> Deleted </span>',
		self::UNVERIFIED => '<span class="badge bg-label-primary"> Unverified </span>',
		self::ENDED => '<span class="badge bg-label-danger"> Ended </span>',
	];
}
