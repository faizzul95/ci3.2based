<?php

namespace App\services\modules\core\systemQueueFailedJob\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemQueueFailedJob\processors\SystemQueueFailedJobSearchProcessors;

class SystemQueueFailedJobShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new SystemQueueFailedJobSearchProcessors)->execute(
			[
				'fields' => '',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}