<?php

namespace App\services\generals\traits;

trait QueryFilterRecordTrait
{
	public function newQuery($modelName, $filter = NULL)
	{
		model($modelName);

		$query = ci()->$modelName;

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
