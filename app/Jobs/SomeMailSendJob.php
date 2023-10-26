<?php

namespace App\Jobs;

use App\Mail\SomeMail;
use App\Models\Mails\MailData;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SomeMailSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public MailData $mailData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MailData $mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mailUser = new SomeMail($this->mailData);
        Mail::send($mailUser);

        //Elastic
        /**@var ElasticsearchHelperInterface $elasticsearchHelper*/
        $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);
        $elasticRecordId = $elasticsearchHelper->storeEmail($this->mailData);
        if (!$elasticRecordId)
            throw new \Exception("Can't save email to elasticsearch. ".
                "Email subject: {$this->mailData->messageSubject}; ".
                "email reciever: {$this->mailData->toEmailAddress}.");
        $this->mailData->id = $elasticRecordId;

        //Redis (keeping only the last message)
        /**@var RedisHelperInterface $redisHelper*/
        $redisHelper = app()->make(RedisHelperInterface::class);
        $redisHelper->storeRecentMessage($this->mailData);
    }
}
