<?php

namespace App\services\generals\constants;

final class DefaultEmailTemplate
{
    public const SECURE_LOGIN = 'SECURE_LOGIN';
    public const FORGOT_PASSWORD = 'FORGOT_PASSWORD';
    public const TICKET_CREATED = 'TICKET_CREATED';
    public const TICKET_CLOSED = 'TICKET_CLOSED';

    public const TEMPLATE = [
        'LOGIN' => [
            self::SECURE_LOGIN => [
                'email_subject'   => 'APP_NAME : Secure Login',
                'email_body'      => 'Hi %name%,
                                    <br><br>
                                    Your account <b>%email%</b> was just used to sign in from <b>%browsers% on %os%</b>.
                                    <br><br>
                                    %details%
                                    <br><br>
                                    Don\'t recognise this activity?
                                    <br>
                                    Secure your account, from this link.
                                    <br>
                                    <a href="%url%"><b>Login.</b></a>
                                    <br><br>
                                    Why are we sending this?<br>We take security very seriously and we want to keep you in the loop on important actions in your account.
                                    <br><br>
                                    Sincerely,<br>
                                    APP_NAME',
                'email_footer'    => NULL,
                'email_cc'        => NULL,
                'email_bcc'       => NULL,
            ],
            self::FORGOT_PASSWORD => [
                'email_subject'     => 'APP_NAME : Forgot Password',
                'email_body'        => '<table class="body-wrap" style="background-color: transparent; color: var(--vz-body-color); font-weight: var(--vz-body-font-weight); text-align: var(--vz-body-text-align); font-family: Roboto, sans-serif; font-size: 14px; width: 100%; margin: 0px;"><tbody><tr style="margin: 0px;"><td style="vertical-align: top; margin: 0px;" valign="top"></td><td class="container" width="600" style="vertical-align: top; margin-top: 0px; margin-bottom: 0px; display: block !important; max-width: 600px !important; clear: both !important;" valign="top"><div class="content" style="max-width: 600px; margin: 0px auto; padding: 20px;"><table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope="" itemtype="http://schema.org/ConfirmAction" style="border-radius: 3px; margin: 0px; border: none;"><tbody><tr style="margin: 0px;"><td class="content-wrap" style="color: rgb(73, 80, 87); vertical-align: top; margin: 0px; padding: 30px; box-shadow: rgba(30, 32, 37, 0.06) 0px 3px 15px; border-radius: 7px;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" style="margin: 0px;"><tbody><tr style="margin: 0px;"><td class="content-block" style="vertical-align: top; margin: 0px; padding: 0px 0px 20px;" valign="top"><div style="text-align: center;"><br></div></td></tr><tr style="margin: 0px;"><td class="content-block" style="font-size: 24px; vertical-align: top; margin: 0px; padding: 0px 0px 10px; text-align: center;" valign="top"><h4 style="font-family: Roboto, sans-serif; margin-bottom: 0px; line-height: 1.5;">Change or reset your password</h4></td></tr><tr style="margin: 0px;"><td class="content-block" style="color: rgb(135, 138, 153); font-size: 15px; vertical-align: top; margin: 0px; padding: 0px 0px 12px; text-align: center;" valign="top"><p style="margin-bottom: 13px; line-height: 1.5;"><span style="font-weight: var(--vz-body-font-weight);">Dear</span><b> %to%</b><span style="font-weight: var(--vz-body-font-weight);">,&nbsp;</span></p><p style="margin-bottom: 13px; line-height: 1.5;">You can reset&nbsp;your password by clicking the button below.&nbsp;</p></td></tr><tr style="margin: 0px;"><td class="content-block" itemprop="handler" itemscope="" itemtype="http://schema.org/HttpActionHandler" style="vertical-align: top; margin: 0px; padding: 0px 0px 22px; text-align: center;" valign="top"><a href="%url%" itemprop="url" style="font-size: 0.8125rem; color: rgb(255, 255, 255); cursor: pointer; display: inline-block; border-radius: 0.25rem; text-transform: capitalize; background-color: rgb(64, 81, 137); margin: 0px; border-color: rgb(64, 81, 137); border-style: solid; border-width: 1px; padding: 0.5rem 0.9rem;">Reset Password</a></td></tr></tbody></table></td></tr></tbody></table><div style="text-align: center; margin: 28px auto 0px auto;"><h4>Need Help ?</h4><p style="color: #878a99;">Please send any feedback or bug info to <a href="info@APP_DOMAIN" target="_blank">info@APP_DOMAIN</a></p><p style="color: rgb(152, 166, 173); margin-right: 0px; margin-bottom: 0px; margin-left: 0px;">2023 APP_NAME. Developed by COMPANY_NAME</p></div></div></td></tr></tbody></table>',
                'email_footer'      => NULL,
                'email_cc'          => NULL,
                'email_bcc'         => NULL,
            ],
        ],
        'TICKET' => [
            self::TICKET_CREATED => [
                'email_subject'     => 'APP_NAME : Ticket Received',
                'email_body'        => '<p>Dear %to%,</p>
                                    <p> We would like to acknowledge that we have received your request and a ticket has been created. A support representative will be reviewing your request and will send you a personal response. (usually within %hours% hours). </p> 
                                    <p> To view the status of the ticket or add comments, please visit your client area at our website. You may also request urgent follow up via live chat agent at %url%. </p> 
                                    <p> Thank you for your patience.</p>
                                    Sincerely.<br> APP_NAME',
                'email_footer'      => NULL,
                'email_cc'          => NULL,
                'email_bcc'         => NULL,
            ],
            self::TICKET_CLOSED => [
                'email_subject'     => 'APP_NAME : Ticket Closes',
                'email_body'        => '<p>Dear %to%,</p>
                                        <p> Your ticket %subject% has been closed. </p> 
                                        <p> We hope that the ticket was resolved to your satisfaction. If you feel that the ticket should not be closed or if the ticket has not been resolved, please reply to this email. </p> 
                                        <p> You may also request urgent follow up via live chat agent at %url% if still required.</p>
                                        Sincerely.<br> APP_NAME',
                'email_footer'      => NULL,
                'email_cc'          => NULL,
                'email_bcc'         => NULL,
            ]
        ]
    ];
}
