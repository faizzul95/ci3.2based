<?php

namespace App\services\general\constants;

final class TicketStatus
{
	public const NEW = 0;
	public const ASSIGNED = 1;
	public const ATTEND = 2;
	public const SOLVED = 3;
	public const UNRESOLVED = 4;
	public const CLOSED = 5;
	public const REOPEN = 6;

	public const LOW = 0;
	public const NORMAL = 1;
	public const MEDIUM = 2;
	public const HIGH = 3;
	public const CRITICAL = 4;

	public const CUSTOMER = [
		self::NEW => [
			'name' => 'Submitted',
			'badge' => '<span class="badge bg-label-info"> Submitted </span>',
		],
		self::ASSIGNED => [
			'name' => 'In Review',
			'badge' => '<span class="badge bg-label-secondary"> In Review </span>',
		],
		self::ATTEND => [
			'name' => 'In Progress',
			'badge' => '<span class="badge bg-label-primary"> In Progress </span>',
		],
		self::SOLVED => [
			'name' => 'Solved',
			'badge' => '<span class="badge bg-label-success"> Solved </span>',
		],
		self::UNRESOLVED => [
			'name' => 'Unresolved',
			'badge' => '<span class="badge bg-label-dark"> Unresolved </span>',
		],
		self::CLOSED => [
			'name' => 'Closed',
			'badge' => '<span class="badge bg-success"> Closed </span>',
		],
		self::REOPEN => [
			'name' => 'Re-open',
			'badge' => '<span class="badge rounded-pill bg-label-info"> Re-open </span>',
		],
	];

	public const ADMIN = [
		self::NEW => [
			'name' => 'Submitted',
			'badge' => '<span class="badge bg-label-info"> New </span>',
		],
		self::ASSIGNED => [
			'name' => 'Assigned',
			'badge' => '<span class="badge bg-label-secondary"> Assigned </span>',
		],
		self::ATTEND => [
			'name' => 'Attend',
			'badge' => '<span class="badge bg-label-primary"> Attend </span>',
		],
		self::SOLVED => [
			'name' => 'Solved',
			'badge' => '<span class="badge bg-label-success"> Solved </span>',
		],
		self::UNRESOLVED => [
			'name' => 'Unresolved',
			'badge' => '<span class="badge bg-label-dark"> Unresolved </span>',
		],
		self::CLOSED => [
			'name' => 'Closed',
			'badge' => '<span class="badge bg-success"> Closed </span>',
		],
		self::REOPEN => [
			'name' => 'Re-open',
			'badge' => '<span class="badge rounded-pill bg-label-info"> Re-open </span>',
		],
	];

	public const PRIORITY = [
		self::LOW => [
			'name' => 'Low',
			'badge' => '<span class="badge rounded-pill bg-label-success"> Low </span>',
		],
		self::NORMAL => [
			'name' => 'Normal',
			'badge' => '<span class="badge rounded-pill bg-label-info"> Normal </span>',
		],
		self::MEDIUM => [
			'name' => 'Medium',
			'badge' => '<span class="badge rounded-pill bg-label-warning"> Medium </span>',
		],
		self::HIGH => [
			'name' => 'High',
			'badge' => '<span class="badge rounded-pill bg-label-danger"> High </span>',
		],
		self::CRITICAL => [
			'name' => 'Critical',
			'badge' => '<span class="badge rounded-pill bg-danger"> Critical </span>',
		],
	];
}
