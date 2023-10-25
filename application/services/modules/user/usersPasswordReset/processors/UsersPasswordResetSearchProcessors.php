<?php

namespace App\services\modules\user\usersPasswordReset\processors;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\generals\traits\QueryTrait;

class UsersPasswordResetSearchProcessors
{
    use QueryTrait;

    public function execute($filter = NULL, $fetchType = 'get_all', $cache_files_name = NULL)
    {
        $query = $this->newQuery('UsersPasswordReset_model', $filter);

        if (hasData($filter)) {
            if (hasData($filter, 'searchQuery')) {
                $query->where('', 'like', $filter['searchQuery'])         // this will be LIKE $search
                      ->where('', 'like', $filter['searchQuery'], true);  // if put true, will be OR LIKE $search. else will be AND LIKE $search
            }
        }

        return $this->collectionRecord($query, $filter, $fetchType, $cache_files_name);
    }
}
