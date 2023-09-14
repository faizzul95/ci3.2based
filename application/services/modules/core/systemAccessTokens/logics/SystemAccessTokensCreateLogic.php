<?php

namespace App\services\modules\core\systemAccessTokens\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemAccessTokens\processors\SystemAccessTokensStoreProcessors;

class SystemAccessTokensCreateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new SystemAccessTokensStoreProcessors)->execute($request);
    }
}