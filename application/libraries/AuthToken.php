<?php

namespace App\libraries;

defined('BASEPATH') or exit('No direct script access allowed');

use Exception;

use App\services\generals\constants\LoginType;
use App\services\modules\authentication\processors\UserSessionProcessor;
use App\services\modules\core\systemAccessTokens\processors\SystemAccessTokensSearchProcessors;
use App\services\modules\core\systemAccessTokens\processors\SystemAccessTokensStoreProcessors;

class AuthToken
{
    protected $CI;

    public function __construct()
    {
        $this->CI = ci();
    }

    public static function verification($token = null)
    {
        $ci = ci();
        $ci->load->config('jwt');

        // Get the current timestamp
        $timeRequest = time();

        // Get the token expiration time from the configuration or set a default value (86400 seconds = 24 hours)
        $tokenTimeout = $ci->config->item('token_expire_time') ?? 86400;

        try {
            if (!is_https()) {
                throw new \Exception('This API can only be accessed using an HTTPS connection');
            }

            if (!$token) {
                throw new \Exception('Token not found or provide');
            }

            // Fetch token data
            $tokenAccessData = app(new SystemAccessTokensSearchProcessors)->execute([
                'fields' => 'id,tokenable_id,name,abilities,last_used_at',
                'conditions' => [
                    'name' => 'Auth Token',
                    'token' => $token,
                ],
            ], 'get');

            if (!$tokenAccessData) {
                throw new \Exception('Invalid token');
            }

            // Get the last used timestamp from token data
            $lastUsed = strtotime(hasData($tokenAccessData, 'last_used_at', true, timestamp()));

            // Check if the token has expired
            if ($timeRequest >= ($lastUsed + $tokenTimeout)) {
                throw new \Exception('Token has expired');
            }

            // Calculate the time difference in seconds
            $timeDifference = ($lastUsed + $tokenTimeout) - $timeRequest;

            if ($timeDifference > 7200) {
                // Token is more than 2 hours old; refresh it
                $relogin = app(new UserSessionProcessor)->execute($tokenAccessData['tokenable_id'], LoginType::TOKEN);

                // Get the new token
                $token = $relogin['token'];
            }

            // Update the token's last used timestamp with the current time
            $tokenAccessData['last_used_at'] = timestamp();
            app(new SystemAccessTokensStoreProcessors)->execute($tokenAccessData);

            $data = ['status' => true, 'data' => $tokenAccessData['abilities'], 'token' => $token];
        } catch (\Exception $e) {
            $data = ['status' => false, 'message' => $e->getMessage(), 'data' => '', 'token' => $token];
        }

        return $data;
    }

    public static function verify($methodType = 'read')
    {
        $data = ['status' => false, 'data' => []];
        dd($data);

        // return $data;
    }
}
