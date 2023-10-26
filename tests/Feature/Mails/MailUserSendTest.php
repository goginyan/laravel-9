<?php

namespace Feature\Mails;

use App\Jobs\SomeMailSendJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MailUserSendTest extends TestCase
{
    /**
     * Check if API route "api/{user}/send" creates Job to send email
     *
     * @return void
     */
    public function test_route_send_success()
    {
        //data to check
        $requestData = [
          [
            "message_subject" => "A test subject 1",
            "message_body"  => "Some test email body for a subject 1",
            "to_email_address"  => "test1@gmail.com"
          ],
        ];

        //checks
        Queue::fake();
        $response = $this->postJson("/api/send?api_token=".config('app.api_token'), $requestData);
        $response->assertStatus(200);
        Queue::assertPushed(SomeMailSendJob::class, function ($job) use($requestData) {
            return $job->mailData->toEmailAddress   == $requestData[0]["to_email_address"] and
                   $job->mailData->messageSubject   == $requestData[0]["message_subject"] and
                   $job->mailData->messageBody      == $requestData[0]["message_body"];
        });
    }

    /**
     * Check if API route "api/{user}/send" DON't create Job to send email because of invalid data.
     *
     * @return void
     */
    public function test_route_send_failed_validation()
    {
        //invalid data to check
        $requestData = [
            [
                "invalid_property"  => "value"
            ],
        ];

        //checks
        Queue::fake();
        $response = $this->postJson("/api/send?api_token=".config('app.api_token'), $requestData);
        $response->assertStatus(422);
    }

    /**
     * Check if API route "api/list" returns JSON with expected fields.
     *
     * @return void
     */
    public function test_route_list_success()
    {
        //checks
        $response = $this->getJson("/api/list?api_token=".config('app.api_token'));
        $response->assertStatus(200);

        $responseJson = json_decode( $response->content() );
        $emails = $responseJson->result ?? false;
        $this->assertIsArray($emails);

        foreach ($emails as $email) {
            $this->assertObjectHasProperty("message_subject", $email);
            $this->assertObjectHasProperty("message_body", $email);
            $this->assertObjectHasProperty("to_email_address", $email);
        }
    }
}
