<?php

namespace App\Interfaces\EmailInterface;

use App\Interfaces\EmailInterfaces\Email;
use Carbon\Carbon;
use Mailjet\Client;
use Mailjet\Resources;
use Exception;

class MailJet implements Email
{
    public function send($data)
    {
        $mailJet = new Client(env('MJ_APIKEY_PUBLIC'), env('MJ_APIKEY_PRIVATE'), true);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => config('mail.from.address'),
                        'Name' => config('mail.from.name')
                    ],
                    'To' => [
                        [
                            'Email' => $data['to'],
                        ]
                    ],
                    'Subject' => $data['subject'],
                    'TextPart' => $data['message']['text'],
                    'HTMLPart' => $data['message']['HTML']
                ]
            ]

        ];
        try {
            $response = $mailJet->post(Resources::$Email, ['body' => $body]);
        } catch(Exception $e) {
            /*
             * if the default emails service is down
             * for any reason,
             * then throw an error
             * that will be handled by the queued job
             * which will trigger fallback service
             */
            throw $e;
        }
    }
}
