<?php

namespace App\services\modules\core\systemAccessTokens\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemAccessTokens\processors\SystemAccessTokensSearchProcessors;

class SystemAccessTokensShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new SystemAccessTokensSearchProcessors)->execute(
			[
				'fields' => '',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}