<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('generate_jwt_token')) {
    /**
     * Generate a JWT token with a specified payload and timeout.
     *
     * @param array $payload The data to be included in the token.
     * @return string The generated JWT token.
     */
    function generate_jwt_token($payload = NULL)
    {
        $ci = ci();
        $ci->load->config('jwt');

        $timeRequest = time();
        $jwtTimeout = $ci->config->item('token_expire_time');

        $token = array(
            'iss' => base_url(), // Issuer of the token
            'iat' => $timeRequest, // Issued at
            'exp' => $timeRequest + $jwtTimeout, // Expiration time
            'data' => $payload // Data payload
        );

        return JWT::encode($token, $ci->config->item('jwt_key'), $ci->config->item('jwt_algorithm'));
    }
}

if (!function_exists('validate_jwt_token')) {
    /**
     * Validate a JWT token.
     *
     * @param string $token The JWT token to validate.
     * @return mixed The decoded payload if the token is valid, or false if it's invalid.
     */
    function validate_jwt_token($token)
    {
        $ci = ci();
        $ci->load->config('jwt');

        try {
            $decodedToken = JWT::decode($token, new Key($ci->config->item('jwt_key'), $ci->config->item('jwt_algorithm')));

            if ($decodedToken->exp > time()) {
                return $decodedToken->data;   // Token is valid and has not expired
            } else {
                return false; // Token has expired
            }
        } catch (Exception $e) {
            return false;  // JWT validation failed
        }
    }
}
