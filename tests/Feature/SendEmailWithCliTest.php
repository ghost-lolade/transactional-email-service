<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Jobs\SendEmail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendEmailWithCliTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCannotSendMailWithCliInvalidInputs()
    {
        Queue::fake();

        $payload = $this->payload();

        $this->artisan('send:mail')
            ->expectsQuestion('Provide the recipient\'s email', $payload['to'])
            ->expectsQuestion('Provide the subject of the mail', 234)
            ->expectsQuestion('Provide message in text format', $payload['message']['text'])
            ->expectsQuestion('Provide message in html format', $payload['message']['html'])
            ->expectsQuestion('Provide message in markdown format', $payload['message']['markdown'])

            ->expectsOutput('Something is wrong with your inputs')
            ->assertFailed();

        Queue::assertNotPushed(SendEmail::class);

        $this->assertDatabaseEmpty('sent_emails');
    }

    public function testCanSendMailWithCliMode(): void
    {
        Queue::fake();

        $payload = $this->payload();

        $this->artisan('send:mail')
            ->expectsQuestion('Provide the recipient\'s email', $payload['to'])
            ->expectsQuestion('Provide the subject of the mail', $payload['subject'])
            ->expectsQuestion('Provide message in text format', $payload['message']['text'])
            ->expectsQuestion('Provide message in html format', $payload['message']['html'])
            ->expectsQuestion('Provide message in markdown format', $payload['message']['markdown'])
            ->expectsOutput('Email will be added to queue')
            ->expectsOutput("Email has been sent to {$payload['to']}")
            ->assertOk();

        Queue::assertPushed(SendEmail::class);

        $this->assertDatabaseHas('sent_emails', [
            'to' => $payload['to'],
            'subject' => $payload['subject'],
            'text' => $payload['message']['text'],
            'html' => $payload['message']['html'],
            'markdown' => $payload['message']['markdown'],
        ]);
    }

    public function payload(): array
    {
        return [
            'to' => $this->faker->email,
            'subject' => $this->faker->title,
            'message' => [
                'text' => $this->faker->text,
                'html' => $this->faker->text,
                'markdown' => $this->faker->text
                ]
            ];
    }
}
