<?php

namespace App\services\modules\core\users\processors;

use App\services\generals\traits\QueryTrait;

class UserDeleteProcessors
{
	use QueryTrait;

	public function execute($request = NULL)
	{
		$query = $this->newQuery('USER');
		return $query::remove($request);
	}
}
