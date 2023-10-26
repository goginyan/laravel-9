<?php

namespace App\Utilities\Contracts;

use App\Models\Mails\MailData;

interface RedisHelperInterface {
    /**
     * Store the last message id along with a message subject and e email reciever in Redis.
     *
     * @param MailData $mailData
     * @return void
     */
    public function storeRecentMessage(MailData $mailData): void;
}
