<?php

namespace App\services\generals\constants;

final class ModelDB
{
	public const USER = 'USER';
	public const PROFILE = 'PROFILE';
	public const FILES = 'FILES';

	public const LIST = [
		self::USER => [
			'model' => "User_model",
			'assign' => "userM",
		],
		self::PROFILE => [
			'model' => "UserProfile_model",
			'assign' => "profileM",
		],
		self::FILES => [
			'model' => "EntityFiles_model",
			'assign' => "filesM",
		],
	];
}
