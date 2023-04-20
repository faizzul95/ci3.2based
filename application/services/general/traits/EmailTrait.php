<?php

namespace App\services\general\traits;

trait EmailTrait
{
    public function testSentEmail($dataRecipient, $bodyMessage = NULL, $template = NULL)
    {
        $recipientData = [
            'recipient_name' => $dataRecipient['name'],
            'recipient_email' => $dataRecipient['email'],
            'recipient_cc' => $template['email_cc'],
            'recipient_bcc' => $template['email_bcc'],
        ];

        return sentMail($recipientData, $template['email_subject'], $bodyMessage);
    }
}
