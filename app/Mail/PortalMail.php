<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

/**
 * Every portal email: a subject, a view under resources/views/emails and its data.
 */
class PortalMail extends Mailable
{
    use Queueable;

    public $template;
    public $data;

    public function __construct($subject, $template, array $data = [])
    {
        $this->subject = $subject;
        $this->template = $template;
        $this->data = $data;
    }

    public function build()
    {
        return $this->from(config('notifications.from_address'), config('notifications.from_name'))
            ->view('emails.'.$this->template, $this->data + ['subject' => $this->subject]);
    }
}
