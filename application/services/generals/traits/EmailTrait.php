<?php

namespace App\services\generals\traits;

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

    public function notify($dataRecipient, $bodyMessage = NULL, $template = NULL)
    {
        $recipientData = [
            'recipient_name' => $dataRecipient['name'],
            'recipient_email' => $dataRecipient['email'],
            'recipient_cc' => $template['email_cc'],
            'recipient_bcc' => $template['email_bcc'],
        ];

        return sentMail($recipientData, $template['email_subject'], $bodyMessage);
    }

    public function queueMail($data)
    {
        $recipientData = [
            'recipient_name' => $data['name'],
            'recipient_email' => $data['to'],
            'recipient_cc' => $data['cc'],
            'recipient_bcc' => $data['bcc'],
        ];

        $sentEmail = sentMail($recipientData, $data['subject'], $data['body'], $data['attachment']);

        if ($sentEmail['success']) {
            return ['status' => 3, 'message' => $sentEmail['message']];
        } else {
            return ['status' => 4, 'message' => 'Failed to sent at ' . timestamp('d/m/Y h:i A') . ', ' . $sentEmail['message']];
        }
    }
}
