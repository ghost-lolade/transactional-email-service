<?php

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

if (! function_exists('log_activity')) {
    /**
     * Logs the activity.
     */
    function log_activity(int $statusCode = null, string $message, $data = [])
    {
        Log::channel('email')->info($message, $data);
    }
}

if (! function_exists('log_error')) {
    /**
     * Throw an HTTP exception and log the exception.
     */
    function log_error(int $statusCode, string $message, Exception $exception)
    {
        report($exception);
        Log::channel('email')->info($message, $exception->getTrace());

        throw new HttpException($statusCode, $message);
    }
}
