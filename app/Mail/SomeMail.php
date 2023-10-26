<?php

namespace App\Mail;

use App\Models\Mails\MailData;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SomeMail extends Mailable
{
    use Queueable,
        SerializesModels;

    protected MailData $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MailData $mailData)
    {
        $this->data = $mailData;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            to:      $this->data->toEmailAddress,
            subject: $this->data->messageSubject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mails.some-mail',
            with: ['messageBody' => $this->data->messageBody],
        );
    }
}
