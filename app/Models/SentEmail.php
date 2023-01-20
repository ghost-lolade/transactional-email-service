<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentEmail extends Model
{
    use HasFactory;
    protected $fillable = ['to', 'subject', 'message', 'service'];

    public function store($data)
    {
        // later to add a loop for multiple "to"
        return $this->create([
            'to' => $data['to'],
            'subject' => $data['subject'],
            'message' => $data['message'],
        ]);
    }
}
