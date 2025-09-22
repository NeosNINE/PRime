<?php

namespace App\Jobs;

use App\Models\System\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The email instance.
     *
     * @var \App\Models\System\Email
     */
    protected Email $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Email $email )
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        emails()->__handle($this->email);
    }
}
