<?php

namespace App\services\generals\traits;

trait QueryTrait
{
	public function newQuery($model, $filter = NULL)
	{
		model($model);

		$query = ci()->$model;

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
