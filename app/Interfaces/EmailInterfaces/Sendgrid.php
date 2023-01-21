<?php

namespace App\Interfaces\EmailInterfaces;

use Exception;
use SendGrid as SendGridService;
use SendGrid\Mail\Mail as SendGridMail;
use App\Interfaces\EmailInterfaces\Email;

class SendGrid implements Email
{
    public function send($data)
    {
        $email = new SendGridMail();
        $email->setFrom(config('mail.from.address'), config('mail.from.name'));
        $email->setSubject($data['subject']);
        $email->addTo($data['to']);
        $email->addContent("text/plain", $data['message']['text']);
        $email->addContent("text/html", $data['message']['html']);
        $sendgrid = new SendGridService(env('SENDGRID_API_KEY'));

        try {
            $sendgrid->send($email);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
