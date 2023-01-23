<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SendEmailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'to' => $this->to,
            'subject' => $this->subject,
            'service' => $this->service,
            'html' => $this->html,
            'text' => $this->text,
            'markdown' => $this->markdown,
        ];
    }
}
