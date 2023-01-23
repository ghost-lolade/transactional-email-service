<?php

namespace App\Interfaces\EmailInterfaces;

interface Email
{
    /**
     * Send mail using External service
     * @param array $data
     *
     * @return void
    */
    public function send($data);
}
