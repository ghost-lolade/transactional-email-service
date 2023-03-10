<?php

namespace App\Jobs;

use App\Models\SentEmail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class SendEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $service;
    protected $fallbacks;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->service = config('mail.service');
        $this->data = $data;
        $this->fallbacks = config('mail.fallbacks');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        (new $this->service)->send($this->data);

        log_activity(null, "Sending mail to " . $this->data['to'] . " using" . $this->service, $this->data);

    }

    public function failed()
    {
        $fallback = Arr::random($this->fallbacks);

        log_activity(500, $this->service ."Failed, Sending with fallback mail service:" . $fallback, $this->data);

        config(['mail.service' => $fallback ]);

        self::dispatch($this->data);
    }

}
