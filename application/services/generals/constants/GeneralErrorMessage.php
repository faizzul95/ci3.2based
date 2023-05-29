<?php

namespace App\services\generals\constants;

final class GeneralErrorMessage
{
	public const LOGIN_DEFAULT = 'DEFAULT';
	public const LOGIN_RECAPTCHA = 'RECAPTCHA';
	public const LOGIN_ATTEMPT = 'ATTEMPT';
	public const LOGIN_VERIFY2FA = 'VERIFY2FA';
	public const LOGIN_INACTIVE = 'INACTIVE';
	public const FORGOT_PASSWORD = 'FORGOT';
	public const TOKEN_RESET_PASSWORD = 'TOKEN_RESET';
	public const EMAIL_NOT_VALID = 'EMAIL_NOT_VALID';
	public const SYSTEM_MAINTENANCE = 'MAINTENANCE';

	public const LIST = [
		'GENERAL' => [
			self::SYSTEM_MAINTENANCE => [
				'resCode' => 503,
				'message' => "System under maintenance",
				'redirectUrl' => NULL,
			],
		],
		'AUTH' => [
			self::LOGIN_DEFAULT => [
				'resCode' => 400,
				'message' => "Invalid username or password",
				'redirectUrl' => NULL,
			],
			self::LOGIN_RECAPTCHA => [
				'resCode' => 400,
				'message' => "Please verify that you're a human",
				'redirectUrl' => NULL,
			],
			self::LOGIN_ATTEMPT => [
				'resCode' => 400,
				'message' => "You have reached maximum number of login attempt. Your account has been suspended for 15 minutes.",
				'redirectUrl' => NULL,
			],
			self::LOGIN_VERIFY2FA => [
				'resCode' => 400,
				'message' => "Wrong code or code already expired",
				'redirectUrl' => NULL,
			],
			self::LOGIN_INACTIVE => [
				'resCode' => 400,
				'message' => "Your account is inactive, Please contact system support via ticket",
				'redirectUrl' => NULL,
			],
			self::FORGOT_PASSWORD => [
				'resCode' => 400,
				'message' => "Email sent unsuccessfully",
				'redirectUrl' => NULL,
			],
			self::EMAIL_NOT_VALID => [
				'resCode' => 400,
				'message' => "Email not found or not registered!",
				'redirectUrl' => NULL,
			],
			self::TOKEN_RESET_PASSWORD => [
				'code' => 400,
				'message' => "Please try again as the requested token either does not exist or has expired."
			]
		],
	];
}