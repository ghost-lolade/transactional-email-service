<?php

namespace App\Http\Actions;

use App\Jobs\SendEmail;
use App\Models\SentEmail;

class SendEmailAction
{
    public function execute($request)
    {
        SendEmail::dispatch($request->all())
            ->onQueue('email');

        $response = $this->saveEmailData($request);

        return $response;
    }

    private function saveEmailData($request): SentEmail
    {
        return SentEmail::create([
            'to' => $request['to'],
            'subject' => $request['subject'],
            'text' => $request['message']['text'],
            'html' => $request['message']['html'],
            'markdown' => $request['message']['markdown'],
        ]);
    }
}
