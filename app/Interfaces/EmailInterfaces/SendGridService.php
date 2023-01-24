<?php

namespace App\Interfaces\EmailInterfaces;

use App\Models\SentEmail;
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

            $response = $sendgrid->send($email);

            $this->logActivity($response->statusCode, "The mail was sent using ".self::class, $data);

            $this->updateSentEmailTable($data['id']);

            $this->logActivity(200, "The sent email table was updated successfully");

        } catch(Exception $e) {

            $this->logError($e->getCode(), $e->getMessage(), $e);

            throw $e;
        }
    }

    /**
     * Update sent email table service column
     * @param int $id
     *
     * @return void
    */
    public function updateSentEmailTable($id): void
    {
        SentEmail::where('id', $id)->update(['service'  => self::class]);
    }

    /**
     * Logs email activity
     * @param int $statusCode
     * @param string $message
     * @param array $data
     *
     * @return void
    */
    public function logActivity(int $statusCode = null, string $message, $data = []): void
    {
        log_activity($statusCode, $message, $data);
    }

    /**
     * Logs email errors
     * @param int $statusCode
     * @param string $message
     * @param Exception $exception
     *
     * @return void
    */
    public function logError(int $statusCode, string $message, Exception $exception): void
    {
        log_error($statusCode, $message, $exception);
    }
}
