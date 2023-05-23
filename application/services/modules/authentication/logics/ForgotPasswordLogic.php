<?php

namespace App\services\modules\authentication\logics;

use App\services\generals\constants\GeneralErrorMessage;

class ForgotPasswordLogic
{
    public function __construct()
    {
        model('User_model', 'userM');
        model('UserPasswordReset_model', 'resetM');
        model('CompanyConfigEmailTemplate_model', 'templateM');
        model('SystemQueueJob_model', 'queueM');

        library('recaptcha');
        library('user_agent');
    }

    public function sent($request)
    {
        $email  = $request['email'];

        $validateRecaptcha = recaptchav2();

        // Check with recaptcha first
        if ($validateRecaptcha['success']) {
            // query data user by email
            $dataUser = ci()->userM->getSpecificUser($email);

            // check if data user is exist
            if (hasData($dataUser)) {

                $companyID = $dataUser['company_id'];

                $token = $dataUser['id'] . bin2hex(random_bytes(20));
                $resetPassData = ci()->resetM::save([
                    'user_id' => $dataUser['id'],
                    'email' => $dataUser['email'],
                    'reset_token' => $token,
                    'reset_token_expired' => date('Y-m-d H:i:s', strtotime(timestamp() . ' + 30 minutes'))
                ]);

                if (isSuccess($resetPassData['resCode'])) {
                    $url = 'auth/reset-password/' . $token;
                    $template = ci()->templateM->where('email_type', 'FORGOT_PASSWORD')->where('email_status', '1')->where('company_id', $companyID)->get();

                    if (hasData($template)) {
                        $bodyMessage = replaceTextWithData($template['email_body'], [
                            'to' => $dataUser['name'],
                            'url' => url($url)
                        ]);

                        // Testing Using trait (use phpmailer)
                        // $this->testSentEmail($dataUser, $bodyMessage, $template);

                        // add to queue
                        $saveQueue = ci()->queueM::save([
                            'queue_uuid' => uuid(),
                            'type' => 'email',
                            'payload' => json_encode([
                                'name' => $dataUser['name'],
                                'to' => $email,
                                'cc' => $template['email_cc'],
                                'bcc' => $template['email_bcc'],
                                'subject' => $template['email_subject'],
                                'body' => $bodyMessage,
                                'attachment' => NULL,
                            ])
                        ], false);

                        if (isSuccess($saveQueue['resCode'])) {
                            $responseData = [
                                'resCode' => 200,
                                'message' => 'Email has been sent',
                                'redirectUrl' => url(''),
                            ];
                        } else {
                            $responseData = GeneralErrorMessage::LIST['AUTH']['FORGOT'];
                        }
                    } else {
                        $responseData = GeneralErrorMessage::LIST['AUTH']['FORGOT'];
                    }
                } else {
                    $responseData = GeneralErrorMessage::LIST['AUTH']['FORGOT'];
                }
            } else {
                $responseData = GeneralErrorMessage::LIST['AUTH']['EMAIL_NOT_VALID'];
            }
        } else {
            $responseData = GeneralErrorMessage::LIST['AUTH']['RECAPTCHA'];
            $responseData["message"] = filter_var(env('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ? $validateRecaptcha['error_message'] : "Please verify that you're a human";
        }

        return $responseData;
    }

    public function form($request)
    {
        // query data reset by token
        $dataReset = ci()->resetM::find($request, 'reset_token');

        // check if data reset is exist
        if (hasData($dataReset)) {
            // check if token is expired
            if ($dataReset['reset_token_expired'] > timestamp()) {
                render('auth/reset',  [
                    'title' => 'Reset Password',
                    'currentSidebar' => 'auth',
                    'currentSubSidebar' => 'reset',
                    'data' => $dataReset
                ]);
            } else {
                json(GeneralErrorMessage::LIST['AUTH']['TOKEN_RESET']);
            }
        } else {
            json(GeneralErrorMessage::LIST['AUTH']['TOKEN_RESET']);
        }
    }
}
