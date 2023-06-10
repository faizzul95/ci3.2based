<?php

namespace App\services\generals\traits;

use App\services\generals\constants\ModelDB;

trait QueryTrait
{
	public function newQuery($model, $filter = NULL)
	{
		$modelName = ModelDB::LIST[$model]['model'];
		$assignName = ModelDB::LIST[$model]['assign'];

		model($modelName, $assignName);

		$query = ci()->$assignName;

		if (hasData($filter)) {
			if (hasData($filter, 'fields')) {
				$query->fields($filter['fields']);
			}

			if (hasData($filter, 'with')) {
				$query->scopeWithQuery($query, $filter['with']);
			}

			if (hasData($filter, 'conditions')) {
				$query->scopeConditionQuery($query, $filter['conditions']);
			}
		}

		return $query;
	}
}
