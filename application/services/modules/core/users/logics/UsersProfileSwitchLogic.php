<?php

namespace App\services\modules\core\users\logics;

use App\services\generals\constants\LoginType;
use App\services\modules\authentication\processors\UserSessionProcessor;

class UsersProfileSwitchLogic
{
    public function __construct()
    {
        model('User_model', 'userM');
    }

    public function execute($request)
    {
        $profileID = purify($request['profile_id']);
        $userID = purify($request['user_id']);

        // get cookie remember
        $token = get_cookie('remember_me_token_cipmo');
        $remember = hasData($token) ? true : false; // check if remember cookie is exist

        return app(new UserSessionProcessor)->execute($userID, NULL, $remember, $profileID);
    }
}
