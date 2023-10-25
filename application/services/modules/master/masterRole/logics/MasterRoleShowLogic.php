<?php

namespace App\services\modules\master\masterRole\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\master\masterRole\processors\MasterRoleSearchProcessors;

class MasterRoleShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new MasterRoleSearchProcessors)->execute(
			[
				'fields' => 'role_name,role_code,role_scope,role_status',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}