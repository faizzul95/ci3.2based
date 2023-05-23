<?php

namespace App\services\modules\authentication\logics;

use App\services\generals\constants\LoginType;
use App\services\generals\constants\GeneralErrorMessage;

use App\services\modules\authentication\processors\UserSessionProcessor;

class TwoFactorAuthenticateLogic
{
    public function __construct()
    {
        model('User_model', 'userM');
        model('UserAuthAttempt_model', 'attemptM');

        library('recaptcha');
        library('user_agent');
    }

    public function execute($request, $loginType = LoginType::CREDENTIAL)
    {
        $username  = purify($request['username']);
        $codeEnter  = purify($request['code']);
        $rememberme  = purify($request['rememberme']);

        $dataUser = ci()->userM->getSpecificUser($username);

        if (!empty($dataUser)) {
            $userID = hasData($dataUser) ? $dataUser['id'] : NULL;
            $codeSecret = $dataUser['two_factor_secret'];

            if (verifyGA($codeSecret, $codeEnter)) {
                $responseData = app(new UserSessionProcessor)->execute($userID, $loginType, $rememberme);
            } else {
                $responseData = GeneralErrorMessage::LIST['AUTH']['VERIFY2FA'];
            }
        } else {
            $responseData = GeneralErrorMessage::LIST['AUTH']['DEFAULT'];
        }

        return $responseData;
    }
}
