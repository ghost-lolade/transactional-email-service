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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $to = $this->ask('Provide the recipient\'s email?');
        $subject = $this->ask('Provide the email subject?');
        $text = $this->ask('Provide message in text format?');
        $html = $this->ask('Provide message in html format?');
        $markdown = $this->ask('Provide message in markdown format?');

        $data = [
            'to' => $to,
            'subject' => $subject,
            'message' => [
                'text' => $text,
                'html' => $html,
                'markdown' => $markdown
            ]
        ];

        $validatedData = $this->validateInputs($data);

        $this->info('Email will be added to queue');

        $response = (new SendEmailAction)->execute($data);

        log_activity(null, "email data added to queue", $data);

        if($response->status == false){

            log_error(500, "something went wrong");

            $this->info('Something went wrong, Don\'t worry i\'ts not your fault');

            return Command::FAILURE;
        }

        $this->info("Email has been sent to ${to}");

        return Command::SUCCESS;
    }

    private function validateInputs(array $data):bool
    {
        $validated = Validator::make([
            'to' => $data['to'],
            'subject' => $data['subject'],
            'html' => $data['message']['html'],
            'text' => $data['message']['text'],
            'markdown' => $data['message']['markdown'],
        ], [
            'to' => ['required', 'email'],
            'subject' => ['required','string'],
            'message.html' => ['required', 'string'],
            'message.text' => ['required', 'string'],
            'message.markdown' => ['required', 'string']
        ]);

        if ($validated->fails()) {
            return false;
        }
        return true;
    }

}
