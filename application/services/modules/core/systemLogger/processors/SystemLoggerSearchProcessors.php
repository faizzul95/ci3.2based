<?php

namespace App\services\modules\core\systemLogger\processors;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\generals\traits\QueryTrait;

class SystemLoggerSearchProcessors
{
    use QueryTrait;

    public function execute($filter = NULL, $fetchType = 'get_all', $cache_files_name = NULL)
    {
        $query = $this->newQuery('SystemLogger_model', $filter);

        if (hasData($filter)) {
            if (hasData($filter, 'searchQuery')) {
                $query->where('', 'like', $filter['searchQuery'])         // this will be LIKE $search
                    ->where('', 'like', $filter['searchQuery'], true);  // if put true, will be OR LIKE $search. else will be AND LIKE $search
            }
        }

        return $this->collectionRecord($query, $filter, $fetchType, $cache_files_name);
    }
}
