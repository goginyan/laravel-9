<?php

namespace App\Helpers\Redis;

use App\Models\Mails\MailData;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Support\Facades\Redis;

class RedisHelper implements RedisHelperInterface {
    /**
     * Store the id of a message along with a message subject and email receiver in Redis.
     *
     * @param MailData $mailData
     * @return void
     */
    public function storeRecentMessage(MailData $mailData): void
    {
        Redis::set("email_last", "mail_id:{$mailData->id}:message_subject:{$mailData->messageSubject}:to_email_address:{$mailData->toEmailAddress}");
    }
}
