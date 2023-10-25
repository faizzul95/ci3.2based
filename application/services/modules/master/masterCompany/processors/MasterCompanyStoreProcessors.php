<?php

namespace App\services\modules\master\masterCompany\processors;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\generals\traits\QueryTrait;

class MasterCompanyStoreProcessors
{
    use QueryTrait;

	protected $request = [];

	public function __construct()
	{
	}

	public function execute($request = NULL, $securityXss = true)
	{
		$query = $this->newQuery('MasterCompany_model');
		return $query::save(array_merge($this->request, $request), $securityXss);
	}
}
