<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\Laravel\Facades\Telegram;

class RemoveGrounp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务可尝试次数.
     *
     * @var int
     */
    public $tries = 5;
    /**
     * 标示是否应在超时时标记为失败.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        dump($this->data->toarray());
        if ($this->data['user_status'] == 3) {
            Telegram::kickChatMember([
                'chat_id' => $this->data['chat_ground_id'],
                'user_id' => $this->data['user_no'],
            ]);
        }
        $this->fail();


    }
}
