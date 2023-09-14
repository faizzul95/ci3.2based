<?php

namespace App\services\modules\core\systemQueueJob\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemQueueJob\processors\SystemQueueJobSearchProcessors;

class SystemQueueJobShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new SystemQueueJobSearchProcessors)->execute(
			[
				'fields' => '',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}