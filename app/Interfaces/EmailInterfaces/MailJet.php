<?php

namespace App\Interfaces\EmailInterfaces;

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
                    'HtmlPart' => $data['message']['html']
                ]
            ]

        ];
        try {
            $response = $mailJet->post(Resources::$Email, ['body' => $body]);
        } catch(Exception $e) {
            throw $e;
        }
    }
}
