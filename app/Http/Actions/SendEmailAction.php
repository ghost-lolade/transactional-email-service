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


        $sent = new SentEmail();
        $response = $sent->store($request);

        return $response;
    }
}
