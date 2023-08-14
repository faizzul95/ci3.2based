Requirement : Remember to create MODEL (refer readme to create model using command/cli) first!

==============================================================================================

1- Folder : Logics
    a) use for business logic 
        i)   ShowLogic   - use to make a logic for single data
        ii)  StoreLogic  - use to make a logic for insert & update data
        iii) DeleteLogic - use to make a logic for delete data

2- Folder : Processors
    a) use for processing to database, save, delete, update
    b) Use prefix as below:-
        i)   ShowProcessor - to query single result by id or PK conditions
        ii)  StoreProcessor - to save (insert/update) data
        iii) SearchProcessor - to advanced query by specific column (define by filter). refer example below

                $filter = [
                    'fields' => '', <--- built in, string with coma (,) as separator
                    'conditions' => '', <--- built in, string or array
                    'limit' => [], <--- built in, string 
                    'hidden' => [], <--- built in, boolean = TRUE to return all data include hidden field, FALSE to exclude hidden field
                    'with' => [], <--- built in, array 
                    'min' => '', <--- built in, string or array 
                    'max' => '', <--- built in, string or array 
                    'sum' => '', <--- built in, string or array 
                    'searchQuery'=> '', <--- self-defined in searchProcessors (required)
                    'whereQuery', <--- self-defined in searchProcessors
                ];

                ----------------------------------------------------------------------

                EXAMPLE FOR USING CONDITION 

                // example for conditions using ARRAY
                $filter = [
                    ......
                    'conditions' => [
                        'date_start >=' => '2023-08-10',
                        'date_completed <' => '2023-08-14',
                    ],
                    ......
                ];

                // example for conditions using STRING
                $filter = [
                    ......
                    'conditions' => "date_start >= '2023-08-10' AND date_completed < '2023-08-14'",
                    ......
                ];

                ----------------------------------------------------------------------

                EXAMPLE FOR USING CONDITION "!=", ">", ">=" , "<", "<=" AND ANY SQL CONDITION EXCEPT "=" 

                // example for "!=" conditions
                $filter = [
                    ......
                    'conditions' => [
                        'id !=' => 2,
                    ],
                    ......
                ];

                // example for ">=" & "<" conditions
                $filter = [
                    ......
                    'conditions' => [
                        'date_start >=' => '2023-08-10',
                        'date_completed <' => '2023-08-14',
                    ],
                    ......
                ];

                ----------------------------------------------------------------------

                EXAMPLE FOR USING CONDITION "IN", "NOT IN", "BETWEEN" , "LIKE" 

                // example for IN conditions
                $filter = [
                    ......
                    'conditions' => [
                        'id' => ['IN', [1,2,3,4,5]],
                    ],
                    ......
                ];

                // example for NOT IN
                $filter = [
                    ......
                    'conditions' => [
                        'user_status' => ['NOT IN', [1,2,3,4]],
                    ],
                    ......
                ];

                // example for BETWEEN 
                // NOTE : VALUE 1 & VALUE 2 will auto detect which is min and max. can only have 2 values
                $filter = [
                    ......
                    'conditions' => [
                        'register_date' => ['BETWEEN', ['VALUE 1', 'VALUE 2']],
                    ],
                    ......
                ];

                // example for LIKE 
                // NOTE : 
                    1) 1st param is LIKE - required, 
                    2) 2nd param is value to search - required, 
                    3) 3rd param is pattern (only accept %a, %a%, a%) - optional, default is %a%

                $filter = [
                    ......
                    'conditions' => [
                        'username' => ['LIKE', 'TEST DATA', '%a'],
                    ],
                    ......
                ];

                ----------------------------------------------------------------------

                EXAMPLE 1 (SIMPLE WITH - WITHOUT CONDITION) :

                    $filter = [
                        'fields' => 'id,name,user_preferred_name,email,user_contact_no',
                        'conditions' => [
                            'id' => hasData($impersonateID) ? $impersonateID : $userID,
                        ],
                        'with' => ['main_profile'],
                    ]


                EXAMPLE 2 (SIMPLE WITH - INCLUDE CONDITION) :

                    $filter = [
                        'fields' => 'id,name,user_preferred_name,email,user_contact_no',
                        'conditions' => [
                            'id' => hasData($impersonateID) ? $impersonateID : $userID,
                        ],
                        'with' => [
                            'main_profile' => [
                                'fields' => 'id,user_id,roles_id,is_main,department_id,profile_status',
                                'conditions' => hasData($profileID) ? 'id=' . $profileID : 'is_main=1',
                            ]
                        ],
                    ]


                 EXAMPLE 3 (ADVANCED WITH - INCLUDE CONDITION) :

                    $filter = [
                        'fields' => 'id,name,user_preferred_name,email,user_contact_no',
                        'conditions' => [
                            'id' => hasData($impersonateID) ? $impersonateID : $userID,
                        ],
                        'with' => [
                            'main_profile' => [
                                'fields' => 'id,user_id,roles_id,is_main,department_id,profile_status',
                                'conditions' => hasData($profileID) ? 'id=' . $profileID : 'is_main=1',
                                'with' => [
                                    'roles' => ['fields' => 'role_name,role_code,role_group,abilities_json'],
                                    'department' => ['fields' => 'department_name,department_code'],
                                    'avatar' => [
                                        'fields' => 'files_compression,files_path,files_path_is_url,files_folder,entity_file_type',
                                        'conditions' => '`entity_file_type`=\'PROFILE_PHOTO\'',
                                    ],
                                    'profileHeader' => [
                                        'fields' => 'files_compression,files_path,files_path_is_url,files_folder,entity_file_type',
                                        'conditions' => '`entity_file_type`=\'PROFILE_HEADER_PHOTO\'',
                                    ]
                                ]
                            ]
                        ],
                    ]

                ----------------------------------------------------------------------

                Notes : This example can be use for MAX, MIN & SUM

                EXAMPLE USING MAX (STRING) : 

                    $filter = [
                        ......
                        'max' => 'user_gender' <-- this is column name, use for single column
                        ......
                    ]

                EXAMPLE USING MAX (ARRAY (NON-MULTI) - param1 : column name, param2 : alias_name) : 

                    $filter = [
                        ......
                        'max' => ['user_gender', 'gender'] <--- this is column name & alias for column name
                        ......
                    ]

                EXAMPLE USING MAX (ARRAY MULTI-DIMENSION - param1 : column name, param2 : alias_name) : 

                    $filter = [
                        ......
                        'max' => [['user_gender', 'gender'], ['user_wages', 'wages']] <-- if has multiple column use this instead (include alias)
                        ......
                    ]


==============================================================================================


// BASIC STRUCTURE FOR Logics


<?php

namespace App\services\modules\users\logics; <--- change

class UserShowLogic  <--- change
{
    public function __construct()
    {
    }

    public function logic($request)
    {
        
    }
}


==============================================================================================


// BASIC STRUCTURE FOR Processors


<?php

namespace App\services\modules\users\processors; <--- change

use App\services\generals\traits\QueryTrait;

class UserSearchProcessors <--- change
{
    use QueryTrait;

    public function execute($filter = NULL, $fetchType = 'get_all', $cache_files_name = NULL)
    {
        $query = $this->newQuery('<--- CHANGE TO MODEL CLASS NAME --->', $filter);

        if (hasData($filter)) {
            if (hasData($filter, 'searchQuery')) {
                $query->where('<--- CHANGE TO CoLUMN NAME --->', 'like', $filter['searchQuery'])           // this will be LIKE $search
                    ->where('<--- CHANGE TO CoLUMN NAME --->', 'like', $filter['searchQuery'], true);    // if put true, will be OR LIKE $search. else will be AND LIKE $search
            }
        }

        if (hasData($cache_files_name)) {
            $query->set_cache($cache_files_name);
        }

        return $fetchType == 'toSql' ? $query->$fetchType($query) : $query->$fetchType();
    }
}


==============================================================================================


ADDITIONAL NOTES FOR SearchProcessors : 

structure function : 
 
 execute($filter = NULL, $fetchType = 'get_all', $cache_files_name = NULL)

    1 - param1 = $filter - receive and array for query. default is NULL.
    2 - param2 = $fetchType (2nd param) only can receive value below :-
            a) get_all (default) - get all data, 
            b) get - get single data,
            c) count_rows - return total row
            d) toSql - return string of query
    3 - param3 = $cache_files_name - will cache the result. it will help to serve data more faster when cache is exist. default is NULL
