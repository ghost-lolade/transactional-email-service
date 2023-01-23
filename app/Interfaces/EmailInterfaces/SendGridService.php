<?php

namespace App\Interfaces\EmailInterfaces;

use Exception;
use SendGrid as SendGrid;
use SendGrid\Mail\Mail as SendGridMail;
use SendGrid\Client;
use App\Interfaces\EmailInterfaces\Email;

class SendGridService implements Email
{
    /**
     * Send mail using Sendgrid service
     * @param array $data
     *
     * @return void
     * @throws Exception
    */
    public function send($data)
    {
        $email = new SendGridMail();
        $email->setFrom(config('mail.from.address'), config('mail.from.name'));
        $email->setSubject($data['subject']);
        $email->addTo($data['to']);
        $email->addContent("text/plain", $data['message']['text']);
        $email->addContent("text/html", $data['message']['html']);
        $sendgrid = new SendGrid(config('services.sendgrid.key'));

        try {
            $sendgrid->send($email);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
