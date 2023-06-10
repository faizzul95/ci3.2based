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
		self::ACTIVE => '<span class="badge badge-label bg-success"> Active </span>',
		self::INACTIVE => '<span class="badge badge-label bg-warning"> Inactive </span>',
		self::SUSPENDED => '<span class="badge badge-label bg-dark"> Suspended </span>',
		self::DELETED => '<span class="badge badge-label bg-danger"> Deleted </span>',
		self::UNVERIFIED => '<span class="badge badge-label bg-primary"> Unverified </span>',
		self::ENDED => '<span class="badge badge-label bg-danger"> Ended </span>',
	];

	//  QUEUE STATUS

	public const QUEUE_PENDING = 1;
	public const QUEUE_RUNNING = 2;
	public const QUEUE_COMPLETE = 3;
	public const QUEUE_FAIL = 4;

	public const QUEUE = [
		self::QUEUE_PENDING => [
			'name' => 'Pending',
			'badge' => '<span class="badge badge-label bg-warning"> Pending </span>',
		],
		self::QUEUE_RUNNING => [
			'name' => 'Running',
			'badge' => '<span class="badge badge-label bg-info"> Running </span>',
		],
		self::QUEUE_COMPLETE => [
			'name' => 'Completed',
			'badge' => '<span class="badge badge-label bg-success"> Completed </span>',
		],
		self::QUEUE_FAIL => [
			'name' => 'Failed',
			'badge' => '<span class="badge badge-label bg-danger"> Failed </span>',
		],
	];

	// MODULE STATUS

	public const ALL_STATUS = 'ALL';
	public const USER_STATUS = 'USER';
	public const QUEUE_STATUS = 'QUEUE';

	public const MODULE = [
		self::ALL_STATUS => [self::ACTIVE, self::INACTIVE, self::SUSPENDED, self::DELETED, self::UNVERIFIED, self::ENDED],
		self::USER_STATUS => [self::ACTIVE, self::INACTIVE, self::SUSPENDED, self::DELETED, self::UNVERIFIED],
		self::QUEUE_STATUS => [self::QUEUE_PENDING, self::QUEUE_RUNNING, self::QUEUE_COMPLETE, self::QUEUE_FAIL],
	];
}
