<?php

namespace App\Interfaces\EmailInterfaces;

use Exception;
use Mailjet\Client;
use Mailjet\Resources;
use App\Models\SentEmail;
use App\Traits\HasApiResponse;
use App\Interfaces\EmailInterfaces\Email;
use Mailjet\LaravelMailjet\Contracts\CampaignDraftContract;

class MailJetService implements Email
{
    use HasApiResponse;
    /**
     * Send mail using Mailjet service
     * @param array $data
     *
     * @return void
     * @throws Exception
    */
    public function send(array $data): void
    {
        $mailJet = new Client(config('services.mailjet.key'), config('services.mailjet.secret'), true);

        $params = [
            "method" => "POST",
            "FromEmail" => config('mail.from.address'),
            "FromName" => config('mail.from.name'),
            "Subject" => $data['subject'],
            "Text-part" => $data['message']['text'],
            "Html-part" => $data['message']['html'],
            "To" => $data['to']
        ];

        try {

            $response = $mailJet->post(Resources::$Email, ['body' => $params]);

            $this->logActivity($response->getStatus(), "The mail was sent using ".self::class, $response->getData());

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
