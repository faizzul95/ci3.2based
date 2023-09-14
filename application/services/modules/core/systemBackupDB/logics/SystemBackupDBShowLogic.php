<?php

namespace App\services\modules\core\systemBackupDB\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemBackupDB\processors\SystemBackupDBSearchProcessors;

class SystemBackupDBShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new SystemBackupDBSearchProcessors)->execute(
			[
				'fields' => '',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}