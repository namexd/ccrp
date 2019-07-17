<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $params;

    /**
     * Create a new job instance.
     *
     * @param Message $message
     * @param $params
     */
    public function __construct($params)
    {
        $this->params=$params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new Message())->asyncSend($this->params);
    }
}
