<?php

namespace App\services\modules\core\systemLogger\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemLogger\processors\SystemLoggerSearchProcessors;

class SystemLoggerShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new SystemLoggerSearchProcessors)->execute(
			[
				'fields' => 'errno,errtype,errstr,errfile,errline,user_agent,ip_address,time',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}