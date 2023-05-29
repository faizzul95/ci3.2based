<?php

namespace App\services\modules\core\users\processors;

class UsersSearchProcessors
{
    public function __construct()
    {
        model('User_model', 'userM');
    }

    public function execute($filter = NULL, $fetchType = 'get_all')
    {
        $query = ci()->userM;

        if (hasData($filter)) {

            if (hasData($filter, 'fields')) {
                $query->fields($filter['fields']);
            }

            if (hasData($filter, 'with')) {
                $query->scopeWithQuery($query, $filter['with']);
            }

            // use for login only
            if (hasData($filter, 'whereQuery')) {
                $query->where('id', 'like', $filter['whereQuery'])             // this will be WHERE $search
                    ->where('email', 'like', $filter['whereQuery'], true)      // if put true, will be OR WHERE $search. else will be AND WHERE $search
                    ->where('username', 'like', $filter['whereQuery'], true);  // if put true, will be OR WHERE $search. else will be AND WHERE $search
            }

            if (hasData($filter, 'searchQuery')) {
                $query->where('name', 'like', $filter['searchQuery'])               // this will be LIKE $search
                    ->where('email', 'like', $filter['searchQuery'], true)          // if put true, will be OR LIKE $search. else will be AND LIKE $search
                    ->where('user_nric_visa', 'like', $filter['searchQuery'], true) // if put true, will be OR LIKE $search. else will be AND LIKE $search
                    ->where('user_staff_no', 'like', $filter['searchQuery'], true); // if put true, will be OR LIKE $search. else will be AND LIKE $search
            }

            if (hasData($filter, 'conditions')) {
                $query->scopeConditionQuery($query, $filter['conditions']);
            }
        }

        return $query->$fetchType();
    }
}
