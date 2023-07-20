<?php

namespace App\libraries\WebSocket\Helpers;

use App\libraries\WebSocket\Helpers\JWT;

#[\AllowDynamicProperties]
class AUTHORIZATION
{
	public static function validateTimestamp($token)
	{
		$CI = &get_instance();
		$CI->load->config('jwt');

		$token = self::validateToken($token);
		if ($token != false && (now() - $token->timestamp < ($CI->config->item('token_timeout') * 60))) {
			return $token;
		}
		return false;
	}

	public static function validateToken($token)
	{
		$CI = &get_instance();
		$CI->load->config('jwt');

		return JWT::decode($token, $CI->config->item('jwt_key'));
	}

	public static function generateToken($data)
	{
		$CI = &get_instance();
		$CI->load->config('jwt');

		return JWT::encode($data, $CI->config->item('jwt_key'));
	}
}
