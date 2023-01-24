<?php

namespace App\Interfaces\EmailInterfaces;

use Exception;

interface Email
{
    /**
     * Send mail using External service
     * @param array $data
     *
     * @return void
    */
    public function send(array $data): void;

    /**
     * Send mail using External service
     * @param int $id
     *
     * @return void
    */
    public function updateSentEmailTable(int $id): void;

    public function logActivity(int $statusCode = null, string $message, $data = []): void;

    public function logError(int $statusCode, string $message, Exception $exception): void;
}
