<?php

namespace App\Http\Controllers;

use App\Http\Resources\SendEmailResource;
use App\Http\Actions\SendEmailAction;
use App\Http\Requests\SendEmailRequest;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
    public function sendEmail(SendEmailRequest $request, SendEmailAction $action): JsonResponse
    {
        $response = $action->execute($request->all());

        $response = new SendEmailResource($response);

        return $this->createdResponse('This email has been queued successfully', $response);
    }
}
