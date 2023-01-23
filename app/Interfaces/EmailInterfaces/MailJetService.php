<?php

namespace App\Interfaces\EmailInterfaces;

use App\Interfaces\EmailInterfaces\Email;
use Mailjet\Client;
use Mailjet\LaravelMailjet\Contracts\CampaignDraftContract;
use Exception;
use Mailjet\Resources;

class MailJetService implements Email
{
    /**
     * Send mail using Mailjet service
     * @param array $data
     *
     * @return void
     * @throws Exception
    */
    public function send(array $data)
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
            $mailJet->post(Resources::$Email, ['body' => $params]);
        } catch(Exception $e) {
            throw $e;
        }
    }
}
