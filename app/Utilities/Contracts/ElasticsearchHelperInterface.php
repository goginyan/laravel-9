<?php

namespace App\Utilities\Contracts;

use App\Models\Mails\MailData;

interface ElasticsearchHelperInterface {
    /**
     * Store the email's message body, subject and to address inside elasticsearch.
     *
     * @return array|bool - Return all stored emails inside elasticsearch or false if error occurred
     */
    public function getEmails(): array|bool;

    /**
     * Store the email's message body, subject and to address inside elasticsearch.
     *
     * @param  MailData  $mailData
     * @return mixed - Return the id of the record inserted into Elasticsearch or false if error occurred
     */
    public function storeEmail(MailData $mailData): mixed;
}
