<?php

namespace App\services\modules\authentication\logics;

use App\services\generals\constants\LoginType;
use App\services\generals\constants\GeneralErrorMessage;

use App\services\modules\authentication\processors\UserSessionProcessor;

class SocialliteLogic
{
    public function __construct()
    {
        model('User_model', 'userM');
    }

    public function logic($request, $loginType = LoginType::SOCIALITE)
    {
        // default response
        $responseData = GeneralErrorMessage::LIST['AUTH']['DEFAULT'];

        $email  = purify($request['email']);
        $dataUser = ci()->userM->getSpecificUser($email);

        if (hasData($dataUser)) {
            $userID = hasData($dataUser) ? $dataUser['id'] : NULL;
            $rememberme = purify($request['rememberme']);

            $responseData = app(new UserSessionProcessor)->execute($userID, $loginType, $rememberme);
        }

        return $responseData;
    }
}
