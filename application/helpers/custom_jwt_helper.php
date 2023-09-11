<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Generate a JWT token with a specified payload and timeout.
 *
 * @param array $payload The data to be included in the token.
 * @param array $expire The time data to be timeout.
 * @return string The generated JWT token.
 */
if (!function_exists('generate_jwt_token')) {
    function generate_jwt_token($payload = NULL, $expire = NULL)
    {
        $ci = ci();
        $ci->load->config('jwt');

        $timeRequest = time();
        $jwtTimeout = empty($expire) ? $ci->config->item('token_expire_time') : $expire;

        $token = array(
            'iss' => base_url(), // Issuer of the token
            'iat' => $timeRequest, // Issued at
            'exp' => $timeRequest + $jwtTimeout, // Expiration time
            'data' => $payload // Data payload
        );

        return JWT::encode($token, $ci->config->item('jwt_key'), $ci->config->item('jwt_algorithm'));
    }
}

/**
 * Validate a JWT token.
 *
 * @param string $token The JWT token to validate.
 * @return mixed The decoded payload if the token is valid, or false if it's invalid.
 */
if (!function_exists('validate_jwt_token')) {
    function validate_jwt_token($token)
    {
        $ci = ci();
        $ci->load->config('jwt');

        try {
            $decodedToken = JWT::decode($token, new Key($ci->config->item('jwt_key'), $ci->config->item('jwt_algorithm')));

            if ($decodedToken->exp > time()) {
                
                // check user data
                $verify = userJWTCredentials($decodedToken->data);

                $code = $verify['status'] ? 200 : 401;
                $message = $verify['status'] ? 'Token verified' : 'Unauthorized token credentials';

                return ['code' => $code, 'message' => $message, 'data' => $verify['data'], 'token' => generate_jwt_token($decodedToken->data)];
            } else {
                return ['code' => 401, 'message' => 'Token has expired', 'data' => []]; // Token has expired
            }
        } catch (Exception $e) {
            log_message('error', "JWT ERROR : " . $e->getMessage());
            return ['code' => 400, 'message' => 'Token validation failed', 'data' => []]; // JWT validation failed
        }
    }
}