<?php

namespace App\services\modules\user\users\processors;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\generals\traits\QueryTrait;

class UsersSearchProcessors
{
    use QueryTrait;

    public function execute($filter = NULL, $fetchType = 'get_all', $cache_files_name = NULL)
    {
        $query = $this->newQuery('Users_model', $filter);

        if (hasData($filter)) {
            // use for login only
            if (hasData($filter, 'whereQuery')) {
                $query->where('id', $filter['whereQuery'])             // this will be WHERE $search
                    ->where('email', $filter['whereQuery'], NULL, true)      // if put true, will be OR WHERE $search. else will be AND WHERE $search
                    ->where('username', $filter['whereQuery'], NULL, true);  // if put true, will be OR WHERE $search. else will be AND WHERE $search
            }

            if (hasData($filter, 'searchQuery')) {
                $query->where('name', 'like', $filter['searchQuery'])               // this will be LIKE $search
                    ->where('email', 'like', $filter['searchQuery'], true)          // if put true, will be OR LIKE $search. else will be AND LIKE $search
                    ->where('user_matric_code', 'like', $filter['searchQuery'], true); // if put true, will be OR LIKE $search. else will be AND LIKE $search
            }
        }

        return $this->collectionRecord($query, $filter, $fetchType, $cache_files_name);
    }
}
