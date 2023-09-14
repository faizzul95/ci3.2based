<?php

namespace App\services\modules\core\systemQueueJob\processors;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\generals\traits\QueryTrait;

class SystemQueueJobStoreProcessors
{
    use QueryTrait;

	protected $request = [];

	public function __construct()
	{
	}

	public function execute($request = NULL)
	{
		$query = $this->newQuery('SystemQueueJob_model');
		return $query::save(array_merge($this->request, $request));
	}
}
