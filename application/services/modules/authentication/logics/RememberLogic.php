<?php

namespace App\services\modules\authentication\logics;

use App\services\general\constants\LoginType;
use App\services\modules\authentication\processors\UserSessionProcessor;

class RememberLogic
{
    public function __construct()
    {
        model('User_model', 'userM');
    }

    public function execute()
    {
        $token = get_cookie('remember_me_token_cipmo');

        // check if token cookie is exist in browsers
        if (hasData($token)) {
            $dataUser = ci()->userM::find($token, 'remember_token');

            if ($dataUser) {
                $response = app(new UserSessionProcessor)->execute($dataUser['id'], LoginType::TOKEN, true);

                if (isSuccess($response['resCode']))
                    redirect($response['redirectUrl'], true);
            }
        }
    }
}
