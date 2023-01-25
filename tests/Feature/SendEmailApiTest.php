<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Jobs\SendEmail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendEmailApiTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCannotSendMailWithInvalidInputs(): void
    {
        $incompletePayload = $this->getIncompletePayload();

        $response = $this->post(
            route('email.send'),
            $incompletePayload
        );

        $response->assertStatus(302);
    }

    public function testCannotSendEmailWithoutPayload(): void
    {
        $response = $this->post(
            route('email.send')
        );

        $response->assertStatus(302);
    }

    public function testCanSendMailSuccessfully(): void
    {
        Queue::fake();
        $payload = $this->payload();

        $response = $this->post(
            route('email.send'),
            $payload
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data'
            ])
            ->assertJson([
                'status' => true,
                'message' => 'This email has been queued successfully',
                'data' => [
                    'to' => $payload['to'],
                    'subject' => $payload['subject'],
                    'text' => $payload['message']['text'],
                    'html' => $payload['message']['html'],
                    'markdown' => $payload['message']['markdown'],
                ]
            ]);

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
    public function getIncompletePayload(): array
    {
        return [
            'to' => $this->faker->email,
            'message' => [
                'text' => $this->faker->text,
                'html' => $this->faker->randomHtml,
                'markdown' => $this->faker->text
                ]
            ];
    }
}
