<?php

namespace App\Http\Actions;

use App\Jobs\SendEmail;
use App\Models\SentEmail;

class SendEmailAction
{
    public function execute($request)
    {
        $response = $this->saveEmailData($request);

        $request['id'] = $response->id;

        dispatch(new SendEmail($request->all()));

        return $response;
    }

    private function saveEmailData($request): SentEmail
    {
        return SentEmail::create([
            'to' => $request['to'],
            'subject' => $request['subject'],
            'text' => $request['message']['text'],
            'html' => $request['message']['html'] ?? null,
            'markdown' => $request['message']['markdown'] ?? null,
        ]);
    }
}
