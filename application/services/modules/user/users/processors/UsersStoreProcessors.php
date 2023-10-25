<?php

namespace App\services\modules\user\users\processors;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\generals\traits\QueryTrait;

class UsersStoreProcessors
{
    use QueryTrait;

	protected $request = [];

	public function __construct()
	{
	}

	public function execute($request = NULL, $securityXss = true)
	{
		$query = $this->newQuery('Users_model');
		return $query::save(array_merge($this->request, $request), $securityXss);
	}
}
