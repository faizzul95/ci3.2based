<?php

namespace App\services\generals\constants;

final class TFAType
{
	public const DISABLED = 0;
	public const GA = 1;
	public const OTP = 2;
	public const EMAIL = 3;

	public const LIST = [
		self::DISABLED => NULL,
		self::GA => 'Google Authenticator',
		self::OTP => 'SMS (OTP)',
		self::EMAIL => 'Email',
	];

	public const VIA = [
		self::DISABLED => NULL,
		self::GA => 'via Google Authenticator',
		self::OTP => 'via SMS (OTP)',
		self::EMAIL => 'via Email',
	];
}
