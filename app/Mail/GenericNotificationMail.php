<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $messageText;
    public $extra;

    /**
     * Create a new message instance.
     */
    public function __construct(string $title, string $messageText, array $extra = [])
    {
        $this->title = $title;
        $this->messageText = $messageText;
        $this->extra = $extra;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->title)
                    ->view('emails.generic_notification_plain')
                    ->with([
                        'title' => $this->title,
                        'messageText' => $this->messageText,
                        'extra' => $this->extra,
                    ]);
    }
}
