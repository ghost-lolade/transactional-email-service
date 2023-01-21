<?php

namespace App\Http\Controllers;

use App\Http\Resources\SendEmailResource;
use App\Http\Actions\SendEmailAction;
use App\Http\Requests\SendEmailRequest;

class EmailController extends Controller
{
    public function sendEmail(SendEmailRequest $request, SendEmailAction $action)
    {
        $response = $action->execute($request);

        $response = new SendEmailResource($response);

        return $this->createdResponse('This email has been queued successfully', $response);
    }
}
