<?php

namespace App\services\modules\core\users\submodules\profiles\processors;

use App\services\generals\traits\QueryTrait;

class ProfileDeleteProcessors
{
	use QueryTrait;

	public function execute($request = NULL)
	{
		$query = $this->newQuery('PROFILE');
		return $query::remove($request);
	}
}