<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Actions\SendEmailAction;
use App\Http\Requests\SendEmailRequest;
use Illuminate\Support\Facades\Validator;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email through CLI';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = $this->getInputs();

        $validated = $this->validateInputs($data);

        if(count($validated->errors()) > 0){
            $this->info('Something is wrong with your inputs');

            return Command::FAILURE;
        }

        $this->info('Email will be added to queue');

        $response = (new SendEmailAction)->execute($data);

        log_activity(null, "email data added to queue", $data);

        if(is_null($response)){

            log_activity(500, "something went wrong", $response);

            $this->info('Something went wrong, Don\'t worry i\'ts not your fault');

            return Command::FAILURE;
        }

        $this->info("Email has been sent to {$data['to']}");

        return Command::SUCCESS;
    }

    private function validateInputs(array $data)
    {
        $validated = Validator::make([
            'to' => $data['to'],
            'subject' => $data['subject'],
            'text' => $data['message']['text'],
            'html' => $data['message']['html'],
            'markdown' => $data['message']['markdown'],
        ], [
            'to' => ['required', 'email'],
            'subject' => ['required','string'],
            'text' => ['required', 'string'],
            'html' => 'string',
            'markdown' => ['string']
        ]);

        return $validated;
    }

    private function getInputs(): array
    {
        $to = $this->ask('Provide the recipient\'s email');
        $subject = $this->ask('Provide the subject of the mail');
        $text = $this->ask('Provide message in text format');
        $html = $this->ask('Provide message in html format');
        $markdown = $this->ask('Provide message in markdown format');

        return [
            'to' => $to,
            'subject' => $subject,
            'message' => [
                'text' => $text,
                'html' => $html,
                'markdown' => $markdown
            ]
        ];
    }

}
