<?php

namespace App\Helpers\Elasticsearch;

use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Models\Mails\MailData;
use Elasticsearch;

class ElasticsearchHelper implements ElasticsearchHelperInterface {

    protected string $emailsIndex = "emails";

    /**
     * Store the email's message body, subject and to address inside elasticsearch.
     *
     * @return array|bool - Return all stored emails inside elasticsearch or false if error occurred
     */
    public function getEmails(): array|bool
    {
        if (!Elasticsearch::indices()->exists([ 'index' =>  $this->emailsIndex ])) return [];

        $elasticSearchResult = Elasticsearch::search([
            'index' => $this->emailsIndex,
        ]);

        if (!isset($elasticSearchResult["hits"]["hits"])) return false;

        $elasticSearchResult = $elasticSearchResult["hits"]["hits"];

        $emailsData = [];
        foreach ($elasticSearchResult as $elasticDocument) {
            if (!isset($elasticDocument["_source"])) return false;

            $emailsData[] = $elasticDocument["_source"];
        }

        return $emailsData;
    }

    /**
     * Store the email's message body, subject and to address inside elasticsearch.
     *
     * @param  MailData $mailData
     * @return mixed - Return the id of the record inserted into Elasticsearch or false if error occurred
     */
    public function storeEmail(MailData $mailData): mixed {

        $result = Elasticsearch::index([
            'index' => $this->emailsIndex,
            'body' => [
                'message_subject'  => $mailData->messageSubject,
                'message_body'     => $mailData->messageBody,
                'to_email_address' => $mailData->toEmailAddress
            ]
        ]);

        return $result['_id'] ?? false;
    }
}
