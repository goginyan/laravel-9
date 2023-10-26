<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mails\EmailSendRequest;
use App\Jobs\SomeMailSendJob;
use App\Models\Mails\MailData;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
    public function list(): JsonResponse
    {
        $msgPrefix = "Returning all sent emails. ";
        $responseData = [
            "errors"  => null,
            "success" => null,
            "result" => null,
        ];
        try {
            /**@var ElasticsearchHelperInterface $elasticsearchHelper*/
            $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);
            $emails = $elasticsearchHelper->getEmails();
            if (false === $emails) throw new \Exception("Can't load or parse elasticsearch data");

            $responseData["success"] = true;
            $responseData["result"] = $emails;
            return response()->json($responseData);
        }
        catch (\Exception $Exc) {
            $responseData["success"] = false;
            $responseData["errors"] = config("app.debug") ? "{$msgPrefix}{$Exc->getMessage()}" : "Some error occurred";
            throw new HttpResponseException(response()->json($responseData, 500));
        }
    }
    public function send(EmailSendRequest $request): JsonResponse
    {
        $msgPrefix = "Mail sending. ";
        $responseData = [
            "errors"  => null,
            "success" => null,
        ];
        try {
            foreach ($request->post() as $requestData) {
                $mailUserData = new MailData();
                $mailUserData->messageSubject   = $requestData["message_subject"];
                $mailUserData->messageBody      = $requestData["message_body"];
                $mailUserData->toEmailAddress   = $requestData["to_email_address"];

                SomeMailSendJob::dispatch($mailUserData);
            }

            $responseData["success"] = true;
            return response()->json($responseData);
        }
        catch (\Exception $Exc) {
            $responseData["success"] = false;
            $responseData["errors"] = config("app.debug") ? "{$msgPrefix}{$Exc->getMessage()}" : "Some error occurred";
            throw new HttpResponseException(response()->json($responseData, 500));
        }
    }
}
