<?php

namespace App\libraries\CI_WebSocket\Helpers;

use App\libraries\CI_WebSocket\Helpers\JWT;

#[\AllowDynamicProperties]
class AUTHORIZATION
{
	public static function validateTimestamp($token)
	{
		$token = self::validateToken($token);
		if ($token != false && (now() - $token->timestamp < (env('WEBSOCKET_JWT_TIMEOUT') * 60))) {
			return $token;
		}
		return false;
	}

	public static function validateToken($token)
	{
		return JWT::decode($token, env('WEBSOCKET_JWT_KEY'));
	}

	public static function generateToken($data)
	{
		return JWT::encode($data, env('WEBSOCKET_JWT_KEY'));
	}
}
